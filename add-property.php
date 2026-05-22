<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_login('index.php');

$u = current_user();
// Only agents and admins can add properties
if (!is_agent_or_admin()) {
    set_flash('error', 'Only agents and admins can list properties.');
    header('Location: dashboard.php');
    exit;
}
$page_title = 'Add Property';
$dash_mode = true;
$dash_active = 'add';
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
            <div class="dash-card-title">Add New Property</div>
            <form action="actions/add_property.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
              <div class="listing-section">
                <div class="listing-section-title">Property Description &amp; Price</div>
                <div class="form-group"><label class="form-label">Property Title</label>
                  <input class="form-input" type="text" name="title" placeholder="e.g. Modern Family Villa in NYC" required /></div>
                <div class="form-group"><label class="form-label">Description</label>
                  <textarea class="form-textarea" name="description" style="min-height:100px" placeholder="Describe your property..." required></textarea></div>
                <div class="form-row" style="grid-template-columns:repeat(3,1fr)">
                  <div class="form-group"><label class="form-label">Status</label>
                    <select class="form-select" name="status"><option value="For Sale">For Sale</option><option value="For Rent">For Rent</option></select></div>
                  <div class="form-group"><label class="form-label">Type</label>
                    <select class="form-select" name="type"><option>House</option><option>Apartment</option><option>Villa</option><option>Commercial</option><option>Lot</option><option>Garage</option></select></div>
                  <div class="form-group"><label class="form-label">Rooms</label>
                    <select class="form-select" name="rooms"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option></select></div>
                </div>
                <div class="form-row">
                  <div class="form-group"><label class="form-label">Price (USD)</label>
                    <input class="form-input" type="number" name="price" min="0" step="0.01" placeholder="350000" required /></div>
                  <div class="form-group"><label class="form-label">Area (sqft)</label>
                    <input class="form-input" type="number" name="area_sqft" min="0" placeholder="1200" /></div>
                </div>
              </div>
              <div class="listing-section">
                <div class="listing-section-title">Property Media</div>
                <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                  <input type="file" id="fileInput" name="images[]" multiple accept="image/*" style="display:none" />
                  <i class="fa-solid fa-cloud-arrow-up"></i>
                  <p>Click to upload images</p>
                  <span class="upload-hint">PNG, JPG supported</span>
                </div>
                <div id="uploadPreview"></div>
              </div>
              <div class="listing-section">
                <div class="listing-section-title">Location</div>
                <div class="form-row">
                  <div class="form-group"><label class="form-label">Address</label><input class="form-input" type="text" name="address" placeholder="95 South Park Avenue" /></div>
                  <div class="form-group"><label class="form-label">City</label><input class="form-input" type="text" name="city" placeholder="New York" /></div>
                </div>
                <div class="form-row">
                  <div class="form-group"><label class="form-label">State</label><input class="form-input" type="text" name="state" placeholder="NY" /></div>
                  <div class="form-group"><label class="form-label">Country</label><input class="form-input" type="text" name="country" placeholder="USA" /></div>
                </div>
              </div>
              <div class="listing-section">
                <div class="listing-section-title">Extra Information</div>
                <div class="form-row" style="grid-template-columns:repeat(3,1fr)">
                  <div class="form-group"><label class="form-label">Bedrooms</label>
                    <select class="form-select" name="bedrooms"><option value="">Any</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option><option>6</option></select></div>
                  <div class="form-group"><label class="form-label">Bathrooms</label>
                    <select class="form-select" name="bathrooms"><option value="">Any</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select></div>
                  <div class="form-group"><label class="form-label">Garages</label>
                    <select class="form-select" name="garages"><option value="">None</option><option>1</option><option>2</option><option>3</option></select></div>
                </div>
              </div>
              <div class="listing-section">
                <div class="listing-section-title">Features</div>
                <div class="listing-features">
                  <?php foreach (['Air Conditioning','Swimming Pool','Central Heating','Laundry Room','Gym','Alarm','Window Covering','WiFi','TV Cable','Dryer','Microwave','Washer','Refrigerator','Parking','Balcony','Outdoor Shower'] as $feat): ?>
                  <label class="listing-feature-check"><input type="checkbox" name="features[]" value="<?= e($feat) ?>" /><span><?= e($feat) ?></span></label>
                  <?php endforeach; ?>
                </div>
              </div>
              <button type="submit" class="btn btn-primary btn-lg"><i class="fa-solid fa-floppy-disk"></i> Submit Property</button>
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
