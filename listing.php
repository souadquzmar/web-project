<?php
require_once __DIR__ . '/includes/bootstrap.php';

$page_title = 'Property Listings';
$nav_active = 'listing';
$extra_css = '<link rel="stylesheet" href="css/listing.css">';

$db = get_db();

// Get filters from query string
$status = $_GET['status'] ?? '';
$type = $_GET['type'] ?? '';
$city = $_GET['city'] ?? '';
$bedrooms = $_GET['bedrooms'] ?? '';
$bathrooms = $_GET['bathrooms'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$q = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

// Build WHERE clause
$where = ["listing_status='active'"];
$params = [];
$types = '';

if ($status) {
    $where[] = "status=?";
    $params[] = $status;
    $types .= 's';
}
if ($type) {
    $where[] = "type=?";
    $params[] = $type;
    $types .= 's';
}
if ($city) {
    $where[] = "city=?";
    $params[] = $city;
    $types .= 's';
}
if ($bedrooms) {
    $where[] = "bedrooms>=?";
    $params[] = (int)$bedrooms;
    $types .= 'i';
}
if ($bathrooms) {
    $where[] = "bathrooms>=?";
    $params[] = (int)$bathrooms;
    $types .= 'i';
}
if ($min_price) {
    $where[] = "price>=?";
    $params[] = (float)$min_price;
    $types .= 'd';
}
if ($max_price) {
    $where[] = "price<=?";
    $params[] = (float)$max_price;
    $types .= 'd';
}
if ($q) {
    $where[] = "(title LIKE ? OR description LIKE ? OR address LIKE ?)";
    $search = "%$q%";
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
    $types .= 'sss';
}

$where_clause = implode(' AND ', $where);

// Sort order
$order = "created_at DESC";
if ($sort === 'price_low') $order = "price ASC";
if ($sort === 'price_high') $order = "price DESC";
if ($sort === 'most_viewed') $order = "views DESC";

// Pagination
$per_page = 6;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

// Count total
$count_sql = "SELECT COUNT(*) FROM properties WHERE $where_clause";
if ($types) {
    $stmt = $db->prepare($count_sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_row()[0];
    $stmt->close();
} else {
    $total = $db->query($count_sql)->fetch_row()[0];
}

// Fetch properties with user data
$sql = "SELECT p.*, u.first_name, u.last_name, u.avatar
        FROM properties p
        JOIN users u ON u.id = p.user_id
        WHERE $where_clause
        ORDER BY $order
        LIMIT $per_page OFFSET $offset";

if ($types) {
    $stmt = $db->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $properties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $properties = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
}

$total_pages = ceil($total / $per_page);

include __DIR__ . '/includes/header.php';
?>

  <div class="page-hero">
    <div class="container">
      <h1>Find Your Perfect Property</h1>
      <div class="breadcrumb-nav"><a href="index.php">Home</a><span class="sep">›</span><span class="current">Listing</span></div>
    </div>
  </div>

  <section class="section section-grey">
    <div class="container">
      <!-- Search bar -->
      <form method="GET" action="listing.php" class="search-form mb-4" style="border-radius:var(--radius);box-shadow:var(--shadow-sm)">
        <input type="text" name="q" placeholder="Enter keyword..." value="<?= e($q) ?>" style="flex:2;min-width:160px" />
        <select name="type" style="flex:1;min-width:130px">
          <option value="">Property type</option>
          <option <?= $type==='House'?'selected':'' ?>>House</option>
          <option <?= $type==='Apartment'?'selected':'' ?>>Apartment</option>
          <option <?= $type==='Condo'?'selected':'' ?>>Condo</option>
          <option <?= $type==='Villa'?'selected':'' ?>>Villa</option>
        </select>
        <select name="city" style="flex:1;min-width:120px">
          <option value="">Location</option>
          <option <?= $city==='New York'?'selected':'' ?>>New York</option>
          <option <?= $city==='Los Angeles'?'selected':'' ?>>Los Angeles</option>
          <option <?= $city==='Miami'?'selected':'' ?>>Miami</option>
          <option <?= $city==='Chicago'?'selected':'' ?>>Chicago</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
      </form>

      <div class="listing-wrap">
        <!-- Sidebar -->
        <aside>
          <div class="sidebar-card">
            <div class="sidebar-title">Filter By</div>
            <form method="GET" action="listing.php">
              <div class="form-group"><label class="form-label">Property Status</label><select class="form-select" name="status">
                  <option value="">All</option>
                  <option <?= $status==='For Sale'?'selected':'' ?>>For Sale</option>
                  <option <?= $status==='For Rent'?'selected':'' ?>>For Rent</option>
                </select></div>
              <div class="form-group"><label class="form-label">Property Type</label><select class="form-select" name="type">
                  <option value="">All Types</option>
                  <option <?= $type==='House'?'selected':'' ?>>House</option>
                  <option <?= $type==='Apartment'?'selected':'' ?>>Apartment</option>
                  <option <?= $type==='Condo'?'selected':'' ?>>Condo</option>
                  <option <?= $type==='Villa'?'selected':'' ?>>Villa</option>
                </select></div>
              <div class="form-group"><label class="form-label">Bedrooms</label><select class="form-select" name="bedrooms">
                  <option value="">Any</option>
                  <option <?= $bedrooms=='1'?'selected':'' ?>>1+</option>
                  <option <?= $bedrooms=='2'?'selected':'' ?>>2+</option>
                  <option <?= $bedrooms=='3'?'selected':'' ?>>3+</option>
                  <option <?= $bedrooms=='4'?'selected':'' ?>>4+</option>
                </select></div>
              <div class="form-group"><label class="form-label">Bathrooms</label><select class="form-select" name="bathrooms">
                  <option value="">Any</option>
                  <option <?= $bathrooms=='1'?'selected':'' ?>>1+</option>
                  <option <?= $bathrooms=='2'?'selected':'' ?>>2+</option>
                  <option <?= $bathrooms=='3'?'selected':'' ?>>3+</option>
                </select></div>
              <div class="form-group"><label class="form-label">Min Price</label><input class="form-input" type="number" name="min_price" value="<?= e($min_price) ?>" placeholder="$0" /></div>
              <div class="form-group"><label class="form-label">Max Price</label><input class="form-input" type="number" name="max_price" value="<?= e($max_price) ?>" placeholder="Any" /></div>
              <button type="submit" class="btn btn-primary" style="width:100%">Apply Filters</button>
            </form>
          </div>
        </aside>

        <div>
          <div class="listing-header">
            <span class="results-count">Showing <strong><?= count($properties) ?></strong> of <?= $total ?> results</span>
            <div class="sort-wrap">
              <span style="font-size:13px;color:var(--textLight)">Sort by:</span>
              <select class="sort-select" onchange="window.location.search='?sort='+this.value+'&<?= http_build_query(array_filter($_GET, fn($k)=>$k!=='sort', ARRAY_FILTER_USE_KEY)) ?>'">
                <option value="newest" <?= $sort==='newest'?'selected':'' ?>>Newest First</option>
                <option value="price_low" <?= $sort==='price_low'?'selected':'' ?>>Price: Low to High</option>
                <option value="price_high" <?= $sort==='price_high'?'selected':'' ?>>Price: High to Low</option>
                <option value="most_viewed" <?= $sort==='most_viewed'?'selected':'' ?>>Most Viewed</option>
              </select>
            </div>
          </div>
          <div class="prop-list">
            <?php foreach ($properties as $p): ?>
            <div class="prop-card-h reveal">
              <div class="prop-card-h-img">
                <img src="<?= e(prop_img_url($p['cover_image'] ?? '')) ?>" alt="<?= e($p['title']) ?>" />
                <span class="prop-badge <?= $p['status']==='For Rent'?'rent':'' ?>" style="position:absolute;top:14px;left:14px"><?= e($p['status']) ?></span>
                <?php if (is_logged_in()): ?>
                <form action="actions/toggle_favorite.php" method="POST" style="display:inline">
                  <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                  <input type="hidden" name="property_id" value="<?= $p['id'] ?>">
                  <button type="submit" class="prop-fav<?= is_favorited($p['id']) ? ' active' : '' ?>" style="position:absolute;top:14px;right:14px"><i class="<?= is_favorited($p['id']) ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i></button>
                </form>
                <?php else: ?>
                <button class="prop-fav" style="position:absolute;top:14px;right:14px" data-modal="signin-modal"><i class="fa-regular fa-heart"></i></button>
                <?php endif; ?>
              </div>
              <div class="prop-card-h-body">
                <div>
                  <div class="prop-price"><?= e(format_price((float)$p['price'], $p['status'])) ?></div>
                  <div class="prop-title"><a href="property.php?id=<?= $p['id'] ?>"><?= e($p['title']) ?></a></div>
                  <div class="prop-location"><i class="fa-solid fa-location-dot"></i><?= e($p['address'] . ', ' . $p['city']) ?></div>
                  <div class="prop-meta" style="margin-top:12px">
                    <span><i class="fa-solid fa-bed"></i> <?= (int)$p['bedrooms'] ?> Beds</span>
                    <span><i class="fa-solid fa-bath"></i> <?= (int)$p['bathrooms'] ?> Baths</span>
                    <span><i class="fa-solid fa-ruler-combined"></i> <?= number_format((float)$p['area_sqft']) ?> sqft</span>
                  </div>
                </div>
                <div class="prop-card-h-footer">
                  <div class="agent-mini">
                    <img src="<?= e(avatar_url($p['avatar'])) ?>" alt="<?= e($p['first_name']) ?>" />
                    <span><?= e($p['first_name'] . ' ' . $p['last_name']) ?></span>
                  </div>
                  <div class="prop-date"><i class="fa-regular fa-calendar"></i><?= e(time_ago($p['created_at'])) ?></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <?php if ($total_pages > 1): ?>
          <div class="d-flex justify-content-center mt-5">
            <div class="pagination">
              <?php if ($page > 1): ?>
              <a href="?page=<?= $page-1 ?>&<?= http_build_query(array_filter($_GET, fn($k)=>$k!=='page', ARRAY_FILTER_USE_KEY)) ?>" class="page-btn"><i class="fa-solid fa-chevron-left"></i></a>
              <?php endif; ?>
              <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
              <a href="?page=<?= $i ?>&<?= http_build_query(array_filter($_GET, fn($k)=>$k!=='page', ARRAY_FILTER_USE_KEY)) ?>" class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
              <?php endfor; ?>
              <?php if ($page < $total_pages): ?>
              <a href="?page=<?= $page+1 ?>&<?= http_build_query(array_filter($_GET, fn($k)=>$k!=='page', ARRAY_FILTER_USE_KEY)) ?>" class="page-btn"><i class="fa-solid fa-chevron-right"></i></a>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

<?php
$extra_js = '<script src="js/listing.js"></script>';
include __DIR__ . '/includes/footer.php';
?>
