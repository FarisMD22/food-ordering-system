// File: maintenance.php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodieDelight - Under Maintenance</title>
    <style>
        :root {
            --primary-red: #E74C3C;
            --primary-orange: #FF6B35;
            --primary-yellow: #F1C40F;
            --gradient-appetite: linear-gradient(135deg, #E74C3C 0%, #FF6B35 50%, #F1C40F 100%);
            --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-appetite: 'Playfair Display', Georgia, serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-primary);
            background: var(--gradient-appetite);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .maintenance-container {
            text-align: center;
            max-width: 600px;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            margin: 2rem;
        }

        .maintenance-icon {
            font-size: 5rem;
            margin-bottom: 2rem;
            animation: cookingRotate 3s linear infinite;
        }

        .maintenance-title {
            font-family: var(--font-appetite);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .maintenance-message {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            line-height: 1.6;
            opacity: 0.95;
        }

        .maintenance-details {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
            margin: 2rem 0;
            backdrop-filter: blur(5px);
        }

        .maintenance-details h3 {
            margin-bottom: 1rem;
            color: var(--primary-yellow);
        }

        .maintenance-list {
            list-style: none;
            text-align: left;
        }

        .maintenance-list li {
            margin: 0.75rem 0;
            padding-left: 2rem;
            position: relative;
        }

        .maintenance-list li::before {
            content: '🔧';
            position: absolute;
            left: 0;
            top: 0;
        }

        .contact-info {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }

        .contact-info h4 {
            margin-bottom: 1rem;
            color: var(--primary-yellow);
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-link {
            display: inline-block;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            text-decoration: none;
            color: white;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
        }

        .estimated-time {
            background: rgba(241, 196, 15, 0.2);
            padding: 1rem;
            border-radius: 15px;
            margin: 1.5rem 0;
            border: 2px solid var(--primary-yellow);
        }

        .time-display {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-yellow);
            margin-bottom: 0.5rem;
        }

        @keyframes cookingRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .maintenance-icon {
            animation: cookingRotate 3s linear infinite;
        }

        .time-display {
            animation: pulse 2s ease-in-out infinite;
        }

        @media (max-width: 768px) {
            .maintenance-container {
                padding: 2rem;
                margin: 1rem;
            }

            .maintenance-title {
                font-size: 2rem;
            }

            .maintenance-message {
                font-size: 1rem;
            }

            .social-links {
                flex-wrap: wrap;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
</head>
<body>
<div class="maintenance-container">
    <div class="maintenance-icon">👨‍🍳</div>

    <h1 class="maintenance-title">We're Cooking Up Something Special!</h1>

    <p class="maintenance-message">
        Our kitchen is temporarily closed while we prepare an even better dining experience for you.
        We're adding fresh ingredients to our system and making everything more delicious!
    </p>

    <div class="estimated-time">
        <div class="time-display">🕐 Estimated Time: 30 minutes</div>
        <p>We'll be back serving you soon!</p>
    </div>

    <div class="maintenance-details">
        <h3>🔧 What we're working on:</h3>
        <ul class="maintenance-list">
            <li>Enhancing our psychology-powered recommendation engine</li>
            <li>Optimizing database performance for faster ordering</li>
            <li>Adding new appetite-stimulating features</li>
            <li>Improving mobile ordering experience</li>
            <li>Updating our behavioral analytics system</li>
        </ul>
    </div>

    <div class="contact-info">
        <h4>📞 Need immediate assistance?</h4>
        <p>📧 Email: support@foodiedelight.com</p>
        <p>📱 Phone: +60 12-345-6789</p>
        <p>🕐 Emergency support: 24/7</p>

        <div class="social-links">
            <a href="#" class="social-link" title="Facebook">📘</a>
            <a href="#" class="social-link" title="Twitter">🐦</a>
            <a href="#" class="social-link" title="Instagram">📷</a>
            <a href="#" class="social-link" title="WhatsApp">💬</a>
        </div>
    </div>

    <div style="margin-top: 2rem; font-size: 0.9rem; opacity: 0.8;">
        <p>🍽️ Thank you for your patience. Your cravings are our priority!</p>
        <p style="margin-top: 0.5rem;">
            <strong>FoodieDelight Team</strong> -
            <em>Powered by Psychology & Technology</em>
        </p>
    </div>
</div>

<script>
    // Auto-refresh page every 5 minutes to check if maintenance is over
    setTimeout(function() {
        window.location.reload();
    }, 300000); // 5 minutes

    // Simple countdown if needed
    function updateEstimatedTime() {
        // This could be dynamic based on server response
        const now = new Date();
        const minutes = now.getMinutes();
        const estimatedEnd = 30 - (minutes % 30);

        if (estimatedEnd > 0) {
            document.querySelector('.time-display').innerHTML =
                `🕐 Estimated Time: ${estimatedEnd} minutes`;
        }
    }

    // Update time every minute
    setInterval(updateEstimatedTime, 60000);

    // Track maintenance page views (if analytics service is available)
    if (typeof gtag !== 'undefined') {
        gtag('event', 'maintenance_page_view', {
            'event_category': 'system',
            'event_label': 'maintenance_mode',
            'value': 1
        });
    }

    console.log('🍽️ FoodieDelight is under maintenance');
    console.log('Expected completion: Soon!');
    console.log('Follow us for updates: @FoodieDelight');
</script>
</body>
</html>