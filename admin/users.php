<?php
$pageTitle = 'Users';
require_once 'admin_header.php';

// Promote/demote
if (isset($_GET['toggle_role'])) {
    $uid = (int)$_GET['toggle_role'];
    if ($uid !== (int)$_SESSION['user_id']) { // prevent self-demotion
        $user = $conn->query("SELECT role FROM users WHERE user_id=$uid")->fetch_assoc();
        $newRole = $user['role']==='admin'?'student':'admin';
        $conn->query("UPDATE users SET role='$newRole' WHERE user_id=$uid");
        setFlash('success','User role updated.');
    }
    redirect('users.php');
}
// Delete user
if (isset($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    if ($uid !== (int)$_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE user_id=$uid");
        setFlash('success','User deleted.');
    }
    redirect('users.php');
}

$users = $conn->query("SELECT u.*, (SELECT COUNT(*) FROM roadmaps WHERE user_id=u.user_id) as roadmaps, (SELECT COUNT(*) FROM feedback WHERE user_id=u.user_id) as feedbacks FROM users u ORDER BY u.created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div class="flex-between mb-24">
  <div>
    <h1 class="page-title">Users</h1>
    <p class="text-muted mt-8">Manage registered students and administrators.</p>
  </div>
  <span class="badge badge-teal" style="font-size:14px;padding:8px 16px"><?= count($users) ?> registered</span>
</div>

<div class="table-wrap">
  <table>
    <thead>
      <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Roadmaps</th><th>Feedback</th><th>Joined</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php if ($users): foreach($users as $u): $isSelf = $u['user_id']==(int)$_SESSION['user_id']; ?>
      <tr>
        <td style="color:var(--text-muted);font-size:13px"><?= $u['user_id'] ?></td>
        <td style="font-weight:500">
          <?= h($u['name']) ?>
          <?php if($isSelf): ?><span class="badge badge-navy" style="font-size:11px;margin-left:6px">You</span><?php endif; ?>
        </td>
        <td style="font-size:13px;color:var(--text-muted)"><?= h($u['email']) ?></td>
        <td><span class="badge <?= $u['role']==='admin'?'badge-red':'badge-teal' ?>"><?= $u['role'] ?></span></td>
        <td style="text-align:center"><?= $u['roadmaps'] ?></td>
        <td style="text-align:center"><?= $u['feedbacks'] ?></td>
        <td style="font-size:13px"><?= date('d M Y',strtotime($u['created_at'])) ?></td>
        <td>
          <?php if (!$isSelf): ?>
          <div class="flex gap-6">
            <a href="users.php?toggle_role=<?= $u['user_id'] ?>" class="btn btn-sm btn-outline" data-confirm="Change role of <?= h(addslashes($u['name'])) ?>?">
              <?= $u['role']==='admin'?'→ Student':'→ Admin' ?>
            </a>
            <a href="users.php?delete=<?= $u['user_id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete user '<?= h(addslashes($u['name'])) ?>'? This also deletes their roadmaps.">Del</a>
          </div>
          <?php else: ?>
          <span class="text-sm text-muted">—</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; else: ?>
      <tr><td colspan="8" class="text-center text-muted" style="padding:32px">No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'admin_footer.php'; ?>
