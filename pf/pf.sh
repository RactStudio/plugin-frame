#!/bin/bash

# Define directories
ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/../" && pwd )"
DIST_DIR="$ROOT_DIR/.dist/plugin-frame"
SCRIPT_DIR="$ROOT_DIR/pf"

# Check and create .env if missing or empty
check_env() {
    if [ ! -f "$ROOT_DIR/.env" ] || [ ! -s "$ROOT_DIR/.env" ]; then
        echo ".env file not found or is empty! Let's create it..."
        read -p "Enter NEW_NAMESPACE: " NEW_NAMESPACE
        read -p "Enter PLUGIN_PREFIX: " PLUGIN_PREFIX
        
        # Validate input
        if [ -z "$NEW_NAMESPACE" ] || [ -z "$PLUGIN_PREFIX" ]; then
            echo "Error: NEW_NAMESPACE and PLUGIN_PREFIX cannot be empty!"
            exit 1
        fi
        
        # Create .env file
        echo "NEW_NAMESPACE=$NEW_NAMESPACE" > "$ROOT_DIR/.env"
        echo "PLUGIN_PREFIX=$PLUGIN_PREFIX" >> "$ROOT_DIR/.env"
        echo "Created .env file in $ROOT_DIR"
    fi
    
    # Load .env file
    if ! source "$ROOT_DIR/.env"; then
        echo "Error: Failed to load .env file!"
        exit 1
    fi
    
    # Validate loaded variables
    if [ -z "$NEW_NAMESPACE" ] || [ -z "$PLUGIN_PREFIX" ]; then
        echo "Error: NEW_NAMESPACE or PLUGIN_PREFIX is empty in .env file!"
        exit 1
    fi
}

# Verify directories
verify_directories() {
    if [ ! -d "$DIST_DIR" ]; then
        echo "Error: .dist/plugin-frame directory not found!"
        exit 1
    fi
}

# Set replacement values
set_replacements() {
    NEW_PREFIX="$PLUGIN_PREFIX"
    NEW_PREFIX_UPPER=$(echo "$NEW_PREFIX" | tr '[:lower:]' '[:upper:]')
    
    declare -gA REPLACEMENTS=(
        ["pf"]="${NEW_PREFIX}"
        ["pf-"]="${NEW_PREFIX}-"
        ["pf_"]="${NEW_PREFIX}_"
        ["PF"]="${NEW_PREFIX_UPPER}"
        ["PF-"]="${NEW_PREFIX_UPPER}-"
        ["PF_"]="${NEW_PREFIX_UPPER}_"
    )
}

# Process files
process_files() {
    local total_files=0
    local processed_files=0
    
    # Count total files
    total_files=$(find "$DIST_DIR" -type f \( -name "*.php" -o -name "*.twig" -o -name "*.html" -o -name "*.js" -o -name "*.css" \) | wc -l)
    
    if [ "$total_files" -eq 0 ]; then
        echo "No files found to process in $DIST_DIR!"
        return
    fi
    
    echo "Processing $total_files files in $DIST_DIR..."
    
    # Process each file
    find "$DIST_DIR" -type f \( -name "*.php" -o -name "*.twig" -o -name "*.html" -o -name "*.js" -o -name "*.css" \) | while read -r file; do
        processed_files=$((processed_files + 1))
        
        # Get relative path
        relative_path="${file#$DIST_DIR/}"
        
        echo "Processing file $processed_files/$total_files: .dist -> $relative_path"
        
        # Replace all namespaces
        sed -i.bak -E \
            -e "s/(namespace|use|new|\\\\)(\s*)([a-zA-Z0-9_]+)\\\\/\1\2${NEW_NAMESPACE}\\\\\3\\\\/g" \
            -e "s/(namespace|use|new|\\\\)(\s*)([a-zA-Z0-9_]+);/\1\2${NEW_NAMESPACE}\\\\\3;/g" \
            "$file"
        
        # Replace prefixes
        for pattern in "${!REPLACEMENTS[@]}"; do
            replacement="${REPLACEMENTS[$pattern]}"
            sed -i.bak -E "s/${pattern}/${replacement}/g" "$file"
        done
        
        # Remove backup
        rm -f "$file.bak"
    done
    
    echo "Processed $processed_files files successfully!"
}

# Main execution
main() {
    echo "Starting script..."
    
    check_env
    verify_directories
    set_replacements
    process_files
    
    echo "Updates complete! All replacements done in .dist/pluginframe directory"
}

# Run main function
main