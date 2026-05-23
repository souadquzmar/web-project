<?php
require_once __DIR__ . '/includes/bootstrap.php';
$page_title = 'About Us';
$nav_active = 'about';
$extra_css = '<link rel="stylesheet" href="css/about.css">';

$db = get_db();
$team = $db->query("SELECT * FROM users WHERE role IN ('agent','admin') ORDER BY created_at ASC")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/includes/header.php';
?>

  <div class="page-hero">
    <div class="container">
      <h1>About FindHouses</h1>
      <div class="breadcrumb-nav"><a href="index.php">Home</a><span class="sep">›</span><span class="current">About
          Us</span></div>
    </div>
  </div>

  <section class="section">
    <div class="container">
      <div class="about-split">
        <div class="about-img-stack reveal from-left">
          <img class="img-main" src="img/popular-places/14.jpg" alt="About" />
          <img class="img-secondary" src="img/popular-places/13.jpg" alt="Team" />
          <div class="about-img-badge">12+<small>Years of Excellence</small></div>
        </div>
        <div class="reveal">
          <div
            style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:var(--mainColor);background:var(--mainColorLight);padding:5px 14px;border-radius:20px;margin-bottom:16px">
            Who We Are</div>
          <h2
            style="font-size:clamp(1.8rem,3vw,2.4rem);font-weight:800;letter-spacing:-0.03em;color:var(--dark);margin-bottom:14px">
            We Help You Find Your Perfect Home</h2>
          <div
            style="width:50px;height:4px;background:linear-gradient(90deg,var(--mainColor),#ff7090);border-radius:2px;margin-bottom:20px">
          </div>
          <p style="color:var(--textLight);line-height:1.8;margin-bottom:16px">Founded in 2012, FindHouses has grown
            into America's most trusted real estate platform. We connect buyers, sellers, and renters with exceptional
            properties and the expert agents who know them best.</p>
          <p style="color:var(--textLight);line-height:1.8;margin-bottom:28px">Our mission: make finding your next home
            as easy and enjoyable as possible — with transparency, expertise, and genuine care at every step.</p>
          <img src="img/about-us/signature.png" alt="Signature" style="height:52px;margin-bottom:24px" />
          <div class="about-perks">
            <div class="about-perk">
              <div class="about-perk-icon"><i class="fa-solid fa-shield-halved"></i></div>
              <div>
                <h5>Fully Licensed</h5>
                <p>All agents are verified and background-checked.</p>
              </div>
            </div>
            <div class="about-perk">
              <div class="about-perk-icon"><i class="fa-solid fa-trophy"></i></div>
              <div>
                <h5>Award Winning</h5>
                <p>200+ industry awards since 2012.</p>
              </div>
            </div>
            <div class="about-perk">
              <div class="about-perk-icon"><i class="fa-solid fa-handshake"></i></div>
              <div>
                <h5>5-Star Service</h5>
                <p>98% client satisfaction rate.</p>
              </div>
            </div>
            <div class="about-perk">
              <div class="about-perk-icon"><i class="fa-solid fa-globe"></i></div>
              <div>
                <h5>Nationwide</h5>
                <p>Serving 50+ major cities across the US.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Stats -->
  <section class="section section-grey">
    <div class="container">
      <div class="stats-grid reveal">
        <div class="stats-cell">
          <div class="stat-number">345+</div>
          <div class="stat-label">Homes Sold</div>
        </div>
        <div class="stats-cell">
          <div class="stat-number">432+</div>
          <div class="stat-label">Listings</div>
        </div>
        <div class="stats-cell">
          <div class="stat-number">840+</div>
          <div class="stat-label">Happy Clients</div>
        </div>
        <div class="stats-cell">
          <div class="stat-number">200+</div>
          <div class="stat-label">Awards Won</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Why choose us -->
  <section class="section">
    <div class="container">
      <div class="section-header reveal">
        <h2>What Sets Us Apart</h2>
        <div class="accent-line"></div>
      </div>
      <div class="row g-4">
        <div class="col-lg-3 col-md-6 reveal">
          <div class="feature-card">
            <div class="feature-card__icon"><img src="img/choose-us/icon-1.svg" alt="" /></div>
            <h4>Wide Selection</h4>
            <p>Thousands of curated properties across every city, budget, and style.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 reveal">
          <div class="feature-card">
            <div class="feature-card__icon"><img src="img/choose-us/icon-2.svg" alt="" /></div>
            <h4>Trusted Experts</h4>
            <p>Verified agents with deep local knowledge and a track record of results.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 reveal">
          <div class="feature-card">
            <div class="feature-card__icon"><img src="img/choose-us/icon-3.svg" alt="" /></div>
            <h4>Easy Finance</h4>
            <p>Connect with top lenders and get pre-approved in minutes.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 reveal">
          <div class="feature-card">
            <div class="feature-card__icon"><img src="img/choose-us/icon-4.svg" alt="" /></div>
            <h4>Always Nearby</h4>
            <p>Local agents in every major city ready to guide you in person.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Team -->
  <section class="section section-grey">
    <div class="container">
      <div class="section-header reveal">
        <h2>Leadership Team</h2>
        <div class="accent-line"></div>
      </div>
      <div class="team-grid">
        <?php 
        foreach ($team as $member): 
        ?>
        <div class="reveal">
          <div class="agent-card">
            <img class="agent-img" src="<?= e(avatar_url($member['avatar'] ?? '', $member['id'])) ?>" alt="<?= e($member['first_name'] . ' ' . $member['last_name']) ?>" />
            <div class="agent-name"><?= e($member['first_name'] . ' ' . $member['last_name']) ?></div>
            <div class="agent-role"><?= e(ucfirst($member['role'])) ?></div>
            <div class="agent-socials">
              <a href="#" class="social-icon"><i class="fa-brands fa-twitter"></i></a>
              <a href="#" class="social-icon"><i class="fa-brands fa-linkedin-in"></i></a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="text-center mt-5 reveal">
        <a href="agents.php" class="btn btn-dark btn-lg btn-pill">Meet All Agents <i
            class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </section>

  <!-- Partners -->
  <section class="section" style="padding-top:60px;padding-bottom:60px">
    <div class="container">
      <div class="section-header reveal" style="margin-bottom:36px">
        <h2>Our Partners</h2>
        <div class="accent-line"></div>
      </div>
    </div>
    <div class="swiper swiper-partners">
      <div class="swiper-wrapper">
        <div class="swiper-slide"><img class="partner-logo" src="img/partners/p-1.jpg" alt="Partner" /></div>
        <div class="swiper-slide"><img class="partner-logo" src="img/partners/p-2.jpg" alt="Partner" /></div>
        <div class="swiper-slide"><img class="partner-logo" src="img/partners/p-3.jpg" alt="Partner" /></div>
        <div class="swiper-slide"><img class="partner-logo" src="img/partners/p-4.jpg" alt="Partner" /></div>
        <div class="swiper-slide"><img class="partner-logo" src="img/partners/p-5.jpg" alt="Partner" /></div>
        <div class="swiper-slide"><img class="partner-logo" src="img/partners/p-6.jpg" alt="Partner" /></div>
        <div class="swiper-slide"><img class="partner-logo" src="img/partners/p-1.jpg" alt="Partner" /></div>
        <div class="swiper-slide"><img class="partner-logo" src="img/partners/p-2.jpg" alt="Partner" /></div>
        <div class="swiper-slide"><img class="partner-logo" src="img/partners/p-3.jpg" alt="Partner" /></div>
      </div>
    </div>
  </section>

<?php
$extra_js = '<script src="js/about.js"></script>';
include __DIR__ . '/includes/footer.php';
?>
