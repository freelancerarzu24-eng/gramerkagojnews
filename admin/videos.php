<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../config/database.php';

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $title = trim($_POST['title']);
        $youtube_id = trim($_POST['youtube_id']);
        $status = isset($_POST['status']) ? 1 : 0;

        // Basic Extract Thumbnail from YouTube ID if not provided
        $thumbnail = '';
        if(!empty($youtube_id)){
            $thumbnail = "https://img.youtube.com/vi/{$youtube_id}/hqdefault.jpg";
        }

        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO videos (title, youtube_id, thumbnail, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $youtube_id, $thumbnail, $status]);
            $msg = "Video added successfully.";
        } else {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE videos SET title=?, youtube_id=?, thumbnail=?, status=? WHERE id=?");
            $stmt->execute([$title, $youtube_id, $thumbnail, $status, $id]);
            $msg = "Video updated successfully.";
        }
        header("Location: videos.php?msg=" . urlencode($msg));
        exit();
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM videos WHERE id=?");
        $stmt->execute([$id]);
        header("Location: videos.php?msg=Video deleted successfully.");
        exit();
    }
}

$stmt = $pdo->query("SELECT * FROM videos ORDER BY id DESC");
$videos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Videos - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body style="background-color: #F5F6F8;">

<div class="d-flex">
    <!-- Sidebar Placeholder -->
    <div class="bg-dark text-white p-3 vh-100" style="width: 250px;">
        <h4 class="text-center mb-4 text-danger">CMS Admin</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link text-white" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="categories.php"><i class="fas fa-list"></i> Categories</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="news.php"><i class="fas fa-newspaper"></i> News</a></li>
            <li class="nav-item"><a class="nav-link text-white bg-danger rounded" href="videos.php"><i class="fas fa-video"></i> Videos</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="polls.php"><i class="fas fa-poll"></i> Polls</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li class="nav-item mt-5"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="flex-grow-1 p-4">
        <h2>Manage Videos</h2>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white"><strong>Add New Video</strong></div>
            <div class="card-body">
                <form action="videos.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="add">

                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label>Video Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>YouTube Video ID (e.g., dQw4w9WgXcQ)</label>
                            <input type="text" name="youtube_id" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="d-block">&nbsp;</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="status" id="vStatus" checked>
                                <label class="form-check-label" for="vStatus">Active</label>
                            </div>
                        </div>
                        <div class="col-md-1 mb-3">
                            <label class="d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-danger w-100">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Thumbnail</th>
                            <th>Title</th>
                            <th>YouTube ID</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($videos as $v): ?>
                        <tr>
                            <td><?= $v['id'] ?></td>
                            <td><img src="<?= $v['thumbnail'] ?>" alt="Thumb" width="80" class="rounded"></td>
                            <td><?= htmlspecialchars($v['title']) ?></td>
                            <td><?= htmlspecialchars($v['youtube_id']) ?></td>
                            <td><?= $v['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
                            <td>
                                <form action="videos.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this video?');">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $v['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($videos)): ?>
                        <tr><td colspan="6" class="text-center">No videos found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
