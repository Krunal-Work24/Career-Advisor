<?php
$pageTitle = 'Dashboard';
require_once 'db/connect.php';
require_once 'includes/auth.php';
if (session_status()===PHP_SESSION_NONE) session_start();
requireLogin();

$uid = $_SESSION['user_id'];
$name = $_SESSION['name'];

$roadmaps = $conn->query("SELECT * FROM roadmaps WHERE user_id=$uid ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$feedbacks = $conn->query("SELECT * FROM feedback WHERE user_id=$uid ORDER BY submitted_at DESC")->fetch_all(MYSQLI_ASSOC);
$blogCount = $conn->query("SELECT COUNT(*) as c FROM blogs WHERE status='published'")->fetch_assoc()['c'];
$careerCount = $conn->query("SELECT COUNT(*) as c FROM career_paths")->fetch_assoc()['c'];

// Handle new roadmap save
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save_roadmap'])) {
    $title = trim($_POST['rm_title'] ?? 'My Roadmap');
    $steps = trim($_POST['rm_steps'] ?? '');
    $stmt  = $conn->prepare("INSERT INTO roadmaps (user_id,title,steps) VALUES (?,?,?)");
    $stmt->bind_param('iss',$uid,$title,$steps);
    $stmt->execute();
    setFlash('success','Roadmap saved!');
    redirect('dashboard.php');
}
// Handle roadmap delete
if (isset($_GET['del_roadmap'])) {
    $rid = (int)$_GET['del_roadmap'];
    $conn->query("DELETE FROM roadmaps WHERE roadmap_id=$rid AND user_id=$uid");
    setFlash('success','Roadmap deleted.');
    redirect('dashboard.php');
}

require_once 'includes/header.php';
?>

<div class="container section-sm">
  <!-- Welcome -->
  <div class="flex-between mb-24">
    <div>
      <h1 class="page-title">Hello, <?= h($name) ?> 👋</h1>
      <p class="text-muted mt-8">Track your career journey, build roadmaps, and explore paths.</p>
    </div>
    <a href="careers.php" class="btn btn-primary">Explore Careers</a>
  </div>

  <!-- Stats -->
  <div class="grid-4 mb-24">
    <div class="stat-card">
      <div class="stat-val"><?= count($roadmaps) ?></div>
      <div class="stat-lbl">My Roadmaps</div>
    </div>
    <div class="stat-card">
      <div class="stat-val"><?= count($feedbacks) ?></div>
      <div class="stat-lbl">Feedback Submitted</div>
    </div>
    <div class="stat-card">
      <div class="stat-val"><?= $careerCount ?></div>
      <div class="stat-lbl">Career Paths</div>
    </div>
    <div class="stat-card">
      <div class="stat-val"><?= $blogCount ?></div>
      <div class="stat-lbl">Blogs Available</div>
    </div>
  </div>

  <div class="dashboard-wrap">
    <!-- Sidebar nav -->
    <div class="dash-sidebar">
      <div class="dash-nav">
        <a href="dashboard.php"  class="dash-nav-item active">🏠 Dashboard</a>
        <a href="careers.php"    class="dash-nav-item">🗺️ Career Paths</a>
        <a href="blogs.php"      class="dash-nav-item">📝 Blogs</a>
        <a href="feedback.php"   class="dash-nav-item">💬 Submit Feedback</a>
        <a href="contact.php"    class="dash-nav-item">📞 Contact</a>
        <a href="logout.php"     class="dash-nav-item" style="margin-top:12px;border-top:1px solid var(--border)">🚪 Logout</a>
      </div>
    </div>

    <div class="dash-main">
      <!-- Roadmap Builder -->
      <div class="card mb-24">
        <div class="flex-between mb-16">
          <h2 class="page-title" style="font-size:20px">My Roadmaps</h2>
          <button class="btn btn-primary btn-sm" onclick="document.getElementById('rm-form').classList.toggle('hidden')">+ New Roadmap</button>
        </div>

        <!-- New roadmap form -->
        <div id="rm-form" class="hidden" style="background:var(--cream);border-radius:10px;padding:20px;margin-bottom:20px">
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Roadmap Title</label>
              <input type="text" name="rm_title" class="form-control" placeholder="e.g. My Path to Software Engineer" required />
            </div>
            <div class="form-group">
              <label class="form-label">Steps / Milestones</label>
              <textarea name="rm_steps" class="form-control" rows="4" placeholder="Step 1: Complete 12th PCM&#10;Step 2: Appear for JEE&#10;Step 3: Join B.Tech CS&#10;Step 4: Learn DSA &amp; Web Dev&#10;Step 5: Apply for internships"></textarea>
              <div class="form-hint">Write each step on a new line.</div>
            </div>
            <div class="flex gap-8">
              <button type="submit" name="save_roadmap" class="btn btn-primary btn-sm">Save Roadmap</button>
              <button type="button" class="btn btn-sm btn-outline" onclick="document.getElementById('rm-form').classList.add('hidden')">Cancel</button>
            </div>
          </form>
        </div>

        <?php if ($roadmaps): ?>
          <?php foreach($roadmaps as $rm): ?>
          <div class="card-sm mb-16" style="border-radius:10px">
            <div class="flex-between mb-8">
              <strong><?= h($rm['title']) ?></strong>
              <a href="dashboard.php?del_roadmap=<?= $rm['roadmap_id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete this roadmap?">Delete</a>
            </div>
            <?php if ($rm['steps']):
              $steps = explode("\n", trim($rm['steps']));
              foreach($steps as $i=>$step): if(!trim($step)) continue; ?>
              <div class="roadmap-step" style="margin-bottom:14px">
                <div class="step-dot" style="width:30px;height:30px;font-size:13px"><?= $i+1 ?></div>
                <div class="step-content" style="padding-top:4px">
                  <div class="step-title" style="font-size:14px"><?= h(trim($step)) ?></div>
                </div>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
            <div class="text-sm text-muted mt-8">Created: <?= date('d M Y', strtotime($rm['created_at'])) ?></div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted" style="font-size:14px">No roadmaps yet. Create one to plan your career journey step by step.</p>
        <?php endif; ?>
      </div>

      <!-- Quick links -->
      <div class="card">
        <h2 class="page-title mb-16" style="font-size:20px">Quick Actions</h2>
        <div class="grid-3">
          <a href="careers.php?level=After+10th" class="card-sm card-hover text-center" style="display:block;text-decoration:none;border-radius:10px">
            <div style="font-size:32px;margin-bottom:8px">🏫</div>
            <div style="font-weight:600;font-size:14px;color:var(--text)">After 10th Paths</div>
          </a>
          <a href="careers.php?level=After+12th" class="card-sm card-hover text-center" style="display:block;text-decoration:none;border-radius:10px">
            <div style="font-size:32px;margin-bottom:8px">🎓</div>
            <div style="font-weight:600;font-size:14px;color:var(--text)">After 12th Paths</div>
          </a>
          <a href="feedback.php" class="card-sm card-hover text-center" style="display:block;text-decoration:none;border-radius:10px">
            <div style="font-size:32px;margin-bottom:8px">💬</div>
            <div style="font-weight:600;font-size:14px;color:var(--text)">Share Feedback</div>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>.hidden{display:none!important}</style>
<?php require_once 'includes/footer.php'; ?>
