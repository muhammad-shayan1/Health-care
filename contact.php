<?php
$page_title = 'Contact Us';
$success_msg = '';
$errors = [];
$form = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $form = [
    'name' => trim($_POST['name'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'phone' => trim($_POST['phone'] ?? ''),
    'subject' => trim($_POST['subject'] ?? ''),
    'message' => trim($_POST['message'] ?? ''),
  ];
  if (empty($form['name']))
    $errors[] = 'Full name is required.';
  if (empty($form['email']) || !filter_var($form['email'], FILTER_VALIDATE_EMAIL))
    $errors[] = 'A valid email address is required.';
  if (empty($form['subject']))
    $errors[] = 'Please select a subject.';
  if (empty($form['message']))
    $errors[] = 'Message cannot be empty.';
  if (!isset($_POST['consent']))
    $errors[] = 'You must agree to the privacy policy.';

  if (empty($errors)) {
    // UI only – in real project you'd send email here
    $success_msg = 'Thank you, ' . htmlspecialchars($form['name']) . '! Your message has been received. We will reply within 24 hours.';
    $form = []; // reset form
  }
}
include 'includes/head.php';
?>

<?php include 'includes/navbar.php'; ?>

<style>
    .contact-hero .badge-accent {
    font-size: 1.75rem;
    padding: 8px 20px;
    border-radius: 999px;
    letter-spacing: 0.5px;
    font-weight: 600;
  }  
   .contact-hero {
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
 .contact-hero {
  height: 400px;
 }
   .contact-hero .badge-accent {
    font-size: 0.10rem;
  }
 .contact-hero p{
  font-size: 10px;
 }
 .contact-hero h1{
  font-size: 0.25rem;
 }
}
@media (max-width: 768px){
 .contact-hero {
  height: 400px;
 }
    .contact-hero .badge-accent {
    font-size: 1.0rem;
  }
 .contact-hero p{
  font-size: 16px;
 }
 .contact-hero h1{
  font-size: 1.0rem;
 }
}
</style>
<section class="contact-hero">
  <div class="container text-center">
    <span class="badge-accent"
      style="background:rgba(255,255,255,0.2);color:white;border:1px solid rgba(255,255,255,0.3);">Get in Touch</span>
    <h1 class="hero-title mt-2 mb-2" style="font-size:3rem;">We're Here to Help</h1>
    <p style="color:rgba(255,255,255,0.85);max-width:500px;margin:0 auto;">Have questions, suggestions, or need support?
      Reach out to our team anytime.</p>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row g-4 align-items-start">

      <!-- LEFT: Contact Form -->
      <div class="col-lg-7 animate-on-scroll">
        <div class="contact-form-card">
          <h4 style="font-weight:700;color:var(--text-dark);margin-bottom:8px;"><i class="fas fa-paper-plane me-2"
              style="color:var(--primary);"></i>Send Us a Message</h4>
          <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:28px;">We typically respond within 24 hours
            on business days.</p>

          <?php if ($success_msg): ?>
            <div class="alert alert-success d-flex align-items-center gap-2 mb-4" style="border-radius:var(--radius-sm);">
              <i class="fas fa-check-circle fa-lg" style="color:var(--success);"></i>
              <div><?php echo $success_msg; ?></div>
            </div>
          <?php endif; ?>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mb-4" style="border-radius:var(--radius-sm);">
              <i class="fas fa-exclamation-circle me-2"></i><strong>Please fix the following:</strong>
              <ul class="mb-0 mt-2">
                <?php foreach ($errors as $e): ?>
                  <li style="font-size:0.88rem;"><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="POST" action="contact.php" novalidate>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text"
                    class="form-control <?php echo in_array('Full name is required.', $errors) ? 'is-invalid' : ''; ?>"
                    name="name" id="contactName" placeholder="Full Name"
                    value="<?php echo htmlspecialchars($form['name'] ?? ''); ?>" required />
                  <label for="contactName"><i class="fas fa-user me-1"></i>Full Name</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="email"
                    class="form-control <?php echo in_array('A valid email address is required.', $errors) ? 'is-invalid' : ''; ?>"
                    name="email" id="contactEmail" placeholder="Email"
                    value="<?php echo htmlspecialchars($form['email'] ?? ''); ?>" required />
                  <label for="contactEmail"><i class="fas fa-envelope me-1"></i>Email Address</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="tel" class="form-control" name="phone" id="contactPhone" placeholder="Phone"
                    value="<?php echo htmlspecialchars($form['phone'] ?? ''); ?>" />
                  <label for="contactPhone"><i class="fas fa-phone me-1"></i>Phone (Optional)</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <select
                    class="form-select <?php echo in_array('Please select a subject.', $errors) ? 'is-invalid' : ''; ?>"
                    name="subject" id="contactSubject" required>
                    <option value="" disabled <?php echo empty($form['subject']) ? 'selected' : ''; ?>>Select subject
                    </option>
                    <?php foreach (['General Inquiry', 'Appointment Issue', 'Doctor Registration', 'Technical Support', 'Billing Query', 'Partnership', 'Other'] as $subj): ?>
                      <option value="<?php echo $subj; ?>" <?php echo (($form['subject'] ?? '') === $subj) ? 'selected' : ''; ?>><?php echo $subj; ?></option>
                    <?php endforeach; ?>
                  </select>
                  <label for="contactSubject"><i class="fas fa-tag me-1"></i>Subject</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-floating">
                  <textarea
                    class="form-control <?php echo in_array('Message cannot be empty.', $errors) ? 'is-invalid' : ''; ?>"
                    name="message" id="contactMsg" placeholder="Message" style="height:150px;"
                    required><?php echo htmlspecialchars($form['message'] ?? ''); ?></textarea>
                  <label for="contactMsg"><i class="fas fa-comment me-1"></i>Your Message</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-check mb-2">
                  <input type="checkbox" class="form-check-input" id="contactConsent" name="consent" required />
                  <label class="form-check-label" for="contactConsent"
                    style="font-size:0.85rem;color:var(--text-muted);">
                    I agree to CARE Group's <a href="#" style="color:var(--primary);">Privacy Policy</a> and consent to
                    being contacted.
                  </label>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-primary-care" style="padding:13px 36px;">
                  <i class="fas fa-paper-plane"></i> Send Message
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- RIGHT: Contact Info -->
      <div class="col-lg-5 animate-on-scroll" style="transition-delay:.15s">
        <div class="contact-info-card mb-4">
          <h5 style="font-weight:700;color:white;margin-bottom:24px;"><i class="fas fa-address-book me-2"></i>Contact
            Information</h5>
          <?php $contacts = [['map-marker-alt', 'Head Office', 'Plot 45, Block B, PECHS, Karachi, Sindh 75400, Pakistan'], ['phone', 'Phone', '+92 21 1234 5678'], ['envelope', 'Email', 'info@caregroup.pk'], ['headset', 'Support Helpline', '0800-CARE-24 (Toll Free)'], ['clock', 'Office Hours', 'Mon &ndash; Fri: 8:00 AM &ndash; 8:00 PM']];
          foreach ($contacts as $c): ?>
            <div class="contact-info-item">
              <div class="contact-info-icon"><i class="fas fa-<?php echo $c[0]; ?>"></i></div>
              <div class="contact-info-text"><strong><?php echo $c[1]; ?></strong><span><?php echo $c[2]; ?></span></div>
            </div>
          <?php endforeach; ?>
          <div>
            <p style="font-size:0.82rem;opacity:.75;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;">
              Follow Us</p>
            <div class="d-flex gap-2">
              <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
              <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
              <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
              <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
              <a href="#" class="social-btn"><i class="fab fa-youtube"></i></a>
            </div>
          </div>
        </div>

        <div class="card-care p-4">
          <h6 style="font-weight:700;color:var(--text-dark);margin-bottom:8px;"><i class="fas fa-map-marker-alt me-2"
              style="color:var(--primary);"></i>Find Us on Map</h6>
          <div style="border-radius:12px;overflow:hidden;border:2px solid rgba(21,101,192,0.12);margin-bottom:0;">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3620.576!2d67.0280672!3d24.9104471!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3eb33fb94eb66db5%3A0x6e383e3adce4746a!2sAptech%20Computer%20Education%20Site%20Center!5e0!3m2!1sen!2spk!4v1700000000000!5m2!1sen!2spk"
              width="100%"
              height="220"
              style="border:0;display:block;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="Aptech Site Center - Nazimabad, Karachi">
            </iframe>
          </div>
          <div class="d-flex gap-2 flex-wrap">
            <a href="https://maps.google.com/?q=PECHS+Karachi" target="_blank" class="btn-primary-care"
              style="padding:10px 20px;font-size:0.85rem;">
              <i class="fas fa-directions"></i> Get Directions
            </a>
            <a href="tel:+922112345678" class="btn-accent" style="padding:10px 20px;font-size:0.85rem;">
              <i class="fas fa-phone"></i> Call Now
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php
include "feedback.php";
?>
<!-- FAQ Section -->
<section class="section-padding" style="background:var(--off-white);">
  <div class="container">
    <div class="text-center mb-5">
      <span class="badge-accent">FAQs</span>
      <h2 class="section-title mt-2">Frequently Asked Questions</h2>
    </div>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="accordion" id="faqAccordion">
          <?php $faqs = [['How do I book an appointment?', 'Simply search for a doctor by city and specialization, select an available time slot, and confirm your booking. You\'ll receive an instant confirmation by email.'], ['Are the doctors on CARE Group verified?', 'Yes! Every doctor is verified through the Pakistan Medical Commission (PMC). We also manually verify qualifications, experience, and clinic details before approval.'], ['Can I cancel or reschedule an appointment?', 'Yes, appointments can be cancelled or rescheduled up to 4 hours before the scheduled time through your patient dashboard.'], ['Is my personal health information secure?', 'Absolutely. We use enterprise-grade 256-bit SSL encryption for all data transmission. Your data is never shared with third parties.']];
          foreach ($faqs as $i => $faq): ?>
            <div class="accordion-item mb-2"
              style="border-radius:var(--radius-sm);border:1px solid rgba(21,101,192,0.1);overflow:hidden;">
              <h2 class="accordion-header">
                <button class="accordion-button <?php echo $i > 0 ? 'collapsed' : ''; ?>" type="button"
                  data-bs-toggle="collapse" data-bs-target="#faq<?php echo $i; ?>"
                  style="font-size:0.92rem;font-weight:600;color:var(--text-dark);">
                  <?php echo htmlspecialchars($faq[0]); ?>
                </button>
              </h2>
              <div id="faq<?php echo $i; ?>" class="accordion-collapse collapse <?php echo $i === 0 ? 'show' : ''; ?>"
                data-bs-parent="#faqAccordion">
                <div class="accordion-body" style="font-size:0.88rem;color:var(--text-muted);">
                  <?php echo htmlspecialchars($faq[1]); ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>