<?php
require_once __DIR__ . '/_auth.php';
$today = date('Y-m-d');
echo $today; // delete karna baad mein
$appointments = [];
$db_error = '';
$doc_id = doctor_id();

// ── Handle status update (confirm / cancel) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_csrf()) {
    $apt_id = intval($_POST['appointment_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';
    $allowed = ['confirmed', 'cancelled', 'completed'];
    if ($apt_id > 0 && in_array($new_status, $allowed)) {
        $upd = mysqli_prepare($connect,
            "UPDATE appointments SET status=? WHERE appointment_id=? AND doctor_id=?");
        mysqli_stmt_bind_param($upd, 'sii', $new_status, $apt_id, $doc_id);
        mysqli_stmt_execute($upd);
        mysqli_stmt_close($upd);
    }
    header("Location: appointments.php");
    exit();
}

// ── Fetch appointments — patients.name (not first_name/last_name) ──
$sql = "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status,
               p.name AS patient_name, p.phone AS patient_phone
        FROM appointments a
        LEFT JOIN patients p ON p.patient_id = a.patient_id
        WHERE a.doctor_id = ?
        ORDER BY a.appointment_date ASC, a.appointment_time ASC";

$stmt = mysqli_prepare($connect, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $doc_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($res)) {
        $appointments[] = $r;
    }
    mysqli_stmt_close($stmt);
} else {
    $db_error = 'Could not load appointments. Please contact admin.';
}

// ── Split into tabs: today, upcoming, past ──
$today      = date('Y-m-d');
$tab_today    = [];
$tab_upcoming = [];
$tab_past     = [];
foreach ($appointments as $a) {
    $d      = $a['appointment_date'];
    $status = strtolower($a['status'] ?? '');

    if ($status === 'completed' || $status === 'cancelled') {
        $tab_past[]     = $a;
    } elseif ($d === $today) {
        $tab_today[]    = $a;
    } elseif ($d > $today) {
        $tab_upcoming[] = $a;
    } else {
        $tab_past[]     = $a;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Appointments – Doctor Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet" />
  <link href="../css/style.css" rel="stylesheet" />
  <style>
    body { font-family:'Plus Jakarta Sans',system-ui,Arial,sans-serif; background:#f8fbff; }
    .card-care { background:#fff; border:1px solid #e5eef9; border-radius:16px; }
    table thead th { font-size:.82rem; color:#64748b; text-transform:uppercase; letter-spacing:.4px; }
    table td { vertical-align:middle; font-size:.9rem; }
    .badge-status { padding:5px 10px; border-radius:8px; font-weight:700; font-size:.76rem; display:inline-block; }
    .st-confirmed  { background:#ecfdf5; color:#059669; }
    .st-pending    { background:#fff7ed; color:#c2410c; }
    .st-cancelled  { background:#fef2f2; color:#dc2626; }
    .st-completed  { background:#eff6ff; color:#2563eb; }
    .nav-tabs .nav-link { font-weight:600; color:#64748b; border:none; border-bottom:2px solid transparent; border-radius:0; }
    .nav-tabs .nav-link.active { color:#0b3e8a; border-bottom:2px solid #0b3e8a; background:none; }
    .count-badge { background:#e0e7ff; color:#3730a3; border-radius:20px; padding:1px 8px; font-size:.75rem; font-weight:700; margin-left:5px; }
  </style>
  <script>if (window.top !== window.self) { document.addEventListener('DOMContentLoaded',()=>{document.body.innerHTML='';}); }</script>
</head>
<body>
<?php include __DIR__ . '/sidebar_navbar.php'; ?>
<main class="main">
  <div class="mb-3">
    <h3 style="font-weight:800;color:#0b3e8a;">Appointments</h3>
    <p class="text-muted mb-0">Manage your patient appointments</p>
  </div>

  <?php if ($db_error): ?>
    <div class="alert alert-warning d-flex align-items-center gap-2">
      <i class="fas fa-info-circle"></i><?= htmlspecialchars($db_error) ?>
    </div>
  <?php else: ?>

  <div class="card-care p-3 p-md-4">
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="aptTabs">
      <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-today">
          Today <span class="count-badge"><?= count($tab_today) ?></span>
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-upcoming">
          Upcoming <span class="count-badge"><?= count($tab_upcoming) ?></span>
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-past">
          Past <span class="count-badge"><?= count($tab_past) ?></span>
        </button>
      </li>
    </ul>

    <div class="tab-content">
      <?php
      $tabs = [
          'tab-today'    => ['data' => $tab_today,    'empty' => 'No appointments today.'],
          'tab-upcoming' => ['data' => $tab_upcoming, 'empty' => 'No upcoming appointments.'],
          'tab-past'     => ['data' => $tab_past,     'empty' => 'No past appointments.'],
      ];
      $first = true;
      foreach ($tabs as $tab_id => $tab):
      ?>
      <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" id="<?= $tab_id ?>">
        <?php if (empty($tab['data'])): ?>
          <div class="text-center py-5">
            <i class="fas fa-calendar-times" style="font-size:2rem;color:#9ca3af;"></i>
            <p class="text-muted mt-2 mb-0"><?= $tab['empty'] ?></p>
          </div>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Patient</th>
                <th>Phone</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <?php if ($tab_id !== 'tab-past'): ?>
                <th>Action</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($tab['data'] as $row):
              $status = strtolower($row['status'] ?? 'pending');
              $cls_map = ['confirmed'=>'st-confirmed','pending'=>'st-pending','cancelled'=>'st-cancelled','completed'=>'st-completed'];
              $cls = $cls_map[$status] ?? 'st-pending';
              $apt_id = (int)$row['appointment_id'];
              $pname  = htmlspecialchars($row['patient_name'] ?? 'Patient');
              $pphone = htmlspecialchars($row['patient_phone'] ?? '—');
              $adate  = date('d M Y', strtotime($row['appointment_date']));
              $atime  = date('h:i A', strtotime($row['appointment_time']));
            ?>
              <tr>
                <td style="color:#94a3b8;font-weight:700;">APT-<?= $apt_id ?></td>
                <td><i class="fas fa-user-circle me-1" style="color:#93c5fd;"></i><?= $pname ?></td>
                <td><?= $pphone ?></td>
                <td><?= $adate ?></td>
                <td><?= $atime ?></td>
                <td><span class="badge-status <?= $cls ?>"><?= ucfirst($status) ?></span></td>
                <?php if ($tab_id !== 'tab-past'): ?>
                <td>
                  <?php if ($status === 'pending'): ?>
                    <!-- Confirm -->
                    <form method="post" action="" style="display:inline;">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                      <input type="hidden" name="appointment_id" value="<?= $apt_id ?>">
                      <input type="hidden" name="new_status" value="confirmed">
                      <button type="submit" class="btn btn-sm btn-success" style="font-size:.76rem;">
                        <i class="fas fa-check me-1"></i>Confirm
                      </button>
                    </form>
                    <!-- Cancel -->
                    <form method="post" action="" style="display:inline;">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                      <input type="hidden" name="appointment_id" value="<?= $apt_id ?>">
                      <input type="hidden" name="new_status" value="cancelled">
                      <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size:.76rem;"
                              onclick="return confirm('Cancel this appointment?')">
                        <i class="fas fa-times me-1"></i>Cancel
                      </button>
                    </form>
                  <?php elseif ($status === 'confirmed'): ?>
                    <!-- Mark Complete -->
                    <form method="post" action="" style="display:inline;">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                      <input type="hidden" name="appointment_id" value="<?= $apt_id ?>">
                      <input type="hidden" name="new_status" value="completed">
                      <button type="submit" class="btn btn-sm btn-primary" style="font-size:.76rem;">
                        <i class="fas fa-check-double me-1"></i>Complete
                      </button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted" style="font-size:.8rem;">—</span>
                  <?php endif; ?>
                </td>
                <?php endif; ?>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
      <?php $first = false; endforeach; ?>
    </div>
  </div>

  <?php endif; ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
