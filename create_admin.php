<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👤 Admin User Creator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d1b1b 100%);
            color: #ffffff;
            line-height: 1.6;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 2rem;
            background: rgba(231, 76, 60, 0.1);
            border-radius: 15px;
            border: 2px solid rgba(231, 76, 60, 0.3);
        }

        .header h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #e74c3c, #f39c12);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .issue-summary {
            background: rgba(231, 76, 60, 0.2);
            border: 2px solid rgba(231, 76, 60, 0.5);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .fix-section {
            background: rgba(39, 174, 96, 0.2);
            border: 2px solid rgba(39, 174, 96, 0.5);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .code-snippet {
            background: #1e1e1e;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1rem;
            margin: 0.5rem 0;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
        }

        .code-snippet pre {
            margin: 0;
            color: #f8f8f2;
        }

        .copy-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.3rem 0.8rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
            margin-left: 1rem;
            transition: background 0.3s ease;
        }

        .copy-btn:hover {
            background: #c0392b;
        }

        .step {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .step-number {
            background: #e74c3c;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-weight: bold;
        }

        .step h3 {
            color: #e74c3c;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .credentials {
            background: #2c3e50;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            font-family: 'Courier New', monospace;
        }

        .credential-item {
            margin: 0.5rem 0;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .credential-label {
            color: #f39c12;
            font-weight: bold;
        }

        .credential-value {
            color: #ecf0f1;
            margin-left: 1rem;
        }

        .test-section {
            background: rgba(52, 152, 219, 0.2);
            border: 2px solid rgba(52, 152, 219, 0.5);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
            text-align: center;
        }

        .test-btn {
            background: #3498db;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
            transition: all 0.3s ease;
        }

        .test-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .success {
            background: rgba(39, 174, 96, 0.2);
            border: 2px solid rgba(39, 174, 96, 0.5);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>👤 Admin User Creator</h1>
        <p>Fix your missing admin user issue!</p>
    </div>

    <div class="issue-summary">
        <h3>🚨 Problem Identified</h3>
        <ul style="margin-left: 2rem; margin-top: 1rem;">
            <li>❌ Admin user <code>admin@foodiedelight.com</code> not found in database</li>
            <li>❌ Cannot login because user doesn't exist</li>
            <li>❌ Admin panel inaccessible without admin user</li>
            <li>⚠️ Session headers warning (secondary issue)</li>
        </ul>
    </div>

    <!-- Quick Fix Solution -->
    <div class="fix-section">
        <h3>🚀 Quick Fix: Create Admin User</h3>
        <p>Create this file to add the missing admin user to your database:</p>

        <div class="step">
            <h3><span class="step-number">1</span>Create Admin User Script</h3>
            <p><strong>Create file:</strong> <code>create_admin.php</code></p>
            <button class="copy-btn" onclick="copyCode('create-admin')">Copy</button>
            <div class="code-snippet" id="create-admin">
                    <pre><?php
                        // create_admin.php - Fix missing admin user
                        error_reporting(E_ALL);
                        ini_set('display_errors', 1);

                        echo "<h2>👤 Creating Admin User</h2>";

                        try {
                            // Include database connection
                            require_once 'config/database.php';

                            // Check if admin already exists
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                            $stmt->execute(['admin@foodiedelight.com']);
                            $exists = $stmt->fetchColumn();

                            if ($exists > 0) {
                                echo "<p style='color: orange;'>⚠️ Admin user already exists!</p>";

                                // Show existing admin details
                                $stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE email = ?");
                                $stmt->execute(['admin@foodiedelight.com']);
                                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                                echo "<h3>Existing Admin User:</h3>";
                                echo "ID: " . $admin['id'] . "<br>";
                                echo "Name: " . $admin['name'] . "<br>";
                                echo "Email: " . $admin['email'] . "<br>";
                                echo "Role: " . $admin['role'] . "<br>";

                                if ($admin['role'] !== 'admin') {
                                    echo "<p style='color: red;'>❌ Role is wrong! Fixing...</p>";
                                    $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE email = ?");
                                    $stmt->execute(['admin@foodiedelight.com']);
                                    echo "<p style='color: green;'>✅ Role fixed to 'admin'</p>";
                                }

                            } else {
                                echo "<p style='color: blue;'>➕ Creating new admin user...</p>";

                                // Create admin user
                                $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password_hash, role, appetite_profile, price_sensitivity, created_at)
            VALUES (?, ?, ?, 'admin', 'comfort', 'moderate', NOW())
        ");

                                $stmt->execute([
                                    'Admin User',
                                    'admin@foodiedelight.com',
                                    password_hash('admin123', PASSWORD_DEFAULT)
                                ]);

                                echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
                            }

                            // Test password
                            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE email = ?");
                            $stmt->execute(['admin@foodiedelight.com']);
                            $hash = $stmt->fetchColumn();

                            if (password_verify('admin123', $hash)) {
                                echo "<p style='color: green;'>✅ Password 'admin123' verified!</p>";
                            } else {
                                echo "<p style='color: red;'>❌ Password verification failed!</p>";
                            }

                            // Show final credentials
                            echo "<div style='background: #2c3e50; color: white; padding: 1rem; border-radius: 8px; margin-top: 1rem;'>";
                            echo "<h3>🔐 Admin Credentials:</h3>";
                            echo "<strong>Email:</strong> admin@foodiedelight.com<br>";
                            echo "<strong>Password:</strong> admin123<br>";
                            echo "<strong>Role:</strong> admin<br>";
                            echo "</div>";

                            echo "<div style='background: #27ae60; color: white; padding: 1rem; border-radius: 8px; margin-top: 1rem;'>";
                            echo "<h3>🎉 Success!</h3>";
                            echo "<p>Admin user is ready. You can now:</p>";
                            echo "<ol style='margin-left: 2rem;'>";
                            echo "<li><a href='index.php?page=login' style='color: white;'>Login with admin credentials</a></li>";
                            echo "<li><a href='index.php?page=admin' style='color: white;'>Access admin panel</a></li>";
                            echo "</ol>";
                            echo "</div>";

                        } catch (Exception $e) {
                            echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                            echo "<p>Make sure your database connection is working.</p>";
                        }

                        echo "<br><p><strong>⚠️ Security:</strong> Delete this file after running it!</p>";
                        ?></pre>
            </div>
        </div>

        <div class="step">
            <h3><span class="step-number">2</span>Run the Script</h3>
            <ol style="margin-left: 2rem;">
                <li>Save the code above as <code>create_admin.php</code> in your project root</li>
                <li>Visit: <code>yoursite.com/create_admin.php</code></li>
                <li>Follow the output instructions</li>
                <li>Delete the file after running (security)</li>
            </ol>
        </div>

        <div class="step">
            <h3><span class="step-number">3</span>Test Admin Login</h3>
            <p>After creating the admin user, use these credentials:</p>

            <div class="credentials">
                <div class="credential-item">
                    <span class="credential-label">Email:</span>
                    <span class="credential-value">admin@foodiedelight.com</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Password:</span>
                    <span class="credential-value">admin123</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alternative: SQL Fix -->
    <div class="fix-section">
        <h3>🗄️ Alternative: Direct SQL Fix</h3>
        <p>If you prefer to fix it directly in phpMyAdmin:</p>

        <button class="copy-btn" onclick="copyCode('sql-fix')">Copy SQL</button>
        <div class="code-snippet" id="sql-fix">
                <pre>-- Run this in phpMyAdmin SQL tab

-- First, check if admin exists
SELECT * FROM users WHERE email = 'admin@foodiedelight.com';

-- If no results, insert admin user
INSERT INTO users (name, email, password_hash, role, appetite_profile, price_sensitivity, created_at)
VALUES (
    'Admin User',
    'admin@foodiedelight.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    'comfort',
    'moderate',
    NOW()
);

-- If admin exists but role is wrong, fix role
UPDATE users SET role = 'admin' WHERE email = 'admin@foodiedelight.com';

-- Verify admin user
SELECT id, name, email, role FROM users WHERE email = 'admin@foodiedelight.com';</pre>
        </div>
        <p><strong>Note:</strong> The password hash above is for 'admin123'</p>
    </div>

    <!-- Test Section -->
    <div class="test-section">
        <h3>🧪 Test Your Fix</h3>
        <p>After creating the admin user:</p>
        <button class="test-btn" onclick="testLogin()">🔑 Test Login</button>
        <button class="test-btn" onclick="testAdmin()">📊 Test Admin Panel</button>
        <button class="test-btn" onclick="runSessionDebug()">🔍 Check Session</button>
    </div>

    <!-- Expected Results -->
    <div class="success">
        <h3>✅ Expected Results After Fix</h3>
        <ul style="margin-left: 2rem; margin-top: 1rem;">
            <li>✅ Admin user exists in database with role 'admin'</li>
            <li>✅ Can login with admin@foodiedelight.com / admin123</li>
            <li>✅ $_SESSION['role'] gets set to 'admin' after login</li>
            <li>✅ Admin link appears in navigation menu</li>
            <li>✅ Admin panel loads with dashboard data</li>
        </ul>

        <p style="margin-top: 1rem;"><strong>If this doesn't work, we'll debug the next layer!</strong></p>
    </div>
</div>

<script>
    function copyCode(elementId) {
        const codeElement = document.querySelector(`#${elementId} pre`);
        const text = codeElement.textContent;

        navigator.clipboard.writeText(text).then(function() {
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = '✅ Copied!';
            btn.style.background = '#27ae60';

            setTimeout(() => {
                btn.textContent = originalText;
                btn.style.background = '#e74c3c';
            }, 2000);
        });
    }

    function testLogin() {
        const domain = prompt("Enter your domain (e.g., localhost/food-ordering-system):");
        if (domain) {
            window.open(`http://${domain}/index.php?page=login`, '_blank');
        }
    }

    function testAdmin() {
        const domain = prompt("Enter your domain:");
        if (domain) {
            window.open(`http://${domain}/index.php?page=admin`, '_blank');
        }
    }

    function runSessionDebug() {
        const domain = prompt("Enter your domain:");
        if (domain) {
            window.open(`http://${domain}/session_debug.php`, '_blank');
        }
    }

    console.log('👤 Admin User Creator Tool Loaded');
    console.log('🎯 Root cause: Admin user missing from database');
    console.log('🚀 Solution: Create admin user with proper role');
</script>
</body>
</html>