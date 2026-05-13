<?php
require_once __DIR__ . '/_auth.php';
$pat_id = patient_id();

// Patient ka naam aur city
$stmt = mysqli_prepare($connect, "SELECT name FROM patients WHERE patient_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $pat_id);
mysqli_stmt_execute($stmt);
$pat = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
$pat_name = $pat['name'] ?? 'Patient';

// Avatar initials
$words    = explode(' ', trim($pat_name));
$initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));

// Active page
$current = basename($_SERVER['PHP_SELF']);

// Pending badge count
$stmt2 = mysqli_prepare($connect, "SELECT COUNT(*) AS cnt FROM appointments WHERE patient_id = ? AND status = 'pending'");
mysqli_stmt_bind_param($stmt2, 's', $pat_id);
mysqli_stmt_execute($stmt2);
$badge = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2))['cnt'] ?? 0);
mysqli_stmt_close($stmt2);
?>
<!-- TOP NAVBAR -->
<nav class="top-navbar" role="navigation" aria-label="Patient portal navigation">
    <a href="dashboard.php" class="navbar-brand" aria-label="CARE Group Home">
        <div class="brand-icon" aria-hidden="true"><i class="fas fa-heartbeat"></i></div>
        <span class="brand-text">Patient panel</span>
    </a>
    <button class="nav-mob-toggle d-md-none" id="sidebarToggle" aria-label="Open sidebar" aria-expanded="false">
        <i class="fas fa-bars" aria-hidden="true"></i>
    </button>
    <div class="navbar-right">
        <div class="patient-info">
            <div class="patient-avatar" aria-hidden="true"><?= htmlspecialchars($initials) ?></div>
            <div class="d-none d-sm-block">
                <div class="patient-name"><?= htmlspecialchars($pat_name) ?></div>
                <div class="patient-role">Patient</div>
            </div>
        </div>
        <a href="logout.php" class="btn-logout" aria-label="Logout from portal">
            <i class="fas fa-sign-out-alt me-1" aria-hidden="true"></i>Logout
        </a>
    </div>
</nav>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar" role="complementary" aria-label="Sidebar">
    <div class="sidebar-section-label">Main Menu</div>
    <a href="dashboard.php" class="sidebar-link <?= $current === 'dashboard.php' ? 'active' : '' ?>">
        <i class="fas fa-th-large" aria-hidden="true"></i> Dashboard
    </a>
    <a href="../search-doctor.php" class="sidebar-link <?= $current === 'search_doctor.php' ? 'active' : '' ?>">
        <i class="fas fa-search" aria-hidden="true"></i> Find Doctor
    </a>
    <a href="my_appointments.php" class="sidebar-link <?= $current === 'my_appointments.php' ? 'active' : '' ?>">
        <i class="fas fa-calendar-alt" aria-hidden="true"></i> My Appointments
        <?php if ($badge > 0): ?>
            <span class="sidebar-badge" aria-label="<?= $badge ?> pending"><?= $badge ?></span>
        <?php endif; ?>
    </a>
    <div class="sidebar-section-label">Account</div>
    <a href="profile.php" class="sidebar-link <?= $current === 'profile.php' ? 'active' : '' ?>">
        <i class="fas fa-user" aria-hidden="true"></i> My Profile
    </a>
    <a href="logout.php" class="sidebar-link sidebar-logout">
        <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Logout
    </a>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>
