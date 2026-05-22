<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('index.php');

$u = current_user();
if (!is_agent_or_admin()) {
    set_flash('error', 'Payment methods are available for agents and admins only.');
    header('Location: dashboard.php');
    exit;
}
$db = get_db();
$cards = $db->query("SELECT * FROM payment_methods WHERE user_id={$u['id']} ORDER BY is_default DESC, created_at DESC")->fetch_all(MYSQLI_ASSOC);

$page_title = 'Payment Methods';
$dash_mode = true;
$dash_active = 'payments';
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
            <div class="dash-card-title">Saved Payment Methods</div>
            <div class="payment-cards">
              <?php foreach ($cards as $c): ?>
              <div class="payment-card<?= $c['is_default'] ? ' selected' : '' ?>">
                <div class="payment-card-check"><i class="fa-solid fa-check"></i></div>
                <div class="payment-card-logo">💳</div>
                <div class="payment-card-name"><?= e($c['card_type']) ?></div>
                <div class="payment-card-num">**** **** **** <?= e($c['last_four']) ?></div>
              </div>
              <?php endforeach; ?>
            </div>
            <div class="dash-card-title dash-card-title--mt">Add New Payment Method</div>
            <form action="actions/save_payment.php" method="POST">
              <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
              <div class="form-group"><label class="form-label">Cardholder Name</label><input class="form-input" type="text" name="cardholder" placeholder="Mary Smith" required /></div>
              <div class="form-group"><label class="form-label">Card Number</label><input class="form-input" type="text" name="card_number" placeholder="1234 5678 9012 3456" required /></div>
              <div class="form-row">
                <div class="form-group"><label class="form-label">Expiry Date</label><input class="form-input" type="text" name="expiry" placeholder="MM / YY" required /></div>
                <div class="form-group"><label class="form-label">CVV</label><input class="form-input" type="text" name="cvv" placeholder="123" /></div>
              </div>
              <button type="submit" class="btn btn-primary" id="addCardBtn"><i class="fa-solid fa-plus"></i> Add Card</button>
            </form>
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
