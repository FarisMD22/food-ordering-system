<?php
// pages/menu.php - Complete Enhanced Menu Page with Dark Theme
$selectedCategory = $_GET['category'] ?? null;
$searchQuery = $_GET['search'] ?? '';
$menuItems = getPsychologyMenuItems($selectedCategory);
$categories = getMenuCategories();

// Filter by search if provided
if ($searchQuery) {
    $menuItems = array_filter($menuItems, function($item) use ($searchQuery) {
        return stripos($item['name'], $searchQuery) !== false ||
            stripos($item['description'], $searchQuery) !== false;
    });
}

// Track menu page view
if (isset($_SESSION['user_id'])) {
    trackBehaviorLog($_SESSION['user_id'], 'menu_page_view', [
        'category' => $selectedCategory,
        'search' => $searchQuery,
        'items_count' => count($menuItems)
    ]);
}
?>

<section class="menu-page">
    <div class="container">
        <!-- Enhanced Page Header -->
        <div class="page-header">
            <h1>Our Delicious Menu</h1>
            <p>Discover amazing flavors crafted with passion</p>
        </div>

        <!-- NEW LAYOUT: Stacked Search and Filters -->
        <div class="menu-controls">
            <!-- Search Section (Full Width) -->
            <div class="search-section">
                <form method="GET" action="index.php" class="search-form">
                    <input type="hidden" name="page" value="menu">
                    <?php if ($selectedCategory): ?>
                        <input type="hidden" name="category" value="<?php echo $selectedCategory; ?>">
                    <?php endif; ?>
                    <div class="search-wrapper">
                        <span class="search-icon">🔍</span>
                        <input type="text"
                               name="search"
                               id="menuSearch"
                               placeholder="Search for delicious food..."
                               value="<?php echo htmlspecialchars($searchQuery); ?>"
                               class="search-input">
                        <button type="submit" class="search-btn">Search</button>
                    </div>
                </form>
            </div>

            <!-- Filters Section (Centered) -->
            <div class="filters-section">
                <div class="category-filters">
                    <a href="index.php?page=menu"
                       class="filter-btn category-filter <?php echo !$selectedCategory ? 'active' : ''; ?>"
                       data-category="all">
                        <span class="filter-icon">🍽️</span>
                        <span class="filter-text">All Items</span>
                    </a>
                    <?php foreach ($categories as $key => $category): ?>
                        <a href="index.php?page=menu&category=<?php echo $key; ?>"
                           class="filter-btn category-filter <?php echo $selectedCategory === $key ? 'active' : ''; ?>"
                           data-category="<?php echo $key; ?>">
                            <span class="filter-icon"><?php echo $category['icon']; ?></span>
                            <span class="filter-text"><?php echo $category['name']; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Enhanced Menu Items Grid -->
        <div class="menu-items">
            <?php if (empty($menuItems)): ?>
                <div class="no-items" id="noResultsMessage">
                    <div class="no-results-content">
                        <span class="no-results-icon">🔍</span>
                        <h3>No items found</h3>
                        <p>Try adjusting your search or browse our categories</p>
                        <a href="index.php?page=menu" class="btn-secondary">View All Items</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Enhanced menu items grid - Dark Theme (like home.php categories) -->
                <div class="menu-items-grid dark-theme">
                    <?php foreach ($menuItems as $item): ?>
                        <!-- Enhanced menu item card - Dark Theme -->
                        <div class="menu-item-card enhanced-item-card dark-card"
                             data-item-id="<?php echo $item['id']; ?>"
                             data-category="<?php echo $item['category']; ?>"
                             data-appetite-score="<?php echo $item['appetite_score'] ?? 5; ?>"
                             data-margin="<?php echo $item['margin_level'] ?? 'medium'; ?>">

                            <!-- Featured Badge (if featured) -->
                            <?php if ($item['featured']): ?>
                                <div class="featured-badge dark-badge">⭐ Featured</div>
                            <?php endif; ?>

                            <!-- Psychology Tags (max 2) -->
                            <?php
                            $psychologyTags = generatePsychologyTags($item);
                            $displayTags = array_slice($psychologyTags, 0, 2);
                            if (!empty($displayTags)):
                                ?>
                                <div class="psychology-tags dark-tags">
                                    <?php foreach ($displayTags as $tag): ?>
                                        <span class="psychology-tag dark-tag"><?php echo $tag['text']; ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Item Image/Icon -->
                            <div class="card-image">
                                <?php if ($item['image_url']): ?>
                                    <img src="<?php echo $item['image_url']; ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         class="item-image">
                                <?php else: ?>
                                    <div class="item-icon">🍽️</div>
                                <?php endif; ?>
                            </div>

                            <!-- Enhanced content -->
                            <div class="card-content">
                                <h3 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h3>

                                <p class="card-description">
                                    <?php echo enhanceDescription($item['description'], $item['sensory_words']); ?>
                                </p>

                                <div class="card-meta">
                                    <span class="card-price">$<?php echo number_format($item['price'], 2); ?></span>
                                    <span class="card-category"><?php echo $categories[$item['category']]['name'] ?? $item['category']; ?></span>
                                </div>

                                <!-- Social Proof (if available) -->
                                <?php
                                $socialProof = generateSocialProof($item['id']);
                                if ($socialProof):
                                    ?>
                                    <div class="social-proof dark-social">
                                        <span class="social-proof-icon">👥</span>
                                        <?php echo $socialProof['text']; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Action buttons -->
                            <div class="card-actions">
                                <?php if ($item['available']): ?>
                                    <button class="btn-add-to-cart dark-btn" data-item-id="<?php echo $item['id']; ?>">
                                        <span class="btn-icon">🛒</span>
                                        <span class="btn-text">Add to Cart</span>
                                    </button>
                                    <button class="btn-favorite dark-fav" data-item-id="<?php echo $item['id']; ?>">
                                        🤍
                                    </button>
                                <?php else: ?>
                                    <button class="btn-unavailable dark-btn" disabled>
                                        <span class="btn-text">Not Available</span>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- Hover effect -->
                            <div class="card-hover-effect"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>        <!-- Results Info -->
        <?php if (!empty($menuItems)): ?>
            <div class="results-info">
                <p>Showing <?php echo count($menuItems); ?> items
                    <?php if ($selectedCategory): ?>
                        in "<?php echo $categories[$selectedCategory]['name']; ?>"
                    <?php endif; ?>
                    <?php if ($searchQuery): ?>
                        for "<?php echo htmlspecialchars($searchQuery); ?>"
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Enhanced Recommended Items -->
        <?php
        $personalizedItems = getPersonalizedRecommendations($_SESSION['user_id'] ?? null, 3);
        if (!empty($personalizedItems) && empty($searchQuery)):
            ?>
            <div class="menu-recommendations">
                <h3>Recommended for You</h3>
                <div class="recommendation-grid">
                    <?php foreach ($personalizedItems as $item): ?>
                        <div class="recommendation-item enhanced-recommendation">
                            <img src="<?php echo $item['image_url'] ?: 'assets/images/default-food.jpg'; ?>"
                                 alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="recommendation-content">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p class="rec-price">$<?php echo number_format($item['price'], 2); ?></p>
                                <button class="btn-add-to-cart rec-btn" data-item-id="<?php echo $item['id']; ?>">
                                    <span class="btn-icon">🛒</span>
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>

    /* Dark Theme Menu Items Grid - BALANCED VERSION */
    .menu-items-grid.dark-theme {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
        padding: 2rem 0;
    }

    .menu-item-card.dark-card {
        background: rgba(255, 255, 255, 0.05);
        border: 0 solid rgba(255, 255, 255, 0.1);
        padding: 1.5rem;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgb(30, 31, 34);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        color: #ffffff;
        backdrop-filter: blur(15px);
        display: flex;
        flex-direction: column;
        min-height: 480px;
    }

    /* KEEP the nice hover animation */
    .menu-item-card.dark-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow:
                0 20px 60px rgba(231, 76, 60, 0.3),    /* Red */
                0 25px 70px rgba(243, 156, 18, 0.2),   /* Orange */
                0 30px 80px rgba(39, 174, 96, 0.15),   /* Green */
                0 35px 90px rgba(52, 152, 219, 0.1);   /* Blue */
        border-color: rgba(231, 76, 60, 0.5);
        background: rgba(255, 255, 255, 0.1);
    }

    /* KEEP the top gradient border */
    .menu-item-card.dark-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #e74c3c, #f39c12, #27ae60, #3498db);
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: 2;
    }

    .menu-item-card.dark-card:hover::before {
        opacity: 1;
    }

    /* Simple Featured Badge */
    .featured-badge.dark-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, #f39c12, #e67e22);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 3;
    }


    /* Psychology Tags - POSITIONED TO AVOID IMAGE CONTENT */
    .psychology-tags.dark-tags {
        position: absolute;
        top: 1rem;
        left: 1rem;
        right: 4rem; /* Leave space for Featured badge */
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
        z-index: 3;
    }
    .psychology-tag.dark-tag {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 0.3rem 0.7rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        align-self: flex-start;
        max-width: fit-content; /* Only as wide as needed */
    }

    /* Simple Card Image */
    .card-image {
        height: 200px;
        margin-bottom: 1.5rem;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
    }

    .item-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .menu-item-card:hover .item-image {
        transform: scale(1.05);
    }

    .item-icon {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        background: rgba(231, 76, 60, 0.1);
        color: #e74c3c;
    }

    /* Simple Content - ONE LINE DESCRIPTIONS */
    .dark-card .card-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.5rem;
        font-family: 'Inter', sans-serif;
        line-height: 1.3;
    }

    .dark-card .card-description {
        font-size: 0.9rem;
        color: #d0d0d0;
        margin-bottom: 1rem;
        line-height: 1.4;
        flex-grow: 1;
        font-weight: 600;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding: 0.8rem 0;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .card-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: #e74c3c;
    }

    .card-category {
        background: rgba(255, 255, 255, 0.1);
        color: #b0b0b0;
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    /* Simple Social Proof */
    .social-proof.dark-social {
        font-size: 0.8rem;
        color: #b0b0b0;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        /* Ensure only one line */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Simple Action Buttons */
    .card-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-top: auto;
    }

    .btn-add-to-cart.dark-btn {
        flex: 1;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: none;
        padding: 0.8rem 1rem;
        border-radius: 15px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .btn-add-to-cart.dark-btn:hover {
        background: linear-gradient(135deg, #c0392b, #a93226);
        transform: translateY(-2px);
    }

    .btn-unavailable.dark-btn {
        flex: 1;
        background: #555;
        color: white;
        border: none;
        padding: 0.8rem 1rem;
        border-radius: 15px;
        cursor: not-allowed;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .btn-favorite.dark-fav {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-favorite.dark-fav:hover {
        background: rgba(231, 76, 60, 0.2);
        transform: scale(1.1);
    }

    /* KEEP the nice hover effect */
    .dark-card .card-hover-effect {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #e74c3c, #f39c12, #27ae60, #3498db);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .dark-card:hover .card-hover-effect {
        transform: scaleX(1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .menu-items-grid.dark-theme {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .menu-item-card.dark-card {
            min-height: 480px;
            padding: 1.25rem;
        }

        .card-image {
            height: 180px;
        }
    }

    /* UNIFIED MENU CARD STYLES - Same look for all cards */

    /* Base Menu Card - Unified styling for ALL cards */
    .menu-item.enhanced-menu-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 0;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        height: 520px;
        display: flex;
        flex-direction: column;
        backdrop-filter: blur(15px);
    }

    /* UPDATED: */
    .menu-item.enhanced-menu-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(231, 76, 60, 0.5);
        background: rgba(255, 255, 255, 0.08);
    }

    /* Unified Top Border - Same for ALL cards */
    .menu-item.enhanced-menu-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #e74c3c, #f39c12, #27ae60, #3498db);
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: 2;
    }

    .menu-item.enhanced-menu-card:hover::before {
        opacity: 1;
    }

    /* Unified Featured Badge - Same styling for all featured items */
    .enhanced-menu-card .featured-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, #f39c12, #e67e22);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 700;
        z-index: 3;
        box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);
    }

    /* UNIFIED Psychology Tags - Same styling for ALL tags */
    .enhanced-menu-card .psychology-tags {
        position: absolute;
        top: 1rem;
        left: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        z-index: 3;
        max-width: 60%;
    }

    .enhanced-menu-card .psychology-tag,
    .enhanced-menu-card .unified-tag {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3);
        max-width: 140px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        backdrop-filter: blur(10px);
    }

    /* UNIFIED Urgency Indicators - Same styling for ALL urgency indicators */
    .enhanced-menu-card .urgency-indicator,
    .enhanced-menu-card .unified-urgency {
        position: absolute;
        top: 4rem;
        left: 1rem;
        background: linear-gradient(135deg, #ff6b35, #e74c3c);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        z-index: 3;
        box-shadow: 0 3px 12px rgba(255, 107, 53, 0.5);
        max-width: 160px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Unified Image Styling */
    .enhanced-menu-card .enhanced-image-wrapper {
        position: relative;
        overflow: hidden;
        height: 220px;
    }

    .enhanced-menu-card .menu-item-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .enhanced-menu-card:hover .menu-item-image {
        transform: scale(1.1);
    }

    .enhanced-menu-card .enhanced-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(231, 76, 60, 0.1));
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }

    .enhanced-menu-card:hover .enhanced-image-overlay {
        opacity: 1;
    }

    /* Unified Content Styling */
    .enhanced-menu-card .menu-item-content {
        padding: 1.75rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 2;
    }

    .enhanced-menu-card .menu-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        gap: 1rem;
    }

    .enhanced-menu-card .menu-item-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.5rem;
        font-family: 'Inter', sans-serif;
        line-height: 1.3;
        transition: color 0.3s ease;
    }

    .enhanced-menu-card:hover .menu-item-title {
        color: #e74c3c;
    }

    .enhanced-menu-card .social-proof {
        font-size: 0.8rem;
        color: #b0b0b0;
        margin-top: 0.3rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-weight: 500;
    }

    .enhanced-menu-card .menu-item-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: #e74c3c;
        flex-shrink: 0;
        background: linear-gradient(135deg, #e74c3c, #f39c12);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .enhanced-menu-card .menu-item-description {
        color: #d0d0d0;
        line-height: 1.6;
        margin-bottom: auto;
        font-weight: 400;
        font-size: 0.95rem;
    }

    .enhanced-menu-card .sensory-word {
        color: #e74c3c;
        font-weight: 600;
        background: rgba(231, 76, 60, 0.1);
        padding: 0.1rem 0.3rem;
        border-radius: 4px;
    }

    /* Unified Meta Styling */
    .enhanced-menu-card .menu-item-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 1.25rem 0;
        font-size: 0.85rem;
    }

    .enhanced-menu-card .item-category {
        color: #b0b0b0;
        background: rgba(255, 255, 255, 0.1);
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .enhanced-menu-card .unavailable-badge {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Unified Action Buttons */
    .enhanced-menu-card .menu-item-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-top: auto;
    }

    .enhanced-menu-card .btn-add-to-cart {
        flex: 1;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: none;
        padding: 1rem 1.25rem;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
    }

    .enhanced-menu-card .btn-add-to-cart:hover {
        background: linear-gradient(135deg, #c0392b, #a93226);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
    }

    .enhanced-menu-card .btn-unavailable {
        flex: 1;
        background: #555;
        color: white;
        border: none;
        padding: 1rem 1.25rem;
        border-radius: 20px;
        cursor: not-allowed;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .enhanced-menu-card .btn-favorite {
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.3rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        padding: 0;
        backdrop-filter: blur(10px);
    }

    .enhanced-menu-card .btn-favorite:hover,
    .enhanced-menu-card .btn-favorite.favorited {
        border-color: #e74c3c;
        background: rgba(231, 76, 60, 0.2);
        transform: scale(1.1);
    }

    /* Unified Hover Effect Animation */
    .enhanced-menu-card .enhanced-card-hover-effect {
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(231, 76, 60, 0.1), transparent);
        transition: left 0.8s ease;
        pointer-events: none;
        z-index: 1;
    }

    .enhanced-menu-card:hover .enhanced-card-hover-effect {
        left: 100%;
    }

    /* REMOVE ALL CONDITIONAL STYLING */
    /* Remove high-margin specific styling */
    .menu-item.enhanced-menu-card.high-margin,
    .enhanced-menu-card.high-margin {
        border-left: none !important;
    }



    /* Enhanced Menu Page Styles - Dark Theme with New Layout */
    .menu-page {
        padding: 2rem 0;
        background: #1a1a1a;
        min-height: 100vh;
        color: #ffffff;
    }

    /* Enhanced Page Header */
    .page-header {
        text-align: center;
        margin-bottom: 3rem;
        padding: 2rem 0;
    }

    .page-header h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #e74c3c;
        font-weight: 800;
        background: linear-gradient(135deg, #e74c3c, #f39c12);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-header p {
        font-size: 1.2rem;
        color: #b0b0b0;
        font-weight: 500;
    }

    /* NEW LAYOUT: Stacked Menu Controls */
    .menu-controls {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        margin-bottom: 3rem;
    }

    /* Search Section - Full Width */
    .search-section {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .search-form {
        width: 100%;
        max-width: 600px;
    }

    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 25px;
        padding: 0.5rem;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .search-wrapper:focus-within {
        border-color: #e74c3c;
        box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.2);
        background: rgba(255, 255, 255, 0.08);
    }

    .search-icon {
        font-size: 1.2rem;
        margin: 0 1rem;
        color: #b0b0b0;
    }

    .search-input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        padding: 0.875rem 0;
        font-size: 1.1rem;
        color: #ffffff;
        font-weight: 500;
    }

    .search-input::placeholder {
        color: #888;
    }

    .search-btn {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: none;
        padding: 0.875rem 1.5rem;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
    }

    .search-btn:hover {
        background: linear-gradient(135deg, #c0392b, #a93226);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
    }

    /* Filters Section - Centered */
    .filters-section {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .category-filters {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        justify-content: center;
        max-width: 1000px;
    }

    .filter-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: rgba(255, 255, 255, 0.05);
        color: #ffffff;
        text-decoration: none;
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 25px;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        font-weight: 600;
        white-space: nowrap;
        backdrop-filter: blur(10px);
    }

    .filter-btn:hover,
    .filter-btn.active {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border-color: #e74c3c;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
    }

    .filter-icon {
        font-size: 1.1rem;
    }

    .filter-text {
        font-weight: 600;
    }

    /* Enhanced Menu Grid */
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2rem;
        padding: 1rem 0;
    }

    /* ENHANCED MENU CARDS - Dark Theme */
    .menu-item.enhanced-menu-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 0;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        height: 520px;
        display: flex;
        flex-direction: column;
        backdrop-filter: blur(15px);
    }



    /* Top gradient border on hover */
    .menu-item.enhanced-menu-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #e74c3c, #f39c12, #27ae60, #3498db);
        opacity: 0;
        transition: opacity 0.4s ease;
        z-index: 2;
    }
    /* Enhanced Featured Badge */
    .enhanced-menu-card .featured-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, #f39c12, #e67e22);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 700;
        z-index: 3;
        box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);
        animation: glow 2s ease-in-out infinite alternate;
    }

    @keyframes glow {
        from { box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4); }
        to { box-shadow: 0 4px 20px rgba(243, 156, 18, 0.6); }
    }

    /* Enhanced Psychology Tags */
    .enhanced-menu-card .psychology-tags {
        position: absolute;
        top: 1rem;
        left: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        z-index: 3;
        max-width: 60%;
    }

    .enhanced-menu-card .psychology-tag {
        background: rgba(231, 76, 60, 0.95);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3);
        max-width: 140px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        backdrop-filter: blur(10px);
    }

    /* Enhanced Urgency Indicators */
    .enhanced-menu-card .urgency-indicator {
        position: absolute;
        top: 4rem;
        left: 1rem;
        background: linear-gradient(135deg, #ff4444, #e74c3c);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        z-index: 3;
        animation: pulse 2s infinite;
        box-shadow: 0 3px 12px rgba(255, 68, 68, 0.5);
        max-width: 160px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Enhanced Image Styling */
    .enhanced-menu-card .enhanced-image-wrapper {
        position: relative;
        overflow: hidden;
        height: 220px;
    }

    .enhanced-menu-card .menu-item-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }



    .enhanced-menu-card .enhanced-image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(231, 76, 60, 0.1));
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }



    /* Enhanced Content Styling - Dark Theme */
    .enhanced-menu-card .menu-item-content {
        padding: 1.75rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 2;
    }

    .enhanced-menu-card .menu-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        gap: 1rem;
    }

    .enhanced-menu-card .menu-item-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.5rem;
        font-family: 'Inter', sans-serif;
        line-height: 1.3;
        transition: color 0.3s ease;
    }



    .enhanced-menu-card .social-proof {
        font-size: 0.8rem;
        color: #b0b0b0;
        margin-top: 0.3rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-weight: 500;
    }

    .enhanced-menu-card .menu-item-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: #e74c3c;
        flex-shrink: 0;
        background: linear-gradient(135deg, #e74c3c, #f39c12);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .enhanced-menu-card .menu-item-description {
        color: #d0d0d0;
        line-height: 1.6;
        margin-bottom: auto;
        font-weight: 400;
        font-size: 0.95rem;
    }

    .enhanced-menu-card .sensory-word {
        color: #e74c3c;
        font-weight: 600;
        background: rgba(231, 76, 60, 0.1);
        padding: 0.1rem 0.3rem;
        border-radius: 4px;
    }

    /* Enhanced Meta Styling - Dark Theme */
    .enhanced-menu-card .menu-item-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 1.25rem 0;
        font-size: 0.85rem;
    }

    .enhanced-menu-card .item-category {
        color: #b0b0b0;
        background: rgba(255, 255, 255, 0.1);
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .enhanced-menu-card .unavailable-badge {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Enhanced Action Buttons - Dark Theme */
    .enhanced-menu-card .menu-item-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-top: auto;
    }

    .enhanced-menu-card .btn-add-to-cart {
        flex: 1;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: none;
        padding: 1rem 1.25rem;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
    }

    .enhanced-menu-card .btn-add-to-cart:hover {
        background: linear-gradient(135deg, #c0392b, #a93226);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
    }

    .enhanced-menu-card .btn-unavailable {
        flex: 1;
        background: #555;
        color: white;
        border: none;
        padding: 1rem 1.25rem;
        border-radius: 20px;
        cursor: not-allowed;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .enhanced-menu-card .btn-favorite {
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.3rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        padding: 0;
        backdrop-filter: blur(10px);
    }

    .enhanced-menu-card .btn-favorite:hover,
    .enhanced-menu-card .btn-favorite.favorited {
        border-color: #e74c3c;
        background: rgba(231, 76, 60, 0.2);
        transform: scale(1.1);
    }

    /* Enhanced Hover Effect Animation */
    .enhanced-menu-card .enhanced-card-hover-effect {
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(231, 76, 60, 0.1), transparent);
        transition: left 0.8s ease;
        pointer-events: none;
        z-index: 1;
    }



    /* Results Info Styling - Dark Theme */
    .results-info {
        text-align: center;
        margin: 3rem 0;
        font-size: 1rem;
        color: #b0b0b0;
        font-weight: 500;
        background: rgba(255, 255, 255, 0.05);
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
    }

    /* No Results Styling - Dark Theme */
    .no-results-content {
        text-align: center;
        padding: 4rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(15px);
    }

    .no-results-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        display: block;
        opacity: 0.6;
    }

    .no-results-content h3 {
        color: #ffffff;
        margin-bottom: 1rem;
        font-size: 1.5rem;
    }

    .no-results-content p {
        color: #b0b0b0;
        margin-bottom: 2rem;
    }

    .btn-secondary {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.3);
    }

    /* Enhanced Recommendations - Dark Theme */
    .menu-recommendations {
        margin-top: 4rem;
        padding: 2rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(15px);
    }

    .menu-recommendations h3 {
        font-size: 1.5rem;
        color: #ffffff;
        margin-bottom: 2rem;
        text-align: center;
    }

    .recommendation-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 2rem;
        margin-top: 1rem;
    }

    .recommendation-item.enhanced-recommendation {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }

    .recommendation-item.enhanced-recommendation:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #e74c3c;
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .recommendation-item img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 1rem;
    }

    .recommendation-item h4 {
        color: #ffffff;
        margin-bottom: 0.5rem;
    }

    .rec-price {
        font-size: 1.1rem;
        font-weight: 700;
        color: #e74c3c;
        margin: 0.5rem 0 1rem 0;
    }

    .rec-btn {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 20px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin: 0 auto;
    }

    .rec-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .category-filters {
            gap: 0.5rem;
        }

        .filter-btn {
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
        }
    }

    @media (max-width: 768px) {
        .menu-controls {
            gap: 1.5rem;
        }

        .search-wrapper {
            padding: 0.4rem;
        }

        .search-input {
            padding: 0.75rem 0;
            font-size: 1rem;
        }

        .search-btn {
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
        }

        .category-filters {
            gap: 0.4rem;
        }

        .filter-btn {
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
        }

        .filter-text {
            display: none;
        }

        .filter-icon {
            font-size: 1.2rem;
        }

        .menu-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .menu-item.enhanced-menu-card {
            height: auto;
            min-height: 480px;
        }

        .page-header h1 {
            font-size: 2.5rem;
        }

        .enhanced-menu-card .psychology-tags {
            max-width: 70%;
        }

        .enhanced-menu-card .psychology-tag {
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
            max-width: 120px;
        }

        .enhanced-menu-card .urgency-indicator {
            font-size: 0.7rem;
            max-width: 140px;
        }
    }

    @media (max-width: 480px) {
        .search-form {
            max-width: none;
        }

        .enhanced-menu-card .menu-item-content {
            padding: 1.5rem;
        }

        .enhanced-menu-card .enhanced-image-wrapper {
            height: 200px;
        }

        .enhanced-menu-card .menu-item-title {
            font-size: 1.2rem;
        }

        .enhanced-menu-card .menu-item-price {
            font-size: 1.2rem;
        }

        .enhanced-menu-card .menu-item-description {
            font-size: 0.9rem;
        }

        .recommendation-grid {
            grid-template-columns: 1fr;
        }
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    // Enhanced Menu Page JavaScript - Dark Theme
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize enhanced search functionality
        initializeEnhancedMenuSearch();

        // Setup enhanced category filters
        setupEnhancedCategoryFilters();

        // Track enhanced menu interactions
        trackEnhancedMenuInteractions();

        // Initialize enhanced card animations
        initializeEnhancedCardAnimations();
    });

    function initializeEnhancedMenuSearch() {
        const searchInput = document.getElementById('menuSearch');
        if (!searchInput) return;

        let searchTimeout;

        // Enhanced search input effects
        searchInput.addEventListener('focus', function() {
            const wrapper = this.closest('.search-wrapper');
            wrapper.style.transform = 'translateY(-3px)';
            wrapper.style.boxShadow = '0 12px 30px rgba(231, 76, 60, 0.2)';
        });

        searchInput.addEventListener('blur', function() {
            const wrapper = this.closest('.search-wrapper');
            wrapper.style.transform = 'translateY(0)';
            wrapper.style.boxShadow = 'none';
        });

        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterEnhancedMenuItems(query);

                if (query.length > 2) {
                    // Track search using existing function
                    if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                        window.FoodieDelight.PsychologyEngine.trackEvent('enhanced_menu_search_performed', {
                            query: query,
                            source: 'enhanced_menu_page'
                        });
                    }
                }
            }, 300);
        });
    }

    function filterEnhancedMenuItems(query) {
        const menuItems = document.querySelectorAll('.enhanced-menu-card');
        let visibleCount = 0;

        menuItems.forEach((item, index) => {
            const title = item.querySelector('.menu-item-title')?.textContent.toLowerCase() || '';
            const description = item.querySelector('.menu-item-description')?.textContent.toLowerCase() || '';

            if (query === '' || title.includes(query) || description.includes(query)) {
                item.style.display = 'block';
                item.style.animation = `fadeInUp 0.6s ease ${index * 0.1}s both`;
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide enhanced no results message
        toggleEnhancedNoResultsMessage(visibleCount === 0 && query !== '');
    }

    function toggleEnhancedNoResultsMessage(show) {
        let message = document.getElementById('noResultsMessage');

        if (show && !message) {
            message = document.createElement('div');
            message.id = 'noResultsMessage';
            message.className = 'no-items';
            message.innerHTML = `
            <div class="no-results-content">
                <span class="no-results-icon">🔍</span>
                <h3>No items found</h3>
                <p>Try adjusting your search or browse our categories</p>
                <a href="index.php?page=menu" class="btn-secondary">View All Items</a>
            </div>
        `;

            const menuGrid = document.querySelector('.menu-grid');
            if (menuGrid) {
                menuGrid.parentElement.appendChild(message);
            }
        } else if (!show && message) {
            message.remove();
        }
    }

    function setupEnhancedCategoryFilters() {
        const filters = document.querySelectorAll('.category-filter');

        filters.forEach(filter => {
            filter.addEventListener('click', function() {
                // Enhanced filter animation
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);

                // Track category filter usage
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('enhanced_menu_category_filter_used', {
                        category: this.dataset.category,
                        source: 'enhanced_menu_page'
                    });
                }
            });
        });
    }

    function trackEnhancedMenuInteractions() {
        // Enhanced item views using intersection observer
        const menuItems = document.querySelectorAll('.enhanced-menu-card');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const itemId = entry.target.dataset.itemId;
                    const itemName = entry.target.querySelector('.menu-item-title')?.textContent;

                    // Add viewed animation
                    entry.target.style.animation = 'fadeInUp 0.6s ease';

                    if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                        window.FoodieDelight.PsychologyEngine.trackEvent('enhanced_menu_item_viewed', {
                            itemId: itemId,
                            itemName: itemName,
                            source: 'enhanced_menu_page'
                        });
                    }
                }
            });
        }, { threshold: 0.3 });

        menuItems.forEach(item => observer.observe(item));

        // Enhanced hover interactions
        menuItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                const itemId = this.dataset.itemId;

                // Enhanced hover effects
                this.style.zIndex = '10';

                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('enhanced_menu_item_hover', {
                        itemId: itemId,
                        source: 'enhanced_menu_page'
                    });
                }
            });

            item.addEventListener('mouseleave', function() {
                this.style.zIndex = '1';
            });
        });
    }

    function initializeEnhancedCardAnimations() {
        // Staggered animation for cards on page load
        const cards = document.querySelectorAll('.enhanced-menu-card');

        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';

            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });

        // Enhanced favorite button functionality
        document.querySelectorAll('.btn-favorite').forEach(btn => {
            btn.addEventListener('click', function() {
                this.classList.toggle('favorited');
                this.innerHTML = this.classList.contains('favorited') ? '❤️' : '🤍';

                // Add animation
                this.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            });
        });

        // Enhanced add to cart button functionality
        document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
            btn.addEventListener('click', function() {
                // Success animation
                const originalText = this.innerHTML;
                this.innerHTML = '<span class="btn-icon">✓</span><span class="btn-text">Added!</span>';
                this.style.background = 'linear-gradient(135deg, #27ae60, #229954)';

                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.background = 'linear-gradient(135deg, #e74c3c, #c0392b)';
                }, 2000);
            });
        });
    }

    // Enhanced CSS animations
    const enhancedStyle = document.createElement('style');
    enhancedStyle.textContent = `
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInFromLeft {
        from { opacity: 0; transform: translateX(-30px); }
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes bounceIn {
        0% { opacity: 0; transform: scale(0.3); }
        50% { opacity: 1; transform: scale(1.05); }
        70% { transform: scale(0.9); }
        100% { opacity: 1; transform: scale(1); }
    }
`;
    document.head.appendChild(enhancedStyle);
</script>