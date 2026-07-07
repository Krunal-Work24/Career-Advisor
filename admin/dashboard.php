<?php
$pageTitle = 'Dashboard';
require_once 'admin_header.php';

$stats = [
  ['val' => $conn->query("SELECT COUNT(*) as c FROM users WHERE role='student'")->fetch_assoc()['c'],  'lbl' => 'Students',      'icon' => '👥', 'color' => '#DBEAFE'],
  ['val' => $conn->query("SELECT COUNT(*) as c FROM blogs WHERE status='published'")->fetch_assoc()['c'], 'lbl' => 'Published Blogs', 'icon' => '📝', 'color' => '#CCFBF1'],
  ['val' => $conn->query("SELECT COUNT(*) as c FROM career_paths")->fetch_assoc()['c'],               'lbl' => 'Career Paths',  'icon' => '🗺️', 'color' => '#FEF3C7'],
  ['val' => $conn->query("SELECT COUNT(*) as c FROM feedback WHERE status='pending'")->fetch_assoc()['c'], 'lbl' => 'Pending Feedback','icon' => '💬', 'color' => '#FEE2E2'],
  ['val' => $conn->query("SELECT COUNT(*) as c FROM ads WHERE is_active=1")->fetch_assoc()['c'],       'lbl' => 'Active Ads',    'icon' => '📢', 'color' => '#F3E8FF'],
  ['val' => $conn->query("SELECT COUNT(*) as c FROM contact_messages")->fetch_assoc()['c'],            'lbl' => 'Contact Msgs',  'icon' => '📞', 'color' => '#FEF9C3'],
];

$recentFeedback = $conn->query("SELECT * FROM feedback ORDER BY submitted_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$recentUsers    = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
$avgRating      = $conn->query("SELECT AVG(rating) as avg FROM feedback")->fetch_assoc()['avg'];
?>

<div class="flex-between mb-24">
  <div>
    <h1 class="page-title">Admin Dashboard</h1>
    <p class="text-muted mt-8">Welcome back, <?= h($_SESSION['name']) ?>. Here's your overview.</p>
  </div>
  <div class="flex gap-8">
    <a href="blogs.php?action=new" class="btn btn-primary btn-sm">+ New Blog</a>
    <a href="ads.php?action=new"   class="btn btn-gold btn-sm">+ New Ad</a>
  </div>
</div>

<!-- Stats grid -->
<div class="grid-3 mb-32">
  <?php foreach($stats as $s): ?>
  <div class="stat-card" style="background:<?= $s['color'] ?>">
    <div style="font-size:28px;margin-bottom:8px"><?= $s['icon'] ?></div>
    <div class="stat-val" style="font-size:36px"><?= $s['val'] ?></div>
    <div class="stat-lbl"><?= $s['lbl'] ?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Avg Rating highlight -->
<?php if ($avgRating): ?>
<div class="card mb-24" style="background:linear-gradient(135deg,var(--navy),var(--navy-mid));color:#fff;padding:24px 32px">
  <div class="flex-between">
    <div>
      <div style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.5);margin-bottom:6px">Average Student Rating</div>
      <div style="font-family:'Playfair Display',serif;font-size:48px;font-weight:900;color:var(--teal-light)"><?= number_format((float)$avgRating,1) ?> <span style="font-size:28px;color:var(--gold)"><?= str_repeat('★', round($avgRating)) ?></span></div>
    </div>
    <a href="feedback.php" class="btn btn-outline" style="border-color:rgba(255,255,255,.3);color:#fff">View All Feedback</a>
  </div>
</div>
<?php endif; ?>

<div class="grid-2">
  <!-- Recent Feedback -->
  <div class="card">
    <div class="flex-between mb-16">
      <h2 style="font-size:18px;font-weight:600">Recent Feedback</h2>
      <a href="feedback.php" class="btn btn-sm btn-outline">View All</a>
    </div>
    <?php if ($recentFeedback): ?>
      <?php foreach($recentFeedback as $fb): ?>
      <div style="border-bottom:1px solid var(--border);padding:12px 0" class="last-no-border">
        <div class="flex-between mb-4">
          <strong style="font-size:14px"><?= h($fb['name']) ?></strong>
          <span style="color:var(--gold);font-size:13px"><?= str_repeat('★',(int)$fb['rating']) ?></span>
        </div>
        <p style="font-size:13px;color:var(--text-muted)"><?= h(mb_strimwidth($fb['message'],0,90,'…')) ?></p>
        <div class="flex gap-8 mt-6">
          <span class="badge <?= $fb['status']==='reviewed'?'badge-green':'badge-red' ?>"><?= $fb['status'] ?></span>
          <span class="text-sm text-muted"><?= date('d M Y',strtotime($fb['submitted_at'])) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted text-sm">No feedback yet.</p>
    <?php endif; ?>
  </div>

  <!-- Recent Users -->
  <div class="card">
    <div class="flex-between mb-16">
      <h2 style="font-size:18px;font-weight:600">Recent Registrations</h2>
      <a href="users.php" class="btn btn-sm btn-outline">View All</a>
    </div>
    <?php if ($recentUsers): ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr></thead>
        <tbody>
          <?php foreach($recentUsers as $u): ?>
          <tr>
            <td><?= h($u['name']) ?></td>
            <td style="font-size:13px;color:var(--text-muted)"><?= h($u['email']) ?></td>
            <td><span class="badge <?= $u['role']==='admin'?'badge-red':'badge-teal' ?>"><?= $u['role'] ?></span></td>
            <td style="font-size:13px"><?= date('d M Y',strtotime($u['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
      <p class="text-muted text-sm">No users yet.</p>
    <?php endif; ?>
  </div>
</div>

<style>.last-no-border:last-child{border-bottom:none!important}</style>
<?php require_once 'admin_footer.php'; ?>
