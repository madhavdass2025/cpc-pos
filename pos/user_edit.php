<?php
require_once 'includes/session.php';
require_login();
require_role(1); // 1 = Admin

require_once 'includes/db_connect.php';
require_once 'includes/classes/User.php';

$errors = [];
$user = new User($conn);

if (isset($_GET['id'])) {
    $user->id = $_GET['id'];
    $user_data = $user->getById($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->id = $_POST['id'];
    $user->name = $_POST['name'];
    $user->email = $_POST['email'];
    $user->role_id = $_POST['role_id'];

    if ($user->update()) {
        header('Location: users.php');
        exit;
    } else {
        $errors[] = 'Failed to update user.';
    }
}

$roles_stmt = $conn->prepare("SELECT * FROM roles");
$roles_stmt->execute();
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h2>Edit User</h2>
    <?php if ($user_data): ?>
        <form action="user_edit.php" method="post">
            <input type="hidden" name="id" value="<?php echo $user_data['id']; ?>">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" value="<?php echo $user_data['name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo $user_data['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="role_id">Role</label>
                <select name="role_id" id="role_id" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>" <?php echo ($user_data['role_id'] == $role['id']) ? 'selected' : ''; ?>>
                            <?php echo $role['role_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Update User</button>
        </form>
    <?php else: ?>
        <p>User not found.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
