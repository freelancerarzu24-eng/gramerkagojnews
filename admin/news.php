<?php require_once 'includes/header.php';

// Fetch News
$stmt = $pdo->query("
    SELECT n.*, c.name as category_name, u.username as author_name
    FROM news n
    LEFT JOIN categories c ON n.category_id = c.id
    LEFT JOIN users u ON n.author_id = u.id
    ORDER BY n.id DESC
");
$news_list = $stmt->fetchAll();

?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>খবর সমূহ</h2>
            <a href="news_action.php" class="btn btn-primary">নতুন খবর লিখুন</a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>শিরোনাম</th>
                            <th>ক্যাটাগরি</th>
                            <th>লেখক</th>
                            <th>স্ট্যাটাস</th>
                            <th>তারিখ</th>
                            <th>অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news_list as $news): ?>
                        <tr>
                            <td><?= htmlspecialchars($news['title']) ?></td>
                            <td><?= htmlspecialchars($news['category_name']) ?></td>
                            <td><?= htmlspecialchars($news['author_name']) ?></td>
                            <td><?= ucfirst($news['status']) ?></td>
                            <td><?= date('d M, Y', strtotime($news['created_at'])) ?></td>
                            <td>
                                <a href="news_action.php?id=<?= $news['id'] ?>" class="btn btn-sm btn-info text-white">এডিট</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

<?php require_once 'includes/footer.php'; ?>
