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

</body>
</html>