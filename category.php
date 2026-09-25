<?php require_once 'templates/header.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? AND status = 1");
$stmt->execute([$slug]);
$category = $stmt->fetch();

if (!$category) {
    echo "<div class='container-custom my-5 text-center'><h2>ক্যাটাগরিটি পাওয়া যায়নি (404)</h2></div>";
} else {
    // Fetch News for Category
    $stmt = $pdo->prepare("SELECT * FROM news WHERE category_id = ? AND status = 'published' ORDER BY id DESC LIMIT 12");
    $stmt->execute([$category['id']]);
    $news_list = $stmt->fetchAll();
?>

<div class="container-custom mt-4">
    <h2 class="headline mb-4 pb-2 border-bottom border-danger d-inline-block border-2"><?= htmlspecialchars($category['name']) ?></h2>

    <div class="row">
        <?php if(empty($news_list)): ?>
            <div class="col-12"><p>এই ক্যাটাগরিতে কোন খবর নেই।</p></div>
        <?php else: ?>
            <?php foreach($news_list as $news): ?>
            <div class="col-md-4 col-sm-6">
                <div class="news-card">
                    <a href="article.php?slug=<?= $news['slug'] ?>">
                        <img src="<?= $news['featured_image'] ? 'uploads/news/'.$news['featured_image'] : 'https://via.placeholder.com/400x225' ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                    </a>
                    <a href="article.php?slug=<?= $news['slug'] ?>">
                        <h4 class="headline fs-5"><?= htmlspecialchars($news['title']) ?></h4>
                    </a>
                    <span class="date"><i class="far fa-clock"></i> <?= date('d M, Y', strtotime($news['created_at'])) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
} // end else
require_once 'templates/footer.php';
?>
