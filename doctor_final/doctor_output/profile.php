<?php
require_once __DIR__ . '/_auth.php';
$msg = '';
$err = '';
$doc_id = doctor_id();

function get_doctor($db, $doctor_id)
{
  $sql = "SELECT doctor_id, first_name, last_name, email, phone, address,
                   qualification, experience, consultation_fee, city_id,
                   specialize_id, doctor_image
            FROM doctors WHERE doctor_id = ? LIMIT 1";
  $stmt = mysqli_prepare($db, $sql);
  if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $doctor_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    return $row ?: null;
  }
  return null;
}
// ── Password Change ──
// if (isset($_POST['change_password']) && empty($err)) {
//   $current_pass = $_POST['current_password'] ?? '';
//   $new_pass = $_POST['new_password'] ?? '';
//   $confirm_pass = $_POST['confirm_password'] ?? '';

//   if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
//     $err = 'Tamam password fields fill karo.';
//   } elseif ($new_pass !== $confirm_pass) {
//     $err = 'Naya password aur confirm password match nahi karte.';
//   } elseif (strlen($new_pass) < 6) {
//     $err = 'Password kam az kam 6 characters ka hona chahiye.';
//   } else {
//     // DB se current password fetch karo
//     $stmt_chk = mysqli_prepare($connect, "SELECT password FROM doctors WHERE doctor_id=? LIMIT 1");
//     mysqli_stmt_bind_param($stmt_chk, 'i', $doc_id);
//     mysqli_stmt_execute($stmt_chk);
//     $res_chk = mysqli_stmt_get_result($stmt_chk);
//     $row_chk = mysqli_fetch_assoc($res_chk);
//     mysqli_stmt_close($stmt_chk);

//     if (!$row_chk || !password_verify($current_pass, $row_chk['password'])) {
//       $err = 'Purana password galat hai.';
//     } else {
//       $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
//       $stmt_up = mysqli_prepare($connect, "UPDATE doctors SET password=? WHERE doctor_id=?");
//       mysqli_stmt_bind_param($stmt_up, 'si', $hashed, $doc_id);
//       if (mysqli_stmt_execute($stmt_up)) {
//         $msg = 'Password successfully change ho gaya!';
//       } else {
//         $err = 'Password update fail hua. Dobara koshish karo.';
//       }
//       mysqli_stmt_close($stmt_up);
//     }
//   }
// }

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//   if (!check_csrf()) {
//     $err = 'Invalid form token. Please refresh and try again.';
//   } else {
//     $phone = trim($_POST['phone'] ?? '');
//     $address = trim($_POST['address'] ?? '');
//     $qualification = trim($_POST['qualification'] ?? '');
//     $experience = intval($_POST['experience'] ?? 0);
//     $fee = intval($_POST['consultation_fee'] ?? 0);
//     $city_id = intval($_POST['city_id'] ?? 0);
//     $spec_id = intval($_POST['specialize_id'] ?? 0);

//     // ── Image upload ──
//     $new_image = '';
//     if (isset($_FILES['doctor_image']) && $_FILES['doctor_image']['error'] === UPLOAD_ERR_OK) {
//       $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
//       $max_size = 2 * 1024 * 1024; // 2MB

//       if (!in_array($_FILES['doctor_image']['type'], $allowed_types)) {
//         $err = 'Invalid image type. Only JPEG, PNG, WebP allowed.';
//       } elseif ($_FILES['doctor_image']['size'] > $max_size) {
//         $err = 'Image size must be less than 2MB.';
//       } else {
//         $ext = pathinfo($_FILES['doctor_image']['name'], PATHINFO_EXTENSION);
//         $new_image = 'doc_' . $doc_id . '_' . time() . '.' . $ext;
//         // __DIR__ = php_project/doctor_final/doctor_output
//         // ../../admin/src = php_project/admin/src
//         $upload_path = __DIR__ . '/../../admin/src/' . $new_image;
//         if (!move_uploaded_file($_FILES['doctor_image']['tmp_name'], $upload_path)) {
//           $err = 'Failed to upload image. Please try again.';
//           $new_image = '';
//         }
//       }
//     }

//     if (empty($err)) {
//       if ($phone !== '' && !preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
//         $err = 'Please provide a valid phone number.';
//       } elseif ($fee < 0 || $fee > 1000000) {
//         $err = 'Invalid consultation fee.';
//       } elseif ($experience < 0 || $experience > 80) {
//         $err = 'Invalid years of experience.';
//       } else {
//         if ($new_image) {
//           // Image ke saath update
//           $sql = "UPDATE doctors
//                              SET phone=?, address=?, qualification=?,
//                                  experience=?, consultation_fee=?, city_id=?,
//                                  specialize_id=?, doctor_image=?
//                              WHERE doctor_id=?";
//           $stmt = mysqli_prepare($connect, $sql);
//           if ($stmt) {
//             mysqli_stmt_bind_param(
//               $stmt,
//               'sssiiiisi',
//               $phone,
//               $address,
//               $qualification,
//               $experience,
//               $fee,
//               $city_id,
//               $spec_id,
//               $new_image,
//               $doc_id
//             );
//             if (mysqli_stmt_execute($stmt)) {
//               $msg = 'Profile updated successfully.';
//             } else {
//               $err = 'Failed to update profile.';
//             }
//             mysqli_stmt_close($stmt);
//           }
//         } else {
//           // Baghair image ke update
//           $sql = "UPDATE doctors
//                              SET phone=?, address=?, qualification=?,
//                                  experience=?, consultation_fee=?, city_id=?,
//                                  specialize_id=?
//                              WHERE doctor_id=?";
//           $stmt = mysqli_prepare($connect, $sql);
//           if ($stmt) {
//             mysqli_stmt_bind_param(
//               $stmt,
//               'sssiiiii',
//               $phone,
//               $address,
//               $qualification,
//               $experience,
//               $fee,
//               $city_id,
//               $spec_id,
//               $doc_id
//             );
//             if (mysqli_stmt_execute($stmt)) {
//               $msg = 'Profile updated successfully.';
//             } else {
//               $err = 'Failed to update profile.';
//             }
//             mysqli_stmt_close($stmt);
//           }
//         }
//       }
//     }
//   }
// }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!check_csrf()) {
    $err = 'Invalid form token. Please refresh and try again.';
  } elseif (isset($_POST['change_password'])) {
    // ── Password Change ──
    $current_pass = trim($_POST['current_password'] ?? '');
    $new_pass = trim($_POST['new_password'] ?? '');
    $confirm_pass = trim($_POST['confirm_password'] ?? '');

    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
      $err = 'Tamam password fields fill karo.';
    } elseif ($new_pass !== $confirm_pass) {
      $err = 'Naya password aur confirm password match nahi karte.';
    } elseif (strlen($new_pass) < 6) {
      $err = 'Password kam az kam 6 characters ka hona chahiye.';
    } else {
      $did = (int) $doc_id;
      $result_chk = mysqli_query(
        $connect,
        "SELECT password FROM doctors WHERE doctor_id=$did LIMIT 1"
      );
      $row_chk = mysqli_fetch_assoc($result_chk);

      if (!$row_chk || !password_verify($current_pass, $row_chk['password'])) {
        $err = 'Purana password galat hai.';
      } else {
        $safe_pass = mysqli_real_escape_string($connect, password_hash($new_pass, PASSWORD_DEFAULT));
        $update = mysqli_query(
          $connect,
          "UPDATE doctors SET password='$safe_pass' WHERE doctor_id=$did"
        );
        if ($update) {
          $msg = 'Password successfully change ho gaya!';
        } else {
          $err = 'Password update fail hua. Dobara koshish karo.';
        }
      }
    }
  } else {
    // ── Profile Update ──
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $experience = intval($_POST['experience'] ?? 0);
    $fee = intval($_POST['consultation_fee'] ?? 0);
    $city_id = intval($_POST['city_id'] ?? 0);
    $spec_id = intval($_POST['specialize_id'] ?? 0);

    // ── Image upload ──
    $new_image = '';
    if (isset($_FILES['doctor_image']) && $_FILES['doctor_image']['error'] === UPLOAD_ERR_OK) {
      $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
      $max_size = 2 * 1024 * 1024;

      if (!in_array($_FILES['doctor_image']['type'], $allowed_types)) {
        $err = 'Invalid image type. Only JPEG, PNG, WebP allowed.';
      } elseif ($_FILES['doctor_image']['size'] > $max_size) {
        $err = 'Image size must be less than 2MB.';
      } else {
        $ext = pathinfo($_FILES['doctor_image']['name'], PATHINFO_EXTENSION);
        $new_image = 'doc_' . $doc_id . '_' . time() . '.' . $ext;
        $upload_path = __DIR__ . '/../../admin/src/' . $new_image;
        if (!move_uploaded_file($_FILES['doctor_image']['tmp_name'], $upload_path)) {
          $err = 'Failed to upload image. Please try again.';
          $new_image = '';
        }
      }
    }

    if (empty($err)) {
      if ($phone !== '' && !preg_match('/^[0-9+\-\s]{7,20}$/', $phone)) {
        $err = 'Please provide a valid phone number.';
      } elseif ($fee < 0 || $fee > 1000000) {
        $err = 'Invalid consultation fee.';
      } elseif ($experience < 0 || $experience > 80) {
        $err = 'Invalid years of experience.';
      } else {
        if ($new_image) {
          $sql = "UPDATE doctors SET phone=?, address=?, qualification=?,
                             experience=?, consultation_fee=?, city_id=?,
                             specialize_id=?, doctor_image=? WHERE doctor_id=?";
          $stmt = mysqli_prepare($connect, $sql);
          if ($stmt) {
            mysqli_stmt_bind_param(
              $stmt,
              'sssiiiisi',
              $phone,
              $address,
              $qualification,
              $experience,
              $fee,
              $city_id,
              $spec_id,
              $new_image,
              $doc_id
            );
            if (mysqli_stmt_execute($stmt)) {
              $msg = 'Profile updated successfully.';
            } else {
              $err = 'Failed to update profile.';
            }
            mysqli_stmt_close($stmt);
          }
        } else {
          $sql = "UPDATE doctors SET phone=?, address=?, qualification=?,
                             experience=?, consultation_fee=?, city_id=?,
                             specialize_id=? WHERE doctor_id=?";
          $stmt = mysqli_prepare($connect, $sql);
          if ($stmt) {
            mysqli_stmt_bind_param(
              $stmt,
              'sssiiiii',
              $phone,
              $address,
              $qualification,
              $experience,
              $fee,
              $city_id,
              $spec_id,
              $doc_id
            );
            if (mysqli_stmt_execute($stmt)) {
              $msg = 'Profile updated successfully.';
            } else {
              $err = 'Failed to update profile.';
            }
            mysqli_stmt_close($stmt);
          }
        }
      }
    }
  }
}

$doctor = get_doctor($connect, $doc_id);

// ── Image path — file_exists() ko absolute path chahiye ──
$img_file = $doctor['doctor_image'] ?? '';
$abs_img = __DIR__ . '/../../admin/src/' . $img_file;   // absolute — file_exists ke liye
$rel_img = '../../admin/src/' . $img_file;               // relative  — browser src ke liye
$avatar_url = 'https://ui-avatars.com/api/?name='
  . urlencode(($doctor['first_name'] ?? 'D') . '+' . ($doctor['last_name'] ?? 'r'))
  . '&background=0b3e8a&color=fff&size=200';
$img_src = ($img_file && file_exists($abs_img)) ? $rel_img : $avatar_url;

// ── Specialization name ──
$spec_name = '';
if (!empty($doctor['specialize_id'])) {
  $sp = mysqli_query(
    $connect,
    "SELECT specialize FROM specialization WHERE specialize_id=" . (int) $doctor['specialize_id']
  );
  if ($sp) {
    $sp_row = mysqli_fetch_assoc($sp);
    $spec_name = $sp_row['specialize'] ?? '';
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Profile – Doctor Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
    rel="stylesheet" />
  <link href="../css/style.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Plus Jakarta Sans', system-ui, Arial, sans-serif;
      background: #f8fbff;
    }

    .main {
      padding: 24px;
    }

    .card-care {
      background: #fff;
      border: 1px solid #e5eef9;
      border-radius: 16px;
    }

    .form-section-title {
      font-weight: 800;
      color: #0b3e8a;
      margin-bottom: 8px;
    }

    .info-badge {
      background: #eef4ff;
      border: 1px solid #d6e4ff;
      color: #0b3e8a;
      border-radius: 10px;
      padding: 6px 10px;
      font-size: .8rem;
      font-weight: 700;
    }

    /* Avatar */
    .avatar-wrap {
      position: relative;
      width: 120px;
      height: 120px;
      margin: 0 auto 12px;
    }

    .avatar-img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #e5eef9;
      box-shadow: 0 4px 16px rgba(11, 62, 138, .12);
    }

    .avatar-edit {
      position: absolute;
      bottom: 4px;
      right: 4px;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: #0b3e8a;
      color: #fff;
      border: 2px solid #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: .78rem;
      transition: .15s;
    }

    .avatar-edit:hover {
      background: #2563eb;
    }

    .avatar-edit input {
      display: none;
    }

    .avatar-name {
      text-align: center;
      font-weight: 800;
      color: #0b3e8a;
      font-size: 1rem;
    }

    .avatar-spec {
      text-align: center;
      color: #06b6d4;
      font-size: .8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .05em;
      margin-top: 2px;
    }

    /* Preview */
    #imgPreviewWrap {
      display: none;
      text-align: center;
      margin-top: 8px;
    }

    #imgPreview {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #2563eb;
    }

    .img-hint {
      font-size: .75rem;
      color: #64748b;
      text-align: center;
      margin-top: 4px;
    }
  </style>
  <script>
    if (window.top !== window.self) { document.addEventListener('DOMContentLoaded', () => { document.body.innerHTML = ''; }); }
  </script>
</head>

<body>
  <?php include __DIR__ . '/sidebar_navbar.php'; ?>
  <main class="main">

    <div class="mb-3">
      <h3 style="font-weight:800;color:#0b3e8a;">My Profile</h3>
      <p class="text-muted mb-0">View and update your professional details</p>
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
      <!-- LEFT: Form -->
      <div class="col-12 col-lg-8">
        <div class="card-care p-3 p-md-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="form-section-title"><i class="fas fa-user-md me-2"></i>Basic Details</div>
            <?php if ($doctor): ?>
              <span class="info-badge"><i class="fas fa-id-badge me-1"></i>ID: #<?= (int) $doctor['doctor_id'] ?></span>
            <?php endif; ?>
          </div>

          <form method="post" action="" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

            <!-- Profile Image -->
            <div class="mb-4 text-center">
              <div class="avatar-wrap">
                <img src="<?= htmlspecialchars($img_src) ?>" class="avatar-img" id="currentAvatar" alt="Profile Photo">
                <label class="avatar-edit" title="Change Photo">
                  <i class="fas fa-camera"></i>
                  <input type="file" name="doctor_image" id="imageInput" accept="image/jpeg,image/png,image/webp"
                    onchange="previewImage(this)">
                </label>
              </div>
              <div id="imgPreviewWrap">
                <img id="imgPreview" src="" alt="New Photo">
                <div class="img-hint">New photo preview — save to apply</div>
              </div>
              <div class="img-hint mt-1">
                <i class="fas fa-camera me-1"></i>Click camera icon to change photo (JPEG/PNG/WebP, max 2MB)
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">First Name</label>
                <input class="form-control" value="<?= htmlspecialchars($doctor['first_name'] ?? '') ?>" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <input class="form-control" value="<?= htmlspecialchars($doctor['last_name'] ?? '') ?>" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" value="<?= htmlspecialchars($doctor['email'] ?? '') ?>" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input name="phone" class="form-control" value="<?= htmlspecialchars($doctor['phone'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control"
                  rows="2"><?= htmlspecialchars($doctor['address'] ?? '') ?></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Qualification</label>
                <input name="qualification" class="form-control"
                  value="<?= htmlspecialchars($doctor['qualification'] ?? '') ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label">Experience (years)</label>
                <input type="number" name="experience" class="form-control"
                  value="<?= (int) ($doctor['experience'] ?? 0) ?>">
              </div>
              <div class="col-md-3">
                <label class="form-label">Consultation Fee</label>
                <input type="number" name="consultation_fee" class="form-control"
                  value="<?= (int) ($doctor['consultation_fee'] ?? 0) ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">City</label>
                <select name="city_id" class="form-select">
                  <option value="0">Select City</option>
                  <?php
                  $result = mysqli_query($connect, "SELECT city_id, city_name FROM cities WHERE city_status='active'");
                  if ($result) {
                    $curr = intval($doctor['city_id'] ?? 0);
                    while ($row = mysqli_fetch_assoc($result)) {
                      $cid = (int) $row['city_id'];
                      $sel = $cid === $curr ? 'selected' : '';
                      echo "<option value=\"$cid\" $sel>" . htmlspecialchars($row['city_name']) . "</option>";
                    }
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Specialization</label>
                <select name="specialize_id" class="form-select">
                  <option value="0">Select Specialization</option>
                  <?php
                  $result2 = mysqli_query($connect, "SELECT specialize_id, specialize FROM specialization WHERE specialization_status='active'");
                  if ($result2) {
                    $currs = intval($doctor['specialize_id'] ?? 0);
                    while ($row = mysqli_fetch_assoc($result2)) {
                      $sid = (int) $row['specialize_id'];
                      $sel = $sid === $currs ? 'selected' : '';
                      echo "<option value=\"$sid\" $sel>" . htmlspecialchars($row['specialize']) . "</option>";
                    }
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="d-flex gap-2 mt-3">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Save Changes
              </button>
              <a href="dashboard.php" class="btn btn-outline-secondary">Back</a>
            </div>
          </form>
        </div>
      </div>
      <!-- RIGHT: Summary -->
      <div class="col-12 col-lg-4">
        <div class="card-care p-3 p-md-4">
          <div class="avatar-wrap mb-3">
            <img src="<?= htmlspecialchars($img_src) ?>" class="avatar-img" alt="Profile Photo">
          </div>
          <div class="avatar-name">
            Dr. <?= htmlspecialchars(($doctor['first_name'] ?? '') . ' ' . ($doctor['last_name'] ?? '')) ?>
          </div>
          <?php if ($spec_name): ?>
            <div class="avatar-spec"><?= htmlspecialchars($spec_name) ?></div>
          <?php endif; ?>
          <hr class="my-3">
          <div class="form-section-title"><i class="fas fa-id-card me-2"></i>Account Summary</div>
          <div style="font-size:.88rem;">
            <div class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($doctor['email'] ?? '') ?></div>
            <div class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($doctor['phone'] ?? '—') ?></div>
            <div class="mb-1"><strong>Experience:</strong> <?= (int) ($doctor['experience'] ?? 0) ?> years</div>
            <div class="mb-1"><strong>Fee:</strong> Rs <?= number_format((int) ($doctor['consultation_fee'] ?? 0)) ?>
            </div>
          </div>
          <hr>
          <p class="text-muted mb-0" style="font-size:.82rem;">
            Keep your information up to date so patients can find and book you easily.
          </p>
        </div>
        <!-- Password Change Form -->
        <div class="card-care p-3 p-md-4 mt-3">
          <div class="form-section-title mb-3">
            <i class="fas fa-lock me-2"></i>Change Password
          </div>
          <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <input type="hidden" name="change_password" value="1">

            <div class="mb-3">
              <label class="form-label" style="font-size:.88rem;font-weight:600;">
                <i class="fas fa-key me-1 text-muted"></i>Current Password
              </label>
              <div class="input-group">
                <input type="password" name="current_password" id="curPass" class="form-control"
                  placeholder="Purana password" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('curPass','eyeCur')">
                  <i class="fas fa-eye" id="eyeCur"></i>
                </button>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label" style="font-size:.88rem;font-weight:600;">
                <i class="fas fa-lock me-1 text-muted"></i>New Password
              </label>
              <div class="input-group">
                <input type="password" name="new_password" id="newPass" class="form-control"
                  placeholder="Naya password (min 6 chars)" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('newPass','eyeNew')">
                  <i class="fas fa-eye" id="eyeNew"></i>
                </button>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label" style="font-size:.88rem;font-weight:600;">
                <i class="fas fa-shield-alt me-1 text-muted"></i>Confirm Password
              </label>
              <div class="input-group">
                <input type="password" name="confirm_password" id="conPass" class="form-control"
                  placeholder="Password dobara likhein" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePass('conPass','eyeCon')">
                  <i class="fas fa-eye" id="eyeCon"></i>
                </button>
              </div>
            </div>

            <button type="submit" class="btn btn-danger w-100">
              <i class="fas fa-lock me-1"></i>Update Password
            </button>
          </form>
        </div>
      </div>

    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function previewImage(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById('imgPreview').src = e.target.result;
          document.getElementById('imgPreviewWrap').style.display = 'block';
          document.getElementById('currentAvatar').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
      }
    }
    function togglePass(fieldId, iconId) {
      var f = document.getElementById(fieldId);
      var i = document.getElementById(iconId);
      if (f.type === 'password') {
        f.type = 'text';
        i.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        f.type = 'password';
        i.classList.replace('fa-eye-slash', 'fa-eye');
      }
    }
  </script>
</body>

</html>