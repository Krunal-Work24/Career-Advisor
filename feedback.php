<?php
$pageTitle = 'Submit Feedback';
require_once 'includes/header.php';

$uid   = $_SESSION['user_id'] ?? null;
$uname = $_SESSION['name']    ?? '';
$uemail= $_SESSION['email']   ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $rating  = (int)($_POST['rating'] ?? 5);
    $message = trim($_POST['message'] ?? '');
    $errors  = [];
    if (!$name)    $errors[] = 'Name is required.';
    if (!$message) $errors[] = 'Feedback message is required.';
    if ($rating < 1 || $rating > 5) $rating = 5;
    if (!$errors) {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id,name,email,rating,message) VALUES (?,?,?,?,?)");
        $stmt->bind_param('issis', $uid, $name, $email, $rating, $message);
        $stmt->execute();
        setFlash('success', 'Thank you for your feedback! It helps us improve.');
        redirect('feedback.php');
    }
}

// Show past feedback (public)
$allFeedback = $conn->query("SELECT * FROM feedback WHERE status='reviewed' OR user_id=" . ($uid ?? 0) . " ORDER BY submitted_at DESC LIMIT 20")->fetch_all(MYSQLI_ASSOC);
$avgRating   = $conn->query("SELECT AVG(rating) as avg FROM feedback")->fetch_assoc()['avg'];
$totalFb     = $conn->query("SELECT COUNT(*) as c FROM feedback")->fetch_assoc()['c'];
?>

<div class="container section">
  <div class="sec-header">
    <h1 class="section-title">Student Feedback</h1>
    <p>Share your experience with CareerCompass to help other students and improve the platform.</p>
  </div>

  <div class="grid-2" style="gap:32px;align-items:flex-start">
    <!-- Form -->
    <div>
      <div class="card card-lg">
        <h2 style="font-size:20px;font-weight:600;margin-bottom:20px">Write Your Feedback</h2>

        <?php if (!empty($errors)): ?>
        <div style="background:#FEE2E2;color:#991B1B;padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:18px">
          <?php foreach($errors as $e): ?><div>• <?= h($e) ?></div><?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST">
          <div class="form-group">
            <label class="form-label">Your Name</label>
            <input type="text" name="name" class="form-control" value="<?= h($_POST['name'] ?? $uname) ?>" placeholder="Full name" required />
          </div>
          <div class="form-group">
            <label class="form-label">Email (optional)</label>
            <input type="email" name="email" class="form-control" value="<?= h($_POST['email'] ?? $uemail) ?>" placeholder="your@email.com" />
          </div>
          <div class="form-group">
            <label class="form-label">Rating</label>
            <div class="star-rating" style="direction:rtl;justify-content:flex-end">
              <?php for($i=5;$i>=1;$i--): ?>
              <input type="radio" id="star<?=$i?>" name="rating" value="<?=$i?>" <?=((int)($_POST['rating']??5))===$i?'checked':''?>>
              <label for="star<?=$i?>" title="<?=$i?> stars">★</label>
              <?php endfor; ?>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Your Feedback</label>
            <textarea name="message" class="form-control" rows="5" placeholder="Share how CareerCompass helped you, what you liked, or what we can improve…" data-maxlength="600" required><?= h($_POST['message'] ?? '') ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Submit Feedback</button>
          <?php if (!isLoggedIn()): ?>
          <p class="form-hint mt-8 text-center"><a href="login.php">Login</a> to link feedback to your account.</p>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <!-- Stats + existing feedback -->
    <div>
      <!-- Stats -->
      <div class="card mb-20" style="text-align:center">
        <div style="font-family:'Playfair Display',serif;font-size:52px;font-weight:900;color:var(--teal)">
          <?= $avgRating ? number_format((float)$avgRating, 1) : '—' ?>
        </div>
        <div style="font-size:24px;color:var(--gold);margin:4px 0">
          <?php $avg = round((float)($avgRating??0)); for($i=1;$i<=5;$i++) echo $i<=$avg?'★':'☆'; ?>
        </div>
        <div class="text-muted text-sm"><?= $totalFb ?> student reviews</div>
      </div>

      <!-- Feedback cards -->
      <?php if ($allFeedback): ?>
        <?php foreach($allFeedback as $fb): ?>
        <div class="feedback-card mb-12">
          <div class="flex-between mb-8">
            <strong style="font-size:14px"><?= h($fb['name']) ?></strong>
            <span class="stars-display"><?= str_repeat('★', (int)$fb['rating']) . str_repeat('☆', 5-(int)$fb['rating']) ?></span>
          </div>
          <p style="font-size:14px;color:var(--text-muted);line-height:1.6"><?= h($fb['message']) ?></p>
          <div class="text-sm text-muted mt-8"><?= date('d M Y', strtotime($fb['submitted_at'])) ?></div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="card text-center" style="padding:32px">
          <div style="font-size:36px;margin-bottom:12px">💬</div>
          <p class="text-muted">Be the first to leave feedback!</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
