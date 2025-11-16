<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Account.php';

$account = new Account($conn);
$credit_report = $account->get_customer_credit_report();
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Customer Credit Report</h2>

    <?php if ($credit_report && $credit_report->rowCount() > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Outstanding Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $credit_report->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $ownnam; ?></td>
                        <td><?php echo $outstanding_balance; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No customers with outstanding credit found.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
