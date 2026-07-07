<?php
$pageTitle = 'Register';
require_once 'db/connect.php';
require_once 'includes/auth.php';
if (session_status()===PHP_SESSION_NONE) session_start();
if (isLoggedIn()) redirect('dashboard.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password']   ?? '';
    $pass2 = $_POST['password2']  ?? '';
    if (!$name)  $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (strlen($pass) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($pass !== $pass2) $errors[] = 'Passwords do not match.';
    if (!$errors) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=?");
        $stmt->bind_param('s', $email); $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = 'An account with this email already exists.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins  = $conn->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,'student')");
            $ins->bind_param('sss', $name, $email, $hash);
            $ins->execute();
            $uid = $conn->insert_id;
            $_SESSION['user_id'] = $uid;
            $_SESSION['name']    = $name;
            $_SESSION['email']   = $email;
            $_SESSION['role']    = 'student';
            setFlash('success', 'Welcome, ' . $name . '! Your account has been created.');
            redirect('dashboard.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Register — CareerCompass</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body>
<?php
$baseUrl='/career-advisor'; $flash=null;
include 'includes/header.php';
?>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900">
        <span style="color:var(--navy)">Career</span><span style="color:var(--teal)">Compass</span>
      </div>
    </div>
    <h1 class="auth-title">Create your account</h1>
    <p class="auth-sub mb-24">Free access to all career guidance tools.</p>

    <?php if ($errors): ?>
    <div style="background:#FEE2E2;color:#991B1B;padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:18px">
      <?php foreach($errors as $e): ?><div>• <?= h($e) ?></div><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" placeholder="Krunal Prajapati" value="<?= h($_POST['name']??'') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="you@example.com" value="<?= h($_POST['email']??'') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required />
      </div>
      <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password2" class="form-control" placeholder="Repeat password" required />
      </div>
      <button type="submit" class="btn btn-primary btn-block">Create Account</button>
    </form>
    <p style="text-align:center;margin-top:20px;font-size:14px;color:var(--text-muted)">
      Already have an account? <a href="login.php">Login</a>
    </p>
  </div>
</div>
</body></html>
