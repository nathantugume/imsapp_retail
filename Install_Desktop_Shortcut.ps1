# IMS Retail - Desktop Shortcut Installer (Windows)
# Run this PowerShell script to create a desktop shortcut

Write-Host "=========================================="
Write-Host "  IMS Retail - Desktop Shortcut Installer"
Write-Host "=========================================="
Write-Host ""

# Get the current directory
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$BatchFile = Join-Path $ScriptDir "Start_IMS_Retail.bat"
$IconPath = Join-Path $ScriptDir "desktop\assets\icon.png"

# Desktop path
$DesktopPath = [Environment]::GetFolderPath("Desktop")
$ShortcutPath = Join-Path $DesktopPath "IMS Retail.lnk"

Write-Host "Creating desktop shortcut..."
Write-Host "  Location: $ShortcutPath"
Write-Host ""

# Create WScript Shell object
$WshShell = New-Object -ComObject WScript.Shell

# Create shortcut
$Shortcut = $WshShell.CreateShortcut($ShortcutPath)
$Shortcut.TargetPath = $BatchFile
$Shortcut.WorkingDirectory = $ScriptDir
$Shortcut.Description = "IMS Retail - Mini Price Hardware Inventory System"
$Shortcut.WindowStyle = 1  # Normal window

# Set icon if available (Note: .lnk files prefer .ico, but will accept .png on some systems)
if (Test-Path $IconPath) {
    $Shortcut.IconLocation = $IconPath
}

# Save the shortcut
$Shortcut.Save()

Write-Host "[OK] Desktop shortcut created successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Shortcut Details:"
Write-Host "  Name: IMS Retail" -ForegroundColor Cyan
Write-Host "  Location: $DesktopPath" -ForegroundColor Cyan
Write-Host "  Launches: $BatchFile" -ForegroundColor Cyan
Write-Host ""
Write-Host "Usage:"
Write-Host "  1. Double-click 'IMS Retail' icon on your desktop"
Write-Host "  2. System will start and open in your browser"
Write-Host "  3. Login with: admin@gmail.com / test1234"
Write-Host ""
Write-Host "=========================================="
Write-Host "Installation Complete!"
Write-Host "=========================================="
Write-Host ""

# Ask if user wants to launch now
$Launch = Read-Host "Would you like to launch IMS Retail now? (Y/N)"
if ($Launch -eq "Y" -or $Launch -eq "y") {
    Write-Host ""
    Write-Host "Starting IMS Retail..." -ForegroundColor Green
    Start-Process $BatchFile
}

Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

