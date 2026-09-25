<?php require_once 'templates/header.php'; ?>

<?php
// Fetch Featured News
$stmt = $pdo->query("
    SELECT n.*, c.name as category_name
    FROM news n
    LEFT JOIN categories c ON n.category_id = c.id
    WHERE n.status = 'published' AND n.featured = 1
    ORDER BY n.id DESC LIMIT 1
");
$hero_news = $stmt->fetch();

// Fetch Latest News
$stmt = $pdo->query("
    SELECT n.*, c.name as category_name
    FROM news n
    LEFT JOIN categories c ON n.category_id = c.id
    WHERE n.status = 'published'
    ORDER BY n.id DESC LIMIT 6
");
$latest_news = $stmt->fetchAll();
?>

<div class="container-custom mt-4">
    <div class="row">
        <!-- Hero Section -->
        <div class="col-lg-8">
            <?php if($hero_news): ?>
            <div class="news-card hero-card">
                <a href="article.php?slug=<?= $hero_news['slug'] ?>">
                    <img src="<?= $hero_news['featured_image'] ? 'uploads/news/'.$hero_news['featured_image'] : 'https://via.placeholder.com/800x450' ?>" alt="<?= htmlspecialchars($hero_news['title']) ?>" class="img-fluid rounded">
                </a>
                <div class="mt-3">
                    <span class="category-label"><?= htmlspecialchars($hero_news['category_name']) ?></span>
                    <a href="article.php?slug=<?= $hero_news['slug'] ?>">
                        <h2 class="headline" style="font-size: 32px;"><?= htmlspecialchars($hero_news['title']) ?></h2>
                    </a>
                    <p class="summary"><?= mb_strimwidth(strip_tags($hero_news['content']), 0, 150, "...") ?></p>
                    <span class="date"><i class="far fa-clock"></i> <?= date('d M Y, h:i A', strtotime($hero_news['created_at'])) ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Latest News Sidebar -->
        <div class="col-lg-4">
            <h4 class="mb-3" style="border-bottom: 2px solid var(--primary-red); padding-bottom: 5px; display: inline-block;">সর্বশেষ খবর</h4>
            <div class="latest-news-list">
                <?php foreach($latest_news as $news): ?>
                <div class="d-flex mb-3 border-bottom pb-2">
                    <div class="flex-shrink-0 me-3">
                        <img src="<?= $news['featured_image'] ? 'uploads/news/'.$news['featured_image'] : 'https://via.placeholder.com/100x75' ?>" style="width: 100px; height: 75px; object-fit: cover;" class="rounded" alt="<?= htmlspecialchars($news['title']) ?>">
                    </div>
                    <div>
                        <a href="article.php?slug=<?= $news['slug'] ?>">
                            <h6 class="headline" style="font-size: 16px; margin-bottom: 5px;"><?= htmlspecialchars($news['title']) ?></h6>
                        </a>
                        <span class="date"><i class="far fa-clock"></i> <?= date('h:i A', strtotime($news['created_at'])) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Advertisement -->
    <div class="text-center my-4">
        <img src="https://via.placeholder.com/970x90.png?text=Advertisement" class="img-fluid" alt="Ad">
    </div>

</div>

<?php require_once 'templates/footer.php'; ?>
