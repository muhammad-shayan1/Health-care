<?php
include "connect.php";
?>
<?php $page_title = 'Home';
include 'includes/head.php'; ?>
<?php
function getIcon($name)
{
  $name = strtolower(trim($name));

  $map = [
    'cardiologist' => 'heart',
    'neurologist' => 'brain',
    'general physician' => 'stethoscope',
    'gynecologist' => 'venus',
    'endocrinologist' => 'kidneys',
    'pulmonologist' => 'lungs',
    'orthopedist' => 'bone',
    'dermatologist' => 'allergies',
    'gastroenterologist' => 'stomach',
    'pediatrician' => 'baby',
    'ophthalmologist' => 'eye',
  ];

  return $map[$name] ?? 'user-md'; // default icon
}

?>
<?php include 'includes/navbar.php'; ?>
<?php
include "home_crousel.php";
?>
<!-- ====== HOW IT WORKS ====== -->
<section class="how-it-works section-padding">
  <div class="container">
    <div class="text-center mb-5">
      <span class="badge-accent">Simple Process</span>
      <h2 class="section-title">How It Works</h2>
      <p class="section-subtitle mt-2">Book your appointment in just 3 easy steps and get the care you deserve.</p>
    </div>
    <div class="row g-4 align-items-center">
      <div class="col-md-5 col-lg-4 animate-on-scroll">
        <div class="step-card">
          <div class="step-number">1</div>
          <i class="fas fa-search step-icon"></i>
          <h5>Search a Doctor</h5>
          <p>Browse our network of verified specialists by city, specialization, or name.</p>
        </div>
      </div>
      <div class="col-md-1 d-none d-md-flex step-connector">
        <i class="fas fa-chevron-right"></i>
      </div>
      <div class="col-md-5 col-lg-3 animate-on-scroll" style="transition-delay:0.1s">
        <div class="step-card">
          <div class="step-number">2</div>
          <i class="fas fa-clock step-icon"></i>
          <h5>Select a Time Slot</h5>
          <p>Choose from available time slots that fit your schedule perfectly.</p>
        </div>
      </div>
      <div class="col-md-1 d-none d-md-flex step-connector">
        <i class="fas fa-chevron-right"></i>
      </div>
      <div class="col-md-5 col-lg-3  animate-on-scroll" style="transition-delay:0.2s">
        <div class="step-card">
          <div class="step-number">3</div>
          <i class="fas fa-check-circle step-icon"></i>
          <h5>Confirm Appointment</h5>
          <p>Receive instant confirmation and reminders for your appointment.</p>
        </div>
      </div>
    </div>
  </div>
</section>
<?php
$doctors_result = mysqli_query($connect, "SELECT * FROM doctors WHERE doctor_status = 1 LIMIT 8");
?>

<section class="section-padding bg-white">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-5">
      <div>
        <span class="badge-accent">Top Rated</span>
        <h2 class="section-title mt-1">Featured Doctors</h2>
      </div>
      <a href="search-doctor.php" class="btn-primary-care d-none d-md-flex">View All <i
          class="fas fa-arrow-right"></i></a>
    </div>

    <div class="row g-4">
      <?php while ($doc = mysqli_fetch_assoc($doctors_result)): ?>
        <?php
        // City fetch
        $city_result = mysqli_query($connect, "SELECT * FROM cities WHERE city_id = " . $doc['city_id']);
        $city = mysqli_fetch_assoc($city_result);

        // Specialization fetch
        $spec_result = mysqli_query($connect, "SELECT * FROM specialization WHERE specialize_id = " . $doc['specialize_id']);
        $spec = mysqli_fetch_assoc($spec_result);
        ?>
        <div class="col-md-6 col-lg-3">
          <div class="doctor-card">

            <div class="doctor-card-img">
              <?php if (!empty($doc['doctor_image'])): ?>
                <img src="admin/src/<?php echo $doc['doctor_image']; ?>" alt="Doctor" style="width:100%; height:100%;">
              <?php else: ?>
                <i class="fas fa-user-md"></i>
              <?php endif; ?>
            </div>

            <div class="doctor-card-body">
              <h5>Dr. <?php echo $doc['first_name'] . ' ' . $doc['last_name']; ?></h5>

              <p class="doctor-spec">
                <i class="fas fa-stethoscope me-1"></i>
                <?php echo $spec['specialize']; ?>
              </p>

              <p class="doctor-meta">
                <i class="fas fa-graduation-cap"></i>
                <?php echo $doc['qualification']; ?>
              </p>

              <p class="doctor-meta">
                <i class="fas fa-briefcase"></i>
                <?php echo $doc['experience']; ?> Years Experience
              </p>

              <p class="doctor-meta">
                <i class="fas fa-map-marker-alt"></i>
                <?php echo $city['city_name']; ?>
              </p>

              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="doctor-fees-badge">
                  <i class="fas fa-tag me-1"></i>Rs. <?php echo $doc['consultation_fee']; ?>
                </span>
              </div>

              <a href="appointment.php?doctor_id=<?= $doc['doctor_id'] ?>"
                class="btn-primary-care w-100 justify-content-center">
                <i class="fas fa-calendar-plus"></i> Book Now
              </a>
            </div>

          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<!-- specialization -->
<section class="spec-section section-padding">
  <div class="container">
    <div class="text-center mb-5">
      <span class="badge-accent">Our Expertise</span>
      <h2 class="section-title">Browse Specializations</h2>
      <p class="section-subtitle mt-2">Find specialists across <?php
      $r = $connect->query('SELECT COUNT(*) AS total FROM cities');
      echo $r->fetch_assoc()['total'];
      ?>+ medical disciplines, ready to help you.</p>
    </div>
    <div class="row g-3">
      <?php
      $select_specialization = "select * from specialization";
      $select_specialize_query = mysqli_query($connect, $select_specialization);
      if (mysqli_num_rows($select_specialize_query) > 0) {
        while (
          $specialize_table_row = mysqli_fetch_assoc($select_specialize_query)
        ) {
          $specialize_id = $specialize_table_row["specialize_id"];
          $specialize = $specialize_table_row["specialize"];
          ?>
          <div class="col-6 col-md-4 col-lg-2 animate-on-scroll" style="transition-delay:<?php echo $i * 0.05; ?>s">
            <a href="search-doctor.php" class="spec-card d-block text-decoration-none">
              <div class="spec-icon">
                <i class="fas fa-<?= getIcon($specialize) ?>"></i>
              </div>
              <h6><?= $specialize ?></h6>
              <span>Doctors</span>
            </a>
          </div>
          <?php
        }
      }
      ?>
    </div>
  </div>
</section> 
<!-- testimonials -->

<section class="section-padding bg-white">
  <div class="container">
    <div class="text-center mb-5">
      <span class="badge-accent">What Patients Say</span>
      <h2 class="section-title">Patient Testimonials</h2>
      <p class="section-subtitle mt-2">Hear from patients who found the right care through CARE Group.</p>
    </div>
    <div class="row g-4">
      <?php
      $feedback_query = "SELECT * FROM feedback where feedback_status=1
          ORDER BY feedback_id DESC 
          LIMIT 6";
      $feedback_result = mysqli_query($connect, $feedback_query);

      // -------------------------- STARS FUNCTION --------------------------
      function showStars($rating)
      {
        $output = "";
        for ($i = 1; $i <= 5; $i++) {
          if ($i <= $rating) {
            $output .= "<span class='star filled text-warning'>★</span>";
          } else {
            $output .= "<span class='star text-warning'>★</span>";
          }
        }
        return $output;
      }
      while ($row = mysqli_fetch_assoc($feedback_result)) {
        ?>
        <div class="col-md-6 col-lg-4 col-12 animate-on-scroll" style="transition-delay:0.1s">
          <div class="testimonial-card">
            <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
            <p style="word-break: break-word; overflow-wrap: break-word;">"<?= $row['message'] ?>"</p>
            <div class="testimonial-author">
              <div class="testimonial-avatar"
                style="background:linear-gradient(135deg,var(--accent),var(--accent-light));"></div>
              <div>
                <strong><?= $row['full_name'] ?></strong>
                <span><?= $row['created_at'] ?></span>
                <div class="stars mt-1">★★★★★</div>
              </div>
            </div>
          </div>
        </div>
        <?php
      }
      ?>
    </div>
  </div>
  </div>
  </div>
</section>
<!-- ====== CTA BANNER ====== -->
<section class="cta-banner">
  <div class="container text-center position-relative" style="z-index:1;">
    <span class="badge-accent mb-3"
      style="background:rgba(255,255,255,0.2);color:white;border:1px solid rgba(255,255,255,0.3);">Get Started
      Today</span>
    <h2 class="mb-3">Ready to Take Control of Your Health?</h2>
    <p class="mb-5 mx-auto" style="max-width:500px;">Join over <?php
    $result_patient = $connect->query("SELECT COUNT(*) AS total FROM patients");
    $row_patient = $result_patient->fetch_assoc();
    $total_patient = $row_patient['total'];
    echo $total_patient;
    ?>+ patients who have found the right doctor and
      managed their health with CARE Group.</p>
    <div class="d-flex flex-wrap gap-3 justify-content-center">
      <a href="register.php" class="btn-primary-care"
        style="background:white;color:var(--primary);box-shadow:0 4px 16px rgba(0,0,0,0.15);">
        <i class="fas fa-user-plus"></i> Create Free Account
      </a>
      <a href="search-doctor.php" class="btn-outline-care">
        <i class="fas fa-search"></i> Browse Doctors
      </a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>