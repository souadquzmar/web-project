<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('index.php');

$u = current_user();
$db = get_db();
$favs = $db->query("SELECT p.* FROM properties p JOIN favorites f ON f.property_id=p.id WHERE f.user_id={$u['id']} ORDER BY f.created_at DESC")->fetch_all(MYSQLI_ASSOC);

$page_title = 'Favorited Properties';
$dash_mode = true;
$dash_active = 'favorited';
$extra_css = '<link rel="stylesheet" href="css/dashboard.css">';

include __DIR__ . '/includes/header.php';
?>

  <section class="section section-grey">
    <div class="container">
      <div class="dashboard-wrap">
        <?php include __DIR__ . '/includes/dash-sidebar.php'; ?>
        <div class="dash-main">
          <?= render_flash() ?>
          <div class="dash-card">
            <div class="dash-card-title">Favorited Properties (<?= count($favs) ?>)</div>
            <?php if (!empty($favs)): ?>
            <div class="row g-4">
              <?php foreach ($favs as $p): ?>
              <div class="col-lg-6">
                <div class="prop-card">
                  <div class="prop-img-wrap">
                    <img src="<?= e(prop_img_url($p['cover_image'] ?? '')) ?>" alt="<?= e($p['title']) ?>" />
                    <span class="prop-badge<?= $p['status']==='For Rent'?' rent':'' ?>"><?= e($p['status']) ?></span>
                    <form action="actions/toggle_favorite.php" method="POST" style="display:inline">
                      <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                      <input type="hidden" name="property_id" value="<?= $p['id'] ?>">
                      <button type="submit" class="prop-fav active" title="Remove from favorites"><i class="fa-solid fa-heart"></i></button>
                    </form>
                  </div>
                  <div class="prop-body">
                    <div class="prop-price"><?= e(format_price((float)$p['price'], $p['status'])) ?></div>
                    <div class="prop-title"><a href="property.php?id=<?= $p['id'] ?>"><?= e($p['title']) ?></a></div>
                    <div class="prop-location"><i class="fa-solid fa-location-dot"></i> <?= e($p['city']) ?></div>
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
            <?php else: ?>
            <p style="color:var(--textLight);text-align:center;padding:40px 0">No favorited properties yet. Browse <a href="listing.php">listings</a> to save your favorites!</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php
$dash_footer = true;
$extra_js = '<script src="js/dashboard.js"></script>';
include __DIR__ . '/includes/footer.php';
?>
