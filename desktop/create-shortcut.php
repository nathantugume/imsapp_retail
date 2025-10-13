<?php
session_start();

if(!isset($_SESSION['LOGGEDIN'])){
    header("location:../login.php?unauth=unauthorized access");
    exit;
}

// Get the absolute path to the application
$app_path = realpath(__DIR__ . '/..');
$batch_file = $app_path . '\\Start_IMS_Retail.bat';

// Detect if on Windows
$is_windows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

// Generate VBScript content for Windows
$vbs_content = 'Set oWS = WScript.CreateObject("WScript.Shell")
sLinkFile = oWS.SpecialFolders("Desktop") & "\IMS Retail.lnk"
Set oLink = oWS.CreateShortcut(sLinkFile)
oLink.TargetPath = "' . str_replace('/', '\\', $batch_file) . '"
oLink.WorkingDirectory = "' . str_replace('/', '\\', $app_path) . '"
oLink.Description = "IMS Retail - Mini Price Hardware Inventory System"
oLink.Save
WScript.Echo "Desktop shortcut created successfully!"
WScript.Echo ""
WScript.Echo "You can now close this window and find the IMS Retail icon on your desktop."';

// Set headers for download
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="Create_IMS_Retail_Shortcut.vbs"');
header('Content-Length: ' . strlen($vbs_content));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo $vbs_content;
exit;
?>

