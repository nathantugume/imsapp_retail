# Inventory Management System - Desktop Application

This is a desktop version of the Inventory Management System built with Electron, wrapping the PHP web application.

## Features

- **Native Desktop Experience**: Runs as a standalone desktop application
- **Auto-Start Servers**: Automatically starts PHP development server
- **Cross-Platform**: Works on Windows, macOS, and Linux
- **Full Feature Set**: All web application features available in desktop mode
- **Keyboard Shortcuts**: Quick access to common functions
- **Auto-Updates**: Built-in update mechanism (when configured)

## System Requirements

- **Node.js**: 16.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **RAM**: 512MB minimum
- **Disk Space**: 100MB free space

## Quick Start

### 1. Install Dependencies

```bash
cd desktop
npm install
```

### 2. Run Setup (Optional)

```bash
npm run setup
```

This will check your system requirements and set up the database.

### 3. Start the Application

```bash
npm start
```

## Development

### Run in Development Mode

```bash
npm run dev
```

This will start the application with developer tools enabled.

### Build for Distribution

```bash
# Build for current platform
npm run build

# Build for specific platforms
npm run build-win    # Windows
npm run build-mac    # macOS
npm run build-linux  # Linux
```

## Configuration

The application uses the following default configuration:

- **PHP Server Port**: 8080
- **MySQL Port**: 3306 (default)
- **Database**: imsapp
- **MySQL User**: root
- **MySQL Password**: admin

## Keyboard Shortcuts

- `Ctrl/Cmd + D`: Go to Dashboard
- `Ctrl/Cmd + P`: Go to Products
- `Ctrl/Cmd + O`: Go to Orders
- `Ctrl/Cmd + N`: New Order
- `Ctrl/Cmd + Shift + P`: New Product
- `Ctrl/Cmd + S`: Stock Reconciliation
- `Ctrl/Cmd + M`: Customer Payments
- `Ctrl/Cmd + Shift + M`: Database Migrations
- `Ctrl/Cmd + R`: Reload Application
- `Ctrl/Cmd + Shift + I`: Toggle Developer Tools

## Troubleshooting

### PHP Not Found

If you get a "PHP not found" error:

1. Install PHP from [php.net](https://www.php.net/downloads.php)
2. Add PHP to your system PATH
3. Restart the application

### MySQL Connection Failed

If MySQL connection fails:

1. Ensure MySQL is running
2. Check credentials in `config/config.php`
3. Verify database exists: `CREATE DATABASE imsapp;`

### Port Already in Use

If port 8080 is already in use:

1. Stop other applications using port 8080
2. Or modify the port in `main.js` (CONFIG.appPort)

### Application Won't Start

1. Check system requirements
2. Run `npm run setup` to verify installation
3. Check console output for error messages
4. Ensure all dependencies are installed

## File Structure

```
desktop/
├── main.js              # Main Electron process
├── setup.js             # Setup and requirements checker
├── package.json         # Node.js dependencies and scripts
├── assets/              # Application icons and resources
├── runtime/             # Runtime files (auto-created)
├── downloads/           # Download cache (auto-created)
└── README.md           # This file
```

## Building for Distribution

The application uses `electron-builder` to create distributable packages:

- **Windows**: Creates NSIS installer (.exe)
- **macOS**: Creates DMG package
- **Linux**: Creates AppImage

Built packages will be in the `dist/` directory.

## Security Notes

- The application runs PHP in development server mode
- MySQL credentials are stored in plain text (configurable)
- For production use, consider using a proper web server setup
- The application includes basic security measures but should be hardened for production

## Support

For issues and questions:

1. Check the troubleshooting section above
2. Review the console output for error messages
3. Ensure all system requirements are met
4. Verify PHP and MySQL are properly configured

## License

This desktop application follows the same license as the main Inventory Management System.





