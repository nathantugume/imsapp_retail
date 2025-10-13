# 🏥 Inventory Management System (IMS)

**A comprehensive web-based inventory management system for drugshops, pharmacies, and retail businesses.**

![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-4.5-purple)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Screenshots](#screenshots)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Database Structure](#database-structure)
- [Project Structure](#project-structure)
- [Recent Improvements](#recent-improvements)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)

---

## 🎯 Overview

This Inventory Management System is designed specifically for **Mini Price Hardware** but can be easily customized for any retail business. It provides comprehensive tools for managing products, orders, customers, and inventory with real-time tracking and reporting capabilities.

### Key Highlights

- 📦 **Product Management** - Track stock levels, prices, and expiry dates
- 🛒 **Order Processing** - Create and manage customer orders with invoicing
- 💰 **Payment Tracking** - Monitor payments and outstanding balances
- 📊 **Profit Analytics** - Real-time profit calculations (daily/monthly)
- 👥 **User Management** - Role-based access control (Master/User)
- 🔔 **Smart Alerts** - Expiry warnings and low stock notifications
- 📄 **Invoice Generation** - Professional PDF invoices
- 📱 **Responsive Design** - Works on desktop, tablet, and mobile

---

## ✨ Features

### Core Functionality

#### 📦 Product Management
- Add, edit, and delete products
- Track stock levels with real-time updates
- Manage product categories and brands
- Set buying and selling prices
- Monitor expiry dates for perishable items
- **NEW:** Searchable product dropdown with autocomplete
- Bulk export/print capabilities

#### 🛒 Order Management
- Create customer orders with multiple products
- **NEW:** Default customer values ("Walk-in Customer")
- **NEW:** Double submission prevention
- Real-time price calculations
- Discount and tax management
- Payment tracking (Cash, Credit Card, Bank Transfer)
- Invoice generation with professional PDF format

#### 📄 Invoice System
- Generate invoices during order creation
- Regenerate invoices for historical orders
- **NEW:** Centered business name header
- Professional PDF layout using FPDF
- Automatic file naming and organization
- Print-ready format

#### 💳 Customer Payments
- Record partial and full payments
- Track outstanding balances
- Payment history for each order
- Multiple payment methods supported

#### 📊 Analytics & Reporting
- Daily profit calculations
- Monthly profit tracking
- Profit margin analysis
- Revenue vs cost breakdown
- Stock status reports
- Sales history

#### 🔐 User Management
- Role-based access control
  - **Master/Admin** - Full system access
  - **User** - Limited access (products, orders)
- User status management (Active/Inactive)
- Secure authentication
- Profile management

#### 🔔 Smart Alerts
- **Expiry Warnings** - Products expiring within 90 days
- **Critical Expiry** - Products expiring within 30 days
- **Low Stock Alerts** - Stock below 30 units
- **Critical Stock** - Stock below 10 units
- Visual indicators and notifications

---

## 📸 Screenshots

### Dashboard
- Real-time statistics (Users, Categories, Brands, Stock)
- Profit metrics (Daily/Monthly)
- Recent sales table
- Current stock overview
- Expiry warnings panel

### Order Management
- Searchable product selection
- Real-time calculations
- Professional invoice generation
- Payment tracking

### Product Management
- DataTables with search/filter
- Export to Excel, PDF, CSV
- Print functionality
- Stock reconciliation

---

## 🛠️ Technologies Used

### Backend
- **PHP 7.4+** - Server-side programming
- **MySQL 8.0+** - Database management
- **PDO** - Database abstraction layer
- **FPDF** - PDF generation library

### Frontend
- **HTML5** - Structure
- **CSS3** - Styling
- **Bootstrap 4.5** - Responsive framework
- **jQuery 3.x** - JavaScript library
- **DataTables** - Advanced table features
- **SweetAlert2** - Beautiful alerts
- **Select2** - Enhanced dropdowns with search
- **Font Awesome 5** - Icons

### Features Libraries
- **DataTables Buttons** - Export/Print functionality
- **JSZip** - Excel export
- **pdfMake** - PDF export
- **Chart.js** - Data visualization (optional)

---

## 📥 Installation

### Prerequisites

- **Web Server** - Apache 2.4+ or Nginx
- **PHP** - Version 7.4 or higher
- **MySQL** - Version 8.0 or higher
- **Composer** - (Optional) For dependency management

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/inventory-management-system.git
cd inventory-management-system
```

### Step 2: Configure Database

1. Create a MySQL database:

```sql
CREATE DATABASE imsapp;
```

2. Import the database structure:

```bash
mysql -u root -p imsapp < database/imsapp.sql
```

Or use the provided migrations in the `migrations/` folder.

### Step 3: Configure Application

1. Edit database configuration:

```bash
nano config/config.php
```

Update the following:

```php
private const H_DBHOST = "localhost"; 
private const U_DBUSER = "root";
private const P_DBPASS = "your_password"; 
private const N_DBNAME = "imsapp";
```

2. **OPTIONAL:** Customize branding:

```bash
nano config/branding.php
```

Change business name, colors, and settings:

```php
'business_name' => 'Your Business Name',
'business_name_short' => 'Short Name',
'color_primary' => '#667eea',
// ... more settings
```

### Step 4: Set Permissions

```bash
chmod 755 Invoices/
chmod 644 config/*.php
chown -R www-data:www-data .
```

### Step 5: Access the Application

Open your browser and navigate to:

```
http://localhost/imsapp/
```

**Default Login Credentials:**
- Email: `admin@gmail.com`
- Password: `test1234`

⚠️ **Important:** Change the default password immediately after first login!

---

## ⚙️ Configuration

### Branding Customization

The system includes a centralized branding configuration system:

**File:** `config/branding.php`

**Customizable Settings:**
- Business name and tagline
- Contact information (address, phone, email)
- Logo and favicon paths
- Color scheme (6 customizable colors)
- Currency and formatting
- Alert thresholds (stock levels, expiry warnings)
- Timezone and localization

**Example:**

```php
'business_name' => 'Your Drugshop Name',
'color_primary' => '#007bff',  // Your brand color
'currency_symbol' => 'ugx',
'low_stock_threshold' => 30,
```

### Alert Thresholds

Configure when alerts are triggered:

```php
'low_stock_threshold' => 30,        // Stock warning
'critical_stock_threshold' => 10,   // Critical stock alert
'expiry_warning_days' => 90,        // Expiry warning (days)
'expiry_critical_days' => 30,       // Critical expiry (days)
```

---

## 📖 Usage

### Dashboard

Access the main dashboard to view:
- Total users, categories, brands, and stock items
- Daily and monthly profit
- Profit margins
- Recent sales
- Current stock status
- Product expiry warnings

### Managing Products

1. **Add Product**
   - Navigate to Products page
   - Click "Add Product"
   - Fill in product details (name, category, brand, prices, stock)
   - Optional: Set expiry date for perishable items
   - Click "Add Product"

2. **Update Product**
   - Click edit icon on product row
   - Modify details
   - Click "Update Product"
   - Page auto-reloads with changes

3. **Add Stock**
   - Click "Add Stock" button
   - Enter quantity to add
   - System calculates new total
   - Confirms stock update

### Creating Orders

1. Navigate to Create Order page
2. Customer info auto-fills with "Walk-in Customer" and "In-store"
3. Click "+ Add" to add product rows
4. **Search products** using the searchable dropdown
5. Enter quantities
6. System auto-calculates totals
7. Enter paid amount
8. Click "Save Order and Invoice"
9. Choose whether to generate invoice
10. PDF invoice created automatically

### Managing Payments

1. Go to Customer Payments page
2. Click "Record Payment"
3. Select order with outstanding balance
4. Enter payment amount
5. Select payment method
6. Add notes (optional)
7. Submit payment

### Generating Invoices

**For New Orders:**
- Automatically offered during order creation

**For Existing Orders:**
- Go to Orders page
- Click red "Invoice" button
- PDF generates and opens automatically

---

## 🗄️ Database Structure

### Main Tables

**users** - User accounts and authentication
- id, name, email, password, role, status, country

**products** - Product inventory
- pid, product_name, cat_id, brand_id, price, buying_price, stock, expiry_date, status

**categories** - Product categories
- cat_id, category_name, status

**brands** - Product brands
- brand_id, brand_name, status

**orders** - Customer orders
- invoice_no, customer_name, address, subtotal, gst, discount, net_total, paid, due, payment_method, order_date

**invoices** - Order line items
- id, invoice_no, product_name, order_qty, price_per_item

**customer_payments** - Payment records
- id, order_id, invoice_no, amount_paid, payment_method, payment_date, notes

**stock_reconciliations** - Stock audit records
- id, product_id, expected_qty, actual_qty, difference, reason, reconciliation_date

**migrations** - Database version tracking
- id, migration, batch, executed_at

---

## 📁 Project Structure

```
imsapp/
├── config/
│   ├── config.php          # Database configuration
│   └── branding.php        # Branding and customization
├── init/
│   ├── init.php            # Application initialization
│   └── dbcon.php           # Database connection
├── common/
│   ├── navbar.php          # Navigation bar
│   ├── top.php             # Page header
│   ├── footer.php          # Page footer
│   └── breadcrumb.php      # Breadcrumb navigation
├── dashboard/
│   └── Dashboard.php       # Dashboard logic
├── products/
│   ├── Product.php         # Product class
│   ├── add.php             # Add product
│   ├── update.php          # Update product
│   ├── delete.php          # Delete product
│   └── fetch.php           # Fetch products
├── orders/
│   ├── Order.php           # Order class
│   ├── add.php             # Create order
│   ├── invoice.php         # Generate invoice (new orders)
│   ├── generate_pdf.php    # Regenerate invoice (existing)
│   └── index.php           # List orders
├── category/
│   ├── Category.php        # Category class
│   └── ...                 # CRUD operations
├── brands/
│   ├── Brand.php           # Brand class
│   └── ...                 # CRUD operations
├── users/
│   ├── User.php            # User class
│   ├── login.php           # Authentication
│   └── ...                 # User management
├── payments/
│   ├── CustomerPayment.php # Payment class
│   └── ...                 # Payment operations
├── stock/
│   └── StockReconciliation.php  # Stock audit
├── functions/
│   ├── function.php        # Helper functions
│   └── profit_calculator.php    # Profit calculations
├── css/
│   ├── bootstrap.min.css   # Bootstrap framework
│   ├── custom.css          # Custom styling
│   └── ...                 # Other styles
├── js/
│   ├── product.js          # Product functionality
│   ├── order.js            # Order functionality
│   ├── category.js         # Category functionality
│   └── ...                 # Other scripts
├── Invoices/               # Generated PDF invoices
├── fpdf/                   # FPDF library
├── migrations/             # Database migrations
├── index.php               # Dashboard
├── login.php               # Login page
├── product.php             # Products page
├── order.php               # Orders page
├── create-order.php        # Create order page
└── README.md               # This file
```

---

## 🚀 Recent Improvements

### October 2025 Updates

#### Dashboard Enhancements
- ✅ Fixed Current Stock table styling to match Recent Sales
- ✅ Improved table responsiveness (removed fixed widths)
- ✅ Added proper thead structure with light styling
- ✅ Mobile-responsive table wrappers

#### Order Management
- ✅ **Default Values** - Customer name and address auto-filled
- ✅ **Double Submission Prevention** - Prevents accidental duplicate orders
- ✅ **Searchable Product Dropdown** - Select2 autocomplete for faster product selection
- ✅ Button state management during order processing

#### Invoice Generation
- ✅ **Centered Business Name** - Professional header layout
- ✅ **Fixed Invoice Regeneration** - Works for all orders (even without items)
- ✅ **Matched Formatting** - Both invoice files produce identical output
- ✅ **Clean JSON Response** - Suppressed PHP deprecation warnings
- ✅ **Improved Error Handling** - Graceful handling of missing data

#### User Experience
- ✅ **Auto Page Reload** - After successful edits (products, categories, brands, users)
- ✅ **Professional PDF Button** - Improved button styling in orders table
- ✅ **Better Icons** - Updated to Font Awesome 5
- ✅ **Tooltips** - Helpful hints on buttons

#### Code Quality
- ✅ Explicit SQL column selection (no ambiguity)
- ✅ LEFT JOIN for proper data retrieval
- ✅ Output buffering for clean API responses
- ✅ Better error handling throughout

---

## 🎨 Features in Detail

### 1. Product Management

**Capabilities:**
- Add products with categories and brands
- Set buying price and selling price
- Track stock quantities
- Set expiry dates
- View profit per product
- Export to Excel, PDF, CSV
- Search and filter products
- **Autocomplete search** in order creation

**Stock Alerts:**
- 🟢 **In Stock** - More than 30 units
- 🟡 **Low Stock** - 1-30 units
- 🔴 **Out of Stock** - 0 units

### 2. Order Processing

**Features:**
- Multi-product orders
- Real-time price calculation
- Discount application
- Partial payment support
- Due amount tracking
- Payment method selection
- **Searchable product selection** with Select2
- **Prevents double submission**

**Workflow:**
1. Enter customer details (defaults provided)
2. Add products using searchable dropdown
3. System calculates totals automatically
4. Enter payment details
5. Generate invoice
6. PDF saved to Invoices folder

### 3. Invoice Generation

**Two Methods:**

**During Order Creation:**
- `orders/invoice.php`
- Generates from form POST data
- Immediate PDF creation
- Browser redirects to PDF

**Regenerate Existing:**
- `orders/generate_pdf.php`
- Fetches from database
- Handles missing invoice items
- Returns JSON response

**Invoice Features:**
- Professional layout
- Business branding
- Customer details
- Itemized product list
- All totals and calculations
- Payment information
- Signature section

### 4. Payment Tracking

**Features:**
- Record payments for orders
- Track outstanding balances
- Payment history per customer
- Multiple payment methods
- Optional notes for each payment
- Total outstanding balance display

### 5. Stock Reconciliation

**Capabilities:**
- Compare expected vs actual stock
- Record discrepancies
- Track reasons for differences
- Audit trail for stock movements
- Periodic stock checks

### 6. User Roles

**Master/Admin:**
- Full access to all features
- User management
- Category and brand management
- Database migrations
- System settings
- View all analytics

**User:**
- Product viewing
- Order creation
- Stock checking
- Customer payments
- Limited dashboard access

---

## 💻 System Requirements

### Minimum Requirements
- **PHP:** 7.4 or higher
- **MySQL:** 5.7 or higher (8.0+ recommended)
- **Web Server:** Apache 2.4+ or Nginx 1.18+
- **RAM:** 512 MB minimum
- **Disk Space:** 100 MB minimum
- **Browser:** Modern browser (Chrome, Firefox, Safari, Edge)

### Recommended Requirements
- **PHP:** 8.0+
- **MySQL:** 8.0+
- **RAM:** 1 GB or more
- **SSD Storage:** For better performance
- **HTTPS:** SSL certificate for production

---

## 🔧 Configuration Guide

### Database Setup

1. **Edit:** `config/config.php`

```php
private const H_DBHOST = "localhost"; 
private const U_DBUSER = "your_username";
private const P_DBPASS = "your_password"; 
private const N_DBNAME = "imsapp";
```

### Branding Customization

1. **Edit:** `config/branding.php`

```php
// Business Information
'business_name' => 'Your Business Name',
'business_tagline' => 'Your Tagline',
'business_phone' => '+256 XXX XXXXXX',
'business_email' => 'info@yourbusiness.com',

// Color Scheme
'color_primary' => '#667eea',
'color_secondary' => '#764ba2',

// Currency
'currency' => 'UGX',
'currency_symbol' => 'ugx',

// Alert Thresholds
'low_stock_threshold' => 30,
'expiry_warning_days' => 90,
```

2. **Add Your Logo** (Optional):

```bash
# Upload logo files to images/ folder
cp your-logo.png images/logo.png
cp your-logo-white.png images/logo-white.png
cp your-favicon.ico images/favicon.ico
```

### File Permissions

```bash
# Set proper permissions
chmod 755 Invoices/
chmod 644 config/*.php
chmod 755 images/
chown -R www-data:www-data .
```

---

## 🎓 Usage Guide

### First Time Setup

1. **Login with default credentials**
   - Email: admin@gmail.com
   - Password: test1234

2. **Change default password**
   - Go to Profile
   - Update password

3. **Add Categories**
   - Navigate to Categories
   - Add your product categories

4. **Add Brands**
   - Navigate to Brands
   - Add product brands

5. **Add Products**
   - Navigate to Products
   - Add your inventory items

6. **Create Users** (Optional)
   - Navigate to Users (Admin only)
   - Add additional users

### Daily Operations

**Morning:**
- Check expiry warnings
- Review low stock alerts
- Check yesterday's profit

**During Day:**
- Create orders as customers purchase
- Record payments
- Add stock when deliveries arrive

**Evening:**
- Review daily sales
- Check outstanding payments
- Generate reports

---

## 🔐 Security Features

- Password hashing (bcrypt)
- Session management
- SQL injection prevention (PDO prepared statements)
- XSS protection (htmlspecialchars)
- Role-based access control
- CSRF protection (via form tokens)
- Secure authentication

---

## 📊 API Endpoints

### Products
- `GET products/fetch.php` - Fetch all products
- `POST products/add.php` - Add new product
- `POST products/update.php` - Update product
- `POST products/delete.php` - Delete product

### Orders
- `POST orders/add.php` - Create order
- `POST orders/invoice.php` - Generate invoice
- `POST orders/generate_pdf.php` - Regenerate invoice
- `GET orders/index.php` - List orders

### Payments
- `POST payments/record-payment.php` - Record payment
- `GET payments/get-payment-history.php` - Payment history

---

## 🐛 Troubleshooting

### Common Issues

**1. Login Not Working**
- Check database connection in config/config.php
- Verify users table has data
- Check PHP session settings

**2. Invoice Not Generating**
- Ensure FPDF library exists in fpdf/ folder
- Check Invoices/ folder permissions (755)
- Verify order has data in database

**3. Product Dropdown Empty**
- Check products table has data
- Verify database connection
- Check browser console for errors

**4. DataTables Not Loading**
- Check jQuery loaded before DataTables
- Verify all JS files included
- Clear browser cache

**5. Images/Icons Not Loading**
- Check file paths in HTML
- Verify Font Awesome CSS loaded
- Check web server configuration

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-12 coding style for PHP
- Use meaningful variable and function names
- Comment complex logic
- Test thoroughly before submitting
- Update documentation for new features

---

## 📝 Changelog

### Version 2.0 (October 2025)

**Added:**
- Searchable product dropdown with Select2
- Default customer values in order creation
- Double submission prevention
- Auto page reload after edits
- Centered invoice headers
- Branding configuration system

**Fixed:**
- Invoice regeneration for orders without items
- Table responsiveness on dashboard
- SQL query ambiguity (explicit column selection)
- PHP deprecation warnings in PDF generation
- JSON response formatting

**Improved:**
- Professional button styling
- Font Awesome 5 icons
- Error handling
- Code organization
- User experience

### Version 1.0 (August 2025)

- Initial release
- Core functionality implemented
- User authentication
- Product management
- Order processing
- Invoice generation
- Payment tracking

---

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

## 👨‍💻 Developer

**Nathan Tugume**
- Email: nathantugumee@gmail.com
- Location: Kampala, Uganda

---

## 🙏 Acknowledgments

- **Bootstrap** - Responsive framework
- **jQuery** - JavaScript library
- **DataTables** - Table enhancement
- **FPDF** - PDF generation
- **SweetAlert2** - Beautiful alerts
- **Select2** - Enhanced dropdowns
- **Font Awesome** - Icon library

---

## 📞 Support

For support, email nathantugumee@gmail.com or open an issue in the GitHub repository.

---

## 🚀 Future Roadmap

### Planned Features

- [ ] Chart.js dashboard visualizations
- [ ] Advanced filtering and search
- [ ] Product grid view
- [ ] Bulk operations
- [ ] Email invoice functionality
- [ ] SMS notifications
- [ ] Barcode scanning
- [ ] Multi-currency support
- [ ] Advanced reporting
- [ ] API for mobile app
- [ ] Progressive Web App (PWA) support
- [ ] Multi-language support
- [ ] Dark mode

---

## 📸 Screenshots

_(Add screenshots here)_

### Login Page
![Login](screenshots/login.png)

### Dashboard
![Dashboard](screenshots/dashboard.png)

### Products
![Products](screenshots/products.png)

### Create Order
![Create Order](screenshots/create-order.png)

### Invoice Sample
![Invoice](screenshots/invoice.png)

---

## ⭐ Star History

If you find this project useful, please consider giving it a star! ⭐

---

**Built with ❤️ for Mini Price Hardware**

**Last Updated:** October 8, 2025

