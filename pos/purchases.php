<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Purchase.php';

$purchase = new Purchase($conn);
$stmt = $purchase->read();
$num = $stmt->rowCount();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="header">
        <h2>Purchase Management</h2>
        <a href="purchase_add.php" class="btn btn-primary">Add New Purchase</a>
    </div>

    <?php if ($num > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Bill Number</th>
                    <th>Bill Date</th>
                    <th>Supplier</th>
                    <th>Total Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $bill_number; ?></td>
                        <td><?php echo $bill_date; ?></td>
                        <td><?php echo $supplier_name; ?></td>
                        <td><?php echo $total_amount; ?></td>
                        <td>
                            <a href="purchase_view.php?id=<?php echo $id; ?>" class="btn btn-secondary">View</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No purchases found.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
