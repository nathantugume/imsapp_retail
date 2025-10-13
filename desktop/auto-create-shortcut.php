<?php
session_start();

if(!isset($_SESSION['LOGGEDIN'])){
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_REQUEST['action'] ?? 'detect';
$os = $_REQUEST['os'] ?? 'unknown';

// If it's a direct download request, don't send JSON headers
if ($action === 'create_windows' || $action === 'create_linux') {
    // Headers will be set in the switch cases
} else {
    header('Content-Type: application/json');
}

// Get absolute path to application
$app_path = realpath(__DIR__ . '/..');

switch ($action) {
    case 'detect':
        // Return OS-specific instructions
        echo json_encode([
            'success' => true,
            'os_detected' => $os,
            'ready' => true
        ]);
        break;
        
    case 'create_windows':
        // Generate Windows VBScript
        $batch_file = $app_path . '\\Start_IMS_Retail.bat';
        $vbs_content = 'Set oWS = WScript.CreateObject("WScript.Shell")
sLinkFile = oWS.SpecialFolders("Desktop") & "\IMS Retail.lnk"
Set oLink = oWS.CreateShortcut(sLinkFile)
oLink.TargetPath = "' . str_replace('/', '\\', $batch_file) . '"
oLink.WorkingDirectory = "' . str_replace('/', '\\', $app_path) . '"
oLink.Description = "IMS Retail - Mini Price Hardware Inventory System"
oLink.WindowStyle = 1
oLink.Save

MsgBox "Desktop shortcut created successfully!" & vbCrLf & vbCrLf & "You can now find the IMS Retail icon on your desktop." & vbCrLf & vbCrLf & "Double-click it anytime to launch the application.", vbInformation, "IMS Retail - Shortcut Created"';
        
        // Return as downloadable file
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="Create_IMS_Retail_Shortcut.vbs"');
        header('Content-Length: ' . strlen($vbs_content));
        echo $vbs_content;
        exit;
        
    case 'create_linux':
        // Generate Linux bash installer
        $bash_content = '#!/bin/bash
# Auto-generated IMS Retail Desktop Shortcut Installer

DESKTOP_DIR="$HOME/Desktop"
APPS_DIR="$HOME/.local/share/applications"

# Create desktop file content
cat > "$DESKTOP_DIR/IMS_Retail.desktop" << EOF
[Desktop Entry]
Version=1.0
Type=Application
Name=IMS Retail - Mini Price Hardware
Comment=Inventory Management System - Retail Version
Exec=' . $app_path . '/Start_IMS_Retail.bat
Icon=' . $app_path . '/desktop/assets/icon.png
Terminal=true
Categories=Office;Finance;
Path=' . $app_path . '
EOF

# Make executable
chmod +x "$DESKTOP_DIR/IMS_Retail.desktop"

# Also add to applications menu if directory exists
if [ -d "$APPS_DIR" ]; then
    cp "$DESKTOP_DIR/IMS_Retail.desktop" "$APPS_DIR/"
    chmod +x "$APPS_DIR/IMS_Retail.desktop"
fi

echo "✅ Desktop shortcut created successfully!"
echo "You can find IMS Retail icon on your desktop."
read -p "Press Enter to continue..."
';
        
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="install_ims_retail.sh"');
        header('Content-Length: ' . strlen($bash_content));
        echo $bash_content;
        exit;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>

