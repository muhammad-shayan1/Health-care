<?php
require_once __DIR__ . '/_auth.php';
$pat_id = patient_id();
$msg = '';
$err = '';

// Fetch patient
$stmt = mysqli_prepare($connect,
    "SELECT patient_id, name, email, phone, gender, address, city_id, created_at
     FROM patients WHERE patient_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $pat_id);
mysqli_stmt_execute($stmt);
$patient = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$patient) {
    header('Location: ../login.php');
    exit;
}

// Cities for dropdown
$cities = [];
$res = mysqli_query($connect, "SELECT city_id, city_name FROM cities WHERE city_status='active' ORDER BY city_name");
while ($r = mysqli_fetch_assoc($res)) $cities[] = $r;

// Total appointments
$stmt2 = mysqli_prepare($connect, "SELECT COUNT(*) AS cnt FROM appointments WHERE patient_id = ?");
mysqli_stmt_bind_param($stmt2, 's', $pat_id);
mysqli_stmt_execute($stmt2);
$cnt_apt = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2))['cnt'] ?? 0);
mysqli_stmt_close($stmt2);

// Avatar initials
$words    = explode(' ', trim($patient['name']));
$initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf()) {
        $err = 'Invalid form token. Please refresh and try again.';
    } else {
        $name    = trim($_POST['name']    ?? '');
        $phone   = trim($_POST['phone']   ?? '');
        $gender  = trim($_POST['gender']  ?? '');
        $address = trim($_POST['address'] ?? '');
        $city_id = intval($_POST['city_id'] ?? 0);

        if (empty($name)) {
            $err = 'Name is required.';
        } elseif (strlen($name) < 2 || strlen($name) > 100) {
            $err = 'Name must be between 2 and 100 characters.';
        } elseif ($phone !== '' && !preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
            $err = 'Please enter a valid phone number.';
        } else {
            $upd = mysqli_prepare($connect,
                "UPDATE patients SET name=?, phone=?, gender=?, address=?, city_id=? WHERE patient_id=?");
            mysqli_stmt_bind_param($upd, 'ssssis', $name, $phone, $gender, $address, $city_id, $pat_id);
            if (mysqli_stmt_execute($upd)) {
                $msg = 'Profile updated successfully.';
                // Refresh data
                $stmt = mysqli_prepare($connect, "SELECT patient_id, name, email, phone, gender, address, city_id, created_at FROM patients WHERE patient_id = ? LIMIT 1");
                mysqli_stmt_bind_param($stmt, 's', $pat_id);
                mysqli_stmt_execute($stmt);
                $patient = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);
                // Update initials
                $words    = explode(' ', trim($patient['name']));
                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            } else {
                $err = 'Failed to update profile. Please try again.';
            }
            mysqli_stmt_close($upd);
        }
    }
}

// City name
$city_name = '';
if (!empty($patient['city_id'])) {
    foreach ($cities as $c) {
        if ((int)$c['city_id'] === (int)$patient['city_id']) {
            $city_name = $c['city_name'];
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="View and update your profile on CARE Group patient portal."/>
  <title>My Profile | CARE Group Patient Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
<?php require __DIR__ . '/sidebar_navbar.php'; ?>
<main class="main-content" id="main-content">

  <div class="page-header">
    <h1>My Profile</h1>
    <p>Manage your personal information and account details</p>
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

  <!-- Profile Hero -->
  <section class="profile-hero" aria-label="Profile overview">
    <div class="profile-avatar-big" aria-hidden="true"><?= htmlspecialchars($initials) ?></div>
    <div>
      <h3><?= htmlspecialchars($patient['name']) ?></h3>
      <div class="email-badge"><i class="fas fa-envelope me-1" aria-hidden="true"></i><?= htmlspecialchars($patient['email']) ?></div>
      <div class="profile-badges">
        <span class="profile-badge"><i class="fas fa-id-card" aria-hidden="true"></i> <?= htmlspecialchars($pat_id) ?></span>
        <?php if ($city_name): ?>
        <span class="profile-badge"><i class="fas fa-map-marker-alt" aria-hidden="true"></i> <?= htmlspecialchars($city_name) ?></span>
        <?php endif; ?>
        <?php if ($patient['created_at']): ?>
        <span class="profile-badge"><i class="fas fa-calendar" aria-hidden="true"></i> Member since <?= date('M Y', strtotime($patient['created_at'])) ?></span>
        <?php endif; ?>
        <span class="profile-badge" style="background:rgba(16,185,129,.25);">
          <i class="fas fa-check-circle" aria-hidden="true"></i> Active
        </span>
      </div>
    </div>
  </section>

  <div class="row g-3">
    <!-- Edit Form -->
    <div class="col-12 col-lg-8">
      <div class="form-card">
        <div class="form-card-header">
          <h6><i class="fas fa-user-edit me-2" aria-hidden="true"></i>Edit Personal Information</h6>
          <p>Update your personal details and contact information</p>
        </div>
        <div class="form-card-body">
          <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

            <div class="section-heading"><i class="fas fa-user me-1" aria-hidden="true"></i> Basic Information</div>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label" for="name">Full Name</label>
                <div class="input-group">
                  <span class="input-group-text" aria-hidden="true"><i class="fas fa-user"></i></span>
                  <input type="text" class="form-control" id="name" name="name"
                         value="<?= htmlspecialchars($patient['name']) ?>" required maxlength="100">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                  <span class="input-group-text" aria-hidden="true"><i class="fas fa-envelope"></i></span>
                  <input type="email" class="form-control"
                         value="<?= htmlspecialchars($patient['email']) ?>" readonly
                         aria-describedby="emailNote">
                </div>
                <small id="emailNote" style="font-size:.75rem;color:var(--muted);">Email cannot be changed.</small>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="phone">Phone Number</label>
                <div class="input-group">
                  <span class="input-group-text" aria-hidden="true"><i class="fas fa-phone"></i></span>
                  <input type="tel" class="form-control" id="phone" name="phone"
                         value="<?= htmlspecialchars($patient['phone'] ?? '') ?>" maxlength="20">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="gender">Gender</label>
                <div class="input-group">
                  <span class="input-group-text" aria-hidden="true"><i class="fas fa-venus-mars"></i></span>
                  <select class="form-select" id="gender" name="gender" style="border-left:none;border-radius:0 10px 10px 0;">
                    <option value="">Select Gender</option>
                    <option value="male"   <?= ($patient['gender'] ?? '') === 'male'   ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= ($patient['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                    <option value="other"  <?= ($patient['gender'] ?? '') === 'other'  ? 'selected' : '' ?>>Other</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="section-heading"><i class="fas fa-map-marker-alt me-1" aria-hidden="true"></i> Address Information</div>
            <div class="row g-3 mb-4">
              <div class="col-12">
                <label class="form-label" for="address">Street Address</label>
                <div class="input-group">
                  <span class="input-group-text" aria-hidden="true"><i class="fas fa-home"></i></span>
                  <textarea class="form-control" id="address" name="address" rows="2"
                            style="border-left:none;border-radius:0 10px 10px 0;"><?= htmlspecialchars($patient['address'] ?? '') ?></textarea>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="city_id">City</label>
                <div class="input-group">
                  <span class="input-group-text" aria-hidden="true"><i class="fas fa-city"></i></span>
                  <select class="form-select" id="city_id" name="city_id" style="border-left:none;border-radius:0 10px 10px 0;">
                    <option value="0">Select City</option>
                    <?php foreach ($cities as $c): ?>
                      <option value="<?= (int)$c['city_id'] ?>"
                        <?= (int)($patient['city_id'] ?? 0) === (int)$c['city_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['city_name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="d-flex gap-3 flex-wrap">
              <button type="submit" class="btn-update">
                <i class="fas fa-save me-1" aria-hidden="true"></i>Save Changes
              </button>
              <button type="reset" class="btn-reset-form">
                <i class="fas fa-undo me-1" aria-hidden="true"></i>Reset
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Right Panel -->
    <div class="col-12 col-lg-4">
      <div class="form-card">
        <div class="form-card-header">
          <h6><i class="fas fa-id-badge me-2" aria-hidden="true"></i>Account Summary</h6>
        </div>
        <div class="form-card-body">
          <div class="info-row">
            <div class="info-icon" aria-hidden="true"><i class="fas fa-hashtag"></i></div>
            <div>
              <div class="info-label">Patient ID</div>
              <div class="info-value" style="font-size:.78rem;"><?= htmlspecialchars($pat_id) ?></div>
            </div>
          </div>
          <?php if ($patient['created_at']): ?>
          <div class="info-row">
            <div class="info-icon" aria-hidden="true"><i class="fas fa-calendar-alt"></i></div>
            <div>
              <div class="info-label">Member Since</div>
              <div class="info-value"><?= date('d M Y', strtotime($patient['created_at'])) ?></div>
            </div>
          </div>
          <?php endif; ?>
          <div class="info-row">
            <div class="info-icon" aria-hidden="true"><i class="fas fa-calendar-check"></i></div>
            <div>
              <div class="info-label">Total Appointments</div>
              <div class="info-value"><?= $cnt_apt ?> Appointment<?= $cnt_apt !== 1 ? 's' : '' ?></div>
            </div>
          </div>
          <div class="info-row">
            <div class="info-icon" aria-hidden="true"><i class="fas fa-venus-mars"></i></div>
            <div>
              <div class="info-label">Gender</div>
              <div class="info-value"><?= htmlspecialchars(ucfirst($patient['gender'] ?? '—')) ?></div>
            </div>
          </div>
          <div class="info-row">
            <div class="info-icon" aria-hidden="true"><i class="fas fa-check-circle" style="color:#059669;"></i></div>
            <div>
              <div class="info-label">Account Status</div>
              <div class="info-value" style="color:#059669;">Active</div>
            </div>
          </div>
        </div>
      </div>
    </div>
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
