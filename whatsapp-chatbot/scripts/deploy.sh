#!/bin/bash
set -e

echo "=== [$(date '+%Y-%m-%d %H:%M:%S')] Starting PGIA Production Deployment ==="

REPO_DIR="/opt/pgia-student-chatbot"
APP_DIR="/var/www/whatsapp-chatbot"

# 1. Pull latest code from GitHub main
echo "[1/6] Pulling latest code from GitHub..."
cd "$REPO_DIR"
git fetch origin main
git reset --hard origin/main
COMMIT_HASH=$(git rev-parse --short HEAD)
COMMIT_MSG=$(git log -1 --pretty=%B | head -n 1)
echo "  Deploying commit: $COMMIT_HASH - $COMMIT_MSG"

# 2. Rsync code to /var/www/whatsapp-chatbot while protecting database, .env, and uploaded files
echo "[2/6] Syncing code to $APP_DIR..."
rsync -av \
  --exclude='.env' \
  --exclude='database/database.sqlite*' \
  --exclude='storage/app/knowledge_documents/' \
  --exclude='storage/logs/' \
  --exclude='storage/framework/' \
  --exclude='vendor/' \
  --exclude='node_modules/' \
  --exclude='python/venv/' \
  "$REPO_DIR/whatsapp-chatbot/" "$APP_DIR/"

# 3. Ensure permissions
echo "[3/6] Setting permissions..."
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" 2>/dev/null || true

# 4. Run Laravel migrations & optimize cache
echo "[4/6] Running Laravel migrations & caching..."
cd "$APP_DIR"
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Restart Python Hybrid RAG Service
echo "[5/6] Restarting Python Hybrid RAG Microservice..."
sudo systemctl restart pgia-hybrid-rag.service

# 6. Verify health with retry loop
echo "[6/6] Verifying health checks..."
WEB_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8090/ || echo "000")

RAG_STATUS="000"
for i in {1..15}; do
  RAG_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8003/health || echo "000")
  if [ "$RAG_STATUS" = "200" ]; then
    break
  fi
  sleep 1
done

echo "  Web App HTTP Status : $WEB_STATUS"
echo "  RAG Microservice    : $RAG_STATUS"

if [ "$WEB_STATUS" = "200" ] || [ "$WEB_STATUS" = "302" ]; then
  echo "✅ Deployment successful! PGIA AI Chatbot is live on commit $COMMIT_HASH."
else
  echo "❌ Error: Web app returned HTTP $WEB_STATUS"
  exit 1
fi
