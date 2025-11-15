<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Account.php';
require_once 'includes/classes/Supplier.php';

$supplier = new Supplier($conn);
$supplier_stmt = $supplier->read();

$supplier_ledger = null;
$balance = 0;
if (isset($_GET['supplier_id'])) {
    $account = new Account($conn);
    $supplier_ledger = $account->get_supplier_ledger($_GET['supplier_id']);
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Supplier Ledger</h2>

    <form action="report_supplier_ledger.php" method="get">
        <div class="form-group">
            <label for="supplier_id">Select Supplier</label>
            <select name="supplier_id" id="supplier_id" onchange="this.form.submit()">
                <option value="">Select a supplier to view their ledger</option>
                <?php while ($row = $supplier_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo $row['id']; ?>" <?php echo (isset($_GET['supplier_id']) && $_GET['supplier_id'] == $row['id']) ? 'selected' : ''; ?>>
                        <?php echo $row['supplier_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </form>

    <?php if ($supplier_ledger && $supplier_ledger->rowCount() > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction Type</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $supplier_ledger->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php
                        $debit = $row['amount'] > 0 ? $row['amount'] : '';
                        $credit = $row['amount'] < 0 ? abs($row['amount']) : '';
                        $balance += $row['amount'];
                    ?>
                    <tr>
                        <td><?php echo $row['date']; ?></td>
                        <td><?php echo $row['transaction_type']; ?></td>
                        <td><?php echo $debit; ?></td>
                        <td><?php echo $credit; ?></td>
                        <td><?php echo $balance; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Closing Balance</th>
                    <th><?php echo $balance; ?></th>
                </tr>
            </tfoot>
        </table>
    <?php elseif (isset($_GET['supplier_id'])): ?>
        <p>No transactions found for the selected supplier.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
