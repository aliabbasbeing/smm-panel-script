#!/bin/bash

# Cron Logs Installation Script
# This script helps install the cron logs database tables

echo "========================================="
echo "Cron Logs Installation Script"
echo "========================================="
echo ""

# Check if mysql command is available
if ! command -v mysql &> /dev/null; then
    echo "Error: mysql command not found. Please install MySQL client."
    exit 1
fi

# Read database credentials
echo "Please enter your database credentials:"
read -p "Database Host [localhost]: " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "Database Name: " DB_NAME
if [ -z "$DB_NAME" ]; then
    echo "Error: Database name is required"
    exit 1
fi

read -p "Database User: " DB_USER
if [ -z "$DB_USER" ]; then
    echo "Error: Database user is required"
    exit 1
fi

read -sp "Database Password: " DB_PASS
echo ""

# Verify database connection
echo ""
echo "Testing database connection..."
if ! mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME" 2>/dev/null; then
    echo "Error: Failed to connect to database. Please check your credentials."
    exit 1
fi

echo "Database connection successful!"
echo ""

# Import SQL file
SQL_FILE="database/cron-logs.sql"

if [ ! -f "$SQL_FILE" ]; then
    echo "Error: SQL file not found at $SQL_FILE"
    exit 1
fi

echo "Installing cron logs database tables..."
if mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$SQL_FILE"; then
    echo ""
    echo "========================================="
    echo "Installation completed successfully!"
    echo "========================================="
    echo ""
    echo "Next steps:"
    echo "1. Access the admin panel at: yoursite.com/cron_logs"
    echo "2. Configure settings at: yoursite.com/cron_logs/settings"
    echo "3. Review the README file: CRON_LOGS_README.md"
    echo ""
else
    echo ""
    echo "Error: Failed to install database tables"
    exit 1
fi
