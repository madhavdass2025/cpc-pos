<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Sale.php';

$sale = new Sale($conn);
if (isset($_GET['id'])) {
    $sale->id = $_GET['id'];
    $sale->readOneWithDetails();
}

?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Sale Details</h2>

    <div class="row">
        <div class="col-md-6">
            <p><strong>Invoice Number:</strong> <?php echo $sale->invoice_number; ?></p>
            <p><strong>Invoice Date:</strong> <?php echo $sale->invoice_date; ?></p>
            <p><strong>Customer:</strong> <?php echo $sale->ownnam; ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Net Amount:</strong> <?php echo $sale->net_amount; ?></p>
            <p><strong>Payment Status:</strong> <?php echo $sale->payment_status; ?></p>
            <p><strong>Sold By:</strong> <?php echo $sale->user_name; ?></p>
        </div>
    </div>

    <h3>Products Sold</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sale->products as $product): ?>
                <tr>
                    <td><?php echo $product['product_name']; ?></td>
                    <td><?php echo $product['quantity']; ?></td>
                    <td><?php echo $product['total_price']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
