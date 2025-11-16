<?php
require_once 'includes/session.php';
require_login();

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

require_once 'includes/db_connect.php';
require_once 'includes/classes/Customer.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed.');
    }
    $customer = new Customer($conn);
    $customer->custType = 'regular';
    $customer->RegDt = date('Y-m-d');
    $customer->RegNo = 'REG-' . date('YmdHis');
    $customer->Pettyp = htmlspecialchars(strip_tags($_POST['Pettyp']));
    $customer->petnam = htmlspecialchars(strip_tags($_POST['petnam']));
    $customer->petsp = htmlspecialchars(strip_tags($_POST['petsp']));
    $customer->year = htmlspecialchars(strip_tags($_POST['year']));
    $customer->month = htmlspecialchars(strip_tags($_POST['month']));
    $customer->gram = htmlspecialchars(strip_tags($_POST['gram']));
    $customer->kg = htmlspecialchars(strip_tags($_POST['kg']));
    $customer->ownnam = htmlspecialchars(strip_tags($_POST['ownnam']));
    $customer->ownmob = htmlspecialchars(strip_tags($_POST['ownmob']));

    if ($customer->create()) {
        header('Location: customers.php');
        exit;
    } else {
        $errors[] = 'Failed to create customer.';
    }
}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Add New Customer</h2>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="customer_add.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group">
            <label for="ownnam">Owner Name</label>
            <input type="text" name="ownnam" id="ownnam" required>
        </div>
        <div class="form-group">
            <label for="ownmob">Owner Mobile</label>
            <input type="text" name="ownmob" id="ownmob">
        </div>
        <div class="form-group">
            <label for="petnam">Pet Name</label>
            <input type="text" name="petnam" id="petnam">
        </div>
        <div class="form-group">
            <label for="Pettyp">Pet Type</label>
            <input type="text" name="Pettyp" id="Pettyp">
        </div>
        <div class="form-group">
            <label for="petsp">Pet Species</label>
            <input type="text" name="petsp" id="petsp">
        </div>
        <div class="form-group">
            <label for="year">Year</label>
            <input type="text" name="year" id="year">
        </div>
        <div class="form-group">
            <label for="month">Month</label>
            <input type="text" name="month" id="month">
        </div>
        <div class="form-group">
            <label for="gram">Gram</label>
            <input type="text" name="gram" id="gram">
        </div>
        <div class="form-group">
            <label for="kg">Kg</label>
            <input type="text" name="kg" id="kg">
        </div>
        <button type="submit">Add Customer</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
