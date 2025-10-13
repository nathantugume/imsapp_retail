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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_branding'])) {
    $brandingFile = __DIR__ . '/config/branding.php';
    $content = file_get_contents($brandingFile);
    
    // Update values in the file
    $updates = [
        'business_name' => $_POST['business_name'] ?? 'Mini Price Hardware',
        'business_name_short' => $_POST['business_name_short'] ?? 'Mini Price',
        'business_tagline' => $_POST['business_tagline'] ?? '',
        'business_address' => $_POST['business_address'] ?? '',
        'business_phone' => $_POST['business_phone'] ?? '',
        'business_email' => $_POST['business_email'] ?? '',
        'color_primary' => $_POST['color_primary'] ?? '#667eea',
        'color_secondary' => $_POST['color_secondary'] ?? '#764ba2',
        'currency_symbol' => $_POST['currency_symbol'] ?? 'ugx',
        'low_stock_threshold' => $_POST['low_stock_threshold'] ?? 30,
        'critical_stock_threshold' => $_POST['critical_stock_threshold'] ?? 10,
        'expiry_warning_days' => $_POST['expiry_warning_days'] ?? 90,
        'expiry_critical_days' => $_POST['expiry_critical_days'] ?? 30,
    ];
    
    // Replace each value in the file
    foreach ($updates as $key => $value) {
        $pattern = "/('" . $key . "'\s*=>\s*)'[^']*'/";
        $replacement = "$1'" . addslashes($value) . "'";
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    // Save the file
    if (file_put_contents($brandingFile, $content)) {
        $success_message = "Branding settings updated successfully!";
    } else {
        $error_message = "Failed to save branding settings. Check file permissions.";
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
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fa fa-exclamation-circle"></i> <?php echo $error_message; ?>
    </div>
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
            <strong><i class="fa fa-lightbulb-o"></i> File-Based Branding</strong>
            <p style="margin: 10px 0;">All branding settings are stored in <code>config/branding.php</code> - not in the database!</p>
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
                <li>Settings are saved to <code>config/branding.php</code> file</li>
                <li>Changes apply immediately (no database restart needed)</li>
                <li>File must have write permissions</li>
                <li>Backup the file before major changes</li>
                <li>These settings update automatically on GitHub pushes</li>
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
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    
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
            this.submit();
        }
    });
});
</script>

<?php include("common/footer.php"); ?>

