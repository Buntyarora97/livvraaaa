<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/models/Category.php';

$pageTitle = 'Categories';
$currentPage = 'categories';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        try {
            Category::create($_POST);
            $success = 'Category created successfully!';
        } catch (Exception $e) {
            $error = 'Failed to create category.';
        }
    } elseif ($action === 'update') {
        try {
            Category::update($_POST['id'], $_POST);
            $success = 'Category updated successfully!';
        } catch (Exception $e) {
            $error = 'Failed to update category.';
        }
    } elseif ($action === 'delete') {
        try {
            Category::delete($_POST['id']);
            $success = 'Category deleted successfully!';
        } catch (Exception $e) {
            $error = 'Failed to delete category.';
        }
    }
}

$categories = Category::getAll();

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
        <h2>Add New Category</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="icon_class">Icon Class (FontAwesome)</label>
                    <input type="text" id="icon_class" name="icon_class" class="form-control" value="fa-leaf" placeholder="fa-leaf">
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="0">
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
                <i class="fas fa-plus"></i> Add Category
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>All Categories (<?php echo count($categories); ?>)</h2>
    </div>
    <div class="card-body">
        <?php if (empty($categories)): ?>
        <div class="empty-state">
            <i class="fas fa-tags"></i>
            <p>No categories yet. Add your first category above.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Icon</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?php echo $cat['id']; ?></td>
                    <td><i class="fas <?php echo htmlspecialchars($cat['icon_class']); ?>"></i></td>
                    <td><?php echo htmlspecialchars($cat['name']); ?></td>
                    <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                    <td><?php echo Category::getProductCount($cat['id']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $cat['is_active'] ? 'success' : 'danger'; ?>">
                            <?php echo $cat['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td class="actions-cell">
                        <a href="category-edit.php?id=<?php echo $cat['id']; ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="" style="display: inline;" onsubmit="return confirmDelete('Delete this category?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
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
