const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');

class SetupHelper {
  constructor() {
    this.basePath = path.join(__dirname, '..');
    this.desktopPath = __dirname;
  }

  async checkRequirements() {
    console.log('Checking system requirements...\n');
    
    const requirements = {
      php: false,
      mysql: false,
      node: false,
      npm: false
    };

    // Check PHP
    try {
      const phpVersion = await this.execCommand('php --version');
      console.log('✓ PHP found:', phpVersion.split('\n')[0]);
      requirements.php = true;
    } catch (error) {
      console.log('✗ PHP not found. Please install PHP 7.4 or higher.');
    }

    // Check MySQL
    try {
      const mysqlVersion = await this.execCommand('mysql --version');
      console.log('✓ MySQL found:', mysqlVersion);
      requirements.mysql = true;
    } catch (error) {
      console.log('✗ MySQL not found. Please install MySQL 5.7 or higher.');
    }

    // Check Node.js
    try {
      const nodeVersion = await this.execCommand('node --version');
      console.log('✓ Node.js found:', nodeVersion);
      requirements.node = true;
    } catch (error) {
      console.log('✗ Node.js not found. Please install Node.js 16 or higher.');
    }

    // Check npm
    try {
      const npmVersion = await this.execCommand('npm --version');
      console.log('✓ npm found:', npmVersion);
      requirements.npm = true;
    } catch (error) {
      console.log('✗ npm not found. Please install npm.');
    }

    return requirements;
  }

  async setupDatabase() {
    console.log('\nSetting up database...');
    
    try {
      // Test MySQL connection
      await this.execCommand('mysql -u root -padmin -e "SELECT 1"');
      console.log('✓ MySQL connection successful');
      
      // Create database if it doesn't exist
      await this.execCommand('mysql -u root -padmin -e "CREATE DATABASE IF NOT EXISTS imsapp"');
      console.log('✓ Database "imsapp" created/verified');
      
      return true;
    } catch (error) {
      console.log('✗ Database setup failed:', error.message);
      console.log('\nPlease ensure MySQL is running with:');
      console.log('- Host: localhost');
      console.log('- User: root');
      console.log('- Password: admin');
      return false;
    }
  }

  async installDependencies() {
    console.log('\nInstalling Electron dependencies...');
    
    try {
      await this.execCommand('npm install', { cwd: this.desktopPath });
      console.log('✓ Dependencies installed successfully');
      return true;
    } catch (error) {
      console.log('✗ Failed to install dependencies:', error.message);
      return false;
    }
  }

  async createLauncher() {
    console.log('\nCreating desktop launcher...');
    
    const launcherContent = `#!/bin/bash
cd "${this.desktopPath}"
npm start
`;

    const launcherPath = path.join(this.desktopPath, 'launch.sh');
    fs.writeFileSync(launcherPath, launcherContent);
    fs.chmodSync(launcherPath, '755');
    
    console.log('✓ Desktop launcher created:', launcherPath);
  }

  async createWindowsLauncher() {
    const launcherContent = `@echo off
cd /d "${this.desktopPath}"
npm start
pause`;

    const launcherPath = path.join(this.desktopPath, 'launch.bat');
    fs.writeFileSync(launcherPath, launcherContent);
    
    console.log('✓ Windows launcher created:', launcherPath);
  }

  async execCommand(command, options = {}) {
    return new Promise((resolve, reject) => {
      exec(command, options, (error, stdout, stderr) => {
        if (error) {
          reject(error);
        } else {
          resolve(stdout);
        }
      });
    });
  }

  async run() {
    console.log('=== Inventory Management System - Desktop Setup ===\n');
    
    // Check requirements
    const requirements = await this.checkRequirements();
    
    if (!requirements.node || !requirements.npm) {
      console.log('\n❌ Node.js and npm are required to run the desktop application.');
      console.log('Please install Node.js from: https://nodejs.org/');
      return;
    }

    if (!requirements.php) {
      console.log('\n⚠️  PHP is not found. The desktop app will not work without PHP.');
      console.log('Please install PHP from: https://www.php.net/downloads.php');
    }

    if (!requirements.mysql) {
      console.log('\n⚠️  MySQL is not found. The desktop app will not work without MySQL.');
      console.log('Please install MySQL from: https://dev.mysql.com/downloads/');
    }

    // Install dependencies
    const depsInstalled = await this.installDependencies();
    if (!depsInstalled) {
      console.log('\n❌ Failed to install dependencies. Please check your internet connection.');
      return;
    }

    // Setup database
    if (requirements.mysql) {
      await this.setupDatabase();
    }

    // Create launchers
    if (process.platform === 'win32') {
      await this.createWindowsLauncher();
    } else {
      await this.createLauncher();
    }

    console.log('\n=== Setup Complete ===');
    console.log('\nTo start the desktop application:');
    console.log('1. Run: npm start (from the desktop directory)');
    console.log('2. Or double-click the launcher script');
    console.log('\nTo build the application:');
    console.log('- npm run build-win (Windows)');
    console.log('- npm run build-mac (macOS)');
    console.log('- npm run build-linux (Linux)');
  }
}

// Run setup if called directly
if (require.main === module) {
  const setup = new SetupHelper();
  setup.run().catch(console.error);
}

module.exports = SetupHelper;





