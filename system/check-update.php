<?php
session_start();

// Prevent caching
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['LOGGEDIN'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

require_once 'UpdateChecker.php';

$action = $_POST['action'] ?? 'check';
$checker = new UpdateChecker();

switch ($action) {
    case 'check':
        // Check for updates
        $result = $checker->checkForUpdates();
        echo json_encode($result);
        break;
        
    case 'download':
        // Download update
        $result = $checker->downloadUpdate();
        echo json_encode($result);
        break;
        
    case 'backup':
        // Create backup
        $result = $checker->createBackup();
        echo json_encode($result);
        break;
        
    case 'apply':
        // Apply update
        $zip_file = $_POST['zip_file'] ?? '';
        if (empty($zip_file) || !file_exists($zip_file)) {
            echo json_encode(['success' => false, 'message' => 'Invalid update file']);
            break;
        }
        
        $result = $checker->applyUpdate($zip_file);
        echo json_encode($result);
        break;
        
    case 'full_update':
        // Complete update process (backup + download + apply)
        set_time_limit(600); // 10 minutes
        
        // Step 1: Create backup (non-critical - proceed even if it fails)
        $backup_result = $checker->createBackup();
        $backup_warning = '';
        if (!$backup_result['success']) {
            // Log warning but continue with update
            $backup_warning = 'Warning: Backup failed (' . $backup_result['message'] . '). Proceeding with update...';
            $backup_result['file'] = 'N/A - Backup failed';
        }
        
        // Step 2: Download update
        $download_result = $checker->downloadUpdate();
        if (!$download_result['success']) {
            echo json_encode([
                'success' => false,
                'step' => 'download',
                'message' => $download_result['message']
            ]);
            break;
        }
        
        // Step 3: Apply update
        $apply_result = $checker->applyUpdate($download_result['file']);
        if (!$apply_result['success']) {
            echo json_encode([
                'success' => false,
                'step' => 'apply',
                'message' => $apply_result['message'],
                'backup_file' => $backup_result['file'],
                'log' => $apply_result['log'] ?? []
            ]);
            break;
        }
        
        // Success
        $message = 'Update completed successfully!';
        if ($backup_warning) {
            $message .= ' ' . $backup_warning;
        }
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'backup_file' => $backup_result['file'],
            'backup_warning' => $backup_warning,
            'version' => $apply_result['version'],
            'log' => $apply_result['log'] ?? []
        ]);
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

