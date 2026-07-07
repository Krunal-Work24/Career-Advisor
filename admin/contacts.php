<?php
$pageTitle = 'Contact Messages';
require_once 'admin_header.php';

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM contact_messages WHERE contact_id=" . (int)$_GET['delete']);
    setFlash('success','Message deleted.');
    redirect('contacts.php');
}

$msgs = $conn->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div class="flex-between mb-24">
  <h1 class="page-title">Contact Messages</h1>
  <span class="badge badge-navy" style="font-size:14px;padding:8px 16px"><?= count($msgs) ?> total</span>
</div>

<div class="table-wrap">
  <table>
    <thead>
      <tr><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th><th></th></tr>
    </thead>
    <tbody>
      <?php if ($msgs): foreach($msgs as $m): ?>
      <tr>
        <td style="font-weight:500"><?= h($m['name']) ?></td>
        <td style="font-size:13px"><a href="mailto:<?= h($m['email']) ?>"><?= h($m['email']) ?></a></td>
        <td style="font-size:13px"><?= h($m['subject']??'-') ?></td>
        <td style="font-size:13px;max-width:260px"><?= h(mb_strimwidth($m['message'],0,100,'…')) ?></td>
        <td style="font-size:13px"><?= date('d M Y',strtotime($m['submitted_at'])) ?></td>
        <td><a href="contacts.php?delete=<?= $m['contact_id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete this message?">Del</a></td>
      </tr>
      <?php endforeach; else: ?>
      <tr><td colspan="6" class="text-center text-muted" style="padding:32px">No contact messages yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once 'admin_footer.php'; ?>
