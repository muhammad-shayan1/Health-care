<?php
include("../connect.php");
session_start();

// Agar already login ho chuka ho
if (isset($_SESSION["admin_email"])) {
    header("Location: dashboard.php");
    exit();
}

$error_login = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_email = trim($_POST['email'] ?? '');
    $admin_password = trim($_POST['password'] ?? '');
    // Email validation
    if (empty($admin_email)) {
        $error_login = "Email is required.";
    } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $error_login = "Invalid email format.";
    }
    // Password validation
    elseif (empty($admin_password)) {
        $error_login = "Password is required.";
    } 
    else {
        // Check email in database
        $stmt = $connect->prepare("SELECT admin_id, admin_password FROM admin WHERE admin_email = ?");
        $stmt->bind_param("s", $admin_email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($admin_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($admin_password, $hashed_password)) {
                // Login success
                $_SESSION['admin_email'] = $admin_email;
                header("Location: dashboard.php");
                exit();
            } else {
                $error_login = "Incorrect password.";
            }
        } else {
            $error_login = "Email not registered.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(to right, #0d6efd, #0dcaf0);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-card {
    width: 100%;
    padding: 20px;
    max-width: 450px;
    border-radius: 15px;
}

.card-header {
    background-color: #0d6efd;
    color: white;
    text-align: center;
    font-weight: 600;
}

.btn-primary {
    width: 100%;
}
</style>
</head>

<body>

<div class="card shadow login-card">
    <div class="card-header">
        🏥 Medical Admin Login
    </div>

    <div class="card-body p-4">

        <?php if(!empty($error_login)): ?>
            <div class="alert alert-danger text-center">
                <?= $error_login ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>

            <div class="text-center mt-3">
                Don't have an account?
                <a href="admin_register.php" class="text-decoration-none">Register</a>
            </div>

        </form>
    </div>
</div>

</body>
</html>
