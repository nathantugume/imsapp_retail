<?php
/**
 * Web Interface for IMS App Database Migration Tools
 * 
 * This provides a simple web interface to access the migration functionality
 * through a browser instead of command line.
 */

require_once(__DIR__ . "/DatabaseExporter.php");
require_once(__DIR__ . "/DatabaseImporter.php");
require_once(__DIR__ . "/BackupManager.php");

// Handle AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'export':
                $exporter = new DatabaseExporter();
                $result = $exporter->exportFullDatabase([
                    'compatibility_mode' => $_POST['compatibility'] ?? 'mysql5.7',
                    'compression' => $_POST['compression'] ?? 'gzip',
                    'include_migrations' => isset($_POST['include_migrations'])
                ]);
                echo json_encode($result);
                break;
                
            case 'backup':
                $backupManager = new BackupManager(null, 30, true); // Suppress output for web interface
                $result = $backupManager->createBackup([
                    'type' => $_POST['type'] ?? 'full',
                    'compression' => $_POST['compression'] ?? 'gzip',
                    'description' => $_POST['description'] ?? '',
                    'retention_days' => (int)($_POST['retention_days'] ?? 30)
                ]);
                echo json_encode($result);
                break;
                
            case 'history':
                $exporter = new DatabaseExporter();
                $backupManager = new BackupManager(null, 30, true); // Suppress output for web interface
                $result = [
                    'exports' => $exporter->getExportHistory(),
                    'backups' => $backupManager->getBackupHistory()
                ];
                echo json_encode($result);
                break;
                
            default:
                echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } catch (Error $e) {
        echo json_encode(['success' => false, 'error' => 'PHP Error: ' . $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMS App - Database Migration Tools</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        h2 {
            color: #555;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        select, input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
            border: 1px solid #bee5eb;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .history-table th,
        .history-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .history-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ IMS App Database Migration Tools</h1>
        
        <div id="messages"></div>
        
        <!-- Export Section -->
        <h2>📤 Export Database</h2>
        <form id="exportForm">
            <div class="form-group">
                <label for="exportCompatibility">Compatibility Mode:</label>
                <select id="exportCompatibility" name="compatibility">
                    <option value="mysql5.7">MySQL 5.7</option>
                    <option value="mysql8.0">MySQL 8.0</option>
                    <option value="mariadb10.3">MariaDB 10.3</option>
                    <option value="mariadb10.4">MariaDB 10.4</option>
                    <option value="mariadb10.5">MariaDB 10.5</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="exportCompression">Compression:</label>
                <select id="exportCompression" name="compression">
                    <option value="gzip">GZIP (Recommended)</option>
                    <option value="bzip2">BZIP2</option>
                    <option value="none">No Compression</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="include_migrations" checked> Include Migrations Table
                </label>
            </div>
            
            <button type="submit">Export Database</button>
        </form>
        
        <!-- Backup Section -->
        <h2>💾 Create Backup</h2>
        <form id="backupForm">
            <div class="form-group">
                <label for="backupType">Backup Type:</label>
                <select id="backupType" name="type">
                    <option value="full">Full Backup</option>
                    <option value="incremental">Incremental Backup</option>
                    <option value="structure_only">Structure Only</option>
                    <option value="data_only">Data Only</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="backupCompression">Compression:</label>
                <select id="backupCompression" name="compression">
                    <option value="gzip">GZIP (Recommended)</option>
                    <option value="bzip2">BZIP2</option>
                    <option value="none">No Compression</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="backupDescription">Description:</label>
                <input type="text" id="backupDescription" name="description" placeholder="Optional description">
            </div>
            
            <div class="form-group">
                <label for="retentionDays">Retention Days:</label>
                <input type="number" id="retentionDays" name="retention_days" value="30" min="1" max="365">
            </div>
            
            <button type="submit">Create Backup</button>
        </form>
        
        <!-- History Section -->
        <h2>📋 History</h2>
        <button onclick="loadHistory()">Refresh History</button>
        <div id="historyContent"></div>
        
        <!-- Loading Indicator -->
        <div id="loading" class="loading">
            <div class="spinner"></div>
            <p>Processing...</p>
        </div>
    </div>

    <script>
        function showMessage(message, type = 'info') {
            const messagesDiv = document.getElementById('messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = type;
            messageDiv.textContent = message;
            messagesDiv.appendChild(messageDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }
        
        function showLoading(show = true) {
            document.getElementById('loading').style.display = show ? 'block' : 'none';
        }
        
        function formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Export Form Handler
        document.getElementById('exportForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            showLoading(true);
            
            const formData = new FormData(this);
            formData.append('action', 'export');
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(`Export successful! File: ${result.filename} (${formatBytes(result.size)})`, 'success');
                } else {
                    showMessage(`Export failed: ${result.error}`, 'error');
                }
            } catch (error) {
                showMessage(`Error: ${error.message}`, 'error');
            } finally {
                showLoading(false);
            }
        });
        
        // Backup Form Handler
        document.getElementById('backupForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            showLoading(true);
            
            const formData = new FormData(this);
            formData.append('action', 'backup');
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(`Backup created! File: ${result.filename} (${formatBytes(result.size)})`, 'success');
                } else {
                    showMessage(`Backup failed: ${result.error}`, 'error');
                }
            } catch (error) {
                showMessage(`Error: ${error.message}`, 'error');
            } finally {
                showLoading(false);
            }
        });
        
        // Load History
        async function loadHistory() {
            showLoading(true);
            
            try {
                const formData = new FormData();
                formData.append('action', 'history');
                
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.exports || result.backups) {
                    let html = '<h3>Recent Exports</h3>';
                    if (result.exports && result.exports.length > 0) {
                        html += '<table class="history-table"><tr><th>Filename</th><th>Size</th><th>Created</th></tr>';
                        result.exports.slice(0, 5).forEach(export_item => {
                            html += `<tr><td>${export_item.filename}</td><td>${formatBytes(export_item.size)}</td><td>${export_item.created}</td></tr>`;
                        });
                        html += '</table>';
                    } else {
                        html += '<p>No exports found.</p>';
                    }
                    
                    html += '<h3>Recent Backups</h3>';
                    if (result.backups && result.backups.length > 0) {
                        html += '<table class="history-table"><tr><th>Filename</th><th>Type</th><th>Version</th><th>Size</th><th>Created</th></tr>';
                        result.backups.slice(0, 5).forEach(backup => {
                            html += `<tr><td>${backup.filename}</td><td>${backup.type}</td><td>v${backup.version}</td><td>${formatBytes(backup.size)}</td><td>${backup.created}</td></tr>`;
                        });
                        html += '</table>';
                    } else {
                        html += '<p>No backups found.</p>';
                    }
                    
                    document.getElementById('historyContent').innerHTML = html;
                } else {
                    showMessage('No history found.', 'info');
                }
            } catch (error) {
                showMessage(`Error loading history: ${error.message}`, 'error');
            } finally {
                showLoading(false);
            }
        }
        
        // Load history on page load
        window.addEventListener('load', loadHistory);
    </script>
</body>
</html>

