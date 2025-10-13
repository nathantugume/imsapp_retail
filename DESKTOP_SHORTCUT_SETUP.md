# 🖥️ Desktop Shortcut - Quick Access Setup

## ✨ Simple One-Click Access to IMS Retail

### 🎯 Quick Setup (Super Easy!)

1. **Log in to IMS Retail**
2. **Look for the purple banner** at the top of the dashboard
3. **Click "Download Shortcut Creator"** button
4. **Run the downloaded file** (Create_IMS_Retail_Shortcut.vbs)
5. **Done!** Find "IMS Retail" icon on your desktop

---

## 📱 Features

### 🚀 What You Get
- ✅ Desktop icon for one-click launch
- ✅ Automatic PHP server startup
- ✅ Browser opens automatically
- ✅ No need to type URLs
- ✅ Professional application experience

### 🎨 Smart Banner
- 🔔 Shows on first login (only once)
- 💾 Remembers if you dismissed it
- 📐 Clean, modern design
- ❌ Easy to dismiss if not needed

---

## 🔧 How It Works

### The Banner
```
┌─────────────────────────────────────────────┐
│ 🖥️ Create Desktop Shortcut                 │
│                                             │
│ Launch IMS Retail with one click!          │
│ Create a desktop shortcut for quick access │
│                                             │
│ [Download Shortcut Creator]                 │
│                                             │
│ ℹ️ One-time setup: Download, run, done!    │
└─────────────────────────────────────────────┘
```

### The Download
- 📥 Downloads: `Create_IMS_Retail_Shortcut.vbs`
- 📝 Small file (~500 bytes)
- 🔒 Safe VBScript (built into Windows)
- ⚡ Creates shortcut instantly

### The Shortcut
- 📌 Creates: `IMS Retail.lnk` on your desktop
- 🎯 Launches: `Start_IMS_Retail.bat`
- 🌐 Opens: http://localhost:8080
- 💾 Database: imsapp_retail

---

## 📋 Manual Installation (Alternative)

### Method 1: Use Installer Script

**Windows:**
```cmd
cd C:\path\to\imsapp
Install_Desktop_Shortcut.bat
```

**Linux:**
```bash
cd /home/nathan/shop_mgt/imsapp
./install_desktop_shortcut.sh
```

### Method 2: Direct Launch File

Just double-click:
- **Windows**: `Start_IMS_Retail.bat`
- **Linux**: `desktop/start_ims.sh`

---

## 🎬 User Experience

### First Time
1. User logs in
2. Banner appears with purple gradient
3. "Create Desktop Shortcut" option
4. Download with one click
5. Run the file
6. Icon appears on desktop

### Every Time After
1. Double-click desktop icon
2. Terminal shows startup progress
3. Browser opens to login page
4. Start working!

---

## 🛠️ Requirements

### Windows
- ✅ Windows 7 or higher
- ✅ PHP installed (XAMPP/WAMP recommended)
- ✅ MySQL running
- ✅ Database: imsapp_retail

### Linux
- ✅ Any modern distribution
- ✅ PHP 7.4+ installed
- ✅ MySQL running
- ✅ Desktop environment (GNOME/KDE/XFCE)

---

## 🔐 Security

**What the VBScript does:**
```vbscript
1. Creates a Windows shortcut (.lnk) file
2. Points to: Start_IMS_Retail.bat
3. Sets working directory
4. Adds description
5. That's it! Nothing else.
```

**It does NOT:**
- ❌ Access the internet
- ❌ Modify system files
- ❌ Install anything
- ❌ Require admin rights

---

## ⚙️ Customization

### Change Port
Edit `Start_IMS_Retail.bat`:
```batch
set PORT=8080  REM Change to your preferred port
```

### Change Database
Edit `desktop/desktop_launcher.py` or `Start_IMS_Retail.bat`:
```batch
set DB_NAME=your_database_name
```

### Custom Icon
Replace `desktop/assets/icon.png` with your own image.

---

## 🚨 Troubleshooting

### Banner doesn't appear
**Cause**: Already dismissed or localStorage blocked  
**Solution**: Clear localStorage or click this in browser console:
```javascript
localStorage.removeItem('shortcutBannerDismissed');
location.reload();
```

### Download doesn't start
**Cause**: Popup blocker or browser security  
**Solution**: 
1. Allow popups for localhost
2. Or download manually: `desktop/create-shortcut.php`

### Shortcut doesn't work
**Cause**: File paths or PHP not found  
**Solution**: 
1. Ensure PHP is in PATH
2. Check `Start_IMS_Retail.bat` paths
3. Run batch file manually to see errors

### "PHP not found" error
**Cause**: PHP not installed or not in PATH  
**Solution**: 
- Install XAMPP: https://www.apachefriends.org/
- Or add PHP to PATH manually

---

## 💡 Tips

### For First-Time Users
- ✅ Banner shows automatically
- ✅ Very clear instructions
- ✅ One-click download
- ✅ Works out of the box

### For Power Users
- 🔧 Dismiss the banner (won't show again)
- 📁 Keep the .bat file for manual launch
- ⚙️ Customize port and settings
- 🔄 Re-download anytime from dashboard

### For Administrators
- 📊 All users see the banner
- 🎯 Improves adoption
- 📱 Professional user experience
- ⚡ Reduces support calls

---

## 📁 Files Included

```
imsapp/
├── Start_IMS_Retail.bat              # Main launcher (Windows)
├── Install_Desktop_Shortcut.bat      # Manual installer (Windows)
├── Install_Desktop_Shortcut.ps1      # PowerShell installer (Windows)
├── install_desktop_shortcut.sh       # Installer (Linux)
├── IMS_Retail.desktop                # Shortcut file (Linux)
└── desktop/
    ├── start_ims.sh                  # Launcher (Linux)
    ├── create-shortcut.php           # Web-based creator
    └── assets/
        └── icon.png                  # Application icon
```

---

## ✅ Quick Test

After creating the shortcut:

1. **Close all browser windows**
2. **Double-click** the desktop icon
3. **Watch the magic happen:**
   - Terminal opens
   - Server starts
   - Browser launches
   - Login page appears
4. **Success!** 🎉

---

**🎊 Enjoy easy access to your IMS Retail system!**

