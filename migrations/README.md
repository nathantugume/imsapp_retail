# IMS App Database Migration Tools

This directory contains comprehensive database migration tools for the IMS App, providing easy export, import, and backup functionality with full backward compatibility support.

## Overview

The migration tools are designed to:
- Export databases with multiple compatibility modes (MySQL 5.7, 8.0, MariaDB 10.3-10.5)
- Import databases with safety checks and validation
- Create automated backups with versioning
- Maintain backward compatibility across different MySQL versions
- Provide command-line interfaces for easy automation

## Files

### Core Classes
- `DatabaseExporter.php` - Main export functionality with compatibility options
- `DatabaseImporter.php` - Import functionality with safety checks
- `BackupManager.php` - Automated backup management with versioning
- `DatabaseMigration.php` - Existing migration system (unchanged)

### Command-Line Scripts
- `export-database.php` - Export database with various options
- `import-database.php` - Import database with safety features
- `backup-database.php` - Create and manage automated backups
- `run-migration.php` - Run database migrations (existing)
- `rollback-migration.php` - Rollback migrations (existing)

## Quick Start

### 1. Export Database

```bash
# Full database export with MySQL 5.7 compatibility
php export-database.php --type=full --compatibility=mysql5.7

# Structure-only export
php export-database.php --type=structure --compression=none

# Data-only export
php export-database.php --type=data --compression=gzip
```

### 2. Import Database

```bash
# Import with safety checks
php import-database.php backup.sql --dry-run

# Import with backup creation
php import-database.php backup.sql --backup-before-import

# Import with error skipping
php import-database.php backup.sql --skip-errors
```

### 3. Create Backups

```bash
# Full backup
php backup-database.php --type=full --compression=gzip

# Incremental backup
php backup-database.php --type=incremental --description="Daily backup"

# Schedule automated backups
php backup-database.php --schedule="0 2 * * *" --type=full
```

## Detailed Usage

### Database Export

The `export-database.php` script provides comprehensive export functionality:

#### Export Types
- `full` - Complete database export (structure + data)
- `structure` - Database structure only (no data)
- `data` - Database data only (no structure)

#### Compatibility Modes
- `mysql5.7` - MySQL 5.7 compatibility (default)
- `mysql8.0` - MySQL 8.0 compatibility
- `mariadb10.3` - MariaDB 10.3 compatibility
- `mariadb10.4` - MariaDB 10.4 compatibility
- `mariadb10.5` - MariaDB 10.5 compatibility

#### Compression Options
- `none` - No compression
- `gzip` - GZIP compression (default)
- `bzip2` - BZIP2 compression

#### Examples

```bash
# Export for MySQL 5.7 with GZIP compression
php export-database.php --type=full --compatibility=mysql5.7 --compression=gzip

# Export structure only for MariaDB 10.4
php export-database.php --type=structure --compatibility=mariadb10.4 --compression=none

# Export data only with custom output directory
php export-database.php --type=data --output=/backup/exports/

# Show export history
php export-database.php --history

# Clean old exports (older than 30 days)
php export-database.php --clean=30
```

### Database Import

The `import-database.php` script provides safe import functionality:

#### Safety Features
- Compatibility checks before import
- Automatic backup creation before import
- Dry run mode for validation
- Error handling and reporting
- Transaction support with rollback

#### Examples

```bash
# Dry run import (validation only)
php import-database.php backup.sql --dry-run

# Import with backup creation
php import-database.php backup.sql --backup-before-import

# Import with error skipping
php import-database.php backup.sql --skip-errors

# Import with custom timeout
php import-database.php backup.sql --timeout=600

# Show import history
php import-database.php --history
```

#### Supported File Formats
- `.sql` - Plain SQL file
- `.sql.gz` - GZIP compressed SQL file
- `.sql.bz2` - BZIP2 compressed SQL file

### Automated Backups

The `backup-database.php` script provides comprehensive backup management:

#### Backup Types
- `full` - Complete database backup
- `incremental` - Only changed data since last backup
- `structure_only` - Database structure only
- `data_only` - Database data only

#### Features
- Versioning system
- Automatic cleanup of old backups
- Compression support
- Metadata tracking
- Restore functionality

#### Examples

```bash
# Create full backup
php backup-database.php --type=full --compression=gzip

# Create incremental backup
php backup-database.php --type=incremental --description="Daily backup"

# Schedule daily backups at 2 AM
php backup-database.php --schedule="0 2 * * *" --type=full

# Restore from backup
php backup-database.php --restore=backup_file.sql --dry-run

# Show backup history
php backup-database.php --history
```

#### Scheduling

Use cron expressions to schedule automated backups:

```bash
# Daily at 2 AM
php backup-database.php --schedule="0 2 * * *" --type=full

# Every 6 hours
php backup-database.php --schedule="0 */6 * * *" --type=incremental

# Weekly on Sunday
php backup-database.php --schedule="0 0 * * 0" --type=full

# Monthly on 1st
php backup-database.php --schedule="0 0 1 * *" --type=full
```

## Migration Scenarios

### Scenario 1: Moving to New Server

1. **Export from old server:**
   ```bash
   php export-database.php --type=full --compatibility=mysql5.7 --compression=gzip
   ```

2. **Transfer file to new server**

3. **Import on new server:**
   ```bash
   php import-database.php imsapp_export_2025-01-15_14-30-25.sql.gz --dry-run
   php import-database.php imsapp_export_2025-01-15_14-30-25.sql.gz
   ```

### Scenario 2: Database Upgrade

1. **Create backup before upgrade:**
   ```bash
   php backup-database.php --type=full --description="Pre-upgrade backup"
   ```

2. **Export with new compatibility:**
   ```bash
   php export-database.php --type=full --compatibility=mysql8.0
   ```

3. **Import with new compatibility:**
   ```bash
   php import-database.php imsapp_export_2025-01-15_14-30-25.sql --dry-run
   php import-database.php imsapp_export_2025-01-15_14-30-25.sql
   ```

### Scenario 3: Development Environment Setup

1. **Export from production:**
   ```bash
   php export-database.php --type=full --compatibility=mysql5.7 --compression=gzip
   ```

2. **Import to development:**
   ```bash
   php import-database.php production_backup.sql.gz --create-database --drop-existing
   ```

## Compatibility Matrix

| Export Compatibility | MySQL 5.7 | MySQL 8.0 | MariaDB 10.3 | MariaDB 10.4 | MariaDB 10.5 |
|---------------------|------------|-----------|--------------|--------------|--------------|
| mysql5.7            | ✅ Full    | ✅ Full    | ✅ Full      | ✅ Full      | ✅ Full      |
| mysql8.0            | ⚠️ Partial | ✅ Full    | ⚠️ Partial   | ⚠️ Partial   | ⚠️ Partial   |
| mariadb10.3         | ✅ Full    | ✅ Full    | ✅ Full      | ✅ Full      | ✅ Full      |
| mariadb10.4         | ✅ Full    | ✅ Full    | ✅ Full      | ✅ Full      | ✅ Full      |
| mariadb10.5         | ✅ Full    | ✅ Full    | ✅ Full      | ✅ Full      | ✅ Full      |

## File Structure

```
migrations/
├── DatabaseExporter.php      # Export functionality
├── DatabaseImporter.php      # Import functionality
├── BackupManager.php         # Backup management
├── DatabaseMigration.php     # Existing migrations
├── export-database.php       # Export CLI script
├── import-database.php       # Import CLI script
├── backup-database.php       # Backup CLI script
├── run-migration.php         # Migration runner
├── rollback-migration.php    # Migration rollback
├── exports/                  # Export files directory
├── imports/                  # Import files directory
├── backups/                  # Backup files directory
└── README.md                 # This file
```

## Error Handling

### Common Issues and Solutions

1. **Permission Denied**
   - Ensure PHP has write permissions to export/import directories
   - Check file ownership and permissions

2. **Memory Limit Exceeded**
   - Increase PHP memory limit in php.ini
   - Use smaller chunk sizes for large imports

3. **Timeout Issues**
   - Increase timeout values for large operations
   - Use compression to reduce file sizes

4. **Compatibility Warnings**
   - Review warnings before proceeding
   - Test imports in development environment first

## Best Practices

1. **Always test imports in development first**
2. **Create backups before any import operation**
3. **Use dry-run mode to validate imports**
4. **Monitor disk space for exports and backups**
5. **Schedule regular backups for production systems**
6. **Keep export files in version control for schema changes**
7. **Document any custom modifications to the migration tools**

## Troubleshooting

### Debug Mode

Enable debug mode by setting environment variable:
```bash
export DEBUG=1
php export-database.php --type=full
```

### Log Files

Check PHP error logs for detailed error information:
```bash
tail -f /var/log/php_errors.log
```

### Performance Optimization

For large databases:
1. Use compression to reduce file sizes
2. Increase PHP memory limit
3. Use incremental backups for regular operations
4. Consider using mysqldump for very large exports

## Support

For issues or questions:
1. Check the error messages and logs
2. Review the compatibility matrix
3. Test with dry-run mode first
4. Create minimal test cases to reproduce issues

## Version History

- v1.0 - Initial release with full export/import/backup functionality
- Compatible with IMS App database schema
- Supports MySQL 5.7+ and MariaDB 10.3+


