<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Stock.php';

$stock = new Stock($conn);
$low_stock_stmt = $stock->get_low_stock();
$expiring_soon_stmt = $stock->get_expiring_soon();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <h2>Low Stock & Expiry Report</h2>

    <h3>Low Stock Items</h3>
    <?php if ($low_stock_stmt->rowCount() > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Total Quantity</th>
                    <th>Reorder Level</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $low_stock_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $product_name; ?></td>
                        <td><?php echo $total_qty; ?></td>
                        <td><?php echo $reorder_level; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No low stock items found.</p>
    <?php endif; ?>

    <h3>Items Expiring Soon (within 30 days)</h3>
    <?php if ($expiring_soon_stmt->rowCount() > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Batch Number</th>
                    <th>Quantity</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $expiring_soon_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $product_name; ?></td>
                        <td><?php echo $batch_number; ?></td>
                        <td><?php echo $current_qty; ?></td>
                        <td><?php echo $expiry_date; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No items expiring soon.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
