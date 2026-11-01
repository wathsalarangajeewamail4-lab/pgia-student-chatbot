#!/usr/bin/env python3
"""
PGIA Institutional MCP Server (Model Context Protocol 2.x)
Exposes PGIA Hybrid RAG (Dense Vector + BM25 + RRF + BGE Cross-Encoder)
over the standardized Model Context Protocol for Agent Swarms and IDEs.
"""

import sys
import os
import json
CURRENT_DIR = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, CURRENT_DIR)
if os.path.exists("/var/www/whatsapp-chatbot/python"):
    sys.path.insert(0, "/var/www/whatsapp-chatbot/python")

from mcp.server.mcpserver import MCPServer
from pgia_hybrid_engine import PgiaHybridEngine

mcp = MCPServer(name="PGIA-Institutional-MCP")
engine = None

def get_engine():
    global engine
    if engine is None:
        engine = PgiaHybridEngine.get_instance()
    return engine

@mcp.tool()
def pgia_hybrid_knowledge_search(query: str, limit: int = 4) -> str:
    """
    Search PGIA official documentation using Hybrid RAG (Dense Vector + BM25 string search)
    fused with Reciprocal Rank Fusion (RRF) and re-ranked via BAAI/bge-reranker-base.
    """
    eng = get_engine()
    results = eng.hybrid_search(query, top_k=limit)
    if not results:
        return "No matching documentation found."

    output = []
    for rank, r in enumerate(results, 1):
        output.append(
            f"[{rank}] Document: {r['source']} (Match Confidence: {r['confidence']*100:.1f}%)\n"
            f"{r['text'].strip()}"
        )
    return "\n\n---\n\n".join(output)

@mcp.tool()
def pgia_generate_grounded_answer(question: str) -> str:
    """
    Generate a strictly grounded institutional answer formatted for WhatsApp/Mobile
    using LangChain Neural Inference Core and official PGIA handbooks.
    """
    eng = get_engine()
    res = eng.generate_grounded_answer(question)
    return res["answer"]

@mcp.resource("handbooks://pgia/list")
def list_handbooks() -> str:
    """List all 7 official postgraduate handbooks and guides grounded in this knowledge base."""
    eng = get_engine()
    unique_sources = sorted(list(set(eng.sources)))
    return json.dumps({
        "institution": "Postgraduate Institute of Agriculture (PGIA)",
        "university": "University of Peradeniya",
        "grounded_handbooks": unique_sources,
        "total_chunks": len(eng.texts)
    }, indent=2)

if __name__ == "__main__":
    mcp.run()
