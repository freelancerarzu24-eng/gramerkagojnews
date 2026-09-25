<?php require_once 'templates/header.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("
    SELECT n.*, c.name as category_name, c.slug as category_slug, u.username as author_name
    FROM news n
    LEFT JOIN categories c ON n.category_id = c.id
    LEFT JOIN users u ON n.author_id = u.id
    WHERE n.slug = ? AND n.status = 'published'
");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    echo "<div class='container-custom my-5 text-center'><h2>খবরটি পাওয়া যায়নি (404)</h2></div>";
} else {
    // Update views
    $pdo->prepare("UPDATE news SET views = views + 1 WHERE id = ?")->execute([$article['id']]);
?>

<div class="container-custom mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> হোম</a></li>
            <li class="breadcrumb-item"><a href="category.php?slug=<?= $article['category_slug'] ?>"><?= htmlspecialchars($article['category_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">বিস্তারিত</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <h1 class="headline mb-3"><?= htmlspecialchars($article['title']) ?></h1>
            <div class="d-flex justify-content-between align-items-center mb-4 text-muted border-top border-bottom py-2">
                <div>
                    <i class="fas fa-user-edit"></i> <strong><?= htmlspecialchars($article['author_name']) ?></strong><br>
                    <small><i class="far fa-clock"></i> প্রকাশিত: <?= date('d F Y, h:i A', strtotime($article['created_at'])) ?></small>
                </div>
                <div>
                    <!-- Share buttons -->
                    <a href="#" class="btn btn-sm btn-outline-primary"><i class="fab fa-facebook-f"></i> শেয়ার</a>
                </div>
            </div>

            <?php if($article['featured_image']): ?>
            <div class="mb-4">
                <img src="uploads/news/<?= $article['featured_image'] ?>" class="img-fluid rounded w-100" alt="<?= htmlspecialchars($article['title']) ?>">
            </div>
            <?php endif; ?>

            <div class="article-content" style="font-size: 18px; line-height: 1.8;">
                <?= nl2br(htmlspecialchars($article['content'])) ?>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Sidebar Ad -->
            <div class="text-center mb-4">
                <img src="https://via.placeholder.com/300x250.png?text=Ad" class="img-fluid" alt="Ad">
            </div>

            <h4 class="mb-3 border-bottom pb-2">জনপ্রিয় খবর</h4>
            <?php
            $stmt = $pdo->query("SELECT * FROM news WHERE status='published' ORDER BY views DESC LIMIT 5");
            $popular = $stmt->fetchAll();
            $count = 1;
            foreach($popular as $pop):
            ?>
            <div class="d-flex mb-3 align-items-center">
                <div class="text-danger fw-bold fs-4 me-3" style="font-family: 'Noto Serif Bengali', serif;"><?= str_pad($count++, 2, '0', STR_PAD_LEFT) ?></div>
                <div>
                    <a href="article.php?slug=<?= $pop['slug'] ?>"><h6 class="headline mb-1"><?= htmlspecialchars($pop['title']) ?></h6></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
} // end else
require_once 'templates/footer.php';
?>
