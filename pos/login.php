<?php
session_start();
require_once 'includes/db_connect.php';

// If the user is already logged in, redirect to the dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $errors[] = 'Email and password are required.';
        } else {
            $stmt = $conn->prepare("SELECT id, name, password_hash, role_id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($password, $user['password_hash'])) {
                    // Password is correct, start a new session
                    session_regenerate_id();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['user_name'] = $user['name'];
                    header('Location: index.php');
                    exit;
                } else {
                    $errors[] = 'Invalid email or password.';
                }
            } else {
                $errors[] = 'Invalid email or password.';
            }
        }
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>
<?php include 'includes/header.php'; ?>

<div class="login-container">
    <h2>Login</h2>
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="login.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include 'includes/footer.php'; ?>
