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

// Fetch Videos
$stmt = $pdo->query("
    SELECT * FROM videos WHERE status = 1 ORDER BY id DESC LIMIT 4
");
$videos = $stmt->fetchAll();

// Fetch Photo Albums
$stmt = $pdo->query("
    SELECT * FROM photo_albums WHERE status = 1 ORDER BY id DESC LIMIT 4
");
$albums = $stmt->fetchAll();

// Fetch Active Poll
$stmt = $pdo->query("
    SELECT * FROM polls WHERE status = 1 AND (start_date IS NULL OR start_date <= NOW()) AND (end_date IS NULL OR end_date >= NOW()) ORDER BY id DESC LIMIT 1
");
$active_poll = $stmt->fetch();
$poll_options = [];
if ($active_poll) {
    $stmt = $pdo->prepare("SELECT * FROM poll_options WHERE poll_id = ?");
    $stmt->execute([$active_poll['id']]);
    $poll_options = $stmt->fetchAll();
}
?>

<div class="container-custom mt-4">
    <div class="row">
        <!-- Hero Section -->
        <div class="col-lg-8">
            <?php if($hero_news): ?>
            <div class="news-card hero-card">
                <a href="article.php?slug=<?= $hero_news['slug'] ?>">
                    <img src="<?= $hero_news['featured_image'] ? 'uploads/news/'.$hero_news['featured_image'] : 'https://via.placeholder.com/800x450' ?>" alt="<?= htmlspecialchars($hero_news['title']) ?>" class="img-fluid rounded w-100">
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
            <h4 class="section-title">সর্বশেষ খবর</h4>
            <div class="latest-news-list">
                <?php foreach($latest_news as $news): ?>
                <div class="d-flex mb-3 border-bottom pb-2 news-card">
                    <div class="flex-shrink-0 me-3">
                        <a href="article.php?slug=<?= $news['slug'] ?>">
                            <img src="<?= $news['featured_image'] ? 'uploads/news/'.$news['featured_image'] : 'https://via.placeholder.com/100x75' ?>" style="width: 100px; height: 75px; object-fit: cover;" class="rounded" alt="<?= htmlspecialchars($news['title']) ?>">
                        </a>
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
            <a href="category.php?slug=latest" class="btn btn-outline-danger w-100 btn-sm mt-2">আরও খবর <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    <!-- Advertisement -->
    <div class="text-center my-4 py-2 bg-light border">
        <img src="https://via.placeholder.com/970x90.png?text=Advertisement" class="img-fluid" alt="Ad">
    </div>

    <div class="row">
        <!-- Main Content Area (Videos + Gallery) -->
        <div class="col-lg-9">
            <!-- Video Section -->
            <div class="video-section py-4 bg-dark text-white rounded px-3 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="section-title border-danger m-0">ভিডিও</h4>
                    <a href="video.php" class="text-white text-decoration-none">সকল ভিডিও <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="row">
                    <?php foreach($videos as $video): ?>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="position-relative">
                            <img src="<?= $video['thumbnail'] ? 'uploads/videos/'.$video['thumbnail'] : 'https://via.placeholder.com/400x225/333/fff?text=Video' ?>" class="img-fluid rounded w-100" alt="<?= htmlspecialchars($video['title']) ?>">
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <a href="video.php?id=<?= $video['id'] ?>" class="text-white bg-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                        </div>
                        <h6 class="mt-2 text-white"><a href="video.php?id=<?= $video['id'] ?>" class="text-white text-decoration-none"><?= htmlspecialchars($video['title']) ?></a></h6>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($videos)): ?>
                        <p class="text-muted text-center w-100">কোনো ভিডিও পাওয়া যায়নি</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Photo Gallery Section -->
            <div class="photo-gallery-section mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="section-title">ফটোগ্যালারি</h4>
                    <a href="gallery.php" class="text-danger text-decoration-none">সকল ছবি <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="row">
                    <?php foreach($albums as $album): ?>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="position-relative border rounded overflow-hidden news-card">
                            <a href="gallery.php?slug=<?= $album['slug'] ?>">
                                <img src="<?= $album['cover_image'] ? 'uploads/gallery/'.$album['cover_image'] : 'https://via.placeholder.com/400x300?text=Gallery' ?>" class="img-fluid w-100" alt="<?= htmlspecialchars($album['title']) ?>">
                                <div class="position-absolute bottom-0 start-0 w-100 p-2" style="background: rgba(0,0,0,0.7);">
                                    <h6 class="text-white m-0" style="font-size: 14px;"><i class="fas fa-camera me-1"></i> <?= htmlspecialchars($album['title']) ?></h6>
                                </div>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($albums)): ?>
                        <p class="text-muted text-center w-100">কোনো ছবি পাওয়া যায়নি</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Sidebar (Polls + Popular) -->
        <div class="col-lg-3">
            <!-- Opinion Poll -->
            <div class="poll-widget bg-light border p-3 rounded mb-4">
                <h4 class="section-title mb-3" style="font-size:18px;">মতামত জরিপ</h4>
                <?php if($active_poll): ?>
                    <p class="fw-bold"><?= htmlspecialchars($active_poll['question']) ?></p>
                    <form action="vote.php" method="POST">
                        <input type="hidden" name="poll_id" value="<?= $active_poll['id'] ?>">
                        <?php foreach($poll_options as $opt): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="option_id" value="<?= $opt['id'] ?>" id="opt<?= $opt['id'] ?>">
                                <label class="form-check-label" for="opt<?= $opt['id'] ?>">
                                    <?= htmlspecialchars($opt['option_text']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-danger btn-sm w-100 mt-2">ভোট দিন</button>
                    </form>
                <?php else: ?>
                    <p class="text-muted">বর্তমানে কোনো জরিপ নেই।</p>
                <?php endif; ?>
            </div>

            <!-- Popular Sidebar Mockup -->
            <div class="popular-widget bg-white border p-3 rounded">
                <h4 class="section-title mb-3" style="font-size:18px;">জনপ্রিয় খবর</h4>
                <ol class="text-danger fw-bold ps-3 m-0" style="font-family: 'Noto Serif Bengali', serif;">
                    <?php
                    // Reuse latest for popular visual mockup
                    foreach(array_slice($latest_news, 0, 5) as $pn): ?>
                    <li class="mb-2">
                        <a href="article.php?slug=<?= $pn['slug'] ?>" class="text-dark text-decoration-none" style="font-size:14px; font-weight:normal;">
                            <?= htmlspecialchars($pn['title']) ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </div>

</div>

<?php require_once 'templates/footer.php'; ?>
