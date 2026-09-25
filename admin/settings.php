<?php require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed");
    }

    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->execute([$value, $key]);
    }
    $success = "সেটিংস সফলভাবে আপডেট হয়েছে।";
}

$stmt = $pdo->query("SELECT * FROM settings");
$settings_data = $stmt->fetchAll();
$settings = [];
foreach ($settings_data as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}
?>

        <h2>সাধারণ সেটিংস</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <div class="mb-3">
                        <label class="form-label">ওয়েবসাইটের নাম</label>
                        <input type="text" name="settings[site_name]" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ট্যাগলাইন</label>
                        <input type="text" name="settings[site_tagline]" class="form-control" value="<?= htmlspecialchars($settings['site_tagline'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">যোগাযোগের ইমেইল</label>
                        <input type="email" name="settings[contact_email]" class="form-control" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">যোগাযোগের ফোন নম্বর</label>
                        <input type="text" name="settings[contact_phone]" class="form-control" value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">আপডেট করুন</button>
                </form>
            </div>
        </div>

<?php require_once 'includes/footer.php'; ?>
