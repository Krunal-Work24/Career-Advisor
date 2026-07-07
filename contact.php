<?php
$pageTitle = 'Contact';
require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $errors  = [];
    if (!$name)    $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (!$message) $errors[] = 'Message is required.';
    if (!$errors) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name,email,subject,message) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $name, $email, $subject, $message);
        $stmt->execute();
        setFlash('success', 'Message sent! We will get back to you soon.');
        redirect('contact.php');
    }
}
?>

<div class="container section">
  <div class="sec-header">
    <h1 class="section-title">Get in Touch</h1>
    <p>Have a question about career guidance or the platform? We'd love to hear from you.</p>
  </div>

  <div class="grid-2" style="max-width:900px;margin:0 auto;gap:40px;align-items:flex-start">
    <div class="card card-lg">
      <h2 style="font-size:20px;font-weight:600;margin-bottom:20px">Send a Message</h2>
      <?php if (!empty($errors)): ?>
      <div style="background:#FEE2E2;color:#991B1B;padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:18px">
        <?php foreach($errors as $e): ?><div>• <?= h($e) ?></div><?php endforeach; ?>
      </div>
      <?php endif; ?>
      <form method="POST">
        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" value="<?= h($_POST['name']??$_SESSION['name']??'') ?>" required />
        </div>
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control" value="<?= h($_POST['email']??$_SESSION['email']??'') ?>" required />
        </div>
        <div class="form-group">
          <label class="form-label">Subject</label>
          <input type="text" name="subject" class="form-control" placeholder="e.g. Career query, Bug report, Suggestion" value="<?= h($_POST['subject']??'') ?>" />
        </div>
        <div class="form-group">
          <label class="form-label">Message</label>
          <textarea name="message" class="form-control" rows="5" placeholder="Your message…" required><?= h($_POST['message']??'') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Send Message</button>
      </form>
    </div>

    <div>
      <div class="card mb-20">
        <h3 style="font-size:16px;font-weight:600;margin-bottom:16px">About CareerCompass</h3>
        <p style="font-size:14px;color:var(--text-muted);line-height:1.7">
          CareerCompass is a free, student-first career and education guidance platform built as part of MCA Mini Project — I at S.K. Patel Institute of Management & Computer Studies, Kadi Sarva Vishwavidyalaya.
        </p>
        <div class="mt-16 flex gap-8" style="flex-direction:column">
          <div style="font-size:14px">🎓 <strong>Institute:</strong> SKPIMCS</div>
          <div style="font-size:14px">📚 <strong>University:</strong> KSV</div>
          <div style="font-size:14px">🧑‍💻 <strong>Team:</strong> Krunal Prajapati &amp; Himanshu Vaghela</div>
        </div>
      </div>
      <div class="card" style="background:linear-gradient(135deg,var(--navy),var(--navy-mid));color:#fff">
        <h3 style="font-size:16px;font-weight:600;margin-bottom:10px;color:#fff">Philosophy: A.C.E.</h3>
        <p style="font-size:14px;line-height:1.8;color:rgba(255,255,255,.75)">
          <strong style="color:var(--teal-light)">A</strong>wareness — Know your options<br>
          <strong style="color:var(--teal-light)">C</strong>larity — Understand your path<br>
          <strong style="color:var(--teal-light)">E</strong>xcellence — Achieve your goals
        </p>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
