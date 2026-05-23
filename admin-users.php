<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('admin');

$u = current_user();
$db = get_db();

// Handle role change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    verify_csrf();
    $target_id = (int)$_POST['user_id'];
    $new_role = $_POST['new_role'];
    if (in_array($new_role, ['user','agent','admin']) && $target_id !== $u['id']) {
        $stmt = $db->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->bind_param('si', $new_role, $target_id);
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'User role updated.');
    }
    header('Location: admin-users.php');
    exit;
}

// Handle user delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    verify_csrf();
    $target_id = (int)$_POST['user_id'];
    if ($target_id !== $u['id']) {
        $db->query("DELETE FROM users WHERE id=$target_id");
        set_flash('success', 'User deleted.');
    }
    header('Location: admin-users.php');
    exit;
}

$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$page_title = 'Manage Users';
$dash_mode = true;
$dash_active = 'users';
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
            <div class="dash-card-title">All Users (<?= count($users) ?>)</div>
            <div class="table-scroll">
              <table class="dash-table">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($users as $row): ?>
                  <tr>
                    <td>
                      <div class="prop-cell"><img src="<?= e(avatar_url($row['avatar'] ?? '', $row['id'] ?? 0)) ?>" class="prop-img-sm" style="border-radius:50%" /><span class="prop-name"><?= e($row['first_name'] . ' ' . $row['last_name']) ?></span></div>
                    </td>
                    <td><?= e($row['email']) ?></td>
                    <td>
                      <?php if ($row['id'] != $u['id']): ?>
                      <form action="admin-users.php" method="POST" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="change_role" value="1">
                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                        <select name="new_role" class="form-select" style="width:auto;display:inline-block;min-width:100px" onchange="this.form.submit()">
                          <option value="user" <?= $row['role']==='user'?'selected':'' ?>>User</option>
                          <option value="agent" <?= $row['role']==='agent'?'selected':'' ?>>Agent</option>
                          <option value="admin" <?= $row['role']==='admin'?'selected':'' ?>>Admin</option>
                        </select>
                      </form>
                      <?php else: ?>
                      <span class="status-badge status-active"><?= e(ucfirst($row['role'])) ?> (You)</span>
                      <?php endif; ?>
                    </td>
                    <td><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                    <td>
                      <?php if ($row['id'] != $u['id']): ?>
                      <form action="admin-users.php" method="POST" style="display:inline" onsubmit="return confirm('Delete this user? This cannot be undone.')">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="delete_user" value="1">
                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="dash-action-btn danger" title="Delete User"><i class="fa-solid fa-trash"></i></button>
                      </form>
                      <?php else: ?>
                      <span style="color:var(--textLight);font-size:12px">—</span>
                      <?php endif; ?>
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
