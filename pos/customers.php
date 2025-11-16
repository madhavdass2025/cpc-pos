<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Customer.php';

$customer = new Customer($conn);
$stmt = $customer->read();
$num = $stmt->rowCount();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="header">
        <h2>Customer Management</h2>
        <a href="customer_add.php" class="btn btn-primary">Add New Customer</a>
    </div>

    <?php if ($num > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Owner Name</th>
                    <th>Owner Mobile</th>
                    <th>Pet Name</th>
                    <th>Pet Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $ownnam; ?></td>
                        <td><?php echo $ownmob; ?></td>
                        <td><?php echo $petnam; ?></td>
                        <td><?php echo $Pettyp; ?></td>
                        <td>
                            <a href="customer_edit.php?id=<?php echo $RegID; ?>" class="btn btn-secondary">Edit</a>
                            <a href="customer_delete.php?id=<?php echo $RegID; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No customers found.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
