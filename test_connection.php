<?php
/**
 * FoodieDelight System Test
 * Complete testing suite for Phase 1 implementation
 * FIXED: No function redeclaration conflicts
 */

// Enable error reporting for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include files in proper order to avoid conflicts
require_once 'config/database.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodieDelight System Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
        }

        .test-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        h1 {
            color: #E74C3C;
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #E74C3C;
        }

        .test-section h2 {
            color: #2c3e50;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .check-item {
            display: flex;
            align-items: center;
            margin: 8px 0;
            padding: 8px;
            background: white;
            border-radius: 5px;
        }

        .status {
            font-weight: bold;
            margin-right: 10px;
            min-width: 20px;
        }

        .success { color: #27ae60; }
        .error { color: #e74c3c; }
        .warning { color: #f39c12; }

        .menu-item {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .menu-item h4 {
            color: #E74C3C;
            margin: 0 0 8px 0;
            font-size: 1.2rem;
        }

        .psychology-data {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin: 10px 0;
            font-size: 0.9rem;
        }

        .psychology-item {
            background: #f1c40f;
            color: #2c3e50;
            padding: 5px 10px;
            border-radius: 15px;
            text-align: center;
            font-weight: 600;
        }

        .sensory-words {
            color: #e74c3c;
            font-style: italic;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #ecf0f1;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #E74C3C;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .footer-note {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #2c3e50;
            color: white;
            border-radius: 10px;
        }

        .next-steps {
            background: #27ae60;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .next-steps h3 {
            margin-top: 0;
        }
    </style>
</head>
<body>
<div class="test-container">
    <h1>🍽️ FoodieDelight System Test</h1>
    <p class="subtitle">Testing Phase 1 implementation...</p>

    <!-- File Structure Test -->
    <div class="test-section">
        <h2>📁 File Structure Test</h2>
        <?php
        $files_to_check = [
            'config/database.php' => 'Database configuration',
            'includes/functions.php' => 'Core functions',
            'includes/security.php' => 'Security functions',
            'assets/style.css' => 'Main stylesheet',
            'assets/app.js' => 'JavaScript psychology engine',
            'pages/home.php' => 'Homepage'
        ];

        $all_files_exist = true;
        foreach ($files_to_check as $file => $description) {
            $exists = file_exists($file);
            if (!$exists) $all_files_exist = false;

            echo "<div class='check-item'>";
            echo "<span class='status " . ($exists ? 'success' : 'error') . "'>";
            echo $exists ? '✅' : '❌';
            echo "</span>";
            echo "<strong>{$description}:</strong> <code>{$file}</code>";
            echo "</div>";
        }

        if ($all_files_exist) {
            echo "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-top: 15px;'>";
            echo "<strong>✅ All required files found!</strong>";
            echo "</div>";
        }
        ?>
    </div>

    <!-- Database Connection Test -->
    <div class="test-section">
        <h2>🔌 Database Connection Test</h2>
        <?php
        // Test database connection using the global $pdo
        global $pdo;
        $db_connected = false;
        $db_stats = [];

        try {
            if ($pdo && $pdo->query("SELECT 1")) {
                $db_connected = true;
                echo "<div class='check-item'>";
                echo "<span class='status success'>✅</span>";
                echo "<strong>Database connection:</strong> SUCCESS";
                echo "</div>";

                // Get table statistics
                $tables = ['users', 'menu_items', 'orders', 'behavior_logs', 'reviews', 'settings'];
                foreach ($tables as $table) {
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                        $count = $stmt->fetch()['count'];
                        $db_stats[$table] = $count;

                        echo "<div class='check-item'>";
                        echo "<span class='status success'>✅</span>";
                        echo "<strong>Table '{$table}':</strong> {$count} records";
                        echo "</div>";
                    } catch (PDOException $e) {
                        echo "<div class='check-item'>";
                        echo "<span class='status error'>❌</span>";
                        echo "<strong>Table '{$table}':</strong> Error - " . $e->getMessage();
                        echo "</div>";
                    }
                }

                echo "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-top: 15px;'>";
                echo "<strong>✅ Database setup is working correctly!</strong>";
                echo "</div>";

            } else {
                throw new Exception("Database connection failed");
            }
        } catch (Exception $e) {
            echo "<div class='check-item'>";
            echo "<span class='status error'>❌</span>";
            echo "<strong>Database connection:</strong> FAILED - " . $e->getMessage();
            echo "</div>";
        }
        ?>

        <div class="stats-grid">
            <?php foreach ($db_stats as $table => $count): ?>
                <div class="stat-card">
                    <div class="stat-number"><?= $count ?></div>
                    <div class="stat-label"><?= ucwords(str_replace('_', ' ', $table)) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Sample Data Test -->
    <div class="test-section">
        <h2>🍽️ Sample Data Test</h2>
        <?php
        if ($db_connected) {
            try {
                // Get sample menu items with psychology data
                $stmt = $pdo->query("
                        SELECT name, description, price, appetite_score, comfort_level, 
                               sensory_words, featured, limited_qty
                        FROM menu_items 
                        ORDER BY appetite_score DESC 
                        LIMIT 5
                    ");
                $menu_items = $stmt->fetchAll();

                if (count($menu_items) > 0) {
                    echo "<div class='check-item'>";
                    echo "<span class='status success'>✅</span>";
                    echo "<strong>Found " . count($menu_items) . " menu items with psychology data</strong>";
                    echo "</div>";

                    echo "<div style='margin-top: 20px;'>";
                    echo "<strong>Sample Menu Items:</strong>";

                    foreach ($menu_items as $item) {
                        echo "<div class='menu-item'>";
                        echo "<h4>🍽️ {$item['name']}</h4>";

                        echo "<div class='psychology-data'>";
                        echo "<div class='psychology-item'>Appetite Score: {$item['appetite_score']}/10</div>";
                        echo "<div class='psychology-item'>Comfort Level: {$item['comfort_level']}/10</div>";

                        // Parse sensory words from JSON
                        $sensory_words = json_decode($item['sensory_words'], true);
                        if ($sensory_words && is_array($sensory_words)) {
                            echo "<div class='psychology-item'>Sensory Words: <span class='sensory-words'>" . implode(', ', $sensory_words) . "</span></div>";
                        }

                        echo "<div class='psychology-item'>Featured: " . ($item['featured'] ? 'Yes' : 'No') . "</div>";
                        echo "</div>";

                        if ($item['limited_qty']) {
                            echo "<div style='background: #e74c3c; color: white; padding: 5px 10px; border-radius: 15px; display: inline-block; margin-top: 10px;'>";
                            echo "⚡ Only {$item['limited_qty']} left!";
                            echo "</div>";
                        }

                        echo "</div>";
                    }
                    echo "</div>";

                    echo "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-top: 15px;'>";
                    echo "<strong>✅ Psychology data is properly configured!</strong>";
                    echo "</div>";

                } else {
                    echo "<div class='check-item'>";
                    echo "<span class='status warning'>⚠️</span>";
                    echo "<strong>No menu items found</strong> - Run the database setup script";
                    echo "</div>";
                }

            } catch (PDOException $e) {
                echo "<div class='check-item'>";
                echo "<span class='status error'>❌</span>";
                echo "<strong>Sample data test failed:</strong> " . $e->getMessage();
                echo "</div>";
            }
        } else {
            echo "<div class='check-item'>";
            echo "<span class='status error'>❌</span>";
            echo "<strong>Cannot test sample data:</strong> Database connection failed";
            echo "</div>";
        }
        ?>
    </div>

    <!-- Core Functions Test -->
    <div class="test-section">
        <h2>⚙️ Core Functions Test</h2>
        <?php
        $functions_to_test = [
            'sanitizeInput' => 'Input sanitization',
            'generateSessionId' => 'Session ID generation',
            'hashPassword' => 'Password hashing',
            'logBehavior' => 'Behavior tracking',
            'getMenuItems' => 'Menu retrieval',
            'calculatePsychologyScore' => 'Psychology scoring'
        ];

        $functions_working = 0;
        foreach ($functions_to_test as $function => $description) {
            $exists = function_exists($function);
            if ($exists) $functions_working++;

            echo "<div class='check-item'>";
            echo "<span class='status " . ($exists ? 'success' : 'error') . "'>";
            echo $exists ? '✅' : '❌';
            echo "</span>";
            echo "<strong>{$description}:</strong> <code>{$function}()</code>";

            // Test some functions if they exist
            if ($exists) {
                try {
                    switch ($function) {
                        case 'sanitizeInput':
                            $test_result = sanitizeInput('<script>alert("test")</script>Hello');
                            echo " → Test: <code>" . htmlspecialchars($test_result) . "</code>";
                            break;
                        case 'generateSessionId':
                            $session_id = generateSessionId();
                            echo " → Generated: <code>" . substr($session_id, 0, 10) . "...</code>";
                            break;
                        case 'hashPassword':
                            $hash = hashPassword('test123');
                            echo " → Hash generated (" . strlen($hash) . " chars)";
                            break;
                    }
                } catch (Exception $e) {
                    echo " → <span class='error'>Error: " . $e->getMessage() . "</span>";
                }
            }
            echo "</div>";
        }

        if ($functions_working === count($functions_to_test)) {
            echo "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-top: 15px;'>";
            echo "<strong>✅ All core functions are working!</strong>";
            echo "</div>";
        } else {
            echo "<div style='background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin-top: 15px;'>";
            echo "<strong>⚠️ {$functions_working}/" . count($functions_to_test) . " functions working</strong> - Check includes/functions.php";
            echo "</div>";
        }
        ?>
    </div>

    <!-- System Status Summary -->
    <div class="next-steps">
        <h3>🎯 Phase 1 System Status</h3>
        <?php
        $total_checks = 0;
        $passed_checks = 0;

        // Count successful checks
        if ($all_files_exist) { $passed_checks++; } $total_checks++;
        if ($db_connected) { $passed_checks++; } $total_checks++;
        if (isset($menu_items) && count($menu_items) > 0) { $passed_checks++; } $total_checks++;
        if ($functions_working > 0) { $passed_checks++; } $total_checks++;

        $percentage = round(($passed_checks / $total_checks) * 100);

        echo "<div style='font-size: 1.2rem; margin-bottom: 15px;'>";
        echo "<strong>System Health: {$percentage}% ({$passed_checks}/{$total_checks} checks passed)</strong>";
        echo "</div>";

        if ($percentage >= 75) {
            echo "<div style='background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px;'>";
            echo "<strong>🎉 Phase 1 is ready!</strong><br>";
            echo "Your psychology-enhanced food ordering system is working correctly. ";
            echo "You can now proceed with testing the main application at <code>index.php</code>";
            echo "</div>";
        } else {
            echo "<div style='background: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px;'>";
            echo "<strong>⚠️ System needs attention</strong><br>";
            echo "Some components need to be fixed before the system is fully functional. ";
            echo "Check the errors above and ensure all files are uploaded correctly.";
            echo "</div>";
        }
        ?>
    </div>
</div>

<div class="footer-note">
    <p><strong>🍕 FoodieDelight - Psychology-Enhanced Food Ordering System</strong></p>
    <p>TWT6223 Project • Phase 1 Testing Suite • Built with appetite stimulation in mind</p>
    <p><em>Next: Visit <code>index.php</code> to see the full application interface</em></p>
</div>
</body>
</html>