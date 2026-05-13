<?php $page_title = 'About Us';
include 'includes/head.php';
include "connect.php";
?>
<?php include 'includes/navbar.php'; ?>
<style>
/* about banner */
  .about-hero .badge-accent {
    font-size: 1.75rem;
    padding: 8px 20px;
    border-radius: 999px;
    letter-spacing: 0.5px;
    font-weight: 600;
  }  
   .about-hero {
  position: relative;
  height: 600px;
  display: flex;
  align-items: center;
  overflow: hidden;
  font-family: var(--cg-font-b);
  padding: 130px 0 80px;
    background:
    radial-gradient(ellipse 80% 70% at 80% 50%, rgba(8,145,178,0.50) 0%, transparent 65%),
    radial-gradient(ellipse 50% 60% at 10% 80%, rgba(16,83,160,0.50) 0%, transparent 60%),
    linear-gradient(145deg, rgba(7,30,61,0.85) 0%, rgba(13,46,94,0.85) 45%, rgba(10,74,110,0.85) 100%),
    url('https://www.washingtonpost.com/creativegroup/uploads/2022/01/17220554/hero-1-3.jpg') center/cover no-repeat;
}
@media (max-width: 500px){
 .about-hero {
  height: 400px;
 }
   .about-hero .badge-accent {
    font-size: 0.10rem;
  }
 .about-hero p{
  font-size: 10px;
 }
 .about-hero h1{
  font-size: 0.25rem;
 }
}
@media (max-width: 768px){
 .about-hero {
  height: 400px;
 }
    .about-hero .badge-accent {
    font-size: 1.0rem;
  }
 .about-hero p{
  font-size: 16px;
 }
 .about-hero h1{
  font-size: 1.0rem;
 }
}
</style>
<!-- about-hero-section -->
<section class="about-hero">
  <div class="container text-center">
    <span class="badge-accent"
      style="background:rgba(255,255,255,0.2);color:white;border:1px solid rgba(255,255,255,0.3);">About CARE
      Group</span>
    <h1 class="hero-title mt-2 mb-3" style="font-size:2.6rem;">Transforming Healthcare<br>Across Pakistan</h1>
    <p style="color:rgba(255,255,255,0.85);max-width:560px;margin:0 auto;font-size:1.05rem;">Founded in 2026, CARE Group
      is Pakistan's leading digital health platform connecting patients with verified medical specialists.</p>
  </div>
</section>


<!--our Story -->
<section class="section-padding">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 animate-on-scroll">
        <span class="badge-accent">Our Story</span>
        <h2 class="section-title mt-2 mb-4">Who We Are</h2>
        <p style="color:var(--text-muted);line-height:1.9;margin-bottom:16px;">CARE Group was established with a single
          mission: to make quality healthcare accessible to every Pakistani. We recognized that finding the right doctor
          shouldn't be a complicated, time-consuming process.</p>
        <p style="color:var(--text-muted);line-height:1.9;margin-bottom:16px;">Today, we operate across <?php
              $result2 = $connect->query("SELECT COUNT(*) AS total FROM cities");
              $row2 = $result2->fetch_assoc();
              $totalcities = $row2['total'];
              echo $totalcities;
              ?> cities with a
          network of over <?php
                    $result_doctor = $connect->query("SELECT COUNT(*) AS total FROM doctors");
                    $row_doctor = $result_doctor->fetch_assoc();
                    $total_doctor = $row_doctor['total'];
                    echo $total_doctor;
                    ?> verified medical specialists, serving more than <?php
                    $result_patient = $connect->query("SELECT COUNT(*) AS total FROM patients");
                    $row_patient = $result_patient->fetch_assoc();
                    $total_patient = $row_patient['total'];
                    echo $total_patient;
                    ?> patients. Our platform handles
          everything from appointment booking to medical record management.</p>
        <p style="color:var(--text-muted);line-height:1.9;">Every doctor on our platform is rigorously verified through
          Pakistan Medical Commission (PMC) records, ensuring you always receive care from qualified professionals.</p>
        <div class="d-flex flex-wrap gap-4 mt-4">
          <?php $stats = [
            ['500+', 'var(--primary)', 'Verified Doctors'],
            ['50K+', 'var(--accent)', 'Patients Served'],
            ['15', 'var(--success)', 'Cities'],
            ['4.9★', 'var(--warning)', 'Avg Rating']
          ]; ?>
          <?php foreach ($stats as $s): ?>
            <div>
              <div style="font-size:2rem;font-weight:800;color:<?php echo $s[1]; ?>">
                <?php echo $s[0]; ?>
              </div>
              <div style="font-size:0.85rem;color:var(--text- muted);"><?php echo $s[2]; ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-6 animate-on-scroll" style="transition-delay:.15s;">
        <div
          style="background:linear-gradient(135deg,var(--light-blue),#E0F7FA);border-radius:var(--radius-lg);padding:40px;text-align:center;">
          <i class="fas fa-hospital-user" style="font-size:8rem;color:var(--primary);opacity:0.3;"></i>
          <div class="row g-3 mt-3">
            <?php $features = [['shield-alt', 'var(--primary)', 'PMC Verified'], ['clock', 'var(--accent)', '24/7 Available'], ['lock', 'var(--success)', 'Secure & Private'], ['star', 'var(--warning)', 'Top Rated']];
            foreach ($features as $f): ?>
              <div class="col-6">
                <div style="background:white;border-radius:var(--radius);padding:16px;box-shadow:var(--shadow-sm);"><i
                    class="fas fa-<?php echo $f[0]; ?>"
                    style="font-size:1.5rem;color:<?php echo $f[1]; ?>;display:block;margin-bottom:8px;"></i>
                  <div style="font-size:0.82rem;font-weight:600;color:var(--text-dark);"><?php echo $f[2]; ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Mission & Vision -->
<section class="section-padding" style="background:var(--off-white);">
  <div class="container">
    <div class="text-center mb-5">
      <span class="badge-accent">Our Purpose</span>
      <h2 class="section-title mt-2">Mission &amp; Vision</h2>
    </div>
    <div class="row g-4">
      <?php $mvv = [['bullseye', 'var(--primary)', 'Our Mission', 'To democratize healthcare access in Pakistan by connecting every patient with qualified, affordable medical specialists through innovative technology and compassionate service.'], ['eye', 'var(--accent)', 'Our Vision', 'To become Pakistan\'s most trusted healthcare platform, where every citizen can easily access quality medical care regardless of their location or economic status.'], ['heart', 'var(--success)', 'Our Values', 'Compassion, integrity, and excellence guide everything we do. We are committed to patient privacy, medical accuracy, and building lasting relationships between doctors and patients.']];
      foreach ($mvv as $i => $m): ?>
        <div class="col-md-4 animate-on-scroll" style="transition-delay:<?php echo $i * 0.1; ?>s">
          <div class="mission-card">
            <div class="mission-icon"
              style="background:linear-gradient(135deg,<?php echo $m[1]; ?>,<?php echo $m[1]; ?>99);"><i
                class="fas fa-<?php echo $m[0]; ?>"></i></div>
            <h5 style="font-weight:700;color:var(--text-dark);margin-bottom:12px;"><?php echo $m[2]; ?></h5>
            <p style="color:var(--text-muted);font-size:0.9rem;line-height:1.8;"><?php echo $m[3]; ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Why Choose Us -->
<section class="section-padding">
  <div class="container">
    <div class="text-center mb-5">
      <span class="badge-accent">Our Advantage</span>
      <h2 class="section-title mt-2">Why Choose CARE Group?</h2>
      <p class="section-subtitle mt-2">We go above and beyond to ensure your healthcare experience is exceptional.</p>
    </div>
    <div class="row g-4">
      <?php $features = [['shield-alt', 'var(--light-blue)', 
      'var(--primary)', '100% Verified Doctors', 
      'All doctors are PMC-verified with confirmed qualifications and experience.'],
       ['clock', '#E0F7FA', 'var(--accent)', 'Instant Booking',
        'Book appointments in under 2 minutes, 9/5 from anywhere in pakistan.'],
         ['lock', '#E8F5E9', 'var(--success)', 'Privacy First', 
         'Your medical records are encrypted and fully protected.'], 
         ['headset', '#EDE7F6', '#6A1B9A', '24/7 Support', 'Our customer support team is always ready to assist you.']
          , ['wallet', '#E8F5E9', 'var(--success)', 
          'Affordable Care', 'Competitive consultation fees with payment on appointment.'],
           ['city', '#FFEBEE', 'var(--danger)', 'Nationwide Coverage', 'Present in 15 major cities across Pakistan and growing.']];
      foreach ($features as $i => $f): ?>
        <div class="col-md-6 col-lg-4 animate-on-scroll" style="transition-delay:<?php echo ($i % 4) * 0.07; ?>s">
          <div class="why-choose-card">
            <div class="why-icon" style="background:<?php echo $f[1]; ?>"><i class="fas fa-<?php echo $f[0]; ?>"
                style="color:<?php echo $f[2]; ?>"></i></div>
            <h6 style="font-weight:700;color:var(--text-dark);margin-bottom:8px;"><?php echo $f[3]; ?></h6>
            <p style="font-size:0.85rem;color:var(--text-muted);"><?php echo $f[4]; ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Our Team -->
<section class="section-padding" style="background:var(--off-white);">
  <div class="container">
    <div class="text-center mb-5">
      <span class="badge-accent">The People</span>
      <h2 class="section-title mt-2">Meet Our Leadership Team</h2>
      <p class="section-subtitle mt-2">The dedicated professionals driving CARE Group's vision of accessible healthcare.
      </p>
    </div>
    <div class="row g-4">
      <?php $team = [['user-md', 'var(--light-blue)', 'var(--primary)', 'Dr. Asim Raza', 'Founder & CEO', 'MBBS, MBA | Visionary healthcare entrepreneur with 20 years of experience.'], ['user-tie', '#E0F7FA', 'var(--accent)', 'Fatima Siddiqui', 'Chief Technology Officer', 'MS Computer Science | Building the digital backbone of CARE Group.'], ['user-md', '#EDE7F6', '#6A1B9A', 'Dr. Omar Farooq', 'Chief Medical Officer', 'MBBS, FRCP | Overseeing medical quality and doctor verification.'], ['user-tie', '#E8F5E9', 'var(--success)', 'Amna Tariq', 'Head of Operations', 'MBA | Coordinating nationwide operations and partner hospital network.']];
      foreach ($team as $i => $t): ?>
        <div class="col-md-6 col-lg-3 animate-on-scroll" style="transition-delay:<?php echo $i * 0.08; ?>s">
          <div class="team-card">
            <div class="team-avatar"
              style="background:linear-gradient(135deg,<?php echo $t[1]; ?>,<?php echo $t[1]; ?>99);color:<?php echo $t[2]; ?>">
              <i class="fas fa-<?php echo $t[0]; ?>"></i></div>
            <div class="team-body">
              <h5><?php echo htmlspecialchars($t[3]); ?></h5>
              <span><?php echo htmlspecialchars($t[4]); ?></span>
              <p><?php echo htmlspecialchars($t[5]); ?></p>
              <div class="d-flex justify-content-center gap-2 mt-2">
                <a href="#" style="color:var(--primary);font-size:0.9rem;"><i class="fab fa-linkedin"></i></a>
                <a href="#" style="color:var(--primary);font-size:0.9rem;"><i class="fab fa-twitter"></i></a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- banner how you can join care-group Family -->
<section class="cta-banner">
  <div class="container text-center position-relative" style="z-index:1;">
    <h2 class="mb-3">Join the CARE Group Family</h2>
    <p class="mb-4" style="max-width:500px;margin:0 auto 2rem;">Whether you're a patient seeking care or a doctor
      looking to expand your practice, we're here for you.</p>
    <div class="d-flex flex-wrap gap-3 justify-content-center">
      <a href="register.php" class="btn-primary-care" style="background:white;color:var(--primary);">
        <i class="fas fa-user-plus"></i> Join as Patient
      </a>
      <a href="register.php" class="btn-outline-care">
        <i class="fas fa-user-md"></i> Join as Doctor
      </a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>