<?php
// ============================================================
//  includes/footer.php  —  Site-wide footer + scripts
// ============================================================
if (!isset($dash_footer)) $dash_footer = false;
?>
<?php if ($dash_footer): ?>
<footer class="dash-mini-footer">
  <div class="container dash-mini-footer__inner">
    <p class="dash-mini-footer__copy">© <?= date('Y') ?> FindHouses — All Rights Reserved.</p>
    <div class="dash-mini-footer__links">
      <a href="index.php" class="dash-mini-footer__link">Home</a>
      <a href="contact.php" class="dash-mini-footer__link">Contact</a>
    </div>
  </div>
</footer>
<?php else: ?>
<footer class="site-footer">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-3 col-md-6">
        <div class="footer-logo">Find<span>.</span>Houses</div>
        <p class="footer-desc">Your trusted real estate partner. Helping families find their perfect home since 2006.</p>
        <div class="footer-contact">
          <div class="footer-contact-item"><i class="fa-solid fa-location-dot"></i><p>95 South Park Avenue, USA</p></div>
          <div class="footer-contact-item"><i class="fa-solid fa-phone"></i><p>+456 875 369 208</p></div>
          <div class="footer-contact-item"><i class="fa-solid fa-envelope"></i><p>support@findhouses.com</p></div>
        </div>
        <div class="footer-socials">
          <a href="#" class="footer-social"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" class="footer-social"><i class="fa-brands fa-twitter"></i></a>
          <a href="#" class="footer-social"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" class="footer-social"><i class="fa-brands fa-youtube"></i></a>
        </div>
      </div>
      <div class="col-lg-2 col-md-6">
        <div class="footer-col-title">Navigation</div>
        <div class="footer-links">
          <div class="footer-link"><a href="index.php">Home</a></div>
          <div class="footer-link"><a href="listing.php">Listing</a></div>
          <div class="footer-link"><a href="about.php">About Us</a></div>
          <div class="footer-link"><a href="agents.php">Agents</a></div>
          <div class="footer-link"><a href="blog.php">Blog</a></div>
          <div class="footer-link"><a href="contact.php">Contact</a></div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="footer-col-title">Latest Tweets</div>
        <div class="footer-tweet"><i class="fa-brands fa-twitter"></i>
          <div><p><a href="#">@Findhouses</a> New luxury listings dropped — check them out now!</p>
          <div class="footer-tweet-time">3 days ago</div></div>
        </div>
        <div class="footer-tweet"><i class="fa-brands fa-twitter"></i>
          <div><p><a href="#">@Findhouses</a> Market update: NYC home prices remain strong.</p>
          <div class="footer-tweet-time">6 days ago</div></div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="footer-col-title">Newsletter</div>
        <div class="footer-newsletter">
          <p>Sign up for the latest listings and market news.</p>
          <form action="actions/newsletter.php" method="POST" class="footer-email-row">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
            <input class="footer-email-input" type="email" name="email" placeholder="Your email address..." required/>
            <button type="submit" class="footer-subscribe">SUBSCRIBE</button>
          </form>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p class="footer-copy">© <?= date('Y') ?> FindHouses — All Rights Reserved.</p>
      <div class="footer-legal"><a href="#">Privacy Policy</a><a href="#">Terms of Use</a></div>
    </div>
  </div>
</footer>
<?php endif; ?>

<button class="back-top" id="backTop" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="js/global.js"></script>
<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
