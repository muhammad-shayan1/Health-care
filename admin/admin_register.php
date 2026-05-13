<?php
include("../connect.php");
session_start();

if (!isset($_SESSION["admin_email"])) {
    header("Location: dashboard.php");
    exit();
}

$error_admin = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $admin_name = trim($_POST['name'] ?? '');
    $admin_email = trim($_POST['email'] ?? '');
    $admin_password = trim($_POST['password'] ?? '');
    $admin_confirmPassword = trim($_POST['confirmPassword'] ?? '');

    // Name Validation
    if (empty($admin_name)) {
        $error_admin = "Name is required.";
    } elseif (!preg_match("/^[a-zA-Z ]{3,30}$/", $admin_name)) {
        $error_admin = "Name must be 3-30 letters only.";
    }

    // Email Validation
    elseif (empty($admin_email)) {
        $error_admin = "Email is required.";
    } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $error_admin = "Invalid email format.";
    }

    // Password Validation
    elseif (empty($admin_password)) {
        $error_admin = "Password is required.";
    } elseif (strlen($admin_password) < 6) {
        $error_admin = "Password must be at least 6 characters.";
    }

    elseif ($admin_password !== $admin_confirmPassword) {
        $error_admin = "Passwords do not match.";
    }

    else {

        // Check duplicate email
        $stmt = $connect->prepare("SELECT admin_id FROM admin WHERE admin_email = ?");
        $stmt->bind_param("s", $admin_email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_admin = "Email already registered.";
        } else {

            $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

            $insert = $connect->prepare("INSERT INTO admin (admin_name, admin_email, admin_password) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $admin_name, $admin_email, $hashed_password);

            if ($insert->execute()) {
                $_SESSION['admin_email'] = $admin_email;
                header("Location: dashboard.php");
                exit();
            } else {
                $error_admin = "Something went wrong.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(to right, #0d6efd, #0dcaf0);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.register-card {
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
<div class="card shadow register-card">
    <div class="card-header">
        🏥 Medical Admin Registration
    </div>
    <div class="card-body p-4">
        <?php if(!empty($error_admin)): ?>
            <div class="alert alert-danger text-center">
                <?= $error_admin ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirmPassword" class="form-control" placeholder="Confirm password" required>
            </div>

            <button type="submit" class="btn btn-primary">Register</button>

            <div class="text-center mt-3">
                Already have an account?
                <a href="login.php" class="text-decoration-none">Login</a>
            </div>

        </form>
    </div>
</div>

</body>
</html>
