<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('index.php');

$u = current_user();
if ($u['role'] === 'user') {
    header('Location: dashboard.php');
    exit;
}

$db = get_db();
$msgs = $db->query(
    "SELECT m.*, p.title as prop_title
     FROM messages m
     LEFT JOIN properties p ON p.id = m.property_id
     WHERE m.owner_id={$u['id']}
     ORDER BY m.created_at DESC"
)->fetch_all(MYSQLI_ASSOC);

$page_title = 'My Messages';
$dash_mode = true;
$dash_active = 'messages';
$extra_css = '<link rel="stylesheet" href="css/dashboard.css">';

include __DIR__ . '/includes/header.php';
?>

  <section class="section section-grey">
    <div class="container">
      <div class="dashboard-wrap">
        <?php include __DIR__ . '/includes/dash-sidebar.php'; ?>
        <div class="dash-main">
          <div class="dash-card">
            <div class="dash-card-title">Messages (<?= count($msgs) ?>)</div>
            <?php foreach ($msgs as $m): ?>
            <div class="msg-item" style="padding:16px 0;border-bottom:1px solid var(--greyMid)">
              <img src="img/navbar/ts-1.jpg" class="msg-avatar" />
              <div style="flex:1">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                  <span class="msg-name"><?= e($m['sender_name']) ?></span>
                  <span class="msg-time"><?= e(time_ago($m['created_at'])) ?></span>
                </div>
                <div style="font-size:12px;color:var(--textLight);margin-bottom:6px">
                  <i class="fa-solid fa-envelope" style="margin-right:4px"></i><?= e($m['sender_email']) ?>
                  <?php if ($m['prop_title']): ?>
                  · <i class="fa-solid fa-house" style="margin:0 4px"></i><a href="property.php?id=<?= $m['property_id'] ?>"><?= e($m['prop_title']) ?></a>
                  <?php endif; ?>
                </div>
                <p class="msg-text" style="margin:0"><?= e($m['body']) ?></p>
              </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($msgs)): ?>
            <p style="color:var(--textLight);font-size:14px;padding:20px 0;text-align:center">No messages yet. Messages from property inquiries will appear here.</p>
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
