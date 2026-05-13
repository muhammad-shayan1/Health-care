<?php
require_once __DIR__ . '/_auth.php';
$pat_id = patient_id();
$msg = '';
$err = '';
$today = date('Y-m-d');

// Cancel appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_csrf()) {
    $apt_id = intval($_POST['appointment_id'] ?? 0);
    if ($apt_id > 0) {
        $upd = mysqli_prepare($connect,
            "UPDATE appointments SET status='cancelled'
             WHERE appointment_id = ? AND patient_id = ? AND status IN ('pending','confirmed')");
        mysqli_stmt_bind_param($upd, 'is', $apt_id, $pat_id);
        if (mysqli_stmt_execute($upd) && mysqli_stmt_affected_rows($upd) > 0) {
            $msg = 'Appointment cancelled successfully.';
        } else {
            $err = 'Could not cancel this appointment.';
        }
        mysqli_stmt_close($upd);
    }
    header('Location: my_appointments.php' . ($msg ? '?msg=1' : '?err=1'));
    exit;
}
if (isset($_GET['msg'])) $msg = 'Appointment cancelled successfully.';
if (isset($_GET['err'])) $err = 'Could not cancel this appointment.';

// Fetch all appointments
$appointments = [];
$stmt = mysqli_prepare($connect,
    "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status, a.doctor_id
     FROM appointments a
     WHERE a.patient_id = ?
     ORDER BY a.appointment_date DESC, a.appointment_time DESC");
mysqli_stmt_bind_param($stmt, 's', $pat_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($r = mysqli_fetch_assoc($res)) $appointments[] = $r;
mysqli_stmt_close($stmt);

// Fetch doctor info for each appointment
foreach ($appointments as &$row) {
    $did = $row['doctor_id'];
    $dr  = mysqli_prepare($connect, "SELECT first_name, last_name, doctor_image, specialize_id, consultation_fee FROM doctors WHERE doctor_id = ? LIMIT 1");
    mysqli_stmt_bind_param($dr, 'i', $did);
    mysqli_stmt_execute($dr);
    $drow = mysqli_fetch_assoc(mysqli_stmt_get_result($dr));
    mysqli_stmt_close($dr);
    $row['doctor_name']  = 'Dr. ' . ($drow['first_name'] ?? '') . ' ' . ($drow['last_name'] ?? '');
    $row['doctor_image'] = $drow['doctor_image'] ?? '';
    $row['doctor_fee']   = $drow['consultation_fee'] ?? 0;
    $row['specialize']   = '';
    if (!empty($drow['specialize_id'])) {
        $sp = mysqli_prepare($connect, "SELECT specialize FROM specialization WHERE specialize_id = ? LIMIT 1");
        mysqli_stmt_bind_param($sp, 'i', $drow['specialize_id']);
        mysqli_stmt_execute($sp);
        $spr = mysqli_fetch_assoc(mysqli_stmt_get_result($sp));
        mysqli_stmt_close($sp);
        $row['specialize'] = $spr['specialize'] ?? '';
    }
}
unset($row);

// Stats
$cnt_total     = count($appointments);
$cnt_upcoming  = count(array_filter($appointments, fn($a) => $a['appointment_date'] >= $today && !in_array($a['status'], ['cancelled','completed'])));
$cnt_pending   = count(array_filter($appointments, fn($a) => $a['status'] === 'pending'));
$cnt_cancelled = count(array_filter($appointments, fn($a) => $a['status'] === 'cancelled'));

// Active filter
$filter = $_GET['filter'] ?? 'all';
$filtered = match($filter) {
    'upcoming'  => array_filter($appointments, fn($a) => $a['appointment_date'] >= $today && !in_array($a['status'],['cancelled','completed'])),
    'pending'   => array_filter($appointments, fn($a) => $a['status'] === 'pending'),
    'confirmed' => array_filter($appointments, fn($a) => $a['status'] === 'confirmed'),
    'cancelled' => array_filter($appointments, fn($a) => $a['status'] === 'cancelled'),
    'completed' => array_filter($appointments, fn($a) => $a['status'] === 'completed'),
    default     => $appointments
};

$colors = ['#0b3e8a','#059669','#d97706','#7c3aed','#dc2626','#06b6d4','#ea580c','#0891b2'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="View and manage all your medical appointments on CARE Group patient portal."/>
  <title>My Appointments | CARE Group Patient Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
<?php require __DIR__ . '/sidebar_navbar.php'; ?>
<main class="main-content" id="main-content">

  <div class="page-header-row">
    <div>
      <h1>My Appointments</h1>
      <p style="color:var(--muted);font-size:.88rem;margin:0;">Manage and track all your medical appointments</p>
    </div>
    <a href="../search-doctor.php" class="btn-new-appt" aria-label="Book a new appointment">
      <i class="fas fa-plus" aria-hidden="true"></i> Book New
    </a>
  </div>

  <?php if ($msg): ?>
    <div class="alert-care alert-success-care" role="alert">
      <i class="fas fa-check-circle" aria-hidden="true"></i><?= htmlspecialchars($msg) ?>
    </div>
  <?php endif; ?>
  <?php if ($err): ?>
    <div class="alert-care alert-danger-care" role="alert">
      <i class="fas fa-exclamation-circle" aria-hidden="true"></i><?= htmlspecialchars($err) ?>
    </div>
  <?php endif; ?>

  <!-- Mini Stats -->
  <div class="mini-stats" role="list" aria-label="Appointment statistics">
    <div class="mini-stat" role="listitem">
      <div class="mini-stat-icon" style="background:#e8f0ff;" aria-hidden="true"><i class="fas fa-calendar-check" style="color:#2563eb;"></i></div>
      <div><div class="mini-stat-val"><?= $cnt_total ?></div><div class="mini-stat-lbl">Total</div></div>
    </div>
    <div class="mini-stat" role="listitem">
      <div class="mini-stat-icon" style="background:#ecfdf5;" aria-hidden="true"><i class="fas fa-clock" style="color:#059669;"></i></div>
      <div><div class="mini-stat-val"><?= $cnt_upcoming ?></div><div class="mini-stat-lbl">Upcoming</div></div>
    </div>
    <div class="mini-stat" role="listitem">
      <div class="mini-stat-icon" style="background:#fff7ed;" aria-hidden="true"><i class="fas fa-hourglass-half" style="color:#d97706;"></i></div>
      <div><div class="mini-stat-val"><?= $cnt_pending ?></div><div class="mini-stat-lbl">Pending</div></div>
    </div>
    <div class="mini-stat" role="listitem">
      <div class="mini-stat-icon" style="background:#fef2f2;" aria-hidden="true"><i class="fas fa-times-circle" style="color:#dc2626;"></i></div>
      <div><div class="mini-stat-val"><?= $cnt_cancelled ?></div><div class="mini-stat-lbl">Cancelled</div></div>
    </div>
  </div>

  <!-- Filter Tabs -->
  <nav class="filter-tabs" aria-label="Appointment filters">
    <?php
    $tabs = [
        'all'       => ['label' => 'All',       'count' => $cnt_total],
        'upcoming'  => ['label' => 'Upcoming',  'count' => $cnt_upcoming],
        'pending'   => ['label' => 'Pending',   'count' => $cnt_pending],
        'confirmed' => ['label' => 'Confirmed', 'count' => count(array_filter($appointments, fn($a) => $a['status'] === 'confirmed'))],
        'cancelled' => ['label' => 'Cancelled', 'count' => $cnt_cancelled],
        'completed' => ['label' => 'Completed', 'count' => count(array_filter($appointments, fn($a) => $a['status'] === 'completed'))],
    ];
    foreach ($tabs as $key => $tab):
    ?>
      <a href="?filter=<?= $key ?>"
         class="filter-tab <?= $filter === $key ? 'active' : '' ?>"
         aria-current="<?= $filter === $key ? 'true' : 'false' ?>">
        <?= $tab['label'] ?> <span class="count"><?= $tab['count'] ?></span>
      </a>
    <?php endforeach; ?>
  </nav>

  <!-- Table -->
  <div class="table-card">
    <div class="table-card-header">
      <h6><i class="fas fa-list me-2" aria-hidden="true"></i>Appointment History</h6>
    </div>
    <?php if (empty($filtered)): ?>
      <div class="empty-state">
        <i class="fas fa-calendar-times" aria-hidden="true"></i>
        <p>No appointments found in this category.</p>
      </div>
    <?php else: ?>
    <div class="table-responsive">
      <table aria-label="Appointments list">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Doctor</th>
            <th scope="col">Date & Time</th>
            <th scope="col">Specialization</th>
            <th scope="col">Fee</th>
            <th scope="col">Status</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($filtered as $idx => $row):
          $st  = strtolower($row['status'] ?? 'pending');
          $cls = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed',
                  'cancelled'=>'badge-cancelled','completed'=>'badge-completed'][$st] ?? 'badge-pending';
          $color    = $colors[$idx % count($colors)];
          $img_file = $row['doctor_image'] ?? '';
          $abs_img  = __DIR__ . '/../../admin/src/' . $img_file;
          $has_img  = ($img_file && file_exists($abs_img));
          // Initials from doctor name
          $parts    = explode(' ', str_replace('Dr. ', '', $row['doctor_name']));
          $ini      = strtoupper(substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1));
          $can_cancel = in_array($st, ['pending', 'confirmed']);
        ?>
        <tr>
          <td style="color:var(--muted);font-weight:700;font-size:.78rem;">APT-<?= (int)$row['appointment_id'] ?></td>
          <td>
            <div class="doctor-cell">
              <?php if ($has_img): ?>
                <img src="../../admin/src/<?= htmlspecialchars($img_file) ?>"
                     style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;"
                     alt="<?= htmlspecialchars($row['doctor_name']) ?>"/>
              <?php else: ?>
                <div class="mini-avatar" style="background:linear-gradient(135deg,<?= $color ?>,<?= $color ?>bb);" aria-hidden="true"><?= $ini ?></div>
              <?php endif; ?>
              <div>
                <div style="font-weight:700;font-size:.88rem;"><?= htmlspecialchars($row['doctor_name']) ?></div>
                <div style="font-size:.74rem;color:var(--muted);"><?= htmlspecialchars($row['specialize'] ?? '—') ?></div>
              </div>
            </div>
          </td>
          <td>
            <div style="font-weight:700;font-size:.87rem;"><?= date('d M Y', strtotime($row['appointment_date'])) ?></div>
            <div style="font-size:.76rem;color:var(--muted);">
              <i class="fas fa-clock me-1" aria-hidden="true"></i><?= date('h:i A', strtotime($row['appointment_time'])) ?>
            </div>
          </td>
          <td><span style="background:#f1f5f9;color:var(--muted);padding:3px 10px;border-radius:20px;font-size:.76rem;font-weight:600;"><?= htmlspecialchars($row['specialize'] ?? '—') ?></span></td>
          <td style="font-weight:800;color:var(--success);">Rs <?= number_format((int)$row['doctor_fee']) ?></td>
          <td><span class="badge-status <?= $cls ?>"><?= ucfirst($st) ?></span></td>
          <td>
            <?php if ($can_cancel): ?>
            <form method="POST" action="" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
              <input type="hidden" name="appointment_id" value="<?= (int)$row['appointment_id'] ?>">
              <button type="submit" class="btn-cancel-apt"
                      onclick="return confirm('Cancel this appointment?')"
                      aria-label="Cancel appointment <?= (int)$row['appointment_id'] ?>">
                <i class="fas fa-times me-1" aria-hidden="true"></i>Cancel
              </button>
            </form>
            <?php else: ?>
              <span style="color:var(--muted);font-size:.8rem;">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
var toggle  = document.getElementById('sidebarToggle');
var sidebar = document.getElementById('sidebar');
var overlay = document.getElementById('sidebarOverlay');
if (toggle) { toggle.addEventListener('click', function() { sidebar.classList.toggle('show'); overlay.classList.toggle('show'); }); }
if (overlay) { overlay.addEventListener('click', function() { sidebar.classList.remove('show'); overlay.classList.remove('show'); }); }
</script>
</body>
</html>
