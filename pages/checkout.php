<?php
// pages/checkout.php - Complete Checkout Page using existing functions

// Require login
requireLogin();

// Check if cart is not empty
$cartItems = $_SESSION['cart'] ?? [];
if (empty($cartItems)) {
    header('Location: index.php?page=cart');
    exit;
}

$cartTotal = getCartTotal();
$cartCount = getCartItemCount();
$userId = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    $result = handleCheckout($_POST);
    if ($result['success']) {
        header('Location: index.php?page=home&order_success=1&order_id=' . $result['order_id']);
        exit;
    } else {
        $error = $result['message'];
    }
}

// Track checkout page view
trackBehaviorLog($userId, 'checkout_page_view', [
    'cart_total' => $cartTotal,
    'cart_count' => $cartCount,
    'checkout_step' => 'initial'
]);

// Calculate delivery fee and totals
$deliveryFee = $cartTotal >= 25 ? 0 : 3.99;
$tax = $cartTotal * 0.08; // 8% tax
$finalTotal = $cartTotal + $deliveryFee + $tax;

// Get user's saved addresses (simulate)
$savedAddresses = [
    ['id' => 1, 'label' => 'Home', 'address' => '123 Main St, Seremban, Negeri Sembilan', 'is_default' => true],
    ['id' => 2, 'label' => 'Work', 'address' => '456 Office Blvd, Seremban, Negeri Sembilan', 'is_default' => false]
];

// Estimate delivery time
$estimatedDelivery = date('g:i A', strtotime('+25 minutes')) . ' - ' . date('g:i A', strtotime('+35 minutes'));
?>

<section class="checkout-page">
    <div class="container">
        <!-- Checkout Header -->
        <div class="checkout-header">
            <h1>🛒 Checkout</h1>
            <div class="checkout-steps">
                <div class="step active">
                    <span class="step-number">1</span>
                    <span class="step-label">Order Details</span>
                </div>
                <div class="step-divider"></div>
                <div class="step">
                    <span class="step-number">2</span>
                    <span class="step-label">Payment</span>
                </div>
                <div class="step-divider"></div>
                <div class="step">
                    <span class="step-number">3</span>
                    <span class="step-label">Confirmation</span>
                </div>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="checkout-content">
            <!-- Main Checkout Form -->
            <div class="checkout-main">
                <form method="POST" action="index.php?page=checkout" class="checkout-form" id="checkoutForm">
                    <input type="hidden" name="action" value="checkout">

                    <!-- Delivery Information -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <h3>🚚 Delivery Information</h3>
                            <p>Where should we deliver your delicious order?</p>
                        </div>

                        <!-- Saved Addresses -->
                        <div class="saved-addresses">
                            <h4>Saved Addresses</h4>
                            <div class="address-options">
                                <?php foreach ($savedAddresses as $address): ?>
                                    <label class="address-option">
                                        <input type="radio"
                                               name="saved_address"
                                               value="<?php echo $address['id']; ?>"
                                            <?php echo $address['is_default'] ? 'checked' : ''; ?>
                                               onchange="fillSavedAddress('<?php echo htmlspecialchars($address['address']); ?>')">
                                        <div class="address-content">
                                            <div class="address-label">
                                                <span class="label-icon">📍</span>
                                                <span class="label-text"><?php echo htmlspecialchars($address['label']); ?></span>
                                                <?php if ($address['is_default']): ?>
                                                    <span class="default-badge">Default</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="address-text"><?php echo htmlspecialchars($address['address']); ?></div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>

                                <label class="address-option new-address">
                                    <input type="radio" name="saved_address" value="new" onchange="showNewAddressForm()">
                                    <div class="address-content">
                                        <div class="address-label">
                                            <span class="label-icon">➕</span>
                                            <span class="label-text">Use New Address</span>
                                        </div>
                                        <div class="address-text">Enter a different delivery address</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- New Address Form -->
                        <div class="new-address-form" id="newAddressForm" style="display: none;">
                            <div class="form-group">
                                <label for="delivery_address" class="form-label">Delivery Address *</label>
                                <textarea id="delivery_address"
                                          name="delivery_address"
                                          class="form-textarea"
                                          placeholder="Enter your full delivery address including street, city, and postal code"
                                          rows="3"></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="contact_phone" class="form-label">Phone Number *</label>
                                    <input type="tel"
                                           id="contact_phone"
                                           name="contact_phone"
                                           class="form-input"
                                           placeholder="+60 12-345-6789">
                                </div>
                                <div class="form-group">
                                    <label for="delivery_notes" class="form-label">Delivery Notes</label>
                                    <input type="text"
                                           id="delivery_notes"
                                           name="delivery_notes"
                                           class="form-input"
                                           placeholder="Building name, floor, landmarks...">
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Time -->
                        <div class="delivery-time-section">
                            <h4>⏰ Delivery Time</h4>
                            <div class="delivery-options">
                                <label class="delivery-option">
                                    <input type="radio" name="delivery_time" value="asap" checked>
                                    <div class="option-content">
                                        <span class="option-icon">🚀</span>
                                        <div class="option-text">
                                            <strong>ASAP</strong>
                                            <small>Estimated: <?php echo $estimatedDelivery; ?></small>
                                        </div>
                                    </div>
                                </label>
                                <label class="delivery-option">
                                    <input type="radio" name="delivery_time" value="scheduled">
                                    <div class="option-content">
                                        <span class="option-icon">📅</span>
                                        <div class="option-text">
                                            <strong>Schedule Later</strong>
                                            <small>Choose specific time</small>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="schedule-form" id="scheduleForm" style="display: none;">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="delivery_date" class="form-label">Date</label>
                                        <input type="date"
                                               id="delivery_date"
                                               name="delivery_date"
                                               class="form-input"
                                               min="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="delivery_time_slot" class="form-label">Time</label>
                                        <select id="delivery_time_slot" name="delivery_time_slot" class="form-select">
                                            <option value="">Select time</option>
                                            <option value="11:00-12:00">11:00 AM - 12:00 PM</option>
                                            <option value="12:00-13:00">12:00 PM - 1:00 PM</option>
                                            <option value="13:00-14:00">1:00 PM - 2:00 PM</option>
                                            <option value="18:00-19:00">6:00 PM - 7:00 PM</option>
                                            <option value="19:00-20:00">7:00 PM - 8:00 PM</option>
                                            <option value="20:00-21:00">8:00 PM - 9:00 PM</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <h3>📝 Special Instructions</h3>
                            <p>Any special requests for your order?</p>
                        </div>

                        <div class="form-group">
                            <textarea id="special_instructions"
                                      name="special_instructions"
                                      class="form-textarea"
                                      placeholder="Extra spicy, no onions, extra sauce, etc..."
                                      rows="3"></textarea>
                        </div>

                        <!-- Quick Options -->
                        <div class="quick-options">
                            <h4>Quick Options</h4>
                            <div class="options-grid">
                                <label class="quick-option">
                                    <input type="checkbox" name="options[]" value="extra_utensils">
                                    <span class="option-icon">🍴</span>
                                    <span class="option-text">Extra Utensils</span>
                                </label>
                                <label class="quick-option">
                                    <input type="checkbox" name="options[]" value="napkins">
                                    <span class="option-icon">🧻</span>
                                    <span class="option-text">Extra Napkins</span>
                                </label>
                                <label class="quick-option">
                                    <input type="checkbox" name="options[]" value="no_contact">
                                    <span class="option-icon">🚪</span>
                                    <span class="option-text">Contactless Delivery</span>
                                </label>
                                <label class="quick-option">
                                    <input type="checkbox" name="options[]" value="call_on_arrival">
                                    <span class="option-icon">📞</span>
                                    <span class="option-text">Call on Arrival</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <h3>💳 Payment Method</h3>
                            <p>How would you like to pay?</p>
                        </div>

                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cash" checked>
                                <div class="payment-content">
                                    <span class="payment-icon">💵</span>
                                    <div class="payment-text">
                                        <strong>Cash on Delivery</strong>
                                        <small>Pay when your order arrives</small>
                                    </div>
                                    <span class="payment-badge">Popular</span>
                                </div>
                            </label>

                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card">
                                <div class="payment-content">
                                    <span class="payment-icon">💳</span>
                                    <div class="payment-text">
                                        <strong>Credit/Debit Card</strong>
                                        <small>Secure online payment</small>
                                    </div>
                                </div>
                            </label>

                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="ewallet">
                                <div class="payment-content">
                                    <span class="payment-icon">📱</span>
                                    <div class="payment-text">
                                        <strong>E-Wallet</strong>
                                        <small>GrabPay, Touch 'n Go, Boost</small>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Card Details Form (hidden by default) -->
                        <div class="card-details-form" id="cardDetailsForm" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text"
                                           id="card_number"
                                           name="card_number"
                                           class="form-input"
                                           placeholder="1234 5678 9012 3456"
                                           maxlength="19">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="card_expiry" class="form-label">Expiry Date</label>
                                    <input type="text"
                                           id="card_expiry"
                                           name="card_expiry"
                                           class="form-input"
                                           placeholder="MM/YY"
                                           maxlength="5">
                                </div>
                                <div class="form-group">
                                    <label for="card_cvv" class="form-label">CVV</label>
                                    <input type="text"
                                           id="card_cvv"
                                           name="card_cvv"
                                           class="form-input"
                                           placeholder="123"
                                           maxlength="4">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="checkout-submit">
                        <button type="submit" class="btn-place-order" id="placeOrderBtn">
                            <span class="btn-icon">🛒</span>
                            <span class="btn-text">
                                Place Order • $<?php echo number_format($finalTotal, 2); ?>
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="checkout-sidebar">
                <!-- Order Summary -->
                <div class="order-summary">
                    <div class="summary-header">
                        <h3>📋 Order Summary</h3>
                        <span class="item-count"><?php echo $cartCount; ?> items</span>
                    </div>

                    <div class="summary-items">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="summary-item">
                                <img src="<?php echo $item['image_url'] ?: 'assets/images/default-food.jpg'; ?>"
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     class="summary-item-image">
                                <div class="summary-item-details">
                                    <h4 class="summary-item-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <div class="summary-item-meta">
                                        <span class="item-quantity">×<?php echo $item['quantity']; ?></span>
                                        <span class="item-price">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pricing Breakdown -->
                    <div class="pricing-breakdown">
                        <div class="pricing-row">
                            <span class="pricing-label">Subtotal</span>
                            <span class="pricing-value">$<?php echo number_format($cartTotal, 2); ?></span>
                        </div>

                        <div class="pricing-row">
                            <span class="pricing-label">Delivery Fee</span>
                            <span class="pricing-value <?php echo $deliveryFee == 0 ? 'free' : ''; ?>">
                                <?php echo $deliveryFee == 0 ? 'FREE' : '$' . number_format($deliveryFee, 2); ?>
                            </span>
                        </div>

                        <?php if ($deliveryFee == 0): ?>
                            <div class="free-delivery-message">
                                🎉 You saved $3.99 on delivery!
                            </div>
                        <?php else: ?>
                            <div class="delivery-threshold-message">
                                🚚 Add $<?php echo number_format(25 - $cartTotal, 2); ?> more for free delivery
                            </div>
                        <?php endif; ?>

                        <div class="pricing-row">
                            <span class="pricing-label">Tax (8%)</span>
                            <span class="pricing-value">$<?php echo number_format($tax, 2); ?></span>
                        </div>

                        <div class="pricing-row total">
                            <span class="pricing-label"><strong>Total</strong></span>
                            <span class="pricing-value"><strong>$<?php echo number_format($finalTotal, 2); ?></strong></span>
                        </div>
                    </div>
                </div>

                <!-- Delivery Estimate -->
                <div class="delivery-estimate">
                    <div class="estimate-header">
                        <h4>🚚 Delivery Estimate</h4>
                    </div>
                    <div class="estimate-content">
                        <div class="estimate-time">
                            <span class="estimate-icon">⏰</span>
                            <span class="estimate-text"><?php echo $estimatedDelivery; ?></span>
                        </div>
                        <div class="estimate-location">
                            <span class="estimate-icon">📍</span>
                            <span class="estimate-text">Seremban, Negeri Sembilan</span>
                        </div>
                    </div>
                </div>

                <!-- Trust Indicators -->
                <div class="trust-indicators">
                    <div class="trust-item">
                        <span class="trust-icon">🔒</span>
                        <span class="trust-text">Secure Checkout</span>
                    </div>
                    <div class="trust-item">
                        <span class="trust-icon">✅</span>
                        <span class="trust-text">Order Guarantee</span>
                    </div>
                    <div class="trust-item">
                        <span class="trust-icon">🚚</span>
                        <span class="trust-text">Fresh Delivery</span>
                    </div>
                </div>

                <!-- Support Contact -->
                <div class="support-contact">
                    <h4>Need Help?</h4>
                    <p>Contact our support team</p>
                    <div class="contact-methods">
                        <a href="tel:+60123456789" class="contact-method">
                            <span class="contact-icon">📞</span>
                            <span class="contact-text">+60 12-345-6789</span>
                        </a>
                        <a href="#" class="contact-method">
                            <span class="contact-icon">💬</span>
                            <span class="contact-text">Live Chat</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeCheckoutPage();
        setupFormValidation();
        setupPaymentMethods();
        setupDeliveryOptions();
        trackCheckoutBehavior();
    });

    function initializeCheckoutPage() {
        // Track checkout page load
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('checkout_page_loaded', {
                cart_total: <?php echo $cartTotal; ?>,
                cart_count: <?php echo $cartCount; ?>,
                user_id: <?php echo $userId; ?>,
                timestamp: Date.now()
            });
        }

        // Set default delivery address
        fillSavedAddress('<?php echo htmlspecialchars($savedAddresses[0]['address']); ?>');
    }

    function setupFormValidation() {
        const form = document.getElementById('checkoutForm');

        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Validate delivery address
            const addressField = document.getElementById('delivery_address');
            if (!addressField.value.trim()) {
                showFieldError(addressField, 'Delivery address is required');
                isValid = false;
            } else if (addressField.value.trim().length < 10) {
                showFieldError(addressField, 'Please enter a complete address');
                isValid = false;
            } else {
                clearFieldError(addressField);
            }

            // Validate phone number if using new address
            const newAddressRadio = document.querySelector('input[name="saved_address"][value="new"]');
            if (newAddressRadio.checked) {
                const phoneField = document.getElementById('contact_phone');
                if (!phoneField.value.trim()) {
                    showFieldError(phoneField, 'Phone number is required');
                    isValid = false;
                } else {
                    clearFieldError(phoneField);
                }
            }

            // Validate card details if card payment selected
            const cardRadio = document.querySelector('input[name="payment_method"][value="card"]');
            if (cardRadio.checked) {
                const cardNumber = document.getElementById('card_number');
                const cardExpiry = document.getElementById('card_expiry');
                const cardCvv = document.getElementById('card_cvv');

                if (!cardNumber.value.trim()) {
                    showFieldError(cardNumber, 'Card number is required');
                    isValid = false;
                }
                if (!cardExpiry.value.trim()) {
                    showFieldError(cardExpiry, 'Expiry date is required');
                    isValid = false;
                }
                if (!cardCvv.value.trim()) {
                    showFieldError(cardCvv, 'CVV is required');
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();

                // Track validation error
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('checkout_validation_error', {
                        timestamp: Date.now()
                    });
                }
            } else {
                // Track checkout attempt
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('checkout_submitted', {
                        total_amount: <?php echo $finalTotal; ?>,
                        payment_method: document.querySelector('input[name="payment_method"]:checked').value,
                        delivery_option: document.querySelector('input[name="delivery_time"]:checked').value,
                        timestamp: Date.now()
                    });
                }

                // Show loading state
                const submitBtn = document.getElementById('placeOrderBtn');
                submitBtn.innerHTML = '<span class="loading-spinner">⏳</span> Processing Order...';
                submitBtn.disabled = true;
            }
        });
    }

    function setupPaymentMethods() {
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const cardForm = document.getElementById('cardDetailsForm');

        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                if (this.value === 'card') {
                    cardForm.style.display = 'block';
                } else {
                    cardForm.style.display = 'none';
                }

                // Track payment method selection
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('checkout_payment_method_selected', {
                        payment_method: this.value,
                        timestamp: Date.now()
                    });
                }
            });
        });

        // Format card number input
        const cardNumberInput = document.getElementById('card_number');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function() {
                this.value = this.value.replace(/\s/g, '').replace(/(\d{4})/g, '$1 ').trim();
            });
        }

        // Format expiry input
        const cardExpiryInput = document.getElementById('card_expiry');
        if (cardExpiryInput) {
            cardExpiryInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').replace(/(\d{2})(\d)/, '$1/$2');
            });
        }
    }

    function setupDeliveryOptions() {
        const deliveryOptions = document.querySelectorAll('input[name="delivery_time"]');
        const scheduleForm = document.getElementById('scheduleForm');

        deliveryOptions.forEach(option => {
            option.addEventListener('change', function() {
                if (this.value === 'scheduled') {
                    scheduleForm.style.display = 'block';
                } else {
                    scheduleForm.style.display = 'none';
                }

                // Track delivery option selection
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('checkout_delivery_option_selected', {
                        delivery_option: this.value,
                        timestamp: Date.now()
                    });
                }
            });
        });
    }

    function fillSavedAddress(address) {
        document.getElementById('delivery_address').value = address;

        // Track saved address usage
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('checkout_saved_address_used', {
                timestamp: Date.now()
            });
        }
    }

    function showNewAddressForm() {
        document.getElementById('newAddressForm').style.display = 'block';
        document.getElementById('delivery_address').value = '';
        document.getElementById('delivery_address').focus();

        // Track new address form usage
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('checkout_new_address_form_opened', {
                timestamp: Date.now()
            });
        }
    }

    function showFieldError(field, message) {
        clearFieldError(field);

        field.classList.add('error');

        const errorElement = document.createElement('div');
        errorElement.className = 'field-error';
        errorElement.textContent = message;

        field.parentElement.appendChild(errorElement);
    }

    function clearFieldError(field) {
        field.classList.remove('error');

        const existingError = field.parentElement.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    }

    function trackCheckoutBehavior() {
        // Track time spent on checkout
        const startTime = Date.now();

        window.addEventListener('beforeunload', function() {
            const timeSpent = Date.now() - startTime;
            if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                window.FoodieDelight.PsychologyEngine.trackEvent('checkout_time_spent', {
                    time_spent: timeSpent,
                    completed: false
                });
            }
        });

        // Track form field interactions
        const formFields = document.querySelectorAll('input, textarea, select');
        formFields.forEach(field => {
            field.addEventListener('focus', function() {
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('checkout_field_focused', {
                        field_name: this.name || this.id,
                        field_type: this.type
                    });
                }
            });
        });

        // Track section completion
        const sections = document.querySelectorAll('.checkout-section');
        sections.forEach((section, index) => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                            window.FoodieDelight.PsychologyEngine.trackEvent('checkout_section_viewed', {
                                section_index: index,
                                section_title: section.querySelector('h3').textContent
                            });
                        }
                    }
                });
            }, { threshold: 0.5 });

            observer.observe(section);
        });
    }
</script>

<style>
    /* Checkout page specific styles */
    .checkout-page {
        padding: 2rem 0;
        min-height: calc(100vh - 140px);
        background: #f8f9fa;
    }

    .checkout-header {
        background: white;
        padding: 2rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
        text-align: center;
    }

    .checkout-header h1 {
        font-size: 2.5rem;
        margin-bottom: 2rem;
        color: var(--primary-red);
    }

    .checkout-steps {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        max-width: 600px;
        margin: 0 auto;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        transition: var(--transition-smooth);
    }

    .step.active .step-number {
        background: var(--primary-red);
        color: white;
    }

    .step-label {
        font-size: 0.9rem;
        color: #666;
        font-weight: 500;
    }

    .step.active .step-label {
        color: var(--primary-red);
        font-weight: 600;
    }

    .step-divider {
        width: 80px;
        height: 2px;
        background: #e0e0e0;
    }

    .alert {
        padding: 1rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .checkout-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        align-items: start;
    }

    .checkout-main {
        background: white;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .checkout-section {
        padding: 2rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .checkout-section:last-child {
        border-bottom: none;
    }

    .section-header {
        margin-bottom: 2rem;
    }

    .section-header h3 {
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .section-header p {
        color: #666;
        font-size: 1rem;
    }

    .saved-addresses h4,
    .delivery-time-section h4,
    .quick-options h4,
    .support-contact h4 {
        margin-bottom: 1rem;
        color: var(--text-color);
        font-size: 1.1rem;
    }

    .address-options {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .address-option {
        cursor: pointer;
        border: 2px solid #e0e0e0;
        border-radius: var(--border-radius);
        padding: 1rem;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .address-option:hover {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.05);
    }

    .address-option:has(input:checked) {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.1);
    }

    .address-option input[type="radio"] {
        margin: 0;
        flex-shrink: 0;
    }

    .address-content {
        flex: 1;
    }

    .address-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .label-icon {
        font-size: 1.2rem;
    }

    .label-text {
        font-weight: 600;
        color: var(--text-color);
    }

    .default-badge {
        background: var(--primary-red);
        color: white;
        padding: 0.2rem 0.5rem;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .address-text {
        color: #666;
        font-size: 0.9rem;
    }

    .new-address-form {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: var(--border-radius);
        margin-top: 1rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 1.5rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-label {
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text-color);
    }

    .form-input,
    .form-textarea,
    .form-select {
        padding: 0.75rem;
        border: 2px solid #e0e0e0;
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: var(--transition-smooth);
    }

    .form-input:focus,
    .form-textarea:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary-red);
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .form-input.error,
    .form-textarea.error {
        border-color: #dc3545;
    }

    .field-error {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }

    .delivery-options {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .delivery-option {
        flex: 1;
        cursor: pointer;
        border: 2px solid #e0e0e0;
        border-radius: var(--border-radius);
        padding: 1rem;
        transition: var(--transition-smooth);
    }

    .delivery-option:hover {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.05);
    }

    .delivery-option:has(input:checked) {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.1);
    }

    .delivery-option input[type="radio"] {
        display: none;
    }

    .option-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .option-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .option-text strong {
        display: block;
        margin-bottom: 0.25rem;
        color: var(--text-color);
    }

    .option-text small {
        color: #666;
        font-size: 0.85rem;
    }

    .schedule-form {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: var(--border-radius);
        margin-top: 1rem;
    }

    .options-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .quick-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        border: 2px solid #e0e0e0;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: var(--transition-smooth);
    }

    .quick-option:hover {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.05);
    }

    .quick-option:has(input:checked) {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.1);
    }

    .quick-option input[type="checkbox"] {
        margin: 0;
    }

    .quick-option .option-icon {
        font-size: 1.2rem;
    }

    .quick-option .option-text {
        font-weight: 500;
        color: var(--text-color);
    }

    .payment-methods {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .payment-option {
        cursor: pointer;
        border: 2px solid #e0e0e0;
        border-radius: var(--border-radius);
        padding: 1rem;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .payment-option:hover {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.05);
    }

    .payment-option:has(input:checked) {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.1);
    }

    .payment-option input[type="radio"] {
        margin: 0;
        flex-shrink: 0;
    }

    .payment-content {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .payment-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .payment-text {
        flex: 1;
    }

    .payment-text strong {
        display: block;
        margin-bottom: 0.25rem;
        color: var(--text-color);
    }

    .payment-text small {
        color: #666;
        font-size: 0.85rem;
    }

    .payment-badge {
        background: var(--primary-red);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .card-details-form {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: var(--border-radius);
        margin-top: 1rem;
    }

    .checkout-submit {
        padding: 2rem;
        background: #f8f9fa;
        text-align: center;
    }

    .btn-place-order {
        background: var(--primary-red);
        color: white;
        border: none;
        padding: 1.25rem 3rem;
        border-radius: 50px;
        font-size: 1.2rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin: 0 auto;
        min-width: 250px;
    }

    .btn-place-order:hover {
        background: #c0392b;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
    }

    .btn-place-order:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
    }

    .btn-icon {
        font-size: 1.3rem;
    }

    .checkout-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .order-summary {
        background: white;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        position: sticky;
        top: 2rem;
    }

    .summary-header {
        background: #f8f9fa;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e0e0e0;
    }

    .summary-header h3 {
        margin: 0;
        color: var(--text-color);
        font-size: 1.2rem;
    }

    .item-count {
        background: var(--primary-red);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .summary-items {
        padding: 1.5rem;
        max-height: 300px;
        overflow-y: auto;
    }

    .summary-item {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .summary-item:last-child {
        margin-bottom: 0;
        border-bottom: none;
        padding-bottom: 0;
    }

    .summary-item-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: var(--border-radius);
        flex-shrink: 0;
    }

    .summary-item-details {
        flex: 1;
    }

    .summary-item-name {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .summary-item-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .item-quantity {
        color: #666;
        font-size: 0.85rem;
    }

    .item-price {
        font-weight: 600;
        color: var(--primary-red);
        font-size: 0.9rem;
    }

    .pricing-breakdown {
        padding: 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #e0e0e0;
    }

    .pricing-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .pricing-row:last-child {
        margin-bottom: 0;
    }

    .pricing-row.total {
        padding-top: 1rem;
        border-top: 2px solid #e0e0e0;
        font-size: 1.1rem;
    }

    .pricing-label {
        color: #666;
    }

    .pricing-value {
        font-weight: 600;
        color: var(--text-color);
    }

    .pricing-value.free {
        color: #27ae60;
        font-weight: 700;
    }

    .free-delivery-message {
        background: #d4edda;
        color: #155724;
        padding: 0.75rem;
        border-radius: var(--border-radius);
        margin: 1rem 0;
        text-align: center;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .delivery-threshold-message {
        background: #fff3cd;
        color: #856404;
        padding: 0.75rem;
        border-radius: var(--border-radius);
        margin: 1rem 0;
        text-align: center;
        font-size: 0.85rem;
    }

    .delivery-estimate {
        background: white;
        padding: 1.5rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-md);
    }

    .estimate-header h4 {
        margin-bottom: 1rem;
        color: var(--text-color);
    }

    .estimate-content {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .estimate-time,
    .estimate-location {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .estimate-icon {
        font-size: 1.2rem;
        width: 20px;
        text-align: center;
    }

    .estimate-text {
        color: #666;
        font-size: 0.9rem;
    }

    .trust-indicators {
        background: white;
        padding: 1.5rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-md);
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .trust-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .trust-icon {
        font-size: 1.2rem;
        color: #27ae60;
    }

    .trust-text {
        color: #666;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .support-contact {
        background: white;
        padding: 1.5rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-md);
        text-align: center;
    }

    .support-contact h4 {
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .support-contact p {
        color: #666;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .contact-methods {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .contact-method {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: var(--border-radius);
        text-decoration: none;
        color: var(--text-color);
        transition: var(--transition-smooth);
    }

    .contact-method:hover {
        background: rgba(231, 76, 60, 0.1);
        color: var(--primary-red);
    }

    .contact-icon {
        font-size: 1.1rem;
    }

    .contact-text {
        font-weight: 500;
    }

    /* Mobile responsiveness */
    @media (max-width: 968px) {
        .checkout-content {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .checkout-steps {
            gap: 0.5rem;
        }

        .step-divider {
            width: 40px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .delivery-options {
            flex-direction: column;
        }

        .options-grid {
            grid-template-columns: 1fr;
        }

        .order-summary {
            position: static;
        }
    }

    @media (max-width: 480px) {
        .checkout-section {
            padding: 1.5rem;
        }

        .checkout-header {
            padding: 1.5rem;
        }

        .checkout-header h1 {
            font-size: 2rem;
        }

        .checkout-steps {
            flex-direction: column;
            gap: 1rem;
        }

        .step-divider {
            width: 2px;
            height: 20px;
        }

        .address-option,
        .payment-option {
            flex-direction: column;
            text-align: center;
            gap: 0.75rem;
        }

        .btn-place-order {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            min-width: 200px;
        }
    }
</style>