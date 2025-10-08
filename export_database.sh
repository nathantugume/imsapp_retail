#!/bin/bash

# Database Export Script for IMS App
# This script provides various options for exporting the imsapp database

# Database configuration
DB_HOST="localhost"
DB_USER="root"
DB_PASS="admin"
DB_NAME="imsapp"

# Create backups directory if it doesn't exist
mkdir -p backups

# Get current timestamp
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

echo "=== IMS App Database Export Tool ==="
echo "Database: $DB_NAME"
echo "Host: $DB_HOST"
echo "User: $DB_USER"
echo ""

# Function to show usage
show_usage() {
    echo "Usage: $0 [OPTION]"
    echo ""
    echo "Options:"
    echo "  full        - Complete database export (structure + data)"
    echo "  structure   - Export only database structure (no data)"
    echo "  data        - Export only data (no structure)"
    echo "  compressed  - Export with gzip compression"
    echo "  tables      - Export specific tables (interactive)"
    echo "  help        - Show this help message"
    echo ""
    echo "Examples:"
    echo "  $0 full"
    echo "  $0 compressed"
    echo "  $0 structure"
}

# Function for full export
export_full() {
    echo "Exporting complete database..."
    mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --add-drop-database \
        --databases $DB_NAME > backups/imsapp_full_$TIMESTAMP.sql
    
    if [ $? -eq 0 ]; then
        echo "✅ Full export completed: backups/imsapp_full_$TIMESTAMP.sql"
        ls -lh backups/imsapp_full_$TIMESTAMP.sql
    else
        echo "❌ Export failed!"
        exit 1
    fi
}

# Function for structure only
export_structure() {
    echo "Exporting database structure only..."
    mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS \
        --no-data \
        --routines \
        --triggers \
        --events \
        --add-drop-database \
        --databases $DB_NAME > backups/imsapp_structure_$TIMESTAMP.sql
    
    if [ $? -eq 0 ]; then
        echo "✅ Structure export completed: backups/imsapp_structure_$TIMESTAMP.sql"
        ls -lh backups/imsapp_structure_$TIMESTAMP.sql
    else
        echo "❌ Export failed!"
        exit 1
    fi
}

# Function for data only
export_data() {
    echo "Exporting data only..."
    mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS \
        --no-create-info \
        --single-transaction \
        $DB_NAME > backups/imsapp_data_$TIMESTAMP.sql
    
    if [ $? -eq 0 ]; then
        echo "✅ Data export completed: backups/imsapp_data_$TIMESTAMP.sql"
        ls -lh backups/imsapp_data_$TIMESTAMP.sql
    else
        echo "❌ Export failed!"
        exit 1
    fi
}

# Function for compressed export
export_compressed() {
    echo "Exporting with compression..."
    mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --add-drop-database \
        --databases $DB_NAME | gzip > backups/imsapp_compressed_$TIMESTAMP.sql.gz
    
    if [ $? -eq 0 ]; then
        echo "✅ Compressed export completed: backups/imsapp_compressed_$TIMESTAMP.sql.gz"
        ls -lh backups/imsapp_compressed_$TIMESTAMP.sql.gz
    else
        echo "❌ Export failed!"
        exit 1
    fi
}

# Function for specific tables
export_tables() {
    echo "Available tables:"
    mysql -h $DB_HOST -u $DB_USER -p$DB_PASS -e "USE $DB_NAME; SHOW TABLES;" | tail -n +2
    
    echo ""
    read -p "Enter table names (space-separated): " TABLES
    
    if [ -z "$TABLES" ]; then
        echo "No tables specified. Exiting."
        exit 1
    fi
    
    echo "Exporting tables: $TABLES"
    mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS \
        --single-transaction \
        $DB_NAME $TABLES > backups/imsapp_tables_$TIMESTAMP.sql
    
    if [ $? -eq 0 ]; then
        echo "✅ Tables export completed: backups/imsapp_tables_$TIMESTAMP.sql"
        ls -lh backups/imsapp_tables_$TIMESTAMP.sql
    else
        echo "❌ Export failed!"
        exit 1
    fi
}

# Main script logic
case "$1" in
    "full")
        export_full
        ;;
    "structure")
        export_structure
        ;;
    "data")
        export_data
        ;;
    "compressed")
        export_compressed
        ;;
    "tables")
        export_tables
        ;;
    "help"|"-h"|"--help")
        show_usage
        ;;
    "")
        echo "No option specified. Use '$0 help' for usage information."
        show_usage
        ;;
    *)
        echo "Unknown option: $1"
        show_usage
        exit 1
        ;;
esac

echo ""
echo "📁 Backup location: $(pwd)/backups/"
echo "🕒 Timestamp: $TIMESTAMP"







