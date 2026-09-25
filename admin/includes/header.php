<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - News Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #F5F6F8; color: #222222; font-family: 'Noto Sans Bengali', sans-serif; }
        .sidebar { background-color: #171717; color: white; min-height: 100vh; padding-top: 20px; position: fixed; width: 250px; }
        .sidebar a { color: #A0AEC0; text-decoration: none; padding: 10px 20px; display: block; }
        .sidebar a:hover, .sidebar a.active { color: white; background-color: #B62427; }
        .main-content { margin-left: 250px; padding: 20px; }
        .topbar { background-color: white; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .card { border-color: #E5E7EB; margin-bottom: 20px; }
        .btn-primary { background-color: #B62427; border-color: #B62427; }
        .btn-primary:hover { background-color: #8F171B; border-color: #8F171B; }

        @media (max-width: 768px) {
            .sidebar { width: 100%; position: relative; min-height: auto; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="sidebar d-none d-md-block">
        <h4 class="text-center text-white mb-4">News CMS</h4>
        <a href="index.php" class="active"><i class="fas fa-tachometer-alt me-2"></i> ড্যাশবোর্ড</a>
        <a href="categories.php"><i class="fas fa-list me-2"></i> ক্যাটাগরি</a>
        <a href="news.php"><i class="fas fa-newspaper me-2"></i> খবর</a>
        <a href="settings.php"><i class="fas fa-cogs me-2"></i> সেটিংস</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> লগআউট</a>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div>
                <button class="btn btn-outline-secondary d-md-none"><i class="fas fa-bars"></i></button>
            </div>
            <div>
                <span>স্বাগতম, <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)</span>
            </div>
        </div>
