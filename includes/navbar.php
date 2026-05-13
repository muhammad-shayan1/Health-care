<?php
// Determine current page for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<?php
if (session_status() === PHP_SESSION_NONE) {
  $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
  session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => $secure
  ]);
  session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$isLoggedIn = isset($_SESSION['user_email']);
$dashboard_url = null;
if ($isLoggedIn) {
  $role = $_SESSION['role'] ?? null;
if ($role === 'patient') {
    $dashboard_url = 'patient_panel/dashboard.php';
  } elseif ($role === 'doctor') {
    $dashboard_url = 'doctor_final/doctor_output/dashboard.php';
  } else {
    $dashboard_url = 'index.php';
  }
}
?>
<nav class="navbar navbar-expand-lg navbar-care">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <div class="brand-logo-icon"><i class="fas fa-heartbeat"></i></div>
      <span class="brand-name">CARE <span>Group</span></span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto gap-1">
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">
            <i class="fas fa-home me-1"></i>Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'search-doctor.php') ? 'active' : ''; ?>" href="search-doctor.php">
            <i class="fas fa-user-md me-1"></i>Search Doctor
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'diseases.php') ? 'active' : ''; ?>" href="diseases.php">
            <i class="fas fa-virus me-1"></i>Diseases
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'medical-news.php') ? 'active' : ''; ?>" href="disease_news.php">
            <i class="fas fa-newspaper me-1"></i>Medical News
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>" href="about.php">
            <i class="fas fa-info-circle me-1"></i>About
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">
            <i class="fas fa-envelope me-1"></i>Contact
          </a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
        <?php if ($isLoggedIn && $dashboard_url) { ?>
          <a href="<?php echo htmlspecialchars($dashboard_url, ENT_QUOTES, 'UTF-8'); ?>" class="nav-link btn-nav-register text-light">Dashboard</a>
        <?php } else { ?>
          <a href="login.php" class="nav-link btn-nav-login">Login</a>
          <a href="register.php" class="nav-link btn-nav-login">sign up</a>
        <?php } ?>
      </div>
    </div>
  </div>
</nav>