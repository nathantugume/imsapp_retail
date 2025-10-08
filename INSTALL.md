# 📦 Installation Guide

Complete step-by-step installation instructions for the Inventory Management System.

---

## 🚀 Quick Start

```bash
# 1. Clone repository
git clone https://github.com/yourusername/inventory-management-system.git
cd inventory-management-system

# 2. Create database
mysql -u root -p -e "CREATE DATABASE imsapp"

# 3. Import schema
mysql -u root -p imsapp < database/schema.sql

# 4. Configure database
cp config/config.example.php config/config.php
nano config/config.php  # Edit with your credentials

# 5. Set permissions
chmod 755 Invoices/
chmod 644 config/*.php

# 6. Access application
http://localhost/imsapp/
```

**Default Login:**
- Email: admin@gmail.com
- Password: test1234

---

## 📋 Detailed Installation

### Step 1: System Requirements

Ensure you have:
- ✅ PHP 7.4+ (8.0+ recommended)
- ✅ MySQL 5.7+ (8.0+ recommended)
- ✅ Apache/Nginx web server
- ✅ Git (for cloning)

Check versions:
```bash
php -v
mysql --version
apache2 -v  # or nginx -v
```

### Step 2: Clone Repository

```bash
# Navigate to web root
cd /var/www/html/

# Clone repository
git clone https://github.com/yourusername/inventory-management-system.git imsapp

# Enter directory
cd imsapp
```

### Step 3: Database Setup

#### Create Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE imsapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

#### Import Schema

```bash
mysql -u root -p imsapp < database/schema.sql
```

#### Create Default Admin User

```sql
USE imsapp;

INSERT INTO users (name, email, password, role, status, country) 
VALUES ('Admin', 'admin@gmail.com', 'test1234', 'Master', 1, 'Uganda');
```

⚠️ **Note:** Change password after first login!

### Step 4: Configure Application

#### Database Configuration

```bash
cp config/config.example.php config/config.php
nano config/config.php
```

Update with your credentials:

```php
private const H_DBHOST = "localhost"; 
private const U_DBUSER = "your_mysql_user";
private const P_DBPASS = "your_mysql_password"; 
private const N_DBNAME = "imsapp";
```

#### Branding Configuration (Optional)

```bash
nano config/branding.php
```

Customize:
- Business name
- Colors
- Contact information
- Alert thresholds

### Step 5: Set File Permissions

```bash
# Set directory permissions
chmod 755 Invoices/
chmod 755 images/
chmod 755 css/
chmod 755 js/

# Set file permissions
chmod 644 *.php
chmod 644 config/*.php

# Set ownership (for Apache)
sudo chown -R www-data:www-data .

# OR for Nginx
sudo chown -R nginx:nginx .
```

### Step 6: Web Server Configuration

#### Apache Configuration

Create virtual host:

```bash
sudo nano /etc/apache2/sites-available/imsapp.conf
```

```apache
<VirtualHost *:80>
    ServerName imsapp.local
    DocumentRoot /var/www/html/imsapp
    
    <Directory /var/www/html/imsapp>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/imsapp_error.log
    CustomLog ${APACHE_LOG_DIR}/imsapp_access.log combined
</VirtualHost>
```

Enable site:

```bash
sudo a2ensite imsapp.conf
sudo systemctl reload apache2
```

#### Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/imsapp
```

```nginx
server {
    listen 80;
    server_name imsapp.local;
    root /var/www/html/imsapp;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/imsapp /etc/nginx/sites-enabled/
sudo systemctl reload nginx
```

### Step 7: Test Installation

1. **Access application:**
   ```
   http://localhost/imsapp/
   ```

2. **Login with default credentials:**
   - Email: admin@gmail.com
   - Password: test1234

3. **Change password immediately**

4. **Add sample data:**
   - Add categories
   - Add brands
   - Add products

---

## 🔧 Post-Installation

### Security Hardening

1. **Change default password:**
   - Login → Profile → Change Password

2. **Update database password:**
   ```bash
   nano config/config.php
   # Use strong password
   ```

3. **Secure configuration files:**
   ```bash
   chmod 600 config/config.php
   ```

4. **Enable HTTPS** (Production):
   ```bash
   sudo certbot --apache  # or --nginx
   ```

### Performance Optimization

1. **Enable PHP OPcache:**
   ```bash
   sudo nano /etc/php/8.0/apache2/php.ini
   # opcache.enable=1
   sudo systemctl restart apache2
   ```

2. **Enable MySQL query cache:**
   ```bash
   sudo nano /etc/mysql/my.cnf
   # Add query cache settings
   ```

3. **Enable Gzip compression:**
   - Configure in Apache/Nginx

### Backup Setup

1. **Create backup script:**
   ```bash
   nano backup.sh
   ```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u root -p imsapp > backups/imsapp_$DATE.sql
tar -czf backups/files_$DATE.tar.gz . --exclude=backups
```

2. **Schedule backups:**
   ```bash
   crontab -e
   # Add: 0 2 * * * /path/to/backup.sh
   ```

---

## 🧪 Testing

### Test Checklist

- [ ] Can access login page
- [ ] Can login with default credentials
- [ ] Dashboard loads correctly
- [ ] Can add product
- [ ] Can create category
- [ ] Can create brand
- [ ] Can create order
- [ ] Invoice generates correctly
- [ ] Can record payment
- [ ] Profit calculations work
- [ ] Export functions work (Excel, PDF, CSV)
- [ ] Stock reconciliation works

### Sample Data

For testing, add sample data:

```sql
-- Categories
INSERT INTO categories (category_name, status) VALUES 
('Medication', 1), ('Cosmetics', 1), ('Supplements', 1);

-- Brands
INSERT INTO brands (brand_name, status) VALUES 
('Generic', 1), ('Pharmacy Brand', 1);

-- Products
INSERT INTO products (product_name, cat_id, brand_id, price, buying_price, stock, status) VALUES 
('Paracetamol 500mg', 1, 1, 500, 300, 100, 1),
('Amoxicillin 250mg', 1, 1, 1000, 700, 50, 1);
```

---

## 🐛 Troubleshooting

### Database Connection Issues

**Error:** "Connection failed"

**Solutions:**
1. Check MySQL is running:
   ```bash
   sudo systemctl status mysql
   ```

2. Verify credentials in config/config.php

3. Test connection:
   ```bash
   mysql -u root -p imsapp
   ```

4. Check MySQL user permissions:
   ```sql
   GRANT ALL PRIVILEGES ON imsapp.* TO 'root'@'localhost';
   FLUSH PRIVILEGES;
   ```

### Invoice Generation Fails

**Error:** PDF not generating

**Solutions:**
1. Check Invoices folder exists and is writable:
   ```bash
   mkdir -p Invoices
   chmod 755 Invoices/
   ```

2. Verify FPDF library:
   ```bash
   ls -la fpdf/fpdf.php
   ```

3. Check PHP error logs:
   ```bash
   tail -f /var/log/apache2/error.log
   ```

### Page Not Loading

**Error:** 404 or 500 errors

**Solutions:**
1. Check file permissions:
   ```bash
   chmod 644 *.php
   ```

2. Verify .htaccess (if using Apache):
   ```bash
   cat .htaccess
   ```

3. Check web server error logs

4. Verify PHP modules:
   ```bash
   php -m | grep pdo
   php -m | grep mysql
   ```

### Session Issues

**Error:** Not staying logged in

**Solutions:**
1. Check session directory writable:
   ```bash
   chmod 777 /var/lib/php/sessions
   ```

2. Verify session settings in php.ini:
   ```ini
   session.save_path = "/var/lib/php/sessions"
   ```

3. Restart web server:
   ```bash
   sudo systemctl restart apache2
   ```

---

## 📞 Support

If you encounter issues:

1. Check this installation guide
2. Review README.md
3. Check GitHub Issues
4. Contact: nathantugumee@gmail.com

---

## 🎓 Next Steps

After installation:

1. ✅ Change default password
2. ✅ Add your business information (config/branding.php)
3. ✅ Add your logo (images/ folder)
4. ✅ Create categories for your products
5. ✅ Add brands
6. ✅ Import your product catalog
7. ✅ Create additional user accounts
8. ✅ Test order creation
9. ✅ Configure backup schedule
10. ✅ Enable HTTPS for production

---

**Last Updated:** October 8, 2025

