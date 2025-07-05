<?php
// pages/404.php - Smart 404 Page using existing functions

// Set 404 header
http_response_code(404);

// Track 404 error for analytics
$requestedPage = $_GET['page'] ?? 'unknown';
$requestedUrl = $_SERVER['REQUEST_URI'] ?? '';
$referrer = $_SERVER['HTTP_REFERER'] ?? '';

trackBehaviorLog($_SESSION['user_id'] ?? null, '404_error', [
    'requested_page' => $requestedPage,
    'requested_url' => $requestedUrl,
    'referrer' => $referrer,
    'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 100),
    'timestamp' => date('Y-m-d H:i:s')
]);

// Get helpful content using existing functions
$featuredItems = getFeaturedMenuItems(3);
$popularItems = getPopularItems(3);
$categories = getMenuCategories();
$timeGreeting = getTimeBasedGreeting();

// Get some stats for social proof
$totalOrders = getTotalOrdersToday();
$totalUsers = getUserStatistics()['total_users'] ?? 250;
?>

<section class="error-page">
    <div class="container">
        <!-- Error Header -->
        <div class="error-header">
            <div class="error-animation">
                <div class="error-icon">🍽️</div>
                <div class="error-number">404</div>
            </div>
            <h1 class="error-title">Oops! Page Not Found</h1>
            <p class="error-message">
                Looks like this page got eaten! <?php echo $timeGreeting['emoji']; ?>
                But don't worry, we have plenty of delicious options waiting for you.
            </p>
        </div>

        <!-- Quick Navigation -->
        <div class="quick-navigation">
            <h2>🔍 What you can do:</h2>
            <div class="nav-options">
                <a href="index.php?page=home" class="nav-option">
                    <span class="nav-icon">🏠</span>
                    <div class="nav-content">
                        <h3>Go to Homepage</h3>
                        <p>Start fresh with our main page</p>
                    </div>
                </a>

                <a href="index.php?page=menu" class="nav-option">
                    <span class="nav-icon">📋</span>
                    <div class="nav-content">
                        <h3>Browse Our Menu</h3>
                        <p>Discover all our delicious offerings</p>
                    </div>
                </a>

                <a href="index.php?page=cart" class="nav-option">
                    <span class="nav-icon">🛒</span>
                    <div class="nav-content">
                        <h3>Check Your Cart</h3>
                        <p>Continue with your order</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <h3>🔍 Or search for something tasty:</h3>
            <form action="index.php" method="get" class="search-form">
                <input type="hidden" name="page" value="menu">
                <div class="search-input-container">
                    <input type="text"
                           name="search"
                           placeholder="Search for your favorite food..."
                           class="search-input"
                           id="errorPageSearch">
                    <button type="submit" class="search-btn">
                        <span class="search-icon">🔍</span>
                        Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Category Quick Links -->
        <div class="category-section">
            <h3>🍽️ Browse by Category:</h3>
            <div class="category-grid">
                <?php foreach ($categories as $key => $category): ?>
                    <a href="index.php?page=menu&category=<?php echo $key; ?>"
                       class="category-card"
                       data-category="<?php echo $key; ?>">
                        <span class="category-icon" style="color: <?php echo $category['color']; ?>">
                            <?php echo $category['icon']; ?>
                        </span>
                        <h4><?php echo $category['name']; ?></h4>
                        <p>Explore delicious <?php echo strtolower($category['name']); ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Featured Items to Help User -->
        <?php if (!empty($featuredItems)): ?>
            <div class="featured-section">
                <h3>⭐ Featured Recommendations:</h3>
                <p>Since you're here, why not try these popular choices?</p>
                <div class="featured-grid">
                    <?php foreach ($featuredItems as $item): ?>
                        <div class="featured-item" data-item-id="<?php echo $item['id']; ?>">
                            <img src="<?php echo $item['image_url'] ?: 'assets/images/default-food.jpg'; ?>"
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 class="featured-image">

                            <div class="featured-content">
                                <h4 class="featured-title"><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p class="featured-price">$<?php echo number_format($item['price'], 2); ?></p>

                                <!-- Social Proof -->
                                <?php
                                $socialProof = generateSocialProof($item['id']);
                                if ($socialProof):
                                    ?>
                                    <div class="social-proof">
                                        <span class="social-icon">👥</span>
                                        <?php echo $socialProof['text']; ?>
                                    </div>
                                <?php endif; ?>

                                <button class="btn-add-to-cart" data-item-id="<?php echo $item['id']; ?>">
                                    <span class="btn-icon">🛒</span>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Popular Items -->
        <?php if (!empty($popularItems)): ?>
            <div class="popular-section">
                <h3>🔥 What's Popular Right Now:</h3>
                <div class="popular-grid">
                    <?php foreach ($popularItems as $index => $item): ?>
                        <div class="popular-item" data-item-id="<?php echo $item['id']; ?>">
                            <div class="popular-rank">
                                <span class="rank-number">#<?php echo $index + 1; ?></span>
                                <span class="rank-badge">🔥</span>
                            </div>

                            <img src="<?php echo $item['image_url'] ?: 'assets/images/default-food.jpg'; ?>"
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 class="popular-image">

                            <div class="popular-content">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p class="popular-price">$<?php echo number_format($item['price'], 2); ?></p>
                                <button class="btn-quick-add" data-item-id="<?php echo $item['id']; ?>">
                                    Quick Add +
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Social Proof Section -->
        <div class="social-proof-section">
            <h3>🌟 Join Our Happy Customers:</h3>
            <div class="proof-stats">
                <div class="proof-stat">
                    <span class="proof-number"><?php echo number_format($totalUsers); ?>+</span>
                    <span class="proof-label">Satisfied Customers</span>
                </div>
                <div class="proof-stat">
                    <span class="proof-number"><?php echo $totalOrders; ?></span>
                    <span class="proof-label">Orders Today</span>
                </div>
                <div class="proof-stat">
                    <span class="proof-number">4.9</span>
                    <span class="proof-label">⭐ Average Rating</span>
                </div>
            </div>

            <div class="testimonial-carousel">
                <div class="testimonial active">
                    <p>"Amazing food and fast delivery! The recommendations are always spot on."</p>
                    <cite>- Sarah M.</cite>
                </div>
                <div class="testimonial">
                    <p>"Love the psychology-based suggestions. It knows exactly what I'm craving!"</p>
                    <cite>- Ahmad K.</cite>
                </div>
                <div class="testimonial">
                    <p>"Best food delivery experience in Seremban. Highly recommended!"</p>
                    <cite>- Lisa T.</cite>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="help-section">
            <h3>🤔 Still need help?</h3>
            <p>Our support team is here to assist you</p>

            <div class="help-options">
                <div class="help-option">
                    <span class="help-icon">💬</span>
                    <div class="help-content">
                        <h4>Live Chat</h4>
                        <p>Get instant help from our team</p>
                        <button class="help-btn" onclick="openLiveChat()">Start Chat</button>
                    </div>
                </div>

                <div class="help-option">
                    <span class="help-icon">📞</span>
                    <div class="help-content">
                        <h4>Call Us</h4>
                        <p>Speak directly with support</p>
                        <a href="tel:+60123456789" class="help-btn">+60 12-345-6789</a>
                    </div>
                </div>

                <div class="help-option">
                    <span class="help-icon">📧</span>
                    <div class="help-content">
                        <h4>Email Support</h4>
                        <p>Send us your questions</p>
                        <a href="mailto:help@foodiedelight.com" class="help-btn">Send Email</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recovery Actions -->
        <div class="recovery-section">
            <h3>🔄 Quick Actions:</h3>
            <div class="recovery-actions">
                <button class="recovery-btn" onclick="goBack()">
                    <span class="recovery-icon">⬅️</span>
                    Go Back
                </button>

                <button class="recovery-btn" onclick="reloadPage()">
                    <span class="recovery-icon">🔄</span>
                    Refresh Page
                </button>

                <a href="index.php?page=home" class="recovery-btn">
                    <span class="recovery-icon">🏠</span>
                    Homepage
                </a>

                <?php if (!isLoggedIn()): ?>
                    <a href="index.php?page=register" class="recovery-btn special">
                        <span class="recovery-icon">🎁</span>
                        Sign Up for Deals
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
    let testimonialIndex = 0;

    document.addEventListener('DOMContentLoaded', function() {
        initialize404Page();
        setupInteractions();
        startTestimonialRotation();
        trackUserBehavior();
    });

    function initialize404Page() {
        // Track 404 page load
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('404_page_loaded', {
                requested_page: '<?php echo htmlspecialchars($requestedPage); ?>',
                referrer: '<?php echo htmlspecialchars($referrer); ?>',
                timestamp: Date.now()
            });
        }

        // Auto-focus search input
        const searchInput = document.getElementById('errorPageSearch');
        if (searchInput) {
            setTimeout(() => {
                searchInput.focus();
            }, 1000);
        }

        // Add entrance animations
        animateElements();
    }

    function setupInteractions() {
        // Track navigation option clicks
        document.querySelectorAll('.nav-option').forEach(option => {
            option.addEventListener('click', function() {
                const destination = this.href.split('page=')[1] || 'unknown';
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('404_navigation_used', {
                        destination: destination,
                        option_text: this.querySelector('h3').textContent
                    });
                }
            });
        });

        // Track category selections
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', function() {
                const category = this.dataset.category;
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('404_category_selected', {
                        category: category,
                        source: '404_page'
                    });
                }
            });
        });

        // Track search usage
        const searchForm = document.querySelector('.search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                const searchQuery = this.querySelector('input[name="search"]').value;
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('404_search_performed', {
                        query: searchQuery,
                        source: '404_page'
                    });
                }
            });
        }

        // Setup add to cart functionality
        document.addEventListener('click', function(e) {
            if (e.target.matches('.btn-add-to-cart, .btn-quick-add')) {
                e.preventDefault();
                const itemId = e.target.dataset.itemId;

                // Use existing cart manager
                if (window.FoodieDelight && window.FoodieDelight.CartManager) {
                    window.FoodieDelight.CartManager.handleAddToCart(e.target);
                }

                // Track 404 conversion
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('404_conversion', {
                        item_id: itemId,
                        action: 'add_to_cart',
                        source: '404_page'
                    });
                }
            }
        });
    }

    function startTestimonialRotation() {
        const testimonials = document.querySelectorAll('.testimonial');
        if (testimonials.length <= 1) return;

        setInterval(() => {
            testimonials[testimonialIndex].classList.remove('active');
            testimonialIndex = (testimonialIndex + 1) % testimonials.length;
            testimonials[testimonialIndex].classList.add('active');
        }, 4000);
    }

    function animateElements() {
        // Animate error icon and number
        const errorIcon = document.querySelector('.error-icon');
        const errorNumber = document.querySelector('.error-number');

        if (errorIcon && errorNumber) {
            setTimeout(() => {
                errorIcon.style.animation = 'bounce 1s ease infinite';
                errorNumber.style.animation = 'glow 2s ease infinite';
            }, 500);
        }

        // Animate sections with stagger
        const sections = document.querySelectorAll('.quick-navigation, .search-section, .category-section, .featured-section');
        sections.forEach((section, index) => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(30px)';

            setTimeout(() => {
                section.style.transition = 'all 0.6s ease';
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, 200 + (index * 100));
        });
    }

    function trackUserBehavior() {
        // Track time spent on 404 page
        const startTime = Date.now();

        window.addEventListener('beforeunload', function() {
            const timeSpent = Date.now() - startTime;
            if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                window.FoodieDelight.PsychologyEngine.trackEvent('404_time_spent', {
                    time_spent: timeSpent,
                    page_recovered: false
                });
            }
        });

        // Track scroll depth
        let maxScroll = 0;
        window.addEventListener('scroll', () => {
            const scrollPercent = Math.round(
                (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100
            );

            if (scrollPercent > maxScroll) {
                maxScroll = scrollPercent;

                // Track scroll milestones
                if ([25, 50, 75, 100].includes(scrollPercent)) {
                    if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                        window.FoodieDelight.PsychologyEngine.trackEvent('404_scroll_milestone', {
                            depth: scrollPercent
                        });
                    }
                }
            }
        });

        // Track feature interactions
        document.querySelectorAll('.featured-item, .popular-item').forEach(item => {
            item.addEventListener('mouseenter', function() {
                const itemId = this.dataset.itemId;
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('404_item_interest', {
                        item_id: itemId,
                        interaction: 'hover'
                    });
                }
            });
        });
    }

    function goBack() {
        // Track back button usage
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('404_back_button_used', {
                timestamp: Date.now()
            });
        }

        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = 'index.php?page=home';
        }
    }

    function reloadPage() {
        // Track reload button usage
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('404_reload_button_used', {
                timestamp: Date.now()
            });
        }

        window.location.reload();
    }

    function openLiveChat() {
        // Track live chat usage
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('404_live_chat_opened', {
                timestamp: Date.now()
            });
        }

        // Simulate live chat opening
        alert('Live chat would open here. This could integrate with services like Intercom, Zendesk Chat, or a custom chat solution.');
    }

    // Add custom CSS animations
    const style = document.createElement('style');
    style.textContent = `
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

@keyframes glow {
    0%, 100% { text-shadow: 0 0 10px rgba(231, 76, 60, 0.5); }
    50% { text-shadow: 0 0 20px rgba(231, 76, 60, 0.8); }
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
`;
    document.head.appendChild(style);
</script>

<style>
    /* 404 page specific styles */
    .error-page {
        padding: 2rem 0;
        min-height: calc(100vh - 140px);
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .error-header {
        text-align: center;
        margin-bottom: 4rem;
        padding: 3rem 2rem;
        background: white;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .error-animation {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .error-icon {
        font-size: 4rem;
        animation: bounce 2s ease infinite;
    }

    .error-number {
        font-size: 8rem;
        font-weight: 900;
        color: var(--primary-red);
        text-shadow: 0 0 20px rgba(231, 76, 60, 0.3);
        animation: glow 3s ease infinite;
    }

    .error-title {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--text-color);
        font-family: var(--font-appetite);
    }

    .error-message {
        font-size: 1.3rem;
        color: #666;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .quick-navigation {
        background: white;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
        margin-bottom: 3rem;
    }

    .quick-navigation h2 {
        text-align: center;
        margin-bottom: 2rem;
        color: var(--text-color);
        font-size: 1.8rem;
    }

    .nav-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .nav-option {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 2rem;
        background: #f8f9fa;
        border-radius: var(--border-radius-large);
        text-decoration: none;
        color: inherit;
        transition: var(--transition-smooth);
        border: 2px solid transparent;
    }

    .nav-option:hover {
        background: rgba(231, 76, 60, 0.1);
        border-color: var(--primary-red);
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .nav-icon {
        font-size: 2.5rem;
        flex-shrink: 0;
    }

    .nav-content h3 {
        margin-bottom: 0.5rem;
        color: var(--text-color);
        font-size: 1.2rem;
    }

    .nav-content p {
        color: #666;
        font-size: 0.9rem;
    }

    .search-section {
        background: white;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
        margin-bottom: 3rem;
        text-align: center;
    }

    .search-section h3 {
        margin-bottom: 2rem;
        color: var(--text-color);
        font-size: 1.5rem;
    }

    .search-input-container {
        display: flex;
        max-width: 500px;
        margin: 0 auto;
        gap: 1rem;
    }

    .search-input {
        flex: 1;
        padding: 1rem 1.5rem;
        border: 2px solid #e0e0e0;
        border-radius: 50px;
        font-size: 1.1rem;
        transition: var(--transition-smooth);
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary-red);
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .search-btn {
        padding: 1rem 2rem;
        background: var(--primary-red);
        color: white;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .search-btn:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }

    .search-icon {
        font-size: 1.2rem;
    }

    .category-section {
        background: white;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
        margin-bottom: 3rem;
    }

    .category-section h3 {
        text-align: center;
        margin-bottom: 2rem;
        color: var(--text-color);
        font-size: 1.5rem;
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .category-card {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: var(--border-radius-large);
        text-align: center;
        text-decoration: none;
        color: inherit;
        transition: var(--transition-smooth);
        border: 2px solid transparent;
    }

    .category-card:hover {
        background: white;
        border-color: var(--primary-red);
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .category-icon {
        font-size: 3rem;
        display: block;
        margin-bottom: 1rem;
    }

    .category-card h4 {
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .category-card p {
        color: #666;
        font-size: 0.9rem;
    }

    .featured-section,
    .popular-section {
        background: white;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
        margin-bottom: 3rem;
    }

    .featured-section h3,
    .popular-section h3 {
        text-align: center;
        margin-bottom: 1rem;
        color: var(--text-color);
        font-size: 1.5rem;
    }

    .featured-section p {
        text-align: center;
        color: #666;
        margin-bottom: 2rem;
    }

    .featured-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }

    .featured-item {
        background: #f8f9fa;
        border-radius: var(--border-radius-large);
        overflow: hidden;
        transition: var(--transition-smooth);
        cursor: pointer;
    }

    .featured-item:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .featured-image {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .featured-content {
        padding: 1.5rem;
        text-align: center;
    }

    .featured-title {
        margin-bottom: 0.5rem;
        color: var(--text-color);
        font-size: 1.1rem;
    }

    .featured-price {
        font-size: 1.3rem;
        font-weight: bold;
        color: var(--primary-red);
        margin-bottom: 1rem;
    }

    .social-proof {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        font-size: 0.8rem;
        color: #666;
    }

    .social-icon {
        font-size: 1rem;
    }

    .btn-add-to-cart,
    .btn-quick-add {
        background: var(--primary-red);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin: 0 auto;
    }

    .btn-add-to-cart:hover,
    .btn-quick-add:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }

    .btn-icon {
        font-size: 1.1rem;
    }

    .popular-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .popular-item {
        background: #f8f9fa;
        border-radius: var(--border-radius-large);
        overflow: hidden;
        transition: var(--transition-smooth);
        cursor: pointer;
        position: relative;
    }

    .popular-item:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .popular-rank {
        position: absolute;
        top: 1rem;
        left: 1rem;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .rank-number {
        background: var(--primary-red);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .rank-badge {
        font-size: 1.2rem;
    }

    .popular-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }

    .popular-content {
        padding: 1rem;
        text-align: center;
    }

    .popular-content h4 {
        margin-bottom: 0.5rem;
        color: var(--text-color);
        font-size: 1rem;
    }

    .popular-price {
        font-weight: bold;
        color: var(--primary-red);
        margin-bottom: 1rem;
    }

    .social-proof-section {
        background: white;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
        margin-bottom: 3rem;
        text-align: center;
    }

    .social-proof-section h3 {
        margin-bottom: 2rem;
        color: var(--text-color);
        font-size: 1.5rem;
    }

    .proof-stats {
        display: flex;
        justify-content: center;
        gap: 3rem;
        margin-bottom: 3rem;
    }

    .proof-stat {
        text-align: center;
    }

    .proof-number {
        display: block;
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--primary-red);
        margin-bottom: 0.5rem;
    }

    .proof-label {
        color: #666;
        font-size: 0.9rem;
    }

    .testimonial-carousel {
        position: relative;
        height: 100px;
        max-width: 600px;
        margin: 0 auto;
    }

    .testimonial {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        opacity: 0;
        transition: opacity 0.5s ease;
        font-style: italic;
        color: #666;
    }

    .testimonial.active {
        opacity: 1;
    }

    .testimonial cite {
        display: block;
        margin-top: 1rem;
        font-style: normal;
        color: #999;
        font-size: 0.9rem;
    }

    .help-section {
        background: white;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
        margin-bottom: 3rem;
        text-align: center;
    }

    .help-section h3 {
        margin-bottom: 1rem;
        color: var(--text-color);
        font-size: 1.5rem;
    }

    .help-section p {
        color: #666;
        margin-bottom: 2rem;
    }

    .help-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }

    .help-option {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: var(--border-radius-large);
        text-align: center;
        transition: var(--transition-smooth);
    }

    .help-option:hover {
        background: rgba(231, 76, 60, 0.05);
        transform: translateY(-3px);
    }

    .help-icon {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 1rem;
    }

    .help-content h4 {
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .help-content p {
        color: #666;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .help-btn {
        background: var(--primary-red);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: var(--transition-smooth);
    }

    .help-btn:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }

    .recovery-section {
        background: white;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
        text-align: center;
    }

    .recovery-section h3 {
        margin-bottom: 2rem;
        color: var(--text-color);
        font-size: 1.5rem;
    }

    .recovery-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .recovery-btn {
        background: #6c757d;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--transition-smooth);
    }

    .recovery-btn:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .recovery-btn.special {
        background: var(--primary-red);
    }

    .recovery-btn.special:hover {
        background: #c0392b;
    }

    .recovery-icon {
        font-size: 1.1rem;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .error-animation {
            flex-direction: column;
            gap: 1rem;
        }

        .error-number {
            font-size: 6rem;
        }

        .error-title {
            font-size: 2rem;
        }

        .error-message {
            font-size: 1.1rem;
        }

        .nav-options {
            grid-template-columns: 1fr;
        }

        .nav-option {
            flex-direction: column;
            text-align: center;
        }

        .search-input-container {
            flex-direction: column;
        }

        .category-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }

        .featured-grid {
            grid-template-columns: 1fr;
        }

        .popular-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }

        .proof-stats {
            flex-direction: column;
            gap: 1rem;
        }

        .help-options {
            grid-template-columns: 1fr;
        }

        .recovery-actions {
            flex-direction: column;
            align-items: center;
        }
    }

    @media (max-width: 480px) {
        .error-page {
            padding: 1rem 0;
        }

        .error-header,
        .quick-navigation,
        .search-section,
        .category-section,
        .featured-section,
        .popular-section,
        .social-proof-section,
        .help-section,
        .recovery-section {
            padding: 2rem 1.5rem;
        }

        .error-number {
            font-size: 4rem;
        }

        .category-card,
        .help-option {
            padding: 1.5rem;
        }
    }
</style>