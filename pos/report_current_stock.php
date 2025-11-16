<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Stock.php';

$stock = new Stock($conn);
$stmt = $stock->get_current_stock();
$num = $stmt->rowCount();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <h2>Current Stock Report</h2>

    <?php if ($num > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Batch Number</th>
                    <th>Current Quantity</th>
                    <th>Expiry Date</th>
                    <th>Purchase Price</th>
                    <th>MRP</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $product_name; ?></td>
                        <td><?php echo $batch_number; ?></td>
                        <td><?php echo $current_qty; ?></td>
                        <td><?php echo $expiry_date; ?></td>
                        <td><?php echo $purchase_price; ?></td>
                        <td><?php echo $mrp; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No stock found.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
