<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Product.php';

$product = new Product($conn);
$stmt = $product->read();
$num = $stmt->rowCount();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="header">
        <h2>Product Management</h2>
        <a href="product_add.php" class="btn btn-primary">Add New Product</a>
    </div>

    <?php if ($num > 0): ?>
        <table class="table" id="products-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Generic Name</th>
                    <th>MRP</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $product_name; ?></td>
                        <td><?php echo $generic_name; ?></td>
                        <td><?php echo $mrp; ?></td>
                        <td><?php echo $reorder_level; ?></td>
                        <td><?php echo $status; ?></td>
                        <td>
                            <a href="product_edit.php?id=<?php echo $id; ?>" class="btn btn-secondary">Edit</a>
                            <a href="product_delete.php?id=<?php echo $id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
