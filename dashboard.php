<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('index.php');

$u = current_user();
$db = get_db();
$role = $u['role'];

// ── ROLE-BASED STATS ──────────────────────────────────────

if ($role === 'admin') {
    // Admin sees platform-wide stats
    $stat1_num   = $db->query("SELECT COUNT(*) FROM properties")->fetch_row()[0];
    $stat1_label = 'Total Properties';
    $stat2_num   = $db->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
    $stat2_label = 'Registered Users';
    $stat3_num   = $db->query("SELECT COUNT(*) FROM messages")->fetch_row()[0];
    $stat3_label = 'Total Messages';
    $stat4_num   = $db->query("SELECT COUNT(*) FROM reviews")->fetch_row()[0];
    $stat4_label = 'Total Reviews';

    // Admin sees ALL recent listings
    $my_listings = $db->query(
        "SELECT p.*, u.first_name, u.last_name FROM properties p JOIN users u ON u.id=p.user_id ORDER BY p.created_at DESC LIMIT 6"
    )->fetch_all(MYSQLI_ASSOC);

    // Admin sees ALL messages
    $messages = $db->query(
        "SELECT m.*, p.title as prop_title FROM messages m
         LEFT JOIN properties p ON p.id = m.property_id
         ORDER BY m.created_at DESC LIMIT 5"
    )->fetch_all(MYSQLI_ASSOC);

    // Admin sees ALL reviews
    $reviews = $db->query(
        "SELECT r.*, p.title as prop_title, ru.avatar as reviewer_avatar
         FROM reviews r
         JOIN properties p ON p.id = r.property_id
         LEFT JOIN users ru ON ru.id = r.user_id
         ORDER BY r.created_at DESC LIMIT 5"
    )->fetch_all(MYSQLI_ASSOC);

} elseif ($role === 'agent') {
    // Agent sees their own performance
    $stat1_num   = $db->query("SELECT COUNT(*) FROM properties WHERE user_id={$u['id']}")->fetch_row()[0];
    $stat1_label = 'My Listings';
    $stat2_num   = $db->query("SELECT COUNT(*) FROM reviews WHERE property_id IN (SELECT id FROM properties WHERE user_id={$u['id']})")->fetch_row()[0];
    $stat2_label = 'My Reviews';
    $stat3_num   = $db->query("SELECT COUNT(*) FROM messages WHERE owner_id={$u['id']}")->fetch_row()[0];
    $stat3_label = 'Inquiries';
    $stat4_num   = $db->query("SELECT COALESCE(SUM(views),0) FROM properties WHERE user_id={$u['id']}")->fetch_row()[0];
    $stat4_label = 'Total Views';

    // Agent sees their own listings
    $my_listings = $db->query(
        "SELECT * FROM properties WHERE user_id={$u['id']} ORDER BY created_at DESC LIMIT 4"
    )->fetch_all(MYSQLI_ASSOC);

    // Agent sees messages sent to them
    $messages = $db->query(
        "SELECT m.*, p.title as prop_title FROM messages m
         LEFT JOIN properties p ON p.id = m.property_id
         WHERE m.owner_id={$u['id']} ORDER BY m.created_at DESC LIMIT 3"
    )->fetch_all(MYSQLI_ASSOC);

    // Agent sees reviews on their properties
    $reviews = $db->query(
        "SELECT r.*, p.title as prop_title, ru.avatar as reviewer_avatar
         FROM reviews r
         JOIN properties p ON p.id = r.property_id
         LEFT JOIN users ru ON ru.id = r.user_id
         WHERE p.user_id={$u['id']} ORDER BY r.created_at DESC LIMIT 3"
    )->fetch_all(MYSQLI_ASSOC);

} else {
    // Regular user sees their activity
    $stat1_num   = $db->query("SELECT COUNT(*) FROM favorites WHERE user_id={$u['id']}")->fetch_row()[0];
    $stat1_label = 'Saved Properties';
    $stat2_num   = $db->query("SELECT COUNT(*) FROM reviews WHERE user_id={$u['id']}")->fetch_row()[0];
    $stat2_label = 'My Reviews';
    $stat3_num   = $db->query("SELECT COUNT(*) FROM messages WHERE sender_email='{$u['email']}'")->fetch_row()[0];
    $stat3_label = 'Messages Sent';
    $stat4_num   = $db->query("SELECT COUNT(*) FROM favorites WHERE user_id={$u['id']}")->fetch_row()[0];
    $stat4_label = 'Bookmarked';

    // User sees no listings (they don't list properties)
    $my_listings = [];

    // User sees messages they sent
    $messages = $db->query(
        "SELECT m.*, p.title as prop_title FROM messages m
         LEFT JOIN properties p ON p.id = m.property_id
         WHERE m.sender_email='{$u['email']}' ORDER BY m.created_at DESC LIMIT 3"
    )->fetch_all(MYSQLI_ASSOC);

    // User sees their own reviews
    $reviews = $db->query(
        "SELECT r.*, p.title as prop_title FROM reviews r
         JOIN properties p ON p.id = r.property_id
         WHERE r.user_id={$u['id']} ORDER BY r.created_at DESC LIMIT 3"
    )->fetch_all(MYSQLI_ASSOC);
}

$page_title = 'Dashboard';
$dash_mode = true;
$dash_active = 'dashboard';
$extra_css = '<link rel="stylesheet" href="css/dashboard.css"><script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>';

include __DIR__ . '/includes/header.php';
?>

  <section class="section section-grey">
    <div class="container">
      <div class="dashboard-wrap">
        <?php include __DIR__ . '/includes/dash-sidebar.php'; ?>
        <div class="dash-main">
          <?= render_flash() ?>

          <?php if ($role === 'admin'): ?>
          <div class="alert alert-info" style="border-radius:var(--radius);margin-bottom:20px">
            <i class="fa-solid fa-shield-halved"></i> <strong>Admin Panel</strong> — You have full access to manage all users, properties, and messages.
          </div>
          <?php endif; ?>

          <?php if ($role === 'admin' || $role === 'agent'): ?>
          <!-- Stats (agents & admins) -->
          <div class="dash-stats">
            <div class="dash-stat-card">
              <div class="dash-stat-icon"><i class="fa-solid fa-house"></i></div>
              <div>
                <div class="dash-stat-num"><?= $stat1_num ?></div>
                <div class="dash-stat-label"><?= e($stat1_label) ?></div>
              </div>
            </div>
            <div class="dash-stat-card">
              <div class="dash-stat-icon dash-stat-icon--blue"><i class="fa-solid fa-star"></i></div>
              <div>
                <div class="dash-stat-num"><?= $stat2_num ?></div>
                <div class="dash-stat-label"><?= e($stat2_label) ?></div>
              </div>
            </div>
            <div class="dash-stat-card">
              <div class="dash-stat-icon dash-stat-icon--green"><i class="fa-solid fa-envelope"></i></div>
              <div>
                <div class="dash-stat-num"><?= $stat3_num ?></div>
                <div class="dash-stat-label"><?= e($stat3_label) ?></div>
              </div>
            </div>
            <div class="dash-stat-card">
              <div class="dash-stat-icon dash-stat-icon--amber"><i class="fa-solid fa-bookmark"></i></div>
              <div>
                <div class="dash-stat-num"><?= $stat4_num ?></div>
                <div class="dash-stat-label"><?= e($stat4_label) ?></div>
              </div>
            </div>
          </div>

          <!-- Charts Row (agents & admins) -->
          <div class="row g-4 py-3">
            <div class="col-lg-8">
              <div class="dash-card dash-card--no-mb">
                <div class="dash-card-title">Property Views &amp; Inquiries</div>
                <canvas id="chartViews" height="120"></canvas>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="dash-card dash-card--chart-donut">
                <div class="dash-card-title">Listing Status</div>
                <canvas id="chartStatus" height="200"></canvas>
                <div class="chart-legend">
                  <span class="chart-legend-item"><span class="chart-legend-dot chart-legend-dot--green"></span>Active</span>
                  <span class="chart-legend-item"><span class="chart-legend-dot chart-legend-dot--amber"></span>Pending</span>
                  <span class="chart-legend-item"><span class="chart-legend-dot chart-legend-dot--red"></span>Inactive</span>
                </div>
              </div>
            </div>
          </div>
          <?php else: ?>
          <!-- User welcome -->
          <div class="dash-card">
            <h3 style="font-weight:700;margin-bottom:8px">Welcome back, <?= e($u['first_name']) ?>!</h3>
            <p style="color:var(--textLight);line-height:1.7">From here you can manage your favorites, update your profile, and keep track of your property inquiries. Use the sidebar to navigate.</p>
          </div>
          <?php endif; ?>

          <!-- Listings Table (Admin & Agent only) -->
          <?php if ($role === 'admin' || $role === 'agent'): ?>
          <div class="dash-card">
            <div class="dash-card-title"><?= $role === 'admin' ? 'All Recent Listings' : 'My Listings' ?></div>
            <div class="table-scroll">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>Property</th>
                    <?php if ($role === 'admin'): ?><th>Owner</th><?php endif; ?>
                    <th>Date</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($my_listings as $p): ?>
                  <tr>
                    <td>
                      <div class="prop-cell"><img src="<?= e(prop_img_url($p['cover_image'] ?? '')) ?>" class="prop-img-sm" /><span class="prop-name"><?= e($p['title']) ?></span></div>
                    </td>
                    <?php if ($role === 'admin'): ?>
                    <td><?= e(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')) ?></td>
                    <?php endif; ?>
                    <td><?= date('d M Y', strtotime($p['created_at'])) ?></td>
                    <td>$<?= number_format((float)$p['price']) ?></td>
                    <td>
                      <form action="actions/change_status.php" method="POST" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="property_id" value="<?= $p['id'] ?>">
                        <select name="listing_status" class="status-dropdown status-dropdown--<?= $p['listing_status'] ?>" onchange="this.form.submit()">
                          <option value="active" <?= $p['listing_status']==='active'?'selected':'' ?>>Active</option>
                          <option value="pending" <?= $p['listing_status']==='pending'?'selected':'' ?>>Pending</option>
                          <option value="inactive" <?= $p['listing_status']==='inactive'?'selected':'' ?>>Inactive</option>
                        </select>
                      </form>
                    </td>
                    <td>
                      <a href="property.php?id=<?= $p['id'] ?>" class="dash-action-btn" title="View"><i class="fa-solid fa-eye"></i></a>
                      <?php if ($role === 'admin' || $p['user_id'] == $u['id']): ?>
                      <form action="actions/delete_property.php" method="POST" style="display:inline" onsubmit="return confirm('Delete this property?')">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" class="dash-action-btn danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                      </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <?php else: ?>
          <!-- Regular User: Favorited Properties -->
          <?php
          $user_favs = $db->query("SELECT p.* FROM properties p JOIN favorites f ON f.property_id=p.id WHERE f.user_id={$u['id']} ORDER BY f.created_at DESC LIMIT 4")->fetch_all(MYSQLI_ASSOC);
          ?>
          <div class="dash-card">
            <div class="dash-card-title">Your Saved Properties</div>
            <?php if (!empty($user_favs)): ?>
            <div class="row g-3">
              <?php foreach ($user_favs as $p): ?>
              <div class="col-lg-6">
                <div class="prop-card">
                  <div class="prop-img-wrap">
                    <img src="<?= e(prop_img_url($p['cover_image'] ?? '')) ?>" alt="<?= e($p['title']) ?>" />
                    <span class="prop-badge<?= $p['status']==='For Rent'?' rent':'' ?>"><?= e($p['status']) ?></span>
                  </div>
                  <div class="prop-body">
                    <div class="prop-price"><?= e(format_price((float)$p['price'], $p['status'])) ?></div>
                    <div class="prop-title"><a href="property.php?id=<?= $p['id'] ?>"><?= e($p['title']) ?></a></div>
                    <div class="prop-location"><i class="fa-solid fa-location-dot"></i> <?= e($p['city']) ?></div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color:var(--textLight);font-size:14px;padding:10px 0">No saved properties yet. <a href="listing.php">Browse listings</a> to save your favorites!</p>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <!-- Messages + Reviews side by side -->
          <div class="row g-4">
            <div class="<?= $role === 'user' ? 'col-12' : 'col-lg-6' ?>">
              <div class="dash-card dash-card--no-mb">
                <div class="dash-card-title"><?= $role === 'admin' ? 'All Recent Messages' : ($role === 'agent' ? 'Inquiries' : 'My Messages') ?></div>
                <div class="msg-list">
                  <?php foreach ($messages as $m): ?>
                  <div class="msg-item">
                    <img src="<?= e(avatar_url('', crc32($m['sender_email'] ?? ''))) ?>" class="msg-avatar" />
                    <div>
                      <div><span class="msg-name"><?= e($m['sender_name']) ?></span><span class="msg-time"><?= e(time_ago($m['created_at'])) ?></span></div>
                      <p class="msg-text"><?= e(mb_substr($m['body'], 0, 80)) ?>...</p>
                    </div>
                  </div>
                  <?php endforeach; ?>
                  <?php if (empty($messages)): ?>
                  <p style="color:var(--textLight);font-size:14px;padding:10px 0">No messages yet.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php if ($role === 'admin' || $role === 'agent'): ?>
            <div class="col-lg-6">
              <div class="dash-card dash-card--no-mb">
                <div class="dash-card-title"><?= $role === 'admin' ? 'All Recent Reviews' : 'Recent Reviews' ?></div>
                <div class="review-list-dash">
                  <?php foreach ($reviews as $r): ?>
                  <div class="review-item-dash">
                    <img src="<?= e(avatar_url($r['reviewer_avatar'] ?? '', $r['user_id'] ?? crc32($r['name'] ?? ''))) ?>" />
                    <div>
                      <div class="review-prop-name"><?= e($r['prop_title']) ?></div>
                      <div class="review-by">by <?= e($r['name']) ?> · <span class="review-time"><?= e(time_ago($r['created_at'])) ?></span></div>
                      <div class="review-stars-sm"><?= stars((int)$r['rating']) ?></div>
                      <p class="review-text-sm"><?= e(mb_substr($r['body'], 0, 60)) ?>...</p>
                    </div>
                  </div>
                  <?php endforeach; ?>
                  <?php if (empty($reviews)): ?>
                  <p style="color:var(--textLight);font-size:14px;padding:10px 0">No reviews yet.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

<?php
$dash_footer = true;

// ── CHART DATA FROM DATABASE ──────────────────────────────
// Monthly views: aggregate property views by month for the current year
$views_data = [];
$inquiries_data = [];
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

if ($role === 'admin') {
    // Admin: all properties
    $views_q = $db->query("SELECT MONTH(created_at) as m, COUNT(*) as cnt FROM properties GROUP BY MONTH(created_at)");
    $msg_q   = $db->query("SELECT MONTH(created_at) as m, COUNT(*) as cnt FROM messages GROUP BY MONTH(created_at)");
} elseif ($role === 'agent') {
    // Agent: their properties
    $views_q = $db->query("SELECT MONTH(created_at) as m, SUM(views) as cnt FROM properties WHERE user_id={$u['id']} GROUP BY MONTH(created_at)");
    $msg_q   = $db->query("SELECT MONTH(created_at) as m, COUNT(*) as cnt FROM messages WHERE owner_id={$u['id']} GROUP BY MONTH(created_at)");
} else {
    // User: their favorites and reviews
    $views_q = $db->query("SELECT MONTH(created_at) as m, COUNT(*) as cnt FROM favorites WHERE user_id={$u['id']} GROUP BY MONTH(created_at)");
    $msg_q   = $db->query("SELECT MONTH(created_at) as m, COUNT(*) as cnt FROM reviews WHERE user_id={$u['id']} GROUP BY MONTH(created_at)");
}

$views_by_month = [];
$msgs_by_month = [];
if ($views_q) { while ($r = $views_q->fetch_assoc()) $views_by_month[(int)$r['m']] = (int)$r['cnt']; }
if ($msg_q)   { while ($r = $msg_q->fetch_assoc())   $msgs_by_month[(int)$r['m']]  = (int)$r['cnt']; }

for ($i = 1; $i <= 12; $i++) {
    $views_data[]     = $views_by_month[$i] ?? 0;
    $inquiries_data[] = $msgs_by_month[$i]  ?? 0;
}

// Donut chart: listing status counts
if ($role === 'admin') {
    $active_cnt   = (int)$db->query("SELECT COUNT(*) FROM properties WHERE listing_status='active'")->fetch_row()[0];
    $pending_cnt  = (int)$db->query("SELECT COUNT(*) FROM properties WHERE listing_status='pending'")->fetch_row()[0];
    $inactive_cnt = (int)$db->query("SELECT COUNT(*) FROM properties WHERE listing_status='inactive'")->fetch_row()[0];
} elseif ($role === 'agent') {
    $active_cnt   = (int)$db->query("SELECT COUNT(*) FROM properties WHERE user_id={$u['id']} AND listing_status='active'")->fetch_row()[0];
    $pending_cnt  = (int)$db->query("SELECT COUNT(*) FROM properties WHERE user_id={$u['id']} AND listing_status='pending'")->fetch_row()[0];
    $inactive_cnt = (int)$db->query("SELECT COUNT(*) FROM properties WHERE user_id={$u['id']} AND listing_status='inactive'")->fetch_row()[0];
} else {
    // User: show favorites / reviews / messages as donut
    $active_cnt   = (int)$db->query("SELECT COUNT(*) FROM favorites WHERE user_id={$u['id']}")->fetch_row()[0];
    $pending_cnt  = (int)$db->query("SELECT COUNT(*) FROM reviews WHERE user_id={$u['id']}")->fetch_row()[0];
    $inactive_cnt = (int)$db->query("SELECT COUNT(*) FROM messages WHERE sender_email='{$u['email']}'")->fetch_row()[0];
}

$chart_label1 = ($role === 'user') ? 'Favorites' : 'Views';
$chart_label2 = ($role === 'user') ? 'Reviews' : 'Inquiries';
$donut_labels = ($role === 'user') ? ['Favorites','Reviews','Messages'] : ['Active','Pending','Inactive'];
?>
<script>
  window.DASH_CHART_DATA = {
    months: <?= json_encode($months) ?>,
    views: <?= json_encode($views_data) ?>,
    inquiries: <?= json_encode($inquiries_data) ?>,
    viewsLabel: <?= json_encode($chart_label1) ?>,
    inquiriesLabel: <?= json_encode($chart_label2) ?>,
    donutLabels: <?= json_encode($donut_labels) ?>,
    donutData: <?= json_encode([$active_cnt, $pending_cnt, $inactive_cnt]) ?>
  };
</script>
<?php
$extra_js = '<script src="js/dashboard.js"></script>';
include __DIR__ . '/includes/footer.php';
?>
