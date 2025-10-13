#!/bin/bash

# IMS Retail - Desktop Shortcut Installer
# This script creates a desktop shortcut for easy access to IMS Retail

echo "=========================================="
echo "IMS Retail - Desktop Shortcut Installer"
echo "=========================================="
echo

# Get the current directory (where the script is located)
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
DESKTOP_FILE="$SCRIPT_DIR/IMS_Retail.desktop"

# Check if Desktop directory exists
DESKTOP_DIR="$HOME/Desktop"
if [ ! -d "$DESKTOP_DIR" ]; then
    echo "⚠️  Desktop directory not found at: $DESKTOP_DIR"
    echo "Creating Desktop directory..."
    mkdir -p "$DESKTOP_DIR"
fi

# Make the start script executable
echo "Making start script executable..."
chmod +x "$SCRIPT_DIR/desktop/start_ims.sh"
chmod +x "$SCRIPT_DIR/desktop/desktop_launcher.py"

# Copy desktop file to Desktop
echo "Installing desktop shortcut..."
cp "$DESKTOP_FILE" "$DESKTOP_DIR/"

# Make the desktop file executable
chmod +x "$DESKTOP_DIR/IMS_Retail.desktop"

# Also install to applications menu (optional)
LOCAL_APPS="$HOME/.local/share/applications"
if [ -d "$LOCAL_APPS" ]; then
    echo "Installing to applications menu..."
    cp "$DESKTOP_FILE" "$LOCAL_APPS/"
    chmod +x "$LOCAL_APPS/IMS_Retail.desktop"
fi

echo
echo "✅ Installation Complete!"
echo
echo "Desktop shortcut installed at:"
echo "  📁 $DESKTOP_DIR/IMS_Retail.desktop"
echo
if [ -d "$LOCAL_APPS" ]; then
    echo "Also available in applications menu as 'IMS Retail'"
    echo
fi
echo "Usage:"
echo "  1. Double-click the 'IMS Retail' icon on your desktop"
echo "  2. Or search for 'IMS Retail' in your application launcher"
echo "  3. The system will start PHP server and open in browser"
echo
echo "Default URL: http://localhost:8080"
echo "Database: imsapp_retail"
echo
echo "First time login:"
echo "  Email: admin@gmail.com"
echo "  Password: test1234"
echo
echo "=========================================="
echo "Enjoy your IMS Retail system!"
echo "=========================================="

