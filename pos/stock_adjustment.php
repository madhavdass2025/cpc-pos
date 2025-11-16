<?php
require_once 'includes/session.php';
require_login();
require_role(3); // 3 = Inventory Manager

require_once 'includes/db_connect.php';
require_once 'includes/classes/Stock.php';
require_once 'includes/classes/Product.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stock = new Stock($conn);
    $product_id = $_POST['product_id'];
    $batch_id = $_POST['batch_id'];
    $quantity = $_POST['quantity'];
    $transaction_type = $_POST['transaction_type'];

    // If it's an adjustment out, the quantity should be negative
    if ($transaction_type === 'ADJ-OUT') {
        $quantity = -$quantity;
    }

    if ($stock->adjust_stock($product_id, $batch_id, $quantity, $transaction_type)) {
        header('Location: report_current_stock.php');
        exit;
    } else {
        $errors[] = 'Failed to adjust stock.';
    }
}

$product = new Product($conn);
$product_stmt = $product->read();
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Stock Adjustment</h2>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="stock_adjustment.php" method="post">
        <div class="form-group">
            <label for="product_id">Product</label>
            <select name="product_id" id="product_id" required>
                <option value="">Select Product</option>
                <?php while ($row = $product_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['product_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="batch_id">Batch</label>
            <select name="batch_id" id="batch_id" required>
                <option value="">Select Batch</option>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity" id="quantity" required>
        </div>
        <div class="form-group">
            <label for="transaction_type">Adjustment Type</label>
            <select name="transaction_type" id="transaction_type" required>
                <option value="ADJ-IN">Adjustment In (e.g., found stock)</option>
                <option value="ADJ-OUT">Adjustment Out (e.g., breakage)</option>
            </select>
        </div>
        <button type="submit">Adjust Stock</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productIdSelect = document.getElementById('product_id');
    const batchIdSelect = document.getElementById('batch_id');

    productIdSelect.addEventListener('change', function() {
        const productId = this.value;
        batchIdSelect.innerHTML = '<option value="">Loading...</option>';

        if (productId) {
            fetch(`ajax_get_batches.php?product_id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    let optionsHTML = '<option value="">Select Batch</option>';
                    data.forEach(batch => {
                        optionsHTML += `<option value="${batch.id}">${batch.batch_number} (Qty: ${batch.current_qty})</option>`;
                    });
                    batchIdSelect.innerHTML = optionsHTML;
                });
        } else {
            batchIdSelect.innerHTML = '<option value="">Select Batch</option>';
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
