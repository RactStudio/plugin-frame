#!/bin/bash

# Detect OS and set download URL and executable name
detect_os() {
  if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    DOWNLOAD_URL="https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64"
    EXECUTABLE="cloudflared"
  elif [[ "$OSTYPE" == "darwin"* ]]; then
    DOWNLOAD_URL="https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-darwin-amd64.tgz"
    EXECUTABLE="cloudflared"
  elif [[ "$OSTYPE" == "msys"* || "$OSTYPE" == "cygwin"* || "$OSTYPE" == "win32" ]]; then
    DOWNLOAD_URL="https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.exe"
    EXECUTABLE="cloudflared.exe"
  else
    echo "Unsupported OS: $OSTYPE"
    exit 1
  fi
}

# Download and prepare Cloudflared
download_cloudflared() {
  echo "Downloading cloudflared..."
  if [[ "$OSTYPE" == "darwin"* ]]; then
    curl -L "$DOWNLOAD_URL" -o cloudflared.tgz
    tar -xzf cloudflared.tgz
    rm -f cloudflared.tgz
    chmod +x cloudflared.exe
  else
    curl -L "$DOWNLOAD_URL" -o "$EXECUTABLE"
  fi
  chmod +x "$EXECUTABLE"
}

# Start Cloudflared Tunnel
start_tunnel() {
  local url="http://$1"
  echo "Starting tunnel to http://$url..."
  ./"$EXECUTABLE" tunnel --url "$url" &> cloudflared_tunnel.log &
  local pid=$!

  echo "Waiting for the tunnel URL..."
  while true; do
    local tunnel_url
    tunnel_url=$(grep -m 1 'https://.*trycloudflare.com' cloudflared_tunnel.log)
    if [[ -n "$tunnel_url" ]]; then
      echo "Tunnel started successfully!"
      echo "Access your service at: $tunnel_url"
      break
    fi
    sleep 1
  done

  echo "Tunnel is running in the background (PID: $pid)."
}

# Main Script Logic
main() {
  detect_os
  if [[ ! -f "$EXECUTABLE" ]]; then
    download_cloudflared
  else
    echo "Cloudflared already exists in the current directory."
  fi

  read -p "Enter the URL to tunnel (e.g., localhost:8000): " tunnel_url
  start_tunnel "$tunnel_url"
}

main
