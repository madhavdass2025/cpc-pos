<?php
require_once 'includes/session.php';
require_login();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <h2>Sales Return</h2>
    <form action="sales_return.php" method="post">
        <div class="form-group">
            <label for="invoice_id">Original Invoice ID</label>
            <input type="text" name="invoice_id" id="invoice_id" required>
        </div>
        <!-- Add more fields for the return -->
        <button type="submit">Process Return</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
