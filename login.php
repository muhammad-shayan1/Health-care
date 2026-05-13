<?php
ob_start();
include "connect.php";
$page_title = 'Login';
include 'includes/head.php';
?>
<?php
session_start();
// Agar already login ho chuka ho
if (isset($_SESSION["user_email"])) {
  header("Location: patient_panel/dashboard.php");
  exit();
}
include "connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // 1️⃣ Get & sanitize input
  $role = trim($_POST['role']);
  $useremail = trim($_POST['useremail']);
  $password = trim($_POST['password']);

  // 2️⃣ Basic validation
  if (empty($role) || empty($useremail) || empty($password)) {
    $error = "All fields are required!";
  } else {

    // 3️⃣ Decide table & ID field based on role
    if ($role == "patient") {
      $table = "patients";
      $id_field = "patient_id";
    } elseif ($role == "doctor") {
      $table = "doctors";
      $id_field = "doctor_id";
    } else {
      $error = "Invalid role selected!";
    }

    if (empty($error)) {

      // 4️⃣ Prepare statement
      $stmt = $connect->prepare("SELECT * FROM $table WHERE email=? LIMIT 1");
      if ($stmt) {
        $stmt->bind_param("s", $useremail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
          $user = $result->fetch_assoc();
          // 5️⃣ Verify password
          if (password_verify($password, $user['password'])) {

            // 6️⃣ Create session
            $_SESSION['user_id'] = $user[$id_field];
            $_SESSION['role'] = $role;
            $_SESSION['user_email'] = $user['email'];

            if ($role == "patient") {
              header("Location: patient_panel/dashboard.php");
            } else {
              header("Location: doctor_final/doctor_output/dashboard.php");
            }
            exit();

          } else {
            $error = "Incorrect password!";
          }
        } else {
          $error = "User not found!";
        }

        $stmt->close();

      } else {
        $error = "Database error. Please try again later.";
      }
    }
  }
}
?>
<!-- Simple Navbar (no full nav on auth pages) -->
<nav class="navbar navbar-expand-lg navbar-care">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <div class="brand-logo-icon"><i class="fas fa-heartbeat"></i></div>
      <span class="brand-name">CARE <span>Group</span></span>
    </a>
    <div class="d-flex align-items-center gap-2">
      <a href="login.php" class="nav-link btn-nav-login">Login</a>
      <a href="register.php" class="nav-link btn-nav-login">Register</a>
    </div>
  </div>
</nav>
<div class="auth-page">
  <div
    style="position:fixed;top:-80px;right:-80px;width:300px;height:300px;background:radial-gradient(circle,rgba(21,101,192,0.12),transparent);border-radius:50%;pointer-events:none;">
  </div>
  <div
    style="position:fixed;bottom:-60px;left:-60px;width:250px;height:250px;background:radial-gradient(circle,rgba(0,172,193,0.1),transparent);border-radius:50%;pointer-events:none;">
  </div>

  <div class="container">
    <div class="auth-card">
      <!-- Header -->
      <div class="auth-card-header">
        <div class="auth-icon"><i class="fas fa-lock"></i></div>
        <h3>Welcome Back</h3>
        <p>Sign in to access your CARE Group account</p>
      </div>

      <!-- Body -->
      <div class="auth-card-body">
        <!-- 
          <div class="alert alert-danger d-flex align-items-center gap-2 mb-3" style="font-size:0.88rem;border-radius:var(--radius-sm);">
            <i class="fas fa-exclamation-circle"></i>
          </div> -->

        <form method="POST" action="" novalidate>
          <!-- Role -->
          <div class="form-floating mb-3">
            <select class="form-select" name="role" id="loginRole" required>
              <option value="" disabled>Select your role</option>
              <option value="patient">Patient</option>
              <option value="doctor">Doctor</option>
            </select>
            <label for="loginRole"><i class="fas fa-user-tag me-1"></i>Login As</label>
          </div>

          <!-- Username -->
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="loginUser" name="useremail" placeholder="Username or Email"
              value="" required />
            <label for="loginUser"><i class="fas fa-envelope me-1"></i>Email</label>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <div class="input-group">
              <div class="form-floating flex-grow-1">
                <input type="password" class="form-control" id="loginPass" name="password" placeholder="Password"
                  required />
                <label for="loginPass"><i class="fas fa-key me-1"></i>Password</label>
              </div>
              <span class="input-group-text toggle-password" style="cursor:pointer;">
                <i class="fas fa-eye-slash"></i>
              </span>
            </div>
          </div>
          <!-- Submit -->
          <button type="submit" class="btn-primary-care w-100 justify-content-center mb-3" style="padding:14px;">
            <i class="fas fa-sign-in-alt"></i> log In
          </button>
          <?php if (!empty($error)) { ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-3"
              style="font-size:0.88rem;border-radius:var(--radius-sm);">
              <i class="fas fa-exclamation-circle"></i>
              <?php echo $error; ?>
            </div>
          <?php } ?>
          <p class="text-center" style="font-size:0.88rem;color:var(--text-muted);">
            Don't have an account?
            <a href="register.php" style="color:var(--primary);font-weight:700;">Create Account</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/scripts.php'; ?>