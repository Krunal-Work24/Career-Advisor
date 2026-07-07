<?php
$pageTitle = 'Ads Manager';
require_once 'admin_header.php';

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

// ── DELETE ─────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $row = $conn->query("SELECT image_path FROM ads WHERE ad_id=$did")->fetch_assoc();
    if ($row && $row['image_path'] && file_exists(__DIR__.'/../'.$row['image_path'])) {
        unlink(__DIR__.'/../'.$row['image_path']);
    }
    $conn->query("DELETE FROM ads WHERE ad_id=$did");
    setFlash('success','Ad deleted.');
    redirect('ads.php');
}

// ── TOGGLE ACTIVE ──────────────────────────────────────────
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    $conn->query("UPDATE ads SET is_active = NOT is_active WHERE ad_id=$tid");
    setFlash('success','Ad status updated.');
    redirect('ads.php');
}

// ── SAVE (create / update) ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title      = trim($_POST['title']    ?? '');
    $link_url   = trim($_POST['link_url'] ?? '');
    $alt_text   = trim($_POST['alt_text'] ?? '');
    $position   = $_POST['position']  ?? 'header_banner';
    $is_active  = isset($_POST['is_active']) ? 1 : 0;
    $start_date = $_POST['start_date'] ?: null;
    $end_date   = $_POST['end_date']   ?: null;
    $ad_id      = (int)($_POST['ad_id'] ?? 0);

    // Handle image upload
    $image_path = $_POST['existing_image'] ?? '';
    if (!empty($_FILES['image']['name'])) {
        $ext      = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg','jpeg','png','gif','webp'];
        if (in_array($ext, $allowed)) {
            $filename   = 'ad_' . time() . '_' . rand(100,999) . '.' . $ext;
            $uploadDir  = __DIR__ . '/../uploads/ads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                // Remove old image
                if ($image_path && file_exists(__DIR__.'/../'.$image_path)) unlink(__DIR__.'/../'.$image_path);
                $image_path = 'uploads/ads/' . $filename;
            }
        }
    }

    if ($ad_id) {
        $stmt = $conn->prepare("UPDATE ads SET title=?,image_path=?,link_url=?,alt_text=?,position=?,is_active=?,start_date=?,end_date=? WHERE ad_id=?");
        $stmt->bind_param('sssssissi', $title,$image_path,$link_url,$alt_text,$position,$is_active,$start_date,$end_date,$ad_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO ads (title,image_path,link_url,alt_text,position,is_active,start_date,end_date) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param('sssssiis', $title,$image_path,$link_url,$alt_text,$position,$is_active,$start_date,$end_date);
    }
    $stmt->execute();
    setFlash('success', $ad_id ? 'Ad updated!' : 'Ad created and is now live!');
    redirect('ads.php');
}

// ── EDIT: fetch existing ──────────────────────────────────
$editAd = null;
if ($action === 'edit' && $editId) {
    $editAd = $conn->query("SELECT * FROM ads WHERE ad_id=$editId")->fetch_assoc();
}

// ── LIST ──────────────────────────────────────────────────
$ads = $conn->query("SELECT * FROM ads ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div class="flex-between mb-24">
  <div>
    <h1 class="page-title">Ads Manager</h1>
    <p class="text-muted mt-8">Manage header and footer advertisements shown to all visitors.</p>
  </div>
  <?php if($action==='list'): ?>
  <a href="ads.php?action=new" class="btn btn-gold">+ Create New Ad</a>
  <?php else: ?>
  <a href="ads.php" class="btn btn-outline">← Back to Ads</a>
  <?php endif; ?>
</div>

<?php if ($action === 'new' || $action === 'edit'): ?>
<!-- ── CREATE / EDIT FORM ─────────────────────────────── -->
<div class="card card-lg" style="max-width:680px">
  <h2 style="font-size:20px;font-weight:600;margin-bottom:24px"><?= $action==='edit'?'Edit Ad':'Create New Ad' ?></h2>

  <div style="background:#FEF3C7;border-radius:10px;padding:14px 18px;margin-bottom:24px;font-size:14px">
    <strong>💡 How ads work:</strong> Active ads with valid dates appear automatically in the header or footer of <em>every</em> page. 
    You can upload an image banner or use text-only mode (just fill Title &amp; Alt Text).
  </div>

  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="ad_id" value="<?= $editAd['ad_id'] ?? 0 ?>">
    <input type="hidden" name="existing_image" value="<?= h($editAd['image_path'] ?? '') ?>">

    <div class="form-group">
      <label class="form-label">Ad Title <span style="color:red">*</span></label>
      <input type="text" name="title" class="form-control" placeholder="e.g. Summer Course Promo" value="<?= h($editAd['title'] ?? '') ?>" required />
      <div class="form-hint">Internal label — not shown to visitors.</div>
    </div>

    <div class="form-group">
      <label class="form-label">Position <span style="color:red">*</span></label>
      <select name="position" class="form-control">
        <option value="header_banner" <?= ($editAd['position']??'')==='header_banner'?'selected':'' ?>>🔝 Header Banner (top of every page)</option>
        <option value="footer_banner" <?= ($editAd['position']??'')==='footer_banner'?'selected':'' ?>>🔻 Footer Banner (bottom of every page)</option>
      </select>
    </div>

    <div class="form-group">
      <label class="form-label">Banner Image (optional)</label>
      <?php if (!empty($editAd['image_path'])): ?>
      <div style="margin-bottom:10px;padding:10px;background:var(--cream);border-radius:8px">
        <div style="font-size:13px;color:var(--text-muted);margin-bottom:6px">Current image:</div>
        <img src="/career-advisor/<?= h($editAd['image_path']) ?>" style="max-height:70px;border-radius:6px" alt="current ad" />
      </div>
      <?php endif; ?>
      <input type="file" name="image" class="form-control" accept="image/*" />
      <div class="form-hint">Recommended: 1200×90px PNG/JPG. Leave blank to use text-only mode.</div>
    </div>

    <div class="form-group">
      <label class="form-label">Display Text / Alt Text</label>
      <input type="text" name="alt_text" class="form-control" placeholder="e.g. Special Offer — Click to Learn More" value="<?= h($editAd['alt_text'] ?? '') ?>" />
      <div class="form-hint">Shown as the ad headline in text-only mode, and as image alt text when image is uploaded.</div>
    </div>

    <div class="form-group">
      <label class="form-label">Click-Through URL</label>
      <input type="url" name="link_url" class="form-control" placeholder="https://example.com/offer" value="<?= h($editAd['link_url'] ?? '') ?>" />
    </div>

    <div class="grid-2">
      <div class="form-group">
        <label class="form-label">Start Date (optional)</label>
        <input type="date" name="start_date" class="form-control" value="<?= h($editAd['start_date'] ?? '') ?>" />
      </div>
      <div class="form-group">
        <label class="form-label">End Date (optional)</label>
        <input type="date" name="end_date" class="form-control" value="<?= h($editAd['end_date'] ?? '') ?>" />
      </div>
    </div>
    <div class="form-hint mb-16" style="margin-top:-10px">Leave dates empty to run the ad indefinitely.</div>

    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-bottom:24px">
      <input type="checkbox" name="is_active" value="1" <?= ($editAd['is_active']??1)?'checked':'' ?> style="width:16px;height:16px" />
      <span style="font-size:14px;font-weight:500">Active — show this ad immediately</span>
    </label>

    <div class="flex gap-8">
      <button type="submit" class="btn btn-primary"><?= $action==='edit'?'Update Ad':'Publish Ad' ?></button>
      <a href="ads.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>

<?php else: ?>
<!-- ── ADS LIST ───────────────────────────────────────── -->
<?php if ($ads): ?>
<div class="grid-2">
  <?php foreach($ads as $ad): ?>
  <div class="card" style="border-left:4px solid <?= $ad['is_active']?'var(--teal)':'var(--border)' ?>">
    <!-- Preview -->
    <div style="background:var(--navy);border-radius:8px;padding:12px 16px;margin-bottom:16px;min-height:60px;display:flex;align-items:center;justify-content:center">
      <?php if (!empty($ad['image_path']) && file_exists(__DIR__.'/../'.$ad['image_path'])): ?>
        <img src="/career-advisor/<?= h($ad['image_path']) ?>" style="max-height:56px;border-radius:6px" alt="ad preview" />
      <?php else: ?>
        <div style="text-align:center">
          <div style="font-size:11px;color:rgba(255,255,255,.4);letter-spacing:1px;text-transform:uppercase">Ad Preview</div>
          <div style="color:#fff;font-size:14px;font-weight:500;margin-top:4px"><?= h($ad['alt_text'] ?: $ad['title']) ?></div>
        </div>
      <?php endif; ?>
    </div>

    <div class="flex-between mb-8">
      <strong><?= h($ad['title']) ?></strong>
      <span class="badge <?= $ad['is_active']?'badge-green':'badge-red' ?>"><?= $ad['is_active']?'Active':'Inactive' ?></span>
    </div>
    <div style="font-size:13px;color:var(--text-muted);margin-bottom:12px">
      <div>📌 <?= $ad['position']==='header_banner'?'Header Banner':'Footer Banner' ?></div>
      <?php if ($ad['link_url']): ?><div>🔗 <?= h(mb_strimwidth($ad['link_url'],0,45,'…')) ?></div><?php endif; ?>
      <?php if ($ad['start_date']||$ad['end_date']): ?>
      <div>📅 <?= $ad['start_date']?date('d M Y',strtotime($ad['start_date'])):'—' ?> → <?= $ad['end_date']?date('d M Y',strtotime($ad['end_date'])):'∞' ?></div>
      <?php endif; ?>
    </div>
    <div class="flex gap-8" style="flex-wrap:wrap">
      <a href="ads.php?action=edit&id=<?= $ad['ad_id'] ?>" class="btn btn-sm btn-outline">✏ Edit</a>
      <a href="ads.php?toggle=<?= $ad['ad_id'] ?>" class="btn btn-sm <?= $ad['is_active']?'btn-navy':'btn-primary' ?>">
        <?= $ad['is_active']?'⏸ Deactivate':'▶ Activate' ?>
      </a>
      <a href="ads.php?delete=<?= $ad['ad_id'] ?>" class="btn btn-sm btn-danger" data-confirm="Delete this ad permanently?">🗑 Delete</a>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div class="card text-center" style="padding:48px">
  <div style="font-size:48px;margin-bottom:16px">📢</div>
  <h3>No ads created yet</h3>
  <p class="text-muted mt-8">Create your first ad to show it in the header or footer of every page.</p>
  <a href="ads.php?action=new" class="btn btn-gold mt-16">+ Create First Ad</a>
</div>
<?php endif; ?>
<?php endif; ?>

<?php require_once 'admin_footer.php'; ?>
