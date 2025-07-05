<?php
// Fixed index.php - AJAX handler MUST be first!
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Load required files
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// CRITICAL: Handle AJAX requests BEFORE any HTML output
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');

    try {
        switch ($_GET['action']) {
            case 'add_to_cart':
                if (function_exists('handleAddToCart')) {
                    echo json_encode(handleAddToCart());
                } else {
                    echo json_encode(['success' => false, 'message' => 'Function not available']);
                }
                break;
            case 'update_cart':
                if (function_exists('handleUpdateCart')) {
                    echo json_encode(handleUpdateCart());
                } else {
                    echo json_encode(['success' => false, 'message' => 'Function not available']);
                }
                break;
            case 'get_psychology_data':
                if (function_exists('getPsychologyData')) {
                    echo json_encode(getPsychologyData());
                } else {
                    echo json_encode(['success' => false, 'message' => 'Function not available']);
                }
                break;
            case 'track_behavior':
                // Temporarily return success to stop the 502 errors
                echo json_encode(['success' => true, 'message' => 'Tracking received']);
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
    exit; // CRITICAL: Stop execution for AJAX requests
}

// Initialize psychology session if not exists
if (!isset($_SESSION['psychology_profile'])) {
    $_SESSION['psychology_profile'] = [
        'appetite_profile' => 'comfort',
        'price_sensitivity' => 'moderate',
        'session_start' => time(),
        'page_views' => 0,
        'items_viewed' => []
    ];
}

// Track page view for psychology analytics
$_SESSION['psychology_profile']['page_views']++;

// Get current page
$page = $_GET['page'] ?? 'home';
$allowed_pages = ['home', 'menu', 'cart', 'checkout', 'login', 'register', 'profile', 'admin'];

if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// Handle logout action
if ($page === 'logout') {
    session_destroy();
    header('Location: index.php?page=home');
    exit;
}

// Handle form submissions with error handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'login':
                if (function_exists('handleLogin')) {
                    $result = handleLogin($_POST);
                    if ($result['success']) {
                        header('Location: index.php?page=home');
                        exit;
                    } else {
                        $error = $result['message'];
                    }
                } else {
                    $error = 'Login function not available';
                }
                break;
            case 'register':
                if (function_exists('handleRegister')) {
                    $result = handleRegister($_POST);
                    if ($result['success']) {
                        header('Location: index.php?page=login&success=1');
                        exit;
                    } else {
                        $error = $result['message'];
                    }
                } else {
                    $error = 'Registration function not available';
                }
                break;
            case 'checkout':
                if (function_exists('handleCheckout')) {
                    $result = handleCheckout($_POST);
                    if ($result['success']) {
                        header('Location: index.php?page=home&order_success=1&order_id=' . $result['order_id']);
                        exit;
                    } else {
                        $error = $result['message'];
                    }
                } else {
                    $error = 'Checkout function not available';
                }
                break;
        }
    } catch (Exception $e) {
        $error = 'Error processing request: ' . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars((function_exists('getPageTitle') ? getPageTitle($page) : 'FoodieDelight')); ?> - FoodieDelight</title>

    <!-- Psychology-Enhanced Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍽️</text></svg>">

    <!-- Critical CSS for Appetite Stimulation -->
    <style>
        :root {
            --primary-red: #E74C3C;
            --primary-orange: #FF6B35;
            --primary-yellow: #F1C40F;
            --gradient-appetite: linear-gradient(135deg, #E74C3C 0%, #FF6B35 50%, #F1C40F 100%);
        }

        .appetite-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient-appetite);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .appetite-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .appetite-spinner {
            font-size: 4rem;
            animation: cookingRotate 2s linear infinite;
        }

        @keyframes cookingRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-message {
            color: white;
            font-size: 1.5rem;
            margin-top: 1rem;
            text-align: center;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 0.25rem;
            margin: 1rem;
        }
</style>

    <!-- External Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="psychology-optimized">

<!-- Appetite-Preserving Loader -->
<div class="appetite-loader" id="appetiteLoader">
    <div class="appetite-spinner">🍽️</div>
    <div class="loading-message">
        <div>Preparing your delicious experience...</div>
    </div>
</div>

<!-- Psychology Session Data -->
<script>
    window.psychologyData = <?php echo json_encode($_SESSION['psychology_profile']); ?>;
    window.isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    window.cartItems = <?php echo json_encode($_SESSION['cart'] ?? []); ?>;
</script>

<!-- Header with Appetite Branding -->
<header class="appetite-header" id="mainHeader">
    <nav class="container">
        <div class="nav-brand">
            <a href="index.php" class="brand-link">
                <span class="brand-icon">🍽️</span>
                <span class="brand-text">FoodieDelight</span>
            </a>
        </div>

        <div class="nav-menu" id="navMenu">
            <a href="index.php?page=home" class="nav-link <?php echo $page === 'home' ? 'active' : ''; ?>">
                <span class="nav-icon">🏠</span> Home
            </a>
            <a href="index.php?page=menu" class="nav-link <?php echo $page === 'menu' ? 'active' : ''; ?>">
                <span class="nav-icon">📋</span> Menu
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?page=profile" class="nav-link <?php echo $page === 'profile' ? 'active' : ''; ?>">
                    <span class="nav-icon">👤</span> Profile
                </a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="index.php?page=admin" class="nav-link <?php echo $page === 'admin' ? 'active' : ''; ?>">
                        <span class="nav-icon">⚙️</span> Admin
                    </a>
                <?php endif; ?>
                <a href="index.php?page=logout" class="nav-link">
                    <span class="nav-icon">🚪</span> Logout
                </a>
            <?php else: ?>
                <a href="index.php?page=login" class="nav-link <?php echo $page === 'login' ? 'active' : ''; ?>">
                    <span class="nav-icon">🔑</span> Login
                </a>
                <a href="index.php?page=register" class="nav-link <?php echo $page === 'register' ? 'active' : ''; ?>">
                    <span class="nav-icon">📝</span> Register
                </a>
            <?php endif; ?>
        </div>

        <!-- Cart Counter -->
        <div class="cart-counter-container">
            <a href="index.php?page=cart" class="cart-link">
                <span class="cart-icon">🛒</span>
                <span class="cart-count" id="cartCount"><?php echo function_exists('getCartItemCount') ? getCartItemCount() : 0; ?></span>
                <span class="cart-total" id="cartTotal">$<?php echo function_exists('getCartTotal') ? number_format(getCartTotal(), 2) : '0.00'; ?></span>
            </a>
        </div>
    </nav>
</header>

<?php if (isset($error)): ?>
    <div class="error-message">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Main Content Area -->
<main class="main-content" id="mainContent">
    <?php
    // Include the requested page with error handling
    $page_file = __DIR__ . "/pages/{$page}.php";
    if (file_exists($page_file)) {
        try {
            include $page_file;
        } catch (Exception $e) {
            echo '<div class="error-message">Error loading page: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        echo '<div class="error-message">Page not found: ' . htmlspecialchars($page) . '</div>';
    }
    ?>
</main>

<!-- Footer -->
<footer class="main-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>🍽️ FoodieDelight</h3>
                <p style="color: #1A1A1A ">Satisfying your cravings with psychology-powered food ordering experience.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Main JavaScript -->
<script src="assets/app.js"></script>

<!-- Initialize Psychology System -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide loader after DOM is ready
        setTimeout(() => {
            const loader = document.getElementById('appetiteLoader');
            if (loader) {
                loader.classList.add('hidden');
            }
        }, 1500);

        // Initialize psychology features if available (with reduced tracking)
        if (window.PsychologyEngine) {
            try {
                // Temporarily disable aggressive tracking
                window.FoodieDelight = window.FoodieDelight || {};
                window.FoodieDelight.config = window.FoodieDelight.config || {};
                window.FoodieDelight.config.behaviorTrackingEnabled = false;

                window.PsychologyEngine.initialize();
            } catch (e) {
                console.warn('Psychology Engine initialization failed:', e);
            }
        }

        console.log('✅ FoodieDelight loaded successfully');
    });
</script>
</body>
</html>