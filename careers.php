<?php
$pageTitle = 'Career Paths';
require_once 'includes/header.php';

$level = $_GET['level'] ?? 'all';
$search = trim($_GET['q'] ?? '');

$where = "WHERE 1=1";
if ($level !== 'all') $where .= " AND level='" . $conn->real_escape_string($level) . "'";
if ($search) $where .= " AND (title LIKE '%{$conn->real_escape_string($search)}%' OR stream LIKE '%{$conn->real_escape_string($search)}%' OR job_roles LIKE '%{$conn->real_escape_string($search)}%')";

$careers = $conn->query("SELECT * FROM career_paths $where ORDER BY level, title")->fetch_all(MYSQLI_ASSOC);

$levels = ['After 10th','After 12th','Degree'];
$levelColors = ['After 10th'=>'badge-navy','After 12th'=>'badge-teal','Degree'=>'badge-gold'];
?>

<div class="container section">
  <div class="sec-header">
    <h1 class="section-title">Career Path Explorer</h1>
    <p>Visual, structured guidance from 10th grade all the way to professional success.</p>
  </div>

  <!-- Filters -->
  <div class="card mb-24" style="padding:16px 20px">
    <form method="GET" class="flex gap-12" style="flex-wrap:wrap;align-items:flex-end">
      <div style="flex:1;min-width:200px">
        <label class="form-label">Search</label>
        <input type="text" name="q" class="form-control" placeholder="e.g. Software Engineer, MBBS…" value="<?= h($search) ?>" />
      </div>
      <div>
        <label class="form-label">Level</label>
        <select name="level" class="form-control">
          <option value="all" <?= $level==='all'?'selected':'' ?>>All Levels</option>
          <?php foreach($levels as $l): ?>
          <option value="<?= h($l) ?>" <?= $level===$l?'selected':'' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Search</button>
      <?php if($level!=='all'||$search): ?>
      <a href="careers.php" class="btn btn-outline">Clear</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Career Graph (visual) -->
  <?php if ($level === 'all' && !$search): ?>
  <div class="card mb-32">
    <h2 style="font-size:18px;font-weight:600;margin-bottom:16px">Visual Career Graph</h2>
    <div class="graph-wrap">
      <div class="graph-level"><span class="graph-node node-root">10th Pass</span></div>
      <div class="graph-arrow">↓</div>
      <div class="graph-level">
        <span class="graph-node node-stream">Science (PCM)</span>
        <span class="graph-node node-stream">Science (PCB)</span>
        <span class="graph-node node-stream">Commerce</span>
        <span class="graph-node node-stream">Arts / Humanities</span>
      </div>
      <div class="graph-arrow">↓</div>
      <div class="graph-level">
        <a href="?level=After+12th" class="graph-node node-course">B.Tech / B.E.</a>
        <a href="?level=After+12th" class="graph-node node-course">MBBS</a>
        <a href="?level=After+12th" class="graph-node node-course">BCA</a>
        <a href="?level=After+12th" class="graph-node node-course">B.Com</a>
        <a href="?level=After+12th" class="graph-node node-course">BBA</a>
        <a href="?level=After+12th" class="graph-node node-course">BA / B.Sc</a>
      </div>
      <div class="graph-arrow">↓</div>
      <div class="graph-level">
        <a href="?level=Degree" class="graph-node node-job">Software Engineer</a>
        <a href="?level=Degree" class="graph-node node-job">Data Scientist</a>
        <a href="?level=Degree" class="graph-node node-job">Doctor (MD/MS)</a>
        <a href="?level=Degree" class="graph-node node-job">CA</a>
        <a href="?level=Degree" class="graph-node node-job">MBA Manager</a>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Level tabs -->
  <div class="flex gap-8 mb-24" style="flex-wrap:wrap">
    <a href="careers.php" class="btn btn-sm <?= $level==='all'?'btn-navy':'btn-outline' ?>">All</a>
    <?php foreach($levels as $l): ?>
    <a href="careers.php?level=<?= urlencode($l) ?>" class="btn btn-sm <?= $level===$l?'btn-navy':'btn-outline' ?>"><?= $l ?></a>
    <?php endforeach; ?>
  </div>

  <!-- Career cards -->
  <?php if ($careers): ?>
  <div class="grid-3">
    <?php foreach($careers as $c): ?>
    <div class="career-card" onclick="showCareer(<?= $c['career_id'] ?>)">
      <div class="cc-level"><?= h($c['level']) ?> <?= $c['stream']?'· '.h($c['stream']):'' ?></div>
      <div class="cc-title"><?= h($c['title']) ?></div>
      <?php if($c['avg_salary'] && $c['avg_salary']!=='—'): ?>
      <div class="cc-salary mt-8"><?= h($c['avg_salary']) ?></div>
      <?php endif; ?>
      <div class="flex gap-8 mt-12" style="flex-wrap:wrap">
        <?php if($c['duration']): ?><span class="badge badge-gray">⏱ <?= h($c['duration']) ?></span><?php endif; ?>
        <span class="badge <?= $levelColors[$c['level']]??'badge-gray' ?>"><?= h($c['level']) ?></span>
      </div>
      <div class="mt-12" style="font-size:13px;color:var(--text-muted);line-height:1.5">
        <?= h(mb_strimwidth($c['description']??'',0,100,'…')) ?>
      </div>
    </div>

    <!-- Hidden detail data for modal -->
    <script>
    window.__careers = window.__careers || {};
    window.__careers[<?= $c['career_id'] ?>] = <?= json_encode($c) ?>;
    </script>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div class="card text-center" style="padding:48px">
    <div style="font-size:48px;margin-bottom:16px">🔍</div>
    <h3>No results found</h3>
    <p class="text-muted mt-8">Try a different keyword or level.</p>
    <a href="careers.php" class="btn btn-primary mt-16">Reset</a>
  </div>
  <?php endif; ?>
</div>

<!-- Career Detail Modal -->
<div id="career-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;overflow-y:auto;padding:40px 16px" onclick="if(event.target===this)closeModal()">
  <div style="background:#fff;border-radius:18px;max-width:600px;margin:0 auto;padding:32px;position:relative" id="modal-content">
    <button onclick="closeModal()" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:22px;cursor:pointer;color:var(--text-muted)">✕</button>
    <div id="modal-body"></div>
  </div>
</div>

<script>
function showCareer(id) {
  const c = window.__careers[id];
  if (!c) return;
  const levelColors = {'After 10th':'#DBEAFE','After 12th':'#CCFBF1','Degree':'#FEF3C7'};
  const lc = levelColors[c.level] || '#F1F5F9';
  let html = `
    <div style="background:${lc};border-radius:10px;padding:14px 18px;margin-bottom:20px">
      <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px">${c.level}${c.stream?' · '+c.stream:''}</div>
      <h2 style="font-family:'Playfair Display',serif;font-size:24px;font-weight:700;margin-top:4px">${c.title}</h2>
    </div>
    <p style="font-size:15px;color:var(--text-muted);margin-bottom:20px">${c.description||''}</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
      ${c.avg_salary&&c.avg_salary!=='—'?`<div style="background:#F0FDFA;border-radius:10px;padding:14px"><div style="font-size:12px;color:var(--text-muted)">Avg Salary</div><div style="font-size:20px;font-weight:700;color:var(--teal);margin-top:4px">${c.avg_salary}</div></div>`:''}
      ${c.duration?`<div style="background:#F8FAFC;border-radius:10px;padding:14px"><div style="font-size:12px;color:var(--text-muted)">Duration</div><div style="font-size:20px;font-weight:700;margin-top:4px">${c.duration}</div></div>`:''}
    </div>
  `;
  if (c.required_degree) html += `<div class="mb-16"><strong>Required Degree:</strong> <span style="color:var(--text-muted)">${c.required_degree}</span></div>`;
  if (c.job_roles) html += `<div class="mb-16"><strong>Job Roles:</strong><br><span style="color:var(--text-muted)">${c.job_roles}</span></div>`;
  if (c.further_study) html += `<div class="mb-16"><strong>Further Study:</strong><br><span style="color:var(--text-muted)">${c.further_study}</span></div>`;
  html += `<a href="dashboard.php" class="btn btn-primary mt-8">Build My Roadmap →</a>`;
  document.getElementById('modal-body').innerHTML = html;
  document.getElementById('career-modal').style.display = 'block';
}
function closeModal() { document.getElementById('career-modal').style.display = 'none'; }
</script>

<?php require_once 'includes/footer.php'; ?>
