<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            // Set session data
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            
            // Redirect based on role
            if ($row['role'] === 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = 'Password salah';
        }
    } else {
        $error = 'Email tidak ditemukan';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HMZ Store</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" href="images/favi.ico" type="image/x-icon">
    <style>
        .role-info {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .role-info h4 {
            margin: 0 0 10px 0;
            color: white;
        }
        
        .role-info ul {
            margin: 0;
            padding-left: 20px;
            color: rgba(255,255,255,0.9);
        }
        
        .role-info li {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <form id="login-form" class="login-form" method="POST">
        <h1 class="login-title">Login</h1>
        
        <?php if($error): ?>
            <div style="background: rgba(220,53,69,0.2); color: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php 
        $flash = getFlashMessage();
        if ($flash): ?>
            <div style="background: rgba(40,167,69,0.2); color: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="input-box">
            <i class='bx bxs-envelope'></i>
            <input type="email" id="loginEmail" name="email" placeholder="Email" required>
        </div>
        <div class="input-box">
            <i class='bx bxs-lock-alt'></i>
            <input type="password" id="loginPassword" name="password" placeholder="Password" required>
        </div>

        <button class="login-btn" type="submit">Login</button>

        <p class="register">
            Don't have an account?
            <a href="register.php">Register</a>
        </p>
        <p class="register">
            <a href="index.php">Continue as Guest</a>
        </p>
    </form>

    <script src="app.js"></script>
</body>
</html>