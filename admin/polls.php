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

// Handle Add/Edit/Delete Polls
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $question = trim($_POST['question']);
        $status = isset($_POST['status']) ? 1 : 0;

        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO polls (question, status) VALUES (?, ?)");
            $stmt->execute([$question, $status]);
            $poll_id = $pdo->lastInsertId();

            if (isset($_POST['options']) && is_array($_POST['options'])) {
                $stmt_opt = $pdo->prepare("INSERT INTO poll_options (poll_id, option_text) VALUES (?, ?)");
                foreach ($_POST['options'] as $opt) {
                    if (trim($opt) !== '') {
                        $stmt_opt->execute([$poll_id, trim($opt)]);
                    }
                }
            }
            $pdo->commit();
            $msg = "Poll added successfully.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $msg = "Error adding poll: " . $e->getMessage();
        }

        header("Location: polls.php?msg=" . urlencode($msg));
        exit();
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM polls WHERE id=?");
        $stmt->execute([$id]); // cascading deletes options and votes
        header("Location: polls.php?msg=Poll deleted successfully.");
        exit();
    }
}

$stmt = $pdo->query("SELECT * FROM polls ORDER BY id DESC");
$polls = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Polls - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        function addOption() {
            var div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = '<input type="text" name="options[]" class="form-control" placeholder="Option" required><button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>';
            document.getElementById('options-container').appendChild(div);
        }
    </script>
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
            <li class="nav-item"><a class="nav-link text-white" href="videos.php"><i class="fas fa-video"></i> Videos</a></li>
            <li class="nav-item"><a class="nav-link text-white bg-danger rounded" href="polls.php"><i class="fas fa-poll"></i> Polls</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li class="nav-item mt-5"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="flex-grow-1 p-4">
        <h2>Manage Polls</h2>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white"><strong>Add New Poll</strong></div>
            <div class="card-body">
                <form action="polls.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="add">

                    <div class="mb-3">
                        <label>Poll Question</label>
                        <input type="text" name="question" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Options</label>
                        <div id="options-container">
                            <div class="input-group mb-2">
                                <input type="text" name="options[]" class="form-control" placeholder="Option 1" required>
                            </div>
                            <div class="input-group mb-2">
                                <input type="text" name="options[]" class="form-control" placeholder="Option 2" required>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="addOption()">+ Add Option</button>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="status" id="pStatus" checked>
                        <label class="form-check-label" for="pStatus">Active Poll</label>
                    </div>

                    <button type="submit" class="btn btn-danger">Save Poll</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($polls as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['question']) ?></td>
                            <td><?= $p['status'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
                            <td><?= $p['created_at'] ?></td>
                            <td>
                                <form action="polls.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this poll?');">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($polls)): ?>
                        <tr><td colspan="5" class="text-center">No polls found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
