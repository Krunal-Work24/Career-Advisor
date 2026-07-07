<?php
$pageTitle = 'Home';
require_once 'includes/header.php';
$featured = $conn->query("SELECT * FROM blogs WHERE status='published' ORDER BY published_at DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);
$stats = [
  ['val'=>'14+','lbl'=>'Career Paths'],
  ['val'=>'Free','lbl'=>'Zero Fees'],
  ['val'=>'3','lbl'=>'Education Levels'],
];
?>

<!-- Hero -->
<section class="hero">
  <div class="container hero-inner">
    <p class="hero-sub">A.C.E — Awareness · Clarity · Excellence</p>
    <h1 class="display-title">Your Career Journey<br><span style="color:var(--teal-light)">Starts Here.</span></h1>
    <p class="hero-desc">One-stop personalized career and education advisor for students after 10th, 12th, and graduation. Free, structured, and visual.</p>
    <div class="hero-btns">
      <a href="careers.php" class="btn btn-primary btn-lg">Explore Careers</a>
      <a href="register.php" class="btn btn-lg" style="border-color:rgba(255,255,255,.4);color:#fff;">Get Started Free</a>
    </div>
    <div class="hero-stats">
      <?php foreach($stats as $s): ?>
      <div>
        <div class="hero-stat-val"><?= $s['val'] ?></div>
        <div class="hero-stat-lbl"><?= $s['lbl'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Features -->
<section class="section">
  <div class="container">
    <div class="sec-header">
      <h2 class="section-title">Why CareerCompass?</h2>
      <p>We simplify career decisions with visual guidance, expert blogs, and personalised roadmaps — at zero cost.</p>
    </div>
    <div class="grid-3">
      <?php
      $features = [
        ['🗺️','Graph-Based Navigation','Visual journey from stream → course → specialization → job. No confusion, just clarity.'],
        ['📋','Course & Job Cards','Eligibility, duration, salary range, and future scope for every path.'],
        ['📝','Expert Blogs','Career comparisons, course reviews, and skill guides written by advisors.'],
        ['🎯','Personalized Roadmaps','Build and save your own step-by-step career roadmap.'],
        ['💬','Student Feedback','Share your experience to help fellow students make better decisions.'],
        ['🔓','100% Free','Open access to all content — no paywalls, no hidden fees.'],
      ];
      foreach($features as $f): ?>
      <div class="card card-hover">
        <div style="font-size:36px;margin-bottom:14px"><?= $f[0] ?></div>
        <h3 style="font-size:16px;font-weight:600;margin-bottom:8px"><?= $f[1] ?></h3>
        <p style="font-size:14px;color:var(--text-muted);line-height:1.6"><?= $f[2] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Career Graph Preview -->
<section class="section" style="background:var(--white);padding:60px 0">
  <div class="container">
    <div class="sec-header">
      <h2 class="section-title">Career Path Explorer</h2>
      <p>See how education levels connect to career options at a glance.</p>
    </div>
    <div class="card" style="overflow-x:auto">
      <div class="graph-wrap">
        <div class="graph-level">
          <span class="graph-node node-root">10th Pass</span>
        </div>
        <div class="graph-arrow">↓</div>
        <div class="graph-level">
          <span class="graph-node node-stream">Science (PCM)</span>
          <span class="graph-node node-stream">Science (PCB)</span>
          <span class="graph-node node-stream">Commerce</span>
          <span class="graph-node node-stream">Arts</span>
        </div>
        <div class="graph-arrow">↓</div>
        <div class="graph-level">
          <span class="graph-node node-course">B.Tech</span>
          <span class="graph-node node-course">MBBS</span>
          <span class="graph-node node-course">BCA</span>
          <span class="graph-node node-course">B.Com</span>
          <span class="graph-node node-course">BBA</span>
          <span class="graph-node node-course">BA/B.Sc</span>
        </div>
        <div class="graph-arrow">↓</div>
        <div class="graph-level">
          <span class="graph-node node-job">Software Engineer</span>
          <span class="graph-node node-job">Doctor</span>
          <span class="graph-node node-job">Data Scientist</span>
          <span class="graph-node node-job">CA</span>
          <span class="graph-node node-job">MBA Manager</span>
        </div>
      </div>
      <div class="mt-24 text-center">
        <a href="careers.php" class="btn btn-primary">Explore All Paths →</a>
      </div>
    </div>
  </div>
</section>

<!-- Latest Blogs -->
<?php if ($featured): ?>
<section class="section">
  <div class="container">
    <div class="flex-between mb-24">
      <h2 class="section-title">Latest from the Blog</h2>
      <a href="blogs.php" class="btn btn-outline">View All</a>
    </div>
    <div class="grid-3">
      <?php foreach($featured as $b): ?>
      <a href="blog.php?id=<?= $b['blog_id'] ?>" class="blog-card" style="text-decoration:none;color:inherit">
        <div class="blog-thumb"><?= h($b['cover_emoji']) ?></div>
        <div class="blog-body">
          <div class="blog-title"><?= h($b['title']) ?></div>
          <div class="blog-excerpt"><?= h(mb_strimwidth($b['excerpt'] ?? strip_tags($b['content']), 0, 110, '…')) ?></div>
          <div class="blog-meta">
            <span class="badge badge-teal"><?= h($b['category']) ?></span>
            <span><?= date('d M Y', strtotime($b['published_at'])) ?></span>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="section" style="background:linear-gradient(135deg,var(--navy),var(--navy-mid));color:#fff;margin-top:0">
  <div class="container text-center">
    <h2 style="font-family:'Playfair Display',serif;font-size:34px;font-weight:900;margin-bottom:14px">
      Ready to plan your future?
    </h2>
    <p style="color:rgba(255,255,255,.7);font-size:16px;margin-bottom:28px">
      Create a free account, explore career paths, and build your personalized roadmap today.
    </p>
    <a href="register.php" class="btn btn-gold btn-lg">Create Free Account</a>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
