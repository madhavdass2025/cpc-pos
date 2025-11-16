<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Purchase.php';

$purchase = new Purchase($conn);
if (isset($_GET['id'])) {
    $purchase->id = $_GET['id'];
    $purchase->readOneWithDetails();
}

?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Purchase Details</h2>

    <div class="row">
        <div class="col-md-6">
            <p><strong>Bill Number:</strong> <?php echo $purchase->bill_number; ?></p>
            <p><strong>Bill Date:</strong> <?php echo $purchase->bill_date; ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Supplier:</strong> <?php echo $purchase->supplier_name; ?></p>
            <p><strong>Total Amount:</strong> <?php echo $purchase->total_amount; ?></p>
        </div>
    </div>

    <h3>Products Purchased</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Batch Number</th>
                <th>Expiry Date</th>
                <th>Quantity</th>
                <th>Purchase Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($purchase->products as $product): ?>
                <tr>
                    <td><?php echo $product['product_name']; ?></td>
                    <td><?php echo $product['batch_number']; ?></td>
                    <td><?php echo $product['expiry_date']; ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td><?php echo $product['purchase_price']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
