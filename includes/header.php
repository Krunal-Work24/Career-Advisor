<?php
require_once __DIR__ . '/../db/connect.php';
require_once __DIR__ . '/../includes/auth.php';

// Fetch active header ad
$today = date('Y-m-d');
$adQuery = "SELECT * FROM ads WHERE position='header_banner' AND is_active=1
            AND (start_date IS NULL OR start_date <= '$today')
            AND (end_date IS NULL OR end_date >= '$today')
            ORDER BY created_at DESC LIMIT 1";
$headerAd = $conn->query($adQuery)->fetch_assoc();
$flash = getFlash();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$isAdmin = isAdmin();
$baseUrl = '/career-advisor';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= isset($pageTitle) ? h($pageTitle) . ' — ' : '' ?>CareerCompass</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css" />
</head>
<body>

<?php if ($headerAd): ?>
<div class="ad-banner ad-header">
  <a href="<?= h($headerAd['link_url']) ?>" target="_blank" rel="noopener">
    <?php if (!empty($headerAd['image_path']) && file_exists(__DIR__ . '/../' . $headerAd['image_path'])): ?>
      <img src="<?= $baseUrl . '/' . h($headerAd['image_path']) ?>" alt="<?= h($headerAd['alt_text']) ?>" />
    <?php else: ?>
      <div class="ad-text-banner">
        <span class="ad-label">Advertisement</span>
        <span class="ad-title-text"><?= h($headerAd['alt_text'] ?: $headerAd['title']) ?></span>
        <span class="ad-cta">Learn More →</span>
      </div>
    <?php endif; ?>
  </a>
  <span class="ad-tag">Ad</span>
</div>
<?php endif; ?>

<nav class="navbar">
  <a href="<?= $baseUrl ?>/index.php" class="nav-brand">
    <span class="brand-mark">Career</span><span class="brand-accent">Compass</span>
  </a>
  <div class="nav-links">
    <a href="<?= $baseUrl ?>/index.php"     class="nav-link <?= $currentPage==='index'?'active':'' ?>">Home</a>
    <a href="<?= $baseUrl ?>/careers.php"   class="nav-link <?= $currentPage==='careers'?'active':'' ?>">Careers</a>
    <a href="<?= $baseUrl ?>/blogs.php"     class="nav-link <?= $currentPage==='blogs'?'active':'' ?>">Blogs</a>
    <a href="<?= $baseUrl ?>/contact.php"   class="nav-link <?= $currentPage==='contact'?'active':'' ?>">Contact</a>
    <?php if ($isAdmin): ?>
      <a href="<?= $baseUrl ?>/admin/dashboard.php" class="btn btn-sm btn-gold">Admin Panel</a>
    <?php endif; ?>
    <?php if (isLoggedIn()): ?>
      <a href="<?= $baseUrl ?>/dashboard.php" class="nav-link <?= $currentPage==='dashboard'?'active':'' ?>">Dashboard</a>
      <a href="<?= $baseUrl ?>/logout.php" class="btn btn-sm btn-outline">Logout</a>
    <?php else: ?>
      <a href="<?= $baseUrl ?>/login.php"    class="btn btn-sm btn-outline">Login</a>
      <a href="<?= $baseUrl ?>/register.php" class="btn btn-sm btn-primary">Register</a>
    <?php endif; ?>
  </div>
  <button class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('open')">☰</button>
</nav>

<?php if ($flash): ?>
<div class="flash flash-<?= h($flash['type']) ?>">
  <?= h($flash['msg']) ?>
</div>
<?php endif; ?>
