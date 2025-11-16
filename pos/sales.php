<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Sale.php';

$sale = new Sale($conn);
$stmt = $sale->read();
$num = $stmt->rowCount();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="header">
        <h2>Sales Management</h2>
        <a href="pos.php" class="btn btn-primary">New Sale (POS)</a>
    </div>

    <?php if ($num > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Customer</th>
                    <th>Net Amount</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $invoice_number; ?></td>
                        <td><?php echo $invoice_date; ?></td>
                        <td><?php echo $ownnam; ?></td>
                        <td><?php echo $net_amount; ?></td>
                        <td><?php echo $payment_status; ?></td>
                        <td>
                            <a href="sale_view.php?id=<?php echo $id; ?>" class="btn btn-secondary">View</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No sales found.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
