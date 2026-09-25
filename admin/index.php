<?php require_once 'includes/header.php';

// Fetch some basic stats
$total_news = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$published_news = $pdo->query("SELECT COUNT(*) FROM news WHERE status='published'")->fetchColumn();
$total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

?>

        <h2 class="mb-4">ড্যাশবোর্ড</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h3><?= $total_news ?></h3>
                    <p class="text-muted mb-0">মোট খবর</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h3><?= $published_news ?></h3>
                    <p class="text-muted mb-0">প্রকাশিত খবর</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h3><?= $total_categories ?></h3>
                    <p class="text-muted mb-0">ক্যাটাগরি</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h3><?= $total_users ?></h3>
                    <p class="text-muted mb-0">ব্যবহারকারী</p>
                </div>
            </div>
        </div>

<?php require_once 'includes/footer.php'; ?>
