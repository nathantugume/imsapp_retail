# 🔄 Auto-Update Feature - Retail Version

## Overview

The IMS Retail App now includes an automatic update system that checks GitHub for new versions and allows one-click updates directly from the UI!

**GitHub Repository:** [nathantugume/imsapp_retail](https://github.com/nathantugume/imsapp_retail)

**Update Method:** ZIP Download - Universal, reliable, works on all platforms (Linux, Windows, Mac)

## ✨ Features

### 1. **Automatic Update Checking**
- Checks GitHub once per session when you log in
- Silent background check - doesn't interrupt your work
- Shows a **NEW** badge on the Updates button when updates are available

### 2. **One-Click Updates**
- Click the **Updates** button in the navigation bar
- View current version vs. latest version
- Install with one click!

### 3. **Safe Updates**
- **Automatic Backup**: Creates a full backup before updating
- **Preserved Data**: Your config and database are never touched
- **Rollback**: Backup is kept in case you need to restore

### 4. **Update Process**
1. **Backup**: System creates complete backup
2. **Download**: Downloads latest code from GitHub
3. **Apply**: Extracts and replaces files (preserves config/database)
4. **Complete**: Auto-refreshes to new version

## 🎯 How to Use

### For Admin Users

1. **Check for Updates**:
   - Look for the **Updates** button in the top navigation bar
   - If a **NEW** badge appears, an update is available
   - Click **Updates** to view details

2. **View Update Details**:
   - See current version vs. latest version
   - View commit message (what changed)
   - See who made the changes and when

3. **Install Update**:
   - Click **Install Update Now** button
   - Confirm the installation
   - Wait for the process (typically 30-60 seconds)
   - System automatically reloads with new version

### For Regular Users

- The Updates button is only visible to **Master** users
- Contact your administrator to install updates

## 📋 Version Tracking

The system tracks versions using:
- **Version Number**: e.g., 2025.10.13
- **Commit Hash**: 7-character Git commit ID (e.g., 35d703c)
- **Date**: When the version was released
- **Branch**: Always tracks the 'main' branch

Version info is stored in `version.json`.

## 🛡️ Safety Features

### 1. Automatic Backups
- Complete system backup before every update
- Stored in `backups/` folder
- Named with timestamp: `backup_2025-10-13_143022.zip`

### 2. Preserved Files
These files are **NEVER** overwritten:
- ✅ `config/config.php` (your database credentials)
- ✅ `database/imsapp.sql` (your database export)
- ✅ `Invoices/` (all generated invoices)
- ✅ `backups/` (previous backups)

### 3. Error Handling
- If update fails, you get a clear error message
- Backup location is provided for manual restore
- Original files remain untouched on failure

## 🔧 Technical Details

### How It Works

1. **GitHub API Integration**:
   - Connects to GitHub REST API
   - Fetches latest commit from `main` branch
   - Compares with local version

2. **Update Process**:
   ```
   Check GitHub → Compare Versions → Create Backup → 
   Download Update → Extract Files → Copy Files → 
   Update Version → Clean Up → Reload
   ```

3. **File Structure**:
   ```
   system/
   ├── UpdateChecker.php    # Core update logic
   └── check-update.php     # API endpoint
   
   version.json              # Current version info
   system-updates.php        # Update UI page
   ```

### API Endpoints

**`system/check-update.php`** accepts these actions:

- `action=check`: Check if update is available
- `action=download`: Download update from GitHub
- `action=backup`: Create backup
- `action=apply`: Apply downloaded update
- `action=full_update`: Complete update process (all steps)

## 📊 Update Notifications

### Auto-Check on Login
- Runs once per session (first page load)
- Silent check in background
- Shows badge if update available
- Optional toast notification (if SweetAlert2 is loaded)

### Manual Check
- Click Updates button anytime
- Shows page with full details
- Install immediately or close and install later

## 🚨 Troubleshooting

### "Failed to connect to GitHub"
**Problem**: No internet connection or GitHub is down  
**Solution**: Check internet connection and try again

### "Failed to download update"
**Problem**: Network issue during download  
**Solution**: Try again or update manually from GitHub

### "Update failed at apply step"
**Problem**: File permissions or disk space  
**Solution**: 
1. Check file permissions (755 for directories, 644 for files)
2. Ensure enough disk space
3. Use backup to restore if needed

### Manual Restore from Backup
If an update fails:

```bash
cd /home/nathan/shop_mgt/imsapp
unzip backups/backup_TIMESTAMP.zip -d restore_temp
cp -r restore_temp/* .
rm -rf restore_temp
```

## 🔐 Security Considerations

### What's Checked
- ✅ User must be logged in
- ✅ User must have "Master" role
- ✅ HTTPS connection to GitHub
- ✅ Automatic backup before changes

### What's Protected
- ❌ Config files are never overwritten
- ❌ Database is never touched
- ❌ Invoices are preserved
- ❌ Previous backups are kept

## 📝 Version History

Current version information can be viewed:
1. Click **Updates** button
2. View under "Current Version"

Or check `version.json` file:
```json
{
    "version": "2025.10.13",
    "commit": "35d703c",
    "date": "2025.10.13",
    "branch": "main"
}
```

## 🎨 UI Elements

### Navigation Button
```
<i class="fa fa-cloud-download"></i> Updates [NEW]
```
- Icon: Cloud download
- Badge: Red "NEW" badge when update available
- Only visible to Master users

### Update Page
- **Header**: Purple gradient with version info
- **Body**: Shows version comparison and changelog
- **Buttons**: Check for Updates, Install Update Now
- **Progress**: Live progress bar during installation

## ⚙️ Configuration

### Change GitHub Repository

Edit `system/UpdateChecker.php`:

```php
private $github_user = 'your_username';
private $github_repo = 'your_repo_name';
```

### Disable Auto-Check

Remove from `common/footer.php`:

```javascript
// Comment out or remove:
if (!sessionStorage.getItem('updateChecked')) {
    checkForUpdatesBackground();
    sessionStorage.setItem('updateChecked', 'true');
}
```

## 🚀 Quick Start

1. **Log in as Master user**
2. **Click "Updates" in navigation bar**
3. **Click "Check for Updates"**
4. **If update available, click "Install Update Now"**
5. **Wait 30-60 seconds**
6. **Done! Page reloads with new version**

## 📞 Support

If you encounter issues:
1. Check the error message on the update page
2. Look for backup file in `backups/` folder
3. Restore from backup if needed
4. Report issue on GitHub: https://github.com/nathantugume/imsapp_retail/issues

---

**✨ Enjoy seamless updates! Your IMS Retail App stays current automatically!**

