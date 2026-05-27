<?php
require_once 'config.php';
requireAdmin();

// Handle add new admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_admin'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $username = explode('@', $email)[0];
    
    $query = "INSERT INTO users (username, email, password, role) 
              VALUES ('$username', '$email', '$password', 'admin')";
    
    if (mysqli_query($conn, $query)) {
        setFlashMessage('success', 'Admin berhasil ditambahkan');
        header("Location: admin_management.php");
        exit();
    } else {
        $error = "Gagal menambahkan admin: " . mysqli_error($conn);
    }
}

// Handle delete user
if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    
    // Can't delete self
    if ($user_id != $_SESSION['user_id']) {
        $query = "DELETE FROM users WHERE id = $user_id";
        if (mysqli_query($conn, $query)) {
            setFlashMessage('success', 'User berhasil dihapus');
            header("Location: admin_management.php");
            exit();
        }
    }
}

// Get all users
$users_query = "SELECT id, username, email, role, created_at FROM users ORDER BY role DESC, created_at DESC";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - HMZ Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #D2D2D2;
            background-image:
                repeating-linear-gradient(to right, transparent 0 100px,
                    #25283b22 100px 101px),
                repeating-linear-gradient(to bottom, transparent 0 100px,
                    #25283b22 100px 101px);
            font-family: 'Poppins', sans-serif;
        }

        /* Navbar */
        nav {
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        nav .logo {
            display: flex;
            align-items: center;
        }

        nav .logo img {
            height: 60px;
            width: auto;
            margin-right: 10px;
        }

        nav .logo h1 {
            font-size: 2rem;
            background: linear-gradient(to right, black 0%, white 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        nav ul li a {
            text-decoration: none;
            color: white;
            font-size: 1rem;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        nav ul li:nth-child(1) a {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        nav ul li:nth-child(1) a:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        nav ul li:nth-child(2) a {
            color: #333;
        }

        nav ul li:nth-child(2) a:hover {
            background-color: #f0f0f0;
        }

        nav ul li:nth-child(3) a {
            background: #dc3545;
        }

        nav ul li:nth-child(3) a:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .card-header h2 {
            color: #333;
            font-size: 1.8rem;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.9rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .role-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .role-admin {
            background: #d4edda;
            color: #155724;
        }

        .role-user {
            background: #cce5ff;
            color: #004085;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border 0.3s, box-shadow 0.3s;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .current-user {
            background: #fff3cd;
        }

        .flash-success {
            border-radius: 10px !important;
            padding: 20px !important;
            margin-bottom: 20px !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }

        .flash-error {
            border-radius: 10px !important;
            padding: 20px !important;
            margin-bottom: 20px !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">
            <img src="images/logo.png" alt="logo" />
            <h1>HMZ Store - User Management</h1>
        </div>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a></li>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <!-- Flash Message -->
        <?php 
        $flash = getFlashMessage();
        if ($flash): ?>
            <div class="card flash-<?php echo $flash['type']; ?>" style="background: <?php echo $flash['type'] === 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo $flash['type'] === 'success' ? '#155724' : '#721c24'; ?>;">
                <i class="fas <?php echo $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <!-- Add New Admin Form -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-user-plus"></i> Tambah Admin Baru</h2>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email Admin</label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           placeholder="admin@example.com">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required 
                           placeholder="Minimal 6 karakter">
                </div>
                <button type="submit" name="add_admin" class="btn btn-primary">
                    <i class="fas fa-save"></i> Tambah Admin
                </button>
            </form>
        </div>

        <!-- Users List -->
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-users"></i> Daftar User (<?php echo mysqli_num_rows($users_result); ?>)</h2>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = mysqli_fetch_assoc($users_result)): ?>
                        <tr <?php echo $user['id'] == $_SESSION['user_id'] ? 'class="current-user"' : ''; ?>>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="role-badge role-<?php echo $user['role']; ?>">
                                    <i class="fas <?php echo $user['role'] == 'admin' ? 'fa-crown' : 'fa-user'; ?>"></i>
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="?delete_user=<?php echo $user['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Hapus user <?php echo htmlspecialchars($user['username']); ?>?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                <?php else: ?>
                                    <span style="color: #6c757d; font-style: italic;">Akun Anda</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>