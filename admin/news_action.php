<?php require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
$news = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch();
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed");
    }

    $title = $_POST['title'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['slug'])));
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $status = $_POST['status'];
    $author_id = $_SESSION['user_id'];

    // Secure Image Upload
    $featured_image = $news['featured_image'] ?? null;
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['image']['tmp_name']);

        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../uploads/news/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = time() . '_' . uniqid() . '.' . $ext;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $file_name)) {
                $featured_image = $file_name;
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.";
        }
    }

    if (!isset($error)) {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE news SET title=?, slug=?, content=?, category_id=?, status=?, featured_image=? WHERE id=?");
            $stmt->execute([$title, $slug, $content, $category_id, $status, $featured_image, $id]);
            $success = "খবর সফলভাবে আপডেট হয়েছে।";
        } else {
            $stmt = $pdo->prepare("INSERT INTO news (title, slug, content, category_id, status, author_id, featured_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $content, $category_id, $status, $author_id, $featured_image]);
            $success = "নতুন খবর যোগ করা হয়েছে।";
            $id = $pdo->lastInsertId();
            $news = $_POST; // for persisting form values
        }
    }
}
?>

        <h2><?= $id ? 'খবর এডিট করুন' : 'নতুন খবর লিখুন' ?></h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="row">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">শিরোনাম</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($news['title'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">স্লাগ (English)</label>
                            <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($news['slug'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">খবরের বিস্তারিত</label>
                            <textarea name="content" class="form-control" rows="10" required><?= htmlspecialchars($news['content'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-3">সেভ করুন (Publish/Save)</button>

                        <div class="mb-3">
                            <label class="form-label">স্ট্যাটাস</label>
                            <select name="status" class="form-select">
                                <option value="published" <?= (($news['status'] ?? '') == 'published') ? 'selected' : '' ?>>প্রকাশিত (Published)</option>
                                <option value="draft" <?= (($news['status'] ?? '') == 'draft') ? 'selected' : '' ?>>খসড়া (Draft)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ক্যাটাগরি</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">নির্বাচন করুন</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (($news['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ফিচার্ড ইমেজ</label>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                            <?php if(!empty($news['featured_image'])): ?>
                                <img src="../uploads/news/<?= htmlspecialchars($news['featured_image']) ?>" class="img-thumbnail mt-2" style="max-height: 150px;">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>

<?php require_once 'includes/footer.php'; ?>
