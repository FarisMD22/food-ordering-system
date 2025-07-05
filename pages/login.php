<?php
// pages/login.php - Complete Login Page using existing functions

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php?page=home');
    exit;
}

// Handle form submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $result = handleLogin($_POST);
    if ($result['success']) {
        header('Location: index.php?page=home');
        exit;
    } else {
        $error = $result['message'];
    }
}

// Success message from registration
if (isset($_GET['success'])) {
    $success = 'Registration successful! Please log in with your credentials.';
}

// Track login page view
trackBehaviorLog(null, 'login_page_view', [
    'time_of_day' => date('H:i'),
    'referrer' => $_SERVER['HTTP_REFERER'] ?? '',
    'user_agent' => substr($_SERVER['HTTP_AGENT'] ?? '', 0, 100)
]);

// Get stats for social proof
$totalUsers = getUserStatistics()['total_users'] ?? 250;
$todayOrders = getTotalOrdersToday();
?>

<section class="login-section">
    <div class="container">
        <div class="login-container">
            <!-- Login Form -->
            <div class="login-form-container">
                <div class="form-header">
                    <span class="form-icon">🔑</span>
                    <h1 class="form-title">Welcome Back!</h1>
                    <p class="form-subtitle">Sign in to your FoodieDelight account</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <span class="alert-icon">⚠️</span>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <span class="alert-icon">✅</span>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=login" class="login-form">
                    <input type="hidden" name="action" value="login">

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email"
                               id="email"
                               name="email"
                               required
                               class="form-input"
                               placeholder="Enter your email"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               class="form-input"
                               placeholder="Enter your password">
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label" style="color: #1A1A1A">
                            <input  type="checkbox" name="remember_me">
                            <span class="checkmark"></span>
                            Remember me
                        </label>
                        <a href="#" class="forgot-password">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-form-submit">
                        <span class="btn-icon">🚀</span>
                        Sign In
                    </button>
                </form>

                <div class="form-footer">
                    <p>Don't have an account?
                        <a href="index.php?page=register" class="register-link">Create one now</a>
                    </p>
                </div>

                <!-- Demo Account Info -->
                <div class="demo-account">
                    <h4>🎯 Demo Account</h4>
                    <p>Try our system with these credentials:</p>
                    <div class="demo-credentials">
                        <div class="demo-item">
                            <strong>Email:</strong> demo@foodiedelight.com<br>
                            <strong>Password:</strong> demo123
                        </div>
                        <div class="demo-item">
                            <strong>Admin Email:</strong> admin@foodiedelight.com<br>
                            <strong>Admin Password:</strong> admin123
                        </div>
                    </div>
                    <button class="btn-demo" onclick="fillDemoCredentials()">
                        Use Demo Account
                    </button>
                </div>
            </div>

            <!-- Login Benefits -->
            <div class="login-benefits">
                <div class="benefits-header">
                    <h3>🍽️ Why Join FoodieDelight?</h3>
                    <p>Unlock amazing benefits when you sign in</p>
                </div>

                <div class="benefits-list">
                    <div class="benefit-item">
                        <span class="benefit-icon">🎯</span>
                        <div class="benefit-content">
                            <h4>Personalized Recommendations</h4>
                            <p>Get food suggestions based on your taste preferences and psychology profile</p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <span class="benefit-icon">🏃‍♂️</span>
                        <div class="benefit-content">
                            <h4>Faster Checkout</h4>
                            <p>Save your delivery addresses and payment methods for lightning-fast ordering</p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <span class="benefit-icon">📊</span>
                        <div class="benefit-content">
                            <h4>Order History & Analytics</h4>
                            <p>Track your favorite meals and discover your eating patterns</p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <span class="benefit-icon">🎁</span>
                        <div class="benefit-content">
                            <h4>Exclusive Deals</h4>
                            <p>Access member-only discounts and early access to new menu items</p>
                        </div>
                    </div>

                    <div class="benefit-item">
                        <span class="benefit-icon">⭐</span>
                        <div class="benefit-content">
                            <h4>Priority Support</h4>
                            <p>Get faster customer service and priority during peak hours</p>
                        </div>
                    </div>
                </div>

                <!-- Social Proof -->
                <div class="social-proof-section">
                    <div class="social-stats">
                        <div class="stat-card">
                            <span class="stat-number"><?php echo number_format($totalUsers); ?>+</span>
                            <span class="stat-label">Happy Customers</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number"><?php echo $todayOrders; ?></span>
                            <span class="stat-label">Orders Today</span>
                        </div>
                        <div class="stat-card">
                            <span class="stat-number">4.9</span>
                            <span class="stat-label">⭐ Rating</span>
                        </div>
                    </div>

                    <div class="testimonial">
                        <p>"The personalized recommendations are spot on! FoodieDelight knows exactly what I'm craving."</p>
                        <cite>- Sarah M., Regular Customer</cite>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize login page features
        initializeLoginPage();

        // Setup form validation
        setupFormValidation();

        // Track login attempts
        trackLoginInteractions();
    });

    function initializeLoginPage() {
        // Track page load
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('login_page_loaded', {
                timestamp: Date.now(),
                referrer: document.referrer
            });
        }

        // Auto-focus email field
        const emailField = document.getElementById('email');
        if (emailField && !emailField.value) {
            emailField.focus();
        }
    }

    function setupFormValidation() {
        const form = document.querySelector('.login-form');
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');

        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Email validation
            if (!emailField.value.trim()) {
                showFieldError(emailField, 'Email is required');
                isValid = false;
            } else if (!isValidEmail(emailField.value)) {
                showFieldError(emailField, 'Please enter a valid email');
                isValid = false;
            } else {
                clearFieldError(emailField);
            }

            // Password validation
            if (!passwordField.value.trim()) {
                showFieldError(passwordField, 'Password is required');
                isValid = false;
            } else {
                clearFieldError(passwordField);
            }

            if (!isValid) {
                e.preventDefault();

                // Track validation error
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('login_validation_error', {
                        timestamp: Date.now()
                    });
                }
            } else {
                // Track login attempt
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('login_attempt', {
                        email: emailField.value,
                        timestamp: Date.now()
                    });
                }

                // Show loading state
                const submitBtn = this.querySelector('.btn-form-submit');
                submitBtn.innerHTML = '<span class="loading-spinner">⏳</span> Signing In...';
                submitBtn.disabled = true;
            }
        });

        // Real-time validation
        emailField.addEventListener('blur', function() {
            if (this.value.trim() && !isValidEmail(this.value)) {
                showFieldError(this, 'Please enter a valid email');
            } else {
                clearFieldError(this);
            }
        });

        passwordField.addEventListener('input', function() {
            clearFieldError(this);
        });
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

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function fillDemoCredentials() {
        document.getElementById('email').value = 'demo@foodiedelight.com';
        document.getElementById('password').value = 'demo123';

        // Track demo usage
        if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
            window.FoodieDelight.PsychologyEngine.trackEvent('demo_credentials_used', {
                timestamp: Date.now()
            });
        }
    }

    function trackLoginInteractions() {
        // Track time spent on login page
        const startTime = Date.now();

        window.addEventListener('beforeunload', function() {
            const timeSpent = Date.now() - startTime;
            if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                window.FoodieDelight.PsychologyEngine.trackEvent('login_page_time_spent', {
                    time_spent: timeSpent
                });
            }
        });

        // Track benefit interactions
        document.querySelectorAll('.benefit-item').forEach(item => {
            item.addEventListener('click', function() {
                const benefitTitle = this.querySelector('h4').textContent;
                if (window.FoodieDelight && window.FoodieDelight.PsychologyEngine) {
                    window.FoodieDelight.PsychologyEngine.trackEvent('login_benefit_clicked', {
                        benefit: benefitTitle
                    });
                }
            });
        });
    }
</script>

<style>
    /* Login page specific styles */
    .login-section {
        padding: 2rem 0;
        min-height: calc(100vh - 140px);
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .login-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: start;
        max-width: 1200px;
        margin: 0 auto;
    }

    .login-form-container {
        background: white;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .form-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .form-icon {
        font-size: 3rem;
        display: block;
        margin-bottom: 1rem;
    }

    .form-title {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: var(--primary-red);
    }

    .form-subtitle {
        color: #666;
        font-size: 1.1rem;
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

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .login-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text-color);
    }

    .form-input {
        padding: 0.75rem;
        border: 2px solid #e0e0e0;
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: var(--transition-smooth);
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-red);
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .form-input.error {
        border-color: #dc3545;
    }

    .field-error {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 1rem 0;
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

    .forgot-password {
        color: var(--primary-red);
        text-decoration: none;
        font-size: 0.9rem;
    }

    .forgot-password:hover {
        text-decoration: underline;
    }

    .btn-form-submit {
        background: var(--primary-red);
        color: white;
        border: none;
        padding: 1rem;
        border-radius: var(--border-radius);
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-form-submit:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }

    .btn-form-submit:disabled {
        background: #6c757d;
        cursor: not-allowed;
        transform: none;
    }

    .form-footer {
        text-align: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #eee;
    }

    .register-link {
        color: var(--primary-red);
        font-weight: 600;
        text-decoration: none;
    }

    .register-link:hover {
        text-decoration: underline;
    }

    .demo-account {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: var(--border-radius);
        margin-top: 2rem;
        text-align: center;
    }

    .demo-account h4 {
        margin-bottom: 0.5rem;
        color: var(--primary-red);
    }

    .demo-credentials {
        background: white;
        padding: 1rem;
        border-radius: var(--border-radius);
        margin: 1rem 0;
        font-family: monospace;
        font-size: 0.9rem;
    }

    .demo-item {
        margin-bottom: 0.5rem;
    }

    .demo-item:last-child {
        margin-bottom: 0;
    }

    .btn-demo {
        background: var(--secondary-color);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: var(--border-radius);
        cursor: pointer;
        font-size: 0.9rem;
    }

    .login-benefits {
        background: white;
        padding: 3rem;
        border-radius: var(--border-radius-large);
        box-shadow: var(--shadow-lg);
    }

    .benefits-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .benefits-header h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: var(--primary-red);
    }

    .benefits-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .benefit-item {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        padding: 1rem;
        border-radius: var(--border-radius);
        transition: var(--transition-smooth);
        cursor: pointer;
    }

    .benefit-item:hover {
        background: rgba(231, 76, 60, 0.05);
        transform: translateX(5px);
    }

    .benefit-icon {
        font-size: 2rem;
        flex-shrink: 0;
    }

    .benefit-content h4 {
        margin-bottom: 0.5rem;
        color: var(--text-color);
    }

    .benefit-content p {
        color: #666;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .social-proof-section {
        background: var(--light-bg);
        padding: 2rem;
        border-radius: var(--border-radius);
        text-align: center;
    }

    .social-stats {
        display: flex;
        justify-content: space-around;
        margin-bottom: 2rem;
        gap: 1rem;
    }

    .stat-card {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-red);
        display: block;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #666;
    }

    .testimonial {
        font-style: italic;
        color: #666;
    }

    .testimonial cite {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.9rem;
        color: #999;
    }

    /* Mobile responsiveness */
    @media (max-width: 968px) {
        .login-container {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .login-form-container,
        .login-benefits {
            padding: 2rem;
        }

        .form-options {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .social-stats {
            flex-direction: column;
            gap: 1rem;
        }
    }

    @media (max-width: 480px) {
        .login-form-container,
        .login-benefits {
            padding: 1.5rem;
        }

        .form-title {
            font-size: 1.5rem;
        }

        .benefit-item {
            flex-direction: column;
            text-align: center;
        }
    }
</style>