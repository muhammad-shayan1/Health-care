<?php
require_once __DIR__ . '/_auth.php';
$pat_id = patient_id();
$msg = '';
$err = '';

$doctor_id = intval($_GET['doctor_id'] ?? 0);
if ($doctor_id <= 0) {
    header('Location: search_doctor.php');
    exit;
}

// Fetch doctor info
$stmt = mysqli_prepare($connect,
    "SELECT doctor_id, first_name, last_name, doctor_image, experience,
            consultation_fee, city_id, specialize_id, bio
     FROM doctors WHERE doctor_id = ? AND doctor_status = 1 LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $doctor_id);
mysqli_stmt_execute($stmt);
$doctor = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$doctor) {
    header('Location: search_doctor.php');
    exit;
}

// City name
$city_name = '';
if (!empty($doctor['city_id'])) {
    $sc = mysqli_prepare($connect, "SELECT city_name FROM cities WHERE city_id = ? LIMIT 1");
    mysqli_stmt_bind_param($sc, 'i', $doctor['city_id']);
    mysqli_stmt_execute($sc);
    $cr = mysqli_fetch_assoc(mysqli_stmt_get_result($sc));
    mysqli_stmt_close($sc);
    $city_name = $cr['city_name'] ?? '';
}

// Spec name
$spec_name = '';
if (!empty($doctor['specialize_id'])) {
    $ss = mysqli_prepare($connect, "SELECT specialize FROM specialization WHERE specialize_id = ? LIMIT 1");
    mysqli_stmt_bind_param($ss, 'i', $doctor['specialize_id']);
    mysqli_stmt_execute($ss);
    $sr = mysqli_fetch_assoc(mysqli_stmt_get_result($ss));
    mysqli_stmt_close($ss);
    $spec_name = $sr['specialize'] ?? '';
}

// Doctor image
$img_file = $doctor['doctor_image'] ?? '';
$abs_path = __DIR__ . '/../../admin/src/' . $img_file;
$has_img  = ($img_file && file_exists($abs_path));
$img_src  = $has_img ? '../../admin/src/' . $img_file : '';
$initials = strtoupper(substr($doctor['first_name'], 0, 1) . substr($doctor['last_name'], 0, 1));

// ── Handle booking POST ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf()) {
        $err = 'Invalid form token. Please refresh and try again.';
    } else {
        $appt_date = trim($_POST['appt_date'] ?? '');
        $appt_time = trim($_POST['appt_time'] ?? '');

        if (empty($appt_date) || empty($appt_time)) {
            $err = 'Please select a date and time slot.';
        } elseif ($appt_date < date('Y-m-d')) {
            $err = 'Please select a future date.';
        } else {
            // Check already booked
            $chk = mysqli_prepare($connect,
                "SELECT appointment_id FROM appointments
                 WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ?
                 AND status NOT IN ('cancelled') LIMIT 1");
            mysqli_stmt_bind_param($chk, 'iss', $doctor_id, $appt_date, $appt_time);
            mysqli_stmt_execute($chk);
            $already = mysqli_fetch_assoc(mysqli_stmt_get_result($chk));
            mysqli_stmt_close($chk);

            if ($already) {
                $err = 'This slot is already booked. Please choose another.';
            } else {
                // Check patient duplicate
                $dup = mysqli_prepare($connect,
                    "SELECT appointment_id FROM appointments
                     WHERE patient_id = ? AND doctor_id = ? AND appointment_date = ?
                     AND status NOT IN ('cancelled') LIMIT 1");
                mysqli_stmt_bind_param($dup, 'sis', $pat_id, $doctor_id, $appt_date);
                mysqli_stmt_execute($dup);
                $dup_row = mysqli_fetch_assoc(mysqli_stmt_get_result($dup));
                mysqli_stmt_close($dup);

                if ($dup_row) {
                    $err = 'You already have an appointment with this doctor on this date.';
                } else {
                    $ins = mysqli_prepare($connect,
                        "INSERT INTO appointments (doctor_id, patient_id, appointment_date, appointment_time, status)
                         VALUES (?, ?, ?, ?, 'pending')");
                    mysqli_stmt_bind_param($ins, 'isss', $doctor_id, $pat_id, $appt_date, $appt_time);
                    if (mysqli_stmt_execute($ins)) {
                        $msg = 'Appointment booked successfully! The doctor will confirm shortly.';
                    } else {
                        $err = 'Could not book appointment. Please try again.';
                    }
                    mysqli_stmt_close($ins);
                }
            }
        }
    }
}

// ── Fetch available slots for selected date ──
$selected_date = trim($_POST['appt_date'] ?? $_GET['date'] ?? '');
$slots = [];
if ($selected_date) {
    $day_of_week = strtolower(date('l', strtotime($selected_date)));
    // Get availability
    $av = mysqli_prepare($connect,
        "SELECT start_time, end_time FROM doctor_availability
         WHERE doctor_id = ? AND day_of_week = ? AND is_available = 1 LIMIT 1");
    mysqli_stmt_bind_param($av, 'is', $doctor_id, $day_of_week);
    mysqli_stmt_execute($av);
    $av_row = mysqli_fetch_assoc(mysqli_stmt_get_result($av));
    mysqli_stmt_close($av);

    if ($av_row) {
        // Get booked slots for this date
        $booked = [];
        $bk = mysqli_prepare($connect,
            "SELECT appointment_time FROM appointments
             WHERE doctor_id = ? AND appointment_date = ? AND status NOT IN ('cancelled')");
        mysqli_stmt_bind_param($bk, 'is', $doctor_id, $selected_date);
        mysqli_stmt_execute($bk);
        $bkres = mysqli_stmt_get_result($bk);
        while ($br = mysqli_fetch_assoc($bkres)) {
            $booked[] = substr($br['appointment_time'], 0, 5);
        }
        mysqli_stmt_close($bk);

        // Check leave
        $lv = mysqli_prepare($connect,
            "SELECT start_time, end_time FROM doctor_leaves
             WHERE doctor_id = ? AND leave_date = ? LIMIT 1");
        mysqli_stmt_bind_param($lv, 'is', $doctor_id, $selected_date);
        mysqli_stmt_execute($lv);
        $leave = mysqli_fetch_assoc(mysqli_stmt_get_result($lv));
        mysqli_stmt_close($lv);

        // Generate 30-min slots
        $start = strtotime($av_row['start_time']);
        $end   = strtotime($av_row['end_time']);
        $t     = $start;
        while ($t < $end) {
            $slot_str = date('H:i', $t);
            $available = true;
            if (in_array($slot_str, $booked)) $available = false;
            if ($leave) {
                $ls = strtotime($leave['start_time']);
                $le = strtotime($leave['end_time']);
                if ($ls && $le && $t >= $ls && $t < $le) $available = false;
                if (!$ls && !$le) $available = false; // full day
            }
            $slots[] = ['time' => $slot_str, 'available' => $available];
            $t += 1800;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="Book an appointment with Dr. <?= htmlspecialchars($doctor['first_name'].' '.$doctor['last_name']) ?> on CARE Group patient portal."/>
  <title>Book Appointment | CARE Group Patient Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
<?php require __DIR__ . '/sidebar_navbar.php'; ?>
<main class="main-content" id="main-content">

  <nav aria-label="Breadcrumb" style="margin-bottom:12px;">
    <a href="search_doctor.php" style="color:var(--muted);font-size:.84rem;font-weight:600;text-decoration:none;">
      <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Back to Search
    </a>
  </nav>

  <div class="page-header">
    <h1>Book Appointment</h1>
    <p>Schedule your consultation with the doctor</p>
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

  <!-- Doctor Info Card -->
  <div class="doctor-info-card">
    <?php if ($has_img): ?>
      <img src="<?= htmlspecialchars($img_src) ?>" class="doctor-avatar-big"
           alt="Dr. <?= htmlspecialchars($doctor['first_name'].' '.$doctor['last_name']) ?>"/>
    <?php else: ?>
      <div class="doctor-avatar-big" style="background:linear-gradient(135deg,#0b3e8a,#2563eb);" aria-hidden="true">
        <?= $initials ?>
      </div>
    <?php endif; ?>
    <div class="doctor-details">
      <h4>Dr. <?= htmlspecialchars($doctor['first_name'].' '.$doctor['last_name']) ?></h4>
      <?php if ($spec_name): ?>
        <span class="spec-badge"><i class="fas fa-stethoscope me-1" aria-hidden="true"></i><?= htmlspecialchars($spec_name) ?></span>
      <?php endif; ?>
      <div class="doc-meta-row">
        <?php if ($doctor['experience']): ?>
        <div class="doc-meta-item"><i class="fas fa-briefcase-medical" aria-hidden="true"></i> <?= (int)$doctor['experience'] ?> Years Exp.</div>
        <?php endif; ?>
        <?php if ($city_name): ?>
        <div class="doc-meta-item"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> <?= htmlspecialchars($city_name) ?></div>
        <?php endif; ?>
        <?php if ($doctor['consultation_fee']): ?>
        <div class="doc-meta-item"><i class="fas fa-money-bill-wave" aria-hidden="true"></i> Rs <?= number_format((int)$doctor['consultation_fee']) ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php if (!empty($slots)): ?>
  <div class="availability-strip">
    <div class="avail-dot" aria-hidden="true"></div>
    <span>Doctor is <strong>available</strong> on <?= date('d M Y', strtotime($selected_date)) ?> — select your time slot below</span>
  </div>
  <?php endif; ?>

  <div class="row g-3">
    <!-- Booking Form -->
    <div class="col-12 col-lg-8">
      <div class="card-block">

        <!-- Step 1: Select Date -->
        <form method="POST" action="book_appointment.php?doctor_id=<?= $doctor_id ?>">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <div class="section-heading"><i class="fas fa-calendar me-1" aria-hidden="true"></i> Step 1: Select Date</div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label" for="appt_date">Appointment Date</label>
              <input type="date" class="form-control" name="appt_date" id="appt_date"
                     min="<?= date('Y-m-d') ?>"
                     value="<?= htmlspecialchars($selected_date) ?>"
                     required>
              <div style="font-size:.78rem;color:var(--muted);margin-top:4px;">
                <i class="fas fa-info-circle me-1" aria-hidden="true"></i>Select a future date to load available slots.
              </div>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <button type="submit" name="load_slots" class="btn-search w-100">
                <i class="fas fa-clock me-1" aria-hidden="true"></i> Load Time Slots
              </button>
            </div>
          </div>

          <?php if ($selected_date && empty($slots)): ?>
            <div class="alert-care alert-danger-care">
              <i class="fas fa-calendar-times" aria-hidden="true"></i>
              Doctor is not available on this date. Please choose another date.
            </div>
          <?php endif; ?>

          <?php if (!empty($slots)): ?>
          <hr class="section-divider"/>
          <div class="section-heading"><i class="fas fa-clock me-1" aria-hidden="true"></i> Step 2: Select Time Slot</div>

          <?php
          $morning   = array_filter($slots, fn($s) => (int)explode(':', $s['time'])[0] < 12);
          $afternoon = array_filter($slots, fn($s) => (int)explode(':', $s['time'])[0] >= 12);
          ?>

          <?php if (!empty($morning)): ?>
          <div class="time-slot-label">Morning Slots</div>
          <div class="slots-grid" role="group" aria-label="Morning time slots">
            <?php foreach ($morning as $sl):
              $display = date('h:i A', strtotime($sl['time']));
              $cls     = $sl['available'] ? '' : 'unavailable';
            ?>
              <button type="button"
                      class="time-slot <?= $cls ?>"
                      data-time="<?= $sl['time'] ?>"
                      <?= !$sl['available'] ? 'disabled aria-disabled="true"' : '' ?>
                      aria-label="<?= $display ?> <?= !$sl['available'] ? '— unavailable' : '' ?>">
                <?= $display ?>
              </button>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <?php if (!empty($afternoon)): ?>
          <div class="time-slot-label">Afternoon Slots</div>
          <div class="slots-grid" role="group" aria-label="Afternoon time slots">
            <?php foreach ($afternoon as $sl):
              $display = date('h:i A', strtotime($sl['time']));
              $cls     = $sl['available'] ? '' : 'unavailable';
            ?>
              <button type="button"
                      class="time-slot <?= $cls ?>"
                      data-time="<?= $sl['time'] ?>"
                      <?= !$sl['available'] ? 'disabled aria-disabled="true"' : '' ?>
                      aria-label="<?= $display ?> <?= !$sl['available'] ? '— unavailable' : '' ?>">
                <?= $display ?>
              </button>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <input type="hidden" name="appt_time" id="appt_time" value="">
          <div style="display:flex;gap:14px;flex-wrap:wrap;margin-top:10px;margin-bottom:18px;">
            <span style="font-size:.76rem;color:var(--muted);display:flex;align-items:center;gap:6px;">
              <span style="width:13px;height:13px;background:#fff;border:1.5px solid var(--border);border-radius:3px;display:inline-block;"></span>Available
            </span>
            <span style="font-size:.76rem;color:var(--muted);display:flex;align-items:center;gap:6px;">
              <span style="width:13px;height:13px;background:var(--primary);border-radius:3px;display:inline-block;"></span>Selected
            </span>
            <span style="font-size:.76rem;color:var(--muted);display:flex;align-items:center;gap:6px;">
              <span style="width:13px;height:13px;background:#f8fafc;border:1.5px solid #f1f5f9;border-radius:3px;display:inline-block;"></span>Unavailable
            </span>
          </div>

          <hr class="section-divider"/>
          <div class="section-heading"><i class="fas fa-notes-medical me-1" aria-hidden="true"></i> Step 3: Confirm Booking</div>
          <button type="submit" name="confirm_booking" class="btn-book-appt" id="confirmBtn" disabled>
            <i class="fas fa-calendar-check me-1" aria-hidden="true"></i> Confirm Appointment
          </button>
          <?php endif; ?>

        </form>
      </div>
    </div>

    <!-- Summary -->
    <div class="col-12 col-lg-4">
      <div class="card-block">
        <div class="section-heading"><i class="fas fa-receipt me-1" aria-hidden="true"></i> Booking Summary</div>
        <div class="summary-box">
          <div class="summary-row">
            <span class="label"><i class="fas fa-user-md me-1" aria-hidden="true"></i>Doctor</span>
            <span class="value">Dr. <?= htmlspecialchars($doctor['first_name'].' '.$doctor['last_name']) ?></span>
          </div>
          <?php if ($spec_name): ?>
          <div class="summary-row">
            <span class="label"><i class="fas fa-stethoscope me-1" aria-hidden="true"></i>Specialization</span>
            <span class="value"><?= htmlspecialchars($spec_name) ?></span>
          </div>
          <?php endif; ?>
          <div class="summary-row">
            <span class="label"><i class="fas fa-calendar me-1" aria-hidden="true"></i>Date</span>
            <span class="value" id="summaryDate"><?= $selected_date ? date('d M Y', strtotime($selected_date)) : '—' ?></span>
          </div>
          <div class="summary-row">
            <span class="label"><i class="fas fa-clock me-1" aria-hidden="true"></i>Time</span>
            <span class="value" id="summaryTime" style="color:var(--success);">—</span>
          </div>
          <?php if ($city_name): ?>
          <div class="summary-row">
            <span class="label"><i class="fas fa-map-marker-alt me-1" aria-hidden="true"></i>Location</span>
            <span class="value"><?= htmlspecialchars($city_name) ?></span>
          </div>
          <?php endif; ?>
          <div class="summary-row summary-total">
            <span class="label"><i class="fas fa-money-bill-wave me-1" aria-hidden="true"></i>Fee</span>
            <span class="value">Rs <?= number_format((int)$doctor['consultation_fee']) ?></span>
          </div>
        </div>
        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:12px;font-size:.81rem;color:#92400e;">
          <i class="fas fa-info-circle me-1" aria-hidden="true"></i>
          <strong>Note:</strong> Please arrive 10 minutes early. Bring any previous medical records if available.
        </div>
      </div>
    </div>
  </div>

</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle
var toggle  = document.getElementById('sidebarToggle');
var sidebar = document.getElementById('sidebar');
var overlay = document.getElementById('sidebarOverlay');
if (toggle) {
    toggle.addEventListener('click', function() { sidebar.classList.toggle('show'); overlay.classList.toggle('show'); });
}
if (overlay) {
    overlay.addEventListener('click', function() { sidebar.classList.remove('show'); overlay.classList.remove('show'); });
}

// Time slot selection
document.querySelectorAll('.time-slot:not(.unavailable)').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.time-slot').forEach(function(b) { b.classList.remove('selected'); });
        this.classList.add('selected');
        var time = this.getAttribute('data-time');
        document.getElementById('appt_time').value = time;
        document.getElementById('summaryTime').textContent = this.textContent.trim();
        var confirmBtn = document.getElementById('confirmBtn');
        if (confirmBtn) confirmBtn.disabled = false;
    });
});
</script>
</body>
</html>
