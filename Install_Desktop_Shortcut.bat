@echo off
REM IMS Retail - Desktop Shortcut Installer (Windows)
REM Creates a desktop shortcut using VBScript (built into Windows)

title IMS Retail - Desktop Shortcut Installer
color 0B

echo ==========================================
echo   IMS Retail - Desktop Shortcut Installer
echo ==========================================
echo.

REM Get current directory
set "SCRIPT_DIR=%~dp0"
set "BATCH_FILE=%SCRIPT_DIR%Start_IMS_Retail.bat"

REM Create temporary VBScript to make shortcut
echo Creating desktop shortcut...
echo.

REM Create VBScript file
(
echo Set oWS = WScript.CreateObject^("WScript.Shell"^)
echo sLinkFile = oWS.SpecialFolders^("Desktop"^) ^& "\IMS Retail.lnk"
echo Set oLink = oWS.CreateShortcut^(sLinkFile^)
echo oLink.TargetPath = "%BATCH_FILE%"
echo oLink.WorkingDirectory = "%SCRIPT_DIR%"
echo oLink.Description = "IMS Retail - Mini Price Hardware Inventory System"
echo oLink.Save
) > "%TEMP%\CreateShortcut.vbs"

REM Run VBScript
cscript //nologo "%TEMP%\CreateShortcut.vbs"

REM Clean up
del "%TEMP%\CreateShortcut.vbs"

echo [OK] Desktop shortcut created successfully!
echo.
echo ==========================================
echo Installation Complete!
echo ==========================================
echo.
echo A shortcut named 'IMS Retail' has been created on your desktop.
echo.
echo Usage:
echo   1. Double-click 'IMS Retail' icon on your desktop
echo   2. System will start PHP server automatically
echo   3. Browser will open to http://localhost:8080
echo   4. Login with: admin@gmail.com / test1234
echo.
echo Requirements:
echo   - PHP installed (XAMPP/WAMP recommended)
echo   - MySQL running
echo   - Database: imsapp_retail
echo.
echo ==========================================
echo.

set /p LAUNCH="Would you like to launch IMS Retail now? (Y/N): "
if /i "%LAUNCH%"=="Y" (
    echo.
    echo Starting IMS Retail...
    start "" "%BATCH_FILE%"
)

echo.
pause

