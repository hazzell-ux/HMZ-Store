<?php
// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "ux_pak_rizal";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    // Untuk API, return JSON error
    if (isset($_GET['action']) || isset($_POST['action'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Database connection error'
        ]);
        exit();
    }
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isUser() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['error'] = "Akses ditolak! Hanya admin yang bisa mengakses halaman ini.";
        header("Location: index.php");
        exit();
    }
}

function requireUser() {
    requireLogin();
    if (!isUser()) {
        $_SESSION['error'] = "Akses ditolak!";
        header("Location: index.php");
        exit();
    }
}

function getUserRole() {
    return $_SESSION['role'] ?? 'guest';
}

function getUserData() {
    global $conn;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT id, username, email, role, created_at FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_fetch_assoc($result);
}

function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function createDefaultAdmin($conn) {
    $check = "SELECT * FROM users WHERE role = 'admin' LIMIT 1";
    $result = mysqli_query($conn, $check);
    
    if (mysqli_num_rows($result) == 0) {
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, email, password, role) 
                  VALUES ('admin', 'admin@hmzstore.com', '$password', 'admin')";
        mysqli_query($conn, $query);
        
        $user_pass = password_hash('user123', PASSWORD_DEFAULT);
        $query2 = "INSERT INTO users (username, email, password, role) 
                   VALUES ('user', 'user@hmzstore.com', '$user_pass', 'user')";
        mysqli_query($conn, $query2);
    }
}

// Buat tabel transaksi jika belum ada
function createTransactionsTable($conn) {
    $check = "SHOW TABLES LIKE 'transaksi'";
    $result = mysqli_query($conn, $check);
    
    if (mysqli_num_rows($result) == 0) {
        $sql = "CREATE TABLE transaksi (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            username VARCHAR(100),
            total_harga DECIMAL(15,2) NOT NULL,
            metode_pembayaran VARCHAR(50),
            items TEXT,
            status ENUM('pending', 'diproses', 'selesai', 'dibatalkan') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        mysqli_query($conn, $sql);
    }
}

createDefaultAdmin($conn);
createTransactionsTable($conn);

date_default_timezone_set('Asia/Jakarta');

// Tambahkan fungsi ini sebelum penutup 

function terbilang($x) {
    $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
    
    if ($x < 12)
        return " " . $angka[$x];
    elseif ($x < 20)
        return terbilang($x - 10) . " belas";
    elseif ($x < 100)
        return terbilang($x / 10) . " puluh" . terbilang($x % 10);
    elseif ($x < 200)
        return " seratus" . terbilang($x - 100);
    elseif ($x < 1000)
        return terbilang($x / 100) . " ratus" . terbilang($x % 100);
    elseif ($x < 2000)
        return " seribu" . terbilang($x - 1000);
    elseif ($x < 1000000)
        return terbilang($x / 1000) . " ribu" . terbilang($x % 1000);
    elseif ($x < 1000000000)
        return terbilang($x / 1000000) . " juta" . terbilang($x % 1000000);
}
?>