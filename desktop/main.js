const { app, BrowserWindow, Menu, dialog, shell, ipcMain } = require('electron');
const path = require('path');
const { spawn } = require('child_process');
const fs = require('fs');

let mainWindow;
let phpServer;
let mysqlServer;

// Configuration
const CONFIG = {
  appPort: 8080,
  mysqlPort: 3307,
  appPath: path.join(__dirname, '..'), // Parent directory contains the PHP app
  dataPath: path.join(__dirname, 'data')
};

// Create main window
function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1400,
    height: 900,
    minWidth: 1000,
    minHeight: 700,
    webPreferences: {
      nodeIntegration: false,
      contextIsolation: true,
      enableRemoteModule: false
    },
    icon: path.join(__dirname, 'assets', 'icon.png'),
    title: 'Inventory Management System',
    show: false,
    titleBarStyle: 'default'
  });

  // Load the application
  mainWindow.loadURL(`http://localhost:${CONFIG.appPort}`);

  // Show window when ready
  mainWindow.once('ready-to-show', () => {
    mainWindow.show();
    
    // Open DevTools in development
    if (process.env.NODE_ENV === 'development') {
      mainWindow.webContents.openDevTools();
    }
  });

  // Handle window closed
  mainWindow.on('closed', () => {
    mainWindow = null;
  });

  // Handle external links
  mainWindow.webContents.setWindowOpenHandler(({ url }) => {
    shell.openExternal(url);
    return { action: 'deny' };
  });

  // Handle navigation
  mainWindow.webContents.on('will-navigate', (event, navigationUrl) => {
    const parsedUrl = new URL(navigationUrl);
    
    if (parsedUrl.origin !== `http://localhost:${CONFIG.appPort}`) {
      event.preventDefault();
      shell.openExternal(navigationUrl);
    }
  });
}

// Start PHP server
function startPHPServer() {
  return new Promise((resolve, reject) => {
    // Try to find PHP in common locations
    const phpPaths = [
      'php', // System PATH
      '/usr/bin/php', // Linux
      '/usr/local/bin/php', // macOS
      'C:\\php\\php.exe', // Windows
      'C:\\xampp\\php\\php.exe', // XAMPP
      'C:\\wamp64\\bin\\php\\php8.2.12\\php.exe' // WAMP
    ];

    let phpPath = null;
    for (const path of phpPaths) {
      try {
        require('child_process').execSync(`${path} --version`, { stdio: 'ignore' });
        phpPath = path;
        break;
      } catch (e) {
        // Continue to next path
      }
    }

    if (!phpPath) {
      reject(new Error('PHP not found. Please install PHP or add it to your PATH.'));
      return;
    }

    const phpArgs = [
      '-S',
      `localhost:${CONFIG.appPort}`,
      '-t',
      CONFIG.appPath
    ];

    console.log(`Starting PHP server with: ${phpPath} ${phpArgs.join(' ')}`);
    
    phpServer = spawn(phpPath, phpArgs, {
      cwd: CONFIG.appPath,
      stdio: ['pipe', 'pipe', 'pipe']
    });

    phpServer.stdout.on('data', (data) => {
      console.log(`PHP Server: ${data}`);
    });

    phpServer.stderr.on('data', (data) => {
      console.error(`PHP Server Error: ${data}`);
    });

    phpServer.on('error', (error) => {
      console.error('Failed to start PHP server:', error);
      reject(error);
    });

    phpServer.on('exit', (code) => {
      console.log(`PHP server exited with code ${code}`);
    });

    // Wait a moment for server to start
    setTimeout(() => {
      resolve();
    }, 2000);
  });
}

// Check if MySQL is running
async function checkMySQL() {
  return new Promise((resolve) => {
    const mysql = require('child_process').spawn('mysql', [
      '-h', 'localhost',
      '-u', 'root',
      '-padmin',
      '-e', 'SELECT 1'
    ], { stdio: 'ignore' });

    mysql.on('close', (code) => {
      resolve(code === 0);
    });

    mysql.on('error', () => {
      resolve(false);
    });

    // Timeout after 3 seconds
    setTimeout(() => {
      mysql.kill();
      resolve(false);
    }, 3000);
  });
}

// Setup application menu
function createMenu() {
  const template = [
    {
      label: 'File',
      submenu: [
        {
          label: 'New Order',
          accelerator: 'CmdOrCtrl+N',
          click: () => {
            mainWindow.loadURL(`http://localhost:${CONFIG.appPort}/order.php`);
          }
        },
        {
          label: 'New Product',
          accelerator: 'CmdOrCtrl+Shift+P',
          click: () => {
            mainWindow.loadURL(`http://localhost:${CONFIG.appPort}/product.php`);
          }
        },
        { type: 'separator' },
        {
          label: 'Exit',
          accelerator: process.platform === 'darwin' ? 'Cmd+Q' : 'Ctrl+Q',
          click: () => {
            app.quit();
          }
        }
      ]
    },
    {
      label: 'View',
      submenu: [
        {
          label: 'Dashboard',
          accelerator: 'CmdOrCtrl+D',
          click: () => {
            mainWindow.loadURL(`http://localhost:${CONFIG.appPort}/index.php`);
          }
        },
        {
          label: 'Products',
          accelerator: 'CmdOrCtrl+P',
          click: () => {
            mainWindow.loadURL(`http://localhost:${CONFIG.appPort}/product.php`);
          }
        },
        {
          label: 'Orders',
          accelerator: 'CmdOrCtrl+O',
          click: () => {
            mainWindow.loadURL(`http://localhost:${CONFIG.appPort}/order.php`);
          }
        },
        {
          label: 'Stock Reconciliation',
          accelerator: 'CmdOrCtrl+S',
          click: () => {
            mainWindow.loadURL(`http://localhost:${CONFIG.appPort}/stock-reconciliation.php`);
          }
        },
        {
          label: 'Customer Payments',
          accelerator: 'CmdOrCtrl+M',
          click: () => {
            mainWindow.loadURL(`http://localhost:${CONFIG.appPort}/customer-payments.php`);
          }
        },
        { type: 'separator' },
        {
          label: 'Reload',
          accelerator: 'CmdOrCtrl+R',
          click: () => {
            mainWindow.reload();
          }
        },
        {
          label: 'Toggle Developer Tools',
          accelerator: process.platform === 'darwin' ? 'Alt+Cmd+I' : 'Ctrl+Shift+I',
          click: () => {
            mainWindow.webContents.toggleDevTools();
          }
        }
      ]
    },
    {
      label: 'Tools',
      submenu: [
        {
          label: 'Database Migrations',
          accelerator: 'CmdOrCtrl+Shift+M',
          click: () => {
            mainWindow.loadURL(`http://localhost:${CONFIG.appPort}/migrations.php`);
          }
        },
        {
          label: 'Restart Server',
          click: async () => {
            await restartServer();
          }
        }
      ]
    },
    {
      label: 'Help',
      submenu: [
        {
          label: 'About',
          click: () => {
            dialog.showMessageBox(mainWindow, {
              type: 'info',
              title: 'About Inventory Management System',
              message: 'Inventory Management System',
              detail: 'Version 1.0.0\n\nA comprehensive inventory management solution with:\n• Product Management\n• Order Processing\n• Stock Reconciliation\n• Customer Payments\n• Database Migrations\n\nBuilt with PHP, MySQL, and Electron.'
            });
          }
        },
        {
          label: 'System Requirements',
          click: () => {
            dialog.showMessageBox(mainWindow, {
              type: 'info',
              title: 'System Requirements',
              message: 'System Requirements',
              detail: '• PHP 7.4 or higher\n• MySQL 5.7 or higher\n• 100MB free disk space\n• 512MB RAM minimum\n\nMake sure PHP and MySQL are installed and running.'
            });
          }
        }
      ]
    }
  ];

  const menu = Menu.buildFromTemplate(template);
  Menu.setApplicationMenu(menu);
}

// Restart server
async function restartServer() {
  try {
    if (phpServer) {
      phpServer.kill();
    }
    
    await startPHPServer();
    mainWindow.reload();
    
    dialog.showMessageBox(mainWindow, {
      type: 'info',
      title: 'Server Restarted',
      message: 'PHP server has been restarted successfully.'
    });
  } catch (error) {
    dialog.showErrorBox('Server Restart Failed', error.message);
  }
}

// App event handlers
app.whenReady().then(async () => {
  try {
    // Check MySQL connection
    const mysqlRunning = await checkMySQL();
    if (!mysqlRunning) {
      const result = await dialog.showMessageBox(mainWindow, {
        type: 'warning',
        title: 'MySQL Not Running',
        message: 'MySQL server is not running or not accessible.',
        detail: 'Please make sure MySQL is installed and running on localhost with:\n• Host: localhost\n• User: root\n• Password: admin\n• Database: imsapp',
        buttons: ['Continue Anyway', 'Exit'],
        defaultId: 0
      });
      
      if (result.response === 1) {
        app.quit();
        return;
      }
    }
    
    // Start PHP server
    await startPHPServer();
    
    // Create window and menu
    createWindow();
    createMenu();
    
    console.log('Application started successfully');
  } catch (error) {
    console.error('Failed to start application:', error);
    dialog.showErrorBox('Startup Error', `Failed to start the application:\n\n${error.message}\n\nPlease make sure PHP is installed and accessible from the command line.`);
    app.quit();
  }
});

app.on('window-all-closed', () => {
  // Kill servers before quitting
  if (phpServer) {
    phpServer.kill();
  }
  
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

app.on('activate', () => {
  if (BrowserWindow.getAllWindows().length === 0) {
    createWindow();
  }
});

// Handle app quit
app.on('before-quit', () => {
  if (phpServer) {
    phpServer.kill();
  }
});

// Security: Prevent new window creation
app.on('web-contents-created', (event, contents) => {
  contents.on('new-window', (event, navigationUrl) => {
    event.preventDefault();
    shell.openExternal(navigationUrl);
  });
});

// IPC handlers
ipcMain.handle('get-app-info', () => {
  return {
    version: app.getVersion(),
    platform: process.platform,
    arch: process.arch
  };
});

ipcMain.handle('restart-server', async () => {
  await restartServer();
  return true;
});