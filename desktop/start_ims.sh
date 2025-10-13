#!/bin/bash

# IMS Retail - Simple Launcher (No Python Required)
# Works on Linux with just Bash

clear
echo "=========================================="
echo "  IMS Retail - Mini Price Hardware"
echo "  Inventory Management System"
echo "=========================================="
echo

# Configuration
APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
PORT=8080
URL="http://localhost:$PORT"
DB_NAME="imsapp_retail"

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if PHP is installed
echo "🔍 Checking requirements..."
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP is not installed${NC}"
    echo
    echo "Please install PHP:"
    echo "  sudo apt install php php-mysql php-zip"
    echo
    read -p "Press Enter to exit..."
    exit 1
fi

PHP_VERSION=$(php -v | head -n 1)
echo -e "${GREEN}✅ PHP found:${NC} $PHP_VERSION"

# Check if MySQL is running
if ! command -v mysql &> /dev/null; then
    echo -e "${YELLOW}⚠️  MySQL client not found (but server might be running)${NC}"
else
    if mysql -u root -padmin -e "USE $DB_NAME" 2>/dev/null; then
        echo -e "${GREEN}✅ Database connection successful${NC}"
    else
        echo -e "${RED}❌ Cannot connect to database: $DB_NAME${NC}"
        echo
        echo "Please ensure:"
        echo "  1. MySQL is running: sudo systemctl start mysql"
        echo "  2. Database exists: $DB_NAME"
        echo "  3. Credentials are correct in config/config.php"
        echo
        read -p "Continue anyway? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi
fi

# Check if port is already in use
if lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo -e "${YELLOW}⚠️  Port $PORT is already in use${NC}"
    echo
    read -p "Kill existing process and continue? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        lsof -ti:$PORT | xargs kill -9 2>/dev/null
        sleep 2
        echo -e "${GREEN}✅ Port cleared${NC}"
    else
        echo "Please stop the process using port $PORT or change the port in this script."
        read -p "Press Enter to exit..."
        exit 1
    fi
fi

echo
echo "🚀 Starting IMS Retail..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${GREEN}Application URL: $URL${NC}"
echo "Database: $DB_NAME"
echo "Press Ctrl+C to stop the server"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo

# Change to app directory
cd "$APP_DIR"

# Start PHP server in background
php -S localhost:$PORT > /dev/null 2>&1 &
PHP_PID=$!

# Wait a moment for server to start
sleep 2

# Check if server started successfully
if ps -p $PHP_PID > /dev/null; then
    echo -e "${GREEN}✅ PHP server started (PID: $PHP_PID)${NC}"
    
    # Open browser
    echo "🌐 Opening browser..."
    
    # Try different browser opening commands
    if command -v xdg-open &> /dev/null; then
        xdg-open "$URL" 2>/dev/null &
    elif command -v gnome-open &> /dev/null; then
        gnome-open "$URL" 2>/dev/null &
    elif command -v firefox &> /dev/null; then
        firefox "$URL" 2>/dev/null &
    elif command -v google-chrome &> /dev/null; then
        google-chrome "$URL" 2>/dev/null &
    else
        echo -e "${YELLOW}⚠️  Could not auto-open browser${NC}"
        echo "Please open: $URL"
    fi
    
    echo
    echo -e "${GREEN}✅ IMS Retail is now running!${NC}"
    echo
    echo "Access at: $URL"
    echo
    echo "Press Ctrl+C to stop..."
    echo
    
    # Wait for Ctrl+C
    trap "echo; echo 'Stopping server...'; kill $PHP_PID 2>/dev/null; echo -e '${GREEN}✅ Server stopped${NC}'; exit 0" INT TERM
    
    # Keep script running
    wait $PHP_PID
else
    echo -e "${RED}❌ Failed to start PHP server${NC}"
    exit 1
fi
