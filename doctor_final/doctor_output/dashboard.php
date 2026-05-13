<?php
require_once __DIR__ . '/_auth.php';

if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: ../index.php');
    exit;
}

$doc_id = doctor_id();
$today  = date('Y-m-d');

// ── Doctor info ──
$stmt = mysqli_prepare($connect,
    "SELECT first_name, last_name, doctor_image
     FROM doctors WHERE doctor_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $doc_id);
mysqli_stmt_execute($stmt);
$doctor = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$doc_name = ($doctor['first_name'] ?? '') . ' ' . ($doctor['last_name'] ?? '');
$img_file = $doctor['doctor_image'] ?? '';
$abs_img  = __DIR__ . '/../../admin/src/' . $img_file;
$img_src  = ($img_file && file_exists($abs_img))
    ? '../../admin/src/' . $img_file
    : 'https://ui-avatars.com/api/?name=' . urlencode($doc_name) . '&background=0b3e8a&color=fff&size=200';

// ── Stats ──
function get_count($connect, $doc_id, $extra_where = '') {
    $sql  = "SELECT COUNT(*) as cnt FROM appointments WHERE doctor_id = ? $extra_where";
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $doc_id);
    mysqli_stmt_execute($stmt);
    $row  = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return (int)($row['cnt'] ?? 0);
}

$cnt_today     = get_count($connect, $doc_id, "AND appointment_date = '$today'");
$cnt_pending   = get_count($connect, $doc_id, "AND status = 'pending'");
$cnt_confirmed = get_count($connect, $doc_id, "AND status = 'confirmed'");
$cnt_total     = get_count($connect, $doc_id);

// ── Today's appointments ──
$todays = [];
$stmt = mysqli_prepare($connect,
    "SELECT a.appointment_id, a.appointment_time, a.status, p.name AS patient_name
     FROM appointments a
     LEFT JOIN patients p ON p.patient_id = a.patient_id
     WHERE a.doctor_id = ? AND a.appointment_date = ?
     ORDER BY a.appointment_time ASC");
mysqli_stmt_bind_param($stmt, 'is', $doc_id, $today);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($r = mysqli_fetch_assoc($res)) $todays[] = $r;
mysqli_stmt_close($stmt);

// ── Recent appointments (last 8) ──
$recent = [];
$stmt = mysqli_prepare($connect,
    "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status,
            p.name AS patient_name, p.phone AS patient_phone
     FROM appointments a
     LEFT JOIN patients p ON p.patient_id = a.patient_id
     WHERE a.doctor_id = ?
     ORDER BY a.appointment_date DESC, a.appointment_time DESC
     LIMIT 8");
mysqli_stmt_bind_param($stmt, 'i', $doc_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($r = mysqli_fetch_assoc($res)) $recent[] = $r;
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Doctor Dashboard – CARE Group</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet"/>
  <link href="../css/style.css" rel="stylesheet"/>
  <style>
    body { font-family:'Plus Jakarta Sans',system-ui,Arial,sans-serif; background:#f8fbff; }
    .main { padding:24px; }

    /* Welcome */
    .welcome-card {
      background:linear-gradient(135deg,#0b3e8a 0%,#2563eb 100%);
      border-radius:18px; padding:22px 26px; color:#fff;
      display:flex; align-items:center; justify-content:space-between;
      flex-wrap:wrap; gap:14px; margin-bottom:24px;
    }
    .welcome-avatar { width:62px; height:62px; border-radius:50%;
      object-fit:cover; border:3px solid rgba(255,255,255,.35); flex-shrink:0; }
    .welcome-avatar-ph { width:62px; height:62px; border-radius:50%;
      background:rgba(255,255,255,.2); display:flex; align-items:center;
      justify-content:center; font-size:1.5rem; color:#fff; flex-shrink:0; }
    .welcome-name { font-size:1.2rem; font-weight:800; }
    .welcome-sub  { font-size:.85rem; opacity:.8; margin-top:2px; }
    .welcome-date { text-align:right; }
    .welcome-date .date-big { font-size:1rem; font-weight:700; }
    .welcome-date .date-day { font-size:.82rem; opacity:.7; }

    /* Stat cards */
    .stat-card { background:#fff; border:1px solid #e5eef9; border-radius:16px;
      padding:16px 18px; display:flex; align-items:center; gap:12px; height:100%; }
    .stat-icon { width:46px; height:46px; border-radius:13px; flex-shrink:0;
      display:flex; align-items:center; justify-content:center;
      color:#fff; font-size:1.1rem; }
    .i-blue   { background:linear-gradient(135deg,#2563eb,#60a5fa); }
    .i-orange { background:linear-gradient(135deg,#ea580c,#f59e0b); }
    .i-green  { background:linear-gradient(135deg,#059669,#34d399); }
    .i-purple { background:linear-gradient(135deg,#7c3aed,#a78bfa); }
    .stat-num   { font-size:1.7rem; font-weight:800; color:#0b3e8a; line-height:1; }
    .stat-label { font-size:.78rem; color:#64748b; font-weight:600; margin-top:3px; }

    /* Quick tiles */
    .card-tile { background:#fff; border:1px solid #e5eef9; border-radius:14px;
      padding:14px 16px; display:flex; align-items:center; gap:11px;
      transition:all .18s; height:100%; }
    .card-tile:hover { border-color:#2563eb; box-shadow:0 4px 16px rgba(37,99,235,.1);
      transform:translateY(-2px); }
    .tile-icon { width:38px; height:38px; border-radius:11px; flex-shrink:0;
      display:flex; align-items:center; justify-content:center; color:#fff; }

    /* Cards */
    .card-care { background:#fff; border:1px solid #e5eef9; border-radius:16px; overflow:hidden; }
    .card-head { font-weight:800; color:#0b3e8a; font-size:.93rem;
      padding:14px 18px; border-bottom:1px solid #f1f5f9;
      display:flex; align-items:center; justify-content:space-between; }
    .view-all-link { font-size:.8rem; color:#2563eb; font-weight:700;
      text-decoration:none; }
    .view-all-link:hover { text-decoration:underline; }

    /* Table */
    .table thead th { font-size:.76rem; color:#94a3b8; text-transform:uppercase;
      font-weight:700; letter-spacing:.04em; border-bottom:1px solid #f1f5f9; }
    .table td { font-size:.86rem; vertical-align:middle; color:#1e293b; }
    .table tbody tr:hover { background:#fafbff; }

    /* Status badges */
    .badge-status { padding:4px 10px; border-radius:8px; font-weight:700;
      font-size:.73rem; display:inline-block; white-space:nowrap; }
    .st-pending   { background:#fff7ed; color:#c2410c; }
    .st-confirmed { background:#ecfdf5; color:#059669; }
    .st-cancelled { background:#fef2f2; color:#dc2626; }
    .st-completed { background:#eff6ff; color:#2563eb; }

    /* Today slot */
    .today-item { display:flex; align-items:center; justify-content:space-between;
      padding:10px 18px; border-bottom:1px solid #f8fafc; }
    .today-item:last-child { border-bottom:none; }
    .today-time { font-size:.8rem; color:#64748b; font-weight:600; margin-top:2px; }

    /* Empty state */
    .empty-state { text-align:center; padding:32px 16px; }
    .empty-state i { font-size:2rem; color:#e2e8f0; }
    .empty-state p { color:#94a3b8; font-size:.85rem; margin-top:8px; margin-bottom:0; }
  </style>
  <script>
    if (window.top !== window.self) { document.addEventListener('DOMContentLoaded',()=>{document.body.innerHTML='';}); }
  </script>
</head>
<body>
<?php include __DIR__ . '/sidebar_navbar.php'; ?>
<main class="main">

  <!-- Welcome Banner -->
  <div class="welcome-card">
    <div class="d-flex align-items-center gap-3">
      <?php if ($img_file && file_exists($abs_img)): ?>
        <img src="<?= htmlspecialchars($img_src) ?>" class="welcome-avatar" alt="Doctor">
      <?php else: ?>
        <div class="welcome-avatar-ph"><i class="fas fa-user-md"></i></div>
      <?php endif; ?>
      <div>
        <div class="welcome-name">Welcome, Dr. <?= htmlspecialchars($doc_name) ?>!</div>
        <div class="welcome-sub">Manage your appointments and availability</div>
      </div>
    </div>
    <div class="welcome-date">
      <div class="date-big"><?= date('d M Y') ?></div>
      <div class="date-day"><?= date('l') ?></div>
    </div>
  </div>

  <!-- Stats -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-icon i-blue"><i class="fas fa-calendar-day"></i></div>
        <div>
          <div class="stat-num"><?= $cnt_today ?></div>
          <div class="stat-label">Today</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-icon i-orange"><i class="fas fa-hourglass-half"></i></div>
        <div>
          <div class="stat-num"><?= $cnt_pending ?></div>
          <div class="stat-label">Pending</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-icon i-green"><i class="fas fa-check-circle"></i></div>
        <div>
          <div class="stat-num"><?= $cnt_confirmed ?></div>
          <div class="stat-label">Confirmed</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-icon i-purple"><i class="fas fa-calendar-check"></i></div>
        <div>
          <div class="stat-num"><?= $cnt_total ?></div>
          <div class="stat-label">Total</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Nav Tiles -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <a class="text-decoration-none" href="profile.php">
        <div class="card-tile">
          <div class="tile-icon i-blue"><i class="fas fa-user-md"></i></div>
          <div>
            <div style="font-weight:700;color:#0b3e8a;font-size:.88rem;">My Profile</div>
            <div class="text-muted" style="font-size:.76rem;">View & update</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a class="text-decoration-none" href="appointments.php">
        <div class="card-tile">
          <div class="tile-icon i-green"><i class="fas fa-calendar-check"></i></div>
          <div>
            <div style="font-weight:700;color:#0b3e8a;font-size:.88rem;">Appointments</div>
            <div class="text-muted" style="font-size:.76rem;">Today & upcoming</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a class="text-decoration-none" href="availability.php">
        <div class="card-tile">
          <div class="tile-icon i-orange"><i class="fas fa-clock"></i></div>
          <div>
            <div style="font-weight:700;color:#0b3e8a;font-size:.88rem;">Availability</div>
            <div class="text-muted" style="font-size:.76rem;">Set schedule</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a class="text-decoration-none" href="dashboard.php?logout=1">
        <div class="card-tile">
          <div class="tile-icon i-purple"><i class="fas fa-sign-out-alt"></i></div>
          <div>
            <div style="font-weight:700;color:#0b3e8a;font-size:.88rem;">Logout</div>
            <div class="text-muted" style="font-size:.76rem;">Return to home</div>
          </div>
        </div>
      </a>
    </div>
  </div>

  <!-- Today + Recent Table -->
  <div class="row g-3">

    <!-- Today's Schedule -->
    <div class="col-12 col-lg-4">
      <div class="card-care h-100">
        <div class="card-head">
          <span><i class="fas fa-calendar-day me-2" style="color:#06b6d4;"></i>Today's Schedule</span>
          <span style="font-size:.78rem;color:#94a3b8;"><?= date('d M') ?></span>
        </div>
        <?php if (empty($todays)): ?>
          <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <p>No appointments today.</p>
          </div>
        <?php else: ?>
          <?php foreach ($todays as $t):
            $st  = strtolower($t['status']);
            $cls = ['pending'=>'st-pending','confirmed'=>'st-confirmed',
                    'cancelled'=>'st-cancelled','completed'=>'st-completed'][$st] ?? 'st-pending';
          ?>
          <div class="today-item">
            <div>
              <div style="font-weight:700;font-size:.88rem;color:#1e293b;">
                <?= htmlspecialchars($t['patient_name'] ?? 'Patient') ?>
              </div>
              <div class="today-time">
                <i class="fas fa-clock me-1"></i><?= date('h:i A', strtotime($t['appointment_time'])) ?>
              </div>
            </div>
            <span class="badge-status <?= $cls ?>"><?= ucfirst($st) ?></span>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Recent Appointments -->
    <div class="col-12 col-lg-8">
      <div class="card-care">
        <div class="card-head">
          <span><i class="fas fa-history me-2" style="color:#06b6d4;"></i>Recent Appointments</span>
          <a href="appointments.php" class="view-all-link">
            View All <i class="fas fa-arrow-right ms-1"></i>
          </a>
        </div>
        <?php if (empty($recent)): ?>
          <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <p>No appointments yet.</p>
          </div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table mb-0">
            <thead>
              <tr>
                <th class="ps-3">#</th>
                <th>Patient</th>
                <th>Phone</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($recent as $row):
              $st  = strtolower($row['status'] ?? 'pending');
              $cls = ['pending'=>'st-pending','confirmed'=>'st-confirmed',
                      'cancelled'=>'st-cancelled','completed'=>'st-completed'][$st] ?? 'st-pending';
            ?>
              <tr>
                <td class="ps-3" style="color:#94a3b8;font-weight:700;font-size:.8rem;">
                  #<?= (int)$row['appointment_id'] ?>
                </td>
                <td>
                  <i class="fas fa-user-circle me-1" style="color:#93c5fd;"></i>
                  <?= htmlspecialchars($row['patient_name'] ?? '—') ?>
                </td>
                <td style="color:#64748b;"><?= htmlspecialchars($row['patient_phone'] ?? '—') ?></td>
                <td><?= date('d M Y', strtotime($row['appointment_date'])) ?></td>
                <td><?= date('h:i A', strtotime($row['appointment_time'])) ?></td>
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
</body>
</html>