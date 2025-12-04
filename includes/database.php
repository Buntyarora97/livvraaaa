<?php
class Database {
    private static $instance = null;
    private $pdo;
    private $dbPath;

    private function __construct() {
        $this->dbPath = __DIR__ . '/../database/livvra.db';
        
        try {
            $this->pdo = new PDO('sqlite:' . $this->dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            if (!file_exists($this->dbPath) || filesize($this->dbPath) == 0) {
                $this->initializeDatabase();
            }
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    private function initializeDatabase() {
        $sql = "
        CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL,
            email TEXT,
            role TEXT DEFAULT 'admin',
            is_active INTEGER DEFAULT 1,
            last_login_at TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            description TEXT,
            icon_class TEXT DEFAULT 'fa-leaf',
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER,
            name TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            sku TEXT,
            price REAL NOT NULL,
            mrp REAL,
            short_description TEXT,
            long_description TEXT,
            benefits TEXT,
            image TEXT,
            stock_qty INTEGER DEFAULT 100,
            is_featured INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            rating REAL DEFAULT 0,
            reviews_count INTEGER DEFAULT 0,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id)
        );

        CREATE TABLE IF NOT EXISTS customers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT,
            phone TEXT NOT NULL,
            address TEXT,
            city TEXT,
            state TEXT,
            pincode TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_number TEXT UNIQUE NOT NULL,
            customer_id INTEGER,
            customer_name TEXT NOT NULL,
            customer_email TEXT,
            customer_phone TEXT NOT NULL,
            shipping_address TEXT NOT NULL,
            city TEXT,
            state TEXT,
            pincode TEXT,
            payment_method TEXT NOT NULL,
            payment_status TEXT DEFAULT 'pending',
            order_status TEXT DEFAULT 'pending',
            subtotal REAL NOT NULL,
            shipping_fee REAL DEFAULT 0,
            total REAL NOT NULL,
            razorpay_order_id TEXT,
            razorpay_payment_id TEXT,
            razorpay_signature TEXT,
            notes TEXT,
            placed_at TEXT DEFAULT CURRENT_TIMESTAMP,
            delivered_at TEXT,
            FOREIGN KEY (customer_id) REFERENCES customers(id)
        );

        CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            product_id INTEGER,
            product_name TEXT NOT NULL,
            product_image TEXT,
            unit_price REAL NOT NULL,
            quantity INTEGER NOT NULL,
            line_total REAL NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id),
            FOREIGN KEY (product_id) REFERENCES products(id)
        );

        CREATE TABLE IF NOT EXISTS contact_inquiries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT,
            subject TEXT,
            message TEXT NOT NULL,
            status TEXT DEFAULT 'new',
            handled_by INTEGER,
            handled_at TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (handled_by) REFERENCES admins(id)
        );

        CREATE TABLE IF NOT EXISTS settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            setting_key TEXT UNIQUE NOT NULL,
            setting_value TEXT,
            updated_at TEXT DEFAULT CURRENT_TIMESTAMP
        );
        ";

        $this->pdo->exec($sql);
        $this->seedData();
    }

    private function seedData() {
        $passwordHash = password_hash('OfficialLivvra@97296', PASSWORD_DEFAULT);
        
        $this->pdo->exec("INSERT OR IGNORE INTO admins (username, password_hash, email, role) VALUES 
            ('OfficialLivvra', '$passwordHash', 'livvraindia@gmail.com', 'super_admin')");

        $categories = [
            ['Skin Care', 'skin-care', 'Natural skincare products', 'fa-spa', 1],
            ['Gym Foods', 'gym-foods', 'Nutrition for fitness', 'fa-dumbbell', 2],
            ["Men's Health", 'mens-health', 'Vitality and energy', 'fa-mars', 3],
            ['Weight Management', 'weight-management', 'Healthy weight loss', 'fa-weight-scale', 4],
            ['Heart Care', 'heart-care', 'Cardiovascular health', 'fa-heart-pulse', 5],
            ['Daily Wellness', 'daily-wellness', 'Everyday health', 'fa-leaf', 6],
            ['Ayurvedic Juices', 'ayurvedic-juices', 'Pure herbal juices', 'fa-glass-water', 7],
            ['Blood Sugar & Chronic Care', 'blood-sugar', 'Diabetes management', 'fa-droplet', 8]
        ];

        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO categories (name, slug, description, icon_class, sort_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($categories as $cat) {
            $stmt->execute($cat);
        }

        $products = [
            [3, 'Pure Shilajit Gold', 'pure-shilajit-gold', 'SG001', 1499, 1999, 'Premium Himalayan Shilajit enriched with gold for enhanced energy and vitality. 100% natural and lab tested.', 'Boosts Energy, Enhances Stamina, Improves Immunity, Natural Aphrodisiac', 'shilajit-gold.jpg', 4.8, 256],
            [6, 'Ashwagandha Capsules', 'ashwagandha-capsules', 'AC001', 599, 799, 'Organic Ashwagandha root extract capsules for stress relief and improved sleep quality.', 'Reduces Stress, Better Sleep, Muscle Recovery, Mental Clarity', 'ashwagandha.jpg', 4.7, 189],
            [1, 'Aloe Vera Gel', 'aloe-vera-gel', 'AV001', 349, 499, 'Pure Aloe Vera gel for skin hydration, acne control, and natural glow.', 'Deep Hydration, Acne Control, Anti-Aging, Sun Protection', 'aloe-vera.jpg', 4.6, 342],
            [2, 'Protein Powder Plus', 'protein-powder-plus', 'PP001', 1899, 2499, 'Plant-based protein powder with added vitamins and minerals for muscle building.', 'Muscle Building, Fast Recovery, High Protein, No Artificial Flavors', 'protein-powder.jpg', 4.9, 167],
            [4, 'Weight Loss Formula', 'weight-loss-formula', 'WL001', 899, 1299, 'Natural weight management supplement with Garcinia Cambogia and Green Tea extract.', 'Burns Fat, Appetite Control, Boosts Metabolism, Natural Ingredients', 'weight-loss.jpg', 4.5, 298],
            [5, 'Heart Care Capsules', 'heart-care-capsules', 'HC001', 799, 1099, 'Ayurvedic formulation for heart health with Arjuna and Omega-3 fatty acids.', 'Healthy Heart, Blood Pressure, Cholesterol Control, Circulation', 'heart-care.jpg', 4.7, 156],
            [7, 'Amla Juice', 'amla-juice', 'AJ001', 299, 399, 'Pure Amla juice rich in Vitamin C for immunity and digestive health.', 'Immunity Boost, Digestive Health, Hair Growth, Skin Glow', 'amla-juice.jpg', 4.8, 423],
            [8, 'Diabetic Care Plus', 'diabetic-care-plus', 'DC001', 699, 999, 'Herbal supplement for blood sugar management with Karela and Jamun extracts.', 'Blood Sugar Control, Pancreas Health, Natural Formula, Safe Long-term Use', 'diabetic-care.jpg', 4.6, 278],
            [6, 'Immunity Booster', 'immunity-booster', 'IB001', 449, 599, 'Powerful immunity booster with Giloy, Tulsi, and Turmeric extracts.', 'Strong Immunity, Fights Infections, Antioxidant Rich, Daily Protection', 'immunity-booster.jpg', 4.9, 512],
            [1, 'Hair Growth Oil', 'hair-growth-oil', 'HG001', 399, 549, 'Ayurvedic hair oil with Bhringraj, Amla, and Coconut for healthy hair growth.', 'Hair Growth, Prevents Hairfall, Nourishes Scalp, Reduces Dandruff', 'hair-oil.jpg', 4.7, 367],
            [2, 'Energy Drink Mix', 'energy-drink-mix', 'ED001', 549, 749, 'Natural energy drink powder with electrolytes and B-vitamins for workout performance.', 'Instant Energy, Electrolyte Balance, Pre-Workout, No Crash', 'energy-drink.jpg', 4.6, 198],
            [6, 'Joint Pain Relief', 'joint-pain-relief', 'JP001', 649, 899, 'Herbal formulation with Boswellia and Turmeric for joint pain and inflammation.', 'Pain Relief, Reduces Inflammation, Joint Mobility, Cartilage Support', 'joint-pain.jpg', 4.8, 234]
        ];

        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO products (category_id, name, slug, sku, price, mrp, short_description, benefits, image, rating, reviews_count, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        foreach ($products as $prod) {
            $stmt->execute($prod);
        }

        $settings = [
            ['site_name', 'LIVVRA'],
            ['site_tagline', 'Live Better Live Strong'],
            ['site_email', 'livvraindia@gmail.com'],
            ['site_phone', '+91 9876543210'],
            ['site_address', 'Dr Tridosha Herbotech Pvt Ltd, Sco no 27, Second Floor, Phase 3, Model Town, Bathinda 151001'],
            ['currency_symbol', '₹'],
            ['razorpay_key_id', ''],
            ['razorpay_key_secret', ''],
            ['shipping_fee', '0'],
            ['free_shipping_above', '499']
        ];

        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        foreach ($settings as $setting) {
            $stmt->execute($setting);
        }
    }
}

function db() {
    return Database::getInstance()->getConnection();
}
