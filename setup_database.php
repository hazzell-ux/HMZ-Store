<?php
// setup_database.php
echo "<h2>Setup Database HMZ Store</h2>";

// Database configuration
$host = "localhost";
$username = "root";
$password = "";

// Connect to MySQL server
$conn = mysqli_connect($host, $username, $password);

if (!$conn) {
    die("Koneksi MySQL gagal: " . mysqli_connect_error());
}

echo "✓ Terhubung ke MySQL server<br>";

// Create database
$database = "ux_pak_rizal";
$sql = "CREATE DATABASE IF NOT EXISTS $database 
        CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";

if (mysqli_query($conn, $sql)) {
    echo "✓ Database '$database' berhasil dibuat/ada<br>";
} else {
    die("✗ Gagal membuat database: " . mysqli_error($conn));
}

// Select database
mysqli_select_db($conn, $database);

// SQL untuk membuat tabel
$sql_tables = "
-- Table users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table produk
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(255) NOT NULL,
    harga DECIMAL(15,2) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255),
    kategori VARCHAR(100),
    stok INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table transaksi
CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(100),
    total_harga DECIMAL(15,2) NOT NULL,
    metode_pembayaran VARCHAR(50),
    items TEXT,
    status ENUM('pending', 'diproses', 'selesai', 'dibatalkan') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Execute SQL
if (mysqli_multi_query($conn, $sql_tables)) {
    do {
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    
    echo "✓ Tabel berhasil dibuat<br>";
} else {
    echo "✗ Gagal membuat tabel: " . mysqli_error($conn) . "<br>";
}

// Insert default admin user
$check_admin = "SELECT * FROM users WHERE username = 'admin'";
$result = mysqli_query($conn, $check_admin);

if (mysqli_num_rows($result) == 0) {
    $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
    $insert_admin = "INSERT INTO users (username, email, password, role) 
                    VALUES ('admin', 'admin@hmzstore.com', '$hashed_password', 'admin')";
    
    if (mysqli_query($conn, $insert_admin)) {
        echo "✓ User admin berhasil dibuat<br>";
        echo "&nbsp;&nbsp;- Email: admin@hmzstore.com<br>";
        echo "&nbsp;&nbsp;- Password: admin123<br>";
    }
}

// Insert sample products
$check_products = "SELECT COUNT(*) as total FROM produk";
$result = mysqli_query($conn, $check_products);
$row = mysqli_fetch_assoc($result);

if ($row['total'] == 0) {
    $products_sql = "
    INSERT INTO produk (nama_produk, harga, deskripsi, kategori, stok) VALUES
    ('Naga Api Merah', 13000000, 'Naga dengan elemen api murni', 'Fire Dragon', 5),
    ('Naga Air Biru', 10000000, 'Naga penguasa lautan', 'Water Dragon', 8),
    ('Naga Tanah Hijau', 15000000, 'Naga penjaga pegunungan', 'Earth Dragon', 3),
    ('Naga Angin Putih', 20000000, 'Naga penguasa angin', 'Air Dragon', 4),
    ('Naga Petir Emas', 18000000, 'Naga pembawa petir', 'Lightning Dragon', 6),
    ('Naga Es Perak', 23000000, 'Naga penguasa es', 'Ice Dragon', 2),
    ('Naga Legenda Hitam', 25000000, 'Naga legenda dari zaman purba', 'Legendary Dragon', 1),
    ('Naga Bonar', 1000000000, 'Naga paling kuat dalam sejarah', 'Legendary Dragon', 1);
    ";
    
    if (mysqli_multi_query($conn, $products_sql)) {
        do {
            if ($result = mysqli_store_result($conn)) {
                mysqli_free_result($result);
            }
        } while (mysqli_next_result($conn));
        
        echo "✓ 8 produk sample berhasil ditambahkan<br>";
    }
}

// Display summary
echo "<hr><h3>Database Summary:</h3>";

$tables = ['users', 'produk', 'transaksi'];
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM $table");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "• Tabel <strong>$table</strong>: " . $row['total'] . " records<br>";
    } else {
        echo "• Tabel <strong>$table</strong>: ERROR<br>";
    }
}

echo "<hr>";
echo "<h3 style='color: green;'>✓ Setup database selesai!</h3>";
echo "<p>Silakan akses:</p>";
echo "<ul>";
echo "<li><a href='index.php'>Home Page</a></li>";
echo "<li><a href='login.php'>Login Admin</a> (admin@hmzstore.com / admin123)</li>";
echo "<li><a href='login.php'>Login User</a> (user@hmzstore.com / user123)</li>";
echo "<li><a href='about.php'>Shop Page</a></li>";
echo "<li><a href='dashboard.php'>Admin Dashboard</a></li>";
echo "</ul>";

mysqli_close($conn);
?>