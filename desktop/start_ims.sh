#!/bin/bash

# Inventory Management System - Desktop Launcher
# Linux/macOS version

echo "=========================================="
echo "Inventory Management System - Desktop"
echo "=========================================="
echo

# Check if Python 3 is available
if ! command -v python3 &> /dev/null; then
    echo "❌ Python 3 is not installed or not in PATH."
    echo "Please install Python 3.6 or higher:"
    echo "  Ubuntu/Debian: sudo apt install python3"
    echo "  CentOS/RHEL: sudo yum install python3"
    echo "  macOS: brew install python3"
    echo
    read -p "Press Enter to exit..."
    exit 1
fi

# Make the Python script executable
chmod +x "$(dirname "$0")/desktop_launcher.py"

# Run the Python launcher
python3 "$(dirname "$0")/desktop_launcher.py"




