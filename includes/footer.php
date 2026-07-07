<?php
// Fetch active footer ad
$today = date('Y-m-d');
$footerAdQ = "SELECT * FROM ads WHERE position='footer_banner' AND is_active=1
              AND (start_date IS NULL OR start_date <= '$today')
              AND (end_date IS NULL OR end_date >= '$today')
              ORDER BY created_at DESC LIMIT 1";
$footerAd = $conn->query($footerAdQ)->fetch_assoc();
?>

<?php if ($footerAd): ?>
<div class="ad-banner ad-footer">
  <a href="<?= h($footerAd['link_url']) ?>" target="_blank" rel="noopener">
    <?php if (!empty($footerAd['image_path']) && file_exists(__DIR__ . '/../' . $footerAd['image_path'])): ?>
      <img src="/career-advisor/<?= h($footerAd['image_path']) ?>" alt="<?= h($footerAd['alt_text']) ?>" />
    <?php else: ?>
      <div class="ad-text-banner">
        <span class="ad-label">Advertisement</span>
        <span class="ad-title-text"><?= h($footerAd['alt_text'] ?: $footerAd['title']) ?></span>
        <span class="ad-cta">Explore Now →</span>
      </div>
    <?php endif; ?>
  </a>
  <span class="ad-tag">Ad</span>
</div>
<?php endif; ?>

<footer class="footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <div class="brand-mark-lg">CareerCompass</div>
      <p>Empowering students with clarity, awareness, and excellence in career planning.</p>
    </div>
    <div class="footer-links">
      <div>
        <h4>Explore</h4>
        <a href="/career-advisor/careers.php">Career Paths</a>
        <a href="/career-advisor/blogs.php">Blogs</a>
        <a href="/career-advisor/contact.php">Contact</a>
      </div>
      <div>
        <h4>Account</h4>
        <a href="/career-advisor/login.php">Login</a>
        <a href="/career-advisor/register.php">Register</a>
        <?php if(isLoggedIn()): ?>
        <a href="/career-advisor/feedback.php">Submit Feedback</a>
        <?php endif; ?>
      </div>
      <div>
        <h4>Philosophy</h4>
        <p style="font-size:13px;color:#94a3b8;line-height:1.7">
          <strong style="color:#e2e8f0">A</strong>wareness<br>
          <strong style="color:#e2e8f0">C</strong>larity<br>
          <strong style="color:#e2e8f0">E</strong>xcellence
        </p>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© <?= date('Y') ?> CareerCompass. Free &amp; Open Access.</span>
    <span>Built with ❤ for students everywhere.</span>
  </div>
</footer>
<script src="/career-advisor/assets/js/main.js"></script>
</body>
</html>
