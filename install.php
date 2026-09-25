<?php
// install.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// SECURITY: Prevent accessing installation if already installed
if (file_exists('config/database.php')) {
    die("Installation already completed. To reinstall, please remove config/database.php.");
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$message = '';

if ($step === 2 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? '127.0.0.1';
    $db_name = $_POST['db_name'] ?? 'news_portal';
    $db_user = $_POST['db_user'] ?? 'root';
    $db_pass = $_POST['db_pass'] ?? '';
    $site_name = $_POST['site_name'] ?? 'আমার নিউজ';
    $admin_user = $_POST['admin_user'] ?? 'admin';
    $admin_pass = $_POST['admin_pass'] ?? 'password';
    $admin_email = $_POST['admin_email'] ?? 'admin@example.com';

    try {
        // 1. Create DB config file
        $config_content = "<?php\n"
            . "\$host = '$db_host';\n"
            . "\$db = '$db_name';\n"
            . "\$user = '$db_user';\n"
            . "\$pass = '$db_pass';\n"
            . "\$charset = 'utf8mb4';\n"
            . "\$dsn = \"mysql:host=\$host;dbname=\$db;charset=\$charset\";\n"
            . "\$options = [\n"
            . "    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,\n"
            . "    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n"
            . "    PDO::ATTR_EMULATE_PREPARES   => false,\n"
            . "];\n"
            . "try {\n"
            . "    \$pdo = new PDO(\$dsn, \$user, \$pass, \$options);\n"
            . "} catch (\\PDOException \$e) {\n"
            . "    throw new \\PDOException(\$e->getMessage(), (int)\$e->getCode());\n"
            . "}\n";

        file_put_contents('config/database.php', $config_content);

        // 2. Connect to Database (create it if doesn't exist)
        $temp_pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
        $temp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $temp_pdo->exec("USE `$db_name`");

        // 3. Import Schema
        $schema = file_get_contents('database/schema.sql');
        // Simple splitting by semicolon for demo, real parser recommended for complex SQL
        $statements = array_filter(array_map('trim', explode(';', $schema)));
        foreach ($statements as $stmt) {
            if (!empty($stmt)) {
                $temp_pdo->exec($stmt);
            }
        }

        // 4. Update Admin User Password (from default in schema)
        $hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);
        $update_admin = $temp_pdo->prepare("UPDATE users SET password = ?, username = ?, email = ? WHERE id = 1");
        $update_admin->execute([$hashed_pass, $admin_user, $admin_email]);

        // 5. Update Site Name Setting
        $update_setting = $temp_pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'site_name'");
        $update_setting->execute([$site_name]);

        // 6. Create required folders if not exist
        $folders = ['uploads/news', 'uploads/videos', 'uploads/gallery', 'admin/assets', 'admin/includes', 'admin/modules'];
        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }
        }

        $step = 3;
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install News Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f6f8; }
        .install-box { max-width: 600px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .install-header { text-align: center; margin-bottom: 30px; }
        .install-header h2 { color: #B62427; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="install-box">
            <div class="install-header">
                <h2>News Portal Installation</h2>
                <p class="text-muted">Set up your Bengali News Portal</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <?php if ($step === 1): ?>
                <form action="install.php?step=2" method="POST">
                    <h5 class="mb-3">Database Configuration</h5>
                    <div class="mb-3">
                        <label>Database Host</label>
                        <input type="text" name="db_host" class="form-control" value="127.0.0.1" required>
                    </div>
                    <div class="mb-3">
                        <label>Database Name</label>
                        <input type="text" name="db_name" class="form-control" value="news_portal" required>
                    </div>
                    <div class="mb-3">
                        <label>Database User</label>
                        <input type="text" name="db_user" class="form-control" value="root" required>
                    </div>
                    <div class="mb-3">
                        <label>Database Password</label>
                        <input type="password" name="db_pass" class="form-control">
                    </div>

                    <h5 class="mb-3 mt-4">Site Information</h5>
                    <div class="mb-3">
                        <label>Site Name (Bengali)</label>
                        <input type="text" name="site_name" class="form-control" value="আমার নিউজ" required>
                    </div>

                    <h5 class="mb-3 mt-4">Admin Account</h5>
                    <div class="mb-3">
                        <label>Admin Username</label>
                        <input type="text" name="admin_user" class="form-control" value="admin" required>
                    </div>
                    <div class="mb-3">
                        <label>Admin Email</label>
                        <input type="email" name="admin_email" class="form-control" value="admin@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label>Admin Password</label>
                        <input type="password" name="admin_pass" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" style="background-color: #B62427; border-color: #B62427;">Install Now</button>
                </form>
            <?php elseif ($step === 3): ?>
                <div class="text-center">
                    <div class="alert alert-success">
                        <h4>Installation Successful!</h4>
                        <p>Your news portal has been successfully installed.</p>
                    </div>
                    <p class="text-danger fw-bold">IMPORTANT: For security reasons, please delete or rename install.php before using the site.</p>
                    <a href="index.php" class="btn btn-outline-secondary">Go to Homepage</a>
                    <a href="admin/login.php" class="btn btn-primary" style="background-color: #B62427; border-color: #B62427;">Go to Admin Panel</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
