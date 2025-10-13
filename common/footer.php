</div>
<br>
<div style="background:#273c75; width: 100%; height: 30px; line-height: 30px; text-align: center; border-radius: 2px;color: white;">
	<p>All Rights Reserved @ 2025 Mini Price Hardware </p>
</div>

<?php if(isset($_SESSION['LOGGEDIN']) && $_SESSION['LOGGEDIN']['role'] == "Master"): ?>
<script>
// Auto-check for updates on first load (Master users only)
$(document).ready(function() {
    // Check if update check has already been done this session
    if (!sessionStorage.getItem('updateChecked')) {
        checkForUpdatesBackground();
        sessionStorage.setItem('updateChecked', 'true');
    }
});

function checkForUpdatesBackground() {
    $.ajax({
        url: 'system/check-update.php',
        method: 'POST',
        data: { action: 'check' },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.update_available === true) {
                // Show the NEW badge
                $('#update-badge').show();
                
                // Optional: Show a toast notification
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Update Available!',
                        text: 'A new version is available. Click the Updates button to install.',
                        icon: 'info',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000
                    });
                }
            }
        },
        error: function(xhr, status, error) {
            // Silent fail - don't annoy user with errors on background check
            console.log('Background update check failed:', error);
        }
    });
}
</script>
<?php endif; ?>

<script>
// Desktop Shortcut Functions (Available on all pages)
function detectOS() {
    var userAgent = window.navigator.userAgent.toLowerCase();
    var platform = window.navigator.platform.toLowerCase();
    
    if (userAgent.indexOf('win') !== -1 || platform.indexOf('win') !== -1) {
        return 'windows';
    } else if (userAgent.indexOf('mac') !== -1 || platform.indexOf('mac') !== -1) {
        return 'mac';
    } else if (userAgent.indexOf('linux') !== -1 || platform.indexOf('linux') !== -1) {
        return 'linux';
    } else {
        return 'unknown';
    }
}

function createDesktopShortcut() {
    var os = detectOS();
    
    // Only offer for Windows (most common for clients)
    if (os !== 'windows') {
        Swal.fire({
            title: 'Platform Not Supported',
            text: 'Desktop shortcut creation is currently only available for Windows users.',
            icon: 'info',
            confirmButtonColor: '#667eea'
        });
        return;
    }
    
    // Show instructions and download
    Swal.fire({
        title: '🖥️ Create Desktop Shortcut',
        html: '<div style="text-align: left;">' +
              '<p><strong>Easy 3-Step Setup:</strong></p>' +
              '<ol style="font-size: 14px; line-height: 1.8;">' +
              '<li>Click "Download" below</li>' +
              '<li>Double-click the downloaded file</li>' +
              '<li>Find "IMS Retail" icon on your desktop</li>' +
              '</ol>' +
              '<hr>' +
              '<p style="font-size: 13px; color: #666;">' +
              '<i class="fa fa-info-circle"></i> The shortcut will launch IMS Retail with one click.' +
              '</p>' +
              '</div>',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: '<i class="fa fa-download"></i> Download',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#667eea',
        width: '500px'
    }).then((result) => {
        if (result.isConfirmed) {
            // Trigger download
            window.location.href = 'desktop/auto-create-shortcut.php?action=create_windows&os=windows';
            
            // Show next steps
            setTimeout(function() {
                Swal.fire({
                    title: 'Download Started!',
                    html: '<p><strong>Next:</strong> Open Downloads folder and double-click the file.</p>',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            }, 500);
        }
    });
}
</script>

</body>
</html>