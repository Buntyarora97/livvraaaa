<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/models/Product.php';
require_once '../includes/models/Category.php';

$pageTitle = 'Edit Product';
$currentPage = 'products';

$id = $_GET['id'] ?? 0;
$product = Product::getById($id);

if (!$product) {
    header('Location: products.php');
    exit;
}

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
        
        Product::update($id, $data);
        $success = 'Product updated successfully!';
        $product = Product::getById($id);
    } catch (Exception $e) {
        $error = 'Failed to update product: ' . $e->getMessage();
    }
}

require_once 'views/layouts/header.php';
?>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>Edit Product: <?php echo htmlspecialchars($product['name']); ?></h2>
        <a href="products.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="sku">SKU</label>
                    <input type="text" id="sku" name="sku" class="form-control" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="stock_qty">Stock Quantity</label>
                    <input type="number" id="stock_qty" name="stock_qty" class="form-control" value="<?php echo $product['stock_qty']; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Selling Price (₹) *</label>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" value="<?php echo $product['price']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="mrp">MRP (₹)</label>
                    <input type="number" id="mrp" name="mrp" class="form-control" step="0.01" value="<?php echo $product['mrp']; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="short_description">Short Description</label>
                <textarea id="short_description" name="short_description" class="form-control" rows="2"><?php echo htmlspecialchars($product['short_description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="long_description">Long Description</label>
                <textarea id="long_description" name="long_description" class="form-control" rows="4"><?php echo htmlspecialchars($product['long_description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="benefits">Benefits (comma separated)</label>
                <input type="text" id="benefits" name="benefits" class="form-control" value="<?php echo htmlspecialchars($product['benefits'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label>Current Image</label>
                <?php if ($product['image']): ?>
                <div>
                    <img src="../public/assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="" class="product-image-preview">
                </div>
                <?php else: ?>
                <p style="color: #888;">No image uploaded</p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="image">Change Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="is_featured">Featured Product</label>
                    <select id="is_featured" name="is_featured" class="form-control">
                        <option value="0" <?php echo !$product['is_featured'] ? 'selected' : ''; ?>>No</option>
                        <option value="1" <?php echo $product['is_featured'] ? 'selected' : ''; ?>>Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="is_active">Status</label>
                    <select id="is_active" name="is_active" class="form-control">
                        <option value="1" <?php echo $product['is_active'] ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo !$product['is_active'] ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Product
            </button>
        </form>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
