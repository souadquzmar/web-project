<?php
// ============================================================
//  config/db.php  —  Database connection (mysqli)
//  Edit the four constants below to match your XAMPP setup.
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // your MySQL username
define('DB_PASS', '');           // your MySQL password (blank for default XAMPP)
define('DB_NAME', '');

/**
 * Returns a mysqli connection.
 * Terminates with a plain error if connection fails.
 */
function get_db(): mysqli
{
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_errno) {
            die('Database connection failed: ' . $conn->connect_error);
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}
