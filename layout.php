<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user_name = $_SESSION['fullname'] ?? 'User';
$user_role = $_SESSION['role'] ?? 'Staff';

function activeLink($file, $current_page) {
    return $file === $current_page ? 'active-link' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bookstore ISMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body { background: #f4f6f9; font-family: Arial, sans-serif; }

        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #111827;
            color: white;
            padding: 20px 15px;
            overflow-y: auto;
        }

        .sidebar-brand {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
        }

        .sidebar-section {
            font-size: 12px;
            text-transform: uppercase;
            color: #9ca3af;
            margin: 20px 10px 8px;
            letter-spacing: .08em;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #d1d5db;
            padding: 11px 14px;
            border-radius: 10px;
            text-decoration: none;
            margin-bottom: 6px;
            font-size: 15px;
        }

        .sidebar a:hover,
        .sidebar .active-link {
            background: #2563eb;
            color: white;
        }

        .main-content {
            margin-left: 260px;
            padding: 25px;
            min-height: 100vh;
        }

        .topbar {
            background: white;
            border-radius: 14px;
            padding: 15px 20px;
            margin-bottom: 25px;
            box-shadow: 0 3px 12px rgba(0,0,0,.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card { border: none; border-radius: 14px; }
        .table { background: white; }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="sidebar-brand">📖 Bookstore ISMS</div>

    <div class="sidebar-section">Main</div>

    <a href="dashboard.php" class="<?= activeLink('dashboard.php', $current_page) ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>

   <!-- <div class="sidebar-section">Inventory Setup</div>

<a href="books.php" class="<?= activeLink('books.php', $current_page) ?>">
    <i class="bi bi-box-seam"></i>Inventory Products
</a> -->

    <div class="sidebar-section">CRM Management</div>

    <a href="members.php" class="<?= activeLink('members.php', $current_page) ?>">
        <i class="bi bi-people"></i> Clients
    </a>

    <a href="borrow.php" class="<?= activeLink('borrow.php', $current_page) ?>">
        <i class="bi bi-cart-check"></i> Sales
    </a>

    <a href="return.php" class="<?= activeLink('return.php', $current_page) ?>">
        <i class="bi bi-clock-history"></i> Sales History
    </a>

    <a href="fines.php" class="<?= activeLink('fines.php', $current_page) ?>">
        <i class="bi bi-cash-coin"></i> Payments
    </a>

    <div class="sidebar-section">Customer Engagement</div>

    <a href="leaderboard.php" class="<?= activeLink('leaderboard.php', $current_page) ?>">
        <i class="bi bi-trophy"></i> Top Clients
    </a>

    <a href="badges.php" class="<?= activeLink('badges.php', $current_page) ?>">
        <i class="bi bi-gift"></i> Loyalty Rewards
    </a>

    <div class="sidebar-section">Business Reports</div>

    <a href="reports.php" class="<?= activeLink('reports.php', $current_page) ?>">
        <i class="bi bi-graph-up-arrow"></i> Financial Reports
    </a>

    <div class="sidebar-section">Account</div>

    <a href="logout.php">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>

<div class="main-content">

    <div class="topbar">
        <div>
            <strong><?= htmlspecialchars($user_name) ?></strong>
            <div class="text-muted small"><?= htmlspecialchars($user_role) ?></div>
        </div>

        <span class="badge bg-success">Business ISMS</span>
    </div>