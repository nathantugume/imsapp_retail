# Inventory Management System - Windows Setup Guide

## Quick Start (Recommended)

1. **Download and install XAMPP** from https://xampp.org
2. **Run the Windows installer**: Double-click `install_windows.bat` (Run as Administrator)
3. **Start the application**: Double-click the desktop shortcut

## Manual Setup

### Prerequisites

1. **Python 3.6+**
   - Download from https://python.org/downloads/
   - ✅ Check "Add Python to PATH" during installation

2. **PHP 7.4+**
   - Option 1: XAMPP (Recommended) - https://xampp.org
   - Option 2: Standalone PHP - https://php.net/downloads.php
   - Add PHP to your system PATH

3. **MySQL**
   - Included with XAMPP
   - Or standalone MySQL Server

### Installation Steps

#### Option 1: Using XAMPP (Easiest)

1. **Install XAMPP**
   ```
   - Download XAMPP from https://xampp.org
   - Install to C:\xampp
   - Start XAMPP Control Panel
   - Start Apache and MySQL services
   ```

2. **Configure MySQL**
   ```
   - Open XAMPP Control Panel
   - Click "Admin" next to MySQL
   - Set root password to "admin"
   - Create database "imsapp"
   ```

3. **Add PHP to PATH**
   ```
   - Add C:\xampp\php to your system PATH
   - Restart Command Prompt
   ```

4. **Run the installer**
   ```
   - Right-click install_windows.bat
   - Select "Run as administrator"
   ```

#### Option 2: Manual Setup

1. **Install Python**
   ```
   - Download Python 3.6+ from python.org
   - Install with "Add to PATH" checked
   ```

2. **Install PHP**
   ```
   - Download PHP 7.4+ from php.net
   - Extract to C:\php
   - Add C:\php to system PATH
   ```

3. **Install MySQL**
   ```
   - Download MySQL Server from mysql.com
   - Install with root password "admin"
   - Create database "imsapp"
   ```

4. **Run the installer**
   ```
   - Right-click install_windows.bat
   - Select "Run as administrator"
   ```

### Starting the Application

After installation, you can start the application in several ways:

1. **Desktop Shortcut** (Recommended)
   - Double-click "Inventory Management System" on desktop

2. **Start Menu**
   - Start Menu → Programs → Inventory Management System

3. **Command Line**
   ```cmd
   cd C:\path\to\imsapp\desktop
   start_ims.bat
   ```

4. **Python Launcher**
   ```cmd
   cd C:\path\to\imsapp\desktop
   python desktop_launcher.py
   ```

### Default Login Credentials

- **Master Account**: admin / admin123
- **User Account**: user / user123

### Troubleshooting

#### "Python not found"
- Install Python from https://python.org
- Make sure "Add to PATH" is checked during installation
- Restart Command Prompt after installation

#### "PHP not found"
- Install XAMPP or standalone PHP
- Add PHP directory to system PATH
- Restart Command Prompt

#### "MySQL connection failed"
- Start MySQL service (XAMPP Control Panel)
- Check MySQL root password is "admin"
- Verify database "imsapp" exists

#### "Port 8080 already in use"
- The launcher will try different ports automatically
- Or manually edit `desktop_launcher.py` to change the port

#### Application won't start
- Check Windows Firewall settings
- Make sure no antivirus is blocking the application
- Run Command Prompt as Administrator

### File Structure

```
desktop/
├── install_windows.bat          # Windows installer (Run as Admin)
├── start_ims.bat               # Windows launcher
├── desktop_launcher.py         # Cross-platform Python launcher
├── install.py                  # Cross-platform installer
├── assets/
│   └── icon.png               # Application icon
└── WINDOWS_SETUP.md           # This guide
```

### Features

- ✅ **Auto-install**: One-click setup with dependency checking
- ✅ **Desktop Integration**: Desktop shortcuts and Start Menu entries
- ✅ **Cross-platform**: Works on Windows, Linux, and macOS
- ✅ **Auto-restart**: Server automatically restarts if it crashes
- ✅ **Browser Integration**: Opens automatically in default browser
- ✅ **Port Management**: Automatically finds available ports
- ✅ **Database Setup**: Creates database and imports schema

### Support

If you encounter issues:

1. Check this troubleshooting guide
2. Ensure all prerequisites are installed
3. Run the installer as Administrator
4. Check Windows Firewall and antivirus settings

The application runs locally on your computer and doesn't require internet connection after installation.


