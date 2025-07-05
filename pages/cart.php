<?php
// pages/cart.php - Complete Cart Page using existing functions
$cartItems = $_SESSION['cart'] ?? [];
$cartTotal = getCartTotal();
$cartCount = getCartItemCount();

// Track cart page view
if (isset($_SESSION['user_id'])) {
    trackBehaviorLog($_SESSION['user_id'], 'cart_page_view', [
        'cart_count' => $cartCount,
        'cart_total' => $cartTotal,
        'items' => array_keys($cartItems)
    ]);
}

// Get smart upsells for current cart
$upsellSuggestions = getSmartUpsells($cartItems);
?>

<section class="cart-page">
    <div class="container">
        <div class="page-header">
            <h1>Your Cart</h1>
            <p><?php echo $cartCount; ?> items • $<?php echo number_format($cartTotal, 2); ?> total</p>
        </div>

        <?php if (empty($cartItems)): ?>
            <!-- Empty Cart -->
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2 style="color: #e74b3c">Your cart is empty</h2>
                <p style="color: #615a59">Looks like you haven't added anything to your cart yet.</p>
                <a href="index.php?page=menu" class="btn-primary">
                    Browse Menu
                </a>

                <!-- Smart Suggestions for Empty Cart -->
                <?php
                $popularItems = getFeaturedMenuItems(3);
                if (!empty($popularItems)):
                    ?>
                    <div class="empty-cart-suggestions">
                        <h3 style="color: #e74b3c">Popular Choices</h3>
                        <div class="suggestion-grid">
                            <?php foreach ($popularItems as $item): ?>
                                <div  class="suggestion-item">
                                    <img src="<?php echo $item['image_url'] ?: 'assets/images/default-food.jpg'; ?>"
                                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <h4 style="color: #1A1A1A"><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p style="color: #1A1A1A">$<?php echo number_format($item['price'], 2); ?></p>

                                    <!-- Social Proof -->
                                    <?php
                                    $socialProof = generateSocialProof($item['id']);
                                    if ($socialProof):
                                        ?>
                                        <div class="suggestion-social-proof">
                                            <?php echo $socialProof['text']; ?>
                                        </div>
                                    <?php endif; ?>

                                    <button class="btn-add-to-cart" data-item-id="<?php echo $item['id']; ?>">
                                        Add to Cart
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Cart Items -->
            <div class="cart-content">
                <div class="cart-items">
                    <?php foreach ($cartItems as $itemId => $item): ?>
                        <div class="cart-item" data-item-id="<?php echo $itemId; ?>">
                            <img src="<?php echo $item['image_url'] ?: 'assets/images/default-food.jpg'; ?>"
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 class="cart-item-image">

                            <div style="color: #1A1A1A" class="cart-item-details">
                                <h3 class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="cart-item-price">$<?php echo number_format($item['price'], 2); ?> each</p>

                                <!-- Psychology tags for cart items -->
                                <div class="cart-item-tags">
                                    <?php if ($item['price'] > 20): ?>
                                        <span class="cart-tag premium">💎 Premium</span>
                                    <?php endif; ?>
                                    <?php if (rand(1, 100) > 70): ?>
                                        <span class="cart-tag popular">🔥 Popular</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div style="color: #1A1A1A" class="cart-item-quantity">
                                <button class="quantity-btn" data-item-id="<?php echo $itemId; ?>" data-action="decrease">-</button>
                                <span class="quantity-display"><?php echo $item['quantity']; ?></span>
                                <button class="quantity-btn" data-item-id="<?php echo $itemId; ?>" data-action="increase">+</button>
                            </div>

                            <div class="cart-item-total">
                                <span class="item-total">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                <button class="btn-remove-item" data-item-id="<?php echo $itemId; ?>">
                                    🗑️ Remove
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Smart Upsells -->
                <?php if (!empty($upsellSuggestions)): ?>
                    <div style="color: #e74b3c" class="cart-upsells">
                        <h3>Complete Your Meal</h3>
                        <div class="upsell-suggestions">
                            <?php foreach ($upsellSuggestions as $suggestion): ?>
                                <div class="upsell-suggestion">
                                    <span class="upsell-icon"><?php echo $suggestion['category'] === 'drinks' ? '🥤' : '🍰'; ?></span>
                                    <span class="upsell-text"><?php echo $suggestion['reason']; ?></span>
                                    <a href="index.php?page=menu&category=<?php echo $suggestion['category']; ?>" class="btn-small">
                                        Browse <?php echo ucfirst($suggestion['category']); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Cart Summary -->
                <div style="color: #e74b3c" class="cart-summary">
                    <div class="summary-card">
                        <h3>Order Summary</h3>

                        <div class="summary-row">
                            <span>Subtotal (<?php echo $cartCount; ?> items)</span>
                            <span>$<?php echo number_format($cartTotal, 2); ?></span>
                        </div>

                        <div class="summary-row">
                            <span>Delivery Fee</span>
                            <span class="<?php echo $cartTotal >= 25 ? 'free-delivery' : ''; ?>">
                                <?php echo $cartTotal >= 25 ? 'FREE' : '$3.99'; ?>
                            </span>
                        </div>

                        <?php if ($cartTotal < 25): ?>
                            <div class="delivery-notice">
                                <small>🚚 Add $<?php echo number_format(25 - $cartTotal, 2); ?> more for free delivery!</small>
                            </div>
                        <?php endif; ?>

                        <!-- Psychology: Show savings -->
                        <?php if ($cartTotal >= 25): ?>
                            <div class="savings-notice">
                                <small>💰 You saved $3.99 on delivery!</small>
                            </div>
                        <?php endif; ?>

                        <div class="summary-row total-row">
                            <span><strong>Total</strong></span>
                            <span><strong>$<?php echo number_format($cartTotal + ($cartTotal >= 25 ? 0 : 3.99), 2); ?></strong></span>
                        </div>

                        <!-- Psychology: Order time estimate -->
                        <div class="delivery-estimate">
                            <div class="estimate-item">
                                <span class="estimate-icon">⏱️</span>
                                <span class="estimate-text">Ready in 15-30 minutes</span>
                            </div>
                            <div class="estimate-item">
                                <span class="estimate-icon">🚚</span>
                                <span class="estimate-text">Fresh & hot delivery</span>
                            </div>
                        </div>

                        <div class="checkout-actions">
                            <?php if (isLoggedIn()): ?>
                                <form method="POST" action="index.php" class="checkout-form">
                                    <input type="hidden" name="action" value="checkout">

                                    <div class="form-group">
                                        <label for="delivery_address">Delivery Address</label>
                                        <input type="text"
                                               id="delivery_address"
                                               name="delivery_address"
                                               placeholder="Enter your delivery address"
                                               required
                                               class="checkout-input">
                                    </div>

                                    <div class="form-group">
                                        <label for="payment_method">Payment Method</label>
                                        <select name="payment_method" id="payment_method" class="checkout-select">
                                            <option value="cash">💵 Cash on Delivery</option>
                                            <option value="card">💳 Credit Card</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="special_instructions">Special Instructions (Optional)</label>
                                        <textarea name="special_instructions"
                                                  id="special_instructions"
                                                  placeholder="Any special requests for your order..."
                                                  class="checkout-textarea"></textarea>
                                    </div>

                                    <button type="submit" class="btn-primary btn-checkout">
                                        <span class="checkout-icon">🛒</span>
                                        Place Order ($<?php echo number_format($cartTotal + ($cartTotal >= 25 ? 0 : 3.99), 2); ?>)
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="login-notice">
                                    <div class="login-notice-icon">🔒</div>
                                    <div class="login-notice-content">
                                        <h4>Please log in to checkout</h4>
                                        <p>Create an account or sign in to complete your order</p>
                                    </div>
                                </div>
                                <a href="index.php?page=login" class="btn-primary">
                                    🔑 Login to Checkout
                                </a>
                                <a href="index.php?page=register" class="btn-secondary">
                                    📝 Create Account
                                </a>
                            <?php endif; ?>

                            <a href="index.php?page=menu" class="btn-continue-shopping">
                                ← Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    // Cart page specific JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize cart functionality
        initializeCartPage();

        // Setup quantity controls
        setupQuantityControls();

        // Track cart interactions
        trackCartInteractions();

        // Initialize checkout form validation
        initializeCheckoutValidation();
    });

    function initializeCartPage() {
        // Track cart page load
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('cart_page_loaded', {
                cart_count: <?php echo $cartCount; ?>,
                cart_total: <?php echo $cartTotal; ?>,
                timestamp: Date.now()
            });
        }

        // Setup cart abandonment prevention
        setupAbandonmentPrevention();
    }

    function setupQuantityControls() {
        document.addEventListener('click', function(e) {
            if (e.target.matches('.quantity-btn')) {
                e.preventDefault();
                const itemId = e.target.dataset.itemId;
                const action = e.target.dataset.action;

                updateCartQuantity(itemId, action);
            }

            if (e.target.matches('.btn-remove-item')) {
                e.preventDefault();
                const itemId = e.target.dataset.itemId;
                const itemName = e.target.closest('.cart-item').querySelector('.cart-item-name').textContent;

                if (confirm(`Remove ${itemName} from cart?`)) {
                    removeCartItem(itemId);
                }
            }
        });
    }

    function updateCartQuantity(itemId, action) {
        // Show loading state
        const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
        cartItem.style.opacity = '0.7';

        fetch('index.php?ajax=1&action=update_cart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `item_id=${itemId}&action=${action}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart display
                    updateCartCounters(data.cart_count, data.cart_total);

                    // Update quantity display
                    const quantityDisplay = cartItem.querySelector('.quantity-display');
                    if (quantityDisplay) {
                        const currentQty = parseInt(quantityDisplay.textContent);
                        if (action === 'increase') {
                            quantityDisplay.textContent = currentQty + 1;
                        } else if (action === 'decrease') {
                            if (currentQty > 1) {
                                quantityDisplay.textContent = currentQty - 1;
                            } else {
                                cartItem.remove();
                                checkEmptyCart();
                            }
                        }

                        // Update item total
                        const priceText = cartItem.querySelector('.cart-item-price').textContent;
                        const price = parseFloat(priceText.replace(/,/g, '').replace(' each', ''));
                        const newQty = parseInt(quantityDisplay.textContent);
                        const itemTotal = cartItem.querySelector('.item-total');
                        if (itemTotal) {
                            itemTotal.textContent = `${(price * newQty).toFixed(2)}`;
                        }
                    }

                    // Track quantity change
                    if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                        window.FoodieDelight.PsychologyEngine.trackEvent('cart_quantity_changed', {
                            itemId: itemId,
                            action: action,
                            new_total: data.cart_total
                        });
                    }
                } else {
                    showNotification('Failed to update quantity', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Something went wrong', 'error');
            })
            .finally(() => {
                cartItem.style.opacity = '1';
            });
    }

    function removeCartItem(itemId) {
        const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
        const itemName = cartItem.querySelector('.cart-item-name').textContent;

        // Animate removal
        cartItem.style.animation = 'slideOut 0.3s ease';

        fetch('index.php?ajax=1&action=update_cart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `item_id=${itemId}&action=remove`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => {
                        cartItem.remove();
                        updateCartCounters(data.cart_count, data.cart_total);
                        checkEmptyCart();
                        showNotification('Item removed from cart', 'info');
                    }, 300);

                    // Track removal
                    if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                        window.FoodieDelight.PsychologyEngine.trackEvent('cart_item_removed', {
                            itemId: itemId,
                            itemName: itemName,
                            remaining_items: data.cart_count
                        });
                    }
                } else {
                    showNotification('Failed to remove item', 'error');
                    cartItem.style.animation = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Something went wrong', 'error');
                cartItem.style.animation = '';
            });
    }

    function updateCartCounters(count, total) {
        // Update header cart counter
        const cartCount = document.getElementById('cartCount');
        const cartTotal = document.getElementById('cartTotal');

        if (cartCount) cartCount.textContent = count;
        if (cartTotal) cartTotal.textContent = total.toFixed(2);

        // Update page header
        const pageHeader = document.querySelector('.page-header p');
        if (pageHeader) {
            pageHeader.textContent = `${count} items • ${total.toFixed(2)} total`;
        }

        // Update summary
        updateOrderSummary(count, total);
    }

    function updateOrderSummary(count, total) {
        const subtotalRow = document.querySelector('.summary-row:first-child span:last-child');
        const totalRow = document.querySelector('.total-row span:last-child');
        const deliveryFee = total >= 25 ? 0 : 3.99;

        if (subtotalRow) subtotalRow.textContent = `${total.toFixed(2)}`;
        if (totalRow) totalRow.textContent = `${(total + deliveryFee).toFixed(2)}`;

        // Update delivery notice
        const deliveryNotice = document.querySelector('.delivery-notice');
        const savingsNotice = document.querySelector('.savings-notice');

        if (total < 25) {
            if (deliveryNotice) {
                deliveryNotice.innerHTML = `<small>🚚 Add ${(25 - total).toFixed(2)} more for free delivery!</small>`;
                deliveryNotice.style.display = 'block';
            }
            if (savingsNotice) savingsNotice.style.display = 'none';
        } else {
            if (deliveryNotice) deliveryNotice.style.display = 'none';
            if (savingsNotice) {
                savingsNotice.innerHTML = `<small>💰 You saved $3.99 on delivery!</small>`;
                savingsNotice.style.display = 'block';
            }
        }

        // Update checkout button
        const checkoutBtn = document.querySelector('.btn-checkout');
        if (checkoutBtn) {
            checkoutBtn.innerHTML = `
            <span class="checkout-icon">🛒</span>
            Place Order (${(total + deliveryFee).toFixed(2)})
        `;
        }
    }

    function checkEmptyCart() {
        const cartItems = document.querySelectorAll('.cart-item');
        if (cartItems.length === 0) {
            // Redirect to empty cart state or reload page
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
    }

    function setupAbandonmentPrevention() {
        let abandonmentTimer;

        // Track mouse leave events
        document.addEventListener('mouseleave', function(e) {
            if (e.clientY <= 0) {
                abandonmentTimer = setTimeout(() => {
                    if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                        window.FoodieDelight.PsychologyEngine.trackEvent('cart_abandonment_risk', {
                            cart_total: <?php echo $cartTotal; ?>,
                            cart_count: <?php echo $cartCount; ?>,
                            time_on_page: Date.now() - performance.timing.navigationStart
                        });
                    }

                    // Show abandonment prevention message
                    showAbandonmentMessage();
                }, 2000);
            }
        });

        document.addEventListener('mouseenter', function() {
            clearTimeout(abandonmentTimer);
        });
    }

    function showAbandonmentMessage() {
        if (document.querySelector('.abandonment-message')) return;

        const message = document.createElement('div');
        message.className = 'abandonment-message';
        message.innerHTML = `
        <div class="abandonment-content">
            <span class="abandonment-icon">🍽️</span>
            <span class="abandonment-text">Don't forget your delicious items!</span>
            <button class="abandonment-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;

        document.body.appendChild(message);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (message.parentElement) message.remove();
        }, 5000);
    }

    function trackCartInteractions() {
        // Track time spent on cart page
        const startTime = Date.now();

        window.addEventListener('beforeunload', function() {
            const timeSpent = Date.now() - startTime;
            if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                window.FoodieDelight.PsychologyEngine.trackEvent('cart_page_time_spent', {
                    time_spent: timeSpent,
                    cart_total: <?php echo $cartTotal; ?>
                });
            }
        });

        // Track upsell interactions
        document.querySelectorAll('.upsell-suggestion a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('cart_upsell_clicked', {
                        category: this.href.split('category=')[1],
                        source: 'cart_page'
                    });
                }
            });
        });
    }

    function initializeCheckoutValidation() {
        const checkoutForm = document.querySelector('.checkout-form');
        if (!checkoutForm) return;

        checkoutForm.addEventListener('submit', function(e) {
            const addressInput = this.querySelector('#delivery_address');
            const address = addressInput.value.trim();

            if (address.length < 10) {
                e.preventDefault();
                showNotification('Please enter a complete delivery address', 'error');
                addressInput.focus();
                return;
            }

            // Track checkout attempt
            if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                window.FoodieDelight.PsychologyEngine.trackEvent('checkout_attempted', {
                    cart_total: <?php echo $cartTotal; ?>,
                    cart_count: <?php echo $cartCount; ?>,
                    payment_method: this.querySelector('#payment_method').value
                });
            }

            // Show loading state
            const submitBtn = this.querySelector('.btn-checkout');
            submitBtn.innerHTML = '<span class="loading-spinner">⏳</span> Processing Order...';
            submitBtn.disabled = true;
        });
    }

    function showNotification(message, type = 'info') {
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
    // Monitor Popular Choices buttons for cart additions
    document.addEventListener('DOMContentLoaded', function() {
        // Only on empty cart pages
        const popularChoices = document.querySelectorAll('.empty-cart-suggestions .btn-add-to-cart');

        popularChoices.forEach(button => {
            button.addEventListener('click', function() {
                // Use the same refresh mechanism as checkEmptyCart()
                setTimeout(() => {
                    location.reload();
                }, 2000); // Wait 2 seconds for the add to cart to complete, then refresh
            });
        });
    });
    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
    @keyframes slideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(-100%); }
    }

    .abandonment-message {
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--primary-red);
        color: white;
        padding: 1rem;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        z-index: 1000;
        animation: slideIn 0.3s ease;
    }

    .abandonment-content {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .abandonment-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        margin-left: 0.5rem;
    }

    .cart-notification {
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

    .cart-notification.show {
        transform: translateX(0);
    }

    .cart-notification.info {
        background: #17a2b8;
    }

    .cart-notification.error {
        background: #dc3545;
    }

    @keyframes slideIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }
`;
    document.head.appendChild(style);
</script>

<style>
    /* Cart page specific styles */
    .cart-page {
        padding: 2rem 0;
        min-height: calc(100vh - 140px);
    }

    .page-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .page-header h1 {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--primary-red);
    }

    .empty-cart {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
    }

    .empty-cart-icon {
        font-size: 4rem;
        margin-bottom: 2rem;
        opacity: 0.7;
    }

    .empty-cart h2 {
        margin-bottom: 1rem;
        color: var(--text-color);
    }

    .empty-cart-suggestions {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid ;
    }

    .suggestion-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .suggestion-item {
        background: white;
        border: 1px solid;
        border-radius: var(--border-radius);
        padding: 1rem;
        text-align: center;
        transition: var(--transition-smooth);
    }

    .suggestion-item:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .suggestion-item img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: var(--border-radius);
        margin-bottom: 0.5rem;
    }

    .suggestion-social-proof {
        font-size: 0.8rem;
        color: #666;
        margin: 0.5rem 0;
    }

    .cart-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 3rem;
        align-items: start;
    }

    .cart-items {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
        padding: 1.5rem;
    }

    .cart-item {
        display: flex;
        gap: 1rem;
        padding: 1.5rem 0;
        border-bottom: 1px solid var(--border-color);
        align-items: center;
        transition: var(--transition-smooth);
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-item:hover {
        background: rgba(0,0,0,0.02);
        margin: 0 -1rem;
        padding-left: 1rem;
        padding-right: 1rem;
        border-radius: var(--border-radius);
    }

    .cart-item-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: var(--border-radius);
    }

    .cart-item-details {
        flex: 1;
    }

    .cart-item-name {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .cart-item-price {
        color: #666;
        font-size: 0.9rem;
    }

    .cart-item-tags {
        margin-top: 0.5rem;
        display: flex;
        gap: 0.5rem;
    }

    .cart-tag {
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .cart-tag.premium {
        background: #9b59b6;
        color: white;
    }

    .cart-tag.popular {
        background: #e74c3c;
        color: white;
    }

    .cart-item-quantity {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--light-bg);
        border-radius: var(--border-radius);
        padding: 0.5rem;
    }

    .quantity-btn {
        width: 30px;
        height: 30px;
        border: none;
        background: var(--primary-red);
        color: white;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        transition: var(--transition-smooth);
    }

    .quantity-btn:hover {
        background: #c0392b;
        transform: scale(1.1);
    }

    .quantity-display {
        min-width: 30px;
        text-align: center;
        font-weight: bold;
        font-size: 1.1rem;
    }

    .cart-item-total {
        text-align: right;
        min-width: 100px;
    }

    .item-total {
        display: block;
        font-weight: bold;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        color: var(--primary-red);
    }

    .btn-remove-item {
        background: #dc3545;
        color: white;
        border: none;
        padding: 0.25rem 0.5rem;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-size: 0.8rem;
        transition: var(--transition-smooth);
    }

    .btn-remove-item:hover {
        background: #c82333;
    }

    .cart-upsells {
        background: var(light-bg);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    .cart-upsells h3 {
        margin-bottom: 1rem;
        color: var(--text-color);
    }

    .upsell-suggestions {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .upsell-suggestion {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
    }

    .upsell-icon {
        font-size: 1.5rem;
    }

    .upsell-text {
        flex: 1;
        font-size: 0.9rem;
    }

    .btn-small {
        padding: 0.5rem 1rem;
        background: var(--primary-red);
        color: white;
        text-decoration: none;
        border-radius: var(--border-radius);
        font-size: 0.8rem;
        font-weight: 600;
        transition: var(--transition-smooth);
    }

    .btn-small:hover {
        background: #c0392b;
    }

    .summary-card {
        background: white;
        padding: 2rem;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
        position: sticky;
        top: 2rem;
    }

    .summary-card h3 {
        margin-bottom: 1.5rem;
        color: var(--text-color);
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 1rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
    }

    .total-row {
        font-size: 1.2rem;
        font-weight: bold;
        border-top: 1px solid var(--border-color);
        padding-top: 1rem;
        margin-top: 1rem;
    }

    .free-delivery {
        color: #27ae60;
        font-weight: bold;
    }

    .delivery-notice,
    .savings-notice {
        background: #e8f5e8;
        color: #155724;
        padding: 0.75rem;
        border-radius: var(--border-radius);
        margin: 1rem 0;
        text-align: center;
    }

    .savings-notice {
        background: #d4edda;
    }

    .delivery-estimate {
        background: var(--light-bg);
        border-radius: var(--border-radius);
        padding: 1rem;
        margin: 1.5rem 0;
    }

    .estimate-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .estimate-item:last-child {
        margin-bottom: 0;
    }

    .estimate-icon {
        font-size: 1.2rem;
    }

    .estimate-text {
        font-size: 0.9rem;
        color: #666;
    }

    .checkout-actions {
        margin-top: 2rem;
    }

    .checkout-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text-color);
    }

    .checkout-input,
    .checkout-select,
    .checkout-textarea {
        padding: 0.75rem;
        border: 2px solid var(--border-color);
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: var(--transition-smooth);
    }

    .checkout-input:focus,
    .checkout-select:focus,
    .checkout-textarea:focus {
        outline: none;
        border-color: var(--primary-red);
    }

    .checkout-textarea {
        resize: vertical;
        min-height: 80px;
    }

    .btn-checkout {
        background: var(--primary-red);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: var(--border-radius);
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .btn-checkout:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }

    .btn-checkout:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
    }

    .login-notice {
        display: flex;
        gap: 1rem;
        background: var(--light-bg);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        margin-bottom: 1rem;
        align-items: center;
    }

    .login-notice-icon {
        font-size: 2rem;
    }

    .login-notice-content h4 {
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .login-notice-content p {
        color: #666;
        font-size: 0.9rem;
    }

    .btn-continue-shopping {
        color: var(--primary-red);
        text-decoration: none;
        display: block;
        text-align: center;
        margin-top: 1rem;
        padding: 0.5rem;
        transition: var(--transition-smooth);
    }

    .btn-continue-shopping:hover {
        background: rgba(231, 76, 60, 0.1);
        border-radius: var(--border-radius);
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .cart-content {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .cart-item {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .cart-item-quantity {
            order: 1;
        }

        .cart-item-total {
            order: 2;
            text-align: center;
        }

        .suggestion-grid {
            grid-template-columns: 1fr;
        }

        .upsell-suggestion {
            flex-direction: column;
            text-align: center;
        }

        .summary-card {
            position: static;
        }
    }

    @media (max-width: 480px) {
        .cart-items {
            padding: 1rem;
        }

        .cart-item {
            padding: 1rem 0;
        }

        .summary-card {
            padding: 1.5rem;
        }
    }
</style>