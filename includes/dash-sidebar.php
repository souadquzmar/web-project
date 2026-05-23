<?php
// includes/dash-sidebar.php - Role-aware dashboard sidebar
// Expects $u (current user) and $dash_active (active page name)
if (!isset($dash_active)) $dash_active = '';
$role = $u['role'] ?? 'user';
?>
        <aside class="dash-sidebar">
          <div class="dash-profile">
            <img src="<?= e(avatar_url($u['avatar'] ?? '', $u['id'] ?? 0)) ?>" class="dash-avatar" alt="<?= e($u['first_name']) ?>" />
            <div class="dash-name"><?= e($u['first_name'] . ' ' . $u['last_name']) ?></div>
            <div class="dash-role">
              <?php if ($role === 'admin'): ?>
                Administrator
              <?php elseif ($role === 'agent'): ?>
                Real Estate Agent
              <?php else: ?>
                Real Estate Member
              <?php endif; ?>
            </div>
          </div>
          <nav class="dash-nav">
            <a href="dashboard.php" class="dash-nav-link<?= $dash_active==='dashboard'?' active':'' ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="user-profile.php" class="dash-nav-link<?= $dash_active==='profile'?' active':'' ?>"><i class="fa-solid fa-user"></i> Profile</a>

            <?php if ($role === 'admin' || $role === 'agent'): ?>
            <a href="my-listings.php" class="dash-nav-link<?= $dash_active==='listings'?' active':'' ?>"><i class="fa-solid fa-house"></i> My Properties</a>
            <?php endif; ?>

            <a href="favorited-listings.php" class="dash-nav-link<?= $dash_active==='favorited'?' active':'' ?>"><i class="fa-solid fa-heart"></i> Favorited</a>

            <?php if ($role === 'admin'): ?>
            <a href="admin-users.php" class="dash-nav-link<?= $dash_active==='users'?' active':'' ?>"><i class="fa-solid fa-users"></i> Manage Users</a>
            <a href="admin-properties.php" class="dash-nav-link<?= $dash_active==='all-properties'?' active':'' ?>"><i class="fa-solid fa-building"></i> All Properties</a>
            <a href="admin-messages.php" class="dash-nav-link<?= $dash_active==='all-messages'?' active':'' ?>"><i class="fa-solid fa-envelope-open-text"></i> All Messages</a>
            <?php endif; ?>

            <?php if ($role === 'admin' || $role === 'agent'): ?>
            <a href="my-messages.php" class="dash-nav-link<?= $dash_active==='messages'?' active':'' ?>"><i class="fa-solid fa-envelope"></i> Messages</a>
            <?php endif; ?>
            <a href="change-password.php" class="dash-nav-link<?= $dash_active==='password'?' active':'' ?>"><i class="fa-solid fa-lock"></i> Change Password</a>

            <?php if ($role === 'admin' || $role === 'agent'): ?>
            <a href="add-property.php" class="dash-nav-link<?= $dash_active==='add'?' active':'' ?>"><i class="fa-solid fa-plus-circle"></i> Add Property</a>
            <?php endif; ?>

            <div class="dash-nav-divider"></div>
            <a href="actions/logout.php" class="dash-nav-link logout"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
          </nav>
        </aside>
