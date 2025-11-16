<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Account.php';

$account = new Account($conn);
$collections = null;
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $collections = $account->get_collection_report($_GET['start_date'], $_GET['end_date'], $_GET['payment_method'] ?? null);
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Collection Report</h2>

    <form action="report_collection.php" method="get">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $_GET['start_date'] ?? ''; ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $_GET['end_date'] ?? ''; ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-control">
                        <option value="">All</option>
                        <option value="Cash" <?php echo (isset($_GET['payment_method']) && $_GET['payment_method'] == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                        <option value="Card" <?php echo (isset($_GET['payment_method']) && $_GET['payment_method'] == 'Card') ? 'selected' : ''; ?>>Card</option>
                        <option value="UPI" <?php echo (isset($_GET['payment_method']) && $_GET['payment_method'] == 'UPI') ? 'selected' : ''; ?>>UPI</option>
                    </select>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Generate Report</button>
    </form>

    <?php if ($collections && $collections->rowCount() > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Invoice ID</th>
                    <th>Payment Method</th>
                    <th>Amount Paid</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $collections->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $payment_date; ?></td>
                        <td><?php echo $invoice_id; ?></td>
                        <td><?php echo $payment_method; ?></td>
                        <td><?php echo $amount_paid; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif (isset($_GET['start_date'])): ?>
        <p>No collections found for the selected criteria.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
