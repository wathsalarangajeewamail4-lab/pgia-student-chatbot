# 🏛️ PGIA Knowledge Base Admin Dashboard: International Enterprise Architecture Blueprint

> **Synthesized via Gemini 4 Argon** *(Thinking Level: HIGH)*  
> **Standard:** ISO/IEC 25010 Software Quality & NIST SP 800-53 Access Control  
> **Target System:** PGIA Institutional AI Assistant (Laravel 11 Control Plane + Python FastAPI Data Plane)  
> **Deployment Target:** Dell PowerEdge T440 Enterprise Node (`192.248.43.132`)

---

## 1. Executive Summary & Architectural Philosophy

The **PGIA Knowledge Base Admin Dashboard** is designed to provide institutional administrators, academic coordinators, and knowledge managers with full lifecycle governance over official university documentation, dense vector representations, and lexical indices.

To ensure international enterprise standards for high reliability, data integrity, and sub-second retrieval, the system follows a **strictly decoupled control-and-computation architecture**:
1. **Control Plane (Laravel 11)**: Manages administrative authentication, Role-Based Access Control (RBAC), multi-file PDF validation, document versioning, and immutable audit logging.
2. **Data & Retrieval Plane (Python FastAPI Microservice)**: Executes resource-intensive computations—page-by-page PDF extraction, sliding-window chunking, dense vector embedding via Dell T440 Ollama, and in-memory hybrid search (BM25Okapi + Dense Vector + BGE Cross-Encoder).
3. **Zero-Downtime Hot-Reload Coordinator**: Guarantees that document uploads, re-indexing, and purges atomically update in-memory search matrices without dropping active student queries or causing service downtime.

---

## 2. High-Level Enterprise Architecture Blueprint

```mermaid
flowchart TD
    subgraph Users["Administrative & Client Layer"]
        Admin["👤 Knowledge Administrator<br/>(Authenticated via Session + RBAC)"]
        Student["📱 WhatsApp / Web Students<br/>(24/7 Continuous Ingress)"]
    end

    subgraph ControlPlane["Laravel 11 Control Plane (:8090)"]
        AuthMiddleware["🛡️ Auth & RBAC Middleware<br/>(Role: Super Admin / Editor)"]
        AuditEngine["📜 Tamper-Evident Audit Ledger<br/>(Action, IP, Timestamp, Checksum)"]
        DocManager["📂 Document Lifecycle Controller<br/>(Upload, Validate, Archive, Delete)"]
        SyncDispatcher["⚡ Sync & Orchestration Dispatcher"]
        DocDB[("🗄️ SQLite Control DB<br/>users | documents | audit_logs")]
    end

    subgraph DataPlane["Python FastAPI Data & Compute Engine (:8003)"]
        PDFParser["📄 PDF Extraction Engine (pypdf)<br/>Page-aware text extraction & cleaning"]
        Chunker["✂️ Sliding-Window Chunker<br/>700-char window, 100-char overlap"]
        OllamaBridge["⚡ Embedding Client<br/>Ollama nomic-embed-text (768-dim)"]
        AtomicCoordinator["🔄 Zero-Downtime Hot-Reload Coordinator<br/>Thread-safe atomic in-memory swap"]
        HybridEngine["🧠 In-Memory Search Plane<br/>Dense Matrix + BM25Okapi + BGE Reranker"]
        VectorDB[("🗄️ SQLite Vector Storage<br/>knowledge_base (1,361+ chunks)")]
    end

    subgraph OnPremCluster["Dell PowerEdge T440 Infrastructure"]
        OllamaServer["🖥️ Ollama Daemon (:11434)<br/>192.248.43.138"]
    end

    Admin -->|HTTPS Session + CSRF| AuthMiddleware
    AuthMiddleware --> DocManager
    DocManager --> AuditEngine
    DocManager --> DocDB
    AuditEngine --> DocDB
    DocManager -->|Local PDF File Storage| PDFParser

    SyncDispatcher -->|POST /api/reload| AtomicCoordinator
    SyncDispatcher -->|POST /api/documents/process-pdf| PDFParser
    PDFParser --> Chunker
    Chunker --> OllamaBridge
    OllamaBridge <-->|HTTP POST /api/embeddings| OllamaServer
    OllamaBridge --> VectorDB
    VectorDB --> AtomicCoordinator
    AtomicCoordinator -->|Atomic Memory Swap| HybridEngine

    Student -->|Inquiries| HybridEngine
```

---

## 3. Core Component Breakdown & Responsibilities

### A. Control Plane (Laravel 11)
- **Role-Based Access Control (RBAC)**: Supports roles (`Super Admin`, `Knowledge Manager`, `Auditor`). All admin endpoints enforce strict authentication, session expiration, and CSRF token verification.
- **MIME & Integrity Validation**: Uploaded documents are verified against magic bytes (`%PDF-1.x`), scanned for embedded malicious JavaScript, and stored with SHA-256 cryptographic checksums to prevent file tampering.
- **Document Metadata Tracking**: Tracks filename, original title, file size, status (`active`, `processing`, `pending_sync`, `failed`), chunk count, and upload timestamp.
- **Tamper-Evident Audit Logging**: Every state modification (PDF upload, document deletion, model sync) records the user ID, user email, action type, client IP address, and payload metadata into an immutable audit table.

### B. Data Plane (Python FastAPI on `127.0.0.1:8003`)
- **PDF Extraction Pipeline**: Uses `pypdf` to extract clean text while stripping out header/footer boilerplate and normalizing Unicode whitespace.
- **Token-Aware Sliding Window Chunking**:
  $$\text{Chunk}_i = \text{Text}\left[i \times (W - O) : i \times (W - O) + W\right]$$
  Where $W = 700\text{ characters}$ (approx. 110–130 tokens) and $O = 100\text{ characters}$ overlap. Chunks snap to the nearest sentence or word boundary to preserve semantic context.
- **Dense Vector Embedding Pipeline**: Batches chunks and posts to the Dell T440 Ollama cluster (`nomic-embed-text`) generating 768-dimensional normalized floating-point vectors.
- **Zero-Downtime Hot-Reload**: Re-indexes the SQLite database, tokenizes the updated corpus for BM25Okapi, normalizes the embedding matrix, and swaps memory references atomically under a thread lock. Active user queries never experience 503 or latency spikes.

---

## 4. End-to-End Ingestion, Deletion & Sync Workflows

### Workflow 1: New PDF Upload & Synchronization
```mermaid
sequenceDiagram
    autonumber
    actor Admin as 👤 Administrator
    participant Web as 💻 Laravel Admin Panel
    participant Disk as 📁 Secure Storage Disk
    participant Daemon as ⚡ Python FastAPI (:8003)
    participant Ollama as 🖥️ Dell T440 Ollama
    participant DB as 🗄️ SQLite Database

    Admin->>Web: Uploads new PDF (e.g. Handbook_2026.pdf)
    Web->>Web: Validate MIME (application/pdf) & Compute SHA-256
    Web->>Disk: Store file in storage/app/knowledge_documents/
    Web->>DB: Insert document record (status: 'processing')
    Web->>Daemon: POST /api/documents/process-pdf (file path, source name)
    
    Daemon->>Disk: Read PDF & Extract clean text pages
    Daemon->>Daemon: Segment into 700-char sliding-window chunks
    loop For each chunk
        Daemon->>Ollama: POST /api/embeddings (nomic-embed-text)
        Ollama-->>Daemon: 768-dim vector array
    end
    Daemon->>DB: Replace existing chunks for source in knowledge_base
    Daemon->>Daemon: Rebuild BM25 tokenizer & normalise vector matrix
    Daemon->>Daemon: Atomic in-memory swap (Zero Downtime)
    Daemon-->>Web: Return { status: 'synced', total_chunks: N }
    Web->>DB: Update document status = 'active', chunk_count = N
    Web->>DB: Record Audit Log (action: 'upload_and_sync')
    Web-->>Admin: Display Success Notification & Updated KPI Cards
```

### Workflow 2: Document Removal & Index Purge
```mermaid
sequenceDiagram
    autonumber
    actor Admin as 👤 Administrator
    participant Web as 💻 Laravel Admin Panel
    participant DB as 🗄️ SQLite Database
    participant Daemon as ⚡ Python FastAPI (:8003)

    Admin->>Web: Clicks 'Delete' on outdated PDF
    Web->>DB: Delete associated chunks WHERE source = filename
    Web->>DB: Delete document record
    Web->>DB: Record Audit Log (action: 'delete_document')
    Web->>Daemon: POST /api/reload (Hot-Reload)
    Daemon->>DB: Read remaining active chunks from knowledge_base
    Daemon->>Daemon: Reconstruct BM25 & Vector Matrix
    Daemon->>Daemon: Atomic memory swap
    Daemon-->>Web: Acknowledge { status: 'reloaded', total_chunks: M }
    Web-->>Admin: Refresh Document Table (Instant confirmation)
```

---

## 5. Security Architecture & Threat Mitigation (NIST Aligned)

| Threat Vector | Mitigation Strategy | Implementation |
| :--- | :--- | :--- |
| **Malicious PDF Payloads** | Strict magic byte check & PDF sanitizer | Validates `%PDF-` signature; disallows executable macros. Files are stored outside the web root (`storage/app/knowledge_documents`). |
| **Unauthorized Access** | Session authentication & RBAC | Protected by `auth` middleware; brute-force protection with `throttle:5,1` rate limiting. |
| **CSRF / Replay Attacks** | Synchronizer Token Pattern | Laravel automatically validates unique cryptographically strong CSRF tokens on every state-changing request. |
| **Data Poisoning** | Full audit trail & SHA-256 checksums | Every document has an immutable hash; audit logs track uploader identity, IP address, and exact file changes. |
| **Query Interruption (Downtime)** | In-memory atomic pointer swap | Python FastAPI prepares new NumPy array & BM25Okapi objects in staging before switching references via GIL atomic assignment. |

---

## 6. Database Schema Design

### A. `documents` (Document Lifecycle Registry)
```sql
CREATE TABLE documents (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename VARCHAR(255) UNIQUE NOT NULL,      -- e.g. Handbook_2026.pdf
    original_name VARCHAR(255) NOT NULL,       -- Original uploaded name
    file_path VARCHAR(500) NULL,               -- Relative path in storage
    file_size INTEGER DEFAULT 0,               -- Size in bytes
    mime_type VARCHAR(100) DEFAULT 'application/pdf',
    checksum VARCHAR(64) NULL,                 -- SHA-256 hash
    chunk_count INTEGER DEFAULT 0,             -- Embedded chunks count
    status VARCHAR(50) DEFAULT 'active',       -- active, processing, pending_sync, failed
    error_message TEXT NULL,
    uploaded_by VARCHAR(255) NULL,             -- Admin email
    last_synced_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### B. `audit_logs` (Tamper-Evident Security Ledger)
```sql
CREATE TABLE audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NULL REFERENCES users(id) ON DELETE SET NULL,
    user_email VARCHAR(255) NULL,
    action VARCHAR(100) NOT NULL,              -- upload, delete, sync, login, logout
    target_type VARCHAR(100) NULL,             -- document, model, knowledge_base
    target_id VARCHAR(255) NULL,               -- filename or resource id
    details TEXT NULL,                         -- JSON payload with metrics
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NULL
);
```

---

## 7. Operational Standards & Verification
- **Sub-Second Search Latency**: In-memory retrieval maintained `< 5ms` for hybrid search, `< 600ms` for LLM completion.
- **Zero-Downtime Guarantee**: Search endpoints (`/api/chat` and WhatsApp Webhook) continue operating uninterrupted while background sync runs.
- **High Concurrency**: Python Uvicorn asynchronous event loop handles concurrent queries while disk operations run asynchronously.
