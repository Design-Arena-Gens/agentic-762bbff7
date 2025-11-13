<?php
// AIPS configuration and database connection (MySQLi procedural)

// Helper to read environment variables with default fallback
function aips_env($key, $default = '') {
    $value = getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }
    return $value;
}

// Site settings
$AIPS_SETTINGS = [
    'site_name' => 'All In Packaging Solution (AIPS)',
    'brand_slogan' => 'Safety & Clean',
    'brand_green' => '#00793B',
    'brand_blue'  => '#1A237E',
    'company_email' => aips_env('AIPS_EMAIL', 'info@aips.example'),
    'company_phone' => aips_env('AIPS_PHONE', '+1 (000) 000-0000'),
    'company_address' => aips_env('AIPS_ADDRESS', '123 Eco Street, Clean City, Earth'),
];

// Database configuration via environment variables (for Vercel/production)
$DB_CONFIG = [
    'host' => aips_env('DB_HOST', '127.0.0.1'),
    'user' => aips_env('DB_USER', 'root'),
    'pass' => aips_env('DB_PASS', ''),
    'name' => aips_env('DB_NAME', 'aips'),
    'port' => (int) aips_env('DB_PORT', '3306'),
    'charset' => 'utf8mb4'
];

// Flutterwave
$FLW_SECRET_KEY = aips_env('FLUTTERWAVE_SECRET_KEY', '');
$FLW_PUBLIC_KEY = aips_env('FLUTTERWAVE_PUBLIC_KEY', '');
$FLW_REDIRECT_URL = aips_env('FLUTTERWAVE_REDIRECT_URL', ''); // fallback set in checkout

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function db_connect() {
    global $DB_CONFIG;
    static $conn = null;
    if ($conn) {
        return $conn;
    }
    $conn = mysqli_init();
    mysqli_options($conn, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
    if (!mysqli_real_connect($conn, $DB_CONFIG['host'], $DB_CONFIG['user'], $DB_CONFIG['pass'], $DB_CONFIG['name'], $DB_CONFIG['port'])) {
        die('Database connection failed: ' . mysqli_connect_error());
    }
    mysqli_set_charset($conn, $DB_CONFIG['charset']);
    return $conn;
}

function safe($conn, $str) {
    return mysqli_real_escape_string($conn, $str);
}

function money_format_aips($amount) {
    return '$' . number_format((float)$amount, 2);
}

function site_url($path = '') {
    // Attempt to derive base URL
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
    $base = $base === '' ? '/' : $base . '/';
    $path = ltrim($path, '/');
    return $scheme . '://' . $host . '/' . $path;
}

function get_logo_path() {
    // Prefer uploaded PNG if present; else fallback to bundled SVG
    if (file_exists(__DIR__ . '/assets/logo.png')) {
        return 'assets/logo.png';
    }
    return 'assets/logo.svg';
}
