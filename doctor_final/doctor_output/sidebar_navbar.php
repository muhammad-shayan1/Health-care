<?php require_once __DIR__ . '/_auth.php'; ?>
<style>
  *, *::before, *::after { box-sizing: border-box; }

  /* TOP NAVBAR */
  .top-navbar {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    height: 62px;
    background: #fff;
    border-bottom: 1px solid #e5eef9;
    box-shadow: 0 2px 8px rgba(11,62,138,.07);
  }
  .navbar-brand {
    display: flex; align-items: center; gap: 8px;
    text-decoration: none; color: #0b3e8a;
    font-weight: 800; font-size: 1.05rem;
  }
  .brand-icon {
    width: 32px; height: 32px; border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg,#2563eb,#60a5fa);
    color: #fff; font-size: .95rem;
  }
  .navbar-right { display: flex; align-items: center; gap: 8px; }
  .btn-logout {
    display: inline-flex; align-items: center; gap: 6px;
    background: #ef4444; color: #fff !important;
    padding: 7px 14px; border-radius: 10px;
    text-decoration: none; font-size: .88rem; font-weight: 600;
    transition: background .18s;
  }
  .btn-logout:hover { background: #dc2626; }

  /* SIDEBAR */
  .sidebar {
    position: fixed;
    top: 62px; left: 0; bottom: 0;
    width: 240px; z-index: 1040;
    background: #0b3e8a; color: #fff;
    padding: 20px 12px; overflow-y: auto;
    transition: transform .26s ease;
  }
  .sidebar-label {
    font-size: .72rem; font-weight: 700;
    letter-spacing: .08em; opacity: .55;
    text-transform: uppercase;
    padding: 0 10px; margin-bottom: 6px;
  }
  .sidebar-link {
    display: flex; align-items: center; gap: 10px;
    color: rgba(255,255,255,.85) !important;
    text-decoration: none !important;
    padding: 10px 12px; border-radius: 10px;
    font-size: .93rem; font-weight: 500;
    margin-bottom: 3px; transition: background .18s, color .18s;
  }
  .sidebar-link i { width: 18px; text-align: center; opacity: .8; }
  .sidebar-link:hover { background: rgba(255,255,255,.12); color: #fff !important; }
  .sidebar-link.active { background: rgba(255,255,255,.18); color: #fff !important; font-weight: 700; }
  .sidebar-link.logout-link { color: #fca5a5 !important; margin-top: 8px; }
  .sidebar-link.logout-link:hover { background: rgba(239,68,68,.2); color: #fecaca !important; }

  #sidebarToggle {
    background: none; border: none; color: #0b3e8a;
    font-size: 1.2rem; cursor: pointer;
    padding: 4px 8px; border-radius: 8px;
  }

  /* MAIN CONTENT */
  .main {
    margin-left: 240px;
    margin-top: 62px;
    padding: 24px;
    min-height: calc(100vh - 62px);
  }

  @media (max-width: 767.98px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.open { transform: translateX(0); }
    .main { margin-left: 0; }
    #sidebarToggle { display: inline-block !important; }
  }
  @media (min-width: 768px) {
    #sidebarToggle { display: none !important; }
  }
</style>

<nav class="top-navbar">
  <a href="dashboard.php" class="navbar-brand">
    <div class="brand-icon"><i class="fas fa-heartbeat"></i></div>
    <span>Doctor Panel</span>
  </a>
  <div class="d-flex align-items-center gap-2">
    <button id="sidebarToggle" aria-label="Toggle sidebar"><i class="fas fa-bars"></i></button>
  </div>
  <div class="navbar-right">
    <a href="profile.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-user-md me-1"></i>My Profile</a>
    <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</nav>

<?php $cur = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-label">Main Menu</div>
  <a href="dashboard.php"    class="sidebar-link <?= $cur==='dashboard.php'    ? 'active':'' ?>"><i class="fas fa-th-large"></i> Dashboard</a>
  <a href="profile.php"      class="sidebar-link <?= $cur==='profile.php'      ? 'active':'' ?>"><i class="fas fa-user-md"></i> Profile</a>
  <a href="appointments.php" class="sidebar-link <?= $cur==='appointments.php' ? 'active':'' ?>"><i class="fas fa-calendar-check"></i> Appointments</a>
  <a href="availability.php" class="sidebar-link <?= $cur==='availability.php' ? 'active':'' ?>"><i class="fas fa-clock"></i> Availability</a>
  <div class="sidebar-label mt-3">Account</div>
  <a href="logout.php" class="sidebar-link logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
</aside>

<div id="sidebarOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:1039;" onclick="closeSidebar()"></div>

<script>
(function(){
  var btn=document.getElementById('sidebarToggle');
  var sb=document.getElementById('sidebar');
  var ov=document.getElementById('sidebarOverlay');
  window.closeSidebar=function(){ sb.classList.remove('open'); ov.style.display='none'; };
  if(btn) btn.addEventListener('click',function(){ sb.classList.contains('open')?closeSidebar():(sb.classList.add('open'),ov.style.display='block'); });
})();
</script>
