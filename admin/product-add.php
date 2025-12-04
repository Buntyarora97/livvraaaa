<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/models/Product.php';
require_once '../includes/models/Category.php';

$pageTitle = 'Add Product';
$currentPage = 'products';

$categories = Category::getAll(true);
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = $_POST;
        
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = '../public/assets/images/products/';
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($_FILES['image']['type'], $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $data['image'] = $fileName;
                }
            }
        }
        
        Product::create($data);
        header('Location: products.php?success=1');
        exit;
    } catch (Exception $e) {
        $error = 'Failed to create product: ' . $e->getMessage();
    }
}

require_once 'views/layouts/header.php';
?>

<?php if ($error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>Add New Product</h2>
        <a href="products.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="sku">SKU</label>
                    <input type="text" id="sku" name="sku" class="form-control">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="stock_qty">Stock Quantity</label>
                    <input type="number" id="stock_qty" name="stock_qty" class="form-control" value="100">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Selling Price (₹) *</label>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="mrp">MRP (₹)</label>
                    <input type="number" id="mrp" name="mrp" class="form-control" step="0.01">
                </div>
            </div>
            
            <div class="form-group">
                <label for="short_description">Short Description</label>
                <textarea id="short_description" name="short_description" class="form-control" rows="2"></textarea>
            </div>
            
            <div class="form-group">
                <label for="long_description">Long Description</label>
                <textarea id="long_description" name="long_description" class="form-control" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label for="benefits">Benefits (comma separated)</label>
                <input type="text" id="benefits" name="benefits" class="form-control" placeholder="Benefit 1, Benefit 2, Benefit 3">
            </div>
            
            <div class="form-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="is_featured">Featured Product</label>
                    <select id="is_featured" name="is_featured" class="form-control">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="is_active">Status</label>
                    <select id="is_active" name="is_active" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Product
            </button>
        </form>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
