# PGIA Student Assistant & WhatsApp Chatbot - Architecture & Backup

**Last Updated**: October 27, 2026  
**Server IP**: `192.248.43.132` (Dell T440 Cluster / Ubuntu)  
**Web Port**: `8090` (http://192.248.43.132:8090)  
**Hybrid RAG Microservice Port**: `8003` (http://127.0.0.1:8003)

---

## 🚀 Advanced Hybrid RAG & MCP Stack

| Component | Technology | Description |
| :--- | :--- | :--- |
| **Lexical String Search** | `rank_bm25` (BM25Okapi) | In-memory tokenized search for exact course codes, fee figures, and policy terms (<3ms) |
| **Semantic Vector Search** | `numpy` + Dell T440 (`nomic-embed-text`) | 768-dim dense embedding matrix across 1,361 official handbook chunks |
| **Rank Fusion** | Reciprocal Rank Fusion (RRF $k=60$) | Mathematically merges Vector & BM25 top ranks so exact terms and concepts co-elevate |
| **Neural Re-Ranker** | `BAAI/bge-reranker-base` (ONNX fastembed) | Joint cross-encoder scoring of top candidates; eliminates hallucinations & false positives |
| **LLM Generation** | Groq LPU (`openai/gpt-oss-120b` / Qwen 27B) | Ultra-fast (<500ms) grounded response generation formatted for mobile WhatsApp |
| **Model Context Protocol** | Python MCP 2.x Server (`pgia_mcp_server.py`) | Exposes standardized MCP tools (`pgia_hybrid_knowledge_search`) & resources (`handbooks://pgia/list`) |
| **Microservice Daemon** | FastAPI + Uvicorn (`pgia_daemon.py`) | Persistent in-memory model runner managed by `pgia-hybrid-rag.service` |

---

## 📁 Local Backup Contents

- `whatsapp-chatbot/python/`:
  - `pgia_hybrid_engine.py`: Core Hybrid RAG engine (Vector + BM25 + RRF + BGE Reranker)
  - `pgia_daemon.py`: FastAPI daemon holding models in memory on port 8003
  - `pgia_mcp_server.py`: Standardized Model Context Protocol (MCP 2.x) server
- `whatsapp-chatbot/app/Services/RagService.php`: Laravel service querying port 8003 with native fallback
- `whatsapp-chatbot/database/database.sqlite`: 1,361 embedded chunks database (22.6 MB)
- `whatsapp-chatbot/storage/app/combined_knowledge.txt`: Full raw text from 7 official PGIA handbooks
- `configs/pgia-hybrid-rag.service`: Systemd service for the Python RAG daemon
- `configs/whatsapp-tunnel.service`: Systemd service for Cloudflare Tunnel
- `configs/run-whatsapp-tunnel.sh`: Auto-reconnect watchdog script
- `whatsapp-chatbot-live-backup-pre-mcp.tar.gz`: Pre-upgrade clean archive (23 MB)
