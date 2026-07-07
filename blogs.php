<?php
$pageTitle = 'Blogs';
require_once 'includes/header.php';

$search = trim($_GET['q'] ?? '');
$cat    = trim($_GET['cat'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;
$offset  = ($page - 1) * $perPage;

$where = "WHERE status='published'";
if ($search) $where .= " AND (title LIKE '%" . $conn->real_escape_string($search) . "%' OR tags LIKE '%" . $conn->real_escape_string($search) . "%')";
if ($cat)    $where .= " AND category='" . $conn->real_escape_string($cat) . "'";

$total   = $conn->query("SELECT COUNT(*) as c FROM blogs $where")->fetch_assoc()['c'];
$pages   = ceil($total / $perPage);
$blogs   = $conn->query("SELECT b.*, u.name as author_name FROM blogs b JOIN users u ON b.author_id=u.user_id $where ORDER BY published_at DESC LIMIT $perPage OFFSET $offset")->fetch_all(MYSQLI_ASSOC);
$cats    = $conn->query("SELECT DISTINCT category FROM blogs WHERE status='published' AND category IS NOT NULL")->fetch_all(MYSQLI_ASSOC);
?>

<div class="container section">
  <div class="sec-header">
    <h1 class="section-title">Career Blog</h1>
    <p>Expert guidance on courses, careers, and education planning — completely free.</p>
  </div>

  <!-- Search & filter -->
  <div class="card mb-24" style="padding:16px 20px">
    <form method="GET" class="flex gap-12" style="flex-wrap:wrap;align-items:flex-end">
      <div style="flex:1;min-width:200px">
        <label class="form-label">Search</label>
        <input type="text" name="q" class="form-control" placeholder="e.g. MCA, Science, career after 12th…" value="<?= h($search) ?>" />
      </div>
      <?php if ($cats): ?>
      <div>
        <label class="form-label">Category</label>
        <select name="cat" class="form-control">
          <option value="">All Categories</option>
          <?php foreach($cats as $c): ?>
          <option value="<?= h($c['category']) ?>" <?= $cat===$c['category']?'selected':'' ?>><?= h($c['category']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>
      <button type="submit" class="btn btn-primary">Search</button>
      <?php if($search||$cat): ?>
      <a href="blogs.php" class="btn btn-outline">Clear</a>
      <?php endif; ?>
    </form>
  </div>

  <?php if ($blogs): ?>
  <div class="grid-3">
    <?php foreach($blogs as $b): ?>
    <a href="blog.php?id=<?= $b['blog_id'] ?>" class="blog-card" style="text-decoration:none;color:inherit">
      <div class="blog-thumb"><?= h($b['cover_emoji'] ?: '📘') ?></div>
      <div class="blog-body">
        <div class="blog-title"><?= h($b['title']) ?></div>
        <div class="blog-excerpt"><?= h(mb_strimwidth($b['excerpt'] ?: strip_tags($b['content']), 0, 120, '…')) ?></div>
        <div class="blog-meta">
          <?php if($b['category']): ?><span class="badge badge-teal"><?= h($b['category']) ?></span><?php endif; ?>
          <span><?= date('d M Y', strtotime($b['published_at'])) ?></span>
          <span>by <?= h($b['author_name']) ?></span>
        </div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if ($pages > 1): ?>
  <div class="pagination">
    <?php for($p=1;$p<=$pages;$p++): ?>
    <a href="?page=<?=$p?>&q=<?=urlencode($search)?>&cat=<?=urlencode($cat)?>" class="page-link <?=$p===$page?'active':''?>"><?=$p?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>

  <?php else: ?>
  <div class="card text-center" style="padding:48px">
    <div style="font-size:48px;margin-bottom:16px">📝</div>
    <h3>No blogs found</h3>
    <p class="text-muted mt-8">Try a different search or check back soon.</p>
    <a href="blogs.php" class="btn btn-primary mt-16">View All Blogs</a>
  </div>
  <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
