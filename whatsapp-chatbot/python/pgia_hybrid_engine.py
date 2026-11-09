# -*- coding: utf-8 -*-
import os
import sys
import time
import json
import math
import re
import sqlite3
import requests
import threading
from datetime import datetime
import numpy as np
from typing import List, Dict, Any, Optional
from rank_bm25 import BM25Okapi
from fastembed.rerank.cross_encoder import TextCrossEncoder
from langchain_groq import ChatGroq
from langchain_core.messages import SystemMessage, HumanMessage

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

# Auto-detect database and env paths with fallback for production and local environments
_default_db = os.path.join(BASE_DIR, "database", "database.sqlite")
DB_PATH = os.environ.get("DB_PATH", _default_db if os.path.exists(_default_db) else "/var/www/whatsapp-chatbot/database/database.sqlite")

_default_env = os.path.join(BASE_DIR, ".env")
ENV_PATH = os.environ.get("ENV_PATH", _default_env if os.path.exists(_default_env) else "/var/www/whatsapp-chatbot/.env")

def load_env_vars() -> Dict[str, str]:
    res = {}
    if os.path.exists(ENV_PATH):
        with open(ENV_PATH, "r", encoding="utf-8") as f:
            for line in f:
                line = line.strip()
                if line and not line.startswith("#") and "=" in line:
                    k, v = line.split("=", 1)
                    res[k.strip()] = v.strip().replace('"', '').replace("'", "")
    return res

class PgiaHybridEngine:
    _instance = None

    @classmethod
    def get_instance(cls):
        if cls._instance is None:
            cls._instance = cls()
        return cls._instance

    def __init__(self):
        self.env = load_env_vars()
        self.groq_api_key = self.env.get("GROQ_API_KEY", "")
        self.embed_url = self.env.get("OLLAMA_EMBED_URL", "http://192.248.43.138:11434/api/embeddings")
        
        self.load_knowledge_base()

        print("[HybridEngine] Initializing BAAI/bge-reranker-base...")
        t0 = time.time()
        self.reranker = TextCrossEncoder(model_name="BAAI/bge-reranker-base")
        print(f"[HybridEngine] Reranker initialized in {time.time()-t0:.2f}s")

        self.llm = ChatGroq(
            api_key=self.groq_api_key,
            model_name="openai/gpt-oss-120b",
            temperature=0.15,
            max_tokens=1024
        )

        journey_path = os.path.join(os.path.dirname(__file__), "pgia_student_journey_map.json")
        self.journey_map = {}
        if os.path.exists(journey_path):
            try:
                with open(journey_path, "r", encoding="utf-8") as f:
                    self.journey_map = json.load(f)
                print(f"[HybridEngine] Student Journey Map loaded successfully ({len(self.journey_map.get('degrees', {}))} degrees, {len(self.journey_map.get('states', {}))} states)")
            except Exception as e:
                print(f"[HybridEngine] Warning: Could not load Journey Map: {e}")

    def load_knowledge_base(self):
        """Thread-safe zero-downtime hot-reload of Vector Matrix and BM25 index."""
        print(f"[HybridEngine] Loading chunks from {DB_PATH}...")
        t0 = time.time()
        conn = sqlite3.connect(DB_PATH)
        c = conn.cursor()
        c.execute("SELECT id, source, chunk_text, embedding FROM knowledge_base ORDER BY id ASC")
        rows = c.fetchall()
        conn.close()

        if not rows:
            print("[HybridEngine] Warning: knowledge_base table is currently empty.")
            return

        new_ids = [r[0] for r in rows]
        new_sources = [r[1] for r in rows]
        new_texts = [r[2] for r in rows]

        tokenized_corpus = [doc.lower().split() for doc in new_texts]
        new_bm25 = BM25Okapi(tokenized_corpus)

        raw_embeddings = [json.loads(r[3]) for r in rows]
        new_embeddings = np.array(raw_embeddings, dtype=np.float32)
        norms = np.linalg.norm(new_embeddings, axis=1, keepdims=True)
        norms[norms == 0] = 1e-10
        new_norm_embeddings = new_embeddings / norms

        # Atomic memory pointer swap (Zero downtime for active concurrent queries)
        self.ids = new_ids
        self.sources = new_sources
        self.texts = new_texts
        self.bm25 = new_bm25
        self.embeddings = new_embeddings
        self.norm_embeddings = new_norm_embeddings

        print(f"[HybridEngine] Hot-reloaded {len(self.texts)} chunks into Vector Matrix & BM25 Index in {time.time()-t0:.2f}s")

    def embed_text(self, text: str) -> Optional[List[float]]:
        """Compute embedding vector using the Dell T440 Ollama nomic-embed-text cluster."""
        try:
            resp = requests.post(self.embed_url, json={
                "model": "nomic-embed-text",
                "prompt": text
            }, timeout=10)
            if resp.status_code == 200:
                data = resp.json()
                return data.get("embedding")
        except Exception as e:
            print(f"[HybridEngine] Embed error on {self.embed_url}: {e}")
        return None

    def ingest_pdf(self, file_path: str, source_name: str) -> int:
        """
        Extracts, chunks, embeds, and updates the knowledge base from an uploaded PDF.
        Automatically reloads the in-memory models upon completion.
        """
        try:
            from pypdf import PdfReader
        except ImportError:
            raise RuntimeError("pypdf is required for PDF ingestion. Run: pip install pypdf")

        if not os.path.exists(file_path):
            raise FileNotFoundError(f"PDF file not found at: {file_path}")

        print(f"[HybridEngine] Parsing PDF: {file_path} for source: {source_name}...")
        reader = PdfReader(file_path)
        full_text_pages = []
        for p_idx, page in enumerate(reader.pages):
            p_text = page.extract_text() or ""
            # Clean excessive whitespace and carriage returns
            p_text = re.sub(r'[ \t]+', ' ', p_text)
            p_text = re.sub(r'\n{3,}', '\n\n', p_text).strip()
            if p_text:
                full_text_pages.append(p_text)

        extracted_text = "\n\n".join(full_text_pages)
        if not extracted_text.strip():
            raise ValueError("No extractable text found in the provided PDF.")

        # Sliding window chunking (700 characters, 100 character overlap)
        chunk_size = 700
        overlap = 100
        chunks = []
        start = 0
        length = len(extracted_text)

        while start < length:
            sub = extracted_text[start : start + chunk_size]
            if start + chunk_size < length:
                last_space = sub.rfind(" ")
                if last_space != -1 and last_space > chunk_size * 0.6:
                    sub = sub[:last_space]
            clean_chunk = sub.strip()
            if len(clean_chunk) > 35:
                chunks.append(clean_chunk)
            start += max(1, len(sub) - overlap)

        print(f"[HybridEngine] Generated {len(chunks)} sliding-window chunks. Embedding via Dell T440...")

        conn = sqlite3.connect(DB_PATH)
        c = conn.cursor()

        # Remove existing chunks for this source to prevent duplicate collisions
        c.execute("DELETE FROM knowledge_base WHERE source = ?", (source_name,))

        saved_count = 0
        now_str = datetime.utcnow().strftime("%Y-%m-%d %H:%M:%S")

        for idx, c_text in enumerate(chunks, 1):
            vec = self.embed_text(c_text)
            if vec is not None:
                c.execute(
                    "INSERT INTO knowledge_base (source, chunk_text, embedding, created_at, updated_at) VALUES (?, ?, ?, ?, ?)",
                    (source_name, c_text, json.dumps(vec), now_str, now_str)
                )
                saved_count += 1
            if idx % 50 == 0 or idx == len(chunks):
                conn.commit()
                print(f"[HybridEngine] Embedded & stored {saved_count}/{len(chunks)} chunks...")

        conn.commit()
        conn.close()

        # Perform atomic zero-downtime hot-reload
        self.load_knowledge_base()

        return saved_count

    def remove_source(self, source_name: str) -> int:
        """Removes all chunks associated with a source and triggers hot-reload."""
        conn = sqlite3.connect(DB_PATH)
        c = conn.cursor()
        c.execute("DELETE FROM knowledge_base WHERE source = ?", (source_name,))
        deleted_count = c.rowcount
        conn.commit()
        conn.close()

        print(f"[HybridEngine] Purged {deleted_count} chunks for source: {source_name}")
        self.load_knowledge_base()
        return deleted_count

    def embed_query(self, text: str) -> np.ndarray:
        try:
            resp = requests.post(self.embed_url, json={
                "model": "nomic-embed-text",
                "prompt": text
            }, timeout=6)
            if resp.status_code == 200:
                vec = np.array(resp.json().get("embedding", []), dtype=np.float32)
                norm = np.linalg.norm(vec)
                return vec / (norm if norm > 0 else 1.0)
        except Exception as e:
            print(f"[HybridEngine] Dell T440 embed warning: {e}")
        return np.zeros(768, dtype=np.float32)

    def hybrid_search(self, query: str, top_k: int = 4) -> List[Dict[str, Any]]:
        t_start = time.time()
        query_tokens = query.lower().split()

        bm25_scores = self.bm25.get_scores(query_tokens)
        bm25_top = np.argsort(bm25_scores)[::-1][:25]

        q_vec = self.embed_query(query)
        if np.any(q_vec):
            sims = self.norm_embeddings @ q_vec
            vec_top = np.argsort(sims)[::-1][:25]
        else:
            vec_top = []

        k = 60
        rrf_scores = {}
        for rank, idx in enumerate(vec_top):
            rrf_scores[idx] = rrf_scores.get(idx, 0.0) + (1.0 / (k + rank + 1))
        for rank, idx in enumerate(bm25_top):
            rrf_scores[idx] = rrf_scores.get(idx, 0.0) + (1.0 / (k + rank + 1))

        fused = sorted(rrf_scores.items(), key=lambda x: x[1], reverse=True)[:8]
        if not fused:
            return []

        candidate_indices = [idx for idx, _ in fused]
        candidate_docs = [self.texts[idx] for idx in candidate_indices]

        raw_scores = list(self.reranker.rerank(query, candidate_docs))
        
        ranked_results = []
        for rank_idx, (c_idx, score) in enumerate(zip(candidate_indices, raw_scores)):
            clamped = max(-10.0, min(5.0, score))
            prob = 0.40 + ((clamped + 10.0) / 15.0) * 0.55
            ranked_results.append({
                "id": self.ids[c_idx],
                "source": self.sources[c_idx],
                "text": self.texts[c_idx],
                "score": float(score),
                "confidence": float(prob)
            })

        ranked_results.sort(key=lambda x: x["score"], reverse=True)
        final_matches = ranked_results[:top_k]

        duration = (time.time() - t_start) * 1000
        print(f"[HybridEngine] Hybrid RAG (Vector + BM25 + RRF + Reranker) completed in {duration:.1f}ms (Top score: {final_matches[0]['confidence']*100:.1f}%)")
        return final_matches

    def generate_grounded_answer(self, question: str) -> Dict[str, Any]:
        t0 = time.time()
        matches = self.hybrid_search(question, top_k=4)

        if not matches:
            return {
                "answer": "I am sorry, but I could not find relevant institutional records in the official PGIA handbooks for your request. Please contact the Admissions Branch at info@pgia.pdn.ac.lk.",
                "sources": [],
                "top_score": 0.0,
                "latency_ms": (time.time() - t0) * 1000
            }

        context_parts = []
        unique_sources = []
        for m in matches:
            if m["source"] and m["source"] not in unique_sources:
                unique_sources.append(m["source"])
            context_parts.append(f"[Document: {m['source']}]\n{m['text']}")

        # Ensure Official PGIA Contact Directory is provided for contact inquiries
        contact_keywords = [
            "contact", "phone", "telephone", "tel", "call", "hotline", "mobile",
            "number", "numbers", "inquir", "inquiry", "reach", "email", "address",
            "office", "director", "registrar", "bursar", "librarian", "coordinator",
            "security", "fax"
        ]
        q_lower = question.lower()
        if any(w in q_lower for w in contact_keywords):
            official_directory = (
                "[Official PGIA Institutional Contact Directory - Student Handbook]\n"
                "Postgraduate Institute of Agriculture (PGIA), University of Peradeniya\n"
                "Address: P.O. Box 55, Old Galaha road, Peradeniya, Sri Lanka\n\n"
                "Official General Telephone Lines:\n"
                "• General Enquiries & Admissions: +94 812 388203\n"
                "• Student Coordination & Affairs: +94 812 386542\n"
                "• Director's Office Line: +94 812 385517\n"
                "• Official Fax: +94 812 388318\n"
                "• General Email: dir@pgia.ac.lk\n"
                "• Official Website: http://www.pgia.pdn.ac.lk/\n\n"
                "Key Administration & Staff Direct Lines:\n"
                "• Deputy Registrar (Ms. I. Pathirana): Tel: +94 81 2388203 | Email: dr@pgia.ac.lk\n"
                "• Senior Assistant Bursar - Accounts & Fees (Ms. K.A.I.S. Ranasinghe): Tel: +94 81 2388692 | Email: sab@pgia.ac.lk\n"
                "• PGIA Coordinator (Ms. D.M.M.G.J.M. Dissanayaka): Tel: +94 81 2386542 | Email: pgiacoordinator@pgia.ac.lk\n"
                "• Senior Assistant Librarian (Ms. H.N.K. Dissanayake): Tel: +94 81 2388956 | Email: sal@pgia.ac.lk\n"
                "• Systems Analyst - MIS & IT (Mr. A.G.I. Hemajith): Tel: +94 81 2389726 | Email: sa@pgia.ac.lk\n\n"
                "Hotlines & Inquiries:\n"
                "• International Inquiries Hotline: +94 812 388203\n"
                "• Research Facilitation Fund (RFF): 081 2388203\n"
                "• Student Loan Facilities: 0812 388219\n"
                "• University Security (24-Hour Campus Emergency): 081-2389182"
            )
            context_parts.insert(0, official_directory)
            if "Handbook_2026.pdf" not in unique_sources:
                unique_sources.append("Handbook_2026.pdf")

        # Check if question relates to student lifecycle, degree progression, transitions, or credit rules
        journey_keywords = [
            "after coursework", "what next", "next step", "progress", "thesis", "defense", 
            "comprehensive exam", "ce", "credit", "gpa", "slqf", "directed study", 
            "upgrade", "convert", "exit", "diploma", "duration", "time limit", 
            "pass out", "convocation", "stages", "roadmap", "lifecycle", "pass masters", "pass msc"
        ]
        if any(k in question.lower() for k in journey_keywords) and self.journey_map:
            journey_context = (
                "=== PGIA ACADEMIC JOURNEY DECISION MAP (LIFECYCLE STATE MACHINE) ===\n"
                "Use this official lifecycle structure to provide exact step-by-step guidance on prerequisites, "
                "transitions, GPA thresholds, and exit routes:\n\n"
            )
            for deg_code, deg in self.journey_map.get("degrees", {}).items():
                journey_context += f"• Track {deg_code} ({deg.get('name')} - SLQF {deg.get('slqf_level')}):\n"
                journey_context += f"  - Credits: {deg.get('total_credits', 'N/A')} ({deg.get('breakdown', deg.get('nature', ''))})\n"
                if "min_gpa" in deg:
                    journey_context += f"  - Min GPA: {deg.get('min_gpa')}\n"
                if "min_duration_years" in deg:
                    journey_context += f"  - Duration: {deg.get('min_duration_years')} to {deg.get('max_duration_years')} years\n"
                if "fallback_exit" in deg:
                    journey_context += f"  - Fallback / Early Exit: {deg.get('fallback_exit')}\n"
                if "upgrade_route" in deg:
                    journey_context += f"  - Upgrade Option: {deg.get('upgrade_route')}\n"
                if "publication_requirement" in deg:
                    journey_context += f"  - Publications: {deg.get('publication_requirement')}\n"
            
            journey_context += "\nKey Lifecycle States & Gates:\n"
            for st_name, st_info in self.journey_map.get("states", {}).items():
                journey_context += f"• Stage [{st_name}]: {st_info.get('description')}\n"
                if "prerequisites" in st_info:
                    journey_context += f"  - Prerequisites: {', '.join(st_info['prerequisites'])}\n"
                if "regulations" in st_info:
                    journey_context += f"  - Regulations: {'; '.join(st_info['regulations'])}\n"
                if "steps" in st_info:
                    journey_context += f"  - Required Steps: {'; '.join(st_info['steps'])}\n"
                if "possible_outcomes" in st_info:
                    journey_context += f"  - Outcomes: {'; '.join(st_info['possible_outcomes'])}\n"
            
            context_parts.insert(0, journey_context)
            if "pgia_student_journey_map.md" not in unique_sources:
                unique_sources.append("pgia_student_journey_map.md")

        context_text = "\n\n---\n\n".join(context_parts)

        system_prompt = (
            "You are the official postgraduate Academic AI Assistant for the Postgraduate Institute of Agriculture (PGIA), University of Peradeniya.\n"
            "Your goal is to provide beautifully structured, highly readable, executive answers strictly grounded in the official excerpts.\n\n"
            "WHATSAPP FORMATTING & COLOR-ACCENT DIRECTIVES:\n"
            "1. SECTION HEADERS WITH COLOR-CODED EMOJIS:\n"
            "   - Use structured section headers styled with bold and distinct colored emojis:\n"
            "     📌 *Topic Title* (The primary topic of the response)\n"
            "     🔹 *Overview & Scope* (Brief 1-2 sentence context)\n"
            "     📋 *Key Requirements* or 🎓 *Eligibility Criteria*\n"
            "     💰 *Fee Structure & Payments*\n"
            "     📞 *Official Contact Lines & Staff Directory*\n"
            "     📝 *Application & Verification Procedure*\n"
            "     🗓️ *Important Deadlines & Semesters*\n"
            "   - Use single asterisks for bold: *Bold Title* (NEVER use markdown hashes '#' or double asterisks '**').\n\n"
            "2. CLEAN ALIGNED BULLET POINTS:\n"
            "   - For main items, use the clean bullet '• ' followed by *Bold Feature Label:* then details.\n"
            "     Example:\n"
            "     • *Degree Requirement:* 4-Year Bachelor's Honours Degree (SLQF Level 6).\n"
            "     • *Minimum GPA:* At least 3.00 on a 4.00 scale.\n"
            "   - For nested sub-details, indent with '  ▫️ '.\n"
            "   - Leave a clean blank line between different major sections for mobile screen comfort.\n\n"
            "3. HIGHLIGHT QUOTE BOXES (WHATSAPP BLOCKQUOTES):\n"
            "   - For vital discounts, payment portals, contact details, or cautions, place them inside a WhatsApp blockquote (prefix lines with '> ').\n"
            "     Example:\n"
            "     > 💡 *Special Payment Privilege:*\n"
            "     > A 10% fee waiver applies when the full program fee is paid at once via credit card or cash.\n\n"
            "4. GROUNDING INTEGRITY:\n"
            "   - State exact numbers, credit amounts, and procedures from the reference excerpts.\n"
            "   - Never hallucinate external universities or unverified rules.\n\n"
            "5. CONTACT DIRECTORY & PHONE NUMBERS (CRITICAL - NEVER REDACT OR USE 'XXXXXX'):\n"
            "   - All PGIA telephone numbers, office lines, staff extensions, hotlines, and emails are public institutional directory information.\n"
            "   - ALWAYS display complete, full real telephone numbers (e.g. +94 812 388203, +94 812 386542, +94 812 385517, +94 81 2388692, +94 81 2389726, +94 81 2388956, 081-2389182).\n"
            "   - NEVER redact, mask, or replace digits with 'xxxxxx', '***', or placeholder characters under any circumstances.\n"
            "   - Present the full numbers clearly alongside the respective officer or branch so users can dial directly.\n\n"
            "6. ACADEMIC LIFECYCLE & PATHWAY GUIDANCE:\n"
            "   - When a student asks about their progression, requirements, 'what should I do next', comprehensive exams, thesis defense, credit rules, or passing out, structure the answer using the official PGIA Academic Journey Map.\n"
            "   - Address their current status, exact prerequisites for the next gate, critical deadlines, forms required, and contingency/exit routes (e.g. Postgraduate Diploma exit, repeating CE, upgrading M.Phil to PhD).\n\n"
            "=== OFFICIAL PGIA REFERENCE EXCERPTS ===\n"
            f"{context_text}"
        )

        messages = [
            SystemMessage(content=system_prompt),
            HumanMessage(content=question)
        ]

        try:
            response = self.llm.invoke(messages)
            raw_answer = response.content
        except Exception as e:
            print(f"[HybridEngine] LangChain Groq Error: {e}, falling back to Qwen...")
            fallback_llm = ChatGroq(
                api_key=self.groq_api_key,
                model_name="qwen/qwen3.8-27b",
                temperature=0.15
            )
            response = fallback_llm.invoke(messages)
            raw_answer = response.content

        formatted_answer = self.format_whatsapp(raw_answer, unique_sources, matches[0]["confidence"])
        total_time = (time.time() - t0) * 1000

        return {
            "answer": formatted_answer,
            "raw_answer": raw_answer,
            "sources": unique_sources,
            "top_score": matches[0]["confidence"],
            "latency_ms": round(total_time, 1)
        }

    def format_whatsapp(self, text: str, sources: List[str], confidence: float) -> str:
        # Convert any markdown headers to bold with emoji
        text = re.sub(r'^\s*#{1,6}\s*(.*?)$', r'📌 *\1*', text, flags=re.MULTILINE)

        # Convert double asterisks to single asterisks for WhatsApp
        text = re.sub(r'\*\*(.*?)\*\*', r'*\1*', text)

        # Fix broken question mark bullets from legacy encodings
        text = re.sub(r'^\s*\?\?\s*', '• ', text, flags=re.MULTILINE)
        text = re.sub(r'^\s*\?\s*', '• ', text, flags=re.MULTILINE)

        # Standardize dashes and asterisks bullets
        text = re.sub(r'^\s*[\*\-]\s+', '• ', text, flags=re.MULTILINE)
        text = text.strip()

        divider = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
        source_text = ", ".join(sources) if sources else "Official PGIA Handbooks"
        score_percent = round(confidence * 100)

        header = (
            "🏛️ *POSTGRADUATE INSTITUTE OF AGRICULTURE*\n"
            "_University of Peradeniya • Institutional AI Desk_\n"
            f"{divider}\n\n"
        )

        footer = (
            f"\n\n{divider}\n"
            "🏛️ *PGIA Official Knowledge Base*\n"
            f"🎯 _Verification Confidence: {score_percent}% High Fidelity_"
        )

        return header + text + footer
