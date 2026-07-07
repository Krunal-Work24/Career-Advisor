<?php
$pageTitle = 'Career Paths';
require_once 'admin_header.php';

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

// DELETE
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM career_paths WHERE career_id=" . (int)$_GET['delete']);
    setFlash('success','Career path deleted.');
    redirect('careers.php');
}

// SAVE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid   = (int)($_POST['career_id'] ?? 0);
    $f     = ['title','level','stream','description','required_degree','job_roles','further_study','avg_salary','duration'];
    $data  = [];
    foreach($f as $field) $data[$field] = trim($_POST[$field] ?? '');

    if ($cid) {
        $stmt = $conn->prepare("UPDATE career_paths SET title=?,level=?,stream=?,description=?,required_degree=?,job_roles=?,further_study=?,avg_salary=?,duration=? WHERE career_id=?");
        $stmt->bind_param('sssssssssi', $data['title'],$data['level'],$data['stream'],$data['description'],$data['required_degree'],$data['job_roles'],$data['further_study'],$data['avg_salary'],$data['duration'],$cid);
    } else {
        $stmt = $conn->prepare("INSERT INTO career_paths (title,level,stream,description,required_degree,job_roles,further_study,avg_salary,duration) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('sssssssss', $data['title'],$data['level'],$data['stream'],$data['description'],$data['required_degree'],$data['job_roles'],$data['further_study'],$data['avg_salary'],$data['duration']);
    }
    $stmt->execute();
    setFlash('success', $cid ? 'Career path updated!' : 'Career path added!');
    redirect('careers.php');
}

$editCareer = null;
if (($action==='edit') && $editId) {
    $editCareer = $conn->query("SELECT * FROM career_paths WHERE career_id=$editId")->fetch_assoc();
}
$careers = $conn->query("SELECT * FROM career_paths ORDER BY level, title")->fetch_all(MYSQLI_ASSOC);
$levels  = ['After 10th','After 12th','Degree'];
?>

<div class="flex-between mb-24">
  <div>
    <h1 class="page-title">Career Paths</h1>
    <p class="text-muted mt-8">Manage the career graph entries shown to students.</p>
  </div>
  <?php if($action==='list'): ?>
  <a href="careers.php?action=new" class="btn btn-primary">+ Add Career Path</a>
  <?php else: ?>
  <a href="careers.php" class="btn btn-outline">← Back</a>
  <?php endif; ?>
</div>

<?php if ($action==='new' || $action==='edit'): ?>
<div class="card card-lg" style="max-width:720px">
  <h2 style="font-size:20px;font-weight:600;margin-bottom:24px"><?= $action==='edit'?'Edit Career Path':'Add New Career Path' ?></h2>
  <form method="POST">
    <input type="hidden" name="career_id" value="<?= $editCareer['career_id'] ?? 0 ?>" />
    <div class="grid-2">
      <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Title <span style="color:red">*</span></label>
        <input type="text" name="title" class="form-control" placeholder="e.g. Software Engineer" value="<?= h($editCareer['title']??'') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Level <span style="color:red">*</span></label>
        <select name="level" class="form-control" required>
          <?php foreach($levels as $l): ?><option value="<?= $l ?>" <?= ($editCareer['level']??'')===$l?'selected':'' ?>><?= $l ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Stream / Branch</label>
        <input type="text" name="stream" class="form-control" placeholder="e.g. Science (PCM), Commerce" value="<?= h($editCareer['stream']??'') ?>" />
      </div>
      <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of this path…"><?= h($editCareer['description']??'') ?></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Required Degree / Eligibility</label>
        <input type="text" name="required_degree" class="form-control" placeholder="e.g. B.Tech CS / BCA" value="<?= h($editCareer['required_degree']??'') ?>" />
      </div>
      <div class="form-group">
        <label class="form-label">Average Salary</label>
        <input type="text" name="avg_salary" class="form-control" placeholder="e.g. ₹5–20 LPA" value="<?= h($editCareer['avg_salary']??'') ?>" />
      </div>
      <div class="form-group">
        <label class="form-label">Duration</label>
        <input type="text" name="duration" class="form-control" placeholder="e.g. 4 years, Career" value="<?= h($editCareer['duration']??'') ?>" />
      </div>
      <div class="form-group">
        <label class="form-label">Job Roles</label>
        <input type="text" name="job_roles" class="form-control" placeholder="e.g. Developer, Architect, Manager" value="<?= h($editCareer['job_roles']??'') ?>" />
      </div>
      <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Further Study Options</label>
        <input type="text" name="further_study" class="form-control" placeholder="e.g. M.Tech, MBA, MS Abroad" value="<?= h($editCareer['further_study']??'') ?>" />
      </div>
    </div>
    <div class="flex gap-8 mt-8">
      <button type="submit" class="btn btn-primary">Save Career Path</button>
      <a href="careers.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>

<?php else: ?>
<div class="table-wrap">
  <table>
    <thead>
      <tr><th>Title</th><th>Level</th><th>Stream</th><th>Salary</th><th>Duration</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php if ($careers): foreach($careers as $c): ?>
      <tr>
        <td style="font-weight:500"><?= h($c['title']) ?></td>
        <td><span class="badge <?= $c['level']==='After 10th'?'badge-navy':($c['level']==='After 12th'?'badge-teal':'badge-gold') ?>"><?= h($c['level']) ?></span></td>
        <td style="font-size:13px;color:var(--text-muted)"><?= h($c['stream']??'-') ?></td>
        <td style="font-size:13px"><?= h($c['avg_salary']??'-') ?></td>
        <td style="font-size:13px"><?= h($c['duration']??'-') ?></td>
        <td>
          <div class="flex gap-6">
            <a href="careers.php?action=edit&id=<?= $c['career_id'] ?>" class="btn btn-sm btn-outline">Edit</a>
            <a href="careers.php?delete=<?= $c['career_id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete '<?= h(addslashes($c['title'])) ?>'?">Del</a>
          </div>
        </td>
      </tr>
      <?php endforeach; else: ?>
      <tr><td colspan="6" class="text-center text-muted" style="padding:32px">No career paths. <a href="careers.php?action=new">Add one</a>.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require_once 'admin_footer.php'; ?>
