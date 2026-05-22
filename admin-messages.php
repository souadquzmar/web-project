<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('admin');

$u = current_user();
$db = get_db();
$msgs = $db->query(
    "SELECT m.*, p.title as prop_title, u.first_name as owner_first, u.last_name as owner_last
     FROM messages m
     LEFT JOIN properties p ON p.id = m.property_id
     LEFT JOIN users u ON u.id = m.owner_id
     ORDER BY m.created_at DESC"
)->fetch_all(MYSQLI_ASSOC);

// Contact form submissions
$contacts = $db->query("SELECT * FROM contact_submissions ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$page_title = 'All Messages';
$dash_mode = true;
$dash_active = 'all-messages';
$extra_css = '<link rel="stylesheet" href="css/dashboard.css">';

include __DIR__ . '/includes/header.php';
?>

  <section class="section section-grey">
    <div class="container">
      <div class="dashboard-wrap">
        <?php include __DIR__ . '/includes/dash-sidebar.php'; ?>
        <div class="dash-main">
          <div class="dash-card">
            <div class="dash-card-title">All Messages (<?= count($msgs) ?>)</div>
            <div class="table-scroll">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>From</th>
                    <th>To (Agent)</th>
                    <th>Property</th>
                    <th>Message</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($msgs as $m): ?>
                  <tr>
                    <td><strong><?= e($m['sender_name']) ?></strong><br><small style="color:var(--textLight)"><?= e($m['sender_email']) ?></small></td>
                    <td><?= e(($m['owner_first'] ?? '') . ' ' . ($m['owner_last'] ?? '')) ?></td>
                    <td><?= e($m['prop_title'] ?? 'N/A') ?></td>
                    <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($m['body']) ?></td>
                    <td><?= e(time_ago($m['created_at'])) ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Contact Form Submissions -->
          <div class="dash-card">
            <div class="dash-card-title">Contact Form Submissions (<?= count($contacts) ?>)</div>
            <div class="table-scroll">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($contacts as $c): ?>
                  <tr>
                    <td><strong><?= e($c['name']) ?></strong></td>
                    <td><?= e($c['email']) ?></td>
                    <td><?= e($c['subject'] ?? 'General') ?></td>
                    <td style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($c['body']) ?></td>
                    <td><?= e(time_ago($c['created_at'])) ?></td>
                  </tr>
                  <?php endforeach; ?>
                  <?php if (empty($contacts)): ?>
                  <tr><td colspan="5" style="text-align:center;color:var(--textLight)">No contact submissions yet.</td></tr>
                  <?php endif; ?>
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
