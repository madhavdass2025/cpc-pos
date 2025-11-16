<?php
require_once 'includes/session.php';
require_login();
require_role(1); // 1 = Admin

require_once 'includes/db_connect.php';
require_once 'includes/classes/User.php';

$user = new User($conn);
$stmt = $user->read();
$num = $stmt->rowCount();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="header">
        <h2>User Management</h2>
        <a href="user_add.php" class="btn btn-primary">Add New User</a>
    </div>

    <?php if ($num > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php extract($row); ?>
                    <tr>
                        <td><?php echo $name; ?></td>
                        <td><?php echo $email; ?></td>
                        <td><?php echo $role_name; ?></td>
                        <td>
                            <a href="user_edit.php?id=<?php echo $id; ?>" class="btn btn-secondary">Edit</a>
                            <a href="user_delete.php?id=<?php echo $id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
