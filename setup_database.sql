-- ============================================================================
-- 🍽️ FoodieDelight - Complete Database Setup Script
-- ============================================================================
-- INSTRUCTIONS
-- 1. Copy this entire file content
-- 2. Paste into phpMyAdmin SQL tab (or MySQL command line)
-- 3. Click "Go" - Setup takes ~30 seconds
-- 4. Look for "🎉 SETUP COMPLETE!" message at the end
-- extra: use create_admin.php to create an admin user if admin user is not created
-- ============================================================================

-- Create database with proper charset
CREATE DATABASE IF NOT EXISTS food_ordering
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE food_ordering;

-- Display setup start message
SELECT '🚀 Starting FoodieDelight Database Setup...' as Status;

-- ============================================================================
-- TABLE CREATION
-- ============================================================================

-- Users table with psychology profiling
CREATE TABLE IF NOT EXISTS users (
                                     id INT PRIMARY KEY AUTO_INCREMENT,
                                     email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',

    -- Psychology Profile Fields
    appetite_profile ENUM('adventurous', 'comfort', 'healthy', 'indulgent') DEFAULT 'comfort',
    price_sensitivity ENUM('budget', 'moderate', 'premium') DEFAULT 'moderate',
    avg_order_value DECIMAL(8,2) DEFAULT 0,
    total_orders INT DEFAULT 0,
    favorite_categories JSON,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

-- Menu items with psychology attributes
CREATE TABLE IF NOT EXISTS menu_items (
                                          id INT PRIMARY KEY AUTO_INCREMENT,
                                          name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(8,2) NOT NULL,
    image_url VARCHAR(500),
    category ENUM('meals', 'drinks', 'desserts', 'specials') NOT NULL,

    -- Psychology Attributes
    appetite_score DECIMAL(3,1) DEFAULT 5.0,
    comfort_level DECIMAL(3,1) DEFAULT 5.0,
    sensory_words JSON,
    margin_level ENUM('low', 'medium', 'high') DEFAULT 'medium',

    -- Availability & Status
    available BOOLEAN DEFAULT 1,
    featured BOOLEAN DEFAULT 0,
    limited_qty INT NULL,
    preparation_time INT DEFAULT 15,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

-- Orders table with psychology tracking
CREATE TABLE IF NOT EXISTS orders (
                                      id INT PRIMARY KEY AUTO_INCREMENT,
                                      user_id INT NOT NULL,
                                      items JSON NOT NULL,
                                      total_amount DECIMAL(8,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'preparing', 'ready', 'delivered') DEFAULT 'pending',
    delivery_address TEXT,
    payment_method VARCHAR(50) DEFAULT 'cash',
    special_instructions TEXT,

    -- Psychology Tracking
    psychology_triggers_used JSON,
    session_data JSON,

    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estimated_delivery TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

-- Behavioral analytics table
CREATE TABLE IF NOT EXISTS behavior_logs (
                                             id INT PRIMARY KEY AUTO_INCREMENT,
                                             user_id INT NULL,
                                             session_id VARCHAR(255),
    event_type VARCHAR(100),
    event_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    );

-- Reviews and ratings table
CREATE TABLE IF NOT EXISTS reviews (
                                       id INT PRIMARY KEY AUTO_INCREMENT,
                                       user_id INT NOT NULL,
                                       item_id INT NOT NULL,
                                       rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_item (user_id, item_id)
    );

-- Settings table for system configuration
CREATE TABLE IF NOT EXISTS settings (
                                        setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

SELECT '✅ All tables created successfully!' as Status;

-- ============================================================================
-- PERFORMANCE INDEXES
-- ============================================================================

-- Users indexes
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);

-- Menu items indexes
CREATE INDEX IF NOT EXISTS idx_menu_category_available ON menu_items(category, available);
CREATE INDEX IF NOT EXISTS idx_menu_featured ON menu_items(featured, available);
CREATE INDEX IF NOT EXISTS idx_menu_psychology ON menu_items(appetite_score, comfort_level);

-- Orders indexes
CREATE INDEX IF NOT EXISTS idx_orders_user_date ON orders(user_id, order_date);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);

-- Behavior logs indexes
CREATE INDEX IF NOT EXISTS idx_behavior_user_event ON behavior_logs(user_id, event_type, created_at);
CREATE INDEX IF NOT EXISTS idx_behavior_session ON behavior_logs(session_id, created_at);

-- Reviews indexes
CREATE INDEX IF NOT EXISTS idx_reviews_item_rating ON reviews(item_id, rating);

SELECT '🚀 Performance indexes created!' as Status;

-- ============================================================================
-- SAMPLE DATA INSERTION
-- ============================================================================

-- Insert default users (password: 'password' for both)
INSERT IGNORE INTO users (email, password_hash, name, role, appetite_profile, price_sensitivity) VALUES
('admin@foodapp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin', 'comfort', 'moderate'),
('demo@foodapp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Demo User', 'user', 'adventurous', 'premium');

-- Insert psychology-enhanced menu items
INSERT IGNORE INTO menu_items (id, name, description, price, category, appetite_score, comfort_level, sensory_words, margin_level, available, featured, limited_qty, preparation_time, image_url) VALUES

-- MEALS (with psychology attributes)
(33, 'Crispy Buffalo Wings', 'Perfectly crispy chicken wings tossed in tangy buffalo sauce with cooling ranch dip', 12.99, 'meals', 9.0, 7.5, '["crispy", "tangy", "spicy", "juicy"]', 'high', 1, 1, NULL, 20, 'assets/images/food/buffalo-wings.jpg'),

(34, 'Loaded Nachos Supreme', 'Mountain of crispy tortilla chips smothered in melted cheese, jalapeños, and fresh guacamole', 14.99, 'meals', 8.5, 8.0, '["crispy", "melted", "fresh", "spicy"]', 'high', 1, 0, 5, 15, 'assets/images/food/loaded-nachos.jpg'),

(35, 'Golden Mozzarella Sticks', 'Hand-breaded mozzarella sticks fried to golden perfection with marinara dipping sauce', 9.99, 'meals', 8.0, 9.0, '["golden", "crispy", "gooey", "warm"]', 'medium', 1, 0, NULL, 12, 'assets/images/food/mozzarella-sticks.jpg'),

(38, 'Prime Angus Burger', 'Juicy prime angus beef patty with melted cheddar, crispy bacon, and golden fries', 18.99, 'meals', 9.5, 9.0, '["juicy", "melted", "crispy", "smoky"]', 'high', 1, 1, NULL, 25, 'assets/images/food/angus-burger.jpg'),

(39, 'Grilled Salmon Fillet', 'Fresh Atlantic salmon grilled to perfection with lemon herb butter and roasted vegetables', 24.99, 'meals', 8.0, 7.0, '["fresh", "flaky", "herbed", "tender"]', 'high', 1, 1, NULL, 30, 'assets/images/food/grilled-salmon.jpg'),

(43, 'Classic Margherita Pizza', 'Wood-fired pizza with fresh mozzarella, ripe tomatoes, and aromatic basil leaves', 17.99, 'meals', 8.0, 8.5, '["wood-fired", "fresh", "aromatic", "bubbly"]', 'medium', 1, 0, NULL, 18, 'assets/images/food/margherita-pizza.jpg'),

(44, 'Tender Beef Steak', 'Perfectly grilled 8oz ribeye steak with garlic mashed potatoes and steamed broccoli', 29.99, 'meals', 9.0, 8.0, '["tender", "juicy", "grilled", "savory"]', 'high', 1, 1, 2, 35, 'assets/images/food/ribeye-steak.jpg'),

-- DRINKS (refreshing options)
(48, 'Freshly Squeezed Orange Juice', 'Pure orange juice squeezed from ripe Florida oranges, bursting with vitamin C', 4.99, 'drinks', 7.0, 6.0, '["fresh", "sweet", "citrusy", "pure"]', 'high', 1, 0, NULL, 5, 'assets/images/food/orange-juice.jpg'),

(49, 'Creamy Vanilla Milkshake', 'Thick and creamy vanilla milkshake topped with whipped cream and a cherry', 6.99, 'drinks', 8.0, 9.0, '["thick", "creamy", "sweet", "indulgent"]', 'high', 1, 0, NULL, 8, 'assets/images/food/vanilla-milkshake.jpg'),

(50, 'Artisan Coffee Blend', 'Rich Colombian coffee beans perfectly roasted and brewed to aromatic perfection', 3.99, 'drinks', 7.5, 8.0, '["rich", "aromatic", "smooth", "bold"]', 'medium', 1, 1, NULL, 5, 'assets/images/food/coffee-blend.jpg'),

(52, 'Tropical Mango Smoothie', 'Blend of ripe mangoes, coconut milk, and tropical fruits for the ultimate refreshment', 7.99, 'drinks', 7.5, 6.5, '["tropical", "smooth", "sweet", "refreshing"]', 'medium', 1, 0, NULL, 10, 'assets/images/food/mango-smoothie.jpg'),

-- DESSERTS (indulgent treats)
(55, 'Decadent Chocolate Lava Cake', 'Warm chocolate cake with molten center, served with vanilla ice cream', 8.99, 'desserts', 9.0, 9.5, '["warm", "molten", "rich", "decadent"]', 'high', 1, 1, 4, 15, 'assets/images/food/chocolate-lava-cake.jpg'),

(56, 'Classic New York Cheesecake', 'Creamy cheesecake on graham cracker crust with fresh berry compote', 7.99, 'desserts', 8.5, 9.0, '["creamy", "smooth", "rich", "indulgent"]', 'medium', 1, 0, NULL, 5, 'assets/images/food/cheesecake.jpg'),

(57, 'Warm Apple Pie à la Mode', 'Traditional apple pie with cinnamon spice, served warm with vanilla ice cream', 6.99, 'desserts', 8.0, 9.5, '["warm", "cinnamon", "flaky", "comforting"]', 'medium', 1, 0, NULL, 20, 'assets/images/food/apple-pie.jpg'),

(60, 'Tiramisu Delight', 'Layers of coffee-soaked ladyfingers with mascarpone cream and cocoa dusting', 9.99, 'desserts', 8.5, 8.0, '["creamy", "coffee-infused", "silky", "elegant"]', 'high', 1, 1, 6, 10, 'assets/images/food/tiramisu.jpg'),

-- SPECIALS (premium offerings)
(61, 'Chef''s Special Pasta', 'Handmade pasta with seasonal ingredients and secret sauce', 18.99, 'specials', 9.0, 7.0, '["handmade", "seasonal", "secret"]', 'high', 1, 1, 3, 25, 'assets/images/food/special-pasta.jpg');

-- Insert system settings
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
('site_name', 'FoodieDelight'),
('delivery_fee', '3.99'),
('free_delivery_threshold', '25.00'),
('psychology_enabled', '1'),
('tax_rate', '0.06'),
('currency', 'USD'),
('timezone', 'America/New_York'),
('order_preparation_time', '15-30'),
('contact_phone', '+1-555-FOODIE'),
('contact_email', 'orders@foodiedelight.com');

-- Insert sample behavior logs for analytics
INSERT IGNORE INTO behavior_logs (user_id, session_id, event_type, event_data) VALUES
(2, 'demo_session_001', 'page_view', '{"page": "home", "timestamp": "2025-07-05 10:00:00"}'),
(2, 'demo_session_001', 'item_viewed', '{"item_id": 33, "item_name": "Crispy Buffalo Wings", "timestamp": "2025-07-05 10:02:00"}'),
(2, 'demo_session_001', 'cart_add', '{"item_id": 33, "quantity": 1, "timestamp": "2025-07-05 10:03:00"}'),
(NULL, 'guest_session_001', 'psychology_trigger', '{"trigger_type": "scarcity", "message": "Only 4 left!", "timestamp": "2025-07-05 10:06:00"}');

-- Insert sample reviews
INSERT IGNORE INTO reviews (user_id, item_id, rating, comment) VALUES
(2, 33, 5, 'Amazing wings! Perfectly crispy and the buffalo sauce had just the right kick.'),
(2, 38, 4, 'Great burger! The beef patty was juicy and the bacon was crispy. Fries were golden.'),
(2, 55, 5, 'Best chocolate lava cake ever! The molten center was pure heaven.');

SELECT '🍽️ Sample menu items and data inserted!' as Status;


-- Update items that should be 'meals' category
UPDATE menu_items SET category = 'meals' WHERE id IN (
                                                      33, -- Crispy Buffalo Wings
                                                      34, -- Loaded Nachos Supreme
                                                      35, -- Golden Mozzarella Sticks
                                                      36, -- Fresh Bruschetta Trio
                                                      37, -- Spicy Jalapeño Poppers
                                                      38, -- Prime Angus Burger
                                                      39, -- Grilled Salmon Fillet
                                                      40, -- Mama's Chicken Parmesan
                                                      42, -- Spicy Shrimp Pasta
                                                      43, -- Classic Margherita Pizza
                                                      44, -- Tender Beef Steak
                                                      45, -- Fish & Chips Classic
                                                      46, -- Vegetarian Buddha Bowl
                                                      47  -- Spicy Korean Bibimbap
    );

-- Add some Chef's Specials (since you have none)
-- Insert Chef's Specials without preparation_time column
INSERT INTO menu_items (name, description, price, category, appetite_score, comfort_level, sensory_words, margin_level, available, featured, limited_qty) VALUES

                                                                                                                                                              ('Chef\'s Signature Wagyu', 'Premium wagyu beef with truffle mashed potatoes and seasonal vegetables', 45.99, 'specials', 9.8, 8.5, '["premium", "tender", "luxurious", "buttery"]', 'high', 1, 1, 2),

                                                                                                                                                              ('Lobster Thermidor Deluxe', 'Fresh Maine lobster in rich cream sauce with herbs and aged cheese', 38.99, 'specials', 9.5, 8.0, '["rich", "creamy", "luxurious", "fresh"]', 'high', 1, 1, 3),

                                                                                                                                                              ('Chef\'s Tasting Platter', 'Five signature appetizers curated by our head chef', 24.99, 'specials', 8.5, 7.5, '["curated", "artisanal", "diverse", "exquisite"]', 'high', 1, 1, 4),

                                                                                                                                                              ('Duck Confit Special', 'Traditional French duck confit with roasted root vegetables', 34.99, 'specials', 9.0, 8.0, '["traditional", "crispy", "succulent", "aromatic"]', 'high', 1, 1, 2),

                                                                                                                                                              ('Seafood Tower Supreme', 'Chef\'s selection of premium seafood with house special sauce', 42.99, 'specials', 9.2, 7.5, '["premium", "fresh", "elegant", "ocean-fresh"]', 'high', 1, 1, 1);
-- Add image URLs to the Chef's Specials we just created
UPDATE menu_items SET image_url = CASE
                                      WHEN name = 'Chef\'s Signature Wagyu' THEN 'assets/images/food/wagyu-steak.jpg'
                                      WHEN name = 'Lobster Thermidor Deluxe' THEN 'assets/images/food/lobster-thermidor.jpg'
                                      WHEN name = 'Chef\'s Tasting Platter' THEN 'assets/images/food/tasting-platter.jpg'
                                      WHEN name = 'Duck Confit Special' THEN 'assets/images/food/duck-confit.jpg'
                                      WHEN name = 'Seafood Tower Supreme' THEN 'assets/images/food/seafood-tower.jpg'
                                      ELSE image_url
    END
WHERE category = 'specials' AND image_url IS NULL;
-- ============================================================================
-- VERIFICATION & SUCCESS CHECKS
-- ============================================================================

-- Verify table creation
SELECT
    CONCAT('✅ ', COUNT(*), ' tables created') as Tables_Status
FROM information_schema.tables
WHERE table_schema = 'food_ordering';

-- Verify sample data
SELECT
    CONCAT('🍕 ', COUNT(*), ' menu items ready') as Menu_Status
FROM menu_items;

SELECT
    CONCAT('👥 ', COUNT(*), ' users created (admin & demo)') as Users_Status
FROM users;

SELECT
    CONCAT('⚙️ ', COUNT(*), ' system settings configured') as Settings_Status
FROM settings;

-- Display featured items for quick test
SELECT
    '🌟 Featured Menu Items:' as Category,
    GROUP_CONCAT(name SEPARATOR ', ') as Items
FROM menu_items
WHERE featured = 1;

-- Display login credentials
SELECT
    '🔑 TEST LOGIN CREDENTIALS:' as Info,
    'Email: admin@foodapp.com, Password: password' as Admin_Login,
    'Email: demo@foodapp.com, Password: password' as Demo_Login;

-- Final success message
SELECT
    '🎉 FOODIEDELIGHT SETUP COMPLETE! 🎉' as Status,
    'Database ready with psychology-enhanced features!' as Message,
    'You can now run the web application!' as Next_Step;

-- Show next steps
SELECT
    '📋 NEXT STEPS FOR PROFESSOR:' as Guide,
    '1. Update config/database.php with connection details' as Step_1,
    '2. Create assets/images/food/ folder for food images' as Step_2,
    '3. Start your web server and visit the application' as Step_3,
    '4. Login with admin@foodapp.com / password' as Step_4;