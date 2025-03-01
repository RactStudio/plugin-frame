#!/bin/bash

# Detect OS and set download URL and executable name
detect_os() {
  if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    DOWNLOAD_URL="https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-amd64.tgz"
    EXECUTABLE="pf/ngrok"
  elif [[ "$OSTYPE" == "darwin"* ]]; then
    DOWNLOAD_URL="https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-darwin-arm64.zip"
    EXECUTABLE="pf/ngrok"
  elif [[ "$OSTYPE" == "msys"* || "$OSTYPE" == "cygwin"* || "$OSTYPE" == "win32" ]]; then
    DOWNLOAD_URL="https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-windows-amd64.zip"
    EXECUTABLE="pf/ngrok.exe"
  else
    echo "Unsupported OS: $OSTYPE"
    exit 1
  fi
}

# Download and prepare ngrok
download_ngrok() {
  echo "Downloading ngrok..."
  mkdir -p pf
  curl -L "$DOWNLOAD_URL" -o ngrok.zip
  unzip -o ngrok.zip -d pf/
  rm ngrok.zip

  # Set executable permissions for non-Windows systems
  if [[ "$OSTYPE" != "msys"* && "$OSTYPE" != "cygwin"* && "$OSTYPE" != "win32" ]]; then
    chmod +x "$EXECUTABLE"
  fi

  # Verify the ngrok version
  local ngrok_version
  ngrok_version=$(./"$EXECUTABLE" --version | awk '{print $2}')
  echo "ngrok version: $ngrok_version"
}

# Check and configure ngrok authentication
configure_auth() {
  local config_file="$HOME/.ngrok2/ngrok.yml"
  if [[ ! -f "$config_file" ]] || ! grep -q "authtoken:" "$config_file"; then
    echo "ngrok authentication is not configured."
    read -p "Enter your ngrok authtoken (get it from https://dashboard.ngrok.com/auth): " authtoken
    read -p "Enter your ngrok API key (get it from https://dashboard.ngrok.com/api): " api_key
    if [[ -n "$authtoken" && -n "$api_key" ]]; then
      mkdir -p "$HOME/.ngrok2"
      # Create a valid ngrok v3 configuration file
      echo "version: 3" > "$config_file"
      echo "agent:" >> "$config_file"
      echo "  authtoken: $authtoken" >> "$config_file"
      echo "  api_key: $api_key" >> "$config_file"
      echo "tunnels:" >> "$config_file"
      echo "  basic:" >> "$config_file"
      echo "    proto: http" >> "$config_file"
      echo "    addr: 80" >> "$config_file"
      echo "ngrok authentication configured successfully!"
    else
      echo "No authtoken or API key provided. ngrok will run without authentication."
    fi
  else
    echo "ngrok authentication is already configured."
  fi
}

# Validate the tunnel address
validate_tunnel_address() {
  local address="$1"
  if [[ "$address" =~ ^[a-zA-Z0-9.-]+:[0-9]{1,5}$ ]]; then
    return 0
  else
    echo "Invalid tunnel address format. Expected format: localhost:PORT or IP:PORT"
    return 1
  fi
}

# Start ngrok tunnel
start_tunnel() {
  local url="$1"
  echo "Starting tunnel to $url..."

  # Start ngrok in the background and capture the tunnel URL
  nohup ./"$EXECUTABLE" http "$url" > pf/ngrok.log 2>&1 &
  local pid=$!

  echo "Waiting for the tunnel URL..."
  while true; do
    sleep 1
    if [[ -f "pf/ngrok.log" ]]; then
      tunnel_url=$(grep -m 1 -oE 'http[s]?://[a-zA-Z0-9.-]+\.ngrok\.io' pf/ngrok.log | head -n 1)
      if [[ -n "$tunnel_url" ]]; then
        echo "Tunnel started successfully!"
        echo "Access your service at: $tunnel_url"
        break
      fi
    fi
  done

  echo "Tunnel is running in the background (PID: $pid)."
  echo "To stop the tunnel, run: kill $pid"
}

# Main Script Logic
main() {
  detect_os
  mkdir -p pf

  if [[ ! -f "$EXECUTABLE" ]]; then
    download_ngrok
  else
    echo "ngrok already exists in the pf directory."
  fi

  # Configure ngrok authentication
  configure_auth

  # Prompt for tunnel address and validate it
  while true; do
    read -p "Enter the local address to tunnel (e.g., localhost:8000): " tunnel_address
    if validate_tunnel_address "$tunnel_address"; then
      break
    fi
  done

  start_tunnel "$tunnel_address"
}

main