<?php
ob_start();
include "connect.php";
use PHPMailer\PHPMailer\PHPMailer;
session_start();
// Agar already login ho chuka ho
if (isset($_SESSION["user_email"])) {
    header("Location: patient_panel/dashboard.php");
    exit();
}
function generatePatientID(){
  return uniqid('PAT_', true);
  // true ke saath fractional microseconds bhi include
}
function validatePatient()
{
  global $connect;
  $errors = "";
  if (isset($_POST['submit_patient'])) {
    // Sanitize inputs
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $city = intval($_POST['city'] ?? 0);
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $patient_id = generatePatientID();
    $check_patient = "SELECT * FROM patients WHERE email='$email'";
    $check_patient_query = mysqli_query($connect, $check_patient);
    // Sabse upar, sanitize karne ke baad yeh check lagao
    if (
      $full_name === "" && $email === "" && $phone === "" &&
      $password === "" && $gender === "" && $city <= 0 && $address === ""
    ) {
      $errors = "Please fill all required fields.";
    } elseif ($full_name === "") {
      $errors = "Full name is required.";
    } elseif (strlen($full_name) > 30) {
      $errors = "Full name must not exceed 30 characters.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
      $errors = "Full name must contain only letters and spaces.";
    } elseif ($email === "") {
      $errors = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors = "Invalid email format.";
    } elseif (
      !empty($email) &&
      !preg_match("/@gmail\.com$/", $email) &&
      !preg_match("/@aptechsite\.net$/", $email) &&
      !preg_match("/@yahoo\.com$/", $email)
    ) {
      $errors = "Enter email in ( @gmail.com OR @aptechsite.net OR yahoo.com ).";
    }
    if (mysqli_num_rows($check_patient_query) > 0) {
      $errors = "This email is already registered. Try with a different one.";
    } elseif ($phone === "") {
      $errors = "Phone number is required.";
    } elseif (!preg_match("/^[0-9]{11}$/", $phone)) {
      $errors = "Phone must be exactly 11 digits.";
    } elseif ($gender === "") {
      $errors = "Gender is required.";
    } elseif ($city <= 0) {
      $errors = "City is required.";
    } elseif ($address === "") {
      $errors = "Address is required.";
    } elseif ($password === "") {
      $errors = "Password is required.";
    } elseif (strlen($password) < 8) {
      $errors = "Password must be at least 8 characters.";
    } elseif (!preg_match("/[A-Z]/", $password)) {
      $errors = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match("/[a-z]/", $password)) {
      $errors = "Password must contain at least one lowercase letter.";
    } elseif (!preg_match("/[0-9]/", $password)) {
      $errors = "Password must contain at least one number.";
    } elseif ($password !== $confirm) {
      $errors = "Passwords do not match.";
    }

    // ==========================
    // INSERT IF NO ERRORS
    // ==========================
    else {
      $safe_name = mysqli_real_escape_string($connect, $full_name);
      $safe_email = mysqli_real_escape_string($connect, $email);
      $safe_phone = mysqli_real_escape_string($connect, $phone);
      $safe_gender = mysqli_real_escape_string($connect, $gender);
      $safe_address = mysqli_real_escape_string($connect, $address);
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $errors = "<h6 class='alert alert-success text-capitalize'>sucessfully register</h6>";
      $insert = "
        INSERT INTO patients 
        (patient_id,name, email, phone, gender, city_id, address, password)
        VALUES
        ('$patient_id','$safe_name', '$safe_email', '$safe_phone',
         '$safe_gender', '$city', '$safe_address', '$hashed_password');
      ";
      mysqli_query($connect, $insert);
      $_SESSION['patient_email'] = $safe_email;
      require 'PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
      require 'PHPMailer-master/PHPMailer-master/src/SMTP.php';
      require 'PHPMailer-master/PHPMailer-master/src/Exception.php';

      $mail = new PHPMailer();
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'syedddsamadd@gmail.com';
      $mail->Password = 'jjpc paeo hwqu dkzn';
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      $mail->setFrom("syedddsamadd@gmail.com", "CARE Group");
      $mail->addAddress($email);
      $mail->Subject = "Welcome to Our Service";
      $mail->Body = "Assalamualaikum,\n\n"
        . "Aapka account successfully create ho gaya hai. "
        . "Aap ab login karke apni services use kar sakte hain.\n\n"
        . "Shukriya,\n"
        . "Team XYZ";

      if ($mail->send()) {
        echo "Message Sent!";
      } else {
        echo "Mailer Error: " . $mail->ErrorInfo;
      }
    }
    $_SESSION["patient_email"] = $email;
    header("location: login.php");
  } else {
    $errors = "all fields are mandatory to filled";
  }

  return $errors;
}
$page_title = 'Register';
$message = "";

include 'includes/head.php';
?>

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
<main class="auth-page register-page py-5">
  <div class="container">
    <div class="auth-card mx-auto" style="max-width:680px;">
      <div class="auth-card-header">
        <div class="auth-icon"><i class="fas fa-user-plus"></i></div>
        <h3>Create Your Account</h3>
        <p class="text-capitalize">Register your self to get appointment of doctors Related to your disesase</p>
      </div>
      <div class="auth-card-body">
        <div class="tab-content">
          <!-- PATIENT form -->
          <div class="tab-pane fade show active" id="patientTab">
            <form method="POST" id="patientForm" action="" novalidate class="needs-validation">
              <input type="hidden" name="register_type" value="patient" />
              <div class="row g-3">
                <div class="col-md-6">

                  <div class="form-floating">
                    <input type="text" class="form-control" id="full_name" name="full_name" placeholder="First Name" />
                    <label><i class="fas fa-user me-1"></i>full Name</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" />
                    <label><i class="fas fa-envelope me-1"></i>Email Address</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating">
                    <input type="tel" class="form-control" name="phone" id="phone_number" placeholder="Phone" />
                    <label><i class="fas fa-phone me-1"></i>Phone Number</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-floating">
                    <select class="form-select" id="gender" name="gender">
                      <option value="" disabled selected>Select</option>
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                    </select>
                    <label><i class="fas fa-venus-mars me-1"></i>Gender</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating">
                    <select class="form-select" id="city" name="city">
                      <option value="" disabled selected>Select city</option>
                      <?php
                      $city_query = mysqli_query($connect, "SELECT city_id, city_name FROM cities");
                      while ($row1 = mysqli_fetch_assoc($city_query)) {
                        echo "<option value='" . $row1['city_id'] . "'>" . $row1['city_name'] . "</option>";
                      }
                      ?>
                    </select>
                    <label><i class="fas fa-city me-1"></i>City</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating">
                    <textarea class="form-control" id="full_address" name="address" placeholder="Address"
                      style="height:80px;"></textarea>
                    <label><i class="fas fa-map-marker-alt me-1"></i>Full Address</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <div class="form-floating flex-grow-1">
                      <input type="password" class="form-control" id="password" name="password"
                        placeholder="Password" />
                      <label><i class="fas fa-lock me-1"></i>Password</label>
                    </div>
                    <span class="input-group-text toggle-password">
                      <i class="fas fa-eye-slash"></i>
                    </span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <div class="form-floating flex-grow-1">
                      <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                        placeholder="Confirm" />
                      <label><i class="fas fa-lock me-1"></i>Confirm Password</label>
                    </div>
                    <span class="input-group-text toggle-password"><i class="fas fa-eye-slash"></i></span>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="agreePatient" name="agree" />
                    <label class="form-check-label" for="agreePatient"
                      style="font-size:0.85rem;color:var(--text-muted);">
                      I agree to the terms and conditions.
                    </label>/

                  </div>
                </div>
                <?php
                $errors = validatePatient();

                if (!empty($errors)) {
                  echo "<h6 style='color:red;' class='text-center text-capitalize my-2'>$errors</h6>";
                }
                ?>
                <div class="col-12">
                  <button type="submit" name="submit_patient" id="form_submit"
                    class="btn-primary-care w-100 justify-content-center" style="padding:14px;">
                    <i class="fas fa-user-plus"></i> Create Patient Account
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <p class="text-center mt-4" style="font-size:0.88rem;color:var(--text-muted);">
          Already have an account? <a href="login.php" style="color:var(--primary);font-weight:700;">Sign In</a>
        </p>
      </div>
    </div>
  </div>
</main>
<!-- <script>
  const form = document.getElementById("patientForm");

  form.addEventListener("submit", function (e) {

    const fullName = document.getElementById("full_name").value.trim();
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone_number").value.trim();
    const gender = document.getElementById("gender").value;
    const city = document.getElementById("city").value;
    const address = document.getElementById("full_address").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document.getElementById("confirm_password").value.trim();
    const agree = document.getElementById("agreePatient").checked;

    if (
      fullName === "" ||
      email === "" ||
      phone === "" ||
      gender === "" ||
      city === "" ||
      address === "" ||
      password === "" ||
      confirmPassword === "" ||
      !agree
    ) {
      e.preventDefault();
      alert("All fields are required!");
      return false;
    }

  });
</script> -->
<?php include 'includes/scripts.php'; ?>