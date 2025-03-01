#!/bin/bash

# Define directories
ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"
DIST_DIR="$ROOT_DIR/.dist/plugin-frame"
SCRIPT_DIR="$ROOT_DIR/pf"

# Check and create .env if missing
check_env() {
    if [ ! -f "$ROOT_DIR/.env" ]; then
        echo ".env file not found! Let's create it..."
        read -p "Enter NEW_NAMESPACE: " NEW_NAMESPACE
        read -p "Enter PLUGIN_PREFIX: " PLUGIN_PREFIX
        
        # Create .env file
        echo "NEW_NAMESPACE=$NEW_NAMESPACE" > "$ROOT_DIR/.env"
        echo "PLUGIN_PREFIX=$PLUGIN_PREFIX" >> "$ROOT_DIR/.env"
        echo "Created .env file in $ROOT_DIR"
    fi
    
    source "$ROOT_DIR/.env"
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
    OLD_NS="PluginFrame"
    NEW_NS="${NEW_NAMESPACE}\\${OLD_NS}"
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
    find "$DIST_DIR" -type f \( -name "*.php" -o -name "*.twig" -o -name "*.html" -o -name "*.js" -o -name "*.css" \) | while read -r file; do
        
        # Replace namespace declarations and use statements
        sed -i.bak -E \
            -e "s/(namespace|use)(\s+)$OLD_NS/\1\2$NEW_NS/g" \
            "$file"
        
        # Replace prefixes
        for pattern in "${!REPLACEMENTS[@]}"; do
            replacement="${REPLACEMENTS[$pattern]}"
            sed -i.bak -E "s/${pattern}/${replacement}/g" "$file"
        done
        
        # Remove backup
        rm -f "$file.bak"
    done
}

# Main execution
main() {
    check_env
    verify_directories
    set_replacements
    process_files
    echo "Updates complete! All replacements done in .dist/plugin-frame directory"
}

# Run main function
main