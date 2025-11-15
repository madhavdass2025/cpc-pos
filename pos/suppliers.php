<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Supplier.php';

$supplier = new Supplier($conn);
$stmt = $supplier->read();
$num = $stmt->rowCount();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="header">
        <h2>Supplier Management</h2>
        <a href="supplier_add.php" class="btn btn-primary">Add New Supplier</a>
    </div>

    <?php if ($num > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Phone</th>
                    <th>GSTIN</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $supplier_name; ?></td>
                        <td><?php echo $phone; ?></td>
                        <td><?php echo $gstin; ?></td>
                        <td><?php echo $address; ?></td>
                        <td>
                            <a href="supplier_edit.php?id=<?php echo $id; ?>" class="btn btn-secondary">Edit</a>
                            <a href="supplier_delete.php?id=<?php echo $id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this supplier?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No suppliers found.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
