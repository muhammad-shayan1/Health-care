<?php
require_once __DIR__ . '/_auth.php';

// Cities dropdown
$cities = [];
$res = mysqli_query($connect, "SELECT city_id, city_name FROM cities WHERE city_status='active' ORDER BY city_name");
while ($r = mysqli_fetch_assoc($res)) $cities[] = $r;

// Specializations dropdown
$specs = [];
$res2 = mysqli_query($connect, "SELECT specialize_id, specialize FROM specialization WHERE specialization_status='active' ORDER BY specialize");
while ($r = mysqli_fetch_assoc($res2)) $specs[] = $r;

// Filter values
$city_filter = intval($_GET['city'] ?? 0);
$spec_filter = intval($_GET['specialization'] ?? 0);

// Build query — no JOIN, separate fetch for specialization name
$sql = "SELECT doctor_id, first_name, last_name, doctor_image, experience,
               consultation_fee, city_id, specialize_id, bio
        FROM doctors WHERE doctor_status = 1";
$params = [];
$types  = '';

if ($city_filter > 0) {
    $sql    .= " AND city_id = ?";
    $types  .= 'i';
    $params[] = $city_filter;
}
if ($spec_filter > 0) {
    $sql    .= " AND specialize_id = ?";
    $types  .= 'i';
    $params[] = $spec_filter;
}
$sql .= " ORDER BY first_name ASC";

$stmt = mysqli_prepare($connect, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$doctors = [];
while ($r = mysqli_fetch_assoc($res)) $doctors[] = $r;
mysqli_stmt_close($stmt);

// Fetch city name and spec name for each doctor
foreach ($doctors as &$doc) {
    // City name
    $doc['city_name'] = '';
    if (!empty($doc['city_id'])) {
        $sc = mysqli_prepare($connect, "SELECT city_name FROM cities WHERE city_id = ? LIMIT 1");
        mysqli_stmt_bind_param($sc, 'i', $doc['city_id']);
        mysqli_stmt_execute($sc);
        $cr = mysqli_fetch_assoc(mysqli_stmt_get_result($sc));
        mysqli_stmt_close($sc);
        $doc['city_name'] = $cr['city_name'] ?? '';
    }
    // Spec name
    $doc['specialize'] = '';
    if (!empty($doc['specialize_id'])) {
        $ss = mysqli_prepare($connect, "SELECT specialize FROM specialization WHERE specialize_id = ? LIMIT 1");
        mysqli_stmt_bind_param($ss, 'i', $doc['specialize_id']);
        mysqli_stmt_execute($ss);
        $sr = mysqli_fetch_assoc(mysqli_stmt_get_result($ss));
        mysqli_stmt_close($ss);
        $doc['specialize'] = $sr['specialize'] ?? '';
    }
}
unset($doc);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="Search and find verified doctors by city and specialization on CARE Group patient portal."/>
  <title>Find a Doctor | CARE Group Patient Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
<?php require __DIR__ . '/sidebar_navbar.php'; ?>
<main class="main-content" id="main-content">

  <div class="page-header">
    <h1>Find a Doctor</h1>
    <p>Search from our network of verified specialists</p>
  </div>

  <!-- Filter -->
  <section class="filter-card" aria-label="Search filters">
    <h6><i class="fas fa-filter me-2" aria-hidden="true"></i>Filter Doctors</h6>
    <form method="GET" action="">
      <div class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
          <label class="form-label" for="city"><i class="fas fa-map-marker-alt me-1"></i> City</label>
          <select class="form-select" name="city" id="city">
            <option value="0">All Cities</option>
            <?php foreach ($cities as $c): ?>
              <option value="<?= (int)$c['city_id'] ?>" <?= $city_filter === (int)$c['city_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['city_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 col-md-4">
          <label class="form-label" for="specialization"><i class="fas fa-stethoscope me-1"></i> Specialization</label>
          <select class="form-select" name="specialization" id="specialization">
            <option value="0">All Specializations</option>
            <?php foreach ($specs as $sp): ?>
              <option value="<?= (int)$sp['specialize_id'] ?>" <?= $spec_filter === (int)$sp['specialize_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($sp['specialize']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
          <button type="submit" class="btn-search flex-fill">
            <i class="fas fa-search" aria-hidden="true"></i> Search
          </button>
          <a href="search_doctor.php" class="btn btn-outline-secondary" aria-label="Clear filters">
            <i class="fas fa-redo" aria-hidden="true"></i>
          </a>
        </div>
      </div>
    </form>
  </section>

  <!-- Results -->
  <div class="results-header">
    <h6>Available Doctors</h6>
    <span class="results-count"><?= count($doctors) ?> doctor<?= count($doctors) !== 1 ? 's' : '' ?> found</span>
  </div>

  <?php if (empty($doctors)): ?>
    <div class="empty-state">
      <i class="fas fa-user-md" aria-hidden="true"></i>
      <p>No doctors found. Try different filters.</p>
    </div>
  <?php else: ?>
  <div class="row g-3">
    <?php
    $colors = ['#0b3e8a','#059669','#d97706','#7c3aed','#dc2626','#06b6d4','#ea580c','#0891b2'];
    foreach ($doctors as $idx => $doc):
      $color    = $colors[$idx % count($colors)];
      $initials = strtoupper(substr($doc['first_name'], 0, 1) . substr($doc['last_name'], 0, 1));
      $img_file = $doc['doctor_image'] ?? '';
      $img_path = '../../admin/src/' . $img_file;
      $abs_path = __DIR__ . '/../../admin/src/' . $img_file;
      $has_img  = ($img_file && file_exists($abs_path));
    ?>
    <div class="col-12 col-sm-6 col-xl-3">
      <article class="doctor-card">
        <div class="doctor-top">
          <?php if ($has_img): ?>
            <img src="<?= htmlspecialchars($img_path) ?>"
                 class="doctor-avatar-img"
                 alt="Dr. <?= htmlspecialchars($doc['first_name'].' '.$doc['last_name']) ?>"/>
          <?php else: ?>
            <div class="doctor-avatar-placeholder"
                 style="background:linear-gradient(135deg,<?= $color ?>,<?= $color ?>bb);"
                 aria-hidden="true"><?= $initials ?></div>
          <?php endif; ?>
          <div class="doctor-info">
            <h6>Dr. <?= htmlspecialchars($doc['first_name'].' '.$doc['last_name']) ?></h6>
            <span class="spec-badge"><?= htmlspecialchars($doc['specialize'] ?: 'General') ?></span>
          </div>
        </div>
        <div class="doctor-meta">
          <?php if ($doc['experience']): ?>
          <div class="doctor-meta-item">
            <i class="fas fa-briefcase-medical" aria-hidden="true"></i>
            <?= (int)$doc['experience'] ?> Years Experience
          </div>
          <?php endif; ?>
          <?php if ($doc['city_name']): ?>
          <div class="doctor-meta-item">
            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
            <?= htmlspecialchars($doc['city_name']) ?>
          </div>
          <?php endif; ?>
        </div>
        <hr class="doctor-divider"/>
        <div class="doctor-footer">
          <div>
            <div class="fee-label">Consultation Fee</div>
            <div class="fee-amount">Rs <?= number_format((int)$doc['consultation_fee']) ?></div>
          </div>
          <a href="book_appointment.php?doctor_id=<?= (int)$doc['doctor_id'] ?>" class="btn-book" aria-label="Book appointment with Dr. <?= htmlspecialchars($doc['first_name']) ?>">
            <i class="fas fa-calendar-plus me-1" aria-hidden="true"></i>Book
          </a>
        </div>
      </article>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
var toggle  = document.getElementById('sidebarToggle');
var sidebar = document.getElementById('sidebar');
var overlay = document.getElementById('sidebarOverlay');
if (toggle) {
    toggle.addEventListener('click', function() {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    });
}
if (overlay) {
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });
}
</script>
</body>
</html>
