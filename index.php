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
        /* ===================================================================
           ENHANCED NAVIGATION - DESKTOP & MOBILE READY
           =================================================================== */

        /* Enhanced Header Base */
        .appetite-header.enhanced-header {
            background: linear-gradient(135deg, #2c3e50, #1a1a1a);
            border-bottom: 3px solid transparent;
            border-image: linear-gradient(90deg, #e74c3c, #f39c12, #27ae60, #3498db) 1;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .nav-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Enhanced Brand */
        .nav-brand.enhanced-brand .brand-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .nav-brand.enhanced-brand .brand-link:hover {
            transform: scale(1.05);
        }

        .nav-brand.enhanced-brand .brand-icon {
            font-size: 2.2rem;
            filter: drop-shadow(0 2px 4px rgba(231, 76, 60, 0.5));
            animation: brandPulse 3s ease-in-out infinite;
        }

        .nav-brand.enhanced-brand .brand-text {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #e74c3c, #f39c12);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: 'Inter', sans-serif;
        }

        @keyframes brandPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Enhanced Desktop Navigation */
        .nav-menu.enhanced-nav-menu {
            display: flex;
            align-items: center;
        }

        .nav-links.enhanced-nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link.enhanced-nav-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            color: #ffffff;
            text-decoration: none;
            border-radius: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-weight: 500;
            backdrop-filter: blur(5px);
        }

        .nav-link.enhanced-nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(231, 76, 60, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .nav-link.enhanced-nav-link:hover::before {
            left: 100%;
        }

        .nav-link.enhanced-nav-link:hover {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.2), rgba(231, 76, 60, 0.1));
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .nav-link.enhanced-nav-link.active {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
            transform: translateY(-1px);
        }

        .nav-link.enhanced-nav-link .nav-icon {
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        .nav-link.enhanced-nav-link:hover .nav-icon {
            transform: scale(1.2);
        }

        /* Enhanced Cart */
        .cart-counter-container.enhanced-cart .cart-link.enhanced-cart-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(231, 76, 60, 0.05));
            border: 2px solid rgba(231, 76, 60, 0.3);
            border-radius: 25px;
            color: #ffffff;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            backdrop-filter: blur(10px);
        }

        .cart-counter-container.enhanced-cart .cart-link.enhanced-cart-link:hover {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.2), rgba(231, 76, 60, 0.1));
            border-color: rgba(231, 76, 60, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
        }

        .cart-counter-container.enhanced-cart .cart-icon {
            font-size: 1.3rem;
            transition: transform 0.3s ease;
        }

        .cart-counter-container.enhanced-cart .cart-link:hover .cart-icon {
            transform: scale(1.1);
        }

        .cart-counter-container.enhanced-cart .cart-badge {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.4);
        }

        .cart-counter-container.enhanced-cart .cart-total {
            font-weight: 700;
            color: #e74c3c;
            font-size: 1.1rem;
        }

        /* Mobile Menu Toggle - Beautiful Hamburger */
        .mobile-menu-toggle.enhanced-toggle {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(231, 76, 60, 0.05));
            border: 2px solid rgba(231, 76, 60, 0.3);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            backdrop-filter: blur(10px);
        }

        .mobile-menu-toggle.enhanced-toggle:hover {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.2), rgba(231, 76, 60, 0.1));
            border-color: rgba(231, 76, 60, 0.5);
            transform: scale(1.05);
        }

        .mobile-menu-toggle.enhanced-toggle .toggle-line {
            width: 20px;
            height: 2px;
            background: linear-gradient(90deg, #e74c3c, #f39c12);
            margin: 2px 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .mobile-menu-toggle.enhanced-toggle.active .toggle-line:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }

        .mobile-menu-toggle.enhanced-toggle.active .toggle-line:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.enhanced-toggle.active .toggle-line:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
        }

        /* Mobile overlay */
        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .mobile-menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* ===================================================================
           MOBILE RESPONSIVE STYLES
           =================================================================== */

        @media (max-width: 768px) {
            .nav-container {
                padding: 1rem;
            }

            /* Show mobile toggle */
            .mobile-menu-toggle.enhanced-toggle {
                display: flex;
            }

            .nav-menu.enhanced-nav-menu {
                position: fixed;
                top: 0;
                right: -100%;
                width: 320px;
                height: 100vh;
                background: linear-gradient(180deg, #2c3e50, #1a1a1a);
                backdrop-filter: blur(20px);
                box-shadow: -10px 0 30px rgba(0, 0, 0, 0.5);
                transition: right 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                z-index: 1001;
                overflow-y: auto;
                /* Ensure it's hidden initially */
                transform: translateX(0);
            }

            .nav-menu.enhanced-nav-menu.active {
                right: 0;
            }

            .nav-links.enhanced-nav-links {
                flex-direction: column;
                gap: 0;
                padding: 2rem 0;
                height: 100%;
            }

            .nav-link.enhanced-nav-link {
                width: 100%;
                justify-content: flex-start;
                padding: 1.25rem 2rem;
                border-radius: 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                font-size: 1.1rem;
            }

            .nav-link.enhanced-nav-link:hover {
                background: linear-gradient(90deg, rgba(231, 76, 60, 0.2), rgba(231, 76, 60, 0.1));
                transform: translateX(10px);
                box-shadow: none;
            }

            .nav-link.enhanced-nav-link.active {
                background: linear-gradient(90deg, #e74c3c, rgba(231, 76, 60, 0.8));
                border-left: 4px solid #f39c12;
            }

            .nav-link.enhanced-nav-link .nav-icon {
                font-size: 1.3rem;
                margin-right: 0.5rem;
            }

            /* Mobile Cart */
            .cart-counter-container.enhanced-cart {
                position: relative;
            }

            .cart-counter-container.enhanced-cart .cart-total {
                display: none;
            }

            .cart-counter-container.enhanced-cart .cart-link.enhanced-cart-link {
                padding: 0.75rem;
                gap: 0.5rem;
            }

            .mobile-menu-overlay {
                display: block;
            }

            /* Brand text smaller on mobile */
            .nav-brand.enhanced-brand .brand-text {
                font-size: 1.3rem;
            }

            .nav-brand.enhanced-brand .brand-icon {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .nav-container {
                padding: 0.75rem;
            }

            .nav-menu.enhanced-nav-menu {
                width: 280px;
            }

            .nav-link.enhanced-nav-link {
                padding: 1rem 1.5rem;
                font-size: 1rem;
            }

            .nav-brand.enhanced-brand .brand-text {
                font-size: 1.2rem;
            }
        }
        /* ===================================================================
           ENHANCED FOOTER - BEAUTIFUL & PROFESSIONAL
           =================================================================== */
        body {
            display: grid;
            grid-template-rows: auto 1fr auto;
            min-height: 100vh;
            margin: 0;
        }

        .appetite-header {
            grid-row: 1;
        }

        .main-content {
            grid-row: 2;
        }

        .footer-bottom {
            grid-row: 3;
            margin-top: auto;
        }
        .main-footer.enhanced-footer {
            background: linear-gradient(135deg, #1a1a1a 0%, #2c3e50 50%, #1a1a1a 100%);
            color: #ffffff;
            position: relative;
            overflow: hidden;
            margin-top: 4rem;
            border-top: 3px solid transparent;
            border-image: linear-gradient(90deg, #e74c3c, #f39c12, #27ae60, #3498db) 1;
        }

        .enhanced-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(231, 76, 60, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(243, 156, 18, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 80%, rgba(39, 174, 96, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .enhanced-footer-content {
            position: relative;
            z-index: 2;
            padding: 4rem 0 0 0;
            display: flex;
            flex-direction: column;
            width: 100%;
            min-height: 400px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .footer-main {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 6rem;
            flex: 1;
            width: 100%;
            align-items: start;
            margin-bottom: auto;
        }
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding: 2rem 0;
            margin-top: 3rem;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
        }

        .footer-bottom-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
        }
        .footer-badges {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .footer-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.8rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            color: #d0d0d0;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .footer-badge:hover {
            background: rgba(231, 76, 60, 0.15);
            border-color: rgba(231, 76, 60, 0.3);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .footer-badge-icon {
            font-size: 1rem;
        }
        /* Footer Brand Section - More compact */
        .footer-brand {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            max-width: 400px; /* Constrain width */
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .footer-icon {
            font-size: 2.5rem;
            filter: drop-shadow(0 4px 8px rgba(231, 76, 60, 0.5));
            animation: footerIconPulse 4s ease-in-out infinite;
        }

        @keyframes footerIconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .footer-title {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #e74c3c, #f39c12);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        .footer-description {
            color: #d0d0d0;
            line-height: 1.6;
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        /* Footer Stats - Horizontal layout */
        .footer-stats {
            display: flex;
            flex-direction: column; /* Stack vertically */
            gap: 1rem;
            align-items: stretch; /* Full width cards */
            max-width: 350px; /* Constrain width */
        }


        .footer-stat {
            width: 100%;
            padding: 1.2rem 1.5rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            backdrop-filter: blur(15px);
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 65px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .footer-stat:hover {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.15), rgba(231, 76, 60, 0.08));
            border-color: rgba(231, 76, 60, 0.4);
            transform: translateX(3px);
            box-shadow: 0 4px 20px rgba(231, 76, 60, 0.25);
        }


        .footer-stat .stat-number {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-align: center;
        }

        .footer-stat .stat-number .number-part {
            font-size: 1.8rem;
            font-weight: 900;
            color: #e74c3c;
            text-shadow: 0 1px 3px rgba(231, 76, 60, 0.3);
        }

        .footer-stat .stat-number .text-part {
            font-size: 1rem;
            color: #f0f0f0;
            font-weight: 600;
        }
        /* Footer Links Section */
        .footer-links {
            display: grid;
            grid-template-columns: 1fr 1fr 1.2fr; /* Slightly more space for Contact Info */
            gap: 3rem;
        }

        .footer-column {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .footer-heading {
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffffff;
            margin: 0 0 1rem 0;
            position: relative;
            padding-bottom: 0.5rem;
            white-space: nowrap; /* Keep headings on one line */
        }

        .footer-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 30px;
            height: 2px;
            background: linear-gradient(90deg, #e74c3c, #f39c12);
            border-radius: 2px;
        }

        .footer-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .footer-link {
            color: #d0d0d0;
            text-decoration: none;
            transition: all 0.3s ease;
            padding: 0.4rem 0;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .footer-link:hover {
            color: #e74c3c;
            transform: translateX(5px);
            background: rgba(231, 76, 60, 0.1);
            padding-left: 0.5rem;
        }

        .footer-contact {
            color: #d0d0d0;
            padding: 0.4rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            line-height: 1.4;
        }




        .footer-copyright {
            color: #b0b0b0;
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.4;
        }


        .footer-feature:hover {
            background: rgba(231, 76, 60, 0.1);
            border-color: rgba(231, 76, 60, 0.3);
            color: #ffffff;
            transform: translateY(-2px);
        }

        /* Background Elements */
        .footer-bg-elements {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 1;
        }

        .footer-bg-circle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(231, 76, 60, 0.1) 0%, transparent 70%);
            animation: floatCircle 8s ease-in-out infinite;
        }

        .footer-bg-circle-1 {
            width: 200px;
            height: 200px;
            top: 20%;
            right: 10%;
            animation-delay: 0s;
        }

        .footer-bg-circle-2 {
            width: 150px;
            height: 150px;
            bottom: 30%;
            left: 15%;
            animation-delay: 2s;
            background: radial-gradient(circle, rgba(243, 156, 18, 0.1) 0%, transparent 70%);
        }

        .footer-bg-circle-3 {
            width: 100px;
            height: 100px;
            top: 60%;
            right: 30%;
            animation-delay: 4s;
            background: radial-gradient(circle, rgba(39, 174, 96, 0.1) 0%, transparent 70%);
        }

        @keyframes floatCircle {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* ===================================================================
           RESPONSIVE FOOTER
           =================================================================== */

        @media (max-width: 1024px) {
            .enhanced-footer-content {
                min-height: 350px;
            }

            .footer-main {
                grid-template-columns: 1fr 1.5fr;
                gap: 4rem;
            }


            .footer-links {
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }

            .footer-links .footer-column:nth-child(3) {
                grid-column: 1 / -1;
                margin-top: 1rem;
            }
            .footer-stats {
                justify-content: center;
            }

            .footer-stat {
                min-width: 80px;
                padding: 0.75rem;
            }

            .footer-bg-circle {
                display: none;
            }
        }
        @media (max-width: 768px) {
            .enhanced-footer-content {
                min-height: 300px;
            }
            .footer-bottom-wrapper {
                flex-direction: column;
                text-align: center;
                gap: 1.5rem;
            }
            .footer-badges {
                     justify-content: center;
                     gap: 1rem;
                 }
            .footer-badge {
                font-size: 0.8rem;
                padding: 0.35rem 0.7rem;
            }
            .footer-main {
                grid-template-columns: 1fr;
                gap: 3rem;
                text-align: center;
            }

            .footer-links {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .footer-links .footer-column:nth-child(3) {
                grid-column: 1;
                margin-top: 0;
            }

            .footer-stats {
                justify-content: center;
            }

        }
        @media (max-width: 480px) {
            .enhanced-footer-content {
                padding: 2rem 0 0 0;
                min-height: 250px;
            }
            .footer-bottom {
                padding: 1.5rem 0;
            }
            .footer-bottom-wrapper {
                padding: 0 1rem;
                gap: 1rem;
            }
            .footer-badges {
                flex-direction: column;
                width: 100%;
                gap: 0.75rem;
            }
            .footer-badge {
                justify-content: center;
                width: 100%;
                max-width: 200px;
            }
            .footer-logo {
                justify-content: center;
            }
            .footer-main {
                gap: 2rem;
            }


            .footer-title {
                font-size: 1.7rem;
            }

            .footer-icon {
                font-size: 2rem;
            }

            .footer-stats {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }

            .footer-stat {
                width: 100%;
                max-width: 200px;
            }

            .footer-feature {
                width: fit-content;
            }
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

<!-- Enhanced Header with Appetite Branding -->
<header class="appetite-header enhanced-header" id="mainHeader">
    <nav class="container nav-container">
        <!-- Brand Section -->
        <div class="nav-brand enhanced-brand">
            <a href="index.php" class="brand-link">
                <span class="brand-icon">🍽️</span>
                <span class="brand-text">FoodieDelight</span>
            </a>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle enhanced-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
            <span class="toggle-line"></span>
            <span class="toggle-line"></span>
            <span class="toggle-line"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="nav-menu enhanced-nav-menu" id="navMenu">
            <div class="nav-links enhanced-nav-links">
                <a href="index.php?page=home" class="nav-link enhanced-nav-link <?php echo $page === 'home' ? 'active' : ''; ?>">
                    <span class="nav-icon">🏠</span>
                    <span class="nav-text">Home</span>
                </a>
                <a href="index.php?page=menu" class="nav-link enhanced-nav-link <?php echo $page === 'menu' ? 'active' : ''; ?>">
                    <span class="nav-icon">📋</span>
                    <span class="nav-text">Menu</span>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="index.php?page=profile" class="nav-link enhanced-nav-link <?php echo $page === 'profile' ? 'active' : ''; ?>">
                        <span class="nav-icon">👤</span>
                        <span class="nav-text">Profile</span>
                    </a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="index.php?page=admin" class="nav-link enhanced-nav-link <?php echo $page === 'admin' ? 'active' : ''; ?>">
                            <span class="nav-icon">⚙️</span>
                            <span class="nav-text">Admin</span>
                        </a>
                    <?php endif; ?>
                    <a href="index.php?page=logout" class="nav-link enhanced-nav-link">
                        <span class="nav-icon">🚪</span>
                        <span class="nav-text">Logout</span>
                    </a>
                <?php else: ?>
                    <a href="index.php?page=login" class="nav-link enhanced-nav-link <?php echo $page === 'login' ? 'active' : ''; ?>">
                        <span class="nav-icon">🔑</span>
                        <span class="nav-text">Login</span>
                    </a>
                    <a href="index.php?page=register" class="nav-link enhanced-nav-link <?php echo $page === 'register' ? 'active' : ''; ?>">
                        <span class="nav-icon">📝</span>
                        <span class="nav-text">Register</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Enhanced Cart Counter -->
        <div class="cart-counter-container enhanced-cart">
            <a href="index.php?page=cart" class="cart-link enhanced-cart-link">
                <span class="cart-icon">🛒</span>
                <span class="cart-badge">
                    <span class="cart-count" id="cartCount"><?php echo function_exists('getCartItemCount') ? getCartItemCount() : 0; ?></span>
                </span>
                <span class="cart-total" id="cartTotal">$<?php echo function_exists('getCartTotal') ? number_format(getCartTotal(), 2) : '0.00'; ?></span>
            </a>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
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

<footer class="main-footer enhanced-footer">
    <div class="container">
        <div class="footer-content enhanced-footer-content">
            <!-- Main Footer Section -->
            <div class="footer-main">
                <div class="footer-brand">
                    <div class="footer-logo">
                        <span class="footer-icon">🍽️</span>
                        <h3 class="footer-title">FoodieDelight</h3>
                    </div>
                    <p class="footer-description">
                        Satisfying your cravings with our psychology-powered food ordering experience.
                        Fresh ingredients, expert preparation, and delivery that brings joy to your door.
                    </p>
                    <div class="footer-stats">
                        <div class="footer-stat">
                                <span class="stat-number">
                                    <span class="number-part">4.9</span> <span style="color: white; font-size: 1.5rem;" class="text-part">⭐ Rating</span>
                                </span>
                                                </div>
                                                <div class="footer-stat">
                                <span class="stat-number">
                                    <span class="number-part">15-30</span> <span style="color: white; font-size: 1.5rem;"  class="text-part">Min Delivery</span>
                                </span>
                                                </div>
                                                <div class="footer-stat">
                                <span class="stat-number">
                                    <span class="number-part">1000+</span> <span style="color: white; font-size: 1.5rem;" class="text-part">Happy Customers</span>
                                </span>
                        </div>
                    </div>
                </div>

                <div class="footer-links">
                    <div class="footer-column">
                        <h4 class="footer-heading">Quick Links</h4>
                        <ul class="footer-list">
                            <li><a href="index.php?page=home" class="footer-link">🏠 Home</a></li>
                            <li><a href="index.php?page=menu" class="footer-link">📋 Menu</a></li>
                            <li><a href="index.php?page=cart" class="footer-link">🛒 Cart</a></li>
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <li><a href="index.php?page=register" class="footer-link">📝 Sign Up</a></li>
                            <?php else: ?>
                                <li><a href="index.php?page=profile" class="footer-link">👤 Profile</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="footer-column">
                        <h4 class="footer-heading">Categories</h4>
                        <ul class="footer-list">
                            <li><a href="index.php?page=menu&category=meals" class="footer-link">🍽️ Meals</a></li>
                            <li><a href="index.php?page=menu&category=drinks" class="footer-link">🥤 Drinks</a></li>
                            <li><a href="index.php?page=menu&category=desserts" class="footer-link">🍰 Desserts</a></li>
                            <li><a href="index.php?page=menu&category=specials" class="footer-link">⭐ Specials</a></li>
                        </ul>
                    </div>

                    <div class="footer-column">
                        <h4 class="footer-heading">Contact Info</h4>
                        <ul class="footer-list">
                            <li class="footer-contact">📍 123 Food Street, Flavor City</li>
                            <li class="footer-contact">📞 (555) 123-FOOD</li>
                            <li class="footer-contact">📧 hello@foodiedelight.com</li>
                            <li class="footer-contact">🕒 Daily 9AM - 11PM</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-bottom-wrapper">
                    <p class="footer-copyright">
                        © <?php echo date('Y'); ?> FoodieDelight. Made with ❤️ for food lovers everywhere.
                    </p>
                    <div class="footer-badges">
            <span class="footer-badge">
                <span class="footer-badge-icon">⚡</span>
                Fast Delivery
            </span>
                        <span class="footer-badge">
                <span class="footer-badge-icon">🥘</span>
                Fresh Ingredients
            </span>
                        <span class="footer-badge">
                <span class="footer-badge-icon">🧠</span>
                Psychology-Powered
            </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Background Elements -->
    <div class="footer-bg-elements">
        <div class="footer-bg-circle footer-bg-circle-1"></div>
        <div class="footer-bg-circle footer-bg-circle-2"></div>
        <div class="footer-bg-circle footer-bg-circle-3"></div>
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
    // Mobile menu toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu functionality
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const navMenu = document.getElementById('navMenu');
        const overlay = document.getElementById('mobileMenuOverlay');

        console.log('Mobile toggle elements:', { mobileToggle, navMenu, overlay }); // Debug

        if (mobileToggle && navMenu) {
            // Toggle mobile menu
            mobileToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Mobile toggle clicked'); // Debug

                const isActive = navMenu.classList.contains('active');
                console.log('Menu is active:', isActive); // Debug

                if (isActive) {
                    // Close menu
                    navMenu.classList.remove('active');
                    mobileToggle.classList.remove('active');
                    if (overlay) overlay.classList.remove('active');
                    document.body.style.overflow = '';
                    console.log('Menu closed'); // Debug
                } else {
                    // Open menu
                    navMenu.classList.add('active');
                    mobileToggle.classList.add('active');
                    if (overlay) overlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                    console.log('Menu opened'); // Debug
                }
            });

            // Close menu when clicking overlay
            if (overlay) {
                overlay.addEventListener('click', function() {
                    navMenu.classList.remove('active');
                    mobileToggle.classList.remove('active');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }

            // Close menu when clicking nav links
            const navLinks = navMenu.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    navMenu.classList.remove('active');
                    mobileToggle.classList.remove('active');
                    if (overlay) overlay.classList.remove('active');
                    document.body.style.overflow = '';
                });
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    mobileToggle.classList.remove('active');
                    if (overlay) overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });

            console.log('Mobile menu initialized successfully'); // Debug
        } else {
            console.error('Mobile menu elements not found:', { mobileToggle, navMenu, overlay }); // Debug
        }
    });
</script>
</body>
</html>