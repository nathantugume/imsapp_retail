<?php 
session_start();

if(!isset($_SESSION['LOGGEDIN'])){
    header("location:login.php?unauth=unauthorized access");
    exit;
}

// Only Master users can access
if($_SESSION['LOGGEDIN']['role'] != "Master") {
    header("location:index.php");
    exit;
}

require_once 'system/UpdateChecker.php';
$checker = new UpdateChecker();
$current = $checker->getCurrentVersion();

$page_title = "System Updates";
?>
<?php include('common/top.php'); ?>
<link rel="stylesheet" href="css/custom.css">

<style>
.update-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
}

.update-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 8px 8px 0 0;
    margin: -20px -20px 20px -20px;
}

.version-badge {
    display: inline-block;
    padding: 5px 15px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    font-size: 14px;
    margin-top: 10px;
}

.update-section {
    margin-bottom: 30px;
}

.update-section h4 {
    color: #667eea;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f5f5f5;
}

.info-label {
    font-weight: 600;
    color: #666;
}

.info-value {
    color: #333;
}

.btn-check-update {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    padding: 12px 30px;
    font-size: 16px;
    border-radius: 5px;
    transition: all 0.3s;
}

.btn-check-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-install-update {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border: none;
    padding: 12px 30px;
    font-size: 16px;
    border-radius: 5px;
    transition: all 0.3s;
}

.btn-install-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(56, 239, 125, 0.4);
}

.progress-container {
    display: none;
    margin-top: 20px;
}

.alert-update {
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}
</style>

<body>
<?php include('common/navbar.php'); ?>

<div class="container">
    <div class="update-card">
        <div class="update-header">
            <h2><i class="fa fa-cloud-download"></i> System Updates</h2>
            <p>Keep your IMS Retail App up-to-date with the latest features and bug fixes from GitHub</p>
            <span class="version-badge">Current: v<?php echo $current['version']; ?> (Commit: <?php echo $current['commit']; ?>)</span>
        </div>

        <!-- Current Version Section -->
        <div class="update-section">
            <h4><i class="fa fa-info-circle"></i> Current Version Information</h4>
            <div class="info-row">
                <span class="info-label">Version:</span>
                <span class="info-value"><?php echo $current['version']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Commit:</span>
                <span class="info-value"><?php echo $current['commit']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value"><?php echo $current['date']; ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Branch:</span>
                <span class="info-value"><?php echo $current['branch']; ?></span>
            </div>
        </div>

        <!-- Check for Updates Button -->
        <div class="update-section text-center">
            <button id="check-update-btn" class="btn btn-primary btn-lg btn-check-update">
                <i class="fa fa-refresh"></i> Check for Updates
            </button>
        </div>

        <!-- Update Results Section -->
        <div id="update-results" style="display:none;">
            <div class="update-section">
                <h4><i class="fa fa-github"></i> Latest Version from GitHub</h4>
                <div id="latest-version-info">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>

            <!-- Install Button -->
            <div id="install-section" class="update-section text-center" style="display:none;">
                <div class="alert alert-warning alert-update">
                    <strong><i class="fa fa-exclamation-triangle"></i> Important:</strong>
                    <ul class="text-left" style="margin-top: 10px; margin-bottom: 0;">
                        <li>A complete backup will be created automatically</li>
                        <li>Your database and config files will be preserved</li>
                        <li>The update process takes about 30-60 seconds</li>
                        <li>The page will reload automatically after update</li>
                    </ul>
                </div>
                
                <button id="install-update-btn" class="btn btn-success btn-lg btn-install-update">
                    <i class="fa fa-download"></i> Install Update Now
                </button>
            </div>

            <!-- Progress Section -->
            <div class="progress-container">
                <h4>Update Progress</h4>
                <div class="progress" style="height: 30px;">
                    <div id="update-progress-bar" class="progress-bar progress-bar-striped active" role="progressbar" style="width: 0%">
                        <span id="progress-text">Starting...</span>
                    </div>
                </div>
                <p id="progress-message" class="text-center" style="margin-top: 10px;"></p>
            </div>
        </div>

        <!-- Up to Date Section -->
        <div id="up-to-date-section" style="display:none;">
            <div class="alert alert-success alert-update" style="text-align: center;">
                <div style="font-size: 48px; color: #28a745; margin-bottom: 15px;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <h3 style="color: #28a745; margin-bottom: 15px;">System Up to Date!</h3>
                <p style="font-size: 16px;"><strong>No updates needed.</strong></p>
                <p style="margin-top: 15px; font-size: 14px; color: #666;">
                    Your installed version matches the latest version on GitHub.<br>
                    The "Install Update Now" button is hidden because there's nothing to update.
                </p>
                <hr style="margin: 20px 0;">
                <p style="font-size: 12px; color: #999;">
                    💡 New updates are usually released when new features are added or bugs are fixed.<br>
                    Check back periodically or watch for the <span class="badge" style="background-color: #f44336;">NEW</span> badge in the navigation bar.
                </p>
            </div>
        </div>

        <!-- Error Section -->
        <div id="error-section" style="display:none;">
            <div class="alert alert-danger alert-update">
                <h4><i class="fa fa-exclamation-circle"></i> Error</h4>
                <p id="error-message"></p>
            </div>
        </div>
    </div>

    <!-- How to Use Section -->
    <div class="update-card">
        <h4><i class="fa fa-question-circle"></i> How to Use</h4>
        <ol>
            <li><strong>Check for Updates:</strong> Click the "Check for Updates" button above</li>
            <li><strong>Review Changes:</strong> If an update is available, review the version details and changes</li>
            <li><strong>Install:</strong> Click "Install Update Now" to download and apply the update</li>
            <li><strong>Wait:</strong> The system will create a backup, download, and apply the update (30-60 seconds)</li>
            <li><strong>Done:</strong> The page will reload automatically with the new version</li>
        </ol>
        
        <div class="alert alert-info" style="margin-top: 20px;">
            <strong><i class="fa fa-lightbulb-o"></i> About Updates:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <li>Updates are fetched from GitHub: <a href="https://github.com/nathantugume/imsapp_retail" target="_blank">nathantugume/imsapp_retail</a></li>
                <li>Download size: ~2-3 MB (complete application)</li>
                <li>Update time: 30-60 seconds</li>
                <li>Automatic backup created before each update</li>
                <li>Your config and database are always preserved</li>
            </ul>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var latestData = null;
    
    // Check for updates button
    $('#check-update-btn').click(function() {
        checkForUpdates();
    });
    
    // Install update button
    $('#install-update-btn').click(function() {
        installUpdate();
    });
    
    function checkForUpdates() {
        // Hide all sections
        $('#update-results').hide();
        $('#up-to-date-section').hide();
        $('#error-section').hide();
        $('#install-section').hide();
        
        // Show loading state
        $('#check-update-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Checking...');
        
        $.ajax({
            url: 'system/check-update.php',
            method: 'POST',
            data: { action: 'check' },
            dataType: 'json',
            success: function(response) {
                console.log('Update check response:', response);
                
                if (response.status === 'error') {
                    showError(response.message);
                } else if (response.update_available === true) {
                    showUpdateAvailable(response.current, response.latest);
                    latestData = response;
                } else {
                    showUpToDate(response.current);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Response:', xhr.responseText);
                showError('Failed to connect to update server. Please check your internet connection and try again.');
            },
            complete: function() {
                $('#check-update-btn').prop('disabled', false).html('<i class="fa fa-refresh"></i> Check for Updates');
            }
        });
    }
    
    function showUpdateAvailable(current, latest) {
        // Show SweetAlert notification
        Swal.fire({
            title: 'Update Available!',
            html: '<div style="text-align: left;">' +
                  '<p><strong>A new version is available for installation.</strong></p>' +
                  '<hr>' +
                  '<p><i class="fa fa-arrow-up"></i> <strong>New Version:</strong> ' + latest.version + ' <span class="badge badge-success">NEW</span></p>' +
                  '<p><i class="fa fa-code-branch"></i> <strong>Current:</strong> ' + current.version + '</p>' +
                  '<p><i class="fa fa-calendar"></i> <strong>Released:</strong> ' + latest.date + '</p>' +
                  '<p><i class="fa fa-user"></i> <strong>Author:</strong> ' + latest.author + '</p>' +
                  '<hr>' +
                  '<p><strong>What\'s New:</strong></p>' +
                  '<p style="font-style: italic; color: #666;">' + latest.message + '</p>' +
                  '</div>',
            icon: 'info',
            confirmButtonText: 'View Details',
            confirmButtonColor: '#667eea',
            showCancelButton: true,
            cancelButtonText: 'Later'
        });
        
        var html = '<div class="info-row">' +
            '<span class="info-label">Version:</span>' +
            '<span class="info-value"><strong>' + latest.version + '</strong> <span class="label label-success">NEW</span></span>' +
            '</div>' +
            '<div class="info-row">' +
            '<span class="info-label">Commit:</span>' +
            '<span class="info-value">' + latest.commit + '</span>' +
            '</div>' +
            '<div class="info-row">' +
            '<span class="info-label">Date:</span>' +
            '<span class="info-value">' + latest.date + '</span>' +
            '</div>' +
            '<div class="info-row">' +
            '<span class="info-label">Author:</span>' +
            '<span class="info-value">' + latest.author + '</span>' +
            '</div>' +
            '<div style="margin-top: 15px; padding: 15px; background: #f9f9f9; border-radius: 5px;">' +
            '<strong>What\'s New:</strong><br>' +
            '<p style="margin: 10px 0 0 0;">' + latest.message + '</p>' +
            '</div>';
        
        $('#latest-version-info').html(html);
        $('#update-results').show();
        $('#install-section').show();
    }
    
    function showUpToDate(current) {
        // Show SweetAlert notification
        Swal.fire({
            title: 'System Up to Date!',
            html: '<div style="text-align: left;">' +
                  '<p><strong>Your system is already running the latest version.</strong></p>' +
                  '<hr>' +
                  '<p><i class="fa fa-info-circle"></i> <strong>Current Version:</strong> ' + current.version + '</p>' +
                  '<p><i class="fa fa-code-branch"></i> <strong>Commit:</strong> ' + current.commit + '</p>' +
                  '<p><i class="fa fa-calendar"></i> <strong>Last Updated:</strong> ' + current.date + '</p>' +
                  '<p><i class="fa fa-git"></i> <strong>Branch:</strong> ' + current.branch + '</p>' +
                  '</div>',
            icon: 'success',
            confirmButtonText: 'Great!',
            confirmButtonColor: '#28a745'
        });
        
        var html = '<div class="alert alert-success" style="margin-bottom: 20px; padding: 15px; border-radius: 8px;">' +
            '<h5 style="margin: 0 0 10px 0;"><i class="fa fa-check-circle"></i> ✅ Already on Latest Version!</h5>' +
            '<p style="margin: 0;">Your system matches the latest commit on GitHub. No update needed.</p>' +
            '</div>' +
            '<div class="info-row">' +
            '<span class="info-label">Version:</span>' +
            '<span class="info-value"><strong>' + current.version + '</strong> <span class="label label-success">LATEST</span></span>' +
            '</div>' +
            '<div class="info-row">' +
            '<span class="info-label">Commit:</span>' +
            '<span class="info-value">' + current.commit + ' <i class="fa fa-check-circle text-success"></i></span>' +
            '</div>' +
            '<div class="info-row">' +
            '<span class="info-label">Last Updated:</span>' +
            '<span class="info-value">' + current.date + '</span>' +
            '</div>' +
            '<div class="info-row">' +
            '<span class="info-label">Branch:</span>' +
            '<span class="info-value">' + current.branch + '</span>' +
            '</div>';
        
        $('#latest-version-info').html(html);
        $('#update-results').show();
        $('#up-to-date-section').show();
        $('#install-section').hide();
    }
    
    function showError(message) {
        // Show SweetAlert notification
        Swal.fire({
            title: 'Update Check Failed',
            html: message,
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        });
        
        $('#error-message').html(message);
        $('#error-section').show();
    }
    
    function installUpdate() {
        if (!confirm('Install this update?\n\nThe system will:\n• Create a backup of current files\n• Download the update from GitHub\n• Apply the update\n• Reload the page\n\nThis will take about 30-60 seconds.')) {
            return;
        }
        
        // Hide install button and show progress
        $('#install-section').hide();
        $('.progress-container').show();
        
        // Update initial progress
        updateProgress(10, 'Creating backup...');
        
        $.ajax({
            url: 'system/check-update.php',
            method: 'POST',
            data: { action: 'full_update' },
            dataType: 'json',
            timeout: 300000, // 5 minutes
            success: function(response) {
                if (response.success) {
                    updateProgress(100, 'Update completed successfully!');
                    
                    Swal.fire({
                        title: 'Update Successful!',
                        text: 'The system has been updated to version ' + response.version + '. The page will reload in 3 seconds.',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    var errorMsg = 'Update failed: ' + response.message;
                    if (response.step) {
                        errorMsg = 'Update failed at ' + response.step + ' step: ' + response.message;
                    }
                    if (response.backup_file) {
                        errorMsg += '<br><br><strong>Backup Location:</strong> ' + response.backup_file;
                    }
                    if (response.log && response.log.length > 0) {
                        errorMsg += '<br><br><strong>Debug Log:</strong><br><pre style="background:#f5f5f5;padding:10px;max-height:200px;overflow-y:auto;">' + 
                            response.log.join('\n') + '</pre>';
                    }
                    showError(errorMsg);
                    $('.progress-container').hide();
                    $('#install-section').show();
                }
            },
            error: function(xhr, status, error) {
                showError('Update failed: ' + error + '<br>Please try again or update manually from GitHub.');
                $('.progress-container').hide();
                $('#install-section').show();
            }
        });
        
        // Simulate progress for better UX
        setTimeout(function() { updateProgress(33, 'Downloading from GitHub...'); }, 2000);
        setTimeout(function() { updateProgress(66, 'Applying update...'); }, 5000);
        setTimeout(function() { updateProgress(90, 'Finalizing...'); }, 8000);
    }
    
    function updateProgress(percent, message) {
        $('#update-progress-bar').css('width', percent + '%');
        $('#progress-text').text(Math.round(percent) + '%');
        $('#progress-message').text(message);
        
        if (percent === 100) {
            $('#update-progress-bar').removeClass('active').addClass('progress-bar-success');
        }
    }
});
</script>

<?php include("common/footer.php"); ?>

