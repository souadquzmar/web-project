<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('index.php');

$u = current_user();
$db = get_db();
$props = $db->query("SELECT * FROM properties WHERE user_id={$u['id']} ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$page_title = 'My Properties';
$dash_mode = true;
$dash_active = 'listings';
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
            <div class="dash-card-header">
              <div class="dash-card-title">My Properties</div>
            </div>
            <div class="table-scroll">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>Property</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($props as $p): ?>
                  <tr>
                    <td>
                      <div class="prop-cell-inner"><img src="<?= e(prop_img_url($p['cover_image'] ?? '')) ?>" class="prop-img-sm" />
                        <div>
                          <div class="prop-name"><?= e($p['title']) ?></div>
                          <div class="prop-location-sm"><?= e($p['city']) ?></div>
                        </div>
                      </div>
                    </td>
                    <td><span class="prop-badge<?= $p['status']==='For Rent'?' rent':'' ?>"><?= e($p['status']) ?></span></td>
                    <td class="prop-price-cell">$<?= number_format((float)$p['price']) ?></td>
                    <td>
                      <form action="actions/change_status.php" method="POST" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="property_id" value="<?= $p['id'] ?>">
                        <select name="listing_status" class="form-select" style="width:auto;min-width:110px;padding:5px 10px;font-size:12px" onchange="this.form.submit()">
                          <option value="active" <?= $p['listing_status']==='active'?'selected':'' ?>>Active</option>
                          <option value="pending" <?= $p['listing_status']==='pending'?'selected':'' ?>>Pending</option>
                          <option value="inactive" <?= $p['listing_status']==='inactive'?'selected':'' ?>>Inactive</option>
                        </select>
                      </form>
                    </td>
                    <td><a href="property.php?id=<?= $p['id'] ?>" class="dash-action-btn" title="View"><i class="fa-solid fa-eye"></i></a>
                      <form action="actions/delete_property.php" method="POST" style="display:inline" onsubmit="return confirm('Delete this property?')">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" class="dash-action-btn danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
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
