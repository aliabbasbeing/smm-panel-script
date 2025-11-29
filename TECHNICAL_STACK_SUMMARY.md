# SMM Panel - Complete Technical Stack Summary

## Executive Summary
This SMM (Social Media Marketing) panel is a comprehensive web application built on CodeIgniter 3.0.0 with HMVC architecture pattern. The system manages social media marketing services, orders, payments, and includes automated cron jobs for order processing.

---

## 1. Backend Stack

### Core Framework
- **Framework**: CodeIgniter 3.0.0
- **Architecture**: HMVC (Hierarchical Model-View-Controller)
  - HMVC Extension: MX (Modular Extensions)
  - Location: `app/third_party/MX/`
- **Language**: PHP
- **Pattern**: MVC with modular structure

### PHP Composer Dependencies
Located in `app/composer.json`:
```json
{
    "EHER/OAuth": "^1.0",
    "paypal/rest-api-sdk-php": "*",
    "matthiasmullie/minify": "^1.3",
    "lazyjsonmapper/lazyjsonmapper": "^1.6",
    "php-curl-class/php-curl-class": "@dev",
    "jbzoo/image": "3.x-dev",
    "phpmailer/phpmailer": "^6.0"
}
```

### Key Backend Libraries
1. **OAuth** (v1.0+) - OAuth authentication for social platforms
2. **PayPal REST API SDK** - PayPal payment integration
3. **Matthiasmullie/Minify** (v1.3+) - CSS/JS minification
4. **LazyJsonMapper** (v1.6+) - JSON data mapping
5. **PHP-Curl-Class** (dev) - HTTP requests handling
6. **JBZoo/Image** (3.x-dev) - Image manipulation
7. **PHPMailer** (v6.0+) - Email functionality
8. **TwitterOAuth** - Twitter API integration (custom library)
9. **Facebook OAuth** - Facebook authentication (custom library)
10. **SMM APIs** - Custom SMM provider APIs integration

### CodeIgniter Custom Extensions
- **MY_Model** - Extended model class
- **MY_Loader** - Custom loader for HMVC
- **MY_Router** - Custom routing logic
- **Template Library** - View template management
- **Smm_apis Library** - SMM provider API handler

---

## 2. Database Stack

### Database Engine
- **Primary Engine**: MySQL with InnoDB
- **Secondary Engine**: MyISAM (for some tables)
- **Charset**: utf8mb4
- **Collation**: utf8mb4_unicode_ci / utf8mb4_general_ci

### Database Configuration
- **Host**: localhost
- **Timezone**: Asia/Karachi
- **Encryption**: AES encryption with custom key

### Database Features
- Multi-currency support
- Currency rate updates
- Email marketing database
- WhatsApp marketing database
- Order completion tracking
- Payment gateway integrations (JazzCash, Easypaisa, Faysal Bank, SadaPay, PerfectMoney)

---

## 3. Frontend Stack

### CSS Frameworks & UI Components

#### Bootstrap
- **Version**: 4.1.0
- **Location**: `assets/css/core.css`, `assets/js/vendors/bootstrap.bundle.min.js`
- **License**: MIT
- **Features**: Grid system, responsive utilities, components

#### Tabler Dashboard UI
- **Type**: Dashboard UI framework built on Bootstrap 4.1.0
- **Location**: `assets/css/tabler.css`, `assets/css/tabler.rtl.css`
- **Features**: 
  - Pre-built dashboard components
  - RTL (Right-to-Left) support
  - Custom color scheme
  - Responsive design

### Custom CSS Files
1. **core.css** - Core Bootstrap styles
2. **tabler.css** - Tabler dashboard framework
3. **dashboard.rtl.css** - RTL dashboard styles
4. **new-style.css** - Custom panel styles
5. **layout.css** - Layout specific styles
6. **general_page.css** - General page styles
7. **slide.css** - Slider/carousel styles
8. **footer.css** - Footer styles
9. **maintenace.css** - Maintenance page styles
10. **keyframes.css** - CSS animations
11. **font-awesome.min.css** - Icon fonts

---

## 4. JavaScript Stack

### Core JavaScript Libraries

#### jQuery
- **Version**: 3.2.1
- **Files**: 
  - `jquery-3.2.1.min.js` (full version)
  - `jquery-3.2.1.slim.min.js` (slim version)

#### Chart.js
- **File**: `chart.bundle.min.js`
- **Purpose**: Data visualization and charts

#### Selectize.js
- **Version**: 0.12.4
- **Purpose**: Enhanced select boxes and tagging

#### jQuery Sparkline
- **File**: `jquery.sparkline.min.js`
- **Purpose**: Inline charts and graphs

#### Circle Progress
- **File**: `circle-progress.min.js`
- **Purpose**: Circular progress indicators

#### jQuery Vector Map
- **Files**:
  - `jquery-jvectormap-2.0.3.min.js`
  - `jquery-jvectormap-world-mill.js`
  - `jquery-jvectormap-de-merc.js`
- **Purpose**: Interactive vector maps

#### jQuery Table Sorter
- **File**: `jquery.tablesorter.min.js`
- **Purpose**: Table sorting functionality

### Custom JavaScript Files
1. **general.js** - General utility functions
2. **process.js** - Form processing logic
3. **external-scripts.js** - External script loader
4. **chart_template.js** - Chart templates
5. **dashboard.js** - Dashboard specific scripts
6. **core.js** - Core application logic
7. **slide.js** - Slider functionality
8. **service-management.js** - Service management features

### Utility Libraries
- **RequireJS** (`require.min.js`) - Module loader

---

## 5. Plugins & Third-Party Integrations

### Editor & Content
1. **TinyMCE** - Rich text editor
   - Location: `assets/plugins/tinymce/`
   - Features: Full-featured WYSIWYG editor

2. **CodeMirror** - Code editor
   - Location: `assets/plugins/codemirror/`
   - Purpose: Code editing with syntax highlighting

### UI Enhancement Plugins
1. **AOS (Animate On Scroll)**
   - Location: `assets/plugins/aos/`
   - Purpose: Scroll animations

2. **Particles.js**
   - Location: `assets/plugins/particles-js/`
   - Purpose: Particle effects background

3. **Emoji Picker**
   - Location: `assets/plugins/emoji-picker/`
   - Purpose: Emoji selection interface

4. **Emoji Support**
   - Location: `assets/plugins/emoji/`

### Form & Input Plugins
1. **Bootstrap Datepicker**
   - Location: `assets/plugins/bootstrap-datepicker/`
   - Purpose: Date selection

2. **Input Mask**
   - Location: `assets/plugins/input-mask/`
   - Purpose: Input formatting

3. **jQuery Upload**
   - Location: `assets/plugins/jquery-upload/`
   - Purpose: File upload functionality

### Visual & Media
1. **Font Awesome**
   - Location: `assets/plugins/font-awesome/`
   - Purpose: Icon library

2. **Flag Icons**
   - Location: `assets/plugins/flags/`
   - Purpose: Country flags

3. **Charts C3**
   - Location: `assets/plugins/charts-c3/`
   - Purpose: Alternative charting library

### Notifications
1. **jQuery Toast**
   - Location: `assets/plugins/jquery-toast/`
   - Purpose: Toast notifications

---

## 6. Module Structure (HMVC Architecture)

### Total Modules: 42 Active Modules

Located in `app/modules/`, each module follows HMVC pattern with:
- Controllers
- Models  
- Views
- Language files
- Config files

### Core Business Modules
1. **order** - Order management
2. **services** - Service catalog management
3. **category** - Service categorization
4. **payments** - Payment gateway integrations
5. **add_funds** - Account funding
6. **transactions** - Transaction history
7. **balance_logs** - Balance tracking
8. **manual_funds** - Manual payment processing

### User Management Modules
1. **auth** - Authentication & authorization
2. **users** - User account management
3. **client** - Client management
4. **profile** - User profiles
5. **user_logs** - User activity logging
6. **user_mail_logs** - Email tracking
7. **user_block_ip** - IP blocking

### Service Provider Modules
1. **api_provider** - API provider management
2. **api_access** - API access control
3. **api** - API endpoints & documentation
4. **childpanel** - Child panel management

### Marketing & Communication Modules
1. **email** - Email system
2. **email_marketing** - Email campaigns
3. **whatsapp_marketing** - WhatsApp campaigns
4. **subscribers** - Subscriber management
5. **subscriptions** - Subscription handling
6. **notification** - Notification system

### Order Processing Modules
1. **dripfeed** - Drip-feed order processing
2. **refill** - Order refill management

### Financial Modules
1. **payments_bonuses** - Bonus system
2. **affiliate** - Affiliate program
3. **currencies** - Multi-currency support

### Content & Configuration Modules
1. **news** - News/announcements
2. **faqs** - FAQ management
3. **custom_page** - Custom pages
4. **blocks** - Content blocks
5. **language** - Multi-language support
6. **setting** - System settings
7. **maintenance** - Maintenance mode

### Support Modules
1. **tickets** - Support ticket system

### Additional Modules
1. **home** - Homepage
2. **statistics** - Analytics & statistics
3. **file_manager** - File management
4. **module** - Module management

---

## 7. Cron Job Structure

### Cron Endpoints
Configuration file: `cron-jobs.txt`

1. **Order Processing Cron**
   - Endpoint: `https://domain.com/cron/order`
   - Controller: `app/controllers/order_completion_cron.php`
   - Purpose: Process pending orders

2. **Status Update Cron**
   - Endpoint: `https://domain.com/cron/status`
   - Purpose: Update order statuses

3. **Completion Time Cron**
   - Endpoint: `https://domain.com/cron/completion_time`
   - Purpose: Track order completion times

### Cron Controllers
Located in `app/controllers/`:
1. **order_completion_cron.php** - Order completion tracking
2. **Email_cron.php** - Email processing queue
3. **Imap_cron.php** - IMAP email checking
4. **whatsapp_cron.php** - WhatsApp message processing
5. **GoogleAuth.php** - Google authentication cron

---

## 8. Payment Gateway Integrations

### Payment Files (Root Level)
Located at project root:
1. **jazzcash-payment.php** - JazzCash payment integration
2. **easypaisa-payment.php** - Easypaisa payment gateway
3. **faysalbank-payment.php** - Faysal Bank integration
4. **sadapay-payment.php** - SadaPay integration

### Payment Module
- **Module**: `app/modules/payments/`
- **Features**: Multiple payment gateway support

### Supported Payment Methods (Database)
- JazzCash
- Easypaisa
- Faysal Bank
- SadaPay
- PayPal (via REST API SDK)
- PerfectMoney
- Cashmaal
- Other custom gateways

---

## 9. Helper Functions

Located in `app/helpers/`:
1. **common_helper.php** - Common utility functions
2. **email_helper.php** - Email functions
3. **balance_logs_helper.php** - Balance tracking
4. **currency_helper.php** - Currency conversion
5. **smmapis_helper.php** - SMM API helpers
6. **language_helper.php** - Multi-language support
7. **file_manager_helper.php** - File operations

---

## 10. Theme System

### Available Themes
Located in `themes/`:
1. **pergo** - Pergo theme
2. **regular** - Regular theme

### Theme Structure (Each Theme Contains)
- Controllers
- Models
- Views
- Language files
- Config files

### Theme Features
- Sign in/Sign up pages
- Password reset
- Activation pages
- Header/Footer blocks
- Custom layouts

---

## 11. WhatsApp Integration

### WhatsApp Files (Root Level)
1. **whatsapp_api_private_pro.php** - Private WhatsApp API
2. **whatsapp_listed_updated.php** - WhatsApp listing updates

### WhatsApp Module
- **Module**: `app/modules/whatsapp_marketing/`
- **Cron**: `app/controllers/whatsapp_cron.php`

---

## 12. API Structure

### API Module Features
- **Documentation**: Auto-generated API docs
- **Endpoints**: 
  - Order status
  - Service listing
  - Balance check
  - Order placement
  - Multiple order status

### API Access Control
- API key authentication
- Rate limiting support
- User-specific API keys

---

## 13. Security Features

### Encryption
- Custom encryption key
- Password hashing (via MX/PasswordHash.php)
- Secure session management

### Input Sanitization
- XSS protection
- SQL injection prevention
- Input filtering and validation

### Access Control
- Role-based permissions (Admin, Supporter, User)
- IP blocking system
- Session management

---

## 14. Internationalization

### Multi-Language Support
- **Module**: `app/modules/language/`
- **System Languages**: Located in `app/language/`
- **Default**: English
- **RTL Support**: Yes (via dashboard.rtl.css and tabler.rtl.css)

---

## 15. File Upload & Management

### File Manager
- **Module**: `app/modules/file_manager/`
- **Upload Plugin**: jQuery Upload
- **Image Processing**: JBZoo/Image library
- **Storage**: `assets/uploads/user[hash]/`

---

## 16. Development & Production Setup

### Environment Configuration
- **Environment**: Production (configurable)
- **Error Reporting**: Disabled in production
- **Max Execution Time**: 300 seconds
- **Timezone**: Asia/Karachi

### Directory Structure
```
/
├── app/                    # Application directory
│   ├── controllers/       # Cron controllers
│   ├── modules/           # HMVC modules (42 modules)
│   ├── core/              # CodeIgniter core extensions
│   ├── helpers/           # Helper functions
│   ├── libraries/         # Custom libraries
│   ├── third_party/       # Third-party extensions (MX)
│   ├── views/             # Views
│   ├── config/            # Configuration files
│   └── composer.json      # PHP dependencies
├── assets/                # Static assets
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   ├── plugins/          # Third-party plugins
│   └── uploads/          # User uploads
├── database/             # Database SQL files
├── themes/               # Frontend themes
├── install/              # Installation directory
└── *.php                 # Root payment gateway files
```

---

## 17. Additional Technologies & Tools

### Build Tools
- **Minification**: Matthiasmullie/Minify for CSS/JS

### HTTP Client
- **PHP-Curl-Class**: Advanced cURL wrapper

### Email System
- **PHPMailer 6.0+**: Email sending
- **IMAP Support**: Email receiving (via Imap_cron)

### Image Processing
- **JBZoo/Image**: Image manipulation and optimization

### Session Management
- **Location**: `app/sessions/`
- **Type**: File-based sessions

### Caching
- **Location**: `app/cache/`
- **Type**: File-based caching

### Logging
- **Location**: `app/logs/`
- **Error Logging**: CodeIgniter logging system

---

## 18. Controller Patterns

### Standard Controller Structure
```php
class module_name extends MX_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(get_class($this).'_model', 'model');
    }
}
```

### Common Controller Methods
- `index()` - Default method
- CRUD operations (create, read, update, delete)
- AJAX handlers
- API endpoints

---

## 19. Model Patterns

### Model Naming Convention
- Pattern: `{ModuleName}_model.php`
- Example: `Order_model.php`, `Services_model.php`

### Location
- Each model in its respective module: `app/modules/{module}/models/`

---

## 20. View Rendering

### Template System
- **Library**: Custom Template library
- **Layouts**: Multiple layout support (landing_page, default, etc.)
- **Method**: `$this->template->build('view_name', $data)`

---

## Summary of Key Technologies

### Backend
✅ PHP  
✅ CodeIgniter 3.0.0  
✅ HMVC Pattern (MX)  
✅ MySQL (InnoDB/MyISAM)  
✅ Composer Dependencies (7 packages)  

### Frontend
✅ Bootstrap 4.1.0  
✅ Tabler Dashboard UI  
✅ jQuery 3.2.1  
✅ Chart.js  
✅ Selectize.js 0.12.4  
✅ TinyMCE  
✅ Font Awesome  

### Infrastructure
✅ 42 HMVC Modules  
✅ 5 Cron Jobs  
✅ Multiple Payment Gateways  
✅ Multi-currency Support  
✅ Multi-language Support  
✅ RTL Support  
✅ WhatsApp Integration  
✅ Email Marketing  
✅ API System  

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-18  
**Analysis Based On**: Repository code structure, file analysis, and configuration inspection
