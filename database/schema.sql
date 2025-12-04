-- MySQL Schema for LIVVRA E-Commerce
-- Deploy this on Hostinger phpMyAdmin

CREATE DATABASE IF NOT EXISTS livvra_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE livvra_db;

-- Admin Users Table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('super_admin', 'admin', 'manager') DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon_class VARCHAR(50) DEFAULT 'fa-leaf',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    sku VARCHAR(50),
    price DECIMAL(10,2) NOT NULL,
    mrp DECIMAL(10,2),
    short_description TEXT,
    long_description TEXT,
    benefits TEXT,
    image VARCHAR(255),
    stock_qty INT DEFAULT 100,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    rating DECIMAL(2,1) DEFAULT 0,
    reviews_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Customers Table
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20) NOT NULL,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(10),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100),
    customer_phone VARCHAR(20) NOT NULL,
    shipping_address TEXT NOT NULL,
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(10),
    payment_method ENUM('cod', 'razorpay') NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    order_status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    subtotal DECIMAL(10,2) NOT NULL,
    shipping_fee DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    razorpay_order_id VARCHAR(100),
    razorpay_payment_id VARCHAR(100),
    razorpay_signature VARCHAR(255),
    notes TEXT,
    placed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    delivered_at DATETIME NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- Order Items Table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200) NOT NULL,
    product_image VARCHAR(255),
    unit_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    line_total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Contact Inquiries Table
CREATE TABLE contact_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
    handled_by INT NULL,
    handled_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (handled_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Site Settings Table
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user
-- Password: OfficialLivvra@97296
INSERT INTO admins (username, password_hash, email, role) VALUES 
('OfficialLivvra', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'livvraindia@gmail.com', 'super_admin');

-- Insert categories
INSERT INTO categories (name, slug, description, icon_class, sort_order) VALUES
('Skin Care', 'skin-care', 'Natural skincare products', 'fa-spa', 1),
('Gym Foods', 'gym-foods', 'Nutrition for fitness', 'fa-dumbbell', 2),
("Men's Health", 'mens-health', 'Vitality and energy', 'fa-mars', 3),
('Weight Management', 'weight-management', 'Healthy weight loss', 'fa-weight-scale', 4),
('Heart Care', 'heart-care', 'Cardiovascular health', 'fa-heart-pulse', 5),
('Daily Wellness', 'daily-wellness', 'Everyday health', 'fa-leaf', 6),
('Ayurvedic Juices', 'ayurvedic-juices', 'Pure herbal juices', 'fa-glass-water', 7),
('Blood Sugar & Chronic Care', 'blood-sugar', 'Diabetes management', 'fa-droplet', 8);

-- Insert site settings
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'LIVVRA'),
('site_tagline', 'Live Better Live Strong'),
('site_email', 'livvraindia@gmail.com'),
('site_phone', '+91 9876543210'),
('site_address', 'Dr Tridosha Herbotech Pvt Ltd, Sco no 27, Second Floor, Phase 3, Model Town, Bathinda 151001'),
('currency_symbol', '₹'),
('razorpay_key_id', ''),
('razorpay_key_secret', ''),
('shipping_fee', '0'),
('free_shipping_above', '499');
