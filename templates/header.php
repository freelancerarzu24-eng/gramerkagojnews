<?php
require_once 'config/database.php';

// Fetch global settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings_data = $stmt->fetchAll();
$settings = [];
foreach ($settings_data as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}

// Fetch Categories for Nav
$stmt = $pdo->query("SELECT * FROM categories WHERE status=1 ORDER BY sort_order ASC LIMIT 10");
$nav_categories = $stmt->fetchAll();

// Fetch Breaking News
$stmt = $pdo->query("SELECT title, slug FROM news WHERE status='published' AND breaking=1 ORDER BY id DESC LIMIT 5");
$breaking_news = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($settings['site_name'] ?? 'News Portal') ?> - <?= htmlspecialchars($settings['site_tagline'] ?? '') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- Top Info Bar -->
    <div class="top-info-bar">
        <div class="container-custom d-flex justify-content-between align-items-center">
            <div class="date-info">
                <i class="far fa-calendar-alt"></i>
                <?= date('l, d F Y') ?>
            </div>
            <div class="social-icons d-none d-md-flex">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <span class="ms-3 border-start ps-3"><a href="#">English</a></span>
                <span class="ms-2"><a href="#">E-Paper</a></span>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container-custom text-center text-md-start d-flex justify-content-between align-items-center">
            <a href="index.php" class="site-logo">
                <?= htmlspecialchars($settings['site_name'] ?? 'আমার নিউজ') ?>
            </a>
            <div class="d-none d-md-block">
                <!-- Placeholder for Top Ad -->
                <img src="https://via.placeholder.com/728x90.png?text=Advertisement" alt="Ad" class="img-fluid">
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg main-nav">
        <div class="container-custom">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home"></i> হোম</a>
                    </li>
                    <?php foreach($nav_categories as $cat): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="category.php?slug=<?= $cat['slug'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="d-flex">
                    <button class="btn border-0 shadow-none"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Breaking News -->
    <?php if(!empty($breaking_news)): ?>
    <div class="breaking-news">
        <div class="container-custom d-flex">
            <div class="breaking-label"><i class="fas fa-circle text-white" style="font-size: 10px;"></i> ব্রেকিং নিউজ</div>
            <div class="breaking-ticker">
                <p>
                    <?php foreach($breaking_news as $bn): ?>
                        <a href="article.php?slug=<?= $bn['slug'] ?>" class="text-white me-5 text-decoration-none"><?= htmlspecialchars($bn['title']) ?></a>
                    <?php endforeach; ?>
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
