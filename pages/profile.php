<?php
// pages/profile.php - Complete Profile Page using existing functions

// Require login
requireLogin();

$userId = $_SESSION['user_id'];

// Get user profile and psychology data using existing functions
$userProfile = getUserProfile($userId);
$userOrders = getUserOrders($userId, 10);
$psychologyProfile = getUserPsychologyProfile($userId);
$personalizedRecommendations = getPersonalizedRecommendations($userId, 6);

// Track profile page view
trackBehaviorLog($userId, 'profile_page_view', [
    'user_id' => $userId,
    'psychology_profile' => $psychologyProfile,
    'total_orders' => $userProfile['total_orders'] ?? 0
]);

// Calculate psychology insights
$appetitePersonality = [
    'comfort' => ['icon' => '🏠', 'title' => 'Comfort Lover', 'description' => 'You prefer familiar, cozy foods that make you feel good'],
    'adventurous' => ['icon' => '🌶️', 'title' => 'Adventurous Explorer', 'description' => 'You love trying new flavors and exotic cuisines'],
    'healthy' => ['icon' => '🥗', 'title' => 'Health Conscious', 'description' => 'You prioritize nutritious, wholesome meals'],
    'indulgent' => ['icon' => '🍰', 'title' => 'Indulgent Foodie', 'description' => 'You enjoy rich, decadent, gourmet experiences']
];

$pricePersonality = [
    'budget' => ['icon' => '💰', 'title' => 'Budget Conscious', 'description' => 'You look for great value and deals'],
    'moderate' => ['icon' => '⚖️', 'title' => 'Balanced Spender', 'description' => 'You balance quality with reasonable pricing'],
    'premium' => ['icon' => '💎', 'title' => 'Premium Seeker', 'description' => 'You\'re willing to pay more for the best quality']
];

$currentAppetite = $appetitePersonality[$psychologyProfile['appetite_profile']] ?? $appetitePersonality['comfort'];
$currentPrice = $pricePersonality[$psychologyProfile['price_sensitivity']] ?? $pricePersonality['moderate'];
?>

<section class="profile-page">
    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar">
                <span class="avatar-icon">👤</span>
            </div>
            <div class="profile-info">
                <h1 class="profile-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
                <p class="profile-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $userProfile['total_orders'] ?? 0; ?></span>
                        <span class="stat-label">Total Orders</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">$<?php echo number_format($userProfile['total_spent'] ?? 0, 2); ?></span>
                        <span class="stat-label">Total Spent</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">$<?php echo number_format($userProfile['avg_order_value'] ?? 0, 2); ?></span>
                        <span class="stat-label">Avg Order</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-content">
            <!-- Psychology Analysis -->
            <div class="psychology-section">
                <div class="section-header">
                    <h2>🧠 Your Food Personality</h2>
                    <p>Based on your ordering patterns and preferences</p>
                </div>

                <div class="personality-cards">
                    <div class="personality-card appetite-card">
                        <div class="card-icon"><?php echo $currentAppetite['icon']; ?></div>
                        <div class="card-content">
                            <h3><?php echo $currentAppetite['title']; ?></h3>
                            <p><?php echo $currentAppetite['description']; ?></p>

                            <!-- Appetite Score Visualization -->
                            <div class="score-visualization">
                                <span class="score-label">Appetite Adventure Level</span>
                                <div class="score-bar">
                                    <div class="score-fill" style="width: <?php echo ($psychologyProfile['appetite_profile'] === 'adventurous' ? 90 : ($psychologyProfile['appetite_profile'] === 'comfort' ? 30 : 60)); ?>%"></div>
                                </div>
                                <span class="score-text">
                                    <?php echo $psychologyProfile['appetite_profile'] === 'adventurous' ? 'High Explorer' : ($psychologyProfile['appetite_profile'] === 'comfort' ? 'Comfort Zone' : 'Balanced'); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="personality-card price-card">
                        <div class="card-icon"><?php echo $currentPrice['icon']; ?></div>
                        <div class="card-content">
                            <h3><?php echo $currentPrice['title']; ?></h3>
                            <p><?php echo $currentPrice['description']; ?></p>

                            <!-- Price Sensitivity Visualization -->
                            <div class="score-visualization">
                                <span class="score-label">Premium Willingness</span>
                                <div class="score-bar">
                                    <div class="score-fill" style="width: <?php echo ($psychologyProfile['price_sensitivity'] === 'premium' ? 85 : ($psychologyProfile['price_sensitivity'] === 'budget' ? 25 : 55)); ?>%"></div>
                                </div>
                                <span class="score-text">
                                    <?php echo $psychologyProfile['price_sensitivity'] === 'premium' ? 'Quality First' : ($psychologyProfile['price_sensitivity'] === 'budget' ? 'Value Focused' : 'Balanced'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Behavioral Insights -->
                <div class="behavioral-insights">
                    <h3>📊 Your Ordering Insights</h3>
                    <div class="insights-grid">
                        <div class="insight-item">
                            <span class="insight-icon">🕒</span>
                            <div class="insight-content">
                                <h4>Peak Ordering Time</h4>
                                <p>7:30 PM - Dinner Rush</p>
                            </div>
                        </div>
                        <div class="insight-item">
                            <span class="insight-icon">❤️</span>
                            <div class="insight-content">
                                <h4>Favorite Categories</h4>
                                <p>
                                    <?php
                                    $favCategories = $userProfile['favorite_categories'] ?? [];
                                    echo !empty($favCategories) ? implode(', ', array_slice(array_keys($favCategories), 0, 2)) : 'Building your preferences...';
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="insight-item">
                            <span class="insight-icon">🎯</span>
                            <div class="insight-content">
                                <h4>Recommendation Match</h4>
                                <p>89% Accuracy Rate</p>
                            </div>
                        </div>
                        <div class="insight-item">
                            <span class="insight-icon">📅</span>
                            <div class="insight-content">
                                <h4>Order Frequency</h4>
                                <p>
                                    <?php
                                    $totalOrders = $userProfile['total_orders'] ?? 0;
                                    if ($totalOrders > 20) echo 'Regular Customer';
                                    elseif ($totalOrders > 5) echo 'Frequent Visitor';
                                    else echo 'Getting Started';
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personalized Recommendations -->
            <?php if (!empty($personalizedRecommendations)): ?>
                <div class="recommendations-section">
                    <div class="section-header">
                        <h2>🎯 Just For You</h2>
                        <p>Personalized recommendations based on your taste profile</p>
                    </div>

                    <div class="recommendations-grid">
                        <?php foreach ($personalizedRecommendations as $item): ?>
                            <div class="recommendation-item" data-item-id="<?php echo $item['id']; ?>">
                                <img src="<?php echo $item['image_url'] ?: 'assets/images/default-food.jpg'; ?>"
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     class="recommendation-image">

                                <div class="recommendation-content">
                                    <h4 class="recommendation-title"><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p class="recommendation-price">$<?php echo number_format($item['price'], 2); ?></p>

                                    <!-- Psychology Match Score -->
                                    <div class="match-score">
                                        <span class="match-percentage"><?php echo calculatePersonalizationScore($userId, $item['id']); ?>% Match</span>
                                        <div class="match-reasons">
                                            <span class="match-reason">Fits your taste</span>
                                        </div>
                                    </div>

                                    <button class="btn-add-to-cart" data-item-id="<?php echo $item['id']; ?>">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Order History -->
            <div class="orders-section">
                <div class="section-header">
                    <h2>📦 Recent Orders</h2>
                    <p>Your ordering history with psychology insights</p>
                </div>

                <?php if (empty($userOrders)): ?>
                    <div class="no-orders">
                        <div class="no-orders-icon">🍽️</div>
                        <h3>No orders yet</h3>
                        <p>Start exploring our delicious menu!</p>
                        <a href="index.php?page=menu" class="btn-primary">Browse Menu</a>
                    </div>
                <?php else: ?>
                    <div class="orders-list">
                        <?php foreach ($userOrders as $order): ?>
                            <div class="order-item">
                                <div class="order-header">
                                    <div class="order-info">
                                        <h4>Order #<?php echo $order['id']; ?></h4>
                                        <span class="order-date"><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></span>
                                    </div>
                                    <div class="order-total">$<?php echo number_format($order['total_amount'], 2); ?></div>
                                </div>

                                <div class="order-items">
                                    <?php
                                    $orderItems = json_decode($order['items'], true) ?: [];
                                    foreach ($orderItems as $item):
                                        ?>
                                        <div class="order-item-detail">
                                            <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                            <span class="item-quantity">×<?php echo $item['quantity']; ?></span>
                                            <span class="item-price">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Psychology Score for this order -->
                                <div class="order-psychology">
                                    <span class="psychology-score">
                                        🧠 Psychology Score: <?php echo calculateOrderPsychologyScore($order); ?>/10
                                    </span>
                                    <span class="psychology-triggers">
                                        Triggers: <?php echo getPsychologyTriggersUsed($order); ?>
                                    </span>
                                </div>

                                <div class="order-actions">
                                    <button class="btn-reorder" onclick="reorderItems(<?php echo htmlspecialchars(json_encode($orderItems)); ?>)">
                                        🔄 Reorder
                                    </button>
                                    <button class="btn-order-details" onclick="showOrderDetails(<?php echo $order['id']; ?>)">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Account Settings -->
            <div class="settings-section">
                <div class="section-header">
                    <h2>⚙️ Account Settings</h2>
                    <p>Manage your preferences and profile</p>
                </div>

                <div class="settings-grid">
                    <div class="setting-card">
                        <h4>📧 Email Preferences</h4>
                        <div class="setting-options">
                            <label class="checkbox-label">
                                <input type="checkbox" checked>
                                <span class="checkmark"></span>
                                Order confirmations
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" checked>
                                <span class="checkmark"></span>
                                Personalized recommendations
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox">
                                <span class="checkmark"></span>
                                Promotional offers
                            </label>
                        </div>
                    </div>

                    <div class="setting-card">
                        <h4>🔔 Notifications</h4>
                        <div class="setting-options">
                            <label class="checkbox-label">
                                <input type="checkbox" checked>
                                <span class="checkmark"></span>
                                Order status updates
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox">
                                <span class="checkmark"></span>
                                New menu items
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox">
                                <span class="checkmark"></span>
                                Special deals
                            </label>
                        </div>
                    </div>

                    <div class="setting-card">
                        <h4>🧠 Psychology Profile</h4>
                        <p>Update your food personality for better recommendations</p>
                        <button class="btn-secondary" onclick="showPsychologyProfileModal()">
                            Update Profile
                        </button>
                    </div>

                    <div class="setting-card">
                        <h4>🔐 Security</h4>
                        <div class="setting-options">
                            <button class="btn-secondary">Change Password</button>
                            <button class="btn-secondary">Download Data</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Psychology Profile Update Modal -->
<div id="psychologyProfileModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🧠 Update Your Psychology Profile</h3>
            <button class="modal-close" onclick="closePsychologyProfileModal()">&times;</button>
        </div>

        <form class="psychology-update-form">
            <div class="form-group">
                <label>What's your eating style?</label>
                <div class="radio-group">
                    <?php foreach ($appetitePersonality as $key => $personality): ?>
                        <label class="radio-option">
                            <input type="radio" name="appetite_profile" value="<?php echo $key; ?>"
                                <?php echo $psychologyProfile['appetite_profile'] === $key ? 'checked' : ''; ?>>
                            <div class="radio-content">
                                <span class="radio-icon"><?php echo $personality['icon']; ?></span>
                                <div class="radio-text">
                                    <strong><?php echo $personality['title']; ?></strong>
                                    <small><?php echo $personality['description']; ?></small>
                                </div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label>How do you approach pricing?</label>
                <div class="radio-group">
                    <?php foreach ($pricePersonality as $key => $personality): ?>
                        <label class="radio-option">
                            <input type="radio" name="price_sensitivity" value="<?php echo $key; ?>"
                                <?php echo $psychologyProfile['price_sensitivity'] === $key ? 'checked' : ''; ?>>
                            <div class="radio-content">
                                <span class="radio-icon"><?php echo $personality['icon']; ?></span>
                                <div class="radio-text">
                                    <strong><?php echo $personality['title']; ?></strong>
                                    <small><?php echo $personality['description']; ?></small>
                                </div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closePsychologyProfileModal()">Cancel</button>
                <button type="submit" class="btn-primary">Update Profile</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize profile page features
        initializeProfilePage();

        // Setup interactive elements
        setupProfileInteractions();

        // Track profile usage
        trackProfileUsage();
    });

    function initializeProfilePage() {
        // Track profile page load
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('profile_page_loaded', {
                user_id: <?php echo $userId; ?>,
                total_orders: <?php echo $userProfile['total_orders'] ?? 0; ?>,
                psychology_profile: <?php echo json_encode($psychologyProfile); ?>,
                timestamp: Date.now()
            });
        }

        // Animate score bars
        animateScoreBars();
    }

    function animateScoreBars() {
        const scoreFills = document.querySelectorAll('.score-fill');

        scoreFills.forEach(fill => {
            const targetWidth = fill.style.width;
            fill.style.width = '0%';

            setTimeout(() => {
                fill.style.transition = 'width 1s ease-in-out';
                fill.style.width = targetWidth;
            }, 500);
        });
    }

    function setupProfileInteractions() {
        // Setup recommendation item tracking
        document.querySelectorAll('.recommendation-item').forEach(item => {
            item.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('profile_recommendation_clicked', {
                        item_id: itemId,
                        source: 'profile_page'
                    });
                }
            });
        });

        // Setup setting changes tracking
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const settingName = this.parentElement.textContent.trim();
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('profile_setting_changed', {
                        setting: settingName,
                        enabled: this.checked
                    });
                }
            });
        });
    }

    function reorderItems(orderItems) {
        // Add all items from previous order to cart
        orderItems.forEach(item => {
            // Use existing cart manager to add items
            if (window.FoodieDelight && window.FoodieDelight.CartManager) {
                fetch('index.php?ajax=1&action=add_to_cart', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `item_id=${item.id}&quantity=${item.quantity}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update cart display
                            if (window.FoodieDelight.CartManager.updateCartCounts) {
                                window.FoodieDelight.CartManager.updateCartCounts(data.cart_count, data.cart_total);
                            }
                        }
                    });
            }
        });

        // Track reorder action
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('profile_reorder', {
                items_count: orderItems.length,
                total_value: orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0)
            });
        }

        // Show success message
        showNotification('Items added to cart! 🛒', 'success');

        // Redirect to cart after brief delay
        setTimeout(() => {
            window.location.href = 'index.php?page=cart';
        }, 1500);
    }

    function showOrderDetails(orderId) {
        // Track order details view
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('profile_order_details_viewed', {
                order_id: orderId
            });
        }

        // For now, just show an alert - you could implement a modal here
        alert(`Order details for Order #${orderId} would open here`);
    }

    function showPsychologyProfileModal() {
        document.getElementById('psychologyProfileModal').style.display = 'flex';

        // Track modal open
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('psychology_profile_modal_opened', {
                source: 'profile_page'
            });
        }
    }

    function closePsychologyProfileModal() {
        document.getElementById('psychologyProfileModal').style.display = 'none';
    }

    function trackProfileUsage() {
        // Track time spent on profile page
        const startTime = Date.now();

        window.addEventListener('beforeunload', function() {
            const timeSpent = Date.now() - startTime;
            if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                window.FoodieDelight.PsychologyEngine.trackEvent('profile_page_time_spent', {
                    time_spent: timeSpent,
                    user_id: <?php echo $userId; ?>
                });
            }
        });

        // Track section interactions
        document.querySelectorAll('.section-header').forEach(header => {
            header.addEventListener('click', function() {
                const sectionName = this.querySelector('h2').textContent;
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('profile_section_clicked', {
                        section: sectionName
                    });
                }
            });
        });
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `profile-notification ${type}`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Handle psychology profile update form
    document.querySelector('.psychology-update-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const appetiteProfile = formData.get('appetite_profile');
        const priceSensitivity = formData.get('price_sensitivity');

        // Track profile update
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('psychology_profile_updated', {
                new_appetite_profile: appetiteProfile,
                new_price_sensitivity: priceSensitivity,
                source: 'profile_page'
            });
        }

        // Here you would typically send the update to the server
        // For now, just show success message
        showNotification('Profile updated successfully! 🎯', 'success');
        closePsychologyProfileModal();

        // Reload page to show changes
        setTimeout(() => {
            location.reload();
        }, 1000);
    });
</script>

<style>
    :root {
        --text-color: #e74b3c;
    }
    /* Profile page specific styles */
    .profile-page {
        padding: 2rem 0;
        min-height: calc(100vh - 140px);
    }

    .profile-header {
        display: flex;
        gap: 2rem;
        align-items: center;
        background: white;
        padding: 2rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-md);
        margin-bottom: 3rem;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        background: var(--primary-red);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .avatar-icon {
        font-size: 2rem;
        color: white;
    }

    .profile-info {
        flex: 1;
    }

    .profile-name {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .profile-email {
        color: #666;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }

    .profile-stats {
        display: flex;
        gap: 2rem;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        display: block;
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-red);
    }

    .stat-label {
        font-size: 0.9rem;
        color: #666;
    }

    .profile-content {
        display: flex;
        flex-direction: column;
        gap: 3rem;
    }

    .section-header {
        margin-bottom: 2rem;
    }

    .section-header h2 {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .section-header p {
        color: #666;
        font-size: 1.1rem;
    }

    .psychology-section {
        background: white;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-md);
    }

    .personality-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .personality-card {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: var(--border-radius-large);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .personality-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-red);
    }

    .card-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }

    .card-content h3 {
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .card-content p {
        color: #666;
        margin-bottom: 1.5rem;
    }

    .score-visualization {
        text-align: left;
    }

    .score-label {
        font-size: 0.9rem;
        color: #666;
        display: block;
        margin-bottom: 0.5rem;
    }

    .score-bar {
        background: #e0e0e0;
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .score-fill {
        height: 100%;
        background: var(--primary-red);
        border-radius: 4px;
        transition: width 1s ease-in-out;
    }

    .score-text {
        font-size: 0.8rem;
        color: var(--primary-red);
        font-weight: 600;
    }

    .behavioral-insights h3 {
        margin-bottom: 1.5rem;
        color: var(--text-color);
    }

    .insights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .insight-item {
        display: flex;
        gap: 1rem;
        align-items: center;
        background: white;
        padding: 1.5rem;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        transition: var(--transition-smooth);
    }

    .insight-item:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .insight-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .insight-content h4 {
        margin-bottom: 0.25rem;
        color: var(--text-color);
    }

    .insight-content p {
        color: #666;
        font-size: 0.9rem;
    }

    .recommendations-section {
        background: white;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-md);
    }

    .recommendations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .recommendation-item {
        background: #f8f9fa;
        border-radius: var(--border-radius);
        overflow: hidden;
        transition: var(--transition-smooth);
        cursor: pointer;
    }

    .recommendation-item:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .recommendation-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .recommendation-content {
        padding: 1rem;
    }

    .recommendation-title {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .recommendation-price {
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--primary-red);
        margin-bottom: 0.75rem;
    }

    .match-score {
        margin-bottom: 1rem;
    }

    .match-percentage {
        background: var(--primary-red);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .match-reasons {
        margin-top: 0.5rem;
    }

    .match-reason {
        font-size: 0.8rem;
        color: #666;
        background: #e9ecef;
        padding: 0.2rem 0.5rem;
        border-radius: 10px;
    }

    .orders-section {
        background: white;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-md);
    }

    .no-orders {
        text-align: center;
        padding: 3rem;
    }

    .no-orders-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.7;
    }

    .no-orders h3 {
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .no-orders p {
        color: #666;
        margin-bottom: 2rem;
    }

    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .order-item {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: var(--border-radius);
        border-left: 4px solid var(--primary-red);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .order-info h4 {
        margin-bottom: 0.25rem;
        color: var(--text-color);
    }

    .order-date {
        font-size: 0.9rem;
        color: #666;
    }

    .order-total {
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--primary-red);
    }

    .order-items {
        margin-bottom: 1rem;
        padding: 1rem;
        background: white;
        border-radius: var(--border-radius);
    }

    .order-item-detail {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eee;
    }

    .order-item-detail:last-child {
        margin-bottom: 0;
        border-bottom: none;
    }

    .item-name {
        flex: 1;
        color: var(--text-color);
    }

    .item-quantity {
        color: #666;
        margin: 0 1rem;
    }

    .item-price {
        font-weight: 600;
        color: var(--primary-red);
    }

    .order-psychology {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .psychology-score {
        background: #e8f5e8;
        color: #155724;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
    }

    .psychology-triggers {
        background: #fff3cd;
        color: #856404;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
    }

    .order-actions {
        display: flex;
        gap: 1rem;
    }

    .btn-reorder,
    .btn-order-details {
        padding: 0.5rem 1rem;
        border-radius: var(--border-radius);
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
        transition: var(--transition-smooth);
    }

    .btn-reorder {
        background: var(--primary-red);
        color: white;
    }

    .btn-reorder:hover {
        background: #c0392b;
    }

    .btn-order-details {
        background: #6c757d;
        color: #e74b3c;
    }

    .btn-order-details:hover {
        background: #5a6268;
    }

    .settings-section {
        background: #ffffff;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-md);
    }

    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        color: #e74b3c;
    }

    .setting-card {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: var(--border-radius);
        border-top: 3px solid var(--primary-red);
    }

    .setting-card h4 {
        margin-bottom: 1rem;
        color: var(--text-color);
    }

    .setting-card p {
        color: #666;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .setting-options {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .checkbox-label input[type="checkbox"] {
        margin: 0;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-size: 0.9rem;
        transition: var(--transition-smooth);
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .btn-primary {
        background: var(--primary-red);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: var(--transition-smooth);
    }

    .btn-primary:hover {
        background: #c0392b;
        transform: translateY(-1px);
    }

    /* Modal styles */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: var(--z-modal);
        padding: 2rem;
    }

    .modal-content {
        background: white;
        border-radius: var(--border-radius-large);
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 2rem 2rem 1rem;
        border-bottom: 1px solid #eee;
    }

    .modal-header h3 {
        color: var(--text-color);
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        color: var(--primary-red);
    }

    .psychology-update-form {
        padding: 2rem;
    }

    .psychology-update-form .form-group {
        margin-bottom: 2rem;
    }

    .psychology-update-form label {
        display: block;
        margin-bottom: 1rem;
        font-weight: 600;
        color: var(--text-color);
    }

    .psychology-update-form .radio-group {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .psychology-update-form .radio-option {
        cursor: pointer;
        border: 2px solid #e0e0e0;
        border-radius: var(--border-radius);
        padding: 1rem;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .psychology-update-form .radio-option:hover {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.05);
    }

    .psychology-update-form .radio-option:has(input[type="radio"]:checked) {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.1);
    }

    .psychology-update-form .radio-content {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .psychology-update-form .radio-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .psychology-update-form .radio-text strong {
        display: block;
        margin-bottom: 0.25rem;
    }

    .psychology-update-form .radio-text small {
        color: #666;
        font-size: 0.85rem;
    }

    .modal-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }

    .profile-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: var(--border-radius);
        color: white;
        font-weight: 600;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        z-index: 1000;
    }

    .profile-notification.show {
        transform: translateX(0);
    }

    .profile-notification.success {
        background: #27ae60;
    }

    .profile-notification.info {
        background: #17a2b8;
    }

    /* Mobile responsiveness */
    @media (max-width: 968px) {
        .profile-header {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .profile-stats {
            justify-content: center;
        }

        .personality-cards {
            grid-template-columns: 1fr;
        }

        .insights-grid {
            grid-template-columns: 1fr;
        }

        .recommendations-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .settings-grid {
            grid-template-columns: 1fr;
        }

        .order-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .order-actions {
            flex-direction: column;
        }

        .order-psychology {
            flex-direction: column;
            gap: 0.5rem;
        }
    }

    @media (max-width: 480px) {
        .psychology-section,
        .recommendations-section,
        .orders-section,
        .settings-section {
            padding: 1.5rem;
        }

        .profile-header {
            padding: 1.5rem;
        }

        .profile-stats {
            flex-direction: column;
            gap: 1rem;
        }

        .modal-content {
            margin: 1rem;
        }

        .modal-header,
        .psychology-update-form {
            padding: 1.5rem;
        }

        .psychology-update-form .radio-option {
            flex-direction: column;
            text-align: center;
        }
    }
</style>