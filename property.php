<?php
require_once __DIR__ . '/includes/bootstrap.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: listing.php');
    exit;
}

$db = get_db();

// Fetch property with user data
$stmt = $db->prepare(
    "SELECT p.*, u.first_name, u.last_name, u.email, u.phone, u.avatar, u.about, u.role
     FROM properties p
     JOIN users u ON u.id = p.user_id
     WHERE p.id = ? LIMIT 1"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$prop = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$prop) {
    header('Location: listing.php');
    exit;
}

// Increment view count
$db->query("UPDATE properties SET views = views + 1 WHERE id = $id");

// Fetch property images
$imgs_res = $db->query("SELECT filename FROM property_images WHERE property_id = $id ORDER BY sort_order");
$images = $imgs_res->fetch_all(MYSQLI_ASSOC);
if (empty($images) && $prop['cover_image']) {
    $images[] = ['filename' => $prop['cover_image']];
}

// Fetch features
$feat_res = $db->query("SELECT feature FROM property_features WHERE property_id = $id");
$features = $feat_res->fetch_all(MYSQLI_ASSOC);

// Fetch reviews
$rev_res = $db->query("SELECT * FROM reviews WHERE property_id = $id ORDER BY created_at DESC");
$reviews = $rev_res->fetch_all(MYSQLI_ASSOC);

// Similar properties
$similar = $db->query(
    "SELECT * FROM properties WHERE type='{$prop['type']}' AND id != $id AND listing_status='active' LIMIT 3"
)->fetch_all(MYSQLI_ASSOC);

$page_title = $prop['title'];
$nav_active = 'listing';
$extra_css = '<link rel="stylesheet" href="css/property.css">';

include __DIR__ . '/includes/header.php';
?>

  <div class="page-hero">
    <div class="container">
      <h1><?= e($prop['title']) ?></h1>
      <div class="breadcrumb-nav">
        <a href="index.php">Home</a><span class="sep">›</span>
        <a href="listing.php">Listing</a><span class="sep">›</span>
        <span class="current">Property Detail</span>
      </div>
    </div>
  </div>

  <section class="section section-grey">
    <div class="container">

      <!-- ── GALLERY ──────────────────────────────────────────── -->
      <div class="prop-gallery">
        <div class="gallery-main">
          <img src="<?= e(prop_img_url($images[0]['filename'] ?? 'b-11.jpg')) ?>" alt="Property" id="main-gallery-img"/>
        </div>
        <div class="gallery-thumbs">
          <?php foreach (array_slice($images, 0, 5) as $img): ?>
          <div class="gallery-thumb" onclick="switchThumb(this,'<?= e(prop_img_url($img['filename'])) ?>')">
            <img src="<?= e(prop_img_url($img['filename'])) ?>" alt=""/>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- ── MAIN GRID ─────────────────────────────────────────── -->
      <div class="prop-detail-grid">
        <div>

          <!-- Info card -->
          <div class="prop-detail-card">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
              <div>
                <div class="prop-price" style="font-size:1.5rem"><?= e(format_price((float)$prop['price'], $prop['status'])) ?></div>
                <h2 style="font-size:1.4rem;font-weight:800;color:var(--dark);margin:6px 0 4px;letter-spacing:-0.02em">
                  <?= e($prop['title']) ?></h2>
                <div class="prop-location" style="font-size:13px"><i class="fa-solid fa-location-dot"></i> <?= e($prop['address'] . ', ' . $prop['city']) ?></div>
              </div>
              <div class="d-flex gap-2 flex-wrap">
                <span class="prop-badge<?= $prop['status']==='For Rent' ? ' rent':'' ?>"><?= e($prop['status']) ?></span>
                <?php if ($prop['featured']): ?>
                <span class="prop-badge featured-badge">Featured</span>
                <?php endif; ?>
              </div>
            </div>
            <div class="prop-features mb-4">
              <div class="prop-feature"><i class="fa-solid fa-hashtag"></i> ID: <?= $prop['id'] ?></div>
              <div class="prop-feature"><i class="fa-solid fa-bed"></i> <?= (int)$prop['bedrooms'] ?> Bedrooms</div>
              <div class="prop-feature"><i class="fa-solid fa-bath"></i> <?= (int)$prop['bathrooms'] ?> Bathrooms</div>
              <div class="prop-feature"><i class="fa-solid fa-ruler-combined"></i> <?= number_format((float)$prop['area_sqft']) ?> sqft</div>
              <?php if ($prop['garages']): ?>
              <div class="prop-feature"><i class="fa-solid fa-car"></i> <?= $prop['garages'] ?> Garages</div>
              <?php endif; ?>
              <?php if ($prop['year_built']): ?>
              <div class="prop-feature"><i class="fa-solid fa-calendar"></i> Built <?= $prop['year_built'] ?></div>
              <?php endif; ?>
              <div class="prop-feature"><i class="fa-solid fa-tag"></i> $<?= number_format((float)$prop['price'] / (float)$prop['area_sqft'], 0) ?> / sqft</div>
              <div class="prop-feature"><i class="fa-solid fa-house"></i> <?= e($prop['type']) ?></div>
            </div>

            <h3>Description</h3>
            <p style="font-size:14px;color:var(--textLight);line-height:1.8;margin-bottom:14px;white-space:pre-wrap"><?= e($prop['description']) ?></p>
          </div>

          <!-- Property Details table -->
          <div class="prop-detail-card">
            <h3>Property Details</h3>
            <div class="prop-details-table">
              <div class="prop-details-row"><span>Property ID</span><span><?= $prop['id'] ?></span></div>
              <div class="prop-details-row"><span>Price</span><span>$<?= number_format((float)$prop['price']) ?></span></div>
              <div class="prop-details-row"><span>Property Type</span><span><?= e($prop['type']) ?></span></div>
              <div class="prop-details-row"><span>Property Status</span><span><?= e($prop['status']) ?></span></div>
              <div class="prop-details-row"><span>Bedrooms</span><span><?= (int)$prop['bedrooms'] ?></span></div>
              <div class="prop-details-row"><span>Bathrooms</span><span><?= (int)$prop['bathrooms'] ?></span></div>
              <?php if ($prop['garages']): ?>
              <div class="prop-details-row"><span>Garages</span><span><?= $prop['garages'] ?></span></div>
              <?php endif; ?>
              <?php if ($prop['year_built']): ?>
              <div class="prop-details-row"><span>Year Built</span><span><?= $prop['year_built'] ?></span></div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Amenities -->
          <?php if (!empty($features)): ?>
          <div class="prop-detail-card">
            <h3>Amenities &amp; Features</h3>
            <div class="amenities-grid">
              <?php foreach ($features as $f): ?>
              <div class="amenity-item"><i class="fa-solid fa-check"></i> <?= e($f['feature']) ?></div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <!-- Location Map -->
          <?php if ($prop['latitude'] && $prop['longitude']): ?>
          <div class="prop-detail-card">
            <h3>Location</h3>
            <div class="prop-map-wrap">
              <iframe src="https://maps.google.com/maps?q=<?= $prop['latitude'] ?>,<?= $prop['longitude'] ?>&output=embed"
                      title="Map" frameborder="0" allowfullscreen></iframe>
            </div>
          </div>
          <?php endif; ?>

          <!-- Reviews -->
          <div class="prop-detail-card" id="reviews">
            <h3><?= count($reviews) ?> Reviews</h3>
            <?php if (!empty($reviews)): ?>
            <div class="review-list">
              <?php foreach ($reviews as $r): ?>
              <div class="review-item">
                <img src="img/clients/c-1.jpg" alt="<?= e($r['name']) ?>"/>
                <div class="review-item-body">
                  <div class="review-item-header">
                    <strong><?= e($r['name']) ?></strong>
                    <span class="review-item-stars"><?= stars((int)$r['rating']) ?></span>
                    <span class="review-item-date"><?= date('M j, Y', strtotime($r['created_at'])) ?></span>
                  </div>
                  <p><?= e($r['body']) ?></p>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color:var(--textLight);font-size:14px">No reviews yet. Be the first to review this property!</p>
            <?php endif; ?>
          </div>

          <!-- Add Review -->
          <div class="prop-detail-card">
            <h3>Add a Review</h3>
            <?= render_flash() ?>
            <form action="actions/submit_review.php" method="POST">
              <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
              <input type="hidden" name="property_id" value="<?= $id ?>">
              <p style="font-size:13px;color:var(--textLight);margin-bottom:16px">Your rating for this listing</p>
              <div class="star-rating mb-4" id="starRating">
                <i class="fa-regular fa-star" data-val="1"></i>
                <i class="fa-regular fa-star" data-val="2"></i>
                <i class="fa-regular fa-star" data-val="3"></i>
                <i class="fa-regular fa-star" data-val="4"></i>
                <i class="fa-regular fa-star" data-val="5"></i>
              </div>
              <input type="hidden" name="rating" id="rating-input" value="5">
              <div class="form-row mb-3">
                <div class="form-group"><label class="form-label">Name</label>
                  <input class="form-input" type="text" name="name" placeholder="Your name" required/></div>
                <div class="form-group"><label class="form-label">Email</label>
                  <input class="form-input" type="email" name="email" placeholder="Your email" required/></div>
              </div>
              <div class="form-group"><label class="form-label">Review</label>
                <textarea class="form-textarea" name="body" placeholder="Write your review..." required></textarea></div>
              <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
          </div>

        </div>

        <!-- ── SIDEBAR ───────────────────────────────────────────── -->
        <aside>
          <!-- Contact Agent -->
          <div class="contact-agent-card">
            <img class="agent-img" src="<?= e(avatar_url($prop['avatar'])) ?>" alt="Agent" style="width:70px;height:70px;margin-bottom:10px"/>
            <div class="agent-name"><?= e($prop['first_name'] . ' ' . $prop['last_name']) ?></div>
            <div class="agent-role" style="margin-bottom:16px"><?= e(ucfirst($prop['role'])) ?></div>
            <div class="d-flex gap-2 mb-3">
              <?php if ($prop['phone']): ?>
              <a href="tel:<?= e($prop['phone']) ?>" class="btn btn-primary btn-sm" style="flex:1;justify-content:center">
                <i class="fa-solid fa-phone"></i> Call
              </a>
              <?php endif; ?>
              <a href="mailto:<?= e($prop['email']) ?>" class="btn btn-outline btn-sm" style="flex:1;justify-content:center">
                <i class="fa-solid fa-envelope"></i> Email
              </a>
            </div>
            <form action="actions/send_message.php" method="POST">
              <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
              <input type="hidden" name="property_id" value="<?= $id ?>">
              <div class="form-group"><label class="form-label">Your Name</label>
                <input class="form-input" type="text" name="sender_name" placeholder="John Smith" required/></div>
              <div class="form-group"><label class="form-label">Email</label>
                <input class="form-input" type="email" name="sender_email" placeholder="you@example.com" required/></div>
              <div class="form-group"><label class="form-label">Phone</label>
                <input class="form-input" type="tel" name="sender_phone" placeholder="+1 234 567 890"/></div>
              <div class="form-group"><label class="form-label">Message</label>
                <textarea class="form-textarea" name="body" style="min-height:80px" placeholder="Hi, I'm interested in this property." required></textarea></div>
              <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Send Message</button>
            </form>
          </div>
        </aside>
      </div>

      <!-- Similar Properties -->
      <?php if (!empty($similar)): ?>
      <div class="prop-detail-card" style="margin-top:28px">
        <h3>Similar Properties</h3>
        <div class="row g-4">
          <?php foreach ($similar as $s): ?>
          <div class="col-lg-4 col-md-6">
            <div class="prop-card">
              <div class="prop-img-wrap">
                <img src="<?= e(prop_img_url($s['cover_image'] ?? '')) ?>" alt="<?= e($s['title']) ?>"/>
                <span class="prop-badge <?= $s['status']==='For Rent'?'rent':'' ?>"><?= e($s['status']) ?></span>
                <?php if (is_logged_in()): ?>
                <form action="actions/toggle_favorite.php" method="POST" style="display:inline">
                  <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                  <input type="hidden" name="property_id" value="<?= $s['id'] ?>">
                  <button type="submit" class="prop-fav<?= is_favorited($s['id']) ? ' active' : '' ?>"><i class="<?= is_favorited($s['id']) ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i></button>
                </form>
                <?php else: ?>
                <button class="prop-fav" data-modal="signin-modal"><i class="fa-regular fa-heart"></i></button>
                <?php endif; ?>
              </div>
              <div class="prop-body">
                <div class="prop-price"><?= e(format_price((float)$s['price'], $s['status'])) ?></div>
                <div class="prop-title"><a href="property.php?id=<?= $s['id'] ?>"><?= e($s['title']) ?></a></div>
                <div class="prop-location"><i class="fa-solid fa-location-dot"></i> <?= e($s['city']) ?></div>
                <hr class="prop-divider">
                <div class="prop-meta">
                  <span><i class="fa-solid fa-bed"></i> <?= (int)$s['bedrooms'] ?> Beds</span>
                  <span><i class="fa-solid fa-bath"></i> <?= (int)$s['bathrooms'] ?> Baths</span>
                  <span><i class="fa-solid fa-ruler-combined"></i> <?= number_format((float)$s['area_sqft']) ?> sqft</span>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </section>

<?php
$extra_js = '<script src="js/property.js"></script>';
include __DIR__ . '/includes/footer.php';
?>
