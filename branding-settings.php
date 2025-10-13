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

require_once 'config/branding.php';

// Debug: Log all POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST received: " . print_r($_POST, true));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_branding'])) {
    error_log("Save branding triggered");
    try {
        // Collect updates from form
        $updates = [
            'business_name' => trim($_POST['business_name'] ?? 'Mini Price Hardware'),
            'business_name_short' => trim($_POST['business_name_short'] ?? 'Mini Price'),
            'business_tagline' => trim($_POST['business_tagline'] ?? ''),
            'business_address' => trim($_POST['business_address'] ?? ''),
            'business_phone' => trim($_POST['business_phone'] ?? ''),
            'business_email' => trim($_POST['business_email'] ?? ''),
            'color_primary' => trim($_POST['color_primary'] ?? '#667eea'),
            'color_secondary' => trim($_POST['color_secondary'] ?? '#764ba2'),
            'currency_symbol' => trim($_POST['currency_symbol'] ?? 'ugx'),
            'low_stock_threshold' => (int)($_POST['low_stock_threshold'] ?? 30),
            'critical_stock_threshold' => (int)($_POST['critical_stock_threshold'] ?? 10),
            'expiry_warning_days' => (int)($_POST['expiry_warning_days'] ?? 90),
            'expiry_critical_days' => (int)($_POST['expiry_critical_days'] ?? 30),
        ];
        
        // Debug: Check file path and permissions
        $jsonPath = __DIR__ . '/config/branding.json';
        if (!file_exists($jsonPath)) {
            throw new Exception("branding.json file not found at: $jsonPath");
        }
        
        if (!is_writable($jsonPath)) {
            throw new Exception("branding.json is not writable. Current permissions: " . substr(sprintf('%o', fileperms($jsonPath)), -4));
        }
        
        // Save to JSON file using Branding class
        if (Branding::saveSettings($updates)) {
            $success_message = "✅ Branding settings saved successfully! Changes are now active across all pages.";
            
            // Force reload settings
            clearstatcache(true, $jsonPath);
        } else {
            throw new Exception("saveSettings() returned false. Check error logs.");
        }
        
    } catch (Exception $e) {
        $error_message = "❌ Save failed: " . $e->getMessage();
        error_log("Branding save error: " . $e->getMessage());
    }
}

$currentSettings = Branding::getAll();
?>
<?php include('common/top.php'); ?>
<link rel="stylesheet" href="css/custom.css">

<style>
.branding-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
}

.branding-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 8px 8px 0 0;
    margin: -20px -20px 20px -20px;
}

.branding-section {
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.branding-section h4 {
    color: #667eea;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #dee2e6;
}

.color-preview {
    width: 50px;
    height: 38px;
    border-radius: 4px;
    border: 1px solid #ced4da;
    display: inline-block;
    vertical-align: middle;
    margin-left: 10px;
}

.preview-box {
    padding: 15px;
    background: white;
    border-radius: 5px;
    margin-top: 10px;
    border: 1px solid #dee2e6;
}
</style>

<body>
<?php include('common/navbar.php'); ?>

<div class="container">
    <?php if (isset($success_message)): ?>
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fa fa-check-circle"></i> <?php echo $success_message; ?>
    </div>
    <script>
    // Show SweetAlert on success
    Swal.fire({
        title: 'Settings Saved!',
        html: '<p><?php echo addslashes($success_message); ?></p><p><small>Your branding changes are now active across all pages.</small></p>',
        icon: 'success',
        confirmButtonText: 'Great!',
        confirmButtonColor: '#28a745'
    });
    </script>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fa fa-exclamation-circle"></i> <?php echo $error_message; ?>
    </div>
    <script>
    // Show SweetAlert on error
    Swal.fire({
        title: 'Save Failed',
        html: '<p><?php echo addslashes($error_message); ?></p><p><small>Please check the console for more details.</small></p>',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc3545'
    });
    </script>
    <?php endif; ?>

    <div class="branding-card">
        <div class="branding-header">
            <h2><i class="fa fa-paint-brush"></i> Branding & Customization</h2>
            <p>Customize your application's look and feel without touching the database</p>
        </div>

        <form method="POST" action="">
            <!-- Business Information Section -->
            <div class="branding-section">
                <h4><i class="fa fa-building"></i> Business Information</h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Business Name (Full)</label>
                            <input type="text" name="business_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($currentSettings['business_name']); ?>" required>
                            <small class="text-muted">Appears on invoices and reports</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Business Name (Short)</label>
                            <input type="text" name="business_name_short" class="form-control" 
                                   value="<?php echo htmlspecialchars($currentSettings['business_name_short']); ?>" required>
                            <small class="text-muted">Appears in page titles and headers</small>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Business Tagline</label>
                    <input type="text" name="business_tagline" class="form-control" 
                           value="<?php echo htmlspecialchars($currentSettings['business_tagline']); ?>">
                    <small class="text-muted">Brief description of your business</small>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="business_phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($currentSettings['business_phone']); ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="business_email" class="form-control" 
                                   value="<?php echo htmlspecialchars($currentSettings['business_email']); ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Business Address</label>
                            <input type="text" name="business_address" class="form-control" 
                                   value="<?php echo htmlspecialchars($currentSettings['business_address']); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Color Scheme Section -->
            <div class="branding-section">
                <h4><i class="fa fa-palette"></i> Color Scheme</h4>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Primary Color</label>
                            <div style="display: flex; align-items: center;">
                                <input type="color" name="color_primary" class="form-control" style="width: 100px;" 
                                       value="<?php echo $currentSettings['color_primary']; ?>" id="color_primary">
                                <input type="text" class="form-control" style="width: 150px; margin-left: 10px;" 
                                       value="<?php echo $currentSettings['color_primary']; ?>" 
                                       onchange="document.getElementById('color_primary').value = this.value" readonly>
                                <span class="color-preview" id="preview_primary" 
                                      style="background-color: <?php echo $currentSettings['color_primary']; ?>;"></span>
                            </div>
                            <small class="text-muted">Used for buttons, links, and accents</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Secondary Color</label>
                            <div style="display: flex; align-items: center;">
                                <input type="color" name="color_secondary" class="form-control" style="width: 100px;" 
                                       value="<?php echo $currentSettings['color_secondary']; ?>" id="color_secondary">
                                <input type="text" class="form-control" style="width: 150px; margin-left: 10px;" 
                                       value="<?php echo $currentSettings['color_secondary']; ?>" 
                                       onchange="document.getElementById('color_secondary').value = this.value" readonly>
                                <span class="color-preview" id="preview_secondary" 
                                      style="background-color: <?php echo $currentSettings['color_secondary']; ?>;"></span>
                            </div>
                            <small class="text-muted">Used for gradients and secondary elements</small>
                        </div>
                    </div>
                </div>
                
                <div class="preview-box">
                    <strong><i class="fa fa-eye"></i> Preview:</strong>
                    <div style="margin-top: 10px;">
                        <button type="button" class="btn" id="btn-preview-primary" 
                                style="background: <?php echo $currentSettings['color_primary']; ?>; color: white;">
                            Primary Button
                        </button>
                        <button type="button" class="btn" id="btn-preview-gradient" 
                                style="background: linear-gradient(135deg, <?php echo $currentSettings['color_primary']; ?> 0%, <?php echo $currentSettings['color_secondary']; ?> 100%); color: white;">
                            Gradient Button
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Settings Section -->
            <div class="branding-section">
                <h4><i class="fa fa-cogs"></i> System Settings</h4>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Currency Symbol</label>
                            <input type="text" name="currency_symbol" class="form-control" 
                                   value="<?php echo htmlspecialchars($currentSettings['currency_symbol']); ?>" required>
                            <small class="text-muted">e.g., UGX, USD, KSH</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Low Stock Threshold</label>
                            <input type="number" name="low_stock_threshold" class="form-control" 
                                   value="<?php echo $currentSettings['low_stock_threshold']; ?>" required>
                            <small class="text-muted">Alert when stock falls below this</small>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Critical Stock Level</label>
                            <input type="number" name="critical_stock_threshold" class="form-control" 
                                   value="<?php echo $currentSettings['critical_stock_threshold']; ?>" required>
                            <small class="text-muted">Urgent alert level</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Expiry Warning (Days)</label>
                            <input type="number" name="expiry_warning_days" class="form-control" 
                                   value="<?php echo $currentSettings['expiry_warning_days']; ?>" required>
                            <small class="text-muted">Warn when product expires in X days</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Critical Expiry (Days)</label>
                            <input type="number" name="expiry_critical_days" class="form-control" 
                                   value="<?php echo $currentSettings['expiry_critical_days']; ?>" required>
                            <small class="text-muted">Critical alert when expiring in X days</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center" style="margin-top: 30px;">
                <button type="submit" name="save_branding" class="btn btn-primary btn-lg" 
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <i class="fa fa-save"></i> Save Branding Settings
                </button>
                <a href="index.php" class="btn btn-secondary btn-lg">
                    <i class="fa fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- How to Use Section -->
    <div class="branding-card">
        <h4><i class="fa fa-question-circle"></i> How to Use Branding Settings</h4>
        
        <div class="alert alert-info">
            <strong><i class="fa fa-lightbulb-o"></i> JSON-Based Branding</strong>
            <p style="margin: 10px 0;">All branding settings are stored in <code>config/branding.json</code> - not in the database!</p>
            <p style="margin: 10px 0;"><small>Changes are saved instantly and apply across all pages. Easy to backup and version control!</small></p>
        </div>
        
        <h5>Quick Customization Examples:</h5>
        
        <div class="row" style="margin-top: 15px;">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Business Name</strong></div>
                    <div class="panel-body">
                        <p>Change: <code>business_name</code></p>
                        <p><small>Appears on:</small></p>
                        <ul>
                            <li>Navbar header</li>
                            <li>Invoices (PDF)</li>
                            <li>Reports</li>
                            <li>Page titles</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Color Scheme</strong></div>
                    <div class="panel-body">
                        <p>Change: <code>color_primary</code> & <code>color_secondary</code></p>
                        <p><small>Affects:</small></p>
                        <ul>
                            <li>Buttons and links</li>
                            <li>Headers and panels</li>
                            <li>Navigation highlights</li>
                            <li>Charts and graphs</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Stock Alerts</strong></div>
                    <div class="panel-body">
                        <p>Change: <code>low_stock_threshold</code></p>
                        <p><small>Control when alerts trigger:</small></p>
                        <ul>
                            <li>Low Stock: <?php echo $currentSettings['low_stock_threshold']; ?> units</li>
                            <li>Critical: <?php echo $currentSettings['critical_stock_threshold']; ?> units</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading"><strong>Expiry Warnings</strong></div>
                    <div class="panel-body">
                        <p>Change: <code>expiry_warning_days</code></p>
                        <p><small>Alert schedule:</small></p>
                        <ul>
                            <li>Warning: <?php echo $currentSettings['expiry_warning_days']; ?> days before</li>
                            <li>Critical: <?php echo $currentSettings['expiry_critical_days']; ?> days before</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-warning" style="margin-top: 20px;">
            <strong><i class="fa fa-exclamation-triangle"></i> Important Notes:</strong>
            <ul style="margin: 10px 0 0 20px;">
                <li>Settings are saved to <code>config/branding.json</code> file (easy to read/edit)</li>
                <li>Changes apply immediately across all pages (no restart needed)</li>
                <li>File must have write permissions (chmod 644 or 666)</li>
                <li>Backup the file before major changes: <code>cp config/branding.json config/branding.backup.json</code></li>
                <li>JSON format makes it easy to version control and share</li>
                <li>Protected from GitHub updates (excluded from overwrites)</li>
            </ul>
        </div>
    </div>
</div>

<script>
// Live color preview
document.querySelectorAll('input[type="color"]').forEach(function(input) {
    input.addEventListener('change', function() {
        var previewId = 'preview_' + this.name.replace('color_', '');
        var preview = document.getElementById(previewId);
        if (preview) {
            preview.style.backgroundColor = this.value;
        }
        
        // Update button previews
        updatePreview();
    });
});

function updatePreview() {
    var primary = document.getElementById('color_primary').value;
    var secondary = document.getElementById('color_secondary').value;
    
    document.getElementById('btn-preview-primary').style.background = primary;
    document.getElementById('btn-preview-gradient').style.background = 
        'linear-gradient(135deg, ' + primary + ' 0%, ' + secondary + ' 100%)';
}

// Form submission with confirmation
$(document).ready(function() {
    var form = $('form[method="POST"]');
    
    if (form.length === 0) {
        console.error('Form not found!');
        return;
    }
    
    console.log('Form found, attaching submit handler');
    
    form.on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Form submit triggered');
        var formData = form.serialize() + '&save_branding=1';
        
        console.log('Form data:', formData);
        
        Swal.fire({
            title: 'Save Branding Settings?',
            text: 'This will update your application branding immediately.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Save Changes',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#667eea'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('User confirmed, saving via AJAX...');
                
                // Show loading indicator
                Swal.fire({
                    title: 'Saving...',
                    text: 'Updating branding settings',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit via AJAX for better control
                $.ajax({
                    url: 'branding-settings.php',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log('Save successful, reloading page...');
                        // Reload page to show success message
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Save failed:', error);
                        Swal.fire({
                            title: 'Save Failed',
                            text: 'An error occurred while saving. Check console for details.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            } else {
                console.log('User cancelled');
            }
        });
    });
});
</script>

<?php include("common/footer.php"); ?>

