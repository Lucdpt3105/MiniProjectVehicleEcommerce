<?php
// Load environment variables
require_once __DIR__ . '/env.php';

// Determine environment
$isProduction = (getenv('ENV') === 'production') || 
                (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] !== 'localhost');

if ($isProduction) {
    // PRODUCTION: Đọc từ server environment variables
    // Không cần file .env trên production server!
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') ?: '');
    define('DB_NAME', getenv('DB_NAME') ?: 'classicmodels');
    
    // Enable error logging for production
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php-errors.log');
    
} else {
    // DEVELOPMENT: Đọc từ file .env
    $envPath = __DIR__ . '/../.env';
    
    if (file_exists($envPath)) {
        EnvLoader::load($envPath);
    } else {
        // Fallback to default values for development
        error_log("Warning: .env file not found at $envPath");
    }
    
    define('DB_HOST', EnvLoader::get('DB_HOST', 'localhost'));
    define('DB_USER', EnvLoader::get('DB_USER', 'root'));
    define('DB_PASS', EnvLoader::get('DB_PASS', ''));
    define('DB_NAME', EnvLoader::get('DB_NAME', 'classicmodels'));
    
    // Enable error display for development
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        // Log error instead of displaying in production
        error_log("Database connection failed: " . $conn->connect_error);
        
        if (getenv('ENV') === 'production') {
            die("Service temporarily unavailable. Please try again later.");
        } else {
            die("Connection failed: " . $conn->connect_error);
        }
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Global connection
$conn = getDBConnection();
?>
