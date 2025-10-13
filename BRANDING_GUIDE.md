# 🎨 Branding & Customization Guide

## Overview

IMS Retail has a **file-based branding system** that lets you customize the entire application without touching the database! All branding settings are stored in a simple PHP file.

---

## ✨ Two Ways to Customize

### Method 1: Web UI (Easiest!)

1. **Login as Master user**
2. **Click "Branding" in the navbar**
3. **Edit settings in the form**
4. **Click "Save"**
5. **Done!** Changes apply immediately

### Method 2: Edit File Directly

1. **Open `config/branding.php`**
2. **Find the `$settings` array**
3. **Change values**
4. **Save the file**
5. **Refresh browser** - changes appear!

---

## 📁 Configuration File: `config/branding.php`

### Structure

```php
class Branding {
    private static $settings = [
        'business_name' => 'Your Business Name',
        'color_primary' => '#667eea',
        // ... more settings
    ];
}
```

**Key Point:** ✅ Settings stored in FILE, not DATABASE!

---

## 🎯 What You Can Customize

### 1. Business Information

```php
'business_name' => 'Mini Price Hardware'           // Full name
'business_name_short' => 'Mini Price'               // Short name
'business_tagline' => 'Quality at Affordable Prices'
'business_address' => 'Kampala, Uganda'
'business_phone' => '+256 XXX XXXXXX'
'business_email' => 'info@yourcompany.com'
```

**Where it appears:**
- Navbar header
- Invoices/PDFs
- Page titles
- Footer
- Reports

---

### 2. Color Scheme 🎨

```php
'color_primary' => '#667eea'      // Main brand color
'color_secondary' => '#764ba2'    // Secondary/gradient
'color_success' => '#43e97b'      // Success messages
'color_danger' => '#f5576c'       // Errors/alerts
'color_warning' => '#ffa502'      // Warnings
'color_info' => '#4facfe'         // Info messages
```

**Where it appears:**
- Buttons and links
- Navbar active states
- Panel headers
- Gradients
- Alert colors
- Charts

---

### 3. Currency & Formatting

```php
'currency' => 'UGX'               // Currency code
'currency_symbol' => 'ugx'        // Display symbol
'date_format' => 'd-m-Y'          // Date format
'time_format' => 'H:i:s'          // Time format
'timezone' => 'Africa/Kampala'    // System timezone
```

**Affects:**
- All price displays
- Invoices
- Reports
- Date/time stamps

---

### 4. Stock & Expiry Alerts

```php
'low_stock_threshold' => 30       // Show warning
'critical_stock_threshold' => 10  // Show danger alert
'expiry_warning_days' => 90       // Warn 90 days before
'expiry_critical_days' => 30      // Critical at 30 days
```

**Controls:**
- Dashboard warnings
- Product table colors
- Alert notifications
- Report highlights

---

## 🚀 Real-World Examples

### Example 1: Change Business Name

**Current:**
```php
'business_name' => 'Mini Price Hardware'
```

**Change to:**
```php
'business_name' => 'ABC Pharmacy Ltd'
```

**Result:** 
- Navbar shows "ABC Pharmacy Ltd"
- Invoices print with new name
- Page titles updated
- Footer updated

---

### Example 2: Change Color Scheme

**Current (Purple):**
```php
'color_primary' => '#667eea'
'color_secondary' => '#764ba2'
```

**Change to Blue:**
```php
'color_primary' => '#0066cc'
'color_secondary' => '#0099ff'
```

**Result:**
- All buttons become blue
- Active navbar items blue
- Gradients use blue shades
- Modern blue theme throughout

---

### Example 3: Different Currency

**Current:**
```php
'currency_symbol' => 'ugx'
```

**Change to KSH (Kenya):**
```php
'currency' => 'KSH'
'currency_symbol' => 'KSH'
```

**Result:**
- All prices show "KSH" instead of "ugx"
- Invoices use new currency
- Reports updated

---

### Example 4: Adjust Stock Alerts

**Current:**
```php
'low_stock_threshold' => 30
'critical_stock_threshold' => 10
```

**For High-Volume Store:**
```php
'low_stock_threshold' => 100    // Alert earlier
'critical_stock_threshold' => 50  // More urgent threshold
```

**Result:**
- Earlier warnings
- Better inventory management
- Prevents stockouts

---

## 💡 Helper Methods

The Branding class provides useful methods:

```php
// Get business name
Branding::getBusinessName()           // Full name
Branding::getBusinessName(true)       // Short name

// Format currency
Branding::formatCurrency(1000)        // "ugx 1,000.00"

// Format dates
Branding::formatDate('2025-10-13')    // "13-10-2025"

// Get stock status
Branding::getStockStatus(25)          // "low"
Branding::getStockBadgeClass(25)      // "badge-warning"
Branding::getStockBadgeText(25)       // "Low Stock"

// Check expiry
Branding::getExpiryStatus('2025-12-31')
// Returns: ['status' => 'warning', 'days' => 79]
```

---

## 🔧 Advanced Customization

### Add Custom Settings

Edit `config/branding.php`:

```php
private static $settings = [
    // ... existing settings ...
    
    // Your custom settings
    'tax_rate' => 18,
    'company_slogan' => 'Excellence in Service',
    'support_hours' => '8 AM - 6 PM',
];
```

Then use anywhere:

```php
$taxRate = Branding::get('tax_rate');
$slogan = Branding::get('company_slogan');
```

---

## 📊 Where Branding is Used

### Throughout the App

**Navbar:**
- Business name in header
- Active tab colors

**Dashboard:**
- Panel colors
- Alert thresholds
- Currency formatting

**Products:**
- Stock level alerts
- Expiry warnings
- Price display

**Orders:**
- Currency formatting
- Date formatting

**Invoices:**
- Business name and address
- Color scheme
- Logo (if configured)

**Reports:**
- Business information
- Currency
- Date formats
- Chart colors

---

## ✅ Benefits of File-Based Branding

### Advantages

✅ **No Database Changes**
- Settings in file, not DB
- Easy to version control
- Git tracks changes
- Can rollback anytime

✅ **Easy Updates**
- Edit one file
- All pages update instantly
- No SQL needed
- No migration scripts

✅ **Portable**
- Copy config file
- Paste to new installation
- Instant branding transfer

✅ **Version Control Friendly**
- Track in Git
- See change history
- Merge branches easily
- Deploy with code

✅ **Update-Safe**
- Updates preserve your branding
- `config/branding.php` excluded from updates
- Your settings never overwritten

---

## 🔄 Workflow for Different Clients

### Scenario: Deploy to 3 Different Shops

**Shop A: Mini Price Hardware**
```php
'business_name' => 'Mini Price Hardware'
'color_primary' => '#667eea'  // Purple
'business_address' => 'Kampala'
```

**Shop B: Budget Tools Store**
```php
'business_name' => 'Budget Tools Store'
'color_primary' => '#0066cc'  // Blue
'business_address' => 'Entebbe'
```

**Shop C: Quality Pharmacy**
```php
'business_name' => 'Quality Pharmacy'
'color_primary' => '#28a745'  // Green
'business_address' => 'Jinja'
```

**Process:**
1. Deploy same codebase
2. Edit `branding.php` for each
3. Each shop has unique branding
4. One codebase, multiple brands! 🎨

---

## 🛡️ Update Protection

When you update via GitHub:

```
✅ Code files updated
✅ New features added
❌ config/branding.php PRESERVED
❌ config/config.php PRESERVED
❌ database/imsapp.sql PRESERVED
```

**Your branding stays intact!** 🎉

---

## 📝 Quick Reference

### Edit Branding (Web UI)
```
Navbar → Branding → Edit → Save
```

### Edit Branding (File)
```
config/branding.php → Edit → Save → Refresh
```

### Use in Code
```php
<?php require_once 'config/branding.php'; ?>
<?php echo Branding::getBusinessName(); ?>
```

### Use in JavaScript
```php
<script>
var primaryColor = '<?php echo Branding::getPrimaryColor(); ?>';
var currency = '<?php echo Branding::get("currency_symbol"); ?>';
</script>
```

---

## 🎯 Best Practices

1. ✅ **Backup First**: Copy `branding.php` before major changes
2. ✅ **Test Changes**: Edit, save, refresh, verify
3. ✅ **Use Web UI**: Easier than editing file
4. ✅ **Document Changes**: Keep notes of custom settings
5. ✅ **Version Control**: Commit branding changes to Git

---

## 🚀 Future Enhancements (Already Built!)

- ✅ Live color preview
- ✅ Form validation
- ✅ Save confirmation
- ✅ Easy web-based editing
- ✅ No database dependency
- ✅ Update-proof settings

---

**🎨 Customize your IMS Retail to match any brand - no database needed!**

