<?php
// ── Auth & DB ──────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/connect.php';

// Patient login check
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'patient') {
    header('Location: login.php');
    exit;
}
$patient_id = $_SESSION['user_id'];

$msg = '';
$err = '';

// ── Doctor ID from URL ──
$selected_doc_id = intval($_GET['doctor_id'] ?? 0);
if (!$selected_doc_id) {
    header('Location: doctors.php');
    exit;
}

/* ============================================================
   Load Doctor Info
   ============================================================ */
$stmt = mysqli_prepare($connect,
    "SELECT d.doctor_id, d.first_name, d.last_name, d.consultation_fee,
            d.experience, d.doctor_image, d.qualification, d.bio,
            s.specialize, c.city_name
     FROM doctors d
     LEFT JOIN specialization s ON s.specialize_id = d.specialize_id
     LEFT JOIN cities c ON c.city_id = d.city_id
     WHERE d.doctor_id = ? AND d.doctor_status = 1 LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $selected_doc_id);
mysqli_stmt_execute($stmt);
$doctor = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$doctor) {
    header('Location: doctors.php');
    exit;
}

/* ============================================================
   Load Available Slots (jab date select ho)
   ============================================================ */
$slots        = [];
$slots_msg    = '';
$selected_date = trim($_POST['apt_date'] ?? $_GET['apt_date'] ?? '');

if ($selected_date) {
    $day_map = [1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat',0=>'Sun'];
    $dow = $day_map[date('w', strtotime($selected_date))];

    // Weekly availability check
    $stmt = mysqli_prepare($connect,
        "SELECT start_time, end_time FROM doctor_availability
         WHERE doctor_id=? AND day_of_week=? AND is_available=1 LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'is', $selected_doc_id, $dow);
    mysqli_stmt_execute($stmt);
    $avail = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$avail) {
        $slots_msg = 'Doctor is not available on ' . date('l', strtotime($selected_date)) . '.';
    } else {
        // Full day leave check
        $stmt = mysqli_prepare($connect,
            "SELECT id FROM doctor_leaves
             WHERE doctor_id=? AND leave_date=? AND start_time IS NULL LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'is', $selected_doc_id, $selected_date);
        mysqli_stmt_execute($stmt);
        $on_leave = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($on_leave) {
            $slots_msg = 'Doctor is on leave on this date.';
        } else {
            // Generate 30-min slots
            $current = strtotime($selected_date . ' ' . $avail['start_time']);
            $end     = strtotime($selected_date . ' ' . $avail['end_time']);
            $dur     = 30 * 60;

            while (($current + $dur) <= $end) {
                $slot_s = date('H:i:s', $current);
                $slot_e = date('H:i:s', $current + $dur);

                // Partial leave check
                $stmt = mysqli_prepare($connect,
                    "SELECT id FROM doctor_leaves
                     WHERE doctor_id=? AND leave_date=? AND start_time IS NOT NULL
                     AND ? < end_time AND ? > start_time LIMIT 1");
                mysqli_stmt_bind_param($stmt, 'isss', $selected_doc_id, $selected_date, $slot_s, $slot_e);
                mysqli_stmt_execute($stmt);
                $partial = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);

                // Already booked check
                $stmt = mysqli_prepare($connect,
                    "SELECT appointment_id FROM appointments
                     WHERE doctor_id=? AND appointment_date=? AND appointment_time=?
                     AND status != 'cancelled' LIMIT 1");
                mysqli_stmt_bind_param($stmt, 'iss', $selected_doc_id, $selected_date, $slot_s);
                mysqli_stmt_execute($stmt);
                $booked = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);

                if (!$partial && !$booked) {
                    $slots[] = [
                        'time'  => $slot_s,
                        'label' => date('h:i A', $current)
                    ];
                }
                $current += $dur;
            }

            if (empty($slots)) {
                $slots_msg = 'No slots available for this date. All slots are booked.';
            }
        }
    }
}

/* ============================================================
   POST — Book Appointment
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_btn'])) {
    $date = trim($_POST['apt_date'] ?? '');
    $time = trim($_POST['apt_time'] ?? '');

    if (!$date || !$time) {
        $err = 'Please select date and time slot.';
    } elseif ($date < date('Y-m-d')) {
        $err = 'Appointment date cannot be in the past.';
    } else {
        // Double check slot still available
        $chk = mysqli_prepare($connect,
            "SELECT appointment_id FROM appointments
             WHERE doctor_id=? AND appointment_date=? AND appointment_time=?
             AND status != 'cancelled' LIMIT 1");
        mysqli_stmt_bind_param($chk, 'iss', $selected_doc_id, $date, $time);
        mysqli_stmt_execute($chk);
        $already = mysqli_fetch_assoc(mysqli_stmt_get_result($chk));
        mysqli_stmt_close($chk);

        if ($already) {
            $err = 'This slot was just booked by someone. Please choose another time.';
        } else {
            $ins = mysqli_prepare($connect,
                "INSERT INTO appointments
                 (doctor_id, patient_id, appointment_date, appointment_time, status)
                 VALUES (?, ?, ?, ?, 'pending')");
            mysqli_stmt_bind_param($ins, 'isss',
                $selected_doc_id, $patient_id, $date, $time);
            if (mysqli_stmt_execute($ins)) {
                $msg = 'Appointment booked successfully! Pending confirmation from doctor.';
                $selected_date = '';
                $slots = [];
            } else {
                $err = 'Failed to book. Please try again.';
            }
            mysqli_stmt_close($ins);
        }
    }
}

/* ============================================================
   Patient name
   ============================================================ */
$pat_stmt = mysqli_prepare($connect,
    "SELECT name FROM patients WHERE patient_id=? LIMIT 1");
mysqli_stmt_bind_param($pat_stmt, 's', $patient_id);
mysqli_stmt_execute($pat_stmt);
$pat_row  = mysqli_fetch_assoc(mysqli_stmt_get_result($pat_stmt));
$pat_name = $pat_row['name'] ?? 'Patient';
mysqli_stmt_close($pat_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Book Appointment – CARE Group</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    :root{
      --primary:#0b3e8a;--primary-light:#2563eb;--primary-soft:#eef4ff;
      --accent:#06b6d4;--success:#059669;--danger:#dc2626;
      --bg:#f0f6ff;--border:#e5eef9;--text:#1e293b;--muted:#64748b;
      --radius:16px;
    }
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);}

    /* NAV */
    .top-nav{position:sticky;top:0;z-index:100;background:#fff;
      border-bottom:1px solid var(--border);box-shadow:0 2px 12px rgba(11,62,138,.07);
      padding:14px 24px;display:flex;align-items:center;justify-content:space-between;}
    .nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
    .nav-brand-icon{width:36px;height:36px;border-radius:10px;
      background:linear-gradient(135deg,#2563eb,#06b6d4);
      display:flex;align-items:center;justify-content:center;color:#fff;}
    .nav-brand-text{font-weight:800;font-size:1.1rem;color:var(--primary);}
    .btn-back{display:inline-flex;align-items:center;gap:6px;
      background:var(--primary-soft);color:var(--primary);padding:7px 14px;
      border-radius:10px;text-decoration:none;font-weight:600;font-size:.85rem;}
    .btn-back:hover{background:#dbeafe;color:var(--primary);}

    /* PAGE */
    .page-wrap{max-width:860px;margin:0 auto;padding:32px 20px;}
    .page-title{font-size:1.5rem;font-weight:800;color:var(--primary);}
    .page-sub{color:var(--muted);font-size:.9rem;margin-top:4px;}

    /* ALERTS */
    .alert-care{display:flex;align-items:center;gap:10px;padding:14px 18px;
      border-radius:12px;font-size:.9rem;font-weight:600;margin-bottom:20px;}
    .alert-success-care{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;}
    .alert-danger-care{background:#fef2f2;color:#991b1b;border:1px solid #fecaca;}
    .alert-warn-care{background:#fff7ed;color:#92400e;border:1px solid #fed7aa;}

    /* DOCTOR CARD */
    .doctor-card{background:#fff;border:2px solid var(--primary);
      border-radius:var(--radius);padding:22px;margin-bottom:24px;
      display:flex;gap:20px;align-items:flex-start;}
    .doc-avatar{width:80px;height:80px;border-radius:14px;object-fit:cover;flex-shrink:0;}
    .doc-avatar-ph{width:80px;height:80px;border-radius:14px;flex-shrink:0;
      background:linear-gradient(135deg,#dbeafe,#bfdbfe);
      display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:1.8rem;}
    .doc-name{font-weight:800;font-size:1.1rem;color:var(--primary);}
    .doc-spec{color:var(--accent);font-weight:700;font-size:.8rem;
      text-transform:uppercase;letter-spacing:.05em;margin-top:2px;}
    .doc-tags{display:flex;flex-wrap:wrap;gap:7px;margin-top:8px;}
    .doc-tag{background:#f1f5f9;color:var(--muted);border-radius:8px;
      padding:4px 10px;font-size:.76rem;font-weight:600;}
    .doc-fee{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;
      border-radius:10px;padding:6px 14px;font-weight:800;
      font-size:.9rem;margin-top:10px;display:inline-block;}

    /* FORM CARD */
    .form-card{background:#fff;border:1px solid var(--border);
      border-radius:var(--radius);padding:26px;margin-bottom:20px;}
    .step-head{display:flex;align-items:center;gap:10px;
      font-weight:800;color:var(--primary);font-size:.98rem;margin-bottom:16px;}
    .step-num{width:28px;height:28px;border-radius:50%;
      background:var(--primary);color:#fff;
      display:flex;align-items:center;justify-content:center;
      font-size:.8rem;font-weight:800;flex-shrink:0;}

    /* SLOTS */
    .slots-wrap{display:flex;flex-wrap:wrap;gap:8px;margin-top:4px;}
    .slot-label{display:flex;align-items:center;}
    .slot-label input[type=radio]{display:none;}
    .slot-label span{
      padding:9px 18px;border-radius:10px;border:2px solid var(--border);
      background:#fff;color:var(--text);font-weight:600;font-size:.84rem;
      cursor:pointer;transition:all .15s;display:inline-block;}
    .slot-label input[type=radio]:checked + span{
      background:var(--primary);border-color:var(--primary);color:#fff;}
    .slot-label span:hover{
      border-color:var(--primary-light);background:var(--primary-soft);color:var(--primary);}

    /* SUMMARY BOX */
    .summary-box{background:var(--primary-soft);border:1px solid #bfdbfe;
      border-radius:12px;padding:16px 20px;font-size:.9rem;color:var(--primary);}
    .summary-box div{padding:3px 0;}
    .summary-box strong{min-width:80px;display:inline-block;}

    /* SUBMIT */
    .btn-book{width:100%;padding:14px;border:none;border-radius:12px;
      background:linear-gradient(135deg,#2563eb,#0b3e8a);
      color:#fff;font-weight:800;font-size:1rem;cursor:pointer;
      margin-top:18px;font-family:inherit;
      display:flex;align-items:center;justify-content:center;gap:8px;
      transition:all .2s;}
    .btn-book:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(37,99,235,.3);}

    .btn-check-slots{display:inline-flex;align-items:center;gap:6px;
      background:var(--primary);color:#fff;border:none;border-radius:10px;
      padding:10px 22px;font-weight:700;font-size:.9rem;cursor:pointer;
      font-family:inherit;margin-top:10px;transition:.15s;}
    .btn-check-slots:hover{background:var(--primary-light);}

    @media(max-width:576px){
      .doctor-card{flex-direction:column;}
    }
  </style>
</head>
<body>

<nav class="top-nav">
  <a href="index.php" class="nav-brand">
    <div class="nav-brand-icon"><i class="fas fa-heartbeat"></i></div>
    <span class="nav-brand-text">CARE Group</span>
  </a>
  <div class="d-flex align-items-center gap-2">
    <span style="font-weight:700;color:var(--primary);font-size:.88rem;">
      <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($pat_name) ?>
    </span>
    <a href="patient_panel/dashboard.php" class="btn-back">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>
</nav>

<div class="page-wrap">

  <div class="mb-4">
    <h2 class="page-title">
      <i class="fas fa-calendar-plus me-2" style="color:var(--accent)"></i>Book Appointment
    </h2>
    <p class="page-sub">Review doctor, select date and pick a time slot</p>
  </div>

  <!-- Alerts -->
  <?php if ($msg): ?>
    <div class="alert-care alert-success-care">
      <i class="fas fa-check-circle fa-lg"></i><?= htmlspecialchars($msg) ?>
    </div>
  <?php endif; ?>
  <?php if ($err): ?>
    <div class="alert-care alert-danger-care">
      <i class="fas fa-exclamation-circle fa-lg"></i><?= htmlspecialchars($err) ?>
    </div>
  <?php endif; ?>

  <!-- DOCTOR INFO -->
  <div class="doctor-card">
    <?php
      $dimg    = $doctor['doctor_image'] ?? '';
      $imgpath = "src/" . $dimg;
    ?>
    <?php if ($dimg && file_exists($imgpath)): ?>
      <img src="<?= htmlspecialchars($imgpath) ?>" class="doc-avatar" alt="Doctor">
    <?php else: ?>
      <div class="doc-avatar-ph"><i class="fas fa-user-md"></i></div>
    <?php endif; ?>
    <div>
      <div class="doc-name">Dr. <?= htmlspecialchars($doctor['first_name'].' '.$doctor['last_name']) ?></div>
      <div class="doc-spec"><?= htmlspecialchars($doctor['specialize'] ?? 'General') ?></div>
      <div class="doc-tags">
        <span class="doc-tag"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($doctor['city_name'] ?? '') ?></span>
        <span class="doc-tag"><i class="fas fa-briefcase me-1"></i><?= $doctor['experience'] ?> yrs exp</span>
        <span class="doc-tag"><i class="fas fa-graduation-cap me-1"></i><?= htmlspecialchars($doctor['qualification'] ?? '') ?></span>
      </div>
      <div class="doc-fee"><i class="fas fa-tag me-1"></i>Consultation Fee: Rs. <?= number_format($doctor['consultation_fee']) ?></div>
    </div>
  </div>

  <!-- STEP 1: Select Date -->
  <div class="form-card">
    <div class="step-head">
      <div class="step-num">1</div> Select Appointment Date
    </div>
    <form method="POST" action="appointment.php?doctor_id=<?= $selected_doc_id ?>">
      <input type="hidden" name="apt_date" id="dateHidden">
      <input type="date" id="dateInput" class="form-control"
             min="<?= date('Y-m-d') ?>"
             value="<?= htmlspecialchars($selected_date) ?>"
             style="border-radius:10px;border:2px solid var(--border);font-weight:600;max-width:260px;"
             onchange="document.getElementById('dateHidden').value=this.value;">
      <button type="submit" name="check_slots" class="btn-check-slots">
        <i class="fas fa-search"></i> Check Available Slots
      </button>
    </form>
  </div>

  <!-- STEP 2: Slots (sirf tab dikhe jab date select ho) -->
  <?php if ($selected_date): ?>
  <div class="form-card">
    <div class="step-head">
      <div class="step-num">2</div>
      Available Slots — <?= date('l, d M Y', strtotime($selected_date)) ?>
    </div>

    <?php if (!empty($slots_msg)): ?>
      <div class="alert-care alert-warn-care">
        <i class="fas fa-exclamation-triangle"></i><?= htmlspecialchars($slots_msg) ?>
      </div>
    <?php elseif (!empty($slots)): ?>

    <form method="POST" action="appointment.php?doctor_id=<?= $selected_doc_id ?>">
      <input type="hidden" name="apt_date" value="<?= htmlspecialchars($selected_date) ?>">

      <div class="slots-wrap">
        <?php foreach ($slots as $slot): ?>
          <label class="slot-label">
            <input type="radio" name="apt_time" value="<?= htmlspecialchars($slot['time']) ?>" required>
            <span><i class="fas fa-clock me-1"></i><?= htmlspecialchars($slot['label']) ?></span>
          </label>
        <?php endforeach; ?>
      </div>

      <hr style="border:none;border-top:1px solid var(--border);margin:22px 0;">

      <!-- STEP 3: Summary + Confirm -->
      <div class="step-head">
        <div class="step-num">3</div> Confirm Your Booking
      </div>
      <div class="summary-box">
        <div><strong>Patient:</strong> <?= htmlspecialchars($pat_name) ?></div>
        <div><strong>Doctor:</strong> Dr. <?= htmlspecialchars($doctor['first_name'].' '.$doctor['last_name']) ?></div>
        <div><strong>Date:</strong> <?= date('l, d M Y', strtotime($selected_date)) ?></div>
        <div><strong>Fee:</strong> Rs. <?= number_format($doctor['consultation_fee']) ?></div>
      </div>

      <button type="submit" name="book_btn" class="btn-book">
        <i class="fas fa-calendar-check"></i> Confirm Appointment
      </button>
      <p class="text-muted mt-2" style="font-size:.78rem;text-align:center;">
        <i class="fas fa-info-circle me-1"></i>Status will be pending until doctor confirms.
      </p>
    </form>

    <?php endif; ?>
  </div>
  <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Date input value hidden field mein set karo page load par bhi
document.addEventListener('DOMContentLoaded', function() {
    var di = document.getElementById('dateInput');
    var dh = document.getElementById('dateHidden');
    if (di && dh && di.value) dh.value = di.value;
});
</script>
</body>
</html>