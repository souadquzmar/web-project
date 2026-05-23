<?php
require_once __DIR__ . '/includes/bootstrap.php';

$page_title = 'Find Your Dream Property';
$nav_active = 'home';
$extra_css = '<link rel="stylesheet" href="css/index.css">';

$db = get_db();

// Featured properties (6 most recent featured)
$feat_res = $db->query(
    "SELECT p.*, u.first_name, u.last_name, u.avatar
     FROM properties p
     JOIN users u ON u.id = p.user_id
     WHERE p.listing_status='active' AND p.featured=1
     ORDER BY p.created_at DESC LIMIT 6"
);
$featured = $feat_res->fetch_all(MYSQLI_ASSOC);

// Stats
$stat_props = $db->query("SELECT COUNT(*) FROM properties WHERE listing_status='active'")->fetch_row()[0];
$stat_users = $db->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$stat_reviews = $db->query("SELECT COUNT(*) FROM reviews")->fetch_row()[0];

// Popular places with property counts
$places = [
    ['New York','img/popular-places/12.jpg'],
    ['Los Angeles','img/popular-places/13.jpg'],
    ['San Francisco','img/popular-places/14.jpg'],
    ['Atlanta','img/popular-places/9.jpg'],
    ['Miami','img/popular-places/15.jpg'],
    ['Chicago','img/popular-places/10.jpg'],
    ['Houston','img/popular-places/5.jpg'],
    ['Orlando','img/popular-places/6.jpg'],
];

// Agents
$agents = $db->query("SELECT * FROM users WHERE role IN ('agent','admin') ORDER BY created_at ASC LIMIT 4");

// Testimonials
$test_res = $db->query(
    "SELECT r.*, p.title as prop_title FROM reviews r
     JOIN properties p ON p.id = r.property_id
     ORDER BY r.created_at DESC LIMIT 6"
);
$testimonials = $test_res->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/includes/header.php';
?>

  <!-- ══ HERO ══════════════════════════════════════════════════ -->
  <header class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1>Find Your Dream <span class="typed-word" id="typed-word"></span><span class="typed-cursor"></span></h1>
      <p>We Have Over <?= number_format($stat_props) ?> Properties For You</p>
      <div class="search-tabs">
        <button class="tab-pill active">For Sale</button>
        <button class="tab-pill">For Rent</button>
      </div>
      <div class="hero-search-wrap">
        <form action="listing.php" method="GET" class="search-form">
          <input type="text" name="q" placeholder="Enter keyword..." />
          <select name="type">
            <option value="">Property type</option>
            <option>House</option>
            <option>Apartment</option>
            <option>Condo</option>
          </select>
          <select name="city">
            <option value="">Location</option>
            <option>New York</option>
            <option>LA</option>
            <option>Miami</option>
          </select>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        </form>
      </div>
    </div>
    <div class="scroll-hint"><span>Scroll</span><i class="fa-solid fa-chevron-down"></i></div>
  </header>

  <!-- ══ POPULAR PLACES ════════════════════════════════════════ -->
  <section class="section">
    <div class="container">
      <div class="section-header reveal">
        <h2>Popular Places</h2>
        <div class="accent-line"></div>
        <p class="mt-2">Properties in the most sought-after cities</p>
      </div>
      <div class="row g-3">
        <?php 
        foreach ($places as [$city_name, $img]):
          $cnt = $db->prepare("SELECT COUNT(*) FROM properties WHERE city=? AND listing_status='active'");
          $cnt->bind_param('s', $city_name);
          $cnt->execute();
          $city_count = $cnt->get_result()->fetch_row()[0];
          $cnt->close();
        ?>
        <div class="col-lg-3 col-md-6 col-sm-6 reveal">
          <div class="place-card">
            <div class="place-card-img">
              <img src="<?= e($img) ?>" alt="<?= e($city_name) ?>" />
              <div class="place-card-overlay"><i class="fa-solid fa-arrow-right"></i></div>
            </div>
            <div class="place-card-body">
              <h5><?= e($city_name) ?></h5>
              <p><i class="fa-solid fa-house-chimney" style="color:var(--mainColor);font-size:10px"></i> <?= $city_count ?> Properties</p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ══ FEATURED PROPERTIES ══════════════════════════════════ -->
  <section class="section section-grey">
    <div class="container">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4">
        <div class="section-header reveal" style="margin-bottom:0;text-align:left">
          <h2>Featured Properties</h2>
          <div class="accent-line" style="margin-left:0"></div>
        </div>
      </div>
      <div class="row g-4">
        <?php foreach ($featured as $p): ?>
        <div class="col-lg-4 col-md-6 reveal">
          <div class="prop-card">
            <div class="prop-img-wrap">
              <img src="<?= e(prop_img_url($p['cover_image'] ?? '')) ?>" alt="<?= e($p['title']) ?>" />
              <span class="prop-badge<?= $p['status']==='For Rent' ? ' rent':'' ?>"><?= e($p['status']) ?></span>
              <?php if (is_logged_in()): ?>
              <form action="actions/toggle_favorite.php" method="POST" style="display:inline">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="property_id" value="<?= $p['id'] ?>">
                <button type="submit" class="prop-fav<?= is_favorited($p['id']) ? ' active' : '' ?>"><i class="<?= is_favorited($p['id']) ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i></button>
              </form>
              <?php else: ?>
              <button class="prop-fav" data-modal="signin-modal"><i class="fa-regular fa-heart"></i></button>
              <?php endif; ?>
            </div>
            <div class="prop-body">
              <div class="prop-price"><?= e(format_price((float)$p['price'], $p['status'])) ?></div>
              <div class="prop-title"><a href="property.php?id=<?= $p['id'] ?>"><?= e($p['title']) ?></a></div>
              <div class="prop-location"><i class="fa-solid fa-location-dot"></i><?= e($p['address'] . ', ' . $p['city']) ?></div>
              <hr class="prop-divider">
              <div class="prop-meta">
                <span><i class="fa-solid fa-bed"></i> <?= (int)$p['bedrooms'] ?> Beds</span>
                <span><i class="fa-solid fa-bath"></i> <?= (int)$p['bathrooms'] ?> Baths</span>
                <span><i class="fa-solid fa-ruler-combined"></i> <?= number_format((float)$p['area_sqft']) ?> sqft</span>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="text-center mt-5 reveal">
        <a href="listing.php" class="btn btn-primary btn-lg btn-pill">Browse All Listings <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </section>

  <!-- ══ STATS ════════════════════════════════════════════════ -->
  <section class="stats-section section-stats">
    <div class="container">
      <div class="row g-4">
        <div class="col-6 col-lg-3 reveal scale-in">
          <div class="stat-item">
            <div class="stat-number" data-target="<?= $stat_props ?>">0</div>
            <div class="stat-label">Properties Listed</div>
          </div>
        </div>
        <div class="col-6 col-lg-3 reveal scale-in">
          <div class="stat-item">
            <div class="stat-number" data-target="<?= $stat_users ?>">0</div>
            <div class="stat-label">Happy Clients</div>
          </div>
        </div>
        <div class="col-6 col-lg-3 reveal scale-in">
          <div class="stat-item">
            <div class="stat-number" data-target="52">0</div>
            <div class="stat-label">Awards Won</div>
          </div>
        </div>
        <div class="col-6 col-lg-3 reveal scale-in">
          <div class="stat-item">
            <div class="stat-number" data-target="18">0</div>
            <div class="stat-label">Years Experience</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ WHY CHOOSE US ════════════════════════════════════════ -->
  <section class="section section-grey">
    <div class="container">
      <div class="section-header reveal">
        <h2>Why Choose Us</h2>
        <div class="accent-line"></div>
        <p class="mt-2">We provide full service at every step</p>
      </div>
      <div class="row g-4 align-items-stretch">
        <div class="col-lg-3 col-md-6 reveal">
          <div class="feature-card">
            <div class="feature-card__icon"><img src="img/choose-us/icon-1.svg" alt="" /></div>
            <h4>Wide Range</h4>
            <p>Thousands of curated properties across every city, budget, and style.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 reveal">
          <div class="feature-card">
            <div class="feature-card__icon"><img src="img/choose-us/icon-2.svg" alt="" /></div>
            <h4>Trusted Experts</h4>
            <p>Verified agents with deep local knowledge and a proven track record.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 reveal">
          <div class="feature-card">
            <div class="feature-card__icon"><img src="img/choose-us/icon-3.svg" alt="" /></div>
            <h4>Easy Financing</h4>
            <p>Connect with top lenders and explore mortgage solutions in minutes.</p>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 reveal">
          <div class="feature-card">
            <div class="feature-card__icon"><img src="img/choose-us/icon-4.svg" alt="" /></div>
            <h4>Always Near You</h4>
            <p>Local agents in every major city, ready to guide you in person.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ══ AGENTS ════════════════════════════════════════════════ -->
  <section class="section">
    <div class="container">
      <div class="section-header reveal">
        <h2>Meet Our Agents</h2>
        <div class="accent-line"></div>
        <p class="mt-2">Dedicated professionals here to guide you</p>
      </div>
      <div class="row g-4">
        <?php 
        while ($ag = $agents->fetch_assoc()):
        ?>
        <div class="col-lg-3 col-md-6 reveal">
          <div class="agent-card">
            <img class="agent-img" src="<?= e(avatar_url($ag['avatar'] ?? '', $ag['id'])) ?>" alt="<?= e($ag['first_name']) ?>" />
            <div class="agent-name"><?= e($ag['first_name'] . ' ' . $ag['last_name']) ?></div>
            <div class="agent-role"><?= e(ucfirst($ag['role'])) ?></div>
            <div class="agent-socials">
              <a href="#" class="social-icon"><i class="fa-brands fa-twitter"></i></a>
              <a href="#" class="social-icon"><i class="fa-brands fa-instagram"></i></a>
              <a href="#" class="social-icon"><i class="fa-brands fa-linkedin-in"></i></a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
      <div class="text-center mt-5 reveal">
        <a href="agents.php" class="btn btn-dark btn-lg btn-pill">All Agents <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>
  </section>

  <!-- ══ TESTIMONIALS ══════════════════════════════════════════ -->
  <?php if (!empty($testimonials)): ?>
  <section class="section section-grey">
    <div class="container">
      <div class="section-header reveal">
        <h2>Client Testimonials</h2>
        <div class="accent-line"></div>
        <p class="mt-2">What our happy clients say</p>
      </div>
      <div class="testimonials-swiper-wrap reveal">
        <div class="swiper swiper-testimonials">
          <div class="swiper-wrapper">
            <?php foreach ($testimonials as $t): ?>
            <div class="swiper-slide">
              <div class="review-card">
                <div class="review-stars"><?= stars((int)$t['rating']) ?></div>
                <p class="review-text">"<?= e($t['body']) ?>"</p>
                <div class="d-flex align-items-center">
                  <img class="reviewer-img" src="<?= e(avatar_url('', crc32($t['name']))) ?>" alt="<?= e($t['name']) ?>" />
                  <div>
                    <div class="reviewer-name"><?= e($t['name']) ?></div>
                    <div class="reviewer-city"><?= e($t['prop_title']) ?></div>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="testimonials-arrows">
          <button class="testimonials-prev"><i class="fa-solid fa-arrow-left"></i></button>
          <button class="testimonials-next"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

<?php
$extra_js = '<script src="js/index.js"></script>';
include __DIR__ . '/includes/footer.php';
?>
