<?php
$pageTitle = 'Feedback';
require_once 'admin_header.php';

// Mark reviewed
if (isset($_GET['review'])) {
    $conn->query("UPDATE feedback SET status='reviewed' WHERE feedback_id=" . (int)$_GET['review']);
    setFlash('success','Feedback marked as reviewed.');
    redirect('feedback.php');
}
// Delete
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM feedback WHERE feedback_id=" . (int)$_GET['delete']);
    setFlash('success','Feedback deleted.');
    redirect('feedback.php');
}

$filter = $_GET['filter'] ?? 'all';
$where  = $filter==='pending' ? "WHERE status='pending'" : ($filter==='reviewed'?"WHERE status='reviewed'":'');
$feedbacks = $conn->query("SELECT * FROM feedback $where ORDER BY submitted_at DESC")->fetch_all(MYSQLI_ASSOC);
$pending   = $conn->query("SELECT COUNT(*) as c FROM feedback WHERE status='pending'")->fetch_assoc()['c'];
$avgRating = $conn->query("SELECT AVG(rating) as avg FROM feedback")->fetch_assoc()['avg'];

// Rating distribution
$dist = [];
for($i=1;$i<=5;$i++) $dist[$i] = $conn->query("SELECT COUNT(*) as c FROM feedback WHERE rating=$i")->fetch_assoc()['c'];
$total = array_sum($dist);
?>

<div class="flex-between mb-24">
  <div>
    <h1 class="page-title">Student Feedback</h1>
    <p class="text-muted mt-8">Review and manage feedback submitted by students.</p>
  </div>
  <?php if ($pending > 0): ?>
  <span class="badge badge-red" style="font-size:14px;padding:8px 16px"><?= $pending ?> pending</span>
  <?php endif; ?>
</div>

<!-- Stats row -->
<div class="grid-4 mb-24">
  <div class="stat-card">
    <div class="stat-val"><?= $total ?></div>
    <div class="stat-lbl">Total Feedback</div>
  </div>
  <div class="stat-card">
    <div class="stat-val"><?= $avgRating ? number_format((float)$avgRating,1) : '—' ?></div>
    <div class="stat-lbl">Avg Rating ⭐</div>
  </div>
  <div class="stat-card">
    <div class="stat-val"><?= $pending ?></div>
    <div class="stat-lbl">Pending Review</div>
  </div>
  <div class="stat-card">
    <div class="stat-val"><?= $total - $pending ?></div>
    <div class="stat-lbl">Reviewed</div>
  </div>
</div>

<!-- Rating distribution -->
<?php if ($total > 0): ?>
<div class="card mb-24">
  <h3 style="font-size:16px;font-weight:600;margin-bottom:16px">Rating Distribution</h3>
  <?php for($i=5;$i>=1;$i--): $pct = $total>0?round($dist[$i]/$total*100):0; ?>
  <div class="flex gap-12" style="align-items:center;margin-bottom:10px">
    <span style="font-size:13px;width:40px;text-align:right;color:var(--text-muted)"><?= $i ?>★</span>
    <div class="progress-bar" style="flex:1"><div class="progress-fill" style="width:<?= $pct ?>%"></div></div>
    <span style="font-size:13px;width:44px;color:var(--text-muted)"><?= $dist[$i] ?> (<?= $pct ?>%)</span>
  </div>
  <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Filter tabs -->
<div class="flex gap-8 mb-16">
  <a href="feedback.php" class="btn btn-sm <?= $filter==='all'?'btn-navy':'btn-outline' ?>">All</a>
  <a href="feedback.php?filter=pending"  class="btn btn-sm <?= $filter==='pending'?'btn-navy':'btn-outline' ?>">Pending <?= $pending>0?"($pending)":'' ?></a>
  <a href="feedback.php?filter=reviewed" class="btn btn-sm <?= $filter==='reviewed'?'btn-navy':'btn-outline' ?>">Reviewed</a>
</div>

<!-- Table -->
<div class="table-wrap">
  <table>
    <thead>
      <tr><th>Name</th><th>Rating</th><th>Message</th><th>Status</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php if ($feedbacks): foreach($feedbacks as $fb): ?>
      <tr>
        <td>
          <div style="font-weight:500"><?= h($fb['name']) ?></div>
          <?php if($fb['email']): ?><div style="font-size:12px;color:var(--text-muted)"><?= h($fb['email']) ?></div><?php endif; ?>
        </td>
        <td style="color:var(--gold);font-size:16px"><?= str_repeat('★',(int)$fb['rating']) ?><span style="color:#D1D5DB"><?= str_repeat('★',5-(int)$fb['rating']) ?></span></td>
        <td style="max-width:300px;font-size:13px"><?= h(mb_strimwidth($fb['message'],0,120,'…')) ?></td>
        <td><span class="badge <?= $fb['status']==='reviewed'?'badge-green':'badge-red' ?>"><?= $fb['status'] ?></span></td>
        <td style="font-size:13px"><?= date('d M Y',strtotime($fb['submitted_at'])) ?></td>
        <td>
          <div class="flex gap-6">
            <?php if($fb['status']==='pending'): ?>
            <a href="feedback.php?review=<?= $fb['feedback_id'] ?>" class="btn btn-sm btn-primary">✓ Review</a>
            <?php endif; ?>
            <a href="feedback.php?delete=<?= $fb['feedback_id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete this feedback?">Del</a>
          </div>
        </td>
      </tr>
      <?php endforeach; else: ?>
      <tr><td colspan="6" class="text-center text-muted" style="padding:32px">No feedback found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'admin_footer.php'; ?>
