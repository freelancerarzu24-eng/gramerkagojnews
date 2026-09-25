<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'অনুগ্রহ করে ইমেইল এবং পাসওয়ার্ড প্রদান করুন।'; // Please enter email and password
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] == 1) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php");
                exit();
            } else {
                $error = 'আপনার একাউন্টটি নিষ্ক্রিয় করা হয়েছে।'; // Your account is deactivated
            }
        } else {
            $error = 'ভুল ইমেইল বা পাসওয়ার্ড।'; // Invalid email or password
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - News Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #F5F6F8; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); background-color: #FFFFFF; }
        .btn-primary { background-color: #B62427; border-color: #B62427; }
        .btn-primary:hover { background-color: #8F171B; border-color: #8F171B; }
        .form-control:focus { border-color: #B62427; box-shadow: 0 0 0 0.25rem rgba(182, 36, 39, 0.25); }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center mb-4">অ্যাডমিন লগইন</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="email" class="form-label">ইমেইল</label>
                <input type="email" name="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">পাসওয়ার্ড</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">লগইন</button>
        </form>
    </div>
</body>
</html>
