<?php
// session_start();
/**
 * header.php — Top Navbar + CSS includes
 * Include at the top of every admin page.
 *
 * Usage: <?php include('includes/header.php'); ?>
 *
 * Set $pageTitle before including this file, e.g.:
 *   $pageTitle = "Manage Doctors";
 */

$pageTitle = isset($pageTitle) ? $pageTitle : 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?> — MediAdmin</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <!-- Google Fonts — Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ╔══════════════════════════════════════════════════════╗
     ║  TOP NAVBAR                                          ║
     ╚══════════════════════════════════════════════════════╝ -->
<nav id="topNavbar">

    <!-- Sidebar toggle button -->
    <button class="sidebar-toggle" id="sidebarToggleBtn" title="Toggle Sidebar">
        <i class="bi bi-list"></i>
    </button>

    <!-- Page title -->
    <div class="navbar-page-title">
        <?= htmlspecialchars($pageTitle) ?>
    </div>
    <!-- php echo karna hai yaha -->
<!-- <small> //date('l, d M Y') //</small> -->
    <!-- Notification bell -->
    <!-- <a href="#" class="nav-icon-btn" title="Notifications">
        <i class="bi bi-bell"></i>
        <span class="badge-dot"></span> -->
    <!-- </a> -->

    <!-- Messages icon -->
    <!-- <a href="#" class="nav-icon-btn" title="Messages">
        <i class="bi bi-chat-dots"></i>
    </a> -->

    <!-- Admin profile dropdown -->
    <div class="navbar-admin dropdown" data-bs-toggle="dropdown" role="button">
        <div class="navbar-admin-avatar">A</div>
        <div class="navbar-admin-info d-none d-md-block">
            <div class="name">Abdul samad</div>
            <div class="role"><?=$_SESSION['admin_email']?></div>
        </div>
        <iz class="bi bi-chevron-down ms-1" style="font-size:11px;color:#7f8fa6;"></iz>
    </div>
    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;padding:8px;min-width:200px;">
        <li><hr class="dropdown-divider my-1" /></li>
        <li>
            <a class="dropdown-item rounded-3 py-2 text-danger" href="logout.php">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </li>
    </ul>

</nav>
<!-- END TOP NAVBAR -->

<!-- Toast Container -->
<div class="toast-container-custom" id="toastContainer"></div>

<!-- Main wrapper starts -->
<div id="mainWrapper" style="display:flex;">
