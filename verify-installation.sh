#!/bin/bash

# ============================================================================
# Order Completion Feature - Installation & Verification Script
# ============================================================================
# This script helps verify the installation of the order completion tracking feature
# ============================================================================

echo "=========================================="
echo "Order Completion Feature Verification"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if database credentials are available
echo "Step 1: Database Migration"
echo "--------------------------"
echo "Please run the following SQL migration file on your database:"
echo ""
echo -e "${YELLOW}mysql -u your_username -p your_database < database/order-completion-tracking.sql${NC}"
echo ""
echo "This will add the following columns:"
echo "  - orders.completed_at (DATETIME)"
echo "  - orders.last_10_avg_time (INT)"
echo "  - services.avg_completion_time (INT)"
echo ""
read -p "Press Enter when database migration is complete..."

echo ""
echo "Step 2: Verify PHP Files"
echo "-------------------------"

# Check if all required files exist
files_to_check=(
    "app/controllers/order_completion_cron.php"
    "app/modules/api_provider/controllers/api_provider.php"
    "app/modules/order/controllers/order.php"
    "app/modules/order/views/add/get_service.php"
    "app/config/routes.php"
    "app/language/english/common_lang.php"
)

all_files_exist=true
for file in "${files_to_check[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $file exists"
    else
        echo -e "${RED}✗${NC} $file is missing"
        all_files_exist=false
    fi
done

if [ "$all_files_exist" = true ]; then
    echo -e "${GREEN}All required files are present!${NC}"
else
    echo -e "${RED}Some files are missing. Please check the installation.${NC}"
    exit 1
fi

echo ""
echo "Step 3: Syntax Check"
echo "--------------------"

# Check PHP syntax for critical files
php_files=(
    "app/controllers/order_completion_cron.php"
    "app/modules/api_provider/controllers/api_provider.php"
    "app/modules/order/controllers/order.php"
)

all_syntax_ok=true
for file in "${php_files[@]}"; do
    if php -l "$file" > /dev/null 2>&1; then
        echo -e "${GREEN}✓${NC} $file syntax OK"
    else
        echo -e "${RED}✗${NC} $file has syntax errors"
        php -l "$file"
        all_syntax_ok=false
    fi
done

if [ "$all_syntax_ok" = true ]; then
    echo -e "${GREEN}All PHP files have valid syntax!${NC}"
else
    echo -e "${RED}Some PHP files have syntax errors. Please fix them.${NC}"
    exit 1
fi

echo ""
echo "Step 4: Cron Job Setup"
echo "----------------------"
echo "Add the following cron job to run every 3 hours:"
echo ""
echo -e "${YELLOW}0 */3 * * * wget --spider -o - https://yourdomain.com/cron/completion_time >/dev/null 2>&1${NC}"
echo ""
echo "Or add it to crontab:"
echo -e "${YELLOW}crontab -e${NC}"
echo ""
echo "You can also manually test the cron job by visiting:"
echo -e "${YELLOW}https://yourdomain.com/cron/completion_time${NC}"
echo ""

echo "Step 5: Route Verification"
echo "--------------------------"
if grep -q "cron/completion_time" app/config/routes.php; then
    echo -e "${GREEN}✓${NC} Route for /cron/completion_time is registered"
else
    echo -e "${RED}✗${NC} Route for /cron/completion_time is missing"
    echo "Please add this line to app/config/routes.php:"
    echo "\$route['cron/completion_time'] = 'order_completion_cron/calculate_avg_completion';"
fi

echo ""
echo "Step 6: Language Keys"
echo "---------------------"
required_keys=(
    "Average_Completion_Time"
    "based_on_last_10_orders"
    "hour"
    "hours"
    "minute"
    "minutes"
    "second"
    "seconds"
)

all_keys_present=true
for key in "${required_keys[@]}"; do
    if grep -q "$key" app/language/english/common_lang.php; then
        echo -e "${GREEN}✓${NC} Language key: $key"
    else
        echo -e "${RED}✗${NC} Missing language key: $key"
        all_keys_present=false
    fi
done

echo ""
echo "=========================================="
echo "Installation Summary"
echo "=========================================="

if [ "$all_files_exist" = true ] && [ "$all_syntax_ok" = true ] && [ "$all_keys_present" = true ]; then
    echo -e "${GREEN}✓ All checks passed!${NC}"
    echo ""
    echo "Next Steps:"
    echo "1. Run the database migration (Step 1)"
    echo "2. Set up the cron job (Step 4)"
    echo "3. Test by visiting: https://yourdomain.com/cron/completion_time"
    echo "4. Place a test order and mark it as completed"
    echo "5. Run the cron job again to calculate averages"
    echo "6. Check the order add form to see the average completion time"
    echo ""
    echo "For detailed documentation, see:"
    echo "database/ORDER-COMPLETION-FEATURE-README.md"
else
    echo -e "${RED}✗ Some checks failed. Please review the errors above.${NC}"
    exit 1
fi

echo ""
echo "=========================================="
