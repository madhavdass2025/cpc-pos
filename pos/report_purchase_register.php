<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Account.php';

$account = new Account($conn);
$purchases = null;
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $purchases = $account->get_purchase_register($_GET['start_date'], $_GET['end_date']);
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Purchase Register</h2>

    <form action="report_purchase_register.php" method="get">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo $_GET['start_date'] ?? ''; ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo $_GET['end_date'] ?? ''; ?>">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Generate Report</button>
    </form>

    <?php if ($purchases && $purchases->rowCount() > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Bill Number</th>
                    <th>Bill Date</th>
                    <th>Supplier</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $purchases->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $bill_number; ?></td>
                        <td><?php echo $bill_date; ?></td>
                        <td><?php echo $supplier_name; ?></td>
                        <td><?php echo $total_amount; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif (isset($_GET['start_date'])): ?>
        <p>No purchases found for the selected date range.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
