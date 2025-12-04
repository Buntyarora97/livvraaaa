<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/models/Category.php';

$pageTitle = 'Edit Category';
$currentPage = 'categories';

$id = $_GET['id'] ?? 0;
$category = Category::getById($id);

if (!$category) {
    header('Location: categories.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Category::update($id, $_POST);
        $success = 'Category updated successfully!';
        $category = Category::getById($id);
    } catch (Exception $e) {
        $error = 'Failed to update category.';
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
        <h2>Edit Category: <?php echo htmlspecialchars($category['name']); ?></h2>
        <a href="categories.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="icon_class">Icon Class (FontAwesome)</label>
                    <input type="text" id="icon_class" name="icon_class" class="form-control" value="<?php echo htmlspecialchars($category['icon_class']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="2"><?php echo htmlspecialchars($category['description']); ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?php echo $category['sort_order']; ?>">
                </div>
                <div class="form-group">
                    <label for="is_active">Status</label>
                    <select id="is_active" name="is_active" class="form-control">
                        <option value="1" <?php echo $category['is_active'] ? 'selected' : ''; ?>>Active</option>
                        <option value="0" <?php echo !$category['is_active'] ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Category
            </button>
        </form>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
