const { app, dialog, BrowserWindow } = require('electron');
const path = require('path');
const fs = require('fs');
const https = require('https');
const { exec } = require('child_process');
const { promisify } = require('util');

const execAsync = promisify(exec);

class DesktopInstaller {
  constructor() {
    this.basePath = path.join(__dirname, '..');
    this.desktopPath = path.join(__dirname);
    this.downloadsPath = path.join(this.desktopPath, 'downloads');
    this.runtimePath = path.join(this.desktopPath, 'runtime');
  }

  async install() {
    try {
      console.log('Starting desktop application installation...');
      
      // Create necessary directories
      await this.createDirectories();
      
      // Download and install PHP
      await this.installPHP();
      
      // Download and install MySQL
      await this.installMySQL();
      
      // Setup database
      await this.setupDatabase();
      
      // Create desktop shortcuts
      await this.createShortcuts();
      
      console.log('Installation completed successfully!');
      return true;
    } catch (error) {
      console.error('Installation failed:', error);
      return false;
    }
  }

  async createDirectories() {
    const dirs = [
      this.downloadsPath,
      this.runtimePath,
      path.join(this.runtimePath, 'php'),
      path.join(this.runtimePath, 'mysql'),
      path.join(this.runtimePath, 'data'),
      path.join(this.runtimePath, 'data', 'mysql')
    ];

    for (const dir of dirs) {
      if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
        console.log(`Created directory: ${dir}`);
      }
    }
  }

  async installPHP() {
    const phpPath = path.join(this.runtimePath, 'php');
    
    if (fs.existsSync(path.join(phpPath, 'php.exe'))) {
      console.log('PHP already installed');
      return;
    }

    console.log('Downloading PHP...');
    
    // For Windows - download PHP 8.2
    const phpUrl = 'https://windows.php.net/downloads/releases/php-8.2.12-Win32-vs16-x64.zip';
    const phpZip = path.join(this.downloadsPath, 'php.zip');
    
    await this.downloadFile(phpUrl, phpZip);
    await this.extractZip(phpZip, phpPath);
    
    // Copy php.ini
    const phpIniSource = path.join(phpPath, 'php.ini-development');
    const phpIniTarget = path.join(phpPath, 'php.ini');
    
    if (fs.existsSync(phpIniSource)) {
      fs.copyFileSync(phpIniSource, phpIniTarget);
      
      // Enable required extensions
      const phpIniContent = fs.readFileSync(phpIniTarget, 'utf8');
      const updatedContent = phpIniContent
        .replace(/;extension=pdo_mysql/g, 'extension=pdo_mysql')
        .replace(/;extension=mysqli/g, 'extension=mysqli')
        .replace(/;extension=openssl/g, 'extension=openssl')
        .replace(/;extension=curl/g, 'extension=curl');
      
      fs.writeFileSync(phpIniTarget, updatedContent);
    }
    
    console.log('PHP installed successfully');
  }

  async installMySQL() {
    const mysqlPath = path.join(this.runtimePath, 'mysql');
    
    if (fs.existsSync(path.join(mysqlPath, 'bin', 'mysqld.exe'))) {
      console.log('MySQL already installed');
      return;
    }

    console.log('Downloading MySQL...');
    
    // For Windows - download MySQL 8.0
    const mysqlUrl = 'https://dev.mysql.com/get/Downloads/MySQL-8.0/mysql-8.0.35-winx64.zip';
    const mysqlZip = path.join(this.downloadsPath, 'mysql.zip');
    
    await this.downloadFile(mysqlUrl, mysqlZip);
    await this.extractZip(mysqlZip, mysqlPath);
    
    // Create MySQL configuration
    await this.createMySQLConfig();
    
    // Initialize MySQL data directory
    await this.initializeMySQL();
    
    console.log('MySQL installed successfully');
  }

  async createMySQLConfig() {
    const configPath = path.join(this.runtimePath, 'data', 'my.ini');
    const config = `[mysqld]
port=3307
datadir=${path.join(this.runtimePath, 'data', 'mysql').replace(/\\/g, '/')}
basedir=${path.join(this.runtimePath, 'mysql').replace(/\\/g, '/')}
default_authentication_plugin=mysql_native_password
skip-grant-tables
skip-networking
`;

    fs.writeFileSync(configPath, config);
  }

  async initializeMySQL() {
    const mysqlPath = path.join(this.runtimePath, 'mysql', 'bin', 'mysqld.exe');
    const dataPath = path.join(this.runtimePath, 'data', 'mysql');
    const configPath = path.join(this.runtimePath, 'data', 'my.ini');
    
    try {
      await execAsync(`"${mysqlPath}" --defaults-file="${configPath}" --initialize-insecure --datadir="${dataPath}"`);
      console.log('MySQL initialized successfully');
    } catch (error) {
      console.error('MySQL initialization failed:', error);
    }
  }

  async setupDatabase() {
    console.log('Setting up database...');
    
    // Start MySQL server temporarily
    const mysqlPath = path.join(this.runtimePath, 'mysql', 'bin', 'mysqld.exe');
    const configPath = path.join(this.runtimePath, 'data', 'my.ini');
    
    const mysqlServer = require('child_process').spawn(mysqlPath, [
      '--defaults-file=' + configPath,
      '--console'
    ]);
    
    // Wait for server to start
    await new Promise(resolve => setTimeout(resolve, 5000));
    
    try {
      // Create database and user
      const mysqlClient = path.join(this.runtimePath, 'mysql', 'bin', 'mysql.exe');
      
      await execAsync(`"${mysqlClient}" -u root -e "CREATE DATABASE IF NOT EXISTS imsapp;"`);
      await execAsync(`"${mysqlClient}" -u root -e "CREATE USER IF NOT EXISTS 'imsuser'@'localhost' IDENTIFIED BY 'admin';"`);
      await execAsync(`"${mysqlClient}" -u root -e "GRANT ALL PRIVILEGES ON imsapp.* TO 'imsuser'@'localhost';"`);
      await execAsync(`"${mysqlClient}" -u root -e "FLUSH PRIVILEGES;"`);
      
      console.log('Database setup completed');
    } catch (error) {
      console.error('Database setup failed:', error);
    } finally {
      // Stop MySQL server
      mysqlServer.kill();
    }
  }

  async createShortcuts() {
    console.log('Creating desktop shortcuts...');
    
    // This would typically use platform-specific methods
    // For now, we'll create a simple launcher script
    const launcherPath = path.join(this.desktopPath, 'launch.bat');
    const launcherContent = `@echo off
cd /d "${this.desktopPath}"
npm start
pause`;

    fs.writeFileSync(launcherPath, launcherContent);
    console.log('Desktop launcher created');
  }

  async downloadFile(url, destination) {
    return new Promise((resolve, reject) => {
      const file = fs.createWriteStream(destination);
      
      https.get(url, (response) => {
        response.pipe(file);
        
        file.on('finish', () => {
          file.close();
          resolve();
        });
        
        file.on('error', (error) => {
          fs.unlink(destination, () => {});
          reject(error);
        });
      }).on('error', (error) => {
        reject(error);
      });
    });
  }

  async extractZip(zipPath, extractPath) {
    const AdmZip = require('adm-zip');
    const zip = new AdmZip(zipPath);
    zip.extractAllTo(extractPath, true);
  }
}

module.exports = DesktopInstaller;





