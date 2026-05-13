<?php
include "connect.php";
$page_title = 'Find a Doctor';

// Get filter values from GET params
include 'includes/head.php';
?>

<?php include 'includes/navbar.php'; ?>

<!-- Hero Banner -->
<section class="search-hero">
  <div class="container text-center">
    <span class="badge-accent"
      style="background:rgba(255,255,255,0.2);color:white;border:1px solid rgba(255,255,255,0.3);"><?php
      $result_doctor = $connect->query("SELECT COUNT(*) AS total FROM doctors");
      $row_doctor = $result_doctor->fetch_assoc();
      $total_doctor = $row_doctor['total'];
      echo $total_doctor;
      ?>+ Verified
      Doctors</span>
    <h1 class="hero-title mt-2 mb-2" style="font-size:2.4rem;">Find the Right Doctor</h1>
    <p style="color:rgba(255,255,255,0.8);">Search from our network of specialists across Pakistan</p>
  </div>
</section>

<!-- Filter Box -->
<div class="container" style="margin-top:-10px;position:relative;z-index:10;">
  <div class="search-filter-box">
    <?php
    // ✅ WHERE conditions build karo
    $where_conditions = ["doctor_status = 1"];

    if (!empty($_GET['city'])) {
      $city_id = intval($_GET['city']);
      $where_conditions[] = "city_id = '$city_id'";
    }

    if (!empty($_GET['spec'])) {
      $spec_id = intval($_GET['spec']);
      $where_conditions[] = "specialize_id = '$spec_id'";
    }

    $where_sql = "WHERE " . implode(" AND ", $where_conditions);

    // ✅ Pehle 9 doctors fetch karo, search par filter hoga
    $limit = (!empty($_GET['city']) || !empty($_GET['spec'])) ? "" : "LIMIT 9";

    $doctors_result =
      mysqli_query($connect, "SELECT * FROM doctors $where_sql ORDER BY doctor_id ASC $limit");
    ?>

    <!-- ✅ Search Form -->
    <form method="GET" action="">
      <div class="row g-3 mb-4">

        <!-- City Dropdown -->
        <div class="col-md-4">
          <label class="form-label"
            style="font-size:0.82rem;font-weight:600;color:var(--text-dark);text-transform:uppercase;letter-spacing:.5px;">
            <i class="fas fa-map-marker-alt me-1" style="color:var(--primary);"></i> City
          </label>
          <select class="filter-select" name="city">
            <option value="">All Cities</option>
            <?php
            $city_query = mysqli_query($connect, "SELECT city_id, city_name FROM cities WHERE city_status = 'active'");
            while ($crow = mysqli_fetch_assoc($city_query)) {
              $selected = (isset($_GET['city']) && $_GET['city'] == $crow['city_id']) ? 'selected' : '';
              echo "<option value='{$crow['city_id']}' $selected>{$crow['city_name']}</option>";
            }
            ?>
          </select>
        </div>

        <!-- Specialization Dropdown -->
        <div class="col-md-4">
          <label class="form-label"
            style="font-size:0.82rem;font-weight:600;color:var(--text-dark);text-transform:uppercase;letter-spacing:.5px;">
            <i class="fas fa-stethoscope me-1" style="color:var(--primary);"></i> Specialization
          </label>
          <select class="filter-select" name="spec">
            <option value="">All Specializations</option>
            <?php
            $spec_query = mysqli_query($connect, "SELECT specialize_id, specialize FROM specialization WHERE specialization_status = 'active'");
            while ($srow = mysqli_fetch_assoc($spec_query)) {
              $selected = (isset($_GET['spec']) && $_GET['spec'] == $srow['specialize_id']) ? 'selected' : '';
              echo "<option value='{$srow['specialize_id']}' $selected>{$srow['specialize']}</option>";
            }
            ?>
          </select>
        </div>

        <!-- Search Button -->
        <div class="col-md-4 d-flex align-items-end">
          <button type="submit" class="btn-primary-care w-100 justify-content-center" style="padding:13px;">
            <i class="fas fa-search"></i> Search Doctors
          </button>
        </div>

      </div>
    </form>
  </div>
</div>

<!-- Results -->
<section class="section-padding">
  <div class="container">
    <div class="row g-4">

      <?php if (mysqli_num_rows($doctors_result) > 0): ?>
        <?php while ($doc = mysqli_fetch_assoc($doctors_result)): ?>

          <?php
          // City fetch karo
          $city_res = mysqli_query($connect, "SELECT city_name FROM cities WHERE city_id = '" . intval($doc['city_id']) . "'");
          $city_row = mysqli_fetch_assoc($city_res);
          $city_name = htmlspecialchars($city_row['city_name'] ?? '');

          // Specialization fetch karo
          $spec_res = mysqli_query($connect, "SELECT specialize FROM specialization WHERE specialize_id = '" . intval($doc['specialize_id']) . "'");
          $spec_row = mysqli_fetch_assoc($spec_res);
          $spec_name = htmlspecialchars($spec_row['specialize'] ?? 'General');

          // Doctor details
          $full_name = htmlspecialchars($doc['first_name'] . ' ' . $doc['last_name']);
          $qual = htmlspecialchars($doc['qualification'] ?? '');
          $exp = intval($doc['experience']);
          $fee = number_format($doc['consultation_fee']);
          $area = htmlspecialchars($doc['address'] ?? '');
          $img = $doc['doctor_image'];
          $img_path = "admin/src/" . $img;
          $show_img = !empty($img);
          ?>

          <div class="col-md-6 col-lg-4">
            <div class="doctor-card">

              <!-- Image ya Avatar -->
              <div class="doctor-card-img" style="height: 260px; overflow:hidden;">
                <?php if ($show_img): ?>
                  <img src="<?= $img_path ?>" width="100%" height="100%" alt="<?= $full_name ?>"
                    style="width:100%; height:100%; object-position:top center; object-fit:cover;">
                <?php else: ?>
                  <div class="doctor-avatar-letter">
                    <i class="fas fa-user-md" style="font-size:5rem; color:#1a73e8;"></i>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Card Body -->
              <div class="doctor-card-body">
                <h5><?= $full_name ?></h5>
                <p class="doctor-spec">
                  <i class="fas fa-stethoscope me-1"></i><?= $spec_name ?>
                </p>
                <p class="doctor-meta">
                  <i class="fas fa-graduation-cap"></i> <?= $qual ?>
                </p>
                <p class="doctor-meta">
                  <i class="fas fa-briefcase"></i> <?= $exp ?> Years Experience
                </p>
                <p class="doctor-meta">
                  <i class="fas fa-map-marker-alt"></i> <?= $city_name ?>
                  <?php if (!empty($area)): ?> &ndash; <?= $area ?><?php endif; ?>
                </p>
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <span class="doctor-fees-badge">
                    <i class="fas fa-tag me-1"></i> Rs. <?= $fee ?>
                  </span>
                </div>
                <a href="appointment.php?doctor_id=<?= $doc['doctor_id'] ?>&spec=<?= urlencode($spec_name) ?>&fee=<?= $doc['consultation_fee'] ?>"
                  class="btn-primary-care w-100 justify-content-center">
                  <i class="fas fa-calendar-plus"></i> Book Appointment
                </a>
              </div>

            </div>
          </div>

        <?php endwhile; ?>

      <?php else: ?>
        <!-- No Results -->
        <div class="col-12 text-center py-5">
          <i class="fas fa-user-slash fa-3x mb-3" style="color:#ccc;"></i>
          <h5 style="color:#aaa;">Koi doctor nahi mila.</h5>
          <p style="color:#bbb; font-size:0.9rem;">City ya Specialization change karke dobara try karo.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>