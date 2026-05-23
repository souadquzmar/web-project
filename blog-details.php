<?php
require_once __DIR__ . '/includes/bootstrap.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: blog.php'); exit; }

$db = get_db();
$stmt = $db->prepare("SELECT b.*, u.first_name, u.last_name FROM blog_posts b LEFT JOIN users u ON u.id=b.author_id WHERE b.id=? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) { header('Location: blog.php'); exit; }
$db->query("UPDATE blog_posts SET views=views+1 WHERE id=$id");

// Fetch comments for this blog post with user avatars
$comments = $db->query("SELECT c.*, u.avatar as commenter_avatar FROM blog_comments c LEFT JOIN users u ON u.id = c.user_id WHERE c.blog_id=$id ORDER BY c.created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Recent posts for sidebar
$recent = $db->query("SELECT * FROM blog_posts WHERE published=1 AND id != $id ORDER BY created_at DESC LIMIT 4")->fetch_all(MYSQLI_ASSOC);

$page_title = $post['title'];
$nav_active = 'blog';
$extra_css = '<link rel="stylesheet" href="css/blog.css">';
include __DIR__ . '/includes/header.php';
?>

  <div class="page-hero">
    <div class="container">
      <h1>Blog Detail</h1>
      <div class="breadcrumb-nav"><a href="index.php">Home</a><span class="sep">›</span><a
          href="blog.php">Blog</a><span class="sep">›</span><span class="current">Article</span></div>
    </div>
  </div>
  <section class="section section-grey">
    <div class="container">
      <div class="blog-detail-grid">
        <div>
          <div class="blog-body reveal">
            <div class="blog-card-meta" style="font-size:12px;margin-bottom:14px"><i class="fa-solid fa-calendar"></i>
              <?= date('F j, Y', strtotime($post['created_at'])) ?> &nbsp;·&nbsp; <i class="fa-solid fa-tag"></i> <?= e($post['category'] ?? 'Real Estate') ?> &nbsp;·&nbsp; <i
                class="fa-solid fa-user"></i> <?= e($post['first_name'] . ' ' . $post['last_name']) ?></div>
            <h2><?= e($post['title']) ?></h2>
            <img src="img/blogs/<?= e($post['cover_image'] ?? 'b-1.jpg') ?>" alt="<?= e($post['title']) ?>" />
            <?= $post['body'] ?>
            <div class="blog-tags">
              <span class="blog-tag"><?= e($post['category'] ?? 'Real Estate') ?></span>
              <span class="blog-tag">Tips</span>
              <span class="blog-tag">2026</span>
            </div>
          </div>

          <!-- Existing Comments -->
          <div class="blog-body reveal" style="margin-top:24px" id="comments">
            <h2 style="font-size:1.3rem;margin-bottom:20px"><?= count($comments) ?> Comments</h2>
            <?= render_flash() ?>
            <?php foreach ($comments as $c): ?>
            <div style="display:flex;gap:14px;padding:16px 0;border-bottom:1px solid var(--greyMid)">
              <img src="<?= e(avatar_url($c['commenter_avatar'] ?? '', $c['user_id'] ?? crc32($c['name']))) ?>" alt="<?= e($c['name']) ?>" style="width:44px;height:44px;border-radius:50%;object-fit:cover;flex-shrink:0" />
              <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px">
                  <strong style="font-size:14px"><?= e($c['name']) ?></strong>
                  <span style="font-size:11px;color:var(--textLight)"><?= date('M j, Y', strtotime($c['created_at'])) ?></span>
                </div>
                <p style="font-size:14px;color:var(--textLight);line-height:1.6;margin:0"><?= e($c['body']) ?></p>
              </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($comments)): ?>
            <p style="color:var(--textLight);font-size:14px">No comments yet. Be the first to share your thoughts!</p>
            <?php endif; ?>
          </div>

          <!-- Comment form -->
          <div class="blog-body reveal" style="margin-top:24px">
            <h2 style="font-size:1.3rem;margin-bottom:20px">Leave a Comment</h2>
            <form action="actions/blog_comment.php" method="POST">
              <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
              <input type="hidden" name="blog_id" value="<?= $id ?>">
              <div class="form-row">
                <div class="form-group"><label class="form-label">Name</label><input class="form-input" type="text"
                    name="name" placeholder="Your name" required /></div>
                <div class="form-group"><label class="form-label">Email</label><input class="form-input" type="email"
                    name="email" placeholder="you@example.com" required /></div>
              </div>
              <div class="form-group"><label class="form-label">Comment</label><textarea
                  class="form-textarea comment-form" name="body" placeholder="Share your thoughts..." required></textarea></div>
              <button class="btn btn-primary" type="submit">Post Comment</button>
            </form>
          </div>
        </div>

        <!-- Sidebar -->
        <aside>
          <div class="sidebar-card reveal">
            <div class="sidebar-title">Search</div>
            <form action="blog.php" method="GET" style="display:flex;gap:8px">
              <input class="form-input" type="text" name="q" placeholder="Search articles..." style="flex:1" />
              <button class="btn btn-primary btn-sm" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
          </div>
          <div class="sidebar-card reveal">
            <div class="sidebar-title">Categories</div>
            <div class="footer-links">
              <div class="footer-link" style="color:var(--text);border-color:var(--greyMid)"><a href="blog.php">Real Estate
                  <span style="color:var(--mainColor);float:right">(12)</span></a></div>
              <div class="footer-link" style="color:var(--text);border-color:var(--greyMid)"><a href="blog.php">Market Trends
                  <span style="color:var(--mainColor);float:right">(8)</span></a></div>
              <div class="footer-link" style="color:var(--text);border-color:var(--greyMid)"><a href="blog.php">Investment
                  <span style="color:var(--mainColor);float:right">(6)</span></a></div>
              <div class="footer-link" style="color:var(--text);border-color:var(--greyMid)"><a href="blog.php">Buying Guide
                  <span style="color:var(--mainColor);float:right">(5)</span></a></div>
              <div class="footer-link" style="color:var(--text);border-color:var(--greyMid)"><a href="blog.php">Rental Market
                  <span style="color:var(--mainColor);float:right">(4)</span></a></div>
            </div>
          </div>
          <div class="sidebar-card reveal">
            <div class="sidebar-title">Recent Posts</div>
            <?php foreach ($recent as $rp): ?>
            <div class="d-flex gap-3 mb-3 align-items-start">
              <img src="img/blogs/<?= e($rp['cover_image'] ?? 'b-1.jpg') ?>"
                style="width:64px;height:54px;border-radius:8px;object-fit:cover;flex-shrink:0" alt="" />
              <div><a href="blog-details.php?id=<?= $rp['id'] ?>"
                  style="font-size:13px;font-weight:600;color:var(--dark);line-height:1.35;transition:color var(--t);"
                  onmouseover="this.style.color='var(--mainColor)'" onmouseout="this.style.color='var(--dark)'"><?= e($rp['title']) ?></a>
                <div style="font-size:11px;color:var(--mainColor);margin-top:3px"><?= e($rp['category'] ?? 'Real Estate') ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </aside>
      </div>
    </div>
  </section>

<?php
$extra_js = '<script src="js/blog-details.js"></script>';
include __DIR__ . '/includes/footer.php';
?>
