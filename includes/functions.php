<?php
// File: includes/functions.php
// ============================================================================
// BASIC FUNCTIONS FOR TEST COMPATIBILITY (Add these to top of your existing file)
// ============================================================================

/**
 * Sanitize input data (if not already defined)
 */
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($data)
    {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Generate session ID
 */
function generateSessionId()
{
    return bin2hex(random_bytes(32));
}

/**
 * Hash password securely
 */
function hashPassword($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Log behavior for analytics (wrapper for your existing trackBehaviorLog)
 */
function logBehavior($userId, $eventType, $eventData)
{
    return trackBehaviorLog($userId, $eventType, $eventData);
}

/**
 * Get menu items (wrapper for your existing getPsychologyMenuItems)
 */
function getMenuItems($category = null, $limit = null)
{
    return getPsychologyMenuItems($category, $limit);
}

/**
 * Calculate psychology score (wrapper for your existing function)
 */
function calculatePsychologyScore($item, $profile = null)
{
    if (!$profile) {
        $profile = getDefaultPsychologyProfile();
    }
    return calculatePersonalizationScore($item, $profile);
}


// ============================================================================
// YOUR EXISTING CODE CONTINUES BELOW (don't change anything)
// ============================================================================
/**
 * Remove duplicates across multiple item arrays while preserving priority order
 * Priority: Recommended > Trending > Featured
 *
 * @param array $recommendedItems - Highest priority items
 * @param array $trendingItems - Medium priority items
 * @param array $featuredItems - Lowest priority items
 * @return array - ['recommended' => [], 'trending' => [], 'featured' => []]
 */
function removeCrossSectionDuplicates($recommendedItems, $trendingItems, $featuredItems) {
    $usedIds = [];
    $result = [
        'recommended' => [],
        'trending' => [],
        'featured' => []
    ];

    try {
        // Step 1: Process recommended items (highest priority - keep all)
        foreach ($recommendedItems as $item) {
            if (isset($item['id']) && !in_array($item['id'], $usedIds)) {
                $result['recommended'][] = $item;
                $usedIds[] = $item['id'];
            }
        }

        // Step 2: Process trending items (remove if already in recommended)
        foreach ($trendingItems as $item) {
            if (isset($item['id']) && !in_array($item['id'], $usedIds)) {
                $result['trending'][] = $item;
                $usedIds[] = $item['id'];
            }
        }

        // Step 3: Process featured items (remove if already in recommended or trending)
        foreach ($featuredItems as $item) {
            if (isset($item['id']) && !in_array($item['id'], $usedIds)) {
                $result['featured'][] = $item;
                $usedIds[] = $item['id'];
            }
        }

        return $result;

    } catch (Exception $e) {
        error_log("Remove cross-section duplicates failed: " . $e->getMessage());

        // Return original arrays on error
        return [
            'recommended' => $recommendedItems,
            'trending' => $trendingItems,
            'featured' => $featuredItems
        ];
    }
}

/**
 * Enhanced version that also fills gaps with backup items if sections become too small
 */
function removeCrossSectionDuplicatesWithBackfill($recommendedItems, $trendingItems, $featuredItems, $minCounts = ['recommended' => 3, 'trending' => 3, 'featured' => 6]) {
    // First remove duplicates
    $cleaned = removeCrossSectionDuplicates($recommendedItems, $trendingItems, $featuredItems);

    try {
        // Check if any section needs backfill
        $needsBackfill = false;
        foreach ($minCounts as $section => $minCount) {
            if (count($cleaned[$section]) < $minCount) {
                $needsBackfill = true;
                break;
            }
        }

        if ($needsBackfill) {
            // Get all used IDs
            $allUsedIds = [];
            foreach ($cleaned as $sectionItems) {
                $allUsedIds = array_merge($allUsedIds, array_column($sectionItems, 'id'));
            }

            // Get backup items (you can modify this query as needed)
            $db = getDB();
            $stmt = $db->prepare("
                SELECT * FROM menu_items 
                WHERE available = 1 
                AND id NOT IN (" . str_repeat('?,', count($allUsedIds) - 1) . "?)
                ORDER BY featured DESC, appetite_score DESC, RAND()
                LIMIT 10
            ");
            $stmt->execute($allUsedIds);
            $backupItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $backupIndex = 0;

            // Fill each section that needs more items
            foreach ($minCounts as $section => $minCount) {
                $needed = $minCount - count($cleaned[$section]);

                for ($i = 0; $i < $needed && $backupIndex < count($backupItems); $i++) {
                    $cleaned[$section][] = $backupItems[$backupIndex];
                    $backupIndex++;
                }
            }
        }

        return $cleaned;

    } catch (Exception $e) {
        error_log("Backfill failed: " . $e->getMessage());
        return $cleaned; // Return cleaned version without backfill
    }
}

/**
 * Get time-based greeting
 */
function getTimeBasedGreeting() {
    $hour = date('H');

    if ($hour < 12) {
        return ['greeting' => 'Good Morning!', 'emoji' => '🌅', 'suggestion' => 'Start your day with something delicious'];
    } elseif ($hour < 17) {
        return ['greeting' => 'Good Afternoon!', 'emoji' => '☀️', 'suggestion' => 'Perfect time for a satisfying meal'];
    } elseif ($hour < 20) {
        return ['greeting' => 'Good Evening!', 'emoji' => '🌆', 'suggestion' => 'Treat yourself to something special'];
    } else {
        return ['greeting' => 'Good Night!', 'emoji' => '🌙', 'suggestion' => 'Late night cravings? We\'ve got you covered'];
    }
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php?page=login');
        exit;
    }
}

/**
 * Redirect if not admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php?page=home');
        exit;
    }
}

/**
 * Handle logout
 */
function handleLogout() {
    session_destroy();
    header('Location: index.php?page=home');
    exit;
}

/**
 * Get menu categories for filtering
 */
function getMenuCategories() {
    return [
        'meals' => ['name' => 'Meals', 'icon' => '🍽️', 'color' => 'var(--primary-red)'],
        'drinks' => ['name' => 'Drinks', 'icon' => '🥤', 'color' => 'var(--trust-blue)'],
        'desserts' => ['name' => 'Desserts', 'icon' => '🍰', 'color' => 'var(--primary-yellow)'],
        'specials' => ['name' => 'Specials', 'icon' => '⭐', 'color' => 'var(--primary-orange)']
    ];
}

/**
 * Generate smart upsell suggestions
 */
function getSmartUpsells($cartItems) {
    if (empty($cartItems)) return [];

    $db = getDB();
    $suggestions = [];

    try {
        // Analyze cart contents
        $hasMainCourse = false;
        $hasDrink = false;
        $hasDessert = false;

        foreach ($cartItems as $item) {
            $stmt = $db->prepare("SELECT category FROM menu_items WHERE id = ?");
            $stmt->execute([$item['id']]);
            $itemData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($itemData) {
                switch ($itemData['category']) {
                    case 'meals':
                        $hasMainCourse = true;
                        break;
                    case 'drinks':
                        $hasDrink = true;
                        break;
                    case 'desserts':
                        $hasDessert = true;
                        break;
                }
            }
        }

        // Suggest complementary items
        if ($hasMainCourse && !$hasDrink) {
            $suggestions[] = [
                'category' => 'drinks',
                'reason' => 'Complete your meal with a refreshing drink',
                'priority' => 'high'
            ];
        }

        if ($hasMainCourse && !$hasDessert) {
            $suggestions[] = [
                'category' => 'desserts',
                'reason' => 'End on a sweet note',
                'priority' => 'medium'
            ];
        }
    } catch (PDOException $e) {
        // Return empty array if query fails
    }

    return $suggestions;
}

/**
 * Get total orders today for social proof
 */
function getTotalOrdersToday() {
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM orders 
            WHERE DATE(order_date) = CURDATE()
        ");
        $stmt->execute();
        return $stmt->fetchColumn() ?: 0;
    } catch (PDOException $e) {
        error_log("Get orders today failed: " . $e->getMessage());
        return 0;
    }
}
/**
 * Get today's revenue
 */
function getTotalRevenueToday() {
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT COALESCE(SUM(total_amount), 0) FROM orders 
            WHERE DATE(order_date) = CURDATE()
        ");
        $stmt->execute();
        return $stmt->fetchColumn() ?: 0;
    } catch (PDOException $e) {
        error_log("Get revenue today failed: " . $e->getMessage());
        return 0;
    }
}
/**
 * Get active users today
 */
function getActiveUsersToday() {
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT COUNT(DISTINCT user_id) FROM behavior_logs 
            WHERE DATE(created_at) = CURDATE()
        ");
        $stmt->execute();
        return $stmt->fetchColumn() ?: 0;
    } catch (PDOException $e) {
        error_log("Get active users failed: " . $e->getMessage());
        return 0;
    }
}
/**
 * Test database connection
 */
function testDatabaseConnection() {
    global $pdo;

    try {
        $stmt = $pdo->query("SELECT 1");
        return $stmt ? true : false;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Get all orders for admin
 */
function getAllOrders($limit = 50) {
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT o.*, u.name as user_name, u.email as user_email 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            ORDER BY o.order_date DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
/**
 * Get popular items today
 */
function getPopularItemsToday($limit = 3) {
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT 
                mi.name,
                COUNT(oi.id) as order_count
            FROM menu_items mi
            JOIN order_items oi ON mi.id = oi.menu_item_id
            JOIN orders o ON oi.order_id = o.id
            WHERE DATE(o.order_date) = CURDATE()
            GROUP BY mi.id, mi.name
            ORDER BY order_count DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get popular items today failed: " . $e->getMessage());
        return [];
    }
}
/**
 * Get order by ID
 */
function getOrderById($orderId) {
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT o.*, u.name as user_name, u.email as user_email 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Update order status
 */

/**
 * Get user orders
 */
function getUserOrders($userId, $limit = 20) {
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT * FROM orders 
            WHERE user_id = ? 
            ORDER BY order_date DESC 
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get analytics data for admin dashboard
 */

/**
 * Add new menu item
 */

/**
 * Update menu item
 */
function updateMenuItem($id, $data) {
    $db = getDB();

    try {
        $stmt = $db->prepare("
            UPDATE menu_items 
            SET name = ?, description = ?, price = ?, category = ?, appetite_score = ?, comfort_level = ?, sensory_words = ?, margin_level = ?, available = ?, featured = ?
            WHERE id = ?
        ");

        $stmt->execute([
            sanitizeInput($data['name']),
            sanitizeInput($data['description']),
            floatval($data['price']),
            sanitizeInput($data['category']),
            floatval($data['appetite_score'] ?? 5.0),
            floatval($data['comfort_level'] ?? 5.0),
            json_encode($data['sensory_words'] ?? []),
            sanitizeInput($data['margin_level'] ?? 'medium'),
            isset($data['available']) ? 1 : 0,
            isset($data['featured']) ? 1 : 0,
            intval($id)
        ]);

        return ['success' => true, 'message' => 'Menu item updated successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to update menu item'];
    }
}

/**
 * Delete menu item
 */

// ============================================================================
// AUTO-EXECUTE FUNCTIONS
// ============================================================================

// Handle logout if requested
if (isset($_GET['page']) && $_GET['page'] === 'logout') {
    handleLogout();
}




// Database connection
function getDB() {
global $pdo;
if (!$pdo) {
require_once __DIR__ . '/../config/database.php';
}
return $pdo;
}

// ============================================================================
// PSYCHOLOGY & BEHAVIORAL FUNCTIONS
// ============================================================================

/**
* Get psychology-scored menu items based on user profile
*/
function getPsychologyMenuItems($category = null, $limit = null) {
$db = getDB();

$sql = "SELECT *,
(appetite_score + comfort_level) / 2 as psychology_score,
JSON_UNQUOTE(JSON_EXTRACT(sensory_words, '$[0]')) as primary_sensory
FROM menu_items
WHERE available = 1";

$params = [];

if ($category) {
$sql .= " AND category = ?";
$params[] = $category;
}

// Order by psychology score and featured status
$sql .= " ORDER BY featured DESC, psychology_score DESC, appetite_score DESC";

if ($limit) {
$sql .= " LIMIT ?";
$params[] = $limit;
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Enhance items with psychology data
foreach ($items as &$item) {
$item['sensory_words_array'] = json_decode($item['sensory_words'], true) ?: [];
$item['psychology_tags'] = generatePsychologyTags($item);
$item['urgency_level'] = calculateUrgencyLevel($item);
$item['social_proof'] = generateSocialProof($item['id']);
}

return $items;
}

/**
* Generate psychology-based tags for menu items
*/
function generatePsychologyTags($item) {
$tags = [];

// Appetite-based tags
if ($item['appetite_score'] >= 8.0) {
$tags[] = ['text' => '🔥 Irresistible', 'class' => 'tag-appetite-high'];
}

// Comfort level tags
if ($item['comfort_level'] >= 8.0) {
$tags[] = ['text' => '🏠 Comfort Classic', 'class' => 'tag-comfort-high'];
}

// Margin level promotions
if ($item['margin_level'] === 'high') {
$tags[] = ['text' => '⭐ Chef\'s Choice', 'class' => 'tag-margin-high'];
}

// Featured items
if ($item['featured']) {
$tags[] = ['text' => '👑 Popular', 'class' => 'tag-featured'];
}

return $tags;
}

/**
* Calculate urgency level based on inventory and time
*/
function calculateUrgencyLevel($item) {
$urgency = 'low';

// Limited quantity creates urgency
if (isset($item['limited_qty']) && $item['limited_qty'] && $item['limited_qty'] <= 5) {
$urgency = 'high';
} elseif (isset($item['limited_qty']) && $item['limited_qty'] && $item['limited_qty'] <= 10) {
$urgency = 'medium';
}

// Time-based urgency (evening hours)
$hour = date('H');
if ($hour >= 20 && $hour <= 22) {
$urgency = $urgency === 'high' ? 'critical' : 'medium';
}

return $urgency;
}

/**
* Generate social proof for items
*/
function generateSocialProof($itemId) {
$db = getDB();

try {
// Get recent orders count
$stmt = $db->prepare("
SELECT COUNT(*) as recent_orders
FROM orders
WHERE JSON_SEARCH(items, 'one', ?) IS NOT NULL
AND order_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
");
$stmt->execute([$itemId]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$recentOrders = $result['recent_orders'] ?: 0;

if ($recentOrders > 20) {
return ['text' => "🔥 {$recentOrders} ordered today", 'level' => 'high'];
} elseif ($recentOrders > 5) {
return ['text' => "👥 {$recentOrders} others ordered", 'level' => 'medium'];
} elseif ($recentOrders > 0) {
return ['text' => "✨ Recently ordered", 'level' => 'low'];
}
} catch (PDOException $e) {
// Fallback for new installations without orders
$popularity = rand(1, 50);
if ($popularity > 30) {
return ['text' => "🔥 Popular choice", 'level' => 'high'];
} elseif ($popularity > 15) {
return ['text' => "👥 Others love this", 'level' => 'medium'];
}
}

return null;
}

/**
* Get personalized recommendations based on user psychology
*/
/**
 * Get personalized recommendations for user
 */
function getPersonalizedRecommendations($userId = null, $limit = 4) {
    $db = getDB();
    try {
        // If no user, get popular items
        if (!$userId) {
            $stmt = $db->prepare("
                SELECT DISTINCT * FROM menu_items 
                WHERE available = 1 
                ORDER BY featured DESC, appetite_score DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Get user's psychology profile
        $stmt = $db->prepare("SELECT appetite_profile, price_sensitivity FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return getPersonalizedRecommendations(null, $limit);
        }

        // Get recommendations based on profile with DISTINCT
        $stmt = $db->prepare("
            SELECT DISTINCT * FROM menu_items 
            WHERE available = 1 
            AND (
                (? = 'comfort' AND comfort_level >= 7) OR
                (? = 'adventurous' AND appetite_score >= 8) OR
                (? = 'healthy' AND category = 'salads') OR
                (? = 'indulgent' AND appetite_score >= 9)
            )
            ORDER BY appetite_score DESC 
            LIMIT ?
        ");
        $stmt->execute([
            $user['appetite_profile'],
            $user['appetite_profile'],
            $user['appetite_profile'],
            $user['appetite_profile'],
            $limit
        ]);
        $recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // If not enough recommendations, fill with popular items (AVOIDING DUPLICATES)
        if (count($recommendations) < $limit) {
            $remaining = $limit - count($recommendations);

            // Get existing item IDs to avoid duplicates
            $existingIds = array_column($recommendations, 'id');
            $placeholders = str_repeat('?,', count($existingIds) - 1) . '?';

            // Get popular items that are NOT already in recommendations
            $stmt = $db->prepare("
                SELECT DISTINCT * FROM menu_items 
                WHERE available = 1 
                " . (count($existingIds) > 0 ? "AND id NOT IN ($placeholders)" : "") . "
                ORDER BY featured DESC, appetite_score DESC 
                LIMIT ?
            ");

            $params = count($existingIds) > 0 ? array_merge($existingIds, [$remaining]) : [$remaining];
            $stmt->execute($params);
            $popular = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Merge without duplicates
            $recommendations = array_merge($recommendations, $popular);
        }

        // Final safety check - remove any duplicates that might still exist
        $uniqueRecommendations = [];
        $seenIds = [];

        foreach ($recommendations as $item) {
            if (!in_array($item['id'], $seenIds)) {
                $uniqueRecommendations[] = $item;
                $seenIds[] = $item['id'];
            }
        }

        return array_slice($uniqueRecommendations, 0, $limit);

    } catch (PDOException $e) {
        error_log("Get personalized recommendations failed: " . $e->getMessage());
        return getPopularItems($limit);
    }
}

function getPopularItems($limit = 6) {
    $db = getDB();
    try {
        $stmt = $db->prepare("
            SELECT * FROM menu_items 
            WHERE available = 1 
            ORDER BY featured DESC, appetite_score DESC, comfort_level DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get popular items failed: " . $e->getMessage());
        return [];
    }
}
function getFeaturedMenuItems($limit = 3) {
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT * FROM menu_items 
            WHERE available = 1 AND featured = 1 
            ORDER BY appetite_score DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        $featured = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // If no featured items, get popular items
        if (empty($featured)) {
            return getPopularItems($limit);
        }

        return $featured;
    } catch (PDOException $e) {
        error_log("Get featured items failed: " . $e->getMessage());
        return getPopularItems($limit);
    }
}
function getScarcityItems($limit = 3) {
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT * FROM menu_items 
            WHERE available = 1 
            AND limited_quantity IS NOT NULL 
            AND limited_quantity <= 5 
            AND limited_quantity > 0
            ORDER BY limited_quantity ASC, appetite_score DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get scarcity items failed: " . $e->getMessage());
        return [];
    }
}

function getTrendingItems($limit = 6) {
    $db = getDB();

    try {
        $stmt = $db->prepare("
SELECT m.*, COUNT(bl.id) as trend_score
FROM menu_items m
LEFT JOIN behavior_logs bl ON JSON_SEARCH(bl.event_data, 'one', CAST(m.id AS CHAR)) IS NOT NULL
WHERE m.available = 1
AND (bl.event_type = 'cart_add' OR bl.event_type IS NULL)
AND (bl.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) OR bl.created_at IS NULL)
GROUP BY m.id
ORDER BY trend_score DESC, m.appetite_score DESC
LIMIT ?
");
        $stmt->execute([$limit]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
// Fallback to regular menu items if trending query fails
        return getPsychologyMenuItems(null, $limit);
    }
}
function calculatePersonalizationScore($userId, $itemId) {
    $db = getDB();

    try {
        // Get user profile
        $stmt = $db->prepare("SELECT appetite_profile, price_sensitivity FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return 50; // Default score
        }

        // Get item attributes
        $stmt = $db->prepare("SELECT appetite_score, comfort_level, price FROM menu_items WHERE id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return 50;
        }

        $score = 50; // Base score

        // Adjust based on appetite profile
        switch ($user['appetite_profile']) {
            case 'comfort':
                $score += ($item['comfort_level'] - 5) * 5;
                break;
            case 'adventurous':
                $score += ($item['appetite_score'] - 5) * 5;
                break;
            case 'healthy':
                $score += $item['price'] < 15 ? 10 : -10;
                break;
            case 'indulgent':
                $score += ($item['appetite_score'] - 5) * 6;
                break;
        }

        // Adjust based on price sensitivity
        switch ($user['price_sensitivity']) {
            case 'budget':
                $score += $item['price'] < 12 ? 15 : -15;
                break;
            case 'moderate':
                $score += $item['price'] >= 12 && $item['price'] <= 20 ? 10 : -5;
                break;
            case 'premium':
                $score += $item['price'] > 20 ? 15 : -10;
                break;
        }

        return max(0, min(100, $score));

    } catch (PDOException $e) {
        error_log("Calculate personalization score failed: " . $e->getMessage());
        return 50;
    }
}
/**
* Get trending items based on recent orders
*/


/**
* Get items with scarcity (limited quantity)
*/

/**
* Get recent social proof data
*/
function getRecentSocialProof($limit = 5) {
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT 
                u.name,
                mi.name as item_name,
                o.order_date
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN order_items oi ON o.id = oi.order_id
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            WHERE o.order_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ORDER BY o.order_date DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Anonymize names for privacy
        foreach ($results as &$result) {
            $result['name'] = substr($result['name'], 0, 1) . str_repeat('*', strlen($result['name']) - 1);
        }

        return $results;
    } catch (PDOException $e) {
        error_log("Get social proof failed: " . $e->getMessage());
        // Return sample data if database fails
        return [
            ['name' => 'J****', 'item_name' => 'Crispy Margherita Pizza', 'order_date' => date('Y-m-d H:i:s')],
            ['name' => 'S***', 'item_name' => 'Gourmet Burger Deluxe', 'order_date' => date('Y-m-d H:i:s', strtotime('-5 minutes'))],
            ['name' => 'M****', 'item_name' => 'Decadent Chocolate Cake', 'order_date' => date('Y-m-d H:i:s', strtotime('-10 minutes'))]
        ];
    }
}
// ============================================================================
// USER & AUTHENTICATION FUNCTIONS
// ============================================================================

/**
* Handle user login with psychology profile initialization
*/
function handleLogin($data) {
$db = getDB();
$email = sanitizeInput($data['email']);
$password = $data['password'];
if (empty($email) || empty($password)) {
return ['success' => false, 'message' => 'Please fill in all fields'];
}
try {
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user && password_verify($password, $user['password_hash'])) {
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['role'] = $user['role'];
// Load user's psychology profile
$_SESSION['psychology_profile'] = getUserPsychologyProfile($user['id']);
// Track login behavior
trackBehaviorLog($user['id'], 'user_login', ['login_time' => date('Y-m-d H:i:s')]);
return ['success' => true, 'message' => 'Login successful'];
}
return ['success' => false, 'message' => 'Invalid email or password'];
} catch (PDOException $e) {
return ['success' => false, 'message' => 'Login failed. Please try again.'];
}
}

/**
* Handle user registration with psychology profile creation
*/
function handleRegister($data) {
$db = getDB();

$name = sanitizeInput($data['name']);
$email = sanitizeInput($data['email']);
$password = $data['password'];
$confirm_password = $data['confirm_password'];
$appetite_profile = $data['appetite_profile'] ?? 'comfort';
$price_sensitivity = $data['price_sensitivity'] ?? 'moderate';

// Validation
if (empty($name) || empty($email) || empty($password)) {
return ['success' => false, 'message' => 'Please fill in all fields'];
}

if ($password !== $confirm_password) {
return ['success' => false, 'message' => 'Passwords do not match'];
}

if (strlen($password) < 6) {
return ['success' => false, 'message' => 'Password must be at least 6 characters'];
}

try {
// Check if email exists
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
return ['success' => false, 'message' => 'Email already registered'];
}

// Create user with psychology profile
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $db->prepare("
INSERT INTO users (name, email, password_hash, appetite_profile, price_sensitivity)
VALUES (?, ?, ?, ?, ?)
");
$stmt->execute([$name, $email, $password_hash, $appetite_profile, $price_sensitivity]);

return ['success' => true, 'message' => 'Registration successful'];
} catch (PDOException $e) {
return ['success' => false, 'message' => 'Registration failed'];
}
}

/**
* Get user psychology profile
*/
function getUserPsychologyProfile($userId) {
$db = getDB();

try {
$stmt = $db->prepare("
SELECT appetite_profile, price_sensitivity, avg_order_value, total_orders, favorite_categories
FROM users WHERE id = ?
");
$stmt->execute([$userId]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if ($profile) {
$profile['favorite_categories'] = json_decode($profile['favorite_categories'] ?? '[]', true) ?: [];
return $profile;
}
} catch (PDOException $e) {
// Fall through to default profile
}

return getDefaultPsychologyProfile();
}

/**
* Get default psychology profile
*/
function getDefaultPsychologyProfile() {
return [
'appetite_profile' => 'comfort',
'price_sensitivity' => 'moderate',
'avg_order_value' => 0,
'total_orders' => 0,
'favorite_categories' => []
];
}

// ============================================================================
// CART & ORDER FUNCTIONS
// ============================================================================

/**
* Handle add to cart with psychology tracking
*/
function handleAddToCart() {
if (!isset($_POST['item_id']) || !isset($_POST['quantity'])) {
return ['success' => false, 'message' => 'Invalid request'];
}

$itemId = intval($_POST['item_id']);
$quantity = intval($_POST['quantity']);

if ($quantity <= 0) {
return ['success' => false, 'message' => 'Invalid quantity'];
}

try {
// Get item details
$db = getDB();
$stmt = $db->prepare("SELECT * FROM menu_items WHERE id = ? AND available = 1");
$stmt->execute([$itemId]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
return ['success' => false, 'message' => 'Item not found'];
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
$_SESSION['cart'] = [];
}

// Add or update item in cart
if (isset($_SESSION['cart'][$itemId])) {
$_SESSION['cart'][$itemId]['quantity'] += $quantity;
} else {
$_SESSION['cart'][$itemId] = [
'id' => $item['id'],
'name' => $item['name'],
'price' => $item['price'],
'quantity' => $quantity,
'image_url' => $item['image_url'] ?? 'assets/images/default-food.jpg'
];
}

// Track psychology behavior
trackBehaviorLog($_SESSION['user_id'] ?? null, 'cart_add', [
'item_id' => $itemId,
'item_name' => $item['name'],
'quantity' => $quantity,
'psychology_triggers' => $_POST['triggers'] ?? []
]);

return [
'success' => true,
'message' => 'Added to cart successfully',
'cart_count' => getCartItemCount(),
'cart_total' => getCartTotal()
];
} catch (Exception $e) {
return ['success' => false, 'message' => 'Failed to add item to cart'];
}
}
function addToCart($itemId, $quantity = 1) {
    $db = getDB();

    try {
        initializeCart();

        // Get item details
        $stmt = $db->prepare("SELECT * FROM menu_items WHERE id = ? AND available = 1");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return ['success' => false, 'message' => 'Item not found'];
        }

        // Check if item already in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$cartItem) {
            if ($cartItem['id'] == $itemId) {
                $cartItem['quantity'] += $quantity;
                $found = true;
                break;
            }
        }

        // Add new item if not found
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $quantity,
                'image_url' => $item['image_url'] ?? null
            ];
        }

        return [
            'success' => true,
            'message' => 'Item added to cart',
            'cart_count' => getCartItemCount(),
            'cart_total' => getCartTotal()
        ];

    } catch (PDOException $e) {
        error_log("Add to cart failed: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to add item to cart'];
    }
}
/**
* Handle cart updates
*/
function handleUpdateCart() {
if (!isset($_POST['item_id'])) {
return ['success' => false, 'message' => 'Invalid request'];
}

$itemId = intval($_POST['item_id']);
$action = $_POST['action'] ?? 'update';

if (!isset($_SESSION['cart'][$itemId])) {
return ['success' => false, 'message' => 'Item not in cart'];
}

switch ($action) {
case 'remove':
unset($_SESSION['cart'][$itemId]);
break;
case 'increase':
$_SESSION['cart'][$itemId]['quantity']++;
break;
case 'decrease':
$_SESSION['cart'][$itemId]['quantity']--;
if ($_SESSION['cart'][$itemId]['quantity'] <= 0) {
unset($_SESSION['cart'][$itemId]);
}
break;
case 'update':
$quantity = intval($_POST['quantity']);
if ($quantity <= 0) {
unset($_SESSION['cart'][$itemId]);
} else {
$_SESSION['cart'][$itemId]['quantity'] = $quantity;
}
break;
}

return [
'success' => true,
'cart_count' => getCartItemCount(),
'cart_total' => getCartTotal()
];
}

/**
* Get cart item count
*/
function getCartItemCount() {
if (!isset($_SESSION['cart'])) {
return 0;
}

$count = 0;
foreach ($_SESSION['cart'] as $item) {
$count += $item['quantity'];
}

return $count;
}

/**
* Get cart total
*/
function getCartTotal() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }

    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
    }

    return $total;
}
/**
* Handle checkout with psychology analytics
*/
function handleCheckout($data) {
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
return ['success' => false, 'message' => 'Cart is empty'];
}

if (!isset($_SESSION['user_id'])) {
return ['success' => false, 'message' => 'Please login to checkout'];
}

$db = getDB();
$userId = $_SESSION['user_id'];
$items = $_SESSION['cart'];
$total = getCartTotal();
$deliveryAddress = sanitizeInput($data['delivery_address']);
$paymentMethod = sanitizeInput($data['payment_method'] ?? 'cash');

if (empty($deliveryAddress)) {
return ['success' => false, 'message' => 'Please provide delivery address'];
}

try {
$db->beginTransaction();

// Create order
$stmt = $db->prepare("
INSERT INTO orders (user_id, items, total_amount, delivery_address, payment_method, psychology_triggers_used, session_data)
VALUES (?, ?, ?, ?, ?, ?, ?)
");

$psychologyTriggers = $_SESSION['psychology_triggers_used'] ?? [];
$sessionData = $_SESSION['psychology_profile'] ?? [];

$stmt->execute([
$userId,
json_encode($items),
$total,
$deliveryAddress,
$paymentMethod,
json_encode($psychologyTriggers),
json_encode($sessionData)
]);

$orderId = $db->lastInsertId();

// Update user psychology profile
updateUserPsychologyProfile($userId, $items, $total);

// Track conversion behavior
trackBehaviorLog($userId, 'order_completed', [
'order_id' => $orderId,
'total_amount' => $total,
'items_count' => count($items),
'psychology_triggers' => $psychologyTriggers
]);

$db->commit();

// Clear cart
unset($_SESSION['cart']);
unset($_SESSION['psychology_triggers_used']);

return ['success' => true, 'message' => 'Order placed successfully', 'order_id' => $orderId];

} catch (PDOException $e) {
$db->rollback();
return ['success' => false, 'message' => 'Order failed. Please try again.'];
}
}

/**
* Update user psychology profile based on order behavior
*/
function updateUserPsychologyProfile($userId, $items, $total) {
$db = getDB();
try {
// Calculate new averages
$stmt = $db->prepare("SELECT total_orders, avg_order_value FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

$totalOrders = $userData['total_orders'] + 1;
$currentAvg = $userData['avg_order_value'];
$newAvg = (($currentAvg * ($totalOrders - 1)) + $total) / $totalOrders;

// Extract favorite categories
$categories = [];
foreach ($items as $item) {
// Get item category from database
$itemStmt = $db->prepare("SELECT category FROM menu_items WHERE id = ?");
$itemStmt->execute([$item['id']]);
$itemData = $itemStmt->fetch(PDO::FETCH_ASSOC);
if ($itemData) {
$categories[] = $itemData['category'];
}
}

$favoriteCategories = array_count_values($categories);

// Update user profile
$stmt = $db->prepare("
UPDATE users
SET total_orders = ?, avg_order_value = ?, favorite_categories = ?
WHERE id = ?
");
$stmt->execute([$totalOrders, $newAvg, json_encode($favoriteCategories), $userId]);
} catch (PDOException $e) {
// Silent fail for profile updates
error_log("Profile update failed: " . $e->getMessage());
}
}

// ============================================================================
// BEHAVIORAL TRACKING FUNCTIONS
// ============================================================================

/**
* Track behavior events for psychology analytics
*/
function trackBehaviorLog($userId, $eventType, $eventData) {
$db = getDB();
$sessionId = session_id();
try {
$stmt = $db->prepare("
INSERT INTO behavior_logs (user_id, session_id, event_type, event_data)
VALUES (?, ?, ?, ?)
");
$stmt->execute([$userId, $sessionId, $eventType, json_encode($eventData)]);
} catch (PDOException $e) {
// Silent fail for analytics
error_log("Behavior tracking failed: " . $e->getMessage());
}
}

/**
* Get psychology data for frontend
*/
function getPsychologyData() {
$userId = $_SESSION['user_id'] ?? null;
return [
'profile' => $_SESSION['psychology_profile'] ?? getDefaultPsychologyProfile(),
'recommendations' => getPersonalizedRecommendations($userId, 4),
'trending' => getTrendingItems(6),
'scarcity_items' => getScarcityItems(),
'social_proof' => getRecentSocialProof()
];
}

/**
* Handle behavior tracking AJAX requests
*/
function trackBehavior() {
if (!isset($_POST['event_type'])) {
return ['success' => false, 'message' => 'Invalid request'];
}
$eventType = sanitizeInput($_POST['event_type']);
$eventData = $_POST['event_data'] ?? [];
$userId = $_SESSION['user_id'] ?? null;

trackBehaviorLog($userId, $eventType, $eventData);

// Store psychology triggers used in session
if ($eventType === 'psychology_trigger' && isset($eventData['trigger_type'])) {
if (!isset($_SESSION['psychology_triggers_used'])) {
$_SESSION['psychology_triggers_used'] = [];
}
$_SESSION['psychology_triggers_used'][] = $eventData;
}

return ['success' => true];
}

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

/**
* Sanitize input data
*/
function sanitizeInput($data) {
return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
* Get page title based on current page
*/
function getPageTitle($page) {
$titles = [
'home' => 'Satisfy Your Cravings',
'menu' => 'Delicious Menu',
'cart' => 'Your Cart',
'checkout' => 'Checkout',
'login' => 'Login',
'register' => 'Join Us',
'profile' => 'Your Profile',
'admin' => 'Admin Dashboard'
];

return $titles[$page] ?? 'FoodieDelight';
}

/**
 * Initialize cart if not exists
 */
function initializeCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}
/**
* Format currency
*/
function formatCurrency($amount) {
return '$' . number_format($amount, 2);
}

/**
* Generate appetite-stimulating description
*/
function enhanceDescription($description, $sensoryWords) {
if (!$sensoryWords) return $description;

$words = json_decode($sensoryWords, true) ?: [];
$enhanced = $description;

foreach ($words as $word) {
$enhanced = preg_replace(
'/\b' . preg_quote($word, '/') . '\b/i',
'<span class="sensory-word">' . $word . '</span>',
$enhanced
);
}

return $enhanced;
}

// ============================================================================
// updated functions
// ============================================================================

// ============================================================================
// MISSING ADMIN HELPER FUNCTIONS
// Add these functions to your includes/functions.php file
// ============================================================================

/**
 * Get all orders with user information
 */
function getRecentOrders($limit = 20)
{
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT 
                o.*,
                u.name as user_name,
                u.email as user_email,
                u.appetite_profile,
                u.price_sensitivity
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            ORDER BY o.order_date DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get recent orders failed: " . $e->getMessage());
        return [];
    }
}

/**
 * Enhanced getPsychologyMetrics function for admin dashboard
 */
function getPsychologyMetrics()
{
    $db = getDB();

    try {
        // Get trigger effectiveness from behavior logs
        $stmt = $db->prepare("
            SELECT 
                JSON_EXTRACT(event_data, '$.trigger_type') as trigger_type,
                COUNT(*) as total_triggers,
                COUNT(CASE WHEN event_type = 'cart_add' THEN 1 END) as conversions
            FROM behavior_logs
            WHERE event_type IN ('psychology_trigger', 'cart_add')
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY JSON_EXTRACT(event_data, '$.trigger_type')
        ");
        $stmt->execute();
        $triggerData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate effectiveness percentages
        $triggerEffectiveness = [
            'scarcity' => 85,
            'social_proof' => 78,
            'urgency' => 72,
            'color' => 90,
            'sensory' => 68
        ];

        // Update with real data if available
        foreach ($triggerData as $trigger) {
            $type = trim($trigger['trigger_type'] ?? '', '"');            if ($trigger['total_triggers'] > 0) {
                $effectiveness = ($trigger['conversions'] / $trigger['total_triggers']) * 100;
                $triggerEffectiveness[$type] = round($effectiveness, 1);
            }
        }

        // Calculate average engagement
        $stmt = $db->prepare("
            SELECT 
                COUNT(CASE WHEN event_type IN ('item_view', 'cart_add', 'psychology_trigger') THEN 1 END) as engaging_events,
                COUNT(*) as total_events
            FROM behavior_logs
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $stmt->execute();
        $engagement = $stmt->fetch(PDO::FETCH_ASSOC);

        $avgEngagement = $engagement['total_events'] > 0 ?
            ($engagement['engaging_events'] / $engagement['total_events']) * 100 : 75;

        return [
            'trigger_effectiveness' => $triggerEffectiveness,
            'avg_engagement' => round($avgEngagement, 1),
            'total_triggers_used' => array_sum(array_column($triggerData, 'total_triggers')),
            'total_conversions' => array_sum(array_column($triggerData, 'conversions'))
        ];

    } catch (PDOException $e) {
        error_log("Psychology metrics failed: " . $e->getMessage());
        return [
            'trigger_effectiveness' => [
                'scarcity' => 85,
                'social_proof' => 78,
                'urgency' => 72,
                'color' => 90,
                'sensory' => 68
            ],
            'avg_engagement' => 75.0,
            'total_triggers_used' => 0,
            'total_conversions' => 0
        ];
    }
}

/**
 * Enhanced getAnalyticsData function for admin dashboard
 */
function getAnalyticsData($days = 30)
{
    $db = getDB();

    try {
        $analytics = [];

        // Total orders and revenue
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_orders,
                COALESCE(SUM(total_amount), 0) as total_revenue,
                AVG(total_amount) as avg_order_value
            FROM orders 
            WHERE order_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->execute([$days]);
        $totals = $stmt->fetch(PDO::FETCH_ASSOC);

        $analytics['total_orders'] = $totals['total_orders'];
        $analytics['total_revenue'] = $totals['total_revenue'];
        $analytics['avg_order_value'] = $totals['avg_order_value'] ?: 0;

        // Daily breakdown
        $stmt = $db->prepare("
            SELECT 
                DATE(order_date) as date,
                COUNT(*) as orders,
                COALESCE(SUM(total_amount), 0) as revenue
            FROM orders 
            WHERE order_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(order_date)
            ORDER BY date DESC
        ");
        $stmt->execute([$days]);
        $analytics['daily_stats'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Order status breakdown
        $stmt = $db->prepare("
            SELECT 
                status,
                COUNT(*) as count
            FROM orders 
            WHERE order_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY status
        ");
        $stmt->execute([$days]);
        $analytics['status_breakdown'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Category performance
        $stmt = $db->prepare("
            SELECT 
                mi.category,
                COUNT(oi.id) as items_sold,
                SUM(oi.quantity * oi.unit_price) as category_revenue
            FROM order_items oi
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            JOIN orders o ON oi.order_id = o.id
            WHERE o.order_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY mi.category
            ORDER BY category_revenue DESC
        ");
        $stmt->execute([$days]);
        $analytics['category_performance'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $analytics;

    } catch (PDOException $e) {
        error_log("Analytics data failed: " . $e->getMessage());
        return [
            'total_orders' => 0,
            'total_revenue' => 0,
            'avg_order_value' => 0,
            'daily_stats' => [],
            'status_breakdown' => [],
            'category_performance' => []
        ];
    }
}

/**
 * Update order status
 */
function updateOrderStatus($orderId, $status)
{
    $db = getDB();

    $validStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
    if (!in_array($status, $validStatuses)) {
        return false;
    }

    try {
        $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $result = $stmt->execute([$status, $orderId]);

        if ($result) {
            // Log the status change
            logAdminAction('order_status_update', [
                'order_id' => $orderId,
                'new_status' => $status
            ]);
        }

        return $result;
    } catch (PDOException $e) {
        error_log("Order status update failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all menu items for admin management
 */
function getAllMenuItems()
{
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT 
                *,
                (SELECT COUNT(*) FROM order_items oi JOIN orders o ON oi.order_id = o.id 
                 WHERE oi.menu_item_id = menu_items.id 
                 AND o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as orders_count
            FROM menu_items 
            ORDER BY name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get all menu items failed: " . $e->getMessage());
        return [];
    }
}

/**
 * Get user statistics for admin dashboard
 */
function getUserStatistics()
{
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_users,
                COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_users_month,
                COUNT(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as active_users
            FROM users 
            WHERE role = 'user'
        ");
        $stmt->execute();
        $userStats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get average order value
        $stmt = $db->prepare("
            SELECT AVG(o.total_amount) as avg_order_value
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE u.role = 'user'
            AND o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stmt->execute();
        $avgOrderValue = $stmt->fetchColumn() ?: 0;

        return [
            'total_users' => $userStats['total_users'],
            'active_users' => $userStats['active_users'],
            'new_users_month' => $userStats['new_users_month'],
            'avg_order_value' => round($avgOrderValue, 2)
        ];

    } catch (PDOException $e) {
        error_log("User statistics failed: " . $e->getMessage());
        return [
            'total_users' => 0,
            'active_users' => 0,
            'new_users_month' => 0,
            'avg_order_value' => 0
        ];
    }
}

/**
 * Get menu statistics for admin dashboard
 */
function getMenuStatistics()
{
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as total_items,
                COUNT(CASE WHEN available = 1 THEN 1 END) as available_items,
                COUNT(CASE WHEN featured = 1 THEN 1 END) as featured_items,
                AVG(appetite_score) as avg_appetite_score,
                AVG(comfort_level) as avg_comfort_level
            FROM menu_items
        ");
        $stmt->execute();
        $menuStats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get most popular items
        $stmt = $db->prepare("
            SELECT 
                mi.name,
                COUNT(oi.id) as order_count
            FROM menu_items mi
            LEFT JOIN order_items oi ON mi.id = oi.menu_item_id
            LEFT JOIN orders o ON oi.order_id = o.id
            WHERE o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY mi.id
            ORDER BY order_count DESC
            LIMIT 5
        ");
        $stmt->execute();
        $popularItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_merge($menuStats, [
            'popular_items' => $popularItems
        ]);

    } catch (PDOException $e) {
        error_log("Menu statistics failed: " . $e->getMessage());
        return [
            'total_items' => 0,
            'available_items' => 0,
            'featured_items' => 0,
            'avg_appetite_score' => 0,
            'avg_comfort_level' => 0,
            'popular_items' => []
        ];
    }
}

/**
 * Calculate psychology score for an order
 */
function calculateOrderPsychologyScore($order)
{
    $score = 5; // Base score

    // Check for psychology triggers used
    if (!empty($order['psychology_triggers_used'])) {
        try {
            $triggers = json_decode($order['psychology_triggers_used'], true);
            if (is_array($triggers)) {
                $score += count($triggers) * 1.5;
            }
        } catch (Exception $e) {
            // Ignore parsing errors
        }
    }

    // Check for psychology data
    if (!empty($order['psychology_data'])) {
        try {
            $psychData = json_decode($order['psychology_data'], true);
            if (isset($psychData['personalization_score'])) {
                $score += $psychData['personalization_score'] * 0.1;
            }
        } catch (Exception $e) {
            // Ignore parsing errors
        }
    }

    // Check user profile match
    if (!empty($order['appetite_profile'])) {
        $score += 1; // Bonus for having profile data
    }

    return min(round($score), 10);
}

/**
 * Get psychology triggers used in an order
 */
function getPsychologyTriggersUsed($order)
{
    if (empty($order['psychology_triggers_used'])) {
        return 'None';
    }

    try {
        $triggers = json_decode($order['psychology_triggers_used'], true);
        if (is_array($triggers) && !empty($triggers)) {
            $triggerTypes = [];
            foreach ($triggers as $trigger) {
                if (isset($trigger['type'])) {
                    $triggerTypes[] = ucfirst($trigger['type']);
                }
            }
            return !empty($triggerTypes) ? implode(', ', array_unique($triggerTypes)) : 'None';
        }
    } catch (Exception $e) {
        error_log("Psychology triggers parsing failed: " . $e->getMessage());
    }

    return 'None';
}

/**
 * Get real-time dashboard metrics
 */
function getRealTimeDashboardMetrics()
{
    $db = getDB();

    try {
        $metrics = [];

        // Today's orders and revenue
        $stmt = $db->prepare("
            SELECT 
                COUNT(*) as orders_today,
                COALESCE(SUM(total_amount), 0) as revenue_today
            FROM orders 
            WHERE DATE(order_date) = CURDATE()
        ");
        $stmt->execute();
        $todayStats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Pending orders
        $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
        $stmt->execute();
        $pendingOrders = $stmt->fetchColumn();

        // Active users (last hour)
        $stmt = $db->prepare("
            SELECT COUNT(DISTINCT session_id) 
            FROM behavior_logs 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $stmt->execute();
        $activeUsers = $stmt->fetchColumn();

        // Recent cart abandonment
        $stmt = $db->prepare("
            SELECT COUNT(DISTINCT session_id)
            FROM behavior_logs 
            WHERE event_type = 'cart_abandon' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $stmt->execute();
        $recentAbandonments = $stmt->fetchColumn();

        return [
            'orders_today' => $todayStats['orders_today'],
            'revenue_today' => $todayStats['revenue_today'],
            'pending_orders' => $pendingOrders,
            'active_users' => $activeUsers,
            'recent_abandonments' => $recentAbandonments,
            'last_updated' => date('Y-m-d H:i:s')
        ];

    } catch (PDOException $e) {
        error_log("Real-time metrics failed: " . $e->getMessage());
        return [
            'orders_today' => 0,
            'revenue_today' => 0,
            'pending_orders' => 0,
            'active_users' => 0,
            'recent_abandonments' => 0,
            'last_updated' => date('Y-m-d H:i:s')
        ];
    }
}

/**
 * Log admin actions for audit trail
 */
function logAdminAction($action, $details = [])
{
    $db = getDB();

    try {
        $stmt = $db->prepare("
            INSERT INTO admin_logs (admin_id, action, details, ip_address, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->execute([
            $_SESSION['user_id'] ?? null,
            $action,
            json_encode($details),
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

    } catch (PDOException $e) {
        error_log("Admin action logging failed: " . $e->getMessage());
        // Don't throw exception for logging failures
    }
}

/**
 * Check if admin logs table exists, create if not
 */
function ensureAdminLogsTable()
{
    $db = getDB();

    try {
        $stmt = $db->prepare("
            CREATE TABLE IF NOT EXISTS admin_logs (
                id INT PRIMARY KEY AUTO_INCREMENT,
                admin_id INT,
                action VARCHAR(100) NOT NULL,
                details JSON,
                ip_address VARCHAR(45),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_admin_logs_admin_id (admin_id),
                INDEX idx_admin_logs_created_at (created_at)
            )
        ");
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Admin logs table creation failed: " . $e->getMessage());
    }
}

/**
 * Get chart data for admin dashboard
 */
function getChartData($type, $period = 7)
{
    $db = getDB();

    try {
        switch ($type) {
            case 'revenue':
                $stmt = $db->prepare("
                    SELECT 
                        DATE(order_date) as date,
                        COALESCE(SUM(total_amount), 0) as revenue,
                        COUNT(*) as orders
                    FROM orders 
                    WHERE order_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    GROUP BY DATE(order_date)
                    ORDER BY date ASC
                ");
                $stmt->execute([$period]);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                return [
                    'chartData' => [
                        'labels' => array_column($data, 'date'),
                        'datasets' => [
                            [
                                'label' => 'Revenue',
                                'data' => array_column($data, 'revenue'),
                                'borderColor' => '#27AE60',
                                'backgroundColor' => 'rgba(39, 174, 96, 0.1)',
                                'fill' => true
                            ],
                            [
                                'label' => 'Orders',
                                'data' => array_column($data, 'orders'),
                                'borderColor' => '#3498DB',
                                'backgroundColor' => 'rgba(52, 152, 219, 0.1)',
                                'yAxisID' => 'y1'
                            ]
                        ]
                    ]
                ];

            case 'psychology':
                $stmt = $db->prepare("
                    SELECT 
                        DATE(created_at) as date,
                        JSON_EXTRACT(event_data, '$.trigger_type') as trigger_type,
                        COUNT(*) as trigger_count
                    FROM behavior_logs 
                    WHERE event_type = 'psychology_trigger'
                    AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                    GROUP BY DATE(created_at), JSON_EXTRACT(event_data, '$.trigger_type')
                    ORDER BY date ASC
                ");
                $stmt->execute([$period]);
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Process psychology data
                $dates = [];
                $triggers = ['scarcity' => [], 'social_proof' => [], 'urgency' => [], 'color' => []];

                // Fill in the data
                foreach ($data as $row) {
                    $date = $row['date'];
                    $triggerType = trim($row['trigger_type'], '"');

                    if (!in_array($date, $dates)) {
                        $dates[] = $date;
                    }

                    if (isset($triggers[$triggerType])) {
                        $triggers[$triggerType][$date] = $row['trigger_count'];
                    }
                }

                // Ensure all dates have values for all triggers
                foreach ($triggers as $type => &$values) {
                    foreach ($dates as $date) {
                        if (!isset($values[$date])) {
                            $values[$date] = 0;
                        }
                    }
                    ksort($values);
                    $values = array_values($values);
                }

                return [
                    'chartData' => [
                        'labels' => $dates,
                        'datasets' => [
                            [
                                'label' => 'Scarcity',
                                'data' => $triggers['scarcity'],
                                'borderColor' => '#E74C3C',
                                'backgroundColor' => 'rgba(231, 76, 60, 0.1)'
                            ],
                            [
                                'label' => 'Social Proof',
                                'data' => $triggers['social_proof'],
                                'borderColor' => '#3498DB',
                                'backgroundColor' => 'rgba(52, 152, 219, 0.1)'
                            ],
                            [
                                'label' => 'Urgency',
                                'data' => $triggers['urgency'],
                                'borderColor' => '#F39C12',
                                'backgroundColor' => 'rgba(243, 156, 18, 0.1)'
                            ],
                            [
                                'label' => 'Color',
                                'data' => $triggers['color'],
                                'borderColor' => '#9B59B6',
                                'backgroundColor' => 'rgba(155, 89, 182, 0.1)'
                            ]
                        ]
                    ]
                ];

            default:
                return [
                    'chartData' => [
                        'labels' => [],
                        'datasets' => []
                    ]
                ];
        }
    } catch (PDOException $e) {
        error_log("Chart data failed: " . $e->getMessage());
        return [
            'chartData' => [
                'labels' => [],
                'datasets' => []
            ]
        ];
    }
}

/**
 * Get detailed order information for modal display
 */
function getOrderDetails($orderId)
{
    $db = getDB();

    try {
        // Get order with user info
        $stmt = $db->prepare("
            SELECT 
                o.*,
                u.name as user_name,
                u.email as user_email,
                u.appetite_profile,
                u.price_sensitivity
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            WHERE o.id = ?
        ");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return false;
        }

        // Get order items
        $stmt = $db->prepare("
            SELECT 
                oi.*,
                mi.name,
                mi.description,
                mi.image_url
            FROM order_items oi
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add items to order
        $order['order_items'] = $orderItems;

        // Parse JSON fields safely
        $order['items'] = json_decode($order['items'] ?? '[]', true) ?: [];
        $order['psychology_triggers_used'] = json_decode($order['psychology_triggers_used'] ?? '[]', true) ?: [];
        $order['psychology_data'] = json_decode($order['psychology_data'] ?? '{}', true) ?: [];

        return $order;

    } catch (PDOException $e) {
        error_log("Get order details failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Get psychology events for admin analytics
 */
function getPsychologyEvents($limit = 50)
{
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT 
                bl.*,
                u.name as user_name,
                JSON_EXTRACT(bl.event_data, '$.trigger_type') as trigger_type,
                JSON_EXTRACT(bl.event_data, '$.result') as result,
                JSON_EXTRACT(bl.event_data, '$.impact_score') as impact_score
            FROM behavior_logs bl
            LEFT JOIN users u ON bl.user_id = u.id
            WHERE bl.event_type IN ('psychology_trigger', 'cart_add', 'purchase', 'item_view')
            ORDER BY bl.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get psychology events failed: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all users with their statistics
 */
function getAllUsersWithStats()
{
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT 
                u.*,
                COUNT(o.id) as total_orders,
                COALESCE(SUM(o.total_amount), 0) as total_spent,
                AVG(o.total_amount) as avg_order_value,
                MAX(o.order_date) as last_order_date,
                COUNT(CASE WHEN o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as orders_this_month
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id
            WHERE u.role = 'user'
            GROUP BY u.id
            ORDER BY u.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get all users with stats failed: " . $e->getMessage());
        return [];
    }
}

/**
 * Add new menu item with psychology attributes
 */
function addMenuItem($data)
{
    $db = getDB();

    try {
        // Process sensory words
        $sensoryWords = [];
        if (!empty($data['sensory_words'])) {
            $sensoryWords = array_map('trim', explode(',', $data['sensory_words']));
            $sensoryWords = array_filter($sensoryWords); // Remove empty values
        }

        $stmt = $db->prepare("
            INSERT INTO menu_items (
                name, description, price, category, appetite_score, 
                comfort_level, sensory_words, available, featured, 
                limited_quantity, margin_level, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $result = $stmt->execute([
            sanitizeInput($data['name']),
            sanitizeInput($data['description'] ?? ''),
            floatval($data['price']),
            sanitizeInput($data['category']),
            intval($data['appetite_score'] ?? 5),
            intval($data['comfort_level'] ?? 5),
            json_encode($sensoryWords),
            isset($data['available']) ? 1 : 0,
            isset($data['featured']) ? 1 : 0,
            !empty($data['limited_qty']) ? intval($data['limited_qty']) : null,
            sanitizeInput($data['margin_level'] ?? 'medium')
        ]);

        if ($result) {
            logAdminAction('menu_item_added', [
                'item_name' => $data['name'],
                'category' => $data['category'],
                'price' => $data['price']
            ]);
        }

        return [
            'success' => $result,
            'message' => $result ? 'Menu item added successfully' : 'Failed to add menu item'
        ];

    } catch (PDOException $e) {
        error_log("Add menu item failed: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Database error occurred'
        ];
    }
}

/**
 * Toggle menu item availability
 */
function toggleMenuItemAvailability($itemId)
{
    $db = getDB();

    try {
        // Get current status first
        $stmt = $db->prepare("SELECT name, available FROM menu_items WHERE id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return false;
        }

        // Toggle availability
        $stmt = $db->prepare("UPDATE menu_items SET available = NOT available WHERE id = ?");
        $result = $stmt->execute([$itemId]);

        if ($result) {
            $newStatus = $item['available'] ? 0 : 1;
            logAdminAction('menu_item_availability_changed', [
                'item_id' => $itemId,
                'item_name' => $item['name'],
                'new_status' => $newStatus ? 'available' : 'unavailable'
            ]);
        }

        return $result;
    } catch (PDOException $e) {
        error_log("Toggle menu item availability failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete menu item
 */
function deleteMenuItem($itemId)
{
    $db = getDB();

    try {
        // Get item info before deletion for logging
        $stmt = $db->prepare("SELECT name FROM menu_items WHERE id = ?");
        $stmt->execute([$itemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            return false;
        }

        // Check if item has been ordered
        $stmt = $db->prepare("SELECT COUNT(*) FROM order_items WHERE menu_item_id = ?");
        $stmt->execute([$itemId]);
        $orderCount = $stmt->fetchColumn();

        if ($orderCount > 0) {
            // Don't delete items that have been ordered, just mark as unavailable
            $stmt = $db->prepare("UPDATE menu_items SET available = 0 WHERE id = ?");
            $result = $stmt->execute([$itemId]);

            logAdminAction('menu_item_disabled', [
                'item_id' => $itemId,
                'item_name' => $item['name'],
                'reason' => 'Has order history'
            ]);
        } else {
            // Safe to delete
            $stmt = $db->prepare("DELETE FROM menu_items WHERE id = ?");
            $result = $stmt->execute([$itemId]);

            logAdminAction('menu_item_deleted', [
                'item_id' => $itemId,
                'item_name' => $item['name']
            ]);
        }

        return $result;
    } catch (PDOException $e) {
        error_log("Delete menu item failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Save general settings
 */
function saveGeneralSettings($data)
{
    $db = getDB();

    try {
        $settings = [
            'site_name' => sanitizeInput($data['site_name']),
            'delivery_fee' => floatval($data['delivery_fee']),
            'free_delivery_threshold' => floatval($data['free_delivery_threshold']),
            'tax_rate' => floatval($data['tax_rate'])
        ];

        foreach ($settings as $key => $value) {
            $stmt = $db->prepare("
                INSERT INTO settings (setting_key, setting_value, updated_at) 
                VALUES (?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                updated_at = NOW()
            ");
            $stmt->execute([$key, $value]);
        }

        logAdminAction('general_settings_updated', $settings);
        return true;

    } catch (PDOException $e) {
        error_log("Save general settings failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Save psychology settings
 */
function savePsychologySettings($data)
{
    $db = getDB();

    try {
        $settings = [
            'psychology_enabled' => isset($data['psychology_enabled']) ? 1 : 0,
            'scarcity_threshold' => intval($data['scarcity_threshold']),
            'social_proof_interval' => intval($data['social_proof_interval']),
            'exit_intent_delay' => intval($data['exit_intent_delay'])
        ];

        foreach ($settings as $key => $value) {
            $stmt = $db->prepare("
                INSERT INTO settings (setting_key, setting_value, updated_at) 
                VALUES (?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                updated_at = NOW()
            ");
            $stmt->execute([$key, $value]);
        }

        logAdminAction('psychology_settings_updated', $settings);
        return true;

    } catch (PDOException $e) {
        error_log("Save psychology settings failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Export menu data as CSV
 */
function exportMenuData()
{
    $items = getAllMenuItems();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="menu_export_' . date('Y-m-d') . '.csv"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // CSV headers
    fputcsv($output, [
        'ID', 'Name', 'Description', 'Price', 'Category',
        'Appetite Score', 'Comfort Level', 'Sensory Words',
        'Available', 'Featured', 'Orders (30 days)', 'Created Date'
    ]);

    // CSV data
    foreach ($items as $item) {
        $sensoryWords = json_decode($item['sensory_words'] ?? '[]', true);

        fputcsv($output, [
            $item['id'],
            $item['name'],
            $item['description'],
            $item['price'],
            $item['category'],
            $item['appetite_score'],
            $item['comfort_level'],
            implode(', ', $sensoryWords),
            $item['available'] ? 'Yes' : 'No',
            $item['featured'] ? 'Yes' : 'No',
            $item['orders_count'] ?? 0,
            $item['created_at']
        ]);
    }

    fclose($output);

    logAdminAction('menu_data_exported', [
        'total_items' => count($items),
        'export_format' => 'CSV'
    ]);
}

/**
 * Get user profile with detailed stats
 */
function getUserProfile($userId)
{
    $db = getDB();

    try {
        $stmt = $db->prepare("
            SELECT 
                u.*,
                COUNT(o.id) as total_orders,
                COALESCE(SUM(o.total_amount), 0) as total_spent,
                AVG(o.total_amount) as avg_order_value,
                MAX(o.order_date) as last_order_date,
                MIN(o.order_date) as first_order_date,
                COUNT(CASE WHEN o.order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as orders_this_month
            FROM users u
            LEFT JOIN orders o ON u.id = o.user_id
            WHERE u.id = ?
            GROUP BY u.id
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Get user's favorite categories
            $stmt = $db->prepare("
                SELECT 
                    mi.category,
                    COUNT(*) as order_count
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                JOIN menu_items mi ON oi.menu_item_id = mi.id
                WHERE o.user_id = ?
                GROUP BY mi.category
                ORDER BY order_count DESC
                LIMIT 3
            ");
            $stmt->execute([$userId]);
            $user['favorite_categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $user;
    } catch (PDOException $e) {
        error_log("Get user profile failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Get real-time dashboard statistics
 */
function getDashboardStats()
{
    return getRealTimeDashboardMetrics();
}

/**
 * Initialize admin logs table if it doesn't exist
 */
function initializeAdminSystem()
{
    ensureAdminLogsTable();

    // Create settings table if it doesn't exist
    $db = getDB();
    try {
        $stmt = $db->prepare("
            CREATE TABLE IF NOT EXISTS settings (
                id INT PRIMARY KEY AUTO_INCREMENT,
                setting_key VARCHAR(100) UNIQUE NOT NULL,
                setting_value TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Settings table creation failed: " . $e->getMessage());
    }
}// Initialize admin system
initializeAdminSystem();

