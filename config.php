<?php
// ================================================================
// config.php — Database connection + shared utilities
// ================================================================

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);


// ── DB Settings (adjust port if needed — XAMPP default is 3306) ──
$conn = new mysqli("localhost", "root", "", "library_db", 3306);

if ($conn->connect_error) {
    die("<div style='font-family:sans-serif;padding:2rem;background:#fef2f2;color:#991b1b;border-radius:8px;margin:2rem;'>
        <h3>⚠ Database Connection Failed</h3>
        <p><strong>" . $conn->connect_error . "</strong></p>
        <p>Steps to fix:<br>
        1. Open XAMPP Control Panel → Start Apache and MySQL<br>
        2. Open phpMyAdmin → Create database named <code>library_db</code><br>
        3. Import <code>library_db.sql</code><br>
        4. Run <code>setup_passwords.php</code> once<br>
        5. If MySQL uses port 3307, change it in config.php</p>
    </div>");
}

$conn->set_charset("utf8mb4");

// ── Auth helpers ─────────────────────────────────────────────
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: index.php?error=noperm");
        exit();
    }
}

// ── Badge auto-award ─────────────────────────────────────────
// Call after any borrow/return action
function awardBadges($conn) {
    $members = $conn->query("SELECT member_id, COUNT(*) AS total FROM Borrowings GROUP BY member_id");
    while ($m = $members->fetch_assoc()) {
        $mid   = (int)$m['member_id'];
        $total = (int)$m['total'];

        $badge_id = 0;
        if ($total >= 10)    $badge_id = 3; // Bookworm
        elseif ($total >= 5) $badge_id = 2; // Active Reader
        elseif ($total >= 1) $badge_id = 1; // Beginner Reader

        if ($badge_id > 0) {
            $check = $conn->query("SELECT * FROM Member_Badges WHERE member_id=$mid AND badge_id=$badge_id");
            if ($check && $check->num_rows === 0) {
                $conn->query("INSERT INTO Member_Badges (member_id, badge_id, date_awarded) VALUES ($mid, $badge_id, CURDATE())");
            }
        }
    }
}

// ── XSS helper ──────────────────────────────────────────────
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>