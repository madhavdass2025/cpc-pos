<?php
require_once 'includes/session.php';
require_login();
// require_role('Admin');

require_once 'includes/db_connect.php';
require_once 'includes/classes/Supplier.php';
require_once 'includes/classes/Account.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account = new Account($conn);
    $supplier_id = $_POST['supplier_id'];
    $payment_date = $_POST['payment_date'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $notes = $_POST['notes'];

    if ($account->create_supplier_payment($supplier_id, $payment_date, $amount, $payment_method, $notes)) {
        header('Location: report_supplier_ledger.php?supplier_id=' . $supplier_id);
        exit;
    } else {
        $errors[] = 'Failed to add payment.';
    }
}

$supplier = new Supplier($conn);
$supplier_stmt = $supplier->read();

?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Add Supplier Payment</h2>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="supplier_payment_add.php" method="post">
        <div class="form-group">
            <label for="supplier_id">Supplier</label>
            <select name="supplier_id" id="supplier_id" required>
                <option value="">Select Supplier</option>
                <?php while ($row = $supplier_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['supplier_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="payment_date">Payment Date</label>
            <input type="date" name="payment_date" id="payment_date" required>
        </div>
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" name="amount" id="amount" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <input type="text" name="payment_method" id="payment_method">
        </div>
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes"></textarea>
        </div>
        <button type="submit">Add Payment</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
