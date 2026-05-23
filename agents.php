<?php
require_once __DIR__ . '/includes/bootstrap.php';

$page_title = 'Our Agents';
$nav_active = '';
$extra_css = '<link rel="stylesheet" href="css/agents.css">';

$db = get_db();
$agents = $db->query("SELECT * FROM users WHERE role IN ('agent','admin') ORDER BY created_at")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/includes/header.php';
?>

  <div class="page-hero">
    <div class="container">
      <h1>Meet Our Agents</h1>
      <div class="breadcrumb-nav"><a href="index.php">Home</a><span class="sep">›</span><span class="current">Agents</span></div>
    </div>
  </div>

  <section class="section section-grey">
    <div class="container">
      <div class="row g-4">
        <?php 
        foreach ($agents as $a): 
        ?>
        <div class="col-lg-3 col-md-6">
          <div class="agent-card">
            <img class="agent-img" src="<?= e(avatar_url($a['avatar'] ?? '', $a['id'])) ?>" alt="<?= e($a['first_name']) ?>" />
            <div class="agent-name"><?= e($a['first_name'] . ' ' . $a['last_name']) ?></div>
            <div class="agent-role"><?= e(ucfirst($a['role'])) ?></div>
            <div class="agent-socials">
              <a href="#" class="social-icon"><i class="fa-brands fa-twitter"></i></a>
              <a href="#" class="social-icon"><i class="fa-brands fa-instagram"></i></a>
              <a href="#" class="social-icon"><i class="fa-brands fa-linkedin-in"></i></a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

<?php
$extra_js = '<script src="js/agents.js"></script>';
include __DIR__ . '/includes/footer.php';
?>
