    <!-- Footer -->
    <footer class="main-footer">
        <div class="container-custom">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h3 class="site-logo mb-3 text-white"><?= htmlspecialchars($settings['site_name'] ?? 'আমার নিউজ') ?></h3>
                    <p><?= htmlspecialchars($settings['site_tagline'] ?? '') ?></p>
                    <p><i class="fas fa-map-marker-alt me-2"></i> ১২৩, কাজী নজরুল ইসলাম এভিনিউ, ঢাকা</p>
                    <p><i class="fas fa-phone me-2"></i> <?= htmlspecialchars($settings['contact_phone'] ?? '') ?></p>
                    <p><i class="fas fa-envelope me-2"></i> <?= htmlspecialchars($settings['contact_email'] ?? '') ?></p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>গুরুত্বপূর্ণ লিংক</h5>
                    <ul>
                        <li><a href="#">আমাদের সম্পর্কে</a></li>
                        <li><a href="#">যোগাযোগ</a></li>
                        <li><a href="#">বিজ্ঞাপন দিন</a></li>
                        <li><a href="#">শর্তাবলী</a></li>
                        <li><a href="#">গোপনীয়তা নীতি</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>জনপ্রিয় ক্যাটাগরি</h5>
                    <ul>
                        <?php
                        $footer_cats = array_slice($nav_categories, 0, 5);
                        foreach($footer_cats as $cat):
                        ?>
                            <li><a href="category.php?slug=<?= $cat['slug'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="bottom-footer">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['site_name'] ?? 'News Portal') ?>. সর্বস্বত্ব সংরক্ষিত.</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
