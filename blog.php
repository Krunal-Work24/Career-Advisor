<?php
require_once 'db/connect.php';
require_once 'includes/auth.php';
if (session_status()===PHP_SESSION_NONE) session_start();

$id   = (int)($_GET['id'] ?? 0);
$blog = $conn->query("SELECT b.*,u.name as author_name FROM blogs b JOIN users u ON b.author_id=u.user_id WHERE b.blog_id=$id AND b.status='published'")->fetch_assoc();
if (!$blog) { header('Location: blogs.php'); exit; }

$pageTitle = $blog['title'];
$related   = $conn->query("SELECT * FROM blogs WHERE status='published' AND blog_id<>$id AND category='" . $conn->real_escape_string($blog['category']) . "' LIMIT 3")->fetch_all(MYSQLI_ASSOC);

require_once 'includes/header.php';
?>
<div class="container" style="padding-top:40px;padding-bottom:60px;max-width:800px">
  <!-- Breadcrumb -->
  <div class="text-sm text-muted mb-16">
    <a href="blogs.php">Blogs</a> › <?= h(mb_strimwidth($blog['title'],0,50,'…')) ?>
  </div>

  <!-- Category & date -->
  <div class="flex gap-8 mb-16">
    <?php if($blog['category']): ?><span class="badge badge-teal"><?= h($blog['category']) ?></span><?php endif; ?>
    <span class="text-sm text-muted"><?= date('d F Y', strtotime($blog['published_at'])) ?> · by <?= h($blog['author_name']) ?></span>
  </div>

  <h1 style="font-family:'Playfair Display',serif;font-size:clamp(26px,4vw,38px);font-weight:900;line-height:1.2;margin-bottom:24px">
    <?= h($blog['title']) ?>
  </h1>

  <?php if($blog['excerpt']): ?>
  <p style="font-size:18px;color:var(--text-muted);line-height:1.7;margin-bottom:28px;border-left:4px solid var(--teal);padding-left:16px">
    <?= h($blog['excerpt']) ?>
  </p>
  <?php endif; ?>

  <div style="font-size:16px;line-height:1.85;color:#1e293b">
    <?= nl2br(h($blog['content'])) ?>
  </div>

  <?php if($blog['tags']): ?>
  <div class="mt-24 flex gap-8" style="flex-wrap:wrap">
    <?php foreach(explode(',',$blog['tags']) as $tag): ?>
    <a href="blogs.php?q=<?= urlencode(trim($tag)) ?>" class="badge badge-gray" style="text-decoration:none">#<?= h(trim($tag)) ?></a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Related -->
  <?php if ($related): ?>
  <div class="mt-48">
    <h2 style="font-family:'Playfair Display',serif;font-size:22px;margin-bottom:20px">Related Articles</h2>
    <div class="grid-3">
      <?php foreach($related as $r): ?>
      <a href="blog.php?id=<?= $r['blog_id'] ?>" class="blog-card" style="text-decoration:none;color:inherit">
        <div class="blog-thumb" style="height:90px;font-size:32px"><?= h($r['cover_emoji']) ?></div>
        <div class="blog-body">
          <div class="blog-title" style="font-size:14px"><?= h($r['title']) ?></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="mt-32">
    <a href="blogs.php" class="btn btn-outline">← Back to Blogs</a>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
