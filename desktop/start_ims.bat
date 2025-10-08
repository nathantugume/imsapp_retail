@echo off
title Inventory Management System - Desktop Launcher
echo Starting Inventory Management System...
echo.

REM Check if Python is available
python --version >nul 2>&1
if %errorlevel% neq 0 (
    echo Python is not installed or not in PATH.
    echo Please install Python 3.6 or higher from https://python.org
    pause
    exit /b 1
)

REM Run the Python launcher
python desktop_launcher.py

pause




