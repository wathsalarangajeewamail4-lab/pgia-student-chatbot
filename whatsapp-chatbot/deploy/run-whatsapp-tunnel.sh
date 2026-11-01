#!/bin/bash
TUNNEL_LOG="/var/log/whatsapp-tunnel.log"
PUBLIC_TUNNEL_FILE="/var/www/whatsapp-chatbot/public/tunnel_url.txt"

while true; do
    echo "$(date -u +'%Y-%m-%dT%H:%M:%SZ') [Watchdog] Starting cloudflared tunnel on port 8090..." >> "$TUNNEL_LOG"
    
    /usr/local/bin/cloudflared tunnel --url http://127.0.0.1:8090 >> "$TUNNEL_LOG" 2>&1 &
    CF_PID=$!
    
    # Wait for tunnel URL to appear in logs and save it
    sleep 6
    NEW_URL=$(grep -oE 'https://[a-zA-Z0-9.-]+\.trycloudflare\.com' "$TUNNEL_LOG" | tail -n 1)
    if [ -n "$NEW_URL" ]; then
        echo "$NEW_URL" > "$PUBLIC_TUNNEL_FILE"
        chmod 644 "$PUBLIC_TUNNEL_FILE"
        chown www-data:www-data "$PUBLIC_TUNNEL_FILE" 2>/dev/null || true
        echo "$(date -u +'%Y-%m-%dT%H:%M:%SZ') [Watchdog] Extracted Active Tunnel URL: $NEW_URL" >> "$TUNNEL_LOG"
    fi

    # Monitor loop
    while kill -0 "$CF_PID" 2>/dev/null; do
        sleep 10
        if tail -n 20 "$TUNNEL_LOG" | grep -q "Unauthorized: Tunnel not found"; then
            echo "$(date -u +'%Y-%m-%dT%H:%M:%SZ') [Watchdog] Detected expired tunnel session. Restarting..." >> "$TUNNEL_LOG"
            kill -TERM "$CF_PID" 2>/dev/null || true
            sleep 2
            kill -KILL "$CF_PID" 2>/dev/null || true
            break
        fi
    done

    wait "$CF_PID" 2>/dev/null || true
    echo "$(date -u +'%Y-%m-%dT%H:%M:%SZ') [Watchdog] Exited. Restarting in 5s..." >> "$TUNNEL_LOG"
    sleep 5
done
