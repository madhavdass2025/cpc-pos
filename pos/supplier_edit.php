<?php
require_once 'includes/session.php';
require_login();
require_role(1); // 1 = Admin

require_once 'includes/db_connect.php';
require_once 'includes/classes/Supplier.php';

$errors = [];
$supplier = new Supplier($conn);

if (isset($_GET['id'])) {
    $supplier->id = $_GET['id'];
    $supplier->readOne();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier->id = $_POST['id'];
    $supplier->supplier_name = $_POST['supplier_name'];
    $supplier->phone = $_POST['phone'];
    $supplier->gstin = $_POST['gstin'];
    $supplier->address = $_POST['address'];

    if ($supplier->update()) {
        header('Location: suppliers.php');
        exit;
    } else {
        $errors[] = 'Failed to update supplier.';
    }
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Edit Supplier</h2>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="supplier_edit.php" method="post">
        <input type="hidden" name="id" value="<?php echo $supplier->id; ?>">
        <div class="form-group">
            <label for="supplier_name">Supplier Name</label>
            <input type="text" name="supplier_name" id="supplier_name" value="<?php echo $supplier->supplier_name; ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" value="<?php echo $supplier->phone; ?>">
        </div>
        <div class="form-group">
            <label for="gstin">GSTIN</label>
            <input type="text" name="gstin" id="gstin" value="<?php echo $supplier->gstin; ?>">
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <textarea name="address" id="address"><?php echo $supplier->address; ?></textarea>
        </div>
        <button type="submit">Update Supplier</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
