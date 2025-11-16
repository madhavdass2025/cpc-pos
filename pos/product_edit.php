<?php
require_once 'includes/session.php';
require_login();

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

require_once 'includes/db_connect.php';
require_once 'includes/classes/Product.php';

$errors = [];
$product = new Product($conn);

if (isset($_GET['id'])) {
    $product->id = $_GET['id'];
    $product->readOne();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed.');
    }
    $product->id = $_POST['id'];
    $product->product_name = $_POST['product_name'];
    $product->generic_name = $_POST['generic_name'];
    $product->mrp = $_POST['mrp'];
    $product->reorder_level = $_POST['reorder_level'];

    if ($product->update()) {
        header('Location: products.php');
        exit;
    } else {
        $errors[] = 'Failed to update product.';
    }
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Edit Product</h2>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="product_edit.php" method="post">
        <input type="hidden" name="id" value="<?php echo $product->id; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group">
            <label for="product_name">Product Name</label>
            <input type="text" name="product_name" id="product_name" value="<?php echo $product->product_name; ?>" required>
        </div>
        <div class="form-group">
            <label for="generic_name">Generic Name</label>
            <input type="text" name="generic_name" id="generic_name" value="<?php echo $product->generic_name; ?>">
        </div>
        <div class="form-group">
            <label for="mrp">MRP</label>
            <input type="number" name="mrp" id="mrp" step="0.01" value="<?php echo $product->mrp; ?>" required>
        </div>
        <div class="form-group">
            <label for="reorder_level">Reorder Level</label>
            <input type="number" name="reorder_level" id="reorder_level" value="<?php echo $product->reorder_level; ?>" required>
        </div>
        <button type="submit">Update Product</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
