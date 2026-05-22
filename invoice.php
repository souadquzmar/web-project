<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('index.php');

$u = current_user();
if (!is_agent_or_admin()) {
    set_flash('error', 'Invoices are available for agents and admins only.');
    header('Location: dashboard.php');
    exit;
}
$db = get_db();
$invoices = $db->query("SELECT * FROM invoices WHERE user_id={$u['id']} ORDER BY issued_at DESC")->fetch_all(MYSQLI_ASSOC);

$page_title = 'Invoices';
$dash_mode = true;
$dash_active = 'invoices';
$extra_css = '<link rel="stylesheet" href="css/dashboard.css">';

include __DIR__ . '/includes/header.php';
?>

  <section class="section section-grey">
    <div class="container">
      <div class="dashboard-wrap">
        <?php include __DIR__ . '/includes/dash-sidebar.php'; ?>
        <div class="dash-main">
          <div class="dash-card">
            <div class="dash-card-title">Invoices</div>
            <div class="table-scroll">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>Invoice #</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Issued</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($invoices as $i): ?>
                  <tr>
                    <td>#<?= $i['id'] ?></td>
                    <td><?= e($i['description']) ?></td>
                    <td>$<?= number_format((float)$i['amount'], 2) ?></td>
                    <td><span class="status-badge status-<?= $i['status'] ?>"><?= e(ucfirst($i['status'])) ?></span></td>
                    <td><?= date('M j, Y', strtotime($i['issued_at'])) ?></td>
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
