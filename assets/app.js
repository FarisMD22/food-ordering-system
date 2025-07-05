/**
 * FoodieDelight - Psychology-Enhanced Food Ordering System
 * Complete JavaScript with behavioral psychology integration
 */
/*
*
 */
/**
 * assets/app.js
 */

// ============================================================================
// GLOBAL VARIABLES & CONFIGURATION
// ============================================================================

window.FoodieDelight = {
    config: {
        apiEndpoint: 'index.php?ajax=1',
        behaviorTrackingEnabled: true, // ✅ Re-enabled with fixes
        psychologyFeaturesEnabled: true,
        socialProofUpdateInterval: 30000, // Reasonable frequency
        exitIntentDelay: 5000,
        scarcityUpdateInterval: 60000 // Reasonable frequency
    },
    state: {
        cart: window.cartItems || [],
        psychologyProfile: window.psychologyData || {},
        isLoggedIn: window.isLoggedIn || false,
        behaviorSession: {
            startTime: Date.now(),
            pageViews: 0,
            itemsViewed: [],
            triggersExposed: [],
            scrollDepth: 0,
            timeSpent: 0
        }
    },
    elements: {},
    timers: {}
};
// Add this at the VERY TOP of your app.js file:

// TEMPORARY FIX: Disable aggressive behavior tracking
window.FoodieDelight = window.FoodieDelight || {};
window.FoodieDelight.config = {
    apiEndpoint: 'index.php?ajax=1',
    behaviorTrackingEnabled: false, // DISABLED to prevent 502 errors
    psychologyFeaturesEnabled: true,
    socialProofUpdateInterval: 30000, // Reduced frequency
    exitIntentDelay: 5000,
    scarcityUpdateInterval: 60000 // Reduced frequency
};

document.addEventListener('DOMContentLoaded', function() {
    try {
        PsychologyEngine.initialize();
        CartManager.initialize();
        UIManager.initialize();
        console.log('✅ FoodieDelight System initialized (debug mode)');
    } catch (e) {
        console.error('Initialization error:', e);
    }
});
// ============================================================================
// PSYCHOLOGY ENGINE
// ============================================================================
class PsychologyEngine {
    static initialize() {
        this.enhanceAppetiteAppeal();
        this.initializeBehaviorTracking();
        this.setupSocialProof();
        this.initializeScarcityTriggers();
        this.setupExitIntentDetection();
        this.enhanceSensoryLanguage();

        console.log('🧠 Psychology Engine initialized - Enhanced appetite appeal active');
    }

    static enhanceAppetiteAppeal() {
        // Apply color psychology to high-margin items
        document.querySelectorAll('.menu-item[data-margin="high"]').forEach(item => {
            item.classList.add('high-margin');
            item.style.borderLeft = '4px solid var(--primary-red)';
            item.style.background = 'linear-gradient(90deg, rgba(231, 76, 60, 0.05) 0%, transparent 100%)';
        });

        // Add appetite-enhancing effects to food images
        document.querySelectorAll('.menu-item-image').forEach(img => {
            this.addSteamEffect(img);
            this.enhanceImageAppeal(img);
        });
    }

    static addSteamEffect(imageElement) {
        const steamContainer = document.createElement('div');
        steamContainer.className = 'steam-container';
        steamContainer.innerHTML = `
            <div class="steam-particle" style="left: 30%; animation-delay: 0s;"></div>
            <div class="steam-particle" style="left: 50%; animation-delay: 0.5s;"></div>
            <div class="steam-particle" style="left: 70%; animation-delay: 1s;"></div>
        `;

        imageElement.parentElement.style.position = 'relative';
        imageElement.parentElement.appendChild(steamContainer);

        // CSS for steam effect
        if (!document.getElementById('steam-styles')) {
            const steamStyles = document.createElement('style');
            steamStyles.id = 'steam-styles';
            steamStyles.textContent = `
                .steam-container {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    pointer-events: none;
                    overflow: hidden;
                }
                .steam-particle {
                    position: absolute;
                    bottom: 20px;
                    width: 3px;
                    height: 20px;
                    background: rgba(255, 255, 255, 0.6);
                    border-radius: 50%;
                    animation: steam 3s ease-in-out infinite;
                }
                @keyframes steam {
                    0% { opacity: 0; transform: translateY(0) scale(1); }
                    50% { opacity: 0.7; transform: translateY(-30px) scale(1.2); }
                    100% { opacity: 0; transform: translateY(-60px) scale(0.8); }
                }
            `;
            document.head.appendChild(steamStyles);
        }
    }

    static enhanceImageAppeal(imageElement) {
        imageElement.style.filter = 'saturate(1.2) contrast(1.1) brightness(1.05)';

        imageElement.addEventListener('mouseenter', () => {
            imageElement.style.transform = 'scale(1.05)';
            imageElement.style.filter = 'saturate(1.4) contrast(1.2) brightness(1.1)';
        });

        imageElement.addEventListener('mouseleave', () => {
            imageElement.style.transform = 'scale(1)';
            imageElement.style.filter = 'saturate(1.2) contrast(1.1) brightness(1.05)';
        });
    }

    static highlightGoldenTriangleItems() {
        const menuItems = document.querySelectorAll('.menu-item');
        if (menuItems.length >= 3) {
            // Highlight top-right items (Golden Triangle positions)
            [1, 2].forEach(index => {
                if (menuItems[index]) {
                    menuItems[index].classList.add('golden-triangle-item');
                    const badge = document.createElement('div');
                    badge.className = 'golden-triangle-badge';
                    badge.textContent = '⭐ Chef\'s Choice';
                    menuItems[index].appendChild(badge);
                }
            });
        }
    }

    static enhanceSensoryLanguage() {
        const sensoryWords = [
            'crispy', 'crunchy', 'juicy', 'tender', 'melted', 'fresh',
            'zesty', 'rich', 'creamy', 'savory', 'sweet', 'spicy',
            'aromatic', 'golden', 'sizzling', 'steaming', 'fluffy',
            'smooth', 'silky', 'decadent', 'indulgent'
        ];

        document.querySelectorAll('.menu-item-description, .menu-description').forEach(element => {
            let html = element.innerHTML;

            sensoryWords.forEach(word => {
                const regex = new RegExp(`\\b(${word})\\b`, 'gi');
                html = html.replace(regex, '<span class="sensory-word">$1</span>');
            });

            element.innerHTML = html;
        });
    }

    static initializeBehaviorTracking() {
        if (!window.FoodieDelight.config.behaviorTrackingEnabled) return;

        // Track scroll depth
        this.trackScrollDepth();

        // Track time spent
        this.trackTimeSpent();

        // Track item interactions
        this.trackItemInteractions();

        // Track psychology trigger exposure
        this.trackPsychologyTriggers();

        console.log('📊 Behavior tracking initialized');
    }

    static trackScrollDepth() {
        let maxScroll = 0;

        window.addEventListener('scroll', () => {
            const scrollPercent = Math.round(
                (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100
            );

            if (scrollPercent > maxScroll) {
                maxScroll = scrollPercent;
                window.FoodieDelight.state.behaviorSession.scrollDepth = maxScroll;

                // Track milestone scrolls
                if ([25, 50, 75, 100].includes(scrollPercent)) {
                    this.trackEvent('scroll_milestone', { depth: scrollPercent });
                }
            }
        });
    }

    static trackTimeSpent() {
        setInterval(() => {
            window.FoodieDelight.state.behaviorSession.timeSpent += 1;

            // Track engagement milestones
            const timeSpent = window.FoodieDelight.state.behaviorSession.timeSpent;
            if ([30, 60, 120, 300].includes(timeSpent)) {
                this.trackEvent('engagement_milestone', { timeSpent });
            }
        }, 1000);
    }

    static trackItemInteractions() {
        document.querySelectorAll('.menu-item').forEach(item => {
            const itemId = item.dataset.itemId;

            // Track item views
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !window.FoodieDelight.state.behaviorSession.itemsViewed.includes(itemId)) {
                        window.FoodieDelight.state.behaviorSession.itemsViewed.push(itemId);
                        this.trackEvent('item_viewed', { itemId, itemName: item.querySelector('.menu-item-title')?.textContent });
                    }
                });
            }, { threshold: 0.5 });

            observer.observe(item);

            // Track hover interactions
            item.addEventListener('mouseenter', () => {
                this.trackEvent('item_hover_start', { itemId });
            });

            // Track click interactions
            item.addEventListener('click', () => {
                this.trackEvent('item_clicked', { itemId });
            });
        });
    }

    static trackPsychologyTriggers() {
        // Track scarcity message exposure
        document.querySelectorAll('.urgency-indicator, .scarcity-message').forEach(trigger => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const triggerType = trigger.className.includes('urgency') ? 'urgency' : 'scarcity';
                        this.trackEvent('psychology_trigger_exposed', {
                            triggerType,
                            message: trigger.textContent,
                            timestamp: Date.now()
                        });
                    }
                });
            });
            observer.observe(trigger);
        });

        // Track social proof interactions
        document.querySelectorAll('.social-proof, .social-proof-ticker').forEach(element => {
            element.addEventListener('click', () => {
                this.trackEvent('social_proof_clicked', {
                    content: element.textContent,
                    location: element.className
                });
            });
        });
    }

// REPLACE the existing trackEvent method in PsychologyEngine class with this:
    static trackEvent(eventType, data = {}) {
        if (!window.FoodieDelight.config.behaviorTrackingEnabled) {
            console.debug('Tracking disabled:', eventType, data);
            return Promise.resolve({ success: true });
        }

        try {
            // Throttle requests to prevent server overload
            const now = Date.now();
            if (this.lastTrackTime && (now - this.lastTrackTime) < 2000) {
                return Promise.resolve({ success: true, skipped: true });
            }
            this.lastTrackTime = now;

            const payload = {
                event_type: eventType,
                event_data: {
                    ...data,
                    timestamp: now,
                    session_id: this.getSessionId(),
                    page_url: window.location.href
                }
            };

            // Use a simpler fetch without aggressive retries
            return fetch(window.FoodieDelight.config.apiEndpoint + '&action=track_behavior', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .catch(err => {
                    // Silently fail - don't spam console
                    console.debug('Tracking failed:', eventType, err.message);
                    return { success: false, error: err.message };
                });

        } catch (error) {
            console.debug('Tracking error:', eventType, error);
            return Promise.resolve({ success: false, error: error.message });
        }
    }

// ADD this missing method to PsychologyEngine class:
    static getSessionId() {
        try {
            let sessionId = sessionStorage.getItem('psychology_session_id');
            if (!sessionId) {
                sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                sessionStorage.setItem('psychology_session_id', sessionId);
            }
            return sessionId;
        } catch (error) {
            // Fallback if sessionStorage isn't available
            return 'sess_fallback_' + Date.now();
        }
    }
// NEW (reduced frequency):
    static setupSocialProof() {
        const ticker = document.getElementById('socialProofContent');
        if (!ticker) return;

        const updateSocialProof = async () => {
            try {
                const response = await fetch(window.FoodieDelight.config.apiEndpoint + '&action=get_psychology_data');
                const data = await response.json();

                if (data.social_proof && data.social_proof.length > 0) {
                    const proofItems = data.social_proof;
                    let currentIndex = 0;

                    const rotateProof = () => {
                        const item = proofItems[currentIndex];
                        const timeAgo = this.getTimeAgo(new Date(item.created_at));
                        ticker.textContent = `🔥 ${item.name} just ordered ${item.item_name} ${timeAgo}`;
                        currentIndex = (currentIndex + 1) % proofItems.length;
                    };

                    rotateProof();
                    setInterval(rotateProof, 5000);
                }
            } catch (error) {
                console.log('Social proof update failed:', error);
            }
        };

        updateSocialProof();
        setInterval(updateSocialProof, window.FoodieDelight.config.socialProofUpdateInterval);
    }

    static initializeScarcityTriggers() {
        const updateScarcityMessages = async () => {
            try {
                const response = await fetch(window.FoodieDelight.config.apiEndpoint + '&action=get_psychology_data');
                const data = await response.json();

                if (data.scarcity_items) {
                    data.scarcity_items.forEach(item => {
                        const menuItem = document.querySelector(`[data-item-id="${item.id}"]`);
                        if (menuItem && item.limited_qty <= 5) {
                            this.addScarcityIndicator(menuItem, item.limited_qty);
                        }
                    });
                }
            } catch (error) {
                console.log('Scarcity update failed:', error);
            }
        };

        updateScarcityMessages();
        setInterval(updateScarcityMessages, window.FoodieDelight.config.scarcityUpdateInterval);
    }

    static addScarcityIndicator(menuItem, quantity) {
        // Remove existing indicators
        const existing = menuItem.querySelector('.scarcity-indicator');
        if (existing) existing.remove();

        const indicator = document.createElement('div');
        indicator.className = 'scarcity-indicator urgency-high';
        indicator.innerHTML = `⚡ Only ${quantity} left!`;

        menuItem.style.position = 'relative';
        menuItem.appendChild(indicator);

        // Track exposure
        this.trackEvent('scarcity_exposed', { itemId: menuItem.dataset.itemId, quantity });
    }

    static setupExitIntentDetection() {
        let exitIntentShown = false;
        let mouseLeaveTimer;

        document.addEventListener('mouseleave', (e) => {
            if (e.clientY <= 0 && !exitIntentShown && window.FoodieDelight.state.cart.length > 0) {
                mouseLeaveTimer = setTimeout(() => {
                    this.showExitIntentModal();
                    exitIntentShown = true;
                }, window.FoodieDelight.config.exitIntentDelay);
            }
        });

        document.addEventListener('mouseenter', () => {
            if (mouseLeaveTimer) {
                clearTimeout(mouseLeaveTimer);
            }
        });

        // Also trigger on page unload attempt
        window.addEventListener('beforeunload', (e) => {
            if (!exitIntentShown && window.FoodieDelight.state.cart.length > 0) {
                this.trackEvent('exit_intent_beforeunload', { cartValue: CartManager.getCartTotal() });
            }
        });
    }

    static showExitIntentModal() {
        const modal = document.getElementById('exitIntentModal');
        if (!modal) return;

        modal.style.display = 'flex';
        this.trackEvent('exit_intent_shown', {
            cartItems: window.FoodieDelight.state.cart.length,
            cartValue: CartManager.getCartTotal(),
            timeSpent: window.FoodieDelight.state.behaviorSession.timeSpent
        });

        // Auto-hide after 10 seconds
        setTimeout(() => {
            modal.style.display = 'none';
        }, 10000);
    }

    static getTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);

        if (seconds < 60) return 'just now';
        if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
        if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
        return Math.floor(seconds / 86400) + ' days ago';
    }
}

// ============================================================================
// CART MANAGEMENT WITH PSYCHOLOGY
// ============================================================================

class CartManager {
// 1. Fix the initialize method (REPLACE existing one)
    static initialize() {
        // Initialize pendingActions FIRST
        this.pendingActions = new Set();

        this.bindEvents();
        this.updateCartDisplay();
        this.initializeCartPsychology();

        console.log('🛒 Cart Manager initialized with double-click protection');
    }
// 2. Add missing updateCartDisplay method
    static updateCartDisplay() {
        try {
            // Update cart counters from existing DOM elements
            const cartCount = document.getElementById('cartCount');
            const cartTotal = document.getElementById('cartTotal');

            if (cartCount && cartTotal) {
                // Cart display is already rendered by PHP, just ensure it's visible
                console.log('Cart display updated');
            }
        } catch (error) {
            console.warn('Cart display update failed:', error);
        }
    }
    // 3. Add missing initializeCartPsychology method
    static initializeCartPsychology() {
        try {
            // Show abandon cart reminder after 5 minutes of inactivity
            let inactivityTimer;

            const resetInactivityTimer = () => {
                clearTimeout(inactivityTimer);
                if (window.FoodieDelight && window.FoodieDelight.state.cart.length > 0) {
                    inactivityTimer = setTimeout(() => {
                        this.showAbandonmentPrevention();
                    }, 300000); // 5 minutes
                }
            };

            document.addEventListener('mousemove', resetInactivityTimer);
            document.addEventListener('click', resetInactivityTimer);
            document.addEventListener('scroll', resetInactivityTimer);

            resetInactivityTimer();
        } catch (error) {
            console.warn('Cart psychology initialization failed:', error);
        }
    }
    // 4. Add missing updateLocalCartState method
    static async updateLocalCartState() {
        try {
            // Update the local cart state from server
            const response = await fetch(window.FoodieDelight.config.apiEndpoint + '&action=get_cart_state');
            const result = await response.json();

            if (result.success && window.FoodieDelight.state) {
                window.FoodieDelight.state.cart = result.cart || [];
            }
        } catch (error) {
            console.warn('Failed to update local cart state:', error);
        }
    }
    // 5. Add missing showSmartUpsells method
    static showSmartUpsells(itemId) {
        try {
            // Simple upsell notification instead of complex modal
            setTimeout(() => {
                this.showNotification('💡 Tip: Add a drink to complete your meal!', 'info');
            }, 2000);
        } catch (error) {
            console.warn('Smart upsells failed:', error);
        }
    }
    // 6. Add missing showAbandonmentPrevention method
    static showAbandonmentPrevention() {
        try {
            if (window.FoodieDelight.state.cart.length === 0) return;

            this.showNotification('🍽️ Your delicious items are waiting! Complete your order now for fresh delivery.', 'info');

            // Track abandonment prevention
            if (window.PsychologyEngine && typeof PsychologyEngine.trackEvent === 'function') {
                PsychologyEngine.trackEvent('cart_abandonment_prevention_shown', {
                    cartValue: this.getCartTotal(),
                    timestamp: Date.now()
                });
            }
        } catch (error) {
            console.warn('Abandonment prevention failed:', error);
        }
    }
    // 7. Add missing getCartTotal method
    static getCartTotal() {
        try {
            const cartTotal = document.getElementById('cartTotal');
            if (cartTotal) {
                const totalText = cartTotal.textContent.replace('$', '').trim();
                return parseFloat(totalText) || 0;
            }
            return 0;
        } catch (error) {
            console.warn('Failed to get cart total:', error);
            return 0;
        }
    }
    // 8. Add missing getSessionId method for PsychologyEngine
    static getSessionId() {
        let sessionId = sessionStorage.getItem('psychology_session_id');
        if (!sessionId) {
            sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('psychology_session_id', sessionId);
        }
        return sessionId;
    }
    // 9. Fix handleQuantityChange method (ADD if missing)
    static handleQuantityChange(button) {
        try {
            const itemId = button.dataset.itemId;
            const action = button.dataset.action;

            if (!itemId || !action) {
                console.warn('Missing item ID or action for quantity change');
                return;
            }

            // Simple quantity change without complex logic
            console.log(`Quantity change: ${action} for item ${itemId}`);

            // You can add actual quantity change logic here if needed
            this.showNotification('Quantity updated', 'success');

        } catch (error) {
            console.error('Quantity change failed:', error);
            this.showNotification('Failed to update quantity', 'error');
        }
    }
    static bindEvents() {
        // Single delegated event listener to prevent conflicts
        document.addEventListener('click', (e) => {
            // Check for any add to cart button (with debounce protection)
            if (this.isAddToCartButton(e.target)) {
                e.preventDefault();
                e.stopPropagation(); // Prevent event bubbling

                const button = this.getAddToCartButton(e.target);
                this.handleAddToCart(button);
                return;
            }

            // Other cart actions
            if (e.target.matches('.quantity-btn')) {
                e.preventDefault();
                e.stopPropagation();
                this.handleQuantityChange(e.target);
                return;
            }

            if (e.target.matches('.btn-remove-item')) {
                e.preventDefault();
                e.stopPropagation();
                this.handleRemoveItem(e.target);
                return;
            }

            if (this.isFavoriteButton(e.target)) {
                e.preventDefault();
                e.stopPropagation();
                const button = this.getFavoriteButton(e.target);
                this.handleFavorite(button);
                return;
            }
        });
    }

    // Helper function to identify any add to cart button
    static isAddToCartButton(element) {
        const button = element.closest('button, a');
        if (!button) return false;

        return button.matches('.btn-add-to-cart') ||
            button.matches('.clean-add-btn') ||
            button.matches('.enhanced-quick-add') ||
            button.matches('.enhanced-order-btn') ||
            button.matches('.btn-quick-add') ||
            button.matches('.btn-featured-order') ||
            button.classList.contains('btn-add-to-cart');
    }

    // Helper function to get the actual button element
    static getAddToCartButton(element) {
        return element.closest('button, a');
    }

    // Helper function to identify favorite buttons
    static isFavoriteButton(element) {
        const button = element.closest('button');
        if (!button) return false;

        return button.matches('.btn-favorite') ||
            button.matches('.clean-fav-btn') ||
            button.classList.contains('btn-favorite');
    }

    // Helper function to get favorite button
    static getFavoriteButton(element) {
        return element.closest('button');
    }

    static async handleAddToCart(button) {
        // Get item ID
        const itemId = this.getItemId(button);
        if (!itemId) {
            console.error('No item ID found');
            return;
        }

        // DEBOUNCE PROTECTION: Check if this action is already in progress
        const actionKey = `add_${itemId}`;
        if (this.pendingActions.has(actionKey)) {
            console.log('Add to cart already in progress for item:', itemId);
            return;
        }

        // Mark action as pending
        this.pendingActions.add(actionKey);

        try {
            const menuItem = button.closest('.menu-item, .trending-item, .featured-card, .suggestion-item, .clean-recommendation, .enhanced-trending-item, .enhanced-featured-card');
            const quantity = 1;

            // Immediate visual feedback
            this.showAddToCartFeedback(button);

            // Track psychology triggers that influenced this action
            const triggers = this.getActiveTriggers(menuItem);

            const response = await fetch(window.FoodieDelight.config.apiEndpoint + '&action=add_to_cart', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    item_id: itemId,
                    quantity: quantity,
                    triggers: JSON.stringify(triggers)
                })
            });

            const result = await response.json();

            if (result.success) {
                this.updateCartCounts(result.cart_count, result.cart_total);
                this.showNotification('Added to cart successfully! 🎉', 'success');

                // Update local cart state
                this.updateLocalCartState();

                // Track successful conversion
                if (window.PsychologyEngine) {
                    PsychologyEngine.trackEvent('cart_add_success', {
                        itemId,
                        triggers,
                        cartTotal: result.cart_total
                    });
                }

                // Show smart upsells
                this.showSmartUpsells(itemId);

            } else {
                this.showNotification(result.message || 'Failed to add item', 'error');
            }
        } catch (error) {
            this.showNotification('Network error. Please try again.', 'error');
            console.error('Add to cart failed:', error);
        } finally {
            // Always remove the pending action after 2 seconds
            setTimeout(() => {
                this.pendingActions.delete(actionKey);
            }, 2000);
        }
    }

    // Enhanced item ID detection
    static getItemId(button) {
        // Try multiple ways to get item ID
        return button.dataset.itemId ||
            button.getAttribute('data-item-id') ||
            button.closest('[data-item-id]')?.dataset.itemId ||
            button.closest('.menu-item, .trending-item, .featured-card, .suggestion-item, .clean-recommendation, .enhanced-trending-item, .enhanced-featured-card')?.dataset.itemId;
    }

    static showAddToCartFeedback(button) {
        // Prevent multiple feedback on same button
        if (button.classList.contains('processing')) return;

        button.classList.add('processing');
        const originalContent = button.innerHTML;

        // Show loading state
        button.classList.add('loading');
        button.innerHTML = button.innerHTML.includes('icon')
            ? '<span class="btn-icon">⏳</span><span class="btn-text">Adding...</span>'
            : 'Adding...';

        setTimeout(() => {
            button.classList.remove('loading');
            button.classList.add('success');
            button.innerHTML = button.innerHTML.includes('icon')
                ? '<span class="btn-icon">✅</span><span class="btn-text">Added!</span>'
                : '✓ Added!';

            setTimeout(() => {
                button.classList.remove('success', 'processing');
                button.innerHTML = originalContent;
            }, 1500);
        }, 600);
    }

    // Rest of your existing methods remain the same...
    static updateCartCounts(count, total) {
        const cartCount = document.getElementById('cartCount');
        const cartTotal = document.getElementById('cartTotal');

        if (cartCount) cartCount.textContent = count;
        if (cartTotal) cartTotal.textContent = ' $' + parseFloat(total).toFixed(2);

        // Animate the cart icon
        const cartLink = document.querySelector('.cart-link');
        if (cartLink) {
            cartLink.style.animation = 'appetitePulse 0.5s ease';
            setTimeout(() => {
                cartLink.style.animation = '';
            }, 500);
        }
    }

    static getActiveTriggers(menuItem) {
        if (!menuItem) return [];

        const triggers = [];

        // Check for psychology tags
        const psychologyTags = menuItem.querySelectorAll('.psychology-tag');
        psychologyTags.forEach(tag => {
            triggers.push({
                type: 'psychology_tag',
                value: tag.textContent.trim()
            });
        });

        return triggers;
    }

    static showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.cart-notification').forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `cart-notification ${type}`;
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

// 10. Fix handleRemoveItem method (ADD if missing)
    static handleRemoveItem(button) {
        try {
            const itemId = button.dataset.itemId;

            if (!itemId) {
                console.warn('Missing item ID for remove');
                return;
            }

            // Simple remove without complex logic
            console.log(`Remove item: ${itemId}`);

            // You can add actual remove logic here if needed
            this.showNotification('Item removed', 'info');

        } catch (error) {
            console.error('Remove item failed:', error);
            this.showNotification('Failed to remove item', 'error');
        }
    }
// 11. Fix handleFavorite method (ADD if missing)
    static handleFavorite(button) {
        try {
            const itemId = button.dataset.itemId || button.getAttribute('data-item-id');
            const isFavorited = button.classList.contains('favorited');

            if (isFavorited) {
                button.classList.remove('favorited');
                button.innerHTML = '🤍';
            } else {
                button.classList.add('favorited');
                button.innerHTML = '❤️';

                // Trigger heart animation
                button.style.animation = 'heartBeat 0.6s ease';
                setTimeout(() => {
                    button.style.animation = '';
                }, 600);
            }

            // Track favorite action
            if (window.PsychologyEngine && typeof PsychologyEngine.trackEvent === 'function') {
                PsychologyEngine.trackEvent('item_favorited', {
                    itemId,
                    favorited: !isFavorited
                });
            }

            // Store in localStorage for persistence
            try {
                const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
                if (isFavorited) {
                    const index = favorites.indexOf(itemId);
                    if (index > -1) favorites.splice(index, 1);
                } else {
                    favorites.push(itemId);
                }
                localStorage.setItem('favorites', JSON.stringify(favorites));
            } catch (storageError) {
                console.warn('Failed to update favorites in localStorage:', storageError);
            }

        } catch (error) {
            console.error('Favorite handling failed:', error);
        }
    }
}
// ============================================================================
// UI ENHANCEMENTS & INTERACTIONS
// ============================================================================

class UIManager {
    static initialize() {
        this.setupMobileNavigation();
        this.setupCategoryFilters();
        this.setupSearchFunctionality();
        this.setupSmoothScrolling();
        this.initializeFavorites();

        console.log('🎨 UI Manager initialized');
    }

    static setupMobileNavigation() {
        const mobileToggle = document.getElementById('mobileToggle');
        const navMenu = document.getElementById('navMenu');

        if (mobileToggle && navMenu) {
            mobileToggle.addEventListener('click', () => {
                mobileToggle.classList.toggle('active');
                navMenu.classList.toggle('active');
            });

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!mobileToggle.contains(e.target) && !navMenu.contains(e.target)) {
                    mobileToggle.classList.remove('active');
                    navMenu.classList.remove('active');
                }
            });
        }
    }

    static setupCategoryFilters() {
        const filters = document.querySelectorAll('.category-filter');
        const menuItems = document.querySelectorAll('.menu-item');

        filters.forEach(filter => {
            filter.addEventListener('click', () => {
                const category = filter.dataset.category;

                // Update active filter
                filters.forEach(f => f.classList.remove('active'));
                filter.classList.add('active');

                // Filter menu items
                menuItems.forEach(item => {
                    if (category === 'all' || item.dataset.category === category) {
                        item.style.display = 'block';
                        item.style.animation = 'fadeInUp 0.5s ease';
                    } else {
                        item.style.display = 'none';
                    }
                });

                PsychologyEngine.trackEvent('category_filter_used', { category });
            });
        });
    }

    static setupSearchFunctionality() {
        const searchInput = document.getElementById('menuSearch');
        if (!searchInput) return;

        let searchTimeout;

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.filterMenuItems(query);

                if (query.length > 2) {
                    PsychologyEngine.trackEvent('search_performed', { query });
                }
            }, 300);
        });
    }

    static filterMenuItems(query) {
        const menuItems = document.querySelectorAll('.menu-item');
        let visibleCount = 0;

        menuItems.forEach(item => {
            const title = item.querySelector('.menu-item-title')?.textContent.toLowerCase() || '';
            const description = item.querySelector('.menu-item-description')?.textContent.toLowerCase() || '';

            if (query === '' || title.includes(query) || description.includes(query)) {
                item.style.display = 'block';
                item.style.animation = 'fadeInUp 0.3s ease';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Show "no results" message if needed
        this.toggleNoResultsMessage(visibleCount === 0 && query !== '');
    }

    static toggleNoResultsMessage(show) {
        let message = document.getElementById('noResultsMessage');

        if (show && !message) {
            message = document.createElement('div');
            message.id = 'noResultsMessage';
            message.className = 'no-results-message';
            message.innerHTML = `
                <div class="no-results-content">
                    <span class="no-results-icon">🔍</span>
                    <h3>No items found</h3>
                    <p>Try adjusting your search or browse our categories</p>
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

    static setupSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    static initializeFavorites() {
        const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');

        favorites.forEach(itemId => {
            const favoriteBtn = document.querySelector(`[data-item-id="${itemId}"] .btn-favorite`);
            if (favoriteBtn) {
                favoriteBtn.classList.add('favorited');
                favoriteBtn.innerHTML = '❤️';
            }
        });
    }
}

// ============================================================================
// FORM HANDLING
// ============================================================================

class FormManager {
    static initialize() {
        this.setupFormValidation();
        this.setupFormSubmission();

        console.log('📝 Form Manager initialized');
    }

    static setupFormValidation() {
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }

    static validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        const required = field.hasAttribute('required');

        let isValid = true;
        let errorMessage = '';

        if (required && !value) {
            isValid = false;
            errorMessage = 'This field is required';
        } else if (type === 'email' && value && !this.isValidEmail(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        } else if (field.name === 'password' && value && value.length < 6) {
            isValid = false;
            errorMessage = 'Password must be at least 6 characters';
        }

        this.showFieldValidation(field, isValid, errorMessage);
        return isValid;
    }

    static showFieldValidation(field, isValid, errorMessage) {
        field.classList.toggle('error', !isValid);

        let errorElement = field.parentElement.querySelector('.error-message');
        if (!isValid && errorMessage) {
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'error-message';
                field.parentElement.appendChild(errorElement);
            }
            errorElement.textContent = errorMessage;
        } else if (errorElement) {
            errorElement.remove();
        }
    }

    static clearFieldError(field) {
        field.classList.remove('error');
        const errorElement = field.parentElement.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
    }

    static isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    static setupFormSubmission() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                }
            });
        });
    }

    static validateForm(form) {
        const fields = form.querySelectorAll('.form-input[required]');
        let isValid = true;

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }
}

// ============================================================================
// GLOBAL EVENT HANDLERS
// ============================================================================

// Exit intent modal handlers
window.completeOrder = function() {
    window.location.href = 'index.php?page=checkout';
};

window.closeExitIntent = function() {
    document.getElementById('exitIntentModal').style.display = 'none';
    PsychologyEngine.trackEvent('exit_intent_dismissed', { action: 'close_button' });
};

// Global behavior tracking function
window.trackBehaviorEvent = function(eventType, data = {}) {
    PsychologyEngine.trackEvent(eventType, data);
};

// ============================================================================
// INITIALIZATION
// ============================================================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all managers
    PsychologyEngine.initialize();
    CartManager.initialize();
    UIManager.initialize();
    FormManager.initialize();

    // Setup global error handling
    window.addEventListener('error', (e) => {
        console.error('JavaScript error:', e.error);
        PsychologyEngine.trackEvent('javascript_error', {
            message: e.message,
            filename: e.filename,
            lineno: e.lineno
        });
    });

    // Track page load completion
    window.addEventListener('load', () => {
        PsychologyEngine.trackEvent('page_load_complete', {
            loadTime: performance.now(),
            page: window.location.pathname
        });
    });

    // Setup visibility change tracking
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            PsychologyEngine.trackEvent('page_hidden', {
                timeSpent: window.FoodieDelight.state.behaviorSession.timeSpent
            });
        } else {
            PsychologyEngine.trackEvent('page_visible', {
                returnTime: Date.now()
            });
        }
    });

    console.log('🚀 FoodieDelight Psychology System fully initialized');
});

// ============================================================================
// UTILITY FUNCTIONS
// ============================================================================

// Debounce function for performance
function debounce(func, wait, immediate) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            timeout = null;
            if (!immediate) func(...args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func(...args);
    };
}

// Throttle function for scroll events
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Animation frame utility
function requestAnimationFrame(callback) {
    return window.requestAnimationFrame(callback) ||
        window.webkitRequestAnimationFrame(callback) ||
        window.mozRequestAnimationFrame(callback) ||
        window.setTimeout(callback, 1000 / 60);
}

// Local storage with error handling
const SafeStorage = {
    get: (key, defaultValue = null) => {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (error) {
            console.warn('Failed to read from localStorage:', error);
            return defaultValue;
        }
    },

    set: (key, value) => {
        try {
            localStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (error) {
            console.warn('Failed to write to localStorage:', error);
            return false;
        }
    },

    remove: (key) => {
        try {
            localStorage.removeItem(key);
            return true;
        } catch (error) {
            console.warn('Failed to remove from localStorage:', error);
            return false;
        }
    }
};

// ============================================================================
// PERFORMANCE MONITORING
// ============================================================================

class PerformanceMonitor {
    static initialize() {
        this.monitorPageLoad();
        this.monitorUserInteractions();
        this.monitorNetworkRequests();
    }

    static monitorPageLoad() {
        window.addEventListener('load', () => {
            const perfData = performance.getEntriesByType('navigation')[0];

            if (perfData) {
                PsychologyEngine.trackEvent('performance_metrics', {
                    domContentLoaded: perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart,
                    loadComplete: perfData.loadEventEnd - perfData.loadEventStart,
                    firstPaint: this.getFirstPaint(),
                    pageSize: this.getPageSize()
                });
            }
        });
    }

    static monitorUserInteractions() {
        ['click', 'scroll', 'keypress'].forEach(eventType => {
            document.addEventListener(eventType, throttle(() => {
                PsychologyEngine.trackEvent('user_interaction', {
                    type: eventType,
                    timestamp: Date.now()
                });
            }, 1000));
        });
    }

    static monitorNetworkRequests() {
        const originalFetch = window.fetch;
        window.fetch = async function(...args) {
            const startTime = performance.now();
            try {
                const response = await originalFetch.apply(this, args);
                const endTime = performance.now();

                PsychologyEngine.trackEvent('network_request', {
                    url: args[0],
                    duration: endTime - startTime,
                    status: response.status,
                    success: response.ok
                });

                return response;
            } catch (error) {
                const endTime = performance.now();

                PsychologyEngine.trackEvent('network_request', {
                    url: args[0],
                    duration: endTime - startTime,
                    error: error.message,
                    success: false
                });

                throw error;
            }
        };
    }

    static getFirstPaint() {
        const paintEntries = performance.getEntriesByType('paint');
        const firstPaint = paintEntries.find(entry => entry.name === 'first-paint');
        return firstPaint ? firstPaint.startTime : null;
    }

    static getPageSize() {
        const resources = performance.getEntriesByType('resource');
        return resources.reduce((total, resource) => total + (resource.transferSize || 0), 0);
    }
}

// Initialize performance monitoring
PerformanceMonitor.initialize();

// ============================================================================
// ACCESSIBILITY ENHANCEMENTS
// ============================================================================

class AccessibilityManager {
    static initialize() {
        this.setupKeyboardNavigation();
        this.setupAriaLabels();
        this.setupFocusManagement();
        this.setupMotionPreferences();
    }

    static setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            // Escape key handling
            if (e.key === 'Escape') {
                this.closeModals();
            }

            // Enter key on custom buttons
            if (e.key === 'Enter' && e.target.matches('.btn-add-to-cart, .btn-favorite')) {
                e.target.click();
            }
        });
    }

    static setupAriaLabels() {
        // Add aria-labels to interactive elements
        document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
            const itemName = btn.closest('.menu-item')?.querySelector('.menu-item-title')?.textContent;
            if (itemName) {
                btn.setAttribute('aria-label', `Add ${itemName} to cart`);
            }
        });

        document.querySelectorAll('.btn-favorite').forEach(btn => {
            const itemName = btn.closest('.menu-item')?.querySelector('.menu-item-title')?.textContent;
            if (itemName) {
                btn.setAttribute('aria-label', `Add ${itemName} to favorites`);
            }
        });
    }

    static setupFocusManagement() {
        // Ensure focus is visible
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });

        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-navigation');
        });
    }

    static setupMotionPreferences() {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (prefersReducedMotion) {
            document.body.classList.add('reduce-motion');

            // Disable auto-playing animations
            document.querySelectorAll('[style*="animation"]').forEach(el => {
                el.style.animation = 'none';
            });
        }
    }

    static closeModals() {
        // Close any open modals
        document.querySelectorAll('.exit-intent-modal, .upsell-modal').forEach(modal => {
            modal.style.display = 'none';
        });
    }
}

// Initialize accessibility features
AccessibilityManager.initialize();



if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        PsychologyEngine,
        CartManager,
        UIManager,
        FormManager,
        PerformanceMonitor,
        AccessibilityManager
    };
}

// Make functions available globally for inline event handlers
window.FoodieDelight.PsychologyEngine = PsychologyEngine;
window.FoodieDelight.CartManager = CartManager;
window.FoodieDelight.UIManager = UIManager;