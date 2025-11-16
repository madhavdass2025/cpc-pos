<?php
require_once 'includes/session.php';
require_login();
require_role(1); // 1 = Admin

require_once 'includes/db_connect.php';
require_once 'includes/classes/User.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User($conn);
    $user->name = $_POST['name'];
    $user->email = $_POST['email'];
    $user->password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user->role_id = $_POST['role_id'];

    if ($user->create()) {
        header('Location: users.php');
        exit;
    } else {
        $errors[] = 'Failed to create user.';
    }
}

$roles_stmt = $conn->prepare("SELECT * FROM roles");
$roles_stmt->execute();
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Add New User</h2>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="user_add.php" method="post">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
            <label for="role_id">Role</label>
            <select name="role_id" id="role_id" required>
                <option value="">Select Role</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id']; ?>"><?php echo $role['role_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit">Add User</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
