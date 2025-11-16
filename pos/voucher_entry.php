<?php
require_once 'includes/session.php';
require_login();
require_role(1); // 1 = Admin

require_once 'includes/db_connect.php';
require_once 'includes/classes/Account.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account = new Account($conn);
    $voucher_type = $_POST['voucher_type'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    $voucher_date = $_POST['voucher_date'];

    if ($account->create_voucher($voucher_type, $amount, $description, $voucher_date)) {
        header('Location: index.php'); // Redirect to dashboard or a voucher list page
        exit;
    } else {
        $errors[] = 'Failed to create voucher.';
    }
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Voucher Entry</h2>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="voucher_entry.php" method="post">
        <div class="form-group">
            <label for="voucher_type">Voucher Type</label>
            <select name="voucher_type" id="voucher_type" required>
                <option value="Expense">Expense</option>
                <option value="Income">Income</option>
            </select>
        </div>
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" name="amount" id="amount" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description"></textarea>
        </div>
        <div class="form-group">
            <label for="voucher_date">Voucher Date</label>
            <input type="date" name="voucher_date" id="voucher_date" required>
        </div>
        <button type="submit">Create Voucher</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
