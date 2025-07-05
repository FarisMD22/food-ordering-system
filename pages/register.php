<?php
// pages/register.php - Complete Registration Page using existing functions

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php?page=home');
    exit;
}

// Handle form submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $result = handleRegister($_POST);
    if ($result['success']) {
        header('Location: index.php?page=login&success=1');
        exit;
    } else {
        $error = $result['message'];
    }
}

// Track registration page view
trackBehaviorLog(null, 'register_page_view', [
    'time_of_day' => date('H:i'),
    'referrer' => $_SERVER['HTTP_REFERER'] ?? '',
    'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 100)
]);

// Get stats for social proof
$totalUsers = getUserStatistics()['total_users'] ?? 250;
$newUsersThisMonth = getUserStatistics()['new_users_month'] ?? 45;
?>

<section class="register-section">
    <div class="container">
        <!-- Header Section -->
        <div class="register-header">
            <span class="form-icon">🍽️</span>
            <h1 class="form-title">Join FoodieDelight!</h1>
            <p class="form-subtitle">Create your personalized food ordering experience in just a few steps</p>

            <!-- Progress Indicator -->
            <div class="progress-indicator">
                <div class="progress-step active" data-step="1">
                    <span class="step-number">1</span>
                    <span class="step-label">Personal Info</span>
                </div>
                <div class="progress-step" data-step="2">
                    <span class="step-number">2</span>
                    <span class="step-label">Security</span>
                </div>
                <div class="progress-step" data-step="3">
                    <span class="step-number">3</span>
                    <span class="step-label">Preferences</span>
                </div>
                <div class="progress-step" data-step="4">
                    <span class="step-number">4</span>
                    <span class="step-label">Complete</span>
                </div>
            </div>
        </div>

        <!-- Main Form Container -->
        <div class="register-form-wrapper">
            <div class="register-form-container">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">⚠️</span>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=register" class="register-form" id="registerForm">
                    <input type="hidden" name="action" value="register">

                    <!-- Step 1: Personal Information -->
                    <div class="form-step" data-step="1">
                        <div class="step-header">
                            <h2 class="step-title">👤 Let's start with your name</h2>
                            <p class="step-description">We'd love to know what to call you!</p>
                        </div>

                        <div class="form-group highlight">
                            <label for="name" class="form-label">What's your full name?</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   required
                                   class="form-input large"
                                   placeholder="Enter your full name"
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   required
                                   class="form-input"
                                   placeholder="your@email.com"
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            <small class="form-hint">We'll use this to send order updates and receipts</small>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number (Optional)</label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   class="form-input"
                                   placeholder="+60 12-345 6789"
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            <small class="form-hint">For delivery coordination</small>
                        </div>
                    </div>

                    <!-- Step 2: Account Security -->
                    <div class="form-step" data-step="2">
                        <div class="step-header">
                            <h2 class="step-title">🔐 Secure your account</h2>
                            <p class="step-description">Create a strong password to protect your account</p>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Create Password</label>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   required
                                   class="form-input"
                                   placeholder="Create a strong password">
                            <small class="form-hint">At least 8 characters with letters and numbers</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password"
                                   id="confirm_password"
                                   name="confirm_password"
                                   required
                                   class="form-input"
                                   placeholder="Re-enter your password">
                        </div>
                    </div>

                    <!-- Step 3: Food Preferences -->
                    <div class="form-step" data-step="3">
                        <div class="step-header">
                            <h2 class="step-title">🍽️ Tell us about your food preferences</h2>
                            <p class="step-description">This helps us recommend the perfect meals for you</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Dietary Preferences</label>
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="dietary_preference" value="none" checked>
                                    <div class="radio-content">
                                        <span class="radio-icon">🍖</span>
                                        <div class="radio-text">
                                            <strong>No Restrictions</strong>
                                            <small>I enjoy all types of food</small>
                                        </div>
                                    </div>
                                </label>

                                <label class="radio-option">
                                    <input type="radio" name="dietary_preference" value="vegetarian">
                                    <div class="radio-content">
                                        <span class="radio-icon">🥬</span>
                                        <div class="radio-text">
                                            <strong>Vegetarian</strong>
                                            <small>No meat, but dairy and eggs are okay</small>
                                        </div>
                                    </div>
                                </label>

                                <label class="radio-option">
                                    <input type="radio" name="dietary_preference" value="vegan">
                                    <div class="radio-content">
                                        <span class="radio-icon">🌱</span>
                                        <div class="radio-text">
                                            <strong>Vegan</strong>
                                            <small>Plant-based foods only</small>
                                        </div>
                                    </div>
                                </label>

                                <label class="radio-option">
                                    <input type="radio" name="dietary_preference" value="halal">
                                    <div class="radio-content">
                                        <span class="radio-icon">☪️</span>
                                        <div class="radio-text">
                                            <strong>Halal</strong>
                                            <small>Halal-certified food only</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Any Food Allergies? (Optional)</label>
                            <div class="checkbox-group">
                                <label class="checkbox-option">
                                    <input type="checkbox" name="allergies[]" value="nuts">
                                    <div class="checkbox-content">
                                        <strong>🥜 Nuts & Peanuts</strong>
                                        <small>Including tree nuts and peanut products</small>
                                    </div>
                                </label>

                                <label class="checkbox-option">
                                    <input type="checkbox" name="allergies[]" value="dairy">
                                    <div class="checkbox-content">
                                        <strong>🥛 Dairy</strong>
                                        <small>Milk, cheese, and dairy products</small>
                                    </div>
                                </label>

                                <label class="checkbox-option">
                                    <input type="checkbox" name="allergies[]" value="seafood">
                                    <div class="checkbox-content">
                                        <strong>🦐 Seafood</strong>
                                        <small>Fish, shellfish, and seafood products</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Final Terms and Benefits -->
                    <div class="form-step" data-step="4">
                        <div class="step-header">
                            <h2 class="step-title">🎉 Almost there!</h2>
                            <p class="step-description">Just a few final details and you're all set</p>
                        </div>

                        <!-- Benefits Preview -->
                        <div class="benefits-preview">
                            <h3 style="color: #1A1A1A">🌟 What you'll get as a member:</h3>
                            <div class="benefits-grid">
                                <div class="benefit-card">
                                    <span class="benefit-icon">🚚</span>
                                    <strong>Free Delivery</strong>
                                    <small>On orders over RM30</small>
                                </div>
                                <div class="benefit-card">
                                    <span class="benefit-icon">🎯</span>
                                    <strong>Personalized Recs</strong>
                                    <small>AI-powered suggestions</small>
                                </div>
                                <div class="benefit-card">
                                    <span class="benefit-icon">💰</span>
                                    <strong>Exclusive Deals</strong>
                                    <small>Member-only discounts</small>
                                </div>
                                <div class="benefit-card">
                                    <span class="benefit-icon">🏆</span>
                                    <strong>Loyalty Points</strong>
                                    <small>Earn with every order</small>
                                </div>
                            </div>
                        </div>

                        <!-- Social Proof -->
                        <div class="social-proof-inline">
                            <div class="stats-inline">
                                <span class="stat"><strong><?php echo number_format($totalUsers); ?>+</strong> happy customers</span>
                                <span class="stat"><strong><?php echo $newUsersThisMonth; ?></strong> joined this month</span>
                                <span class="stat"><strong>4.8★</strong> average rating</span>
                            </div>
                            <p class="testimonial-inline">
                                💬 <em>"FoodieDelight changed how I order food. The recommendations are spot-on!"</em> - Sarah M.
                            </p>
                        </div>

                        <!-- Terms and Marketing -->
                        <div class="terms-section">
                            <label class="checkbox-option required">
                                <input type="checkbox" name="terms" required>
                                <div class="checkbox-content">
                                    <strong style="color: #1A1A1A">I agree to the Terms of Service and Privacy Policy</strong>
                                    <small>By checking this, you agree to our <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a></small>
                                </div>
                            </label>

                            <label class="checkbox-option">
                                <input type="checkbox" name="marketing" value="1" checked>
                                <div class="checkbox-content">
                                    <strong style="color: #1A1A1A">Send me special offers and updates</strong>
                                    <small>Get exclusive deals, new menu items, and personalized recommendations (you can unsubscribe anytime)</small>
                                </div>
                            </label>
                        </div>

                        <!-- Final Submit -->
                        <div class="form-submit-final">
                            <button type="submit" class="btn-register-final">
                                <span class="btn-icon">🚀</span>
                                Create My Account
                                <span class="btn-subtext">Join <?php echo number_format($totalUsers); ?>+ happy customers!</span>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="form-footer">
                    <p style="color: #1A1A1A">Already have an account? <a href="index.php?page=login" class="login-link">Sign In</a></p>
                    <p><a href="index.php?page=home" class="home-link">← Back to Home</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Register page specific styles - Process Flow Layout */
    .register-section {
        padding: 2rem 0;
        min-height: calc(100vh - 140px);
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 0 2rem;
    }

    /* Header Section */
    .register-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .form-icon {
        font-size: 4rem;
        display: block;
        margin-bottom: 1rem;
    }

    .form-title {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        color: var(--primary-red);
        font-family: var(--font-appetite);
    }

    .form-subtitle {
        color: #666;
        font-size: 1.2rem;
        margin-bottom: 2rem;
    }

    /* Progress Indicator */
    .progress-indicator {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        opacity: 0.5;
        transition: var(--transition-smooth);
    }

    .progress-step.active {
        opacity: 1;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #666;
        transition: var(--transition-smooth);
    }

    .progress-step.active .step-number {
        background: var(--primary-red);
        color: white;
    }

    .step-label {
        font-size: 0.8rem;
        color: #666;
        text-align: center;
    }

    .progress-step.active .step-label {
        color: var(--primary-red);
        font-weight: 600;
    }

    /* Form Container */
    .register-form-wrapper {
        background: white;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .register-form-container {
        padding: 3rem;
    }

    /* Form Steps */
    .form-step {
        margin-bottom: 4rem;
        padding-bottom: 3rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .form-step:last-of-type {
        border-bottom: none;
        margin-bottom: 2rem;
    }

    .step-header {
        text-align: center;
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #f5f5f5;
    }

    .step-title {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: gray;
        font-weight: 700;
    }

    .step-description {
        color: #666;
        font-size: 1.1rem;
        margin: 0;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 2rem;
    }

    .form-group.highlight {
        background: rgba(231, 76, 60, 0.05);
        padding: 2rem;
        border-radius: var(--border-radius);
        border: 2px solid rgba(231, 76, 60, 0.1);
    }

    .form-label {
        display: block;
        margin-bottom: 0.75rem;
        font-weight: 600;
        color: darkgrey;
        font-size: 1.1rem;
    }

    .form-input {
        width: 100%;
        padding: 1rem;
        border: 2px solid #e0e0e0;
        border-radius: var(--border-radius);
        font-size: 1.1rem;
        transition: var(--transition-smooth);
        box-sizing: border-box;
    }

    .form-input.large {
        padding: 1.25rem;
        font-size: 1.3rem;
        font-weight: 500;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-red);
        box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.1);
    }

    .form-hint {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.9rem;
        color: #666;
        font-style: italic;
    }

    /* Radio Groups */
    .radio-group {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .radio-option {
        cursor: pointer;
        border: 2px solid #e0e0e0;
        border-radius: var(--border-radius);
        padding: 1.25rem;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .radio-option:hover {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.05);
        transform: translateX(5px);
    }

    .radio-option:has(input:checked) {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.1);
        transform: translateX(8px);
    }

    .radio-option input[type="radio"] {
        margin: 0;
        flex-shrink: 0;
        transform: scale(1.2);
    }

    .radio-content {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .radio-icon {
        font-size: 1.8rem;
        flex-shrink: 0;
    }

    .radio-text strong {
        display: block;
        margin-bottom: 0.25rem;
        color: var(--text-color);
        font-size: 1.1rem;
    }

    .radio-text small {
        color: #666;
        font-size: 0.9rem;
    }

    /* Checkbox Groups */
    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .checkbox-option {
        cursor: pointer;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem;
        border: 1px solid #e0e0e0;
        border-radius: var(--border-radius);
        transition: var(--transition-smooth);
    }

    .checkbox-option:hover {
        background: rgba(231, 76, 60, 0.05);
        border-color: var(--primary-red);
        transform: translateX(3px);
    }

    .checkbox-option.required {
        border-color: var(--primary-red);
        background: rgba(231, 76, 60, 0.05);
    }

    .checkbox-option input[type="checkbox"] {
        margin: 0;
        flex-shrink: 0;
        margin-top: 2px;
        transform: scale(1.2);
    }

    .checkbox-content strong {
        display: block;
        margin-bottom: 0.25rem;
        color: var(--text-color);
        font-size: 1.05rem;
    }

    .checkbox-content small {
        color: #666;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .checkbox-content a {
        color: var(--primary-red);
        text-decoration: underline;
    }

    /* Benefits Preview */
    .benefits-preview {
        background: var(--light-bg);
        padding: 2rem;
        border-radius: var(--border-radius);
        margin-bottom: 2rem;
        text-align: center;
    }

    .benefits-preview h3 {
        margin-bottom: 1.5rem;
        color: var(--text-color);
        font-size: 1.3rem;
    }

    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }

    .benefit-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .benefit-icon {
        font-size: 1.5rem;
    }

    .benefit-card strong {
        font-size: 0.9rem;
        color: var(--text-color);
        text-align: center;
    }

    .benefit-card small {
        font-size: 0.8rem;
        color: #666;
        text-align: center;
    }

    /* Social Proof Inline */
    .social-proof-inline {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: var(--border-radius);
        margin-bottom: 2rem;
        text-align: center;
    }

    .stats-inline {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .stat {
        font-size: 0.9rem;
        color: #666;
    }

    .stat strong {
        color: var(--primary-red);
        font-size: 1.1rem;
    }

    .testimonial-inline {
        font-size: 0.95rem;
        color: #555;
        margin: 0;
    }

    /* Terms Section */
    .terms-section {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 3rem;
    }

    /* Final Submit Button */
    .form-submit-final {
        text-align: center;
    }

    .btn-register-final {
        background: var(--gradient-appetite);
        color: white;
        border: none;
        border-radius: 60px;
        padding: 1.5rem 3rem;
        font-size: 1.3rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition-smooth);
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        min-width: 300px;
        box-shadow: 0 4px 20px rgba(231, 76, 60, 0.3);
    }

    .btn-register-final:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 8px 30px rgba(231, 76, 60, 0.4);
    }

    .btn-register-final:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
    }

    .btn-icon {
        font-size: 1.5rem;
    }

    .btn-subtext {
        font-size: 0.9rem;
        font-weight: 400;
        opacity: 0.9;
    }

    /* Alert */
    .alert {
        padding: 1rem;
        border-radius: var(--border-radius);
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Form Footer */
    .form-footer {
        text-align: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #eee;
    }

    .form-footer p {
        margin-bottom: 0.5rem;
    }

    .login-link,
    .home-link {
        color: var(--primary-red);
        font-weight: 600;
        text-decoration: none;
    }

    .login-link:hover,
    .home-link:hover {
        text-decoration: underline;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .container {
            padding: 0 1rem;
        }

        .register-form-container {
            padding: 2rem;
        }

        .form-title {
            font-size: 2.5rem;
        }

        .step-title {
            font-size: 1.5rem;
        }

        .progress-indicator {
            gap: 0.5rem;
        }

        .step-number {
            width: 35px;
            height: 35px;
        }

        .step-label {
            font-size: 0.7rem;
        }

        .benefits-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .stats-inline {
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn-register-final {
            min-width: 250px;
            padding: 1.25rem 2rem;
            font-size: 1.1rem;
        }
    }

    @media (max-width: 480px) {
        .register-form-container {
            padding: 1.5rem;
        }

        .form-title {
            font-size: 2rem;
        }

        .progress-indicator {
            flex-wrap: wrap;
            gap: 1rem;
        }

        .benefits-grid {
            grid-template-columns: 1fr;
        }

        .radio-option,
        .checkbox-option {
            padding: 1rem;
        }

        .form-group.highlight {
            padding: 1.5rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeRegisterForm();
        trackRegisterBehavior();
        setupProgressTracking();
    });

    function initializeRegisterForm() {
        const form = document.getElementById('registerForm');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        // Password confirmation validation
        confirmPasswordInput.addEventListener('blur', function() {
            if (passwordInput.value && confirmPasswordInput.value) {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity('Passwords do not match');
                } else {
                    confirmPasswordInput.setCustomValidity('');
                }
            }
        });

        // Form submission enhancement
        if (form) {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('.btn-register-final');
                submitBtn.innerHTML = '<span class="btn-icon">⏳</span> Creating Account...<span class="btn-subtext">Please wait...</span>';
                submitBtn.disabled = true;
            });
        }
    }

    function setupProgressTracking() {
        const formInputs = document.querySelectorAll('#registerForm input[required]');
        const progressSteps = document.querySelectorAll('.progress-step');

        // Function to update progress indicator
        function updateProgress() {
            let step1Complete = document.getElementById('name').value && document.getElementById('email').value;
            let step2Complete = document.getElementById('password').value && document.getElementById('confirm_password').value;
            let step3Complete = document.querySelector('input[name="dietary_preference"]:checked');
            let step4Complete = document.querySelector('input[name="terms"]:checked');

            // Update visual indicators
            progressSteps.forEach((step, index) => {
                const stepNum = index + 1;
                step.classList.remove('active');

                if (stepNum === 1 && step1Complete) step.classList.add('active');
                if (stepNum === 2 && step1Complete && step2Complete) step.classList.add('active');
                if (stepNum === 3 && step1Complete && step2Complete && step3Complete) step.classList.add('active');
                if (stepNum === 4 && step1Complete && step2Complete && step3Complete && step4Complete) step.classList.add('active');
            });
        }

        // Add listeners to track progress
        formInputs.forEach(input => {
            input.addEventListener('input', updateProgress);
            input.addEventListener('change', updateProgress);
        });

        // Initial progress check
        updateProgress();
    }

    function trackRegisterBehavior() {
        // Track time spent on registration
        const startTime = Date.now();

        window.addEventListener('beforeunload', function() {
            const timeSpent = Date.now() - startTime;
            if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                window.FoodieDelight.PsychologyEngine.trackEvent('register_time_spent', {
                    time_spent: timeSpent
                });
            }
        });

        // Track step completion
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', function() {
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('register_step_progress', {
                        field: this.name,
                        step: this.closest('.form-step')?.dataset.step
                    });
                }
            });
        });
    }
</script>