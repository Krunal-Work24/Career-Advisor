<?php
$pageTitle = 'Manage Blogs';
require_once 'admin_header.php';

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

// DELETE
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM blogs WHERE blog_id=" . (int)$_GET['delete']);
    setFlash('success','Blog deleted.');
    redirect('blogs.php');
}

// SAVE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bid      = (int)($_POST['blog_id'] ?? 0);
    $title    = trim($_POST['title']    ?? '');
    $content  = trim($_POST['content']  ?? '');
    $excerpt  = trim($_POST['excerpt']  ?? '');
    $tags     = trim($_POST['tags']     ?? '');
    $category = trim($_POST['category'] ?? '');
    $emoji    = trim($_POST['cover_emoji'] ?? '📘');
    $status   = $_POST['status'] ?? 'draft';
    $pub_at   = ($status === 'published') ? date('Y-m-d H:i:s') : null;
    $author   = $_SESSION['user_id'];

    if ($bid) {
        // Keep original published_at if already published
        $orig = $conn->query("SELECT published_at,status FROM blogs WHERE blog_id=$bid")->fetch_assoc();
        if ($orig['status']==='published' && $status==='published') $pub_at = $orig['published_at'];
        $stmt = $conn->prepare("UPDATE blogs SET title=?,content=?,excerpt=?,tags=?,category=?,cover_emoji=?,status=?,published_at=? WHERE blog_id=?");
        $stmt->bind_param('ssssssssi', $title,$content,$excerpt,$tags,$category,$emoji,$status,$pub_at,$bid);
    } else {
        $stmt = $conn->prepare("INSERT INTO blogs (author_id,title,content,excerpt,tags,category,cover_emoji,status,published_at) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('issssssss', $author,$title,$content,$excerpt,$tags,$category,$emoji,$status,$pub_at);
    }
    $stmt->execute();
    setFlash('success', $bid ? 'Blog updated!' : 'Blog ' . ($status==='published'?'published':'saved as draft') . '!');
    redirect('blogs.php');
}

$editBlog = null;
if (($action==='edit') && $editId) {
    $editBlog = $conn->query("SELECT * FROM blogs WHERE blog_id=$editId")->fetch_assoc();
}
$blogs = $conn->query("SELECT b.*,u.name as author FROM blogs b JOIN users u ON b.author_id=u.user_id ORDER BY b.created_at DESC")->fetch_all(MYSQLI_ASSOC);
$emojis = ['📘','🎯','🔬','🎓','💼','📊','🏫','🩺','⚖️','🎨','🚀','💡','🌍','🧠'];
?>

<div class="flex-between mb-24">
  <div>
    <h1 class="page-title">Blogs</h1>
    <p class="text-muted mt-8">Create and manage career guidance articles.</p>
  </div>
  <?php if($action==='list'): ?>
  <a href="blogs.php?action=new" class="btn btn-primary">+ New Blog</a>
  <?php else: ?>
  <a href="blogs.php" class="btn btn-outline">← Back to Blogs</a>
  <?php endif; ?>
</div>

<?php if ($action==='new' || $action==='edit'): ?>
<div class="card card-lg">
  <h2 style="font-size:20px;font-weight:600;margin-bottom:24px"><?= $action==='edit'?'Edit Blog':'Write New Blog' ?></h2>
  <form method="POST">
    <input type="hidden" name="blog_id" value="<?= $editBlog['blog_id'] ?? 0 ?>" />
    <div class="grid-2">
      <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Title <span style="color:red">*</span></label>
        <input type="text" name="title" class="form-control" placeholder="e.g. Top 10 Careers After 12th Science" value="<?= h($editBlog['title']??'') ?>" required />
      </div>
      <div class="form-group">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" placeholder="e.g. Career Guidance" value="<?= h($editBlog['category']??'') ?>" list="cat-list" />
        <datalist id="cat-list">
          <option>Career Guidance</option><option>Higher Education</option><option>Skill Development</option><option>Entrance Exams</option><option>Scholarships</option>
        </datalist>
      </div>
      <div class="form-group">
        <label class="form-label">Cover Emoji</label>
        <div class="flex gap-8" style="flex-wrap:wrap;margin-top:6px">
          <?php foreach($emojis as $em): ?>
          <label style="cursor:pointer">
            <input type="radio" name="cover_emoji" value="<?= $em ?>" <?= ($editBlog['cover_emoji']??'📘')===$em?'checked':'' ?> style="display:none" />
            <span style="font-size:28px;padding:4px;border-radius:6px;display:block;transition:all .15s" class="emoji-opt"><?= $em ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Tags (comma separated)</label>
        <input type="text" name="tags" class="form-control" placeholder="MCA, career, 12th, science" value="<?= h($editBlog['tags']??'') ?>" />
      </div>
      <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Excerpt (short summary)</label>
        <textarea name="excerpt" class="form-control" rows="2" placeholder="One or two sentences summarising this article…"><?= h($editBlog['excerpt']??'') ?></textarea>
      </div>
      <div class="form-group" style="grid-column:1/-1">
        <label class="form-label">Content <span style="color:red">*</span></label>
        <textarea name="content" class="form-control" rows="16" placeholder="Write your full article here…" required><?= h($editBlog['content']??'') ?></textarea>
        <div class="form-hint">Plain text. Use new lines for paragraphs.</div>
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          <option value="draft"     <?= ($editBlog['status']??'draft')==='draft'?'selected':'' ?>>Draft (hidden from visitors)</option>
          <option value="published" <?= ($editBlog['status']??'')==='published'?'selected':'' ?>>Published (live now)</option>
        </select>
      </div>
    </div>
    <div class="flex gap-8 mt-8">
      <button type="submit" class="btn btn-primary">Save Blog</button>
      <button type="submit" onclick="document.querySelector('[name=status]').value='published'" class="btn btn-gold">Publish Now</button>
      <a href="blogs.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<style>
input[type=radio][name=cover_emoji]:checked + .emoji-opt { background:var(--teal);box-shadow:0 0 0 2px var(--teal); }
</style>

<?php else: ?>
<div class="table-wrap">
  <table>
    <thead>
      <tr><th>Emoji</th><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php if ($blogs): foreach($blogs as $b): ?>
      <tr>
        <td style="font-size:24px"><?= h($b['cover_emoji']) ?></td>
        <td style="font-weight:500;max-width:260px"><?= h($b['title']) ?></td>
        <td><?= $b['category']?'<span class="badge badge-teal">'.h($b['category']).'</span>':'-' ?></td>
        <td style="font-size:13px;color:var(--text-muted)"><?= h($b['author']) ?></td>
        <td><span class="badge <?= $b['status']==='published'?'badge-green':'badge-gray' ?>"><?= $b['status'] ?></span></td>
        <td style="font-size:13px"><?= date('d M Y',strtotime($b['created_at'])) ?></td>
        <td>
          <div class="flex gap-6">
            <a href="blogs.php?action=edit&id=<?= $b['blog_id'] ?>" class="btn btn-sm btn-outline">Edit</a>
            <?php if($b['status']==='published'): ?>
            <a href="/career-advisor/blog.php?id=<?= $b['blog_id'] ?>" class="btn btn-sm btn-navy" target="_blank">View</a>
            <?php endif; ?>
            <a href="blogs.php?delete=<?= $b['blog_id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete '<?= h(addslashes($b['title'])) ?>'?">Del</a>
          </div>
        </td>
      </tr>
      <?php endforeach; else: ?>
      <tr><td colspan="7" class="text-center text-muted" style="padding:32px">No blogs yet. <a href="blogs.php?action=new">Create one</a>.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php require_once 'admin_footer.php'; ?>
