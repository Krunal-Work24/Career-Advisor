<?php
// ── Database Configuration ────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        
define('DB_PASS', '');            
define('DB_NAME', 'career_advisor');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;background:#fee2e2;color:#991b1b;padding:20px;border-radius:8px;margin:20px;">
        <strong>Database connection failed.</strong><br>
        Make sure XAMPP MySQL is running and you have imported <code>db/schema.sql</code>.<br>
        Error: ' . $conn->connect_error . '
    </div>');
}

$conn->set_charset('utf8mb4');
