<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Stock.php';
require_once 'includes/classes/Product.php';

$product = new Product($conn);
$product_stmt = $product->read();

$stock_ledger = null;
if (isset($_GET['product_id'])) {
    $stock = new Stock($conn);
    $stock_ledger = $stock->get_stock_ledger($_GET['product_id']);
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Stock Ledger Report</h2>

    <form action="report_stock_ledger.php" method="get">
        <div class="form-group">
            <label for="product_id">Select Product</label>
            <select name="product_id" id="product_id" onchange="this.form.submit()">
                <option value="">Select a product to view its ledger</option>
                <?php while ($row = $product_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo (isset($_GET['product_id']) && $_GET['product_id'] == $row['id']) ? 'selected' : ''; ?>>
                        <?php echo $row['product_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </form>

    <?php if ($stock_ledger && $stock_ledger->rowCount() > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction Type</th>
                    <th>Batch</th>
                    <th>Quantity</th>
                    <th>Reference ID</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stock_ledger->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $transaction_date; ?></td>
                        <td><?php echo $transaction_type; ?></td>
                        <td><?php echo $batch_number; ?></td>
                        <td><?php echo $quantity; ?></td>
                        <td><?php echo $reference_id; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif (isset($_GET['product_id'])): ?>
        <p>No ledger entries found for the selected product.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
