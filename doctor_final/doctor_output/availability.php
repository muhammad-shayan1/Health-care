<?php
require_once __DIR__ . '/_auth.php';

$err = '';
$msg = '';
$days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
$day_names = ['Mon'=>'Monday','Tue'=>'Tuesday','Wed'=>'Wednesday','Thu'=>'Thursday','Fri'=>'Friday','Sat'=>'Saturday','Sun'=>'Sunday'];

/* ============================================================
   WEEKLY AVAILABILITY — load
   ============================================================ */
function load_availability($db, $doc_id) {
    $data = [];
    $sql = "SELECT day_of_week, start_time, end_time, is_available
            FROM doctor_availability
            WHERE doctor_id = ?
            ORDER BY FIELD(day_of_week,'Mon','Tue','Wed','Thu','Fri','Sat','Sun')";
    $stmt = mysqli_prepare($db, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $doc_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($r = mysqli_fetch_assoc($res)) {
            $data[$r['day_of_week']] = $r;
        }
        mysqli_stmt_close($stmt);
    }
    return $data;
}

/* ============================================================
   WEEKLY AVAILABILITY — upsert (FIXED bind_param)
   ============================================================ */
function upsert_availability($db, $doc_id, $dow, $start, $end, $is_avail) {
    // Check exists
    $chk = mysqli_prepare($db, "SELECT id FROM doctor_availability WHERE doctor_id=? AND day_of_week=? LIMIT 1");
    mysqli_stmt_bind_param($chk, 'is', $doc_id, $dow);
    mysqli_stmt_execute($chk);
    $res = mysqli_stmt_get_result($chk);
    $exists = mysqli_num_rows($res) > 0;
    mysqli_stmt_close($chk);

    if ($exists) {
        $upd = mysqli_prepare($db, "UPDATE doctor_availability SET start_time=?, end_time=?, is_available=? WHERE doctor_id=? AND day_of_week=?");
        // FIXED: 'ssiis' — string, string, int, int, string
        mysqli_stmt_bind_param($upd, 'ssiis', $start, $end, $is_avail, $doc_id, $dow);
        $ok = mysqli_stmt_execute($upd);
        mysqli_stmt_close($upd);
        return $ok;
    } else {
        $ins = mysqli_prepare($db, "INSERT INTO doctor_availability (doctor_id, day_of_week, start_time, end_time, is_available) VALUES (?,?,?,?,?)");
        // 'isssi' — int, string, string, string, int
        mysqli_stmt_bind_param($ins, 'isssi', $doc_id, $dow, $start, $end, $is_avail);
        $ok = mysqli_stmt_execute($ins);
        mysqli_stmt_close($ins);
        return $ok;
    }
}

/* ============================================================
   LEAVES — load upcoming
   ============================================================ */
function load_leaves($db, $doc_id) {
    $rows = [];
    $sql = "SELECT id, leave_date, start_time, end_time, reason
            FROM doctor_leaves
            WHERE doctor_id = ? AND leave_date >= CURDATE()
            ORDER BY leave_date ASC";
    $stmt = mysqli_prepare($db, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $doc_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = $r;
        }
        mysqli_stmt_close($stmt);
    }
    return $rows;
}

/* ============================================================
   LEAVES — add
   ============================================================ */
function add_leave($db, $doc_id, $date, $start, $end, $reason) {
    // Check duplicate
    $chk = mysqli_prepare($db, "SELECT id FROM doctor_leaves WHERE doctor_id=? AND leave_date=? LIMIT 1");
    mysqli_stmt_bind_param($chk, 'is', $doc_id, $date);
    mysqli_stmt_execute($chk);
    $res = mysqli_stmt_get_result($chk);
    if (mysqli_num_rows($res) > 0) {
        mysqli_stmt_close($chk);
        return 'duplicate';
    }
    mysqli_stmt_close($chk);

    $ins = mysqli_prepare($db, "INSERT INTO doctor_leaves (doctor_id, leave_date, start_time, end_time, reason) VALUES (?,?,?,?,?)");
    $st  = ($start  === '') ? null : $start;
    $en  = ($end    === '') ? null : $end;
    $rsn = ($reason === '') ? null : $reason;
    mysqli_stmt_bind_param($ins, 'issss', $doc_id, $date, $st, $en, $rsn);
    $ok = mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);
    return $ok ? true : false;
}

/* ============================================================
   LEAVES — delete
   ============================================================ */
function delete_leave($db, $doc_id, $leave_id) {
    $del = mysqli_prepare($db, "DELETE FROM doctor_leaves WHERE id=? AND doctor_id=?");
    mysqli_stmt_bind_param($del, 'ii', $leave_id, $doc_id);
    $ok = mysqli_stmt_execute($del);
    mysqli_stmt_close($del);
    return $ok;
}

/* ============================================================
   HANDLE POST
   ============================================================ */
$doc_id = doctor_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf()) {
        $err = 'Invalid form token. Please refresh and try again.';
    } else {
        $action = $_POST['action'] ?? '';

        /* ---- Save weekly schedule ---- */
        if ($action === 'save_schedule') {
            $ok_all = true;
            foreach ($days as $d) {
                $is_avail = isset($_POST["avail_$d"]) ? 1 : 0;
                $start = trim($_POST["start_$d"] ?? '');
                $end   = trim($_POST["end_$d"]   ?? '');
                if ($is_avail && $start && $end && strcmp($start, $end) >= 0) {
                    $err = "End time must be after start time for {$day_names[$d]}.";
                    $ok_all = false;
                    break;
                }
            }
            if ($ok_all) {
                foreach ($days as $d) {
                    $is_avail = isset($_POST["avail_$d"]) ? 1 : 0;
                    $start = $is_avail ? trim($_POST["start_$d"] ?? '') : '';
                    $end   = $is_avail ? trim($_POST["end_$d"]   ?? '') : '';
                    if (!upsert_availability($connect, $doc_id, $d, $start, $end, $is_avail)) {
                        $err = 'Failed to save schedule. Please try again.';
                        $ok_all = false;
                        break;
                    }
                }
                if ($ok_all) $msg = 'Weekly schedule updated successfully.';
            }
        }

        /* ---- Add leave ---- */
        if ($action === 'add_leave') {
            $ldate    = trim($_POST['leave_date']   ?? '');
            $lstart   = trim($_POST['leave_start']  ?? '');
            $lend     = trim($_POST['leave_end']    ?? '');
            $lrsn     = trim($_POST['leave_reason'] ?? '');
            $full_day = isset($_POST['full_day_leave']);

            if (!$ldate) {
                $err = 'Please select a leave date.';
            } elseif ($ldate < date('Y-m-d')) {
                $err = 'Leave date cannot be in the past.';
            } elseif (!$full_day && $lstart && $lend && strcmp($lstart, $lend) >= 0) {
                $err = 'Leave end time must be after start time.';
            } else {
                if ($full_day) { $lstart = ''; $lend = ''; }
                $result = add_leave($connect, $doc_id, $ldate, $lstart, $lend, $lrsn);
                if ($result === 'duplicate') {
                    $err = 'Leave already exists for this date.';
                } elseif ($result === true) {
                    $msg = 'Leave added successfully.';
                } else {
                    $err = 'Failed to add leave. Please try again.';
                }
            }
        }

        /* ---- Delete leave ---- */
        if ($action === 'delete_leave') {
            $lid = intval($_POST['leave_id'] ?? 0);
            if ($lid > 0 && delete_leave($connect, $doc_id, $lid)) {
                $msg = 'Leave removed successfully.';
            } else {
                $err = 'Failed to remove leave.';
            }
        }
    }
}

/* ============================================================
   LOAD DATA
   ============================================================ */
$existing = load_availability($connect, $doc_id);
$leaves   = load_leaves($connect, $doc_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Availability – Doctor Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet" />
  <link href="../css/style.css" rel="stylesheet" />
  <style>
    body { font-family:'Plus Jakarta Sans',system-ui,Arial,sans-serif; background:#f8fbff; }
    .card-care { background:#fff; border:1px solid #e5eef9; border-radius:16px; }
    .section-title { font-weight:800; color:#0b3e8a; font-size:1rem; margin-bottom:14px; }
    .week-grid { display:flex; flex-direction:column; gap:8px; }
    .dow-row {
      display:grid;
      grid-template-columns: 36px 130px 1fr 1fr;
      gap:10px; align-items:center;
      padding:10px 14px;
      border:1px solid #e5eef9;
      border-radius:12px;
      background:#fafcff;
      transition: background .15s, opacity .15s;
    }
    .dow-row.off { opacity:.5; background:#f1f5f9; }
    .dow-label { font-weight:800; color:#0b3e8a; font-size:.92rem; }
    .time-inputs { display:flex; gap:8px; }
    .time-inputs.hidden { display:none; }
    .leave-badge {
      display:flex; align-items:flex-start; justify-content:space-between;
      background:#fff3f3; border:1px solid #fecaca;
      border-radius:10px; padding:8px 12px;
      font-size:.84rem; font-weight:600; color:#b91c1c;
    }
    .leave-badge .ldate { color:#374151; font-weight:700; }
    .leave-badge .ltime { color:#6b7280; font-size:.78rem; }
    .btn-del { background:none; border:none; color:#ef4444; cursor:pointer; padding:2px 6px; font-size:.85rem; }
    .btn-del:hover { color:#b91c1c; }
  </style>
  <script>if (window.top !== window.self) { document.addEventListener('DOMContentLoaded',()=>{document.body.innerHTML='';}); }</script>
</head>
<body>
<?php include __DIR__ . '/sidebar_navbar.php'; ?>
<main class="main">

  <div class="mb-3">
    <h3 style="font-weight:800;color:#0b3e8a;">Availability</h3>
    <p class="text-muted mb-0">Manage your weekly schedule and mark leaves</p>
  </div>

  <?php if ($msg): ?>
    <div class="alert alert-success d-flex align-items-center gap-2">
      <i class="fas fa-check-circle"></i><?= htmlspecialchars($msg) ?>
    </div>
  <?php endif; ?>
  <?php if ($err): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2">
      <i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($err) ?>
    </div>
  <?php endif; ?>

  <div class="row g-3">

    <!-- Weekly Schedule -->
    <div class="col-12 col-xl-7">
      <div class="card-care p-3 p-md-4">
        <div class="section-title"><i class="fas fa-calendar-week me-2"></i>Weekly Schedule</div>
        <form method="post" action="">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <input type="hidden" name="action"     value="save_schedule">
          <div class="week-grid">
            <?php foreach ($days as $d):
              $row      = $existing[$d] ?? [];
              $is_avail = isset($row['is_available']) ? (int)$row['is_available'] : 1;
              $st = $row['start_time'] ?? '09:00';
              $en = $row['end_time']   ?? '17:00';
              $st = $st ? substr($st, 0, 5) : '09:00';
              $en = $en ? substr($en, 0, 5) : '17:00';
            ?>
            <div class="dow-row <?= $is_avail ? '' : 'off' ?>" id="row_<?= $d ?>">
              <div>
                <input type="checkbox" class="form-check-input dow-toggle"
                       name="avail_<?= $d ?>" id="chk_<?= $d ?>"
                       data-day="<?= $d ?>"
                       <?= $is_avail ? 'checked' : '' ?>>
              </div>
              <label for="chk_<?= $d ?>" class="dow-label mb-0" style="cursor:pointer;">
                <?= $day_names[$d] ?>
              </label>
              <div class="time-inputs <?= $is_avail ? '' : 'hidden' ?>" id="times_<?= $d ?>">
                <div class="w-100">
                  <label class="form-label mb-1" style="font-size:.78rem;">Start</label>
                  <input type="time" class="form-control form-control-sm"
                         name="start_<?= $d ?>" value="<?= htmlspecialchars($st) ?>">
                </div>
                <div class="w-100">
                  <label class="form-label mb-1" style="font-size:.78rem;">End</label>
                  <input type="time" class="form-control form-control-sm"
                         name="end_<?= $d ?>" value="<?= htmlspecialchars($en) ?>">
                </div>
              </div>
              <?php if (!$is_avail): ?>
                <div><span class="text-muted" style="font-size:.82rem;">Day Off</span></div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i>Save Schedule
            </button>
            <a href="dashboard.php" class="btn btn-outline-secondary">Back</a>
          </div>
        </form>
        <p class="text-muted mt-3" style="font-size:.82rem;">
          <i class="fas fa-info-circle me-1"></i>Uncheck a day to mark it as day off.
        </p>
      </div>
    </div>

    <!-- Leaves -->
    <div class="col-12 col-xl-5">

      <!-- Add Leave -->
      <div class="card-care p-3 p-md-4 mb-3">
        <div class="section-title"><i class="fas fa-calendar-times me-2"></i>Mark Leave</div>
        <form method="post" action="">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <input type="hidden" name="action"     value="add_leave">

          <div class="mb-2">
            <label class="form-label" style="font-size:.85rem;font-weight:700;">Leave Date</label>
            <input type="date" name="leave_date" class="form-control form-control-sm"
                   min="<?= date('Y-m-d') ?>" required>
          </div>

          <div class="mb-2 form-check">
            <input type="checkbox" class="form-check-input" id="fullDayChk"
                   name="full_day_leave" checked onchange="toggleLeaveTime(this)">
            <label class="form-check-label" for="fullDayChk" style="font-size:.85rem;">Full Day Leave</label>
          </div>

          <div id="leaveTimeBlock" style="display:none;">
            <div class="row g-2 mb-2">
              <div class="col-6">
                <label class="form-label" style="font-size:.82rem;">From</label>
                <input type="time" name="leave_start" class="form-control form-control-sm">
              </div>
              <div class="col-6">
                <label class="form-label" style="font-size:.82rem;">To</label>
                <input type="time" name="leave_end" class="form-control form-control-sm">
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-size:.85rem;font-weight:700;">
              Reason <span class="text-muted fw-normal">(optional)</span>
            </label>
            <input type="text" name="leave_reason" class="form-control form-control-sm"
                   placeholder="e.g. Personal, Conference..." maxlength="200">
          </div>

          <button type="submit" class="btn btn-danger btn-sm w-100">
            <i class="fas fa-ban me-1"></i>Mark Unavailable
          </button>
        </form>
      </div>

      <!-- Upcoming Leaves List -->
      <div class="card-care p-3 p-md-4">
        <div class="section-title"><i class="fas fa-list-ul me-2"></i>Upcoming Leaves</div>
        <?php if (empty($leaves)): ?>
          <div class="text-center py-3">
            <i class="fas fa-check-circle" style="font-size:1.8rem;color:#22c55e;"></i>
            <p class="text-muted mt-2 mb-0" style="font-size:.88rem;">No upcoming leaves!</p>
          </div>
        <?php else: ?>
          <div class="d-flex flex-column gap-2">
            <?php foreach ($leaves as $lv):
              $ldate    = date('D, d M Y', strtotime($lv['leave_date']));
              $is_full  = ($lv['start_time'] === null || $lv['start_time'] === '');
              $time_lbl = $is_full
                ? 'Full Day'
                : substr($lv['start_time'],0,5).' – '.substr($lv['end_time'],0,5);
            ?>
            <div class="leave-badge">
              <div>
                <div class="ldate"><i class="fas fa-calendar-day me-1"></i><?= htmlspecialchars($ldate) ?></div>
                <div class="ltime">
                  <i class="fas fa-clock me-1"></i><?= htmlspecialchars($time_lbl) ?>
                  <?php if (!empty($lv['reason'])): ?>
                    &nbsp;·&nbsp;<?= htmlspecialchars($lv['reason']) ?>
                  <?php endif; ?>
                </div>
              </div>
              <form method="post" action="" style="margin:0;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <input type="hidden" name="action"   value="delete_leave">
                <input type="hidden" name="leave_id" value="<?= (int)$lv['id'] ?>">
                <button type="submit" class="btn-del" title="Remove leave"
                        onclick="return confirm('Remove this leave?')">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </form>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toggle day on/off
document.querySelectorAll('.dow-toggle').forEach(function(chk) {
    chk.addEventListener('change', function() {
        var d   = this.dataset.day;
        var row = document.getElementById('row_' + d);
        var tim = document.getElementById('times_' + d);
        if (this.checked) {
            row.classList.remove('off');
            if (tim) tim.classList.remove('hidden');
        } else {
            row.classList.add('off');
            if (tim) tim.classList.add('hidden');
        }
    });
});

// Toggle leave time block
function toggleLeaveTime(chk) {
    document.getElementById('leaveTimeBlock').style.display = chk.checked ? 'none' : 'block';
}
</script>
</body>
</html>
