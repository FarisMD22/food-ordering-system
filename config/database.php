<?php
/**
 * FoodieDelight Database Configuration
 * ONLY connection setup - functions moved to includes/functions.php
 */
//config/database.php
// Database configuration - Update these values for your setup
$db_config = [
    'host' => '127.0.0.1',
    'dbname' => 'food_ordering',
    'username' => 'root',
    'password' => 'root',  // Change this to your MySQL password
    'charset' => 'utf8mb4'
];

// Global PDO instance
$pdo = null;

try {
    $dsn = "mysql:host={$db_config['host']};dbname={$db_config['dbname']};charset={$db_config['charset']}";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);

    // Set timezone to Malaysia
    $pdo->exec("SET time_zone = '+08:00'");

} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());

    // In production, show maintenance page
    if (!defined('SHOW_DB_ERRORS') || !SHOW_DB_ERRORS) {
        http_response_code(503);
        if (file_exists(__DIR__ . '/../maintenance.html')) {
            include __DIR__ . '/../maintenance.html';
            exit;
        } else {
            die("Database service temporarily unavailable. Please try again later.");
        }
    } else {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Make database config available globally
$GLOBALS['db_config'] = $db_config;
$GLOBALS['pdo'] = $pdo;

?>