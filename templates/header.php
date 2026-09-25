<?php
require_once 'config/database.php';

// Fetch global settings
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings_data = $stmt->fetchAll();
$settings = [];
foreach ($settings_data as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}

// Fetch Categories for Nav (Main Nav)
$stmt = $pdo->query("SELECT * FROM categories WHERE status=1 AND parent_id IS NULL ORDER BY sort_order ASC LIMIT 12");
$nav_categories = $stmt->fetchAll();

// Fetch Subcategories for Mega Menu (Simple 2 level array)
$stmt = $pdo->query("SELECT * FROM categories WHERE status=1 AND parent_id IS NOT NULL ORDER BY sort_order ASC");
$subcats = $stmt->fetchAll();
$mega_menu_data = [];
foreach ($nav_categories as $parent) {
    $mega_menu_data[$parent['id']] = ['parent' => $parent, 'children' => []];
}
foreach ($subcats as $sub) {
    if (isset($mega_menu_data[$sub['parent_id']])) {
        $mega_menu_data[$sub['parent_id']]['children'][] = $sub;
    }
}

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
    <style>
        /* Mega Menu Overlay */
        .mega-menu-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85);
            z-index: 10000;
            overflow-y: auto;
            padding: 40px 20px;
        }
        .mega-menu-content {
            background: #fff;
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 8px;
            position: relative;
        }
        .close-mega-menu {
            position: absolute;
            top: 15px; right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }
        .mega-menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .mega-menu-col h5 {
            color: #B62427;
            border-bottom: 2px solid #E5E5E5;
            padding-bottom: 8px;
            margin-bottom: 12px;
            font-size: 16px;
        }
        .mega-menu-col ul {
            list-style: none; padding: 0; margin: 0;
        }
        .mega-menu-col ul li { margin-bottom: 8px; }
        .mega-menu-col ul li a {
            color: #444; text-decoration: none; font-size: 14px;
        }
        .mega-menu-col ul li a:hover { color: #B62427; }

        /* Sticky Nav */
        .sticky-nav {
            position: sticky; top: 0; z-index: 9999;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
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
                <a href="<?= htmlspecialchars($settings['social_facebook'] ?? '#') ?>"><i class="fab fa-facebook-f"></i></a>
                <a href="<?= htmlspecialchars($settings['social_youtube'] ?? '#') ?>"><i class="fab fa-youtube"></i></a>
                <a href="<?= htmlspecialchars($settings['social_twitter'] ?? '#') ?>"><i class="fab fa-twitter"></i></a>
                <span class="ms-3 border-start ps-3"><a href="#">English</a></span>
                <span class="ms-2"><a href="#">E-Paper</a></span>
                <span class="ms-2"><a href="#">Archive</a></span>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container-custom text-center text-md-start d-flex justify-content-between align-items-center">
            <a href="index.php" class="site-logo">
                <?php if(isset($settings['logo']) && $settings['logo'] != ''): ?>
                    <!-- If real logo exists <img src="uploads/<?//=$settings['logo']?>" alt="Logo"> -->
                    <h2><?= htmlspecialchars($settings['site_name'] ?? 'আমার নিউজ') ?></h2>
                <?php else: ?>
                    <h2><?= htmlspecialchars($settings['site_name'] ?? 'আমার নিউজ') ?></h2>
                <?php endif; ?>
            </a>
            <div class="d-none d-md-block">
                <!-- Advertisement / Banner -->
                <img src="https://via.placeholder.com/728x90.png?text=Advertisement" alt="Ad" class="img-fluid border">
            </div>
        </div>
    </header>

    <!-- Sticky Navigation -->
    <nav class="navbar navbar-expand-lg main-nav sticky-nav bg-white">
        <div class="container-custom">
            <!-- Hamburger for Mega Menu -->
            <button class="btn btn-light me-2 border-0 shadow-none d-lg-none" type="button" onclick="document.getElementById('megaMenu').style.display='block'">
                <i class="fas fa-bars"></i>
            </button>
            <button class="btn btn-light me-2 border-0 shadow-none d-none d-lg-inline-block" type="button" onclick="document.getElementById('megaMenu').style.display='block'">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Mobile Logo -->
            <a href="index.php" class="navbar-brand d-lg-none mx-auto fw-bold text-dark" style="font-family: 'Noto Serif Bengali', serif;">
                <?= htmlspecialchars($settings['site_name'] ?? 'আমার নিউজ') ?>
            </a>

            <div class="collapse navbar-collapse d-none d-lg-flex" id="mainNav">
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
            </div>

            <div class="d-flex ms-auto">
                <button class="btn border-0 shadow-none text-dark" onclick="toggleSearch()"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </nav>

    <!-- Search Overlay -->
    <div id="searchOverlay" class="d-none" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.95); z-index:10001;">
        <div class="container h-100 d-flex justify-content-center align-items-center position-relative">
            <button class="btn btn-link text-dark position-absolute top-0 end-0 m-4" style="font-size:30px;" onclick="toggleSearch()"><i class="fas fa-times"></i></button>
            <div class="w-75">
                <form action="search.php" method="GET">
                    <div class="input-group input-group-lg">
                        <input type="text" name="q" class="form-control border-danger" placeholder="আপনি কী খুঁজছেন?" autofocus>
                        <button class="btn btn-danger" type="submit"><i class="fas fa-search"></i> খুঁজুন</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Mega Menu Overlay -->
    <div id="megaMenu" class="mega-menu-overlay">
        <div class="mega-menu-content">
            <span class="close-mega-menu" onclick="document.getElementById('megaMenu').style.display='none'"><i class="fas fa-times"></i></span>
            <h3 class="mb-4 text-center" style="font-family: 'Noto Serif Bengali', serif; color:#B62427;">সকল বিভাগ</h3>
            <div class="mega-menu-grid">
                <?php foreach($mega_menu_data as $cat_id => $data): ?>
                <div class="mega-menu-col">
                    <h5><a href="category.php?slug=<?= $data['parent']['slug'] ?>" class="text-decoration-none text-danger"><?= htmlspecialchars($data['parent']['name']) ?></a></h5>
                    <?php if(!empty($data['children'])): ?>
                    <ul>
                        <?php foreach($data['children'] as $child): ?>
                        <li><a href="category.php?slug=<?= $child['slug'] ?>"><?= htmlspecialchars($child['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

                <!-- Static Additional Sections -->
                <div class="mega-menu-col">
                    <h5>মিডিয়া</h5>
                    <ul>
                        <li><a href="video.php">ভিডিও গ্যালারি</a></li>
                        <li><a href="photo.php">ফটো গ্যালারি</a></li>
                        <li><a href="epaper.php">ই-পেপার</a></li>
                        <li><a href="archive.php">আর্কাইভ</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Breaking News -->
    <?php if(!empty($breaking_news)): ?>
    <div class="breaking-news">
        <div class="container-custom d-flex align-items-center">
            <div class="breaking-label flex-shrink-0"><i class="fas fa-circle text-white me-1" style="font-size: 8px;"></i> ব্রেকিং নিউজ</div>
            <div class="breaking-ticker overflow-hidden w-100">
                <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                    <?php foreach($breaking_news as $bn): ?>
                        <a href="article.php?slug=<?= $bn['slug'] ?>" class="text-white me-5 text-decoration-none fw-bold"><?= htmlspecialchars($bn['title']) ?></a>
                    <?php endforeach; ?>
                </marquee>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
    function toggleSearch() {
        var el = document.getElementById('searchOverlay');
        if (el.classList.contains('d-none')) {
            el.classList.remove('d-none');
        } else {
            el.classList.add('d-none');
        }
    }

    // Close mega menu on outside click
    window.onclick = function(event) {
        var modal = document.getElementById('megaMenu');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
