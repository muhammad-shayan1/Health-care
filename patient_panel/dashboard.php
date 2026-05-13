<?php
require_once __DIR__ . '/_auth.php';
$pat_id = patient_id();
$today  = date('Y-m-d');
$hour   = (int)date('H');
$greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');

// Patient info
$stmt = mysqli_prepare($connect, "SELECT name FROM patients WHERE patient_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $pat_id);
mysqli_stmt_execute($stmt);
$patient  = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
$pat_name = $patient['name'] ?? 'Patient';

// Total appointments
$stmt = mysqli_prepare($connect, "SELECT COUNT(*) AS cnt FROM appointments WHERE patient_id = ?");
mysqli_stmt_bind_param($stmt, 's', $pat_id);
mysqli_stmt_execute($stmt);
$cnt_total = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['cnt'] ?? 0);
mysqli_stmt_close($stmt);

// Upcoming
$stmt = mysqli_prepare($connect, "SELECT COUNT(*) AS cnt FROM appointments WHERE patient_id = ? AND appointment_date >= ? AND status NOT IN ('cancelled','completed')");
mysqli_stmt_bind_param($stmt, 'ss', $pat_id, $today);
mysqli_stmt_execute($stmt);
$cnt_upcoming = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['cnt'] ?? 0);
mysqli_stmt_close($stmt);

// Confirmed
$stmt = mysqli_prepare($connect, "SELECT COUNT(*) AS cnt FROM appointments WHERE patient_id = ? AND status = 'confirmed'");
mysqli_stmt_bind_param($stmt, 's', $pat_id);
mysqli_stmt_execute($stmt);
$cnt_confirmed = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['cnt'] ?? 0);
mysqli_stmt_close($stmt);

// Cancelled
$stmt = mysqli_prepare($connect, "SELECT COUNT(*) AS cnt FROM appointments WHERE patient_id = ? AND status = 'cancelled'");
mysqli_stmt_bind_param($stmt, 's', $pat_id);
mysqli_stmt_execute($stmt);
$cnt_cancelled = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['cnt'] ?? 0);
mysqli_stmt_close($stmt);

// Recent appointments — 5 latest
$recent = [];
$stmt = mysqli_prepare($connect,
    "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status,
            d.first_name, d.last_name, d.specialize_id
     FROM appointments a
     LEFT JOIN doctors d ON d.doctor_id = a.doctor_id
     WHERE a.patient_id = ?
     ORDER BY a.appointment_date DESC, a.appointment_time DESC
     LIMIT 5");
mysqli_stmt_bind_param($stmt, 's', $pat_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($r = mysqli_fetch_assoc($res)) $recent[] = $r;
mysqli_stmt_close($stmt);

// Get specialization names for recent
foreach ($recent as &$row) {
    $row['specialize'] = '';
    if (!empty($row['specialize_id'])) {
        $s = mysqli_prepare($connect, "SELECT specialize FROM specialization WHERE specialize_id = ? LIMIT 1");
        mysqli_stmt_bind_param($s, 'i', $row['specialize_id']);
        mysqli_stmt_execute($s);
        $sr = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
        mysqli_stmt_close($s);
        $row['specialize'] = $sr['specialize'] ?? '';
    }
}
unset($row);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="Patient dashboard — manage your appointments and health records on CARE Group portal."/>
  <title>Dashboard | CARE Group Patient Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
<?php require __DIR__ . '/sidebar_navbar.php'; ?>
<main class="main-content" id="main-content">

  <section class="welcome-banner" aria-label="Welcome">
    <div>
      <h3><?= htmlspecialchars($greeting) ?>, <?= htmlspecialchars($pat_name) ?>! 👋</h3>
      <p>You have <strong><?= $cnt_upcoming ?> upcoming appointment<?= $cnt_upcoming !== 1 ? 's' : '' ?></strong>. Stay healthy!</p>
    </div>
    <div class="welcome-icon" aria-hidden="true"><i class="fas fa-stethoscope"></i></div>
  </section>

  <div class="page-header">
    <h1>Dashboard Overview</h1>
    <p>Track your health journey and appointments</p>
  </div>

  <!-- Stats -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
      <article class="stat-card">
        <div class="stat-icon blue" aria-hidden="true"><i class="fas fa-calendar-check"></i></div>
        <div>
          <div class="stat-num"><?= $cnt_total ?></div>
          <div class="stat-label">Total Appointments</div>
        </div>
      </article>
    </div>
    <div class="col-6 col-xl-3">
      <article class="stat-card">
        <div class="stat-icon green" aria-hidden="true"><i class="fas fa-clock"></i></div>
        <div>
          <div class="stat-num"><?= $cnt_upcoming ?></div>
          <div class="stat-label">Upcoming</div>
        </div>
      </article>
    </div>
    <div class="col-6 col-xl-3">
      <article class="stat-card">
        <div class="stat-icon orange" aria-hidden="true"><i class="fas fa-check-circle"></i></div>
        <div>
          <div class="stat-num"><?= $cnt_confirmed ?></div>
          <div class="stat-label">Confirmed</div>
        </div>
      </article>
    </div>
    <div class="col-6 col-xl-3">
      <article class="stat-card">
        <div class="stat-icon red" aria-hidden="true"><i class="fas fa-times-circle"></i></div>
        <div>
          <div class="stat-num"><?= $cnt_cancelled ?></div>
          <div class="stat-label">Cancelled</div>
        </div>
      </article>
    </div>
  </div>

  <div class="row g-3">
    <!-- Quick Actions -->
    <div class="col-12 col-lg-4">
      <div class="section-card">
        <div class="section-head"><i class="fas fa-bolt text-warning me-1" aria-hidden="true"></i> Quick Actions</div>
        <div class="row g-3">
          <div class="col-6">
            <a href="../search-doctor.php" class="quick-action-btn">
              <i class="fas fa-search" aria-hidden="true"></i> Find Doctor
            </a>
          </div>
          <div class="col-6">
            <a href="my_appointments.php" class="quick-action-btn">
              <i class="fas fa-list-alt" aria-hidden="true"></i> My Appts
            </a>
          </div>
          <div class="col-6">
            <a href="profile.php" class="quick-action-btn">
              <i class="fas fa-user-edit" aria-hidden="true"></i> My Profile
            </a>
          </div>
          <div class="col-6">
            <a href="logout.php" class="quick-action-btn" style="background:#fff0f0;color:#dc2626;">
              <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Logout
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Appointments -->
    <div class="col-12 col-lg-8">
      <div class="section-card">
        <div class="section-head d-flex justify-content-between align-items-center">
          <span><i class="fas fa-history me-1" aria-hidden="true"></i> Recent Appointments</span>
          <a href="my_appointments.php" style="font-size:.82rem;color:var(--primary-mid);font-weight:700;text-decoration:none;">View All →</a>
        </div>
        <?php if (empty($recent)): ?>
          <div class="empty-state">
            <i class="fas fa-calendar-times" aria-hidden="true"></i>
            <p>No appointments yet. <a href="search_doctor.php">Book your first one!</a></p>
          </div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table appt-table mb-0">
            <thead>
              <tr>
                <th scope="col">Doctor</th>
                <th scope="col">Specialization</th>
                <th scope="col">Date</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($recent as $row):
              $st  = strtolower($row['status'] ?? 'pending');
              $cls = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed',
                      'cancelled'=>'badge-cancelled','completed'=>'badge-completed'][$st] ?? 'badge-pending';
            ?>
              <tr>
                <td><strong>Dr. <?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></strong></td>
                <td style="color:var(--muted);font-size:.82rem;"><?= htmlspecialchars($row['specialize'] ?? '—') ?></td>
                <td><?= date('d M Y', strtotime($row['appointment_date'])) ?></td>
                <td><span class="badge-status <?= $cls ?>"><?= ucfirst($st) ?></span></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

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
        toggle.setAttribute('aria-expanded', sidebar.classList.contains('show'));
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
