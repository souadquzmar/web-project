<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('admin');

$u = current_user();
$db = get_db();
$props = $db->query(
    "SELECT p.*, u.first_name, u.last_name, u.role as owner_role
     FROM properties p JOIN users u ON u.id=p.user_id
     ORDER BY p.created_at DESC"
)->fetch_all(MYSQLI_ASSOC);

$page_title = 'All Properties';
$dash_mode = true;
$dash_active = 'all-properties';
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
            <div class="dash-card-title">All Properties (<?= count($props) ?>)</div>
            <div class="table-scroll">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>Property</th>
                    <th>Owner</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($props as $p): ?>
                  <tr>
                    <td>
                      <div class="prop-cell"><img src="<?= e(prop_img_url($p['cover_image'] ?? '')) ?>" class="prop-img-sm" /><span class="prop-name"><?= e($p['title']) ?></span></div>
                    </td>
                    <td><?= e($p['first_name'] . ' ' . $p['last_name']) ?> <small style="color:var(--textLight)">(<?= e($p['owner_role']) ?>)</small></td>
                    <td>$<?= number_format((float)$p['price']) ?></td>
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
                    <td><?= (int)$p['views'] ?></td>
                    <td>
                      <a href="property.php?id=<?= $p['id'] ?>" class="dash-action-btn" title="View"><i class="fa-solid fa-eye"></i></a>
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
