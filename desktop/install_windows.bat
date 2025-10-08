@echo off
title Inventory Management System - Windows Installer
color 0A
echo.
echo ================================================================
echo           Inventory Management System - Windows Installer
echo ================================================================
echo.

REM Check if running as administrator
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo This installer requires administrator privileges.
    echo Please right-click and select "Run as administrator"
    pause
    exit /b 1
)

echo Checking system requirements...
echo.

REM Check Python
echo [1/4] Checking Python...
python --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Python not found!
    echo.
    echo Please install Python 3.6 or higher:
    echo 1. Go to https://python.org/downloads/
    echo 2. Download Python 3.x for Windows
    echo 3. Run the installer and check "Add Python to PATH"
    echo 4. Restart this installer
    echo.
    pause
    exit /b 1
) else (
    for /f "tokens=2" %%i in ('python --version 2^>^&1') do set PYTHON_VERSION=%%i
    echo ✅ Python %PYTHON_VERSION% found
)

REM Check PHP
echo.
echo [2/4] Checking PHP...
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ PHP not found!
    echo.
    echo Please install PHP 7.4 or higher:
    echo 1. Download XAMPP from https://xampp.org
    echo 2. Install XAMPP (includes PHP and MySQL)
    echo 3. Add PHP to your PATH:
    echo    - Add C:\xampp\php to your system PATH
    echo 4. Restart this installer
    echo.
    pause
    exit /b 1
) else (
    for /f "tokens=2" %%i in ('php --version 2^>^&1 ^| findstr "PHP"') do set PHP_VERSION=%%i
    echo ✅ PHP %PHP_VERSION% found
)

REM Check MySQL
echo.
echo [3/4] Checking MySQL...
mysql -u root -padmin -e "SELECT 1" >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ MySQL connection failed!
    echo.
    echo Please ensure MySQL is running with these settings:
    echo - Host: localhost
    echo - User: root  
    echo - Password: admin
    echo - Database: imsapp
    echo.
    echo If using XAMPP:
    echo 1. Start XAMPP Control Panel
    echo 2. Start MySQL service
    echo 3. Set MySQL root password to 'admin'
    echo.
    pause
    exit /b 1
) else (
    echo ✅ MySQL connection successful
)

REM Setup database
echo.
echo [4/4] Setting up database...
mysql -u root -padmin -e "CREATE DATABASE IF NOT EXISTS imsapp" >nul 2>&1
if %errorlevel% equ 0 (
    echo ✅ Database 'imsapp' created/verified
) else (
    echo ⚠️  Database setup failed, but continuing...
)

echo.
echo ================================================================
echo                    Creating Desktop Shortcuts
echo ================================================================

REM Create desktop shortcut
set DESKTOP=%USERPROFILE%\Desktop
set SHORTCUT_PATH=%DESKTOP%\Inventory Management System.lnk

echo Creating desktop shortcut...

REM Create VBS script to make shortcut
echo Set oWS = WScript.CreateObject("WScript.Shell") > "%TEMP%\CreateShortcut.vbs"
echo sLinkFile = "%SHORTCUT_PATH%" >> "%TEMP%\CreateShortcut.vbs"
echo Set oLink = oWS.CreateShortcut(sLinkFile) >> "%TEMP%\CreateShortcut.vbs"
echo oLink.TargetPath = "%~dp0start_ims.bat" >> "%TEMP%\CreateShortcut.vbs"
echo oLink.WorkingDirectory = "%~dp0" >> "%TEMP%\CreateShortcut.vbs"
echo oLink.Description = "Inventory Management System" >> "%TEMP%\CreateShortcut.vbs"
echo oLink.Save >> "%TEMP%\CreateShortcut.vbs"

cscript //nologo "%TEMP%\CreateShortcut.vbs" >nul 2>&1
if %errorlevel% equ 0 (
    echo ✅ Desktop shortcut created
) else (
    echo ⚠️  Could not create desktop shortcut
)

del "%TEMP%\CreateShortcut.vbs" >nul 2>&1

REM Create start menu entry
set START_MENU=%APPDATA%\Microsoft\Windows\Start Menu\Programs
set IMS_FOLDER=%START_MENU%\Inventory Management System

echo Creating start menu entry...
if not exist "%IMS_FOLDER%" mkdir "%IMS_FOLDER%"

echo Set oWS = WScript.CreateObject("WScript.Shell") > "%TEMP%\CreateStartMenu.vbs"
echo sLinkFile = "%IMS_FOLDER%\Inventory Management System.lnk" >> "%TEMP%\CreateStartMenu.vbs"
echo Set oLink = oWS.CreateShortcut(sLinkFile) >> "%TEMP%\CreateStartMenu.vbs"
echo oLink.TargetPath = "%~dp0start_ims.bat" >> "%TEMP%\CreateStartMenu.vbs"
echo oLink.WorkingDirectory = "%~dp0" >> "%TEMP%\CreateStartMenu.vbs"
echo oLink.Description = "Inventory Management System" >> "%TEMP%\CreateStartMenu.vbs"
echo oLink.Save >> "%TEMP%\CreateStartMenu.vbs"

cscript //nologo "%TEMP%\CreateStartMenu.vbs" >nul 2>&1
if %errorlevel% equ 0 (
    echo ✅ Start menu entry created
) else (
    echo ⚠️  Could not create start menu entry
)

del "%TEMP%\CreateStartMenu.vbs" >nul 2>&1

echo.
echo ================================================================
echo                    Installation Complete!
echo ================================================================
echo.
echo ✅ Inventory Management System has been installed successfully!
echo.
echo You can now start the application by:
echo 1. Double-clicking the desktop shortcut
echo 2. Using the Start Menu: Inventory Management System
echo 3. Running: start_ims.bat
echo.
echo The application will:
echo - Start a local PHP server
echo - Open in your default web browser
echo - Run on http://localhost:8080
echo.
echo Default login credentials:
echo - Master: admin / admin123
echo - User: user / user123
echo.
echo Press any key to start the application now...
pause >nul

echo.
echo Starting Inventory Management System...
start "" "%~dp0start_ims.bat"


