#!/usr/bin/env python3
"""
Inventory Management System - Desktop Installer
Sets up the desktop application with auto-install bootstrap
"""

import os
import sys
import subprocess
import json
import shutil
from pathlib import Path

class IMSInstaller:
    def __init__(self):
        self.base_path = Path(__file__).parent.parent.absolute()
        self.desktop_path = Path(__file__).parent.absolute()
        self.install_path = None
        
    def check_python(self):
        """Check Python version"""
        print("Checking Python version...")
        version = sys.version_info
        if version.major < 3 or (version.major == 3 and version.minor < 6):
            print(f"❌ Python 3.6+ required, found {version.major}.{version.minor}")
            return False
        print(f"✅ Python {version.major}.{version.minor}.{version.micro}")
        return True
    
    def check_php(self):
        """Check if PHP is available"""
        print("Checking PHP...")
        try:
            result = subprocess.run(['php', '--version'], 
                                  capture_output=True, text=True, timeout=10)
            if result.returncode == 0:
                version_line = result.stdout.split('\n')[0]
                print(f"✅ {version_line}")
                return True
        except (subprocess.TimeoutExpired, FileNotFoundError):
            pass
        
        print("❌ PHP not found. Please install PHP 7.4 or higher.")
        print("   Ubuntu/Debian: sudo apt install php php-mysql")
        print("   CentOS/RHEL: sudo yum install php php-mysql")
        print("   Windows: Download from https://php.net")
        return False
    
    def check_mysql(self):
        """Check MySQL connection"""
        print("Checking MySQL...")
        try:
            result = subprocess.run([
                'mysql', '-u', 'root', '-padmin', '-e', 'SELECT 1'
            ], capture_output=True, text=True, timeout=10)
            
            if result.returncode == 0:
                print("✅ MySQL connection successful")
                return True
        except (subprocess.TimeoutExpired, FileNotFoundError):
            pass
        
        print("❌ MySQL connection failed.")
        print("   Please ensure MySQL is running with:")
        print("   - Host: localhost")
        print("   - User: root")
        print("   - Password: admin")
        print("   - Database: imsapp")
        return False
    
    def setup_database(self):
        """Setup database if needed"""
        print("Setting up database...")
        try:
            # Create database
            subprocess.run([
                'mysql', '-u', 'root', '-padmin', '-e', 
                'CREATE DATABASE IF NOT EXISTS imsapp'
            ], check=True, capture_output=True)
            print("✅ Database 'imsapp' created/verified")
            
            # Import schema if exists
            schema_file = self.base_path / 'database' / 'schema.sql'
            if schema_file.exists():
                subprocess.run([
                    'mysql', '-u', 'root', '-padmin', 'imsapp'
                ], stdin=open(schema_file), check=True, capture_output=True)
                print("✅ Database schema imported")
            
            return True
        except subprocess.CalledProcessError as e:
            print(f"❌ Database setup failed: {e}")
            return False
    
    def create_desktop_shortcut(self):
        """Create desktop shortcut"""
        print("Creating desktop shortcut...")
        
        if sys.platform == "win32":
            self.create_windows_shortcut()
        else:
            self.create_unix_shortcut()
    
    def create_windows_shortcut(self):
        """Create Windows shortcut"""
        try:
            import winshell
            from win32com.client import Dispatch
            
            desktop = winshell.desktop()
            shortcut_path = os.path.join(desktop, "Inventory Management System.lnk")
            
            shell = Dispatch('WScript.Shell')
            shortcut = shell.CreateShortCut(shortcut_path)
            shortcut.Targetpath = str(self.desktop_path / "start_ims.bat")
            shortcut.WorkingDirectory = str(self.desktop_path)
            shortcut.IconLocation = str(self.desktop_path / "assets" / "icon.png")
            shortcut.save()
            
            print("✅ Desktop shortcut created")
        except ImportError:
            print("⚠️  Could not create desktop shortcut (missing pywin32)")
        except Exception as e:
            print(f"⚠️  Could not create desktop shortcut: {e}")
    
    def create_unix_shortcut(self):
        """Create Unix desktop file"""
        try:
            desktop_file = f"""[Desktop Entry]
Version=1.0
Type=Application
Name=Inventory Management System
Comment=Desktop version of IMS
Exec={self.desktop_path}/start_ims.sh
Icon={self.desktop_path}/assets/icon.png
Terminal=false
Categories=Office;Database;
"""
            
            desktop_dir = Path.home() / "Desktop"
            if not desktop_dir.exists():
                desktop_dir = Path.home() / "desktop"
            
            if desktop_dir.exists():
                shortcut_path = desktop_dir / "Inventory Management System.desktop"
                with open(shortcut_path, 'w') as f:
                    f.write(desktop_file)
                
                os.chmod(shortcut_path, 0o755)
                print("✅ Desktop shortcut created")
            else:
                print("⚠️  Desktop directory not found")
        except Exception as e:
            print(f"⚠️  Could not create desktop shortcut: {e}")
    
    def create_start_menu_entry(self):
        """Create start menu entry"""
        print("Creating start menu entry...")
        
        if sys.platform == "win32":
            self.create_windows_start_menu()
        else:
            self.create_unix_menu_entry()
    
    def create_windows_start_menu(self):
        """Create Windows start menu entry"""
        try:
            import winshell
            
            start_menu = winshell.start_menu()
            programs_dir = os.path.join(start_menu, "Programs")
            ims_dir = os.path.join(programs_dir, "Inventory Management System")
            
            os.makedirs(ims_dir, exist_ok=True)
            
            shortcut_path = os.path.join(ims_dir, "Inventory Management System.lnk")
            
            from win32com.client import Dispatch
            shell = Dispatch('WScript.Shell')
            shortcut = shell.CreateShortCut(shortcut_path)
            shortcut.Targetpath = str(self.desktop_path / "start_ims.bat")
            shortcut.WorkingDirectory = str(self.desktop_path)
            shortcut.IconLocation = str(self.desktop_path / "assets" / "icon.png")
            shortcut.save()
            
            print("✅ Start menu entry created")
        except ImportError:
            print("⚠️  Could not create start menu entry (missing pywin32)")
        except Exception as e:
            print(f"⚠️  Could not create start menu entry: {e}")
    
    def create_unix_menu_entry(self):
        """Create Unix menu entry"""
        try:
            applications_dir = Path.home() / ".local" / "share" / "applications"
            applications_dir.mkdir(parents=True, exist_ok=True)
            
            desktop_file = f"""[Desktop Entry]
Version=1.0
Type=Application
Name=Inventory Management System
Comment=Desktop version of IMS
Exec={self.desktop_path}/start_ims.sh
Icon={self.desktop_path}/assets/icon.png
Terminal=false
Categories=Office;Database;
"""
            
            menu_path = applications_dir / "inventory-management-system.desktop"
            with open(menu_path, 'w') as f:
                f.write(desktop_file)
            
            os.chmod(menu_path, 0o755)
            print("✅ Menu entry created")
        except Exception as e:
            print(f"⚠️  Could not create menu entry: {e}")
    
    def test_installation(self):
        """Test the installation"""
        print("Testing installation...")
        
        try:
            # Test Python launcher
            result = subprocess.run([
                sys.executable, str(self.desktop_path / "desktop_launcher.py"), "--test"
            ], capture_output=True, text=True, timeout=30)
            
            if result.returncode == 0:
                print("✅ Installation test passed")
                return True
            else:
                print(f"❌ Installation test failed: {result.stderr}")
                return False
        except Exception as e:
            print(f"❌ Installation test failed: {e}")
            return False
    
    def run(self):
        """Run the installer"""
        print("=" * 60)
        print("Inventory Management System - Desktop Installer")
        print("=" * 60)
        print()
        
        # Check requirements
        if not self.check_python():
            return False
        
        if not self.check_php():
            return False
        
        if not self.check_mysql():
            return False
        
        print()
        
        # Setup database
        if not self.setup_database():
            print("⚠️  Database setup failed, but continuing...")
        
        print()
        
        # Create shortcuts
        self.create_desktop_shortcut()
        self.create_start_menu_entry()
        
        print()
        
        # Test installation
        if self.test_installation():
            print()
            print("=" * 60)
            print("✅ Installation completed successfully!")
            print("=" * 60)
            print()
            print("You can now start the application by:")
            print(f"1. Double-clicking the desktop shortcut")
            print(f"2. Running: {self.desktop_path}/start_ims.sh")
            print(f"3. Running: python3 {self.desktop_path}/desktop_launcher.py")
            print()
            print("The application will start a local server and open in your browser.")
            return True
        else:
            print()
            print("=" * 60)
            print("❌ Installation completed with errors")
            print("=" * 60)
            print("Please check the error messages above and try again.")
            return False

def main():
    """Entry point"""
    installer = IMSInstaller()
    success = installer.run()
    
    if not success:
        input("Press Enter to exit...")
        sys.exit(1)

if __name__ == "__main__":
    main()




