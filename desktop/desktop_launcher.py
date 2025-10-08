#!/usr/bin/env python3
"""
Inventory Management System - Desktop Launcher
A simple desktop wrapper for the PHP web application
"""

import os
import sys
import subprocess
import webbrowser
import time
import threading
import signal
import json
from pathlib import Path

class IMSDesktopLauncher:
    def __init__(self):
        self.app_path = Path(__file__).parent.parent.absolute()
        self.desktop_path = Path(__file__).parent.absolute()
        self.php_server = None
        self.server_port = 8080
        self.server_url = f"http://localhost:{self.server_port}"
        self.running = True
        
        # Configuration
        self.config = {
            'php_path': self.find_php(),
            'mysql_host': 'localhost',
            'mysql_user': 'root',
            'mysql_password': 'admin',
            'mysql_database': 'imsapp',
            'server_port': self.server_port
        }
    
    def find_php(self):
        """Find PHP executable in common locations"""
        php_paths = [
            'php',  # System PATH
            '/usr/bin/php',  # Linux
            '/usr/local/bin/php',  # macOS
            'C:\\php\\php.exe',  # Windows
            'C:\\xampp\\php\\php.exe',  # XAMPP
            'C:\\wamp64\\bin\\php\\php8.2.12\\php.exe'  # WAMP
        ]
        
        for path in php_paths:
            try:
                result = subprocess.run([path, '--version'], 
                                      capture_output=True, text=True, timeout=5)
                if result.returncode == 0:
                    return path
            except (subprocess.TimeoutExpired, FileNotFoundError, OSError):
                continue
        
        return None
    
    def check_requirements(self):
        """Check if all requirements are met"""
        print("Checking system requirements...")
        
        # Check PHP
        if not self.config['php_path']:
            print("❌ PHP not found. Please install PHP 7.4 or higher.")
            return False
        else:
            print(f"✅ PHP found: {self.config['php_path']}")
        
        # Check MySQL
        mysql_ok = self.check_mysql()
        if not mysql_ok:
            print("❌ MySQL connection failed. Please ensure MySQL is running.")
            return False
        else:
            print("✅ MySQL connection successful")
        
        return True
    
    def check_mysql(self):
        """Check MySQL connection"""
        try:
            # Try to connect to MySQL
            cmd = [
                'mysql', '-h', self.config['mysql_host'],
                '-u', self.config['mysql_user'],
                f'-p{self.config["mysql_password"]}',
                '-e', 'SELECT 1'
            ]
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=10)
            return result.returncode == 0
        except (subprocess.TimeoutExpired, FileNotFoundError, OSError):
            return False
    
    def start_php_server(self):
        """Start PHP development server"""
        if not self.config['php_path']:
            raise Exception("PHP not found")
        
        cmd = [
            self.config['php_path'],
            '-S',
            f'localhost:{self.server_port}',
            '-t',
            str(self.app_path)
        ]
        
        print(f"Starting PHP server: {' '.join(cmd)}")
        
        try:
            self.php_server = subprocess.Popen(
                cmd,
                cwd=str(self.app_path),
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                text=True
            )
            
            # Wait a moment for server to start
            time.sleep(2)
            
            if self.php_server.poll() is None:
                print(f"✅ PHP server started on {self.server_url}")
                return True
            else:
                stdout, stderr = self.php_server.communicate()
                print(f"❌ Failed to start PHP server: {stderr}")
                return False
                
        except Exception as e:
            print(f"❌ Error starting PHP server: {e}")
            return False
    
    def open_browser(self):
        """Open the application in default browser"""
        print(f"Opening application in browser: {self.server_url}")
        webbrowser.open(self.server_url)
    
    def monitor_server(self):
        """Monitor PHP server and restart if needed"""
        while self.running:
            if self.php_server and self.php_server.poll() is not None:
                print("⚠️  PHP server stopped unexpectedly. Restarting...")
                if self.start_php_server():
                    self.open_browser()
                else:
                    print("❌ Failed to restart server")
                    break
            time.sleep(5)
    
    def signal_handler(self, signum, frame):
        """Handle shutdown signals"""
        print("\n🛑 Shutting down...")
        self.running = False
        if self.php_server:
            self.php_server.terminate()
            self.php_server.wait()
        sys.exit(0)
    
    def run(self):
        """Main application loop"""
        print("=" * 50)
        print("Inventory Management System - Desktop Launcher")
        print("=" * 50)
        
        # Check requirements
        if not self.check_requirements():
            print("\n❌ Requirements not met. Please install missing components.")
            input("Press Enter to exit...")
            return
        
        # Set up signal handlers
        signal.signal(signal.SIGINT, self.signal_handler)
        signal.signal(signal.SIGTERM, self.signal_handler)
        
        try:
            # Start PHP server
            if not self.start_php_server():
                print("❌ Failed to start PHP server")
                input("Press Enter to exit...")
                return
            
            # Open browser
            self.open_browser()
            
            # Start server monitor in background
            monitor_thread = threading.Thread(target=self.monitor_server, daemon=True)
            monitor_thread.start()
            
            print("\n" + "=" * 50)
            print("✅ Application is running!")
            print(f"🌐 URL: {self.server_url}")
            print("📱 The application should open in your default browser")
            print("🛑 Press Ctrl+C to stop the server")
            print("=" * 50)
            
            # Keep main thread alive
            while self.running:
                time.sleep(1)
                
        except KeyboardInterrupt:
            self.signal_handler(signal.SIGINT, None)
        except Exception as e:
            print(f"❌ Unexpected error: {e}")
            self.signal_handler(signal.SIGTERM, None)

def main():
    """Entry point"""
    launcher = IMSDesktopLauncher()
    launcher.run()

if __name__ == "__main__":
    main()




