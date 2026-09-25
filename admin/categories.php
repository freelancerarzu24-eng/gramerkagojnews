<?php require_once 'includes/header.php';

// Handle add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed");
    }

    $name = $_POST['name'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['slug'])));

    $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
    try {
        $stmt->execute([$name, $slug]);
        $success = "ক্যাটাগরি সফলভাবে যোগ করা হয়েছে।";
    } catch (PDOException $e) {
        $error = "ত্রুটি: " . $e->getMessage();
    }
}

// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY sort_order, id DESC");
$categories = $stmt->fetchAll();

?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>ক্যাটাগরি সমূহ</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">নতুন ক্যাটাগরি</button>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>আইডি</th>
                            <th>নাম</th>
                            <th>স্লাগ (Slug)</th>
                            <th>স্ট্যাটাস</th>
                            <th>অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><?= htmlspecialchars($cat['name']) ?></td>
                            <td><?= htmlspecialchars($cat['slug']) ?></td>
                            <td><?= $cat['status'] ? 'সক্রিয়' : 'নিষ্ক্রিয়' ?></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-info text-white">এডিট</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div class="modal fade" id="addCategoryModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" class="modal-content">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">নতুন ক্যাটাগরি যোগ করুন</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">নাম</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">স্লাগ (ইংরেজি)</label>
                            <input type="text" name="slug" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">সেভ করুন</button>
                    </div>
                </form>
            </div>
        </div>

<?php require_once 'includes/footer.php'; ?>
