#!/usr/bin/env python3
"""
PGIA High-Speed Hybrid RAG FastAPI Daemon
Listens on 127.0.0.1:8003
Maintains pre-warmed Vector Matrix, BM25 Index, and Cross-Encoder in Memory.
"""

import sys
import os
CURRENT_DIR = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, CURRENT_DIR)
if os.path.exists("/var/www/whatsapp-chatbot/python"):
    sys.path.insert(0, "/var/www/whatsapp-chatbot/python")

from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from typing import Optional, List, Dict, Any
from pgia_hybrid_engine import PgiaHybridEngine

app = FastAPI(title="PGIA Hybrid RAG Microservice", version="2.0.0")
engine: Optional[PgiaHybridEngine] = None

class SearchRequest(BaseModel):
    query: str
    limit: Optional[int] = 4

class AnswerRequest(BaseModel):
    message: Optional[str] = None
    question: Optional[str] = None

class ProcessPdfRequest(BaseModel):
    file_path: str
    source_name: str

class RemoveSourceRequest(BaseModel):
    source_name: str

@app.on_event("startup")
def startup_event():
    global engine
    print("[Daemon] Warming up PGIA Hybrid Engine...")
    engine = PgiaHybridEngine.get_instance()
    print("[Daemon] PGIA Hybrid Engine successfully warmed up and ready!")

@app.get("/health")
def health():
    if engine is None:
        return {"status": "starting"}
    return {
        "status": "ok",
        "chunks_loaded": len(engine.texts),
        "engine": "hybrid_vector_bm25_rrf_bge_reranker",
        "llm": "groq_openai_gpt_oss_120b"
    }

@app.post("/api/search")
def search(req: SearchRequest):
    if not engine:
        raise HTTPException(status_code=503, detail="Engine not ready")
    q = req.query.strip()
    if not q:
        raise HTTPException(status_code=400, detail="Query cannot be empty")
    results = engine.hybrid_search(q, top_k=req.limit or 4)
    return {"query": q, "results": results}

@app.post("/api/answer")
def answer(req: AnswerRequest):
    if not engine:
        raise HTTPException(status_code=503, detail="Engine not ready")
    q = (req.message or req.question or "").strip()
    if not q:
        raise HTTPException(status_code=400, detail="Message/question cannot be empty")
    result = engine.generate_grounded_answer(q)
    return result

@app.post("/api/reload")
def reload_kb():
    if not engine:
        raise HTTPException(status_code=503, detail="Engine not ready")
    engine.load_knowledge_base()
    return {"status": "reloaded", "total_chunks": len(engine.texts)}

@app.post("/api/documents/process-pdf")
def process_pdf(req: ProcessPdfRequest):
    if not engine:
        raise HTTPException(status_code=503, detail="Engine not ready")
    try:
        saved = engine.ingest_pdf(req.file_path, req.source_name)
        return {
            "status": "success",
            "source": req.source_name,
            "chunks_saved": saved,
            "total_chunks": len(engine.texts)
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/api/documents/remove-source")
def remove_source(req: RemoveSourceRequest):
    if not engine:
        raise HTTPException(status_code=503, detail="Engine not ready")
    try:
        deleted = engine.remove_source(req.source_name)
        return {
            "status": "success",
            "source": req.source_name,
            "chunks_deleted": deleted,
            "total_chunks": len(engine.texts)
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port=8003, log_level="info")
