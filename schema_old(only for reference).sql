-- ============================================================================
-- FoodieDelight - FIXED MySQL Database Setup
-- Run this entire script in phpMyAdmin or MySQL command line
-- ============================================================================

-- Drop database if exists (CAREFUL - this removes all data!)
-- DROP DATABASE IF EXISTS food_ordering;

-- Create database
CREATE DATABASE IF NOT EXISTS food_ordering
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Use the database
USE food_ordering;

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

    -- Psychology Profile
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

    -- Psychology Tracking
                                      psychology_triggers_used JSON,
                                      session_data JSON,

                                      order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

                                      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Behavioral analytics
CREATE TABLE IF NOT EXISTS behavior_logs (
                                             id INT PRIMARY KEY AUTO_INCREMENT,
                                             user_id INT NULL,
                                             session_id VARCHAR(255),
                                             event_type VARCHAR(100),
                                             event_data JSON,
                                             created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

                                             FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Reviews and ratings
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

-- Admin settings
CREATE TABLE IF NOT EXISTS settings (
                                        setting_key VARCHAR(100) PRIMARY KEY,
                                        setting_value TEXT,
                                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================================================
-- INDEXES FOR PERFORMANCE
-- ============================================================================

-- Users indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

-- Menu items indexes
CREATE INDEX idx_menu_category_available ON menu_items(category, available);
CREATE INDEX idx_menu_featured ON menu_items(featured, available);
CREATE INDEX idx_menu_psychology ON menu_items(appetite_score, comfort_level);

-- Orders indexes
CREATE INDEX idx_orders_user_date ON orders(user_id, order_date);
CREATE INDEX idx_orders_date_status ON orders(order_date, status);
CREATE INDEX idx_orders_status ON orders(status);

-- Behavior logs indexes
CREATE INDEX idx_behavior_user_event ON behavior_logs(user_id, event_type, created_at);
CREATE INDEX idx_behavior_session ON behavior_logs(session_id, created_at);
CREATE INDEX idx_behavior_event_type ON behavior_logs(event_type);

-- Reviews indexes
CREATE INDEX idx_reviews_item_rating ON reviews(item_id, rating);
CREATE INDEX idx_reviews_user_date ON reviews(user_id, created_at);

-- Fulltext search index for menu items (FIXED - Check if exists first)
SET @exist_check = (SELECT COUNT(*) FROM information_schema.statistics
                    WHERE table_schema = 'food_ordering'
                      AND table_name = 'menu_items'
                      AND index_name = 'name');

SET @sql = IF(@exist_check = 0,
              'ALTER TABLE menu_items ADD FULLTEXT(name, description)',
              'SELECT "Fulltext index already exists" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- SAMPLE DATA INSERTION (FIXED - Check if data exists first)
-- ============================================================================

-- Insert default admin user (password: password) - FIXED to prevent duplicates
INSERT IGNORE INTO users (email, password_hash, name, role, appetite_profile, price_sensitivity) VALUES
                                                                                                     ('admin@foodapp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin', 'comfort', 'moderate'),
                                                                                                     ('demo@foodapp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Demo User', 'user', 'adventurous', 'premium');

-- Insert sample menu items with psychology data (FIXED - Complete INSERT statement)
INSERT IGNORE INTO menu_items (name, description, price, category, appetite_score, comfort_level, sensory_words, margin_level, available, featured, limited_qty) VALUES

-- MEALS
('Crispy Margherita Pizza', 'Fresh mozzarella, juicy tomatoes, crispy thin crust with aromatic basil leaves', 12.99, 'meals', 8.5, 7.0, '["crispy", "fresh", "juicy", "aromatic"]', 'high', 1, 1, NULL),

('Gourmet Burger Deluxe', 'Tender beef patty, melted cheese, golden fries with creamy garlic aioli', 15.99, 'meals', 9.0, 8.5, '["tender", "melted", "golden", "creamy"]', 'high', 1, 1, NULL),

('Fresh Caesar Salad', 'Crisp romaine lettuce, creamy dressing, crunchy croutons, zesty lemon', 8.99, 'meals', 6.0, 4.0, '["crisp", "creamy", "crunchy", "zesty"]', 'medium', 1, 0, NULL),

('Spicy Buffalo Wings', 'Crispy wings tossed in fiery buffalo sauce, served with cooling ranch', 9.99, 'meals', 8.0, 6.0, '["crispy", "fiery", "cooling"]', 'medium', 1, 0, 8),

('Loaded Nachos Supreme', 'Crunchy tortilla chips, melted cheese, spicy jalapeños, rich guacamole', 11.99, 'meals', 8.5, 7.5, '["crunchy", "melted", "spicy", "rich"]', 'high', 1, 1, NULL),

('Grilled Chicken Teriyaki', 'Succulent grilled chicken glazed with sweet teriyaki sauce, steamed rice', 13.99, 'meals', 7.5, 7.0, '["succulent", "sweet", "glazed", "steamed"]', 'medium', 1, 0, NULL),

-- DRINKS
('Steaming Hot Chocolate', 'Rich, creamy hot chocolate topped with fluffy marshmallows', 4.99, 'drinks', 7.5, 9.0, '["steaming", "rich", "creamy", "fluffy"]', 'medium', 1, 0, NULL),

('Fresh Fruit Smoothie', 'Refreshing blend of tropical fruits, creamy yogurt, natural sweetness', 5.99, 'drinks', 7.0, 5.0, '["refreshing", "tropical", "creamy", "natural"]', 'medium', 1, 0, NULL),

('Artisan Coffee Blend', 'Rich, aromatic coffee with smooth finish, perfectly roasted beans', 2.99, 'drinks', 6.5, 6.0, '["rich", "aromatic", "smooth"]', 'low', 1, 0, NULL),

('Iced Vanilla Latte', 'Cool and creamy espresso drink with sweet vanilla syrup over ice', 4.49, 'drinks', 7.0, 6.5, '["cool", "creamy", "sweet"]', 'medium', 1, 0, NULL),

-- DESSERTS
('Decadent Chocolate Cake', 'Moist chocolate layers with silky smooth ganache, pure indulgence', 6.99, 'desserts', 8.0, 8.5, '["decadent", "moist", "silky", "indulgent"]', 'high', 1, 1, NULL),

('Vanilla Bean Ice Cream', 'Smooth, creamy vanilla with real bean specks, cold and refreshing', 3.99, 'desserts', 7.0, 8.0, '["smooth", "creamy", "cold", "refreshing"]', 'medium', 1, 0, NULL),

('Warm Apple Pie', 'Flaky pastry filled with cinnamon-spiced apples, served with vanilla ice cream', 7.99, 'desserts', 8.5, 9.0, '["flaky", "cinnamon-spiced", "warm"]', 'high', 1, 1, 4),

('Chocolate Chip Cookies', 'Freshly baked cookies with gooey chocolate chips, crispy edges', 4.99, 'desserts', 7.5, 8.0, '["freshly", "gooey", "crispy"]', 'medium', 1, 0, NULL),

-- SPECIALS
('Chef''s Special Pasta', 'Handmade pasta with seasonal ingredients, chef''s secret sauce', 18.99, 'specials', 9.0, 7.0, '["handmade", "seasonal", "secret"]', 'high', 1, 1, 3),

('Seafood Platter Deluxe', 'Fresh lobster, grilled salmon, crispy calamari with garlic butter', 24.99, 'specials', 9.5, 6.0, '["fresh", "grilled", "crispy", "garlic"]', 'high', 1, 1, 2);

-- Insert default settings (FIXED to prevent duplicates)
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
                                                             ('site_name', 'FoodieDelight'),
                                                             ('delivery_fee', '3.00'),
                                                             ('free_delivery_threshold', '30.00'),
                                                             ('psychology_enabled', '1'),
                                                             ('scarcity_threshold', '5'),
                                                             ('tax_rate', '0.08'),
                                                             ('currency', 'USD'),
                                                             ('timezone', 'Asia/Kuala_Lumpur');

-- Insert some sample behavior logs for testing (FIXED to prevent duplicates on re-run)
INSERT IGNORE INTO behavior_logs (user_id, session_id, event_type, event_data) VALUES
                                                                                   (2, 'demo_session_001', 'page_view', '{"page": "home", "timestamp": "2025-06-29 10:00:00"}'),
                                                                                   (2, 'demo_session_001', 'item_viewed', '{"item_id": 1, "item_name": "Crispy Margherita Pizza", "timestamp": "2025-06-29 10:02:00"}'),
                                                                                   (2, 'demo_session_001', 'cart_add', '{"item_id": 1, "item_name": "Crispy Margherita Pizza", "quantity": 1, "timestamp": "2025-06-29 10:03:00"}'),
                                                                                   (NULL, 'guest_session_001', 'page_view', '{"page": "menu", "timestamp": "2025-06-29 10:05:00"}'),
                                                                                   (NULL, 'guest_session_001', 'psychology_trigger_exposed', '{"trigger_type": "scarcity", "message": "Only 4 left!", "timestamp": "2025-06-29 10:06:00"}');

-- Insert sample reviews (FIXED to prevent duplicates)
INSERT IGNORE INTO reviews (user_id, item_id, rating, comment) VALUES
                                                                   (2, 1, 5, 'Amazing pizza! The crust was perfectly crispy and the basil was so fresh.'),
                                                                   (2, 2, 4, 'Great burger, but could use more sauce. The fries were golden and delicious!'),
                                                                   (2, 11, 5, 'Best chocolate cake ever! So moist and decadent, exactly as described.');

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Check all tables were created
SELECT
    TABLE_NAME as 'Table',
    TABLE_ROWS as 'Rows',
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) as 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'food_ordering'
ORDER BY TABLE_NAME;

-- Check sample data counts
SELECT 'Users' as Table_Name, COUNT(*) as Record_Count FROM users
UNION ALL
SELECT 'Menu Items', COUNT(*) FROM menu_items
UNION ALL
SELECT 'Orders', COUNT(*) FROM orders
UNION ALL
SELECT 'Behavior Logs', COUNT(*) FROM behavior_logs
UNION ALL
SELECT 'Reviews', COUNT(*) FROM reviews
UNION ALL
SELECT 'Settings', COUNT(*) FROM settings;

-- Check psychology data
SELECT
    name,
    appetite_score,
    comfort_level,
    sensory_words,
    margin_level,
    featured
FROM menu_items
WHERE featured = 1
ORDER BY appetite_score DESC;

-- Check indexes
SELECT
    TABLE_NAME as 'Table',
    INDEX_NAME as 'Index',
    COLUMN_NAME as 'Column'
FROM information_schema.statistics
WHERE table_schema = 'food_ordering'
  AND INDEX_NAME != 'PRIMARY'
ORDER BY TABLE_NAME, INDEX_NAME;




-- ============================================================================
-- SUCCESS MESSAGE
-- ============================================================================
SELECT
    '🎉 DATABASE SETUP COMPLETE! 🎉' as Status,
    'All tables created with sample data and indexes' as Message,
    'Ready for FoodieDelight application!' as Next_Step;

-- Update menu items with new image URLs

-- Update all menu items with image URLs based on your data
-- Make sure to create folder: assets/images/food/ first

-- Appetizers
UPDATE menu_items SET image_url = 'assets/images/food/buffalo-wings.jpg' WHERE id = 33; -- Crispy Buffalo Wings
UPDATE menu_items SET image_url = 'assets/images/food/loaded-nachos.jpg' WHERE id = 34; -- Loaded Nachos Supreme
UPDATE menu_items SET image_url = 'assets/images/food/mozzarella-sticks.jpg' WHERE id = 35; -- Golden Mozzarella Sticks
UPDATE menu_items SET image_url = 'assets/images/food/bruschetta.jpg' WHERE id = 36; -- Fresh Bruschetta Trio
UPDATE menu_items SET image_url = 'assets/images/food/jalapeno-poppers.jpg' WHERE id = 37; -- Spicy Jalapeño Poppers

-- Main Dishes
UPDATE menu_items SET image_url = 'assets/images/food/angus-burger.jpg' WHERE id = 38; -- Prime Angus Burger
UPDATE menu_items SET image_url = 'assets/images/food/grilled-salmon.jpg' WHERE id = 39; -- Grilled Salmon Fillet
UPDATE menu_items SET image_url = 'assets/images/food/chicken-parmesan.jpg' WHERE id = 40; -- Mama's Chicken Parmesan
UPDATE menu_items SET image_url = 'assets/images/food/shrimp-pasta.jpg' WHERE id = 42; -- Spicy Shrimp Pasta
UPDATE menu_items SET image_url = 'assets/images/food/margherita-pizza.jpg' WHERE id = 43; -- Classic Margherita Pizza
UPDATE menu_items SET image_url = 'assets/images/food/ribeye-steak.jpg' WHERE id = 44; -- Tender Beef Steak
UPDATE menu_items SET image_url = 'assets/images/food/fish-and-chips.jpg' WHERE id = 45; -- Fish & Chips Classic
UPDATE menu_items SET image_url = 'assets/images/food/buddha-bowl.jpg' WHERE id = 46; -- Vegetarian Buddha Bowl
UPDATE menu_items SET image_url = 'assets/images/food/korean-bibimbap.jpg' WHERE id = 47; -- Spicy Korean Bibimbap

-- Drinks
UPDATE menu_items SET image_url = 'assets/images/food/orange-juice.jpg' WHERE id = 48; -- Freshly Squeezed Orange Juice
UPDATE menu_items SET image_url = 'assets/images/food/vanilla-milkshake.jpg' WHERE id = 49; -- Creamy Vanilla Milkshake
UPDATE menu_items SET image_url = 'assets/images/food/coffee-blend.jpg' WHERE id = 50; -- Artisan Coffee Blend
UPDATE menu_items SET image_url = 'assets/images/food/green-tea-latte.jpg' WHERE id = 51; -- Iced Green Tea Latte
UPDATE menu_items SET image_url = 'assets/images/food/mango-smoothie.jpg' WHERE id = 52; -- Tropical Mango Smoothie
UPDATE menu_items SET image_url = 'assets/images/food/coca-cola.jpg' WHERE id = 53; -- Classic Coca-Cola
UPDATE menu_items SET image_url = 'assets/images/food/sparkling-water.jpg' WHERE id = 54; -- Sparkling Water with Lime

-- Desserts
UPDATE menu_items SET image_url = 'assets/images/food/chocolate-lava-cake.jpg' WHERE id = 55; -- Decadent Chocolate Lava Cake
UPDATE menu_items SET image_url = 'assets/images/food/cheesecake.jpg' WHERE id = 56; -- Classic New York Cheesecake
UPDATE menu_items SET image_url = 'assets/images/food/apple-pie.jpg' WHERE id = 57; -- Warm Apple Pie à la Mode
UPDATE menu_items SET image_url = 'assets/images/food/chocolate-brownies.jpg' WHERE id = 58; -- Double Chocolate Brownies
UPDATE menu_items SET image_url = 'assets/images/food/strawberry-ice-cream.jpg' WHERE id = 59; -- Fresh Strawberry Ice Cream
UPDATE menu_items SET image_url = 'assets/images/food/tiramisu.jpg' WHERE id = 60; -- Tiramisu Delight

-- Verify updates
SELECT id, name, image_url FROM menu_items WHERE id BETWEEN 33 AND 60 ORDER BY id;