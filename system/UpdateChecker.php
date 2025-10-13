<?php
/**
 * UpdateChecker - Check for updates from GitHub
 */
class UpdateChecker {
    private $github_user = 'nathantugume';
    private $github_repo = 'imsapp_retail';
    private $current_version_file = 'version.json';
    private $github_api_url;
    
    public function __construct() {
        $this->github_api_url = "https://api.github.com/repos/{$this->github_user}/{$this->github_repo}";
    }
    
    /**
     * Get current installed version
     */
    public function getCurrentVersion() {
        // Clear file status cache to ensure fresh read
        clearstatcache(true, $this->current_version_file);
        
        if (file_exists($this->current_version_file)) {
            $content = file_get_contents($this->current_version_file);
            $version_data = json_decode($content, true);
            
            // Validate JSON was parsed correctly
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('version.json parse error: ' . json_last_error_msg());
                return [
                    'version' => '1.0.0',
                    'commit' => 'unknown',
                    'date' => date('Y-m-d H:i:s'),
                    'branch' => 'main',
                    'error' => 'JSON parse error: ' . json_last_error_msg()
                ];
            }
            
            // Ensure commit field exists
            if (!isset($version_data['commit'])) {
                $version_data['commit'] = 'unknown';
            }
            
            return $version_data;
        }
        
        // Default version if file doesn't exist
        return [
            'version' => '1.0.0',
            'commit' => 'unknown',
            'date' => date('Y-m-d H:i:s'),
            'branch' => 'main'
        ];
    }
    
    /**
     * Get latest version from GitHub
     */
    public function getLatestVersion() {
        try {
            // Get latest commit from main branch
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "{$this->github_api_url}/commits/main");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'IMS-App-Update-Checker');
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code !== 200) {
                return ['error' => 'Failed to fetch from GitHub. HTTP Code: ' . $http_code];
            }
            
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['error' => 'Invalid JSON response from GitHub'];
            }
            
            return [
                'version' => $this->extractVersion($data),
                'commit' => substr($data['sha'], 0, 7),
                'date' => date('Y-m-d H:i:s', strtotime($data['commit']['author']['date'])),
                'message' => $data['commit']['message'],
                'author' => $data['commit']['author']['name'],
                'full_sha' => $data['sha']
            ];
            
        } catch (Exception $e) {
            return ['error' => 'Exception: ' . $e->getMessage()];
        }
    }
    
    /**
     * Extract version from commit message or use date
     */
    private function extractVersion($data) {
        $message = $data['commit']['message'];
        
        // Try to find version in commit message (e.g., v1.0.0, version 1.0.0)
        if (preg_match('/v?(\d+\.\d+\.\d+)/', $message, $matches)) {
            return $matches[1];
        }
        
        // Use date as version
        return date('Y.m.d', strtotime($data['commit']['author']['date']));
    }
    
    /**
     * Check if update is available
     */
    public function checkForUpdates() {
        $current = $this->getCurrentVersion();
        $latest = $this->getLatestVersion();
        
        if (isset($latest['error'])) {
            return [
                'status' => 'error',
                'message' => $latest['error']
            ];
        }
        
        $update_available = ($current['commit'] !== $latest['commit']);
        
        return [
            'status' => 'success',
            'update_available' => $update_available,
            'current' => $current,
            'latest' => $latest,
            'update_url' => "https://github.com/{$this->github_user}/{$this->github_repo}/archive/refs/heads/main.zip"
        ];
    }
    
    /**
     * Download update from GitHub
     */
    public function downloadUpdate() {
        try {
            $zip_url = "https://github.com/{$this->github_user}/{$this->github_repo}/archive/refs/heads/main.zip";
            $zip_file = sys_get_temp_dir() . '/imsapp_update.zip';
            
            // Download zip file
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $zip_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'IMS-App-Update-Checker');
            curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minutes
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $data = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code !== 200) {
                return ['success' => false, 'message' => 'Failed to download update. HTTP Code: ' . $http_code];
            }
            
            // Save zip file
            file_put_contents($zip_file, $data);
            
            return [
                'success' => true,
                'message' => 'Update downloaded successfully',
                'file' => $zip_file,
                'size' => filesize($zip_file)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }
    
    /**
     * Create backup before update
     */
    public function createBackup() {
        try {
            $backup_dir = __DIR__ . '/../backups';
            if (!is_dir($backup_dir)) {
                mkdir($backup_dir, 0755, true);
            }
            
            $backup_file = $backup_dir . '/backup_' . date('Y-m-d_His') . '.zip';
            
            $zip = new ZipArchive();
            $open_result = $zip->open($backup_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            if ($open_result !== true) {
                return ['success' => false, 'message' => 'Failed to create backup zip. Error code: ' . $open_result];
            }
            
            // Add files to backup (exclude backups, Invoices, and temp files)
            $root_path = realpath(__DIR__ . '/..');
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($root_path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            $file_count = 0;
            $skipped = 0;
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $file_path = $file->getRealPath();
                    
                    // Skip files that can't be read
                    if (!is_readable($file_path)) {
                        $skipped++;
                        continue;
                    }
                    
                    $relative_path = substr($file_path, strlen($root_path) + 1);
                    
                    // Skip certain directories and large files
                    if (strpos($relative_path, 'backups/') === 0 || 
                        strpos($relative_path, 'Invoices/') === 0 ||
                        strpos($relative_path, 'desktop/') === 0 ||
                        strpos($relative_path, 'runtime/') === 0 ||
                        strpos($relative_path, 'node_modules/') === 0 ||
                        strpos($relative_path, '.git/') === 0) {
                        continue;
                    }
                    
                    // Skip very large files (> 10MB) to prevent memory issues
                    if (filesize($file_path) > 10 * 1024 * 1024) {
                        $skipped++;
                        continue;
                    }
                    
                    // Try to add file, skip on error
                    try {
                        if (@$zip->addFile($file_path, $relative_path)) {
                            $file_count++;
                        } else {
                            $skipped++;
                        }
                    } catch (Exception $e) {
                        $skipped++;
                    }
                }
            }
            
            $close_result = @$zip->close();
            
            if (!$close_result) {
                return ['success' => false, 'message' => 'Failed to close backup zip'];
            }
            
            // Verify the backup was created
            if (!file_exists($backup_file)) {
                return ['success' => false, 'message' => 'Backup file was not created'];
            }
            
            $file_size = filesize($backup_file);
            if ($file_size === 0) {
                return ['success' => false, 'message' => 'Backup file is empty (0 bytes). Added ' . $file_count . ' files.'];
            }
            
            return [
                'success' => true,
                'message' => 'Backup created successfully',
                'file' => $backup_file,
                'size' => $file_size,
                'file_count' => $file_count
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Backup failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Apply update (extract and replace files)
     */
    public function applyUpdate($zip_file) {
        $log = [];
        try {
            $log[] = "Starting update process...";
            
            $zip = new ZipArchive();
            if ($zip->open($zip_file) !== true) {
                return ['success' => false, 'message' => 'Failed to open update zip', 'log' => $log];
            }
            $log[] = "✓ Opened zip file";
            
            $extract_dir = sys_get_temp_dir() . '/imsapp_update_extract';
            $log[] = "Extract dir: $extract_dir";
            
            // Remove old extract directory if exists
            if (is_dir($extract_dir)) {
                $this->deleteDirectory($extract_dir);
                $log[] = "✓ Cleaned old extract directory";
            }
            
            // Extract
            $result = $zip->extractTo($extract_dir);
            $zip->close();
            
            if (!$result) {
                return ['success' => false, 'message' => 'Failed to extract zip', 'log' => $log];
            }
            $log[] = "✓ Extracted zip file";
            
            // Find the extracted folder (GitHub adds repo name prefix)
            $extracted_folder = $extract_dir . '/' . $this->github_repo . '-main';
            $log[] = "Looking for: $extracted_folder";
            
            // List what's actually in extract_dir
            if (is_dir($extract_dir)) {
                $contents = scandir($extract_dir);
                $log[] = "Contents of extract_dir: " . implode(', ', array_diff($contents, ['.', '..']));
            }
            
            if (!is_dir($extracted_folder)) {
                // Try to find the actual folder name
                $dirs = glob($extract_dir . '/*', GLOB_ONLYDIR);
                if (!empty($dirs)) {
                    $extracted_folder = $dirs[0];
                    $log[] = "Using alternate folder: $extracted_folder";
                } else {
                    return ['success' => false, 'message' => 'Extracted folder not found. Expected: ' . $extracted_folder, 'log' => $log];
                }
            }
            $log[] = "✓ Found extracted folder";
            
            // Get latest version BEFORE copying
            $latest = $this->getLatestVersion();
            $log[] = "Target version: {$latest['version']} (commit: {$latest['commit']})";
            
            // Copy files (preserve config and database)
            $root_path = realpath(__DIR__ . '/..');
            $log[] = "Root path: $root_path";
            $log[] = "Copying files...";
            
            $filescopied = 0;
            $errors = [];
            try {
                // Exclude version.json from copy - we'll create it fresh
                $filescopied = $this->copyDirectory(
                    $extracted_folder, 
                    $root_path, 
                    ['config/config.php', 'database/imsapp.sql', 'version.json'], 
                    $errors
                );
                $log[] = "✓ Copied $filescopied files";
                if (!empty($errors)) {
                    $log[] = "Copy errors: " . implode(', ', array_slice($errors, 0, 5));
                }
            } catch (Exception $e) {
                $log[] = "Copy error: " . $e->getMessage();
            }
            
            // Now create NEW version.json with latest commit
            $version_path = $root_path . '/' . $this->current_version_file;
            $log[] = "Creating version file: $version_path";
            
            $version_data = [
                'version' => $latest['version'],
                'commit' => $latest['commit'],
                'date' => date('Y-m-d H:i:s'),
                'branch' => 'main'
            ];
            
            // Delete old version file if exists
            if (file_exists($version_path)) {
                unlink($version_path);
                $log[] = "Deleted old version.json";
            }
            
            $write_result = file_put_contents($version_path, json_encode($version_data, JSON_PRETTY_PRINT));
            if ($write_result === false) {
                $log[] = "❌ Failed to write version file";
            } else {
                $log[] = "✓ Version file created ($write_result bytes)";
                $log[] = "✓ New commit: {$latest['commit']}";
                
                // Verify it was written correctly
                if (file_exists($version_path)) {
                    $verify = json_decode(file_get_contents($version_path), true);
                    $log[] = "Verification - Commit in file: {$verify['commit']}";
                } else {
                    $log[] = "❌ Version file does not exist after write!";
                }
            }
            
            // Cleanup
            $this->deleteDirectory($extract_dir);
            $log[] = "✓ Cleaned up temp files";
            
            if (file_exists($zip_file)) {
                unlink($zip_file);
                $log[] = "✓ Removed zip file";
            }
            
            return [
                'success' => true,
                'message' => 'Update applied successfully! Please refresh the page.',
                'version' => $latest['version'],
                'log' => $log
            ];
            
        } catch (Exception $e) {
            $log[] = "❌ Exception: " . $e->getMessage();
            $log[] = "Stack trace: " . $e->getTraceAsString();
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage(), 'log' => $log];
        }
    }
    
    /**
     * Helper: Copy directory recursively
     */
    private function copyDirectory($src, $dst, $exclude = [], &$errors = []) {
        $count = 0;
        $dir = @opendir($src);
        
        if (!$dir) {
            $errors[] = "Failed to open source directory: $src";
            return 0;
        }
        
        if (!is_dir($dst)) {
            @mkdir($dst, 0755, true);
        }
        
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $src_path = $src . '/' . $file;
                $dst_path = $dst . '/' . $file;
                
                // Check if file should be excluded
                $relative_path = str_replace(realpath(__DIR__ . '/..') . '/', '', $dst_path);
                if (in_array($relative_path, $exclude) || in_array($file, $exclude)) {
                    continue;
                }
                
                if (is_dir($src_path)) {
                    // Skip certain directories
                    if (in_array($file, ['backups', 'Invoices', '.git', 'desktop', 'runtime', 'node_modules'])) {
                        continue;
                    }
                    $count += $this->copyDirectory($src_path, $dst_path, $exclude, $errors);
                } else {
                    if (@copy($src_path, $dst_path)) {
                        $count++;
                    } else {
                        $errors[] = "Failed to copy: $src_path → $dst_path";
                    }
                }
            }
        }
        
        closedir($dir);
        return $count;
    }
    
    /**
     * Helper: Delete directory recursively
     */
    private function deleteDirectory($dir) {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        
        rmdir($dir);
    }
}

