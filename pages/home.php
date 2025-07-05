<?php
// SUPER SIMPLE FIX - Add this right after your function calls in home.php

$timeGreeting = getTimeBasedGreeting();
$recommendedItems = getPersonalizedRecommendations($_SESSION['user_id'] ?? null, 6);
$trendingItems = getTrendingItems(4);
$featuredItems = getPsychologyMenuItems(null, 8);

// ✅ ONE-LINER FIX: Remove duplicates using array_unique with ID comparison
$recommendedItems = array_values(array_reduce($recommendedItems, function($carry, $item) {
    $ids = array_column($carry, 'id');
    if (!in_array($item['id'], $ids)) {
        $carry[] = $item;
    }
    return $carry;
}, []));

// Limit to exactly 3 items
$recommendedItems = array_slice($recommendedItems, 0, 3);

// ✅ Apply same fix to other sections if needed
$trendingItems = array_values(array_reduce($trendingItems, function($carry, $item) {
    $ids = array_column($carry, 'id');
    if (!in_array($item['id'], $ids)) {
        $carry[] = $item;
    }
    return $carry;
}, []));
$trendingItems = array_slice($trendingItems, 0, 3);

// Track page view
if (isset($_SESSION['user_id'])) {
    trackBehaviorLog($_SESSION['user_id'], 'homepage_view', [
        'time_of_day' => date('H:i'),
        'day_of_week' => date('l'),
        'psychology_profile' => $_SESSION['psychology_profile']
    ]);
}
?>

<!-- Hero Section with Appetite Triggers -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <div class="hero-greeting">
                <span class="greeting-emoji"><?php echo $timeGreeting['emoji']; ?></span>
                <h1 class="hero-title"><?php echo $timeGreeting['greeting']; ?></h1>
            </div>

            <h2 class="hero-subtitle appetite-trigger">
                <?php echo $timeGreeting['suggestion']; ?> 🍽️
            </h2>

            <p class="hero-description">
                Satisfy your cravings with our psychology-powered food ordering experience.
                Fresh ingredients, expert preparation, and delivery that brings joy to your door.
            </p>

            <div class="hero-actions">
                <a href="index.php?page=menu" class="hero-cta">
                    <span class="hero-cta-icon">🔥</span>
                    Order Now - 30 Min Delivery
                </a>

                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number" data-count="<?php echo getTotalOrdersToday(); ?>">0</span>
                        <span class="stat-label">Orders Today</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">4.9</span>
                        <span class="stat-label">⭐ Rating</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">15-30</span>
                        <span class="stat-label">Min Delivery</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Background Animation Elements -->
    <div class="hero-bg-elements">
        <div class="floating-food-icon" style="top: 20%; left: 10%;">🍕</div>
        <div class="floating-food-icon" style="top: 60%; right: 15%;">🍔</div>
        <div class="floating-food-icon" style="top: 30%; right: 30%;">🍰</div>
        <div class="floating-food-icon" style="bottom: 20%; left: 20%;">🥤</div>
    </div>
</section>

<!-- COMPLETE REPLACEMENT for quick-categories section -->
<section class="quick-categories">
    <div class="container">
        <!-- Enhanced header -->
        <div class="categories-header">
            <h2 class="section-title">What are you craving?</h2>
        </div>

        <!-- Enhanced categories grid -->
        <div class="categories-grid">
            <?php
            // Use your existing function or fallback
            $categories = function_exists('getMenuCategories') ? getMenuCategories() : [
                'meals' => ['name' => 'Meals', 'icon' => '🍽️', 'color' => '#e74c3c'],
                'drinks' => ['name' => 'Drinks', 'icon' => '🥤', 'color' => '#3498db'],
                'desserts' => ['name' => 'Desserts', 'icon' => '🍰', 'color' => '#f39c12'],
                'specials' => ['name' => 'Specials', 'icon' => '⭐', 'color' => '#27ae60']
            ];

            foreach ($categories as $key => $category):
                // Get count using your existing function
                $categoryCount = function_exists('getPsychologyMenuItems')
                    ? count(getPsychologyMenuItems($key) ?: [])
                    : rand(4, 12);

                // Enhanced descriptions for better readability
                $descriptions = [
                    'meals' => 'Hearty dishes & main courses',
                    'drinks' => 'Refreshing beverages & shakes',
                    'desserts' => 'Sweet treats & indulgences',
                    'specials' => 'Chef\'s recommendations'
                ];
                $description = $descriptions[$key] ?? 'Delicious options await';
                ?>

                <!-- Enhanced category card -->
                <a href="index.php?page=menu&category=<?php echo $key; ?>"
                   class="category-card enhanced-card"
                   data-category="<?php echo $key; ?>">

                    <!-- Icon with dynamic color -->
                    <div class="card-icon" style="color: <?php echo $category['color']; ?>">
                        <?php echo $category['icon']; ?>
                    </div>

                    <!-- Enhanced content -->
                    <div class="card-content">
                        <h3 class="card-title"><?php echo $category['name']; ?></h3>
                        <p class="card-description"><?php echo $description; ?></p>
                        <span class="card-count"><?php echo $categoryCount; ?> delicious options</span>
                    </div>

                    <!-- Hover effect -->
                    <div class="card-hover-effect"></div>
                </a>

            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- COMPLETELY FIXED "Just for You" section - No layout issues -->
<?php if (!empty($recommendedItems)): ?>
    <section class="trending-section enhanced-trending">
        <div class="container">
            <!-- Enhanced section header - Exact same classes as trending -->
            <div class="section-header trending-header">
                <h2 class="section-title trending-title">
                    <span class="title-icon trending-icon">🎯</span>
                    Just for You
                </h2>
                <p class="section-subtitle trending-subtitle">
                    Personalized picks based on your taste preferences
                </p>
            </div>

            <!-- Enhanced recommendations grid - Exact same classes as trending -->
            <div class="trending-grid enhanced-trending-grid">
                <?php foreach ($recommendedItems as $index => $item): ?>
                    <div class="trending-item enhanced-trending-item" data-item-id="<?php echo $item['id']; ?>">

                        <!-- Psychology Tags in rank position -->
                        <div class="trending-rank enhanced-rank">
                            <?php
                            // Use existing function to generate tags
                            $psychologyTags = generatePsychologyTags($item);
                            // Limit to maximum 2 tags for clean design
                            $displayTags = array_slice($psychologyTags, 0, 2);
                            foreach ($displayTags as $tagIndex => $tag):
                                ?>
                                <span class="rank-number enhanced-rank-number"><?php echo $tag['text']; ?></span>
                                <?php if ($tagIndex === 0): ?>
                                <span class="rank-badge enhanced-rank-badge">⭐</span>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- Enhanced image container - Exact same classes as trending -->
                        <div class="trending-image-container">
                            <img src="<?php echo $item['image_url'] ?: 'assets/images/default-food.jpg'; ?>"
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 class="trending-image enhanced-trending-image">

                            <!-- Enhanced image overlay - Exact same classes as trending -->
                            <div class="trending-overlay enhanced-trending-overlay"></div>
                        </div>

                        <!-- Enhanced content - Exact same classes as trending -->
                        <div class="trending-content enhanced-trending-content">
                            <h4 class="trending-title enhanced-trending-title"><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p class="trending-price enhanced-trending-price">$<?php echo number_format($item['price'], 2); ?></p>

                            <div class="trending-stats enhanced-trending-stats">
                                <span class="trend-score enhanced-trend-score">
                                    🍽️ Perfect match for you
                                </span>
                            </div>

                            <button class="btn-quick-add enhanced-quick-add" data-item-id="<?php echo $item['id']; ?>">
                                <span class="quick-add-icon">🛒</span>
                                <span class="quick-add-text">Add to Cart</span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
<!-- Trending Now Section -->
<!-- Enhanced Trending Now Section with Beautiful Design -->
<?php if (!empty($trendingItems)): ?>
    <section class="trending-section enhanced-trending">
        <div class="container">
            <!-- Enhanced section header -->
            <div class="section-header trending-header">
                <h2 class="section-title trending-title">
                    <span class="title-icon trending-icon">📈</span>
                    Trending This Week
                </h2>
                <p class="section-subtitle trending-subtitle">
                    See what everyone's ordering right now
                </p>
            </div>

            <!-- Enhanced trending grid -->
            <div class="trending-grid enhanced-trending-grid">
                <?php foreach ($trendingItems as $index => $item): ?>
                    <div class="trending-item enhanced-trending-item" data-item-id="<?php echo $item['id']; ?>">
                        <!-- Enhanced trending rank -->
                        <div class="trending-rank enhanced-rank">
                            <span class="rank-number enhanced-rank-number">#<?php echo $index + 1; ?></span>
                            <span class="rank-badge enhanced-rank-badge">🔥</span>
                        </div>

                        <!-- Enhanced image container -->
                        <div class="trending-image-container">
                            <img src="<?php echo $item['image_url'] ?: 'assets/images/default-food.jpg'; ?>"
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 class="trending-image enhanced-trending-image">

                            <!-- Enhanced image overlay -->
                            <div class="trending-overlay enhanced-trending-overlay"></div>
                        </div>

                        <!-- Enhanced content -->
                        <div class="trending-content enhanced-trending-content">
                            <h4 class="trending-title enhanced-trending-title"><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p class="trending-price enhanced-trending-price">$<?php echo number_format($item['price'], 2); ?></p>

                            <div class="trending-stats enhanced-trending-stats">
                                <span class="trend-score enhanced-trend-score">
                                    📊 <?php echo $item['trend_score'] ?? 0; ?> orders this week
                                </span>
                            </div>

                            <button class="btn-quick-add enhanced-quick-add" data-item-id="<?php echo $item['id']; ?>">
                                <span class="quick-add-icon">⚡</span>
                                <span class="quick-add-text">Quick Add</span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>


<!-- Enhanced Chef's Specials Section with Beautiful Design -->
<section class="featured-section enhanced-featured">
    <div class="container">
        <!-- Enhanced section header -->
        <div class="section-header featured-header">
            <h2 class="section-title featured-title">
                <span class="title-icon featured-icon">⭐</span>
                Chef's Specials
            </h2>
            <p class="section-subtitle featured-subtitle">
                Handpicked favorites that never disappoint
            </p>
        </div>

        <!-- Enhanced featured showcase -->
        <div class="featured-showcase enhanced-featured-showcase">
            <?php
            $featured = array_filter($featuredItems, function($item) {
                return $item['featured'] == 1;
            });
            $featured = array_slice($featured, 0, 3);

            foreach ($featured as $item):
                ?>
                <div class="featured-card enhanced-featured-card" data-item-id="<?php echo $item['id']; ?>">
                    <!-- Enhanced image container -->
                    <div class="featured-image-container enhanced-image-container">
                        <img src="<?php echo $item['image_url'] ?: 'assets/images/default-food.jpg'; ?>"
                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                             class="featured-image enhanced-featured-image">

                        <!-- Enhanced overlay with chef badge -->
                        <div class="featured-overlay enhanced-featured-overlay">
                            <div class="featured-badge enhanced-chef-badge">
                                <span class="badge-icon enhanced-badge-icon">👨‍🍳</span>
                                <span class="badge-text enhanced-badge-text">Chef's Choice</span>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced content -->
                    <div class="featured-content enhanced-featured-content">
                        <h3 class="featured-title enhanced-featured-title"><?php echo htmlspecialchars($item['name']); ?></h3>

                        <p class="featured-description enhanced-featured-description">
                            <?php echo enhanceDescription($item['description'], $item['sensory_words']); ?>
                        </p>

                        <div class="featured-footer enhanced-featured-footer">
                            <span class="featured-price enhanced-featured-price">$<?php echo number_format($item['price'], 2); ?></span>

                            <button class="btn-featured-order enhanced-order-btn" data-item-id="<?php echo $item['id']; ?>">
                                <span class="order-icon">🍽️</span>
                                <span class="order-text">Order Now</span>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Enhanced Psychology Call to Action Section -->
<section class="cta-section enhanced-cta">
    <div class="container">
        <div class="cta-content enhanced-cta-content">
            <div class="cta-text enhanced-cta-text">
                <h2 class="cta-title enhanced-cta-title">Ready to satisfy those cravings?</h2>
                <p class="cta-subtitle enhanced-cta-subtitle">
                    Join thousands of satisfied customers who trust us with their daily deliciousness.
                    Fast delivery, fresh ingredients, unbeatable taste.
                </p>

                <div class="cta-features enhanced-cta-features">
                    <div class="feature-item enhanced-feature-item">
                        <span class="feature-icon enhanced-feature-icon">⚡</span>
                        <span class="feature-text enhanced-feature-text">15-30 min delivery</span>
                    </div>
                    <div class="feature-item enhanced-feature-item">
                        <span class="feature-icon enhanced-feature-icon">🥘</span>
                        <span class="feature-text enhanced-feature-text">Fresh ingredients</span>
                    </div>
                    <div class="feature-item enhanced-feature-item">
                        <span class="feature-icon enhanced-feature-icon">⭐</span>
                        <span class="feature-text enhanced-feature-text">4.9/5 rating</span>
                    </div>
                </div>
            </div>

            <div class="cta-actions enhanced-cta-actions">
                <a href="index.php?page=menu" class="btn-primary-cta enhanced-primary-cta">
                    <span class="cta-icon enhanced-cta-icon">🍽️</span>
                    <span class="cta-text">Browse Full Menu</span>
                </a>

                <?php if (!isLoggedIn()): ?>
                    <a href="index.php?page=register" class="btn-secondary-cta enhanced-secondary-cta">
                        <span class="cta-icon enhanced-cta-icon">🎁</span>
                        <span class="cta-text">Sign Up for Deals</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Bulletproof JavaScript for Homepage - No Event Conflicts -->
<script>
    (function() {
        'use strict';

        // Namespace for home page functionality
        const HomePage = {
            initialized: false,
            observers: [],
            timers: [],

            // Cleanup function for memory management
            cleanup: function() {
                this.observers.forEach(observer => {
                    if (observer && typeof observer.disconnect === 'function') {
                        observer.disconnect();
                    }
                });
                this.timers.forEach(timer => clearInterval(timer));
                this.observers = [];
                this.timers = [];
            },

            // Safe initialization with error handling
            init: function() {
                if (this.initialized) {
                    console.log('HomePage already initialized');
                    return;
                }

                try {
                    this.animateCounters();
                    this.setupCategoryEffects();
                    this.initializeFloatingAnimations();
                    this.trackHomepageEngagement();
                    this.setupAccessibility();

                    this.initialized = true;
                    console.log('✅ HomePage initialized successfully');

                } catch (error) {
                    console.error('❌ HomePage initialization failed:', error);
                }
            }
        };

        // ========================================================================
        // SAFE COUNTER ANIMATION - No conflicts
        // ========================================================================
        HomePage.animateCounters = function() {
            try {
                const counters = document.querySelectorAll('[data-count]');
                if (!counters.length) return;

                counters.forEach(counter => {
                    try {
                        const target = parseInt(counter.dataset.count) || 0;
                        const duration = 2000;
                        const increment = target / (duration / 16);
                        let current = 0;

                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= target) {
                                current = target;
                                clearInterval(timer);
                                // Remove from tracking array
                                const index = this.timers.indexOf(timer);
                                if (index > -1) this.timers.splice(index, 1);
                            }

                            if (counter && counter.textContent !== undefined) {
                                counter.textContent = Math.floor(current);
                            }
                        }, 16);

                        this.timers.push(timer);

                    } catch (counterError) {
                        console.warn('Counter animation error:', counterError);
                    }
                });

            } catch (error) {
                console.warn('Counter setup error:', error);
            }
        };

        // ========================================================================
        // SAFE CATEGORY EFFECTS - No conflicts
        // ========================================================================
        HomePage.setupCategoryEffects = function() {
            try {
                const categoryCards = document.querySelectorAll('.category-card');
                if (!categoryCards.length) return;

                categoryCards.forEach(card => {
                    try {
                        // Hover enter effect
                        const handleMouseEnter = function() {
                            try {
                                if (this.style !== undefined) {
                                    this.style.transform = 'translateY(-10px) scale(1.02)';
                                }

                                // Safe tracking with multiple fallback checks
                                if (window.FoodieDelight &&
                                    window.FoodieDelight.PsychologyEngine &&
                                    typeof window.FoodieDelight.PsychologyEngine.trackEvent === 'function') {

                                    const category = this.dataset && this.dataset.category;
                                    if (category) {
                                        window.FoodieDelight.PsychologyEngine.trackEvent('category_interest', {
                                            category: category,
                                            interaction: 'hover',
                                            timestamp: Date.now()
                                        });
                                    }
                                }
                            } catch (hoverError) {
                                console.warn('Category hover effect error:', hoverError);
                            }
                        };

                        // Hover leave effect
                        const handleMouseLeave = function() {
                            try {
                                if (this.style !== undefined) {
                                    this.style.transform = 'translateY(0) scale(1)';
                                }
                            } catch (leaveError) {
                                console.warn('Category leave effect error:', leaveError);
                            }
                        };

                        // Click tracking (NOT add to cart - just navigation tracking)
                        const handleClick = function() {
                            try {
                                if (window.FoodieDelight &&
                                    window.FoodieDelight.PsychologyEngine &&
                                    typeof window.FoodieDelight.PsychologyEngine.trackEvent === 'function') {

                                    const category = this.dataset && this.dataset.category;
                                    if (category) {
                                        window.FoodieDelight.PsychologyEngine.trackEvent('category_selected', {
                                            category: category,
                                            source: 'homepage',
                                            timestamp: Date.now()
                                        });
                                    }
                                }
                            } catch (clickError) {
                                console.warn('Category click tracking error:', clickError);
                            }
                        };

                        // Safe event binding with error handling
                        if (card.addEventListener) {
                            card.addEventListener('mouseenter', handleMouseEnter);
                            card.addEventListener('mouseleave', handleMouseLeave);
                            card.addEventListener('click', handleClick);
                        }

                    } catch (cardError) {
                        console.warn('Category card setup error:', cardError);
                    }
                });

            } catch (error) {
                console.warn('Category effects setup error:', error);
            }
        };

        // ========================================================================
        // SAFE FLOATING ANIMATIONS - No conflicts
        // ========================================================================
        HomePage.initializeFloatingAnimations = function() {
            try {
                const floatingElements = document.querySelectorAll('.floating-food-icon');
                if (!floatingElements.length) return;

                floatingElements.forEach((element, index) => {
                    try {
                        if (element.style !== undefined) {
                            element.style.animationDelay = (index * 0.5) + 's';
                            element.style.animation = 'floatFood 6s ease-in-out infinite';
                        }
                    } catch (elementError) {
                        console.warn('Floating element setup error:', elementError);
                    }
                });

                // Safe CSS injection with duplication check
                this.injectFloatingCSS();

            } catch (error) {
                console.warn('Floating animations setup error:', error);
            }
        };

        // Safe CSS injection
        HomePage.injectFloatingCSS = function() {
            try {
                // Check if already exists to prevent duplicates
                if (document.getElementById('floating-animations-home')) return;

                const style = document.createElement('style');
                style.id = 'floating-animations-home';
                style.textContent = `
                @keyframes floatFood {
                    0%, 100% { transform: translateY(0) rotate(0deg); }
                    50% { transform: translateY(-20px) rotate(5deg); }
                }
                .floating-food-icon {
                    position: absolute;
                    font-size: 2rem;
                    opacity: 0.7;
                    pointer-events: none;
                    z-index: 1;
                    transition: opacity 0.3s ease;
                }
                /* Reduce motion for accessibility */
                @media (prefers-reduced-motion: reduce) {
                    .floating-food-icon {
                        animation: none !important;
                    }
                    @keyframes floatFood {
                        0%, 100% { transform: none; }
                    }
                }
            `;

                if (document.head && document.head.appendChild) {
                    document.head.appendChild(style);
                }

            } catch (error) {
                console.warn('CSS injection error:', error);
            }
        };

        // ========================================================================
        // SAFE ENGAGEMENT TRACKING - No conflicts
        // ========================================================================
        HomePage.trackHomepageEngagement = function() {
            try {
                // Check if IntersectionObserver is supported
                if (!window.IntersectionObserver) {
                    console.log('IntersectionObserver not supported, skipping engagement tracking');
                    return;
                }

                const sections = document.querySelectorAll('section[class*="section"]');
                if (!sections.length) return;

                sections.forEach(section => {
                    try {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    try {
                                        const sectionName = this.getSectionName(entry.target);

                                        if (window.FoodieDelight &&
                                            window.FoodieDelight.PsychologyEngine &&
                                            typeof window.FoodieDelight.PsychologyEngine.trackEvent === 'function') {

                                            window.FoodieDelight.PsychologyEngine.trackEvent('section_viewed', {
                                                section: sectionName,
                                                timestamp: Date.now(),
                                                viewport_ratio: Math.round(entry.intersectionRatio * 100)
                                            });
                                        }
                                    } catch (trackingError) {
                                        console.warn('Section tracking error:', trackingError);
                                    }
                                }
                            });
                        }, {
                            threshold: 0.5,
                            rootMargin: '0px 0px -10% 0px' // Only trigger when well into view
                        });

                        if (observer && observer.observe) {
                            observer.observe(section);
                            this.observers.push(observer);
                        }

                    } catch (observerError) {
                        console.warn('Observer setup error:', observerError);
                    }
                });

            } catch (error) {
                console.warn('Engagement tracking setup error:', error);
            }
        };

        // Helper function to safely get section name
        HomePage.getSectionName = function(element) {
            try {
                if (!element || !element.className) return 'unknown';

                const classList = element.className.split(' ');
                const sectionClass = classList.find(cls => cls.includes('section'));
                return sectionClass ? sectionClass.replace('-section', '') : 'unknown';

            } catch (error) {
                return 'unknown';
            }
        };

        // ========================================================================
        // ACCESSIBILITY ENHANCEMENTS - No conflicts
        // ========================================================================
        HomePage.setupAccessibility = function() {
            try {
                // Add keyboard navigation support for category cards
                const categoryCards = document.querySelectorAll('.category-card');

                categoryCards.forEach(card => {
                    try {
                        // Ensure keyboard accessibility
                        if (!card.getAttribute('tabindex') && !card.getAttribute('role')) {
                            card.setAttribute('tabindex', '0');
                            card.setAttribute('role', 'button');
                        }

                        // Add keyboard enter support
                        const handleKeyDown = function(e) {
                            if (e.key === 'Enter' || e.key === ' ') {
                                e.preventDefault();
                                this.click();
                            }
                        };

                        if (card.addEventListener) {
                            card.addEventListener('keydown', handleKeyDown);
                        }

                    } catch (accessibilityError) {
                        console.warn('Accessibility setup error:', accessibilityError);
                    }
                });

            } catch (error) {
                console.warn('Accessibility enhancements error:', error);
            }
        };

        // ========================================================================
        // SAFE INITIALIZATION - Bulletproof startup
        // ========================================================================

        // Multiple initialization strategies for maximum compatibility
        function initializeHomePage() {
            try {
                HomePage.init();
            } catch (error) {
                console.error('HomePage initialization completely failed:', error);
            }
        }

        // Strategy 1: DOMContentLoaded (preferred)
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeHomePage);
        } else {
            // Strategy 2: DOM already loaded
            initializeHomePage();
        }

        // Strategy 3: Fallback timeout initialization
        setTimeout(function() {
            if (!HomePage.initialized) {
                console.log('Fallback initialization triggered');
                initializeHomePage();
            }
        }, 2000);

        // Strategy 4: Window load as final fallback
        window.addEventListener('load', function() {
            if (!HomePage.initialized) {
                console.log('Window load fallback triggered');
                initializeHomePage();
            }
        });

        // ========================================================================
        // CLEANUP ON PAGE UNLOAD - Prevent memory leaks
        // ========================================================================
        window.addEventListener('beforeunload', function() {
            try {
                HomePage.cleanup();
            } catch (error) {
                console.warn('Cleanup error:', error);
            }
        });

        // ========================================================================
        // GLOBAL EXPOSURE (for debugging and compatibility)
        // ========================================================================
        if (typeof window !== 'undefined') {
            window.FoodieDelightHomePage = HomePage;
        }

    })();
</script>

<!-- Additional CSS for Homepage Specific Styles -->
<style>
    /* Homepage specific psychology-enhanced styles */
    .hero-section {
        position: relative;
        overflow: hidden;
        min-height: 80vh;
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
        color: white;
    }

    .hero-greeting {
        text-align: center;
        margin-bottom: 1rem;
    }

    .greeting-emoji {
        font-size: 3rem;
        display: block;
        margin-bottom: 0.5rem;
        animation: appetitePulse 3s ease infinite;
    }

    .hero-stats {
        display: flex;
        gap: 2rem;
        justify-content: center;
        margin-top: 2rem;
        flex-wrap: wrap;
    }

    .stat-item {
        text-align: center;
        background: rgba(255, 255, 255, 0.1);
        padding: 1rem 1.5rem;
        border-radius: 15px;
        backdrop-filter: blur(10px);
    }

    .stat-number {
        display: block;
        font-size: 1.8rem;
        font-weight: 700;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .quick-categories {
        padding: 4rem 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }

    .category-card {
        background: white;
        padding: 2rem;
        border-radius: var(--border-radius-large);
        text-align: center;
        box-shadow: var(--shadow-md);
        transition: var(--transition-smooth);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }

    .category-card:hover {
        box-shadow: var(--shadow-lg);
    }

    .category-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }

    .category-hover-effect {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-appetite);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .category-card:hover .category-hover-effect {
        transform: scaleX(1);
    }

    @keyframes appetitePulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .hero-stats {
            gap: 1rem;
        }

        .categories-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .category-card {
            padding: 1.5rem;
        }
    }

</style>
<script>
    // Clean JavaScript - Works with PHP-generated content
    document.addEventListener('DOMContentLoaded', function() {
        // Clean interactions with bulletproof error handling
        document.querySelectorAll('.clean-recommendation').forEach(card => {
            try {
                // Clean add to cart
                const addBtn = card.querySelector('.clean-add-btn');
                if (addBtn) {
                    addBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const originalText = this.innerHTML;

                        this.innerHTML = '<span class="btn-icon">⏳</span><span class="btn-text">Adding...</span>';
                        this.style.opacity = '0.7';

                        const itemId = this.getAttribute('data-item-id');

                        // Use existing cart functionality if available
                        try {
                            if (window.FoodieDelight && window.FoodieDelight.CartManager) {
                                window.FoodieDelight.CartManager.handleAddToCart(this);
                            } else if (window.PsychologyEngine) {
                                window.PsychologyEngine.trackEvent('recommendation_added', {
                                    item_id: itemId,
                                    source: 'just_for_you',
                                    timestamp: Date.now()
                                });
                            }
                        } catch (e) {
                            console.log('Cart/Tracking not available');
                        }

                        // Success feedback
                        setTimeout(() => {
                            this.innerHTML = '<span class="btn-icon">✅</span><span class="btn-text">Added!</span>';
                            this.style.opacity = '1';

                            setTimeout(() => {
                                this.innerHTML = originalText;
                            }, 1500);
                        }, 600);
                    });
                }

                // Clean favorite button
                const favBtn = card.querySelector('.clean-fav-btn');
                if (favBtn) {
                    favBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        this.innerHTML = this.innerHTML === '❤️' ? '🤍' : '❤️';

                        // Use existing tracking if available
                        try {
                            if (window.PsychologyEngine) {
                                window.PsychologyEngine.trackEvent('item_favorited', {
                                    item_id: this.getAttribute('data-item-id'),
                                    source: 'recommendations',
                                    timestamp: Date.now()
                                });
                            }
                        } catch (e) {
                            console.log('Tracking not available');
                        }
                    });
                }

                // Clean viewport tracking - compatible with existing system
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            try {
                                if (window.PsychologyEngine) {
                                    const itemId = entry.target.getAttribute('data-item-id');
                                    window.PsychologyEngine.trackEvent('recommendation_viewed', {
                                        item_id: itemId,
                                        timestamp: Date.now()
                                    });
                                }
                            } catch (e) {
                                console.log('Tracking not available');
                            }
                        }
                    });
                }, { threshold: 0.5 });

                observer.observe(card);

            } catch (e) {
                console.log('Card setup error:', e);
            }
        });
    });
    / Enhanced JavaScript - Compatible with existing functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced trending interactions
        document.querySelectorAll('.enhanced-trending-item').forEach(item => {
            try {
                // Enhanced quick add functionality
                const quickAddBtn = item.querySelector('.enhanced-quick-add');
                if (quickAddBtn) {
                    quickAddBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const originalText = this.innerHTML;
                        const itemId = this.getAttribute('data-item-id');

                        // Visual feedback
                        this.innerHTML = '<span class="quick-add-icon">⏳</span><span class="quick-add-text">Adding...</span>';
                        this.style.opacity = '0.7';

                        // Use existing cart functionality if available
                        try {
                            if (window.FoodieDelight && window.FoodieDelight.CartManager) {
                                window.FoodieDelight.CartManager.handleAddToCart(this);
                            } else if (window.PsychologyEngine) {
                                window.PsychologyEngine.trackEvent('trending_item_added', {
                                    item_id: itemId,
                                    source: 'trending_section',
                                    timestamp: Date.now()
                                });
                            }
                        } catch (e) {
                            console.log('Cart/Tracking not available');
                        }

                        // Success feedback
                        setTimeout(() => {
                            this.innerHTML = '<span class="quick-add-icon">✅</span><span class="quick-add-text">Added!</span>';
                            this.style.opacity = '1';

                            setTimeout(() => {
                                this.innerHTML = originalText;
                            }, 1500);
                        }, 600);
                    });
                }

                // Enhanced hover tracking
                item.addEventListener('mouseenter', function() {
                    try {
                        if (window.PsychologyEngine) {
                            const itemId = this.getAttribute('data-item-id');
                            window.PsychologyEngine.trackEvent('trending_item_hover', {
                                item_id: itemId,
                                source: 'trending_section',
                                timestamp: Date.now()
                            });
                        }
                    } catch (e) {
                        console.log('Tracking not available');
                    }
                });

                // Enhanced viewport tracking
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            try {
                                if (window.PsychologyEngine) {
                                    const itemId = entry.target.getAttribute('data-item-id');
                                    window.PsychologyEngine.trackEvent('trending_item_viewed', {
                                        item_id: itemId,
                                        source: 'trending_section',
                                        timestamp: Date.now()
                                    });
                                }
                            } catch (e) {
                                console.log('Tracking not available');
                            }
                        }
                    });
                }, { threshold: 0.5 });

                observer.observe(item);

            } catch (e) {
                console.log('Trending item setup error:', e);
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced featured items interactions
        document.querySelectorAll('.enhanced-featured-card').forEach(item => {
            try {
                // Enhanced order button functionality
                const orderBtn = item.querySelector('.enhanced-order-btn');
                if (orderBtn) {
                    orderBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const originalText = this.innerHTML;
                        const itemId = this.getAttribute('data-item-id');

                        // Visual feedback
                        this.innerHTML = '<span class="order-icon">⏳</span><span class="order-text">Adding...</span>';
                        this.style.opacity = '0.7';

                        // Use existing cart functionality if available
                        try {
                            if (window.FoodieDelight && window.FoodieDelight.CartManager) {
                                window.FoodieDelight.CartManager.handleAddToCart(this);
                            } else if (window.PsychologyEngine) {
                                window.PsychologyEngine.trackEvent('featured_item_ordered', {
                                    item_id: itemId,
                                    source: 'chefs_specials',
                                    timestamp: Date.now()
                                });
                            }
                        } catch (e) {
                            console.log('Cart/Tracking not available');
                        }

                        // Success feedback
                        setTimeout(() => {
                            this.innerHTML = '<span class="order-icon">✅</span><span class="order-text">Added!</span>';
                            this.style.opacity = '1';

                            setTimeout(() => {
                                this.innerHTML = originalText;
                            }, 1500);
                        }, 600);
                    });
                }

                // Enhanced hover tracking
                item.addEventListener('mouseenter', function() {
                    try {
                        if (window.PsychologyEngine) {
                            const itemId = this.getAttribute('data-item-id');
                            window.PsychologyEngine.trackEvent('featured_item_hover', {
                                item_id: itemId,
                                source: 'chefs_specials',
                                timestamp: Date.now()
                            });
                        }
                    } catch (e) {
                        console.log('Tracking not available');
                    }
                });

                // Enhanced viewport tracking
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            try {
                                if (window.PsychologyEngine) {
                                    const itemId = entry.target.getAttribute('data-item-id');
                                    window.PsychologyEngine.trackEvent('featured_item_viewed', {
                                        item_id: itemId,
                                        source: 'chefs_specials',
                                        timestamp: Date.now()
                                    });
                                }
                            } catch (e) {
                                console.log('Tracking not available');
                            }
                        }
                    });
                }, { threshold: 0.5 });

                observer.observe(item);

            } catch (e) {
                console.log('Featured item setup error:', e);
            }
        });
    });
    // Enhanced JavaScript - Compatible with existing functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced CTA interactions
        document.querySelectorAll('.enhanced-primary-cta, .enhanced-secondary-cta').forEach(button => {
            try {
                // Enhanced click tracking
                button.addEventListener('click', function(e) {
                    const buttonType = this.classList.contains('enhanced-primary-cta') ? 'primary' : 'secondary';
                    const targetPage = this.getAttribute('href');

                    // Track CTA clicks
                    try {
                        if (window.PsychologyEngine) {
                            window.PsychologyEngine.trackEvent('cta_clicked', {
                                button_type: buttonType,
                                target_page: targetPage,
                                source: 'enhanced_cta_section',
                                timestamp: Date.now()
                            });
                        }
                    } catch (e) {
                        console.log('Tracking not available');
                    }

                    // Visual feedback
                    const originalTransform = this.style.transform;
                    this.style.transform = 'scale(0.98)';

                    setTimeout(() => {
                        this.style.transform = originalTransform;
                    }, 150);
                });

                // Enhanced hover tracking
                button.addEventListener('mouseenter', function() {
                    try {
                        if (window.PsychologyEngine) {
                            const buttonType = this.classList.contains('enhanced-primary-cta') ? 'primary' : 'secondary';
                            window.PsychologyEngine.trackEvent('cta_hover', {
                                button_type: buttonType,
                                source: 'enhanced_cta_section',
                                timestamp: Date.now()
                            });
                        }
                    } catch (e) {
                        console.log('Tracking not available');
                    }
                });

            } catch (e) {
                console.log('CTA button setup error:', e);
            }
        });

        // Enhanced feature items hover effects
        document.querySelectorAll('.enhanced-feature-item').forEach(feature => {
            try {
                feature.addEventListener('mouseenter', function() {
                    // Add subtle scale effect
                    this.style.transform = 'translateY(-4px) scale(1.02)';
                });

                feature.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                });

            } catch (e) {
                console.log('Feature item setup error:', e);
            }
        });

        // Enhanced section visibility tracking
        const section = document.querySelector('.enhanced-cta');
        if (section) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        try {
                            if (window.PsychologyEngine) {
                                window.PsychologyEngine.trackEvent('cta_section_viewed', {
                                    timestamp: Date.now(),
                                    scroll_depth: Math.round(entry.intersectionRatio * 100)
                                });
                            }
                        } catch (e) {
                            console.log('Tracking not available');
                        }
                    }
                });
            }, { threshold: 0.5 });

            observer.observe(section);
        }
    });
</script>