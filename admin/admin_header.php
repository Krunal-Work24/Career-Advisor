<?php
require_once __DIR__ . '/../db/connect.php';
require_once __DIR__ . '/../includes/auth.php';
if (session_status()===PHP_SESSION_NONE) session_start();
requireAdmin('../login.php');
$flash = getFlash();
$currentAdmin = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title><?= isset($pageTitle)?h($pageTitle).' — ':'' ?>Admin · CareerCompass</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="/career-advisor/assets/css/style.css"/>
</head>
<body>

<div style="background:var(--navy);color:#fff;padding:14px 24px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:300">
  <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:var(--teal-light)">
    ⚙ CareerCompass Admin
  </div>
  <div class="flex gap-12">
    <a href="/career-advisor/index.php" style="color:rgba(255,255,255,.6);font-size:13px;text-decoration:none">← View Site</a>
    <a href="/career-advisor/logout.php" class="btn btn-sm btn-outline" style="border-color:rgba(255,255,255,.3);color:#fff;font-size:13px">Logout</a>
  </div>
</div>

<?php if ($flash): ?>
<div class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['msg']) ?></div>
<?php endif; ?>

<div class="admin-wrap">
  <div class="admin-sidebar">
    <div class="admin-sidebar-title">Menu</div>
    <a href="/career-advisor/admin/dashboard.php"  class="admin-nav-item <?= $currentAdmin==='dashboard'?'active':'' ?>">📊 Dashboard</a>
    <a href="/career-advisor/admin/blogs.php"       class="admin-nav-item <?= $currentAdmin==='blogs'?'active':'' ?>">📝 Blogs</a>
    <a href="/career-advisor/admin/careers.php"     class="admin-nav-item <?= $currentAdmin==='careers'?'active':'' ?>">🗺️ Career Paths</a>
    <a href="/career-advisor/admin/feedback.php"    class="admin-nav-item <?= $currentAdmin==='feedback'?'active':'' ?>">💬 Feedback</a>
    <a href="/career-advisor/admin/ads.php"         class="admin-nav-item <?= $currentAdmin==='ads'?'active':'' ?>">📢 Ads Manager</a>
    <a href="/career-advisor/admin/contacts.php"    class="admin-nav-item <?= $currentAdmin==='contacts'?'active':'' ?>">📞 Contact Msgs</a>
    <a href="/career-advisor/admin/users.php"       class="admin-nav-item <?= $currentAdmin==='users'?'active':'' ?>">👥 Users</a>
  </div>
  <div class="admin-content">
