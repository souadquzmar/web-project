<?php
require_once __DIR__ . '/includes/bootstrap.php';
$page_title = 'Contact Us';
$nav_active = 'contact';
$extra_css = '<link rel="stylesheet" href="css/contact.css">';
include __DIR__ . '/includes/header.php';
?>

  <div class="page-hero">
    <div class="container">
      <h1>Contact Us</h1>
      <div class="breadcrumb-nav"><a href="index.php">Home</a><span class="sep">›</span><span
          class="current">Contact</span></div>
    </div>
  </div>
  <section class="section section-grey">
    <div class="container">
      <div class="contact-grid">
        <div class="contact-form-card reveal">
          <h3>Send Us a Message</h3>
          <?= render_flash() ?>
          <form action="actions/contact.php" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <div class="form-row">
              <div class="form-group"><label class="form-label">First Name</label><input class="form-input" type="text"
                  name="first_name" placeholder="John" required /></div>
              <div class="form-group"><label class="form-label">Last Name</label><input class="form-input" type="text"
                  name="last_name" placeholder="Smith" required /></div>
            </div>
            <div class="form-row">
              <div class="form-group"><label class="form-label">Email</label><input class="form-input" type="email"
                  name="email" placeholder="you@example.com" required /></div>
              <div class="form-group"><label class="form-label">Phone</label><input class="form-input" type="tel"
                  name="phone" placeholder="+1 234 567 890" /></div>
            </div>
            <div class="form-group"><label class="form-label">Subject</label><select class="form-select" name="subject">
                <option>General Inquiry</option>
                <option>Property Inquiry</option>
                <option>Agent Request</option>
                <option>Partnership</option>
              </select></div>
            <div class="form-group"><label class="form-label">Message</label><textarea class="form-textarea"
                name="body" style="min-height:140px" placeholder="How can we help you?" required></textarea></div>
            <button class="btn btn-primary btn-lg" type="submit"><i class="fa-solid fa-paper-plane"></i> Send
              Message</button>
            <div class="form-success"><i class="fa-solid fa-circle-check"></i> Message sent! We'll get back to you
              within 24 hours.</div>
          </form>
        </div>
        <div>
          <div class="contact-info reveal">
            <h3>Get In Touch</h3>
            <p>Our team is available Monday through Friday, 9am–6pm EST. We'll respond within one business day.</p>
            <div class="contact-detail">
              <div class="contact-detail-icon"><i class="fa-solid fa-location-dot"></i></div>
              <div>
                <h5>Office Address</h5>
                <p>95 South Park Avenue, New York, NY 10003, USA</p>
              </div>
            </div>
            <div class="contact-detail">
              <div class="contact-detail-icon"><i class="fa-solid fa-phone"></i></div>
              <div>
                <h5>Phone Number</h5>
                <p>+456 875 369 208</p>
              </div>
            </div>
            <div class="contact-detail">
              <div class="contact-detail-icon"><i class="fa-solid fa-envelope"></i></div>
              <div>
                <h5>Email Address</h5>
                <p>support@findhouses.com</p>
              </div>
            </div>
            <div class="contact-detail">
              <div class="contact-detail-icon"><i class="fa-regular fa-clock"></i></div>
              <div>
                <h5>Office Hours</h5>
                <p>Mon–Fri: 9:00am – 6:00pm EST<br>Sat: 10:00am – 2:00pm EST</p>
              </div>
            </div>
            <div class="footer-socials" style="margin-top:24px">
              <a href="#" class="footer-social"><i class="fa-brands fa-facebook-f"></i></a>
              <a href="#" class="footer-social"><i class="fa-brands fa-twitter"></i></a>
              <a href="#" class="footer-social"><i class="fa-brands fa-instagram"></i></a>
              <a href="#" class="footer-social"><i class="fa-brands fa-linkedin-in"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php
$extra_js = '<script src="js/contact.js"></script>';
include __DIR__ . '/includes/footer.php';
?>
