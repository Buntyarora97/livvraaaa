<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/models/Product.php';
require_once '../includes/models/Category.php';

$pageTitle = 'Products';
$currentPage = 'products';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete') {
        try {
            Product::delete($_POST['id']);
            $success = 'Product deleted successfully!';
        } catch (Exception $e) {
            $error = 'Failed to delete product.';
        }
    }
}

$products = Product::getAll();
$categories = Category::getAll(true);

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
        <h2>All Products (<?php echo count($products); ?>)</h2>
        <a href="product-add.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($products)): ?>
        <div class="empty-state">
            <i class="fas fa-box"></i>
            <p>No products yet. Add your first product.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <?php if ($product['image']): ?>
                        <img src="../public/assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="" class="product-image-preview" style="width: 50px; height: 50px;">
                        <?php else: ?>
                        <div style="width: 50px; height: 50px; background: #eee; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                            <i class="fas fa-image" style="color: #ccc;"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                        <br><small style="color: #888;">SKU: <?php echo htmlspecialchars($product['sku'] ?: 'N/A'); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                    <td>
                        <strong>₹<?php echo number_format($product['price']); ?></strong>
                        <?php if ($product['mrp'] > $product['price']): ?>
                        <br><small style="text-decoration: line-through; color: #888;">₹<?php echo number_format($product['mrp']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $product['stock_qty']; ?></td>
                    <td>
                        <span class="badge badge-<?php echo $product['is_featured'] ? 'primary' : 'secondary'; ?>">
                            <?php echo $product['is_featured'] ? 'Yes' : 'No'; ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo $product['is_active'] ? 'success' : 'danger'; ?>">
                            <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td class="actions-cell">
                        <a href="product-edit.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="" style="display: inline;" onsubmit="return confirmDelete('Delete this product?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
