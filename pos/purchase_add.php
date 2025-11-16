<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Purchase.php';
require_once 'includes/classes/Supplier.php';
require_once 'includes/classes/Product.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $purchase = new Purchase($conn);
    $purchase->bill_number = $_POST['bill_number'];
    $purchase->bill_date = $_POST['bill_date'];
    $purchase->supplier_id = $_POST['supplier_id'];
    $purchase->total_amount = $_POST['total_amount'];
    $purchase->products = $_POST['products'];

    if ($purchase->create()) {
        header('Location: purchases.php');
        exit;
    } else {
        $errors[] = 'Failed to create purchase.';
    }
}

$supplier = new Supplier($conn);
$supplier_stmt = $supplier->read();

$product = new Product($conn);
$product_stmt = $product->read();
$product_options = '';
while ($row = $product_stmt->fetch(PDO::FETCH_ASSOC)) {
    $product_options .= "<option value='{$row['id']}'>{$row['product_name']}</option>";
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Add New Purchase</h2>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="purchase_add.php" method="post">
        <div class="form-group">
            <label for="bill_number">Bill Number</label>
            <input type="text" name="bill_number" id="bill_number" required>
        </div>
        <div class="form-group">
            <label for="bill_date">Bill Date</label>
            <input type="date" name="bill_date" id="bill_date" required>
        </div>
        <div class="form-group">
            <label for="supplier_id">Supplier</label>
            <select name="supplier_id" id="supplier_id" required>
                <option value="">Select Supplier</option>
                <?php while ($row = $supplier_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['supplier_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <h3>Products</h3>
        <table class="table" id="products-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Batch Number</th>
                    <th>Expiry Date</th>
                    <th>Quantity</th>
                    <th>Purchase Price</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <!-- Product rows will be added here dynamically -->
            </tbody>
        </table>
        <button type="button" id="add-product-row" class="btn btn-secondary">Add Product</button>

        <div class="form-group">
            <label for="total_amount">Total Amount</label>
            <input type="number" name="total_amount" id="total_amount" step="0.01" required>
        </div>
        <button type="submit">Add Purchase</button>
    </form>
</div>

<textarea id="product-options" style="display: none;"><?php echo $product_options; ?></textarea>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addProductRowBtn = document.getElementById('add-product-row');
    const productsTableBody = document.querySelector('#products-table tbody');
    const productOptions = document.getElementById('product-options').value;

    addProductRowBtn.addEventListener('click', function() {
        const newRow = document.createElement('tr');
        const rowIndex = productsTableBody.children.length;
        newRow.innerHTML = `
            <td>
                <select name="products[${rowIndex}][product_id]" required>
                    <option value="">Select Product</option>
                    ${productOptions}
                </select>
            </td>
            <td><input type="text" name="products[${rowIndex}][batch_number]" required></td>
            <td><input type="date" name="products[${rowIndex}][expiry_date]"></td>
            <td><input type="number" name="products[${rowIndex}][quantity]" required></td>
            <td><input type="number" name="products[${rowIndex}][purchase_price]" step="0.01" required></td>
            <td><button type="button" class="btn btn-danger remove-product-row">Remove</button></td>
        `;
        productsTableBody.appendChild(newRow);
    });

    productsTableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-product-row')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
