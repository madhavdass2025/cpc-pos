<?php
require_once 'includes/session.php';
require_login();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <h2>Purchase Return</h2>
    <form action="purchase_return.php" method="post">
        <div class="form-group">
            <label for="purchase_id">Original Purchase ID</label>
            <input type="text" name="purchase_id" id="purchase_id" required>
        </div>
        <!-- Add more fields for the return -->
        <button type="submit">Process Return</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
