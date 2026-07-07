<?php
$pageTitle = 'Login';
require_once 'db/connect.php';
require_once 'includes/auth.php';
if (session_status()===PHP_SESSION_NONE) session_start();
if (isLoggedIn()) redirect(isAdmin() ? 'admin/dashboard.php' : 'dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $stmt  = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param('s', $email); $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['email']   = $user['email'];
        $_SESSION['role']    = $user['role'];
        setFlash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'dashboard.php');
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Login — CareerCompass</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body>
<?php $baseUrl='/career-advisor'; $flash=null; include 'includes/header.php'; ?>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900">
        <span style="color:var(--navy)">Career</span><span style="color:var(--teal)">Compass</span>
      </div>
    </div>
    <h1 class="auth-title">Welcome back</h1>
    <p class="auth-sub mb-24">Login to access your dashboard and roadmaps.</p>

    <?php if ($error): ?>
    <div style="background:#FEE2E2;color:#991B1B;padding:12px 16px;border-radius:8px;font-size:14px;margin-bottom:18px">
      <?= h($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" placeholder="you@example.com" value="<?= h($_POST['email']??'') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Your password" required />
      </div>
      <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
    <p style="text-align:center;margin-top:20px;font-size:14px;color:var(--text-muted)">
      Don't have an account? <a href="register.php">Register Free</a>
    </p>
    <div class="auth-divider mt-24"><span>Admin Login</span></div>
    <p style="text-align:center;font-size:13px;color:var(--text-muted)">
      Use email: <code>admin@careercompass.com</code> / password: <code>password</code>
    </p>
  </div>
</div>
</body></html>
