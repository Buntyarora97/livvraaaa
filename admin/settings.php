<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/models/Setting.php';

$pageTitle = 'Settings';
$currentPage = 'settings';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Setting::updateMultiple($_POST);
        $success = 'Settings updated successfully!';
    } catch (Exception $e) {
        $error = 'Failed to update settings.';
    }
}

$settings = Setting::getAll();

require_once 'views/layouts/header.php';
?>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="card">
        <div class="card-header">
            <h2>Site Settings</h2>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" id="site_name" name="site_name" class="form-control" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'LIVVRA'); ?>">
                </div>
                <div class="form-group">
                    <label for="site_tagline">Tagline</label>
                    <input type="text" id="site_tagline" name="site_tagline" class="form-control" value="<?php echo htmlspecialchars($settings['site_tagline'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="site_email">Email</label>
                    <input type="email" id="site_email" name="site_email" class="form-control" value="<?php echo htmlspecialchars($settings['site_email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="site_phone">Phone</label>
                    <input type="text" id="site_phone" name="site_phone" class="form-control" value="<?php echo htmlspecialchars($settings['site_phone'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="site_address">Address</label>
                <textarea id="site_address" name="site_address" class="form-control" rows="2"><?php echo htmlspecialchars($settings['site_address'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2>Payment Settings</h2>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="razorpay_key_id">Razorpay Key ID</label>
                    <input type="text" id="razorpay_key_id" name="razorpay_key_id" class="form-control" value="<?php echo htmlspecialchars($settings['razorpay_key_id'] ?? ''); ?>" placeholder="rzp_test_xxxxx">
                </div>
                <div class="form-group">
                    <label for="razorpay_key_secret">Razorpay Key Secret</label>
                    <input type="password" id="razorpay_key_secret" name="razorpay_key_secret" class="form-control" value="<?php echo htmlspecialchars($settings['razorpay_key_secret'] ?? ''); ?>" placeholder="Enter secret key">
                </div>
            </div>
            <p style="color: #888; font-size: 13px;">Get your Razorpay API keys from <a href="https://dashboard.razorpay.com/app/keys" target="_blank">Razorpay Dashboard</a></p>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2>Shipping Settings</h2>
        </div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label for="shipping_fee">Shipping Fee (₹)</label>
                    <input type="number" id="shipping_fee" name="shipping_fee" class="form-control" value="<?php echo htmlspecialchars($settings['shipping_fee'] ?? '0'); ?>">
                </div>
                <div class="form-group">
                    <label for="free_shipping_above">Free Shipping Above (₹)</label>
                    <input type="number" id="free_shipping_above" name="free_shipping_above" class="form-control" value="<?php echo htmlspecialchars($settings['free_shipping_above'] ?? '499'); ?>">
                </div>
            </div>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Save Settings
    </button>
</form>

<?php require_once 'views/layouts/footer.php'; ?>
