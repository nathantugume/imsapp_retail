# 🖥️ Desktop Shortcut Installation Guide

## Quick Setup - One Command

Simply run this command from the project directory:

```bash
./install_desktop_shortcut.sh
```

That's it! A desktop shortcut will be created automatically.

---

## 📋 What Gets Installed

### 1. **Desktop Shortcut**
- 📌 Icon on your Desktop: "IMS Retail - Mini Price Hardware"
- 🖱️ Double-click to launch the application
- 🚀 Automatically starts PHP server and opens browser

### 2. **Application Menu Entry** (Optional)
- 🔍 Search for "IMS Retail" in your application launcher
- 📱 Available in applications menu
- Same functionality as desktop icon

---

## 🎯 How to Use

### Installation

```bash
cd /home/nathan/shop_mgt/imsapp
./install_desktop_shortcut.sh
```

### Launching the App

**Method 1: Desktop Icon**
1. Double-click the "IMS Retail" icon on your desktop
2. Terminal window opens showing startup progress
3. Browser automatically opens to http://localhost:8080
4. Login with your credentials

**Method 2: Application Menu**
1. Press `Super/Windows` key
2. Type "IMS Retail"
3. Click the application
4. System launches automatically

**Method 3: Manual Launch**
```bash
cd /home/nathan/shop_mgt/imsapp/desktop
./start_ims.sh
```

---

## ⚙️ What Happens When You Launch

1. ✅ Checks if PHP is installed
2. ✅ Checks if MySQL is running
3. ✅ Verifies database connection
4. ✅ Starts PHP development server on port 8080
5. ✅ Opens your default browser to the login page
6. ✅ Shows status in terminal window

---

## 🔧 Technical Details

### Desktop File Location
```
~/Desktop/IMS_Retail.desktop
~/.local/share/applications/IMS_Retail.desktop
```

### Configuration
- **Server Port**: 8080
- **Database**: imsapp_retail
- **MySQL User**: root
- **MySQL Password**: admin
- **Icon**: desktop/assets/icon.png

### File Structure
```
imsapp/
├── IMS_Retail.desktop           # Desktop shortcut file
├── install_desktop_shortcut.sh  # Installer script
└── desktop/
    ├── start_ims.sh             # Bash launcher
    ├── desktop_launcher.py      # Python launcher
    └── assets/
        └── icon.png             # Application icon
```

---

## 🚨 Troubleshooting

### "PHP not found"
**Solution**: Install PHP
```bash
sudo apt install php php-mysql php-zip
```

### "MySQL connection failed"
**Solution**: Ensure MySQL is running
```bash
sudo systemctl start mysql
# or
sudo service mysql start
```

### "Permission denied"
**Solution**: Make scripts executable
```bash
chmod +x install_desktop_shortcut.sh
chmod +x desktop/start_ims.sh
chmod +x desktop/desktop_launcher.py
```

### "Desktop shortcut doesn't appear"
**Solution**: 
1. Check if Desktop folder exists: `ls ~/Desktop`
2. If not, create it: `mkdir -p ~/Desktop`
3. Run installer again: `./install_desktop_shortcut.sh`

### Port 8080 already in use
**Solution**: Edit `desktop/desktop_launcher.py` and change:
```python
self.server_port = 8081  # or any available port
```

---

## 🎨 Customization

### Change Application Name
Edit `IMS_Retail.desktop`:
```ini
Name=Your Custom Name
Comment=Your custom description
```

### Change Icon
Replace the icon file:
```bash
cp your-icon.png desktop/assets/icon.png
```

### Change Port
Edit `desktop/desktop_launcher.py`:
```python
self.server_port = 9000  # Your preferred port
```

---

## 🔄 Uninstallation

To remove the desktop shortcut:

```bash
rm ~/Desktop/IMS_Retail.desktop
rm ~/.local/share/applications/IMS_Retail.desktop
```

---

## 💡 Benefits

✅ **Quick Access** - Launch app with single click  
✅ **No Browser Typing** - No need to remember localhost URL  
✅ **Professional** - Appears like a native application  
✅ **Convenient** - Available in application launcher  
✅ **Portable** - Works on any Linux distribution  

---

## 📞 Support

If you need help:
1. Check the terminal output for error messages
2. Verify MySQL is running: `sudo systemctl status mysql`
3. Test PHP: `php -v`
4. Test manual launch: `cd desktop && ./start_ims.sh`

---

## ✨ Quick Commands

```bash
# Install shortcut
./install_desktop_shortcut.sh

# Launch app manually
./desktop/start_ims.sh

# Check if shortcut exists
ls -l ~/Desktop/IMS_Retail.desktop

# Test PHP server manually
cd /home/nathan/shop_mgt/imsapp
php -S localhost:8080
```

---

**🎉 Enjoy quick access to your IMS Retail system!**

