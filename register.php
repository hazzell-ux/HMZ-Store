<?php
require_once 'config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $username = explode('@', $email)[0]; // Create username from email
    
    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        // Check if email exists
        $check_query = "SELECT * FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Email sudah terdaftar';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // Default role adalah 'user'
            $query = "INSERT INTO users (username, email, password, role) 
                     VALUES ('$username', '$email', '$hashed_password', 'user')";
            
            if (mysqli_query($conn, $query)) {
                $success = 'Pendaftaran berhasil! Silakan login.';
                // Kosongkan form
                $_POST = array();
            } else {
                $error = 'Pendaftaran gagal: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - HMZ Store</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="images/favi.ico" type="image/x-icon">
    <style>
        .role-notice {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .role-notice p {
            margin: 0;
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <form id="login-form" class="login-form" method="POST">
        <h1 class="login-title">Register</h1>
        
        <?php if($success): ?>
            <div style="background: rgba(40,167,69,0.2); color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
                <?php echo $success; ?>
                <br>
                <small>Redirect ke halaman login dalam 3 detik...</small>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = "login.php";
                }, 3000);
            </script>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div style="background: rgba(220,53,69,0.2); color: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="input-box">
            <i class='bx bxs-envelope'></i>
            <input type="email" id="signUpEmail" name="email" placeholder="Email" required 
                   value="<?php echo $_POST['email'] ?? ''; ?>">
        </div>
        <div class="input-box">
            <i class='bx bxs-lock-alt'></i>
            <input type="password" id="signUpPassword" name="password" placeholder="Password (minimal 6 karakter)" required>
        </div>

        <button class="login-btn" type="submit">Register</button>

        <p class="register">
            Already have an account?
            <a href="login.php">Login</a>
        </p>
    </form>

    <script src="app.js"></script>
</body>
</html>