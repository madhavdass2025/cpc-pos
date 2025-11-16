<?php
require_once 'includes/session.php';
require_login();
require_role(1); // 1 = Admin

require_once 'includes/db_connect.php';
require_once 'includes/classes/Supplier.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier = new Supplier($conn);
    $supplier->supplier_name = $_POST['supplier_name'];
    $supplier->phone = $_POST['phone'];
    $supplier->gstin = $_POST['gstin'];
    $supplier->address = $_POST['address'];

    if ($supplier->create()) {
        header('Location: suppliers.php');
        exit;
    } else {
        $errors[] = 'Failed to create supplier.';
    }
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Add New Supplier</h2>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="supplier_add.php" method="post">
        <div class="form-group">
            <label for="supplier_name">Supplier Name</label>
            <input type="text" name="supplier_name" id="supplier_name" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone">
        </div>
        <div class="form-group">
            <label for="gstin">GSTIN</label>
            <input type="text" name="gstin" id="gstin">
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <textarea name="address" id="address"></textarea>
        </div>
        <button type="submit">Add Supplier</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
