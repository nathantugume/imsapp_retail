@echo off
REM IMS Retail - Windows Launcher
REM Double-click this file to start the application

title IMS Retail - Mini Price Hardware
color 0A

echo ==========================================
echo   IMS Retail - Mini Price Hardware
echo   Inventory Management System
echo ==========================================
echo.

REM Configuration
set PORT=8080
set DB_NAME=imsapp_retail

REM Check if PHP is installed
echo Checking requirements...
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo.
    echo [ERROR] PHP is not installed or not in PATH
    echo.
    echo Please install PHP from one of these:
    echo   - XAMPP: https://www.apachefriends.org/
    echo   - WAMP: https://www.wampserver.com/
    echo   - PHP Standalone: https://windows.php.net/download/
    echo.
    echo Make sure to add PHP to your system PATH
    echo.
    pause
    exit /b 1
)

REM Get PHP version
for /f "tokens=*" %%i in ('php -v ^| findstr /C:"PHP"') do set PHP_VERSION=%%i
echo [OK] %PHP_VERSION%

REM Check if MySQL is accessible
echo Checking database connection...
mysql -u root -padmin -e "USE %DB_NAME%" 2>nul
if %errorlevel% neq 0 (
    echo.
    echo [WARNING] Cannot connect to database: %DB_NAME%
    echo.
    echo Please ensure:
    echo   1. MySQL/XAMPP/WAMP is running
    echo   2. Database '%DB_NAME%' exists
    echo   3. Username: root, Password: admin
    echo.
    echo Press any key to continue anyway, or Ctrl+C to exit
    pause >nul
) else (
    echo [OK] Database connection successful
)

REM Check if port is available
netstat -ano | findstr ":%PORT%" | findstr "LISTENING" >nul 2>&1
if %errorlevel% equ 0 (
    echo.
    echo [WARNING] Port %PORT% is already in use
    echo Another instance might be running, or another service is using this port.
    echo.
    pause
)

echo.
echo Starting IMS Retail...
echo ==========================================
echo Application URL: http://localhost:%PORT%
echo Database: %DB_NAME%
echo.
echo Press Ctrl+C to stop the server
echo ==========================================
echo.

REM Start PHP built-in server
echo [OK] Starting PHP server on port %PORT%...
echo.

REM Open browser after a short delay
start "" http://localhost:%PORT%

REM Run PHP server (this will block until Ctrl+C)
php -S localhost:%PORT%

REM This part runs after Ctrl+C
echo.
echo ==========================================
echo Server stopped
echo ==========================================
pause

