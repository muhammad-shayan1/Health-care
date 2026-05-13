<?php
/**
 * sidebar.php — Left Sidebar Navigation
 * Include after header.php on every admin page.
 *
 * Usage: <?php include('includes/sidebar.php'); ?>
 */
?>
<!-- ╔══════════════════════════════════════════════════════╗
     ║  SIDEBAR                                             ║
     ╚══════════════════════════════════════════════════════╝ -->
<aside id="sidebar">

    <!-- Brand -->
    <a href="dashboard.php" class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-hospital"></i>
        </div>
        <div class="sidebar-brand-text">
            MediAdmin
            <span>Healthcare Portal</span>
        </div>
    </a>

    <!-- Main Navigation -->
    <p class="nav-section-label">Main Menu</p>
    <ul class="sidebar-nav">

        <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="cities.php" class="nav-link">
                <i class="bi bi-geo-alt"></i>
                <span>Manage Cities</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="doctors.php" class="nav-link">
                <i class="bi bi-person-badge"></i>
                <span>Manage Doctors</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="patients.php" class="nav-link">
                <i class="bi bi-people"></i>
                <span>Manage Patients</span>
            </a>
        </li>

    </ul>

    <!-- Medical Records -->
    <p class="nav-section-label">Medical Records</p>
    <ul class="sidebar-nav">

        <li class="nav-item">
            <a href="specializations.php" class="nav-link">
                <i class="bi bi-award"></i>
                <span>Specializations</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="diseases.php" class="nav-link">
                <i class="bi bi-virus"></i>
                <span>Manage Diseases</span>
            </a>
        </li>
                <li class="nav-item">
            <a href="admin_register.php" class="nav-link">
                <i class="bi bi-newspaper"></i>
                <span>admin register</span>
            </a>
        </li>

    </ul>

    <!-- Spacer -->
    <div style="flex:1;"></div>

    <!-- Logout -->
    <div class="sidebar-logout">
        <ul class="sidebar-nav" style="padding:0;">
            <li class="nav-item">
                <a href="logout.php" class="nav-link">
                    <i class="bi bi-box-arrow-left"></i>
                    <span class="btn btn-primary">Logout</span>
                </a>
            </li>
        </ul>
    </div>

</aside>
<!-- END SIDEBAR -->

<!-- Content Wrapper -->
<div id="mainContent">
