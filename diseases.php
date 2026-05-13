<?php
include "connect.php";
$page_title = 'Diseases Guide';
$filter = isset($_GET['cat']) ? $_GET['cat'] : 'all';


// Filter
include 'includes/head.php';
?>

<?php include 'includes/navbar.php'; ?>

<section class="hero-section-Disease">
  <div  class="container text-center">
    <span class="badge-accent"
      style="background:rgba(255,255,255,0.2);color:white;border:1px solid rgba(255,255,255,0.3);">Health Guide</span>
    <h1 class="hero-title mt-2" style="font-size:2.4rem;">Comprehensive Disease Information Center</h1>
    <p style="color:rgba(255,255,255,0.8);max-width:500px;margin:0 auto;">
Find trusted medical information about diseases. 
Understand symptoms, causes, diagnosis, treatment options, 
and prevention strategies to protect your health and your family.</p>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <!-- 
      <div class="text-center py-5">
        <i class="fas fa-search" style="font-size:3rem;color:var(--medium-gray);"></i>
        <h5 class="mt-3" style="color:var(--text-muted);">No diseases found.</h5>
        <a href="diseases.php" class="btn-primary-care mt-3">Reset Filter</a>
      </div> -->
    <div class="row g-4">
      <?php
      $select_diseases = "select * from disease where status=1";
      $select_diseases_query = mysqli_query($connect, $select_diseases);
      if (mysqli_num_rows($select_diseases_query) > 0) {
        while ($disease_table_row = mysqli_fetch_assoc($select_diseases_query)) {
          $disease_id = $disease_table_row["Disease_id"];
          $disease_name = $disease_table_row["disease_name"];
          $symptoms = $disease_table_row["symptoms"];
          $causes = $disease_table_row["causes"];
          $prevention = $disease_table_row["prevention"];
          $treatment = $disease_table_row["treatment"];
          $status = $disease_table_row["status"];
          $specialize_id = $disease_table_row["specialize_id"];

          $select_specialization = "select * from specialization where specialize_id='$specialize_id' and specialization_status='active'";
          $select_specialize_query = mysqli_query($connect, $select_specialization);
          if (mysqli_num_rows($select_specialize_query) > 0) {
            while ($specialize_table_row = mysqli_fetch_assoc($select_specialize_query)) {
              $specialize = $specialize_table_row["specialize"];
              ?>
              <div class="col-md-6 col-lg-4 animate-on-scroll" style="transition-delay:<?php echo (($i - 1) % 3) * 0.07; ?>s">
                <div class="disease-card">
                  <img
                    src="https://media.istockphoto.com/id/1435498034/photo/heart-attack-and-heart-disease-3d-illustration.jpg?s=612x612&w=0&k=20&c=peIJNOEj2YYzbLVEcgesNYpty4X51oI0Q1V1WqdWja8="
                    width="100%" height="200px" alt="">
                  <div class="disease-card-body">
                    <h5><?= $disease_name ?></h5>
                    <div class="mb-2">
                    </div>
                    <div class="disease-info-label">Symptoms</div>
                    <p class="disease-info-text"><?= $symptoms ?></p>
                    <div class="disease-info-label">Causes</div>
                    <p class="disease-info-text"><?= $causes ?></p>
                    <div class="disease-info-label">Prevention</div>
                    <p class="disease-info-text"><?= $prevention ?></p>
                    <div class="disease-info-label">Treatment</div>
                    <p class="disease-info-text"><?= $treatment ?></p>
                    <a href="search-doctor.php" class="btn-primary-care mt-3" style="padding:9px 20px;font-size:0.83rem;">
                      <i class="fas fa-user-md"></i> Find <?= $specialize ?>
                    </a>
                  </div>
                </div>
              </div>
              <?php
            }
          }
        }
      }
      ?>
    </div>
  </div>
</section>

<section class="cta-banner">
  <div class="container text-center position-relative" style="z-index:1;">
    <h2 class="mb-3">Have Symptoms? Consult a Doctor Now</h2>
    <p class="mb-4">Early diagnosis saves lives. Book an appointment with a specialist today.</p>
    <a href="search-doctor.php" class="btn-primary-care" style="background:white;color:var(--primary);">
      <i class="fas fa-search"></i> Find a Specialist
    </a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>