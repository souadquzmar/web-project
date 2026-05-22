<?php
// blog.php - Using EXACT original HTML structure
require_once __DIR__ . '/includes/bootstrap.php';

$page_title = 'Blog';
$nav_active = 'blog';
$extra_css = '<link rel="stylesheet" href="css/blog.css">';

// Fetch blog posts from database
$db = get_db();
$posts = $db->query("SELECT b.*, u.first_name, u.last_name FROM blog_posts b LEFT JOIN users u ON u.id=b.author_id WHERE b.published=1 ORDER BY b.created_at DESC")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/includes/header.php';
?>

  <div class="page-hero">
    <div class="container">
      <h1>News & Insights</h1>
      <div class="breadcrumb-nav"><a href="index.php">Home</a><span class="sep">›</span><span
          class="current">Blog</span></div>
    </div>
  </div>
  <section class="section section-grey">
    <div class="container">
      <div class="section-header reveal">
        <h2>Latest Articles</h2>
        <div class="accent-line"></div>
        <p class="mt-2">Real estate tips, market insights, and buying guides</p>
      </div>
      <div class="blog-grid">
        <?php foreach ($posts as $p): ?>
        <div class="reveal">
          <div class="blog-card">
            <div class="blog-card-img"><img src="img/blogs/<?= e($p['cover_image'] ?? 'b-1.jpg') ?>"
                alt="<?= e($p['title']) ?>" /></div>
            <div class="blog-card-body">
              <div class="blog-card-meta"><i class="fa-solid fa-tag"></i> <?= e($p['category'] ?? 'Real Estate') ?></div>
              <div class="blog-card-title"><a href="blog-details.php?id=<?= $p['id'] ?>"><?= e($p['title']) ?></a></div>
              <div class="blog-card-excerpt"><?= e($p['excerpt'] ?? substr(strip_tags($p['body']), 0, 120)) ?>...</div>
              <a href="blog-details.php?id=<?= $p['id'] ?>" class="blog-card-link">Read More <i class="fa-solid fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="d-flex justify-content-center mt-5">
        <div class="pagination">
          <button class="page-btn"><i class="fa-solid fa-chevron-left"></i></button>
          <button class="page-btn active">1</button>
          <button class="page-btn">2</button>
          <button class="page-btn">3</button>
          <button class="page-btn"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
      </div>
    </div>
  </section>

<?php
$extra_js = '<script src="js/blog.js"></script>';
include __DIR__ . '/includes/footer.php';
?>
