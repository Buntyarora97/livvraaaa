# LIVVRA - Hostinger Deployment Guide

## Hostinger Premium Hosting par LIVVRA Deploy karna

Ye guide aapko step-by-step batayegi ki kaise LIVVRA e-commerce website ko Hostinger Premium hosting par deploy karein.

---

## Requirements (Zaruri Cheezein)

- Hostinger Premium/Business Hosting Account
- FTP Client (FileZilla recommended) ya Hostinger File Manager
- Domain name (Hostinger se ya bahar se)

---

## Step 1: Hostinger Control Panel mein Login karein

1. **Hostinger.in** par jaayein
2. Apne account mein **Login** karein
3. **hPanel** (Control Panel) kholein
4. Apni hosting select karein

---

## Step 2: Database Create karein (Agar MySQL use karna hai)

> **Note:** Ye project SQLite database use karta hai jo already include hai. Agar aap SQLite continue rakhna chahte ho, toh ye step skip karein.

### SQLite (Default - Recommended for small sites):
- Kuch karne ki zaroorat nahi, database already `database/livvra.db` mein hai

### MySQL (Optional - For larger sites):
1. hPanel mein **Databases** > **MySQL Databases** jaayein
2. New database create karein:
   - Database Name: `livvra_db`
   - Username create karein
   - Password set karein
3. Database credentials note karein
4. `database/schema.sql` file ko **phpMyAdmin** mein import karein

---

## Step 3: Files Upload karein

### Method 1: File Manager (Easy)

1. hPanel mein **Files** > **File Manager** jaayein
2. `public_html` folder kholein
3. Saari files upload karein:
   - `admin/` folder
   - `database/` folder
   - `includes/` folder
   - `public/` folder
   - `attached_assets/` folder (agar images use karna hai)

4. **Important:** `public/` folder ki saari files ko `public_html` ki root mein move karein
5. `admin/`, `database/`, `includes/` folders ko `public_html` mein hi rakhein

### Method 2: FTP (Recommended for bulk upload)

1. **FileZilla** download karein: https://filezilla-project.org/
2. FTP credentials hPanel se lein:
   - **Files** > **FTP Accounts**
   - Host: ftp.yourdomain.com
   - Username: aapka FTP username
   - Password: aapka FTP password
   - Port: 21

3. FileZilla mein connect karein
4. Local site se files select karein
5. Remote site mein `public_html` mein upload karein

---

## Step 4: File Structure Setup

Hostinger par file structure aisa hona chahiye:

```
public_html/
├── admin/
│   ├── assets/
│   │   └── css/
│   │       └── admin.css
│   ├── views/
│   ├── dashboard.php
│   ├── index.php (admin login)
│   └── ... (other admin files)
├── database/
│   ├── livvra.db
│   └── schema.sql
├── includes/
│   ├── models/
│   ├── config.php
│   ├── database.php
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── ajax/
├── index.php (homepage)
├── products.php
├── about.php
├── contact.php
├── cart.php
├── checkout.php
└── ... (other public files)
```

### Important Changes for Hostinger:

1. `public/` folder ki files ko `public_html` root mein copy karein
2. `includes/`, `admin/`, `database/` folders ko `public_html` mein move karein

---

## Step 5: Configuration Update

### includes/config.php mein changes (agar zaruri ho):

```php
// Agar MySQL use kar rahe ho:
// includes/database.php mein ye change karein:

// SQLite se MySQL switch karne ke liye:
$host = 'localhost';
$dbname = 'u123456789_livvra';  // Aapka database name
$username = 'u123456789_admin';  // Aapka username
$password = 'YourPassword';      // Aapka password

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
```

---

## Step 6: Permissions Set karein

1. hPanel mein **Files** > **File Manager** jaayein
2. `database/` folder select karein
3. **Permissions** set karein: `755`
4. `database/livvra.db` file ke liye: `644`

### FTP se permissions:
```
database/ folder: 755
database/livvra.db: 644
```

---

## Step 7: .htaccess File Create karein

`public_html` mein `.htaccess` file banayein:

```apache
# Enable URL Rewriting
RewriteEngine On

# Force HTTPS (Recommended)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Remove .php extension (Optional)
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME}\.php -f
RewriteRule ^([^\.]+)$ $1.php [NC,L]

# Protect sensitive files
<FilesMatch "\.(db|sql|ini|log)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Protect database folder
<IfModule mod_rewrite.c>
    RewriteRule ^database/ - [F,L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/css application/javascript
</IfModule>

# Cache static files
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access 1 year"
    ExpiresByType image/jpeg "access 1 year"
    ExpiresByType image/png "access 1 year"
    ExpiresByType image/gif "access 1 year"
    ExpiresByType text/css "access 1 month"
    ExpiresByType application/javascript "access 1 month"
</IfModule>
```

---

## Step 8: SSL Certificate Enable karein

1. hPanel mein **Security** > **SSL** jaayein
2. **Free SSL** enable karein
3. Certificate install hone tak wait karein (usually 10-15 minutes)

---

## Step 9: Test karein

1. Browser mein apni website kholein: `https://yourdomain.com`
2. Homepage check karein
3. Products page check karein
4. Admin panel check karein: `https://yourdomain.com/admin`
5. Cart aur checkout test karein

---

## Admin Login Details

- **URL:** https://yourdomain.com/admin
- **Username:** OfficialLivvra
- **Password:** OfficialLivvra@97296

> **Important:** Pehli baar login karne ke baad password change kar lein!

---

## Troubleshooting (Agar koi problem aaye)

### 1. 500 Internal Server Error
- `.htaccess` file check karein
- PHP version 8.0+ ensure karein (hPanel > PHP Configuration)
- File permissions check karein

### 2. Database Connection Error
- `database/livvra.db` file permissions check karein
- SQLite extension enabled hai check karein

### 3. CSS/Images not loading
- File paths correct check karein
- Browser cache clear karein

### 4. Admin panel not working
- `admin/` folder upload hua hai check karein
- PHP sessions enabled hai check karein

---

## PHP Configuration (hPanel)

1. **Advanced** > **PHP Configuration** jaayein
2. Settings:
   - PHP Version: **8.0** ya higher
   - memory_limit: 256M
   - max_execution_time: 300
   - upload_max_filesize: 64M
   - post_max_size: 64M

3. **Extensions** mein enable karein:
   - pdo_sqlite
   - pdo_mysql (agar MySQL use kar rahe ho)
   - json
   - mbstring

---

## Razorpay Payment Setup (Optional)

1. Razorpay account banayein: https://razorpay.com
2. API Keys generate karein
3. Admin Panel > Settings mein jaayein
4. Razorpay Key ID aur Secret add karein

---

## Security Recommendations

1. **Admin password change karein** immediately after first login
2. **Regular backups** lein (hPanel > Backups)
3. **Strong passwords** use karein
4. **Two-factor authentication** enable karein (Hostinger account ke liye)
5. `database/` folder ko direct access se protect karein

---

## Support

- **Hostinger Support:** https://www.hostinger.in/support
- **LIVVRA Contact:** livvraindia@gmail.com

---

**Congratulations!** Aapki LIVVRA website ab live hai!

*Dr Tridosha Herbotech Pvt Ltd*
