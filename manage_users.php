<?php
session_start();

// 1. Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Must be an admin (matches your setup_passwords.php logic)
if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access Denied!'); window.location='dashboard.php';</script>";
    exit();
}

$conn = new mysqli("localhost", "root", "", "library_db", 3306);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>User Management</h2>
        <p>This page is only visible to Admins.</p>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        
        <!-- Add your HTML table here to list, edit, or delete users -->
    </div>
</body>
</html>