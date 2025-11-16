<?php
require_once 'includes/session.php';
require_login();

require_once 'includes/db_connect.php';
require_once 'includes/classes/Customer.php';

$errors = [];
$customer = new Customer($conn);

if (isset($_GET['id'])) {
    $customer->RegID = $_GET['id'];
    $customer->readOne();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer->RegID = $_POST['id'];
    $customer->ownnam = htmlspecialchars(strip_tags($_POST['ownnam']));
    $customer->ownmob = htmlspecialchars(strip_tags($_POST['ownmob']));
    $customer->petnam = htmlspecialchars(strip_tags($_POST['petnam']));
    $customer->Pettyp = htmlspecialchars(strip_tags($_POST['Pettyp']));

    if ($customer->update()) {
        header('Location: customers.php');
        exit;
    } else {
        $errors[] = 'Failed to update customer.';
    }
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Edit Customer</h2>
    <?php if ($customer->ownnam): ?>
        <form action="customer_edit.php" method="post">
            <input type="hidden" name="id" value="<?php echo $customer->RegID; ?>">
            <div class="form-group">
                <label for="ownnam">Owner Name</label>
                <input type="text" name="ownnam" id="ownnam" value="<?php echo $customer->ownnam; ?>" required>
            </div>
            <div class="form-group">
                <label for="ownmob">Owner Mobile</label>
                <input type="text" name="ownmob" id="ownmob" value="<?php echo $customer->ownmob; ?>">
            </div>
            <div class="form-group">
                <label for="petnam">Pet Name</label>
                <input type="text" name="petnam" id="petnam" value="<?php echo $customer->petnam; ?>">
            </div>
            <div class="form-group">
                <label for="Pettyp">Pet Type</label>
                <input type="text" name="Pettyp" id="Pettyp" value="<?php echo $customer->Pettyp; ?>">
            </div>
            <button type="submit">Update Customer</button>
        </form>
    <?php else: ?>
        <p>Customer not found.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
