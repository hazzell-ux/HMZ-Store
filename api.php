<?php
// api.php
require_once 'config.php';

// Set header JSON di awal
header('Content-Type: application/json');

// Tangkap semua output
ob_start();

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    if (empty($action)) {
        throw new Exception('Action parameter required');
    }

    $admin_actions = ['save_product', 'delete_product', 'get_all_users', 'get_transactions', 'get_transaction'];

    // Check if action requires admin
    if (in_array($action, $admin_actions) && !isAdmin()) {
        throw new Exception('Akses ditolak! Hanya admin yang bisa melakukan tindakan ini.');
    }

    switch ($action) {
        // ========== AUTHENTICATION ==========
        case 'login':
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = $_POST['password'];

            $query = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($conn, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['role'] = $row['role'];

                    echo json_encode([
                        'success' => true,
                        'message' => 'Login berhasil',
                        'redirect' => 'dashboard.php'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Password salah'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email tidak ditemukan'
                ]);
            }
            break;

        case 'get_all_users':
            $query = "SELECT id, username, email, role, created_at FROM users ORDER BY role DESC";
            $result = mysqli_query($conn, $query);

            $users = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $users[] = $row;
            }

            echo json_encode([
                'success' => true,
                'data' => $users
            ]);
            break;

        case 'register':
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $username = explode('@', $email)[0];

            $check_query = "SELECT * FROM users WHERE email = '$email'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email sudah terdaftar'
                ]);
            } else {
                $query = "INSERT INTO users (username, email, password) 
                         VALUES ('$username', '$email', '$password')";

                if (mysqli_query($conn, $query)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Pendaftaran berhasil'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Pendaftaran gagal: ' . mysqli_error($conn)
                    ]);
                }
            }
            break;

        // ========== PRODUCT CRUD ==========
        case 'save_product':
            requireLogin();

            $id = $_POST['id'] ?? 0;
            $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
            $harga = floatval($_POST['harga']);
            $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
            $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
            $stok = intval($_POST['stok']);

            $gambar = '';
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = $_FILES['gambar']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (in_array($ext, $allowed)) {
                    $new_filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $nama_produk) . '.' . $ext;
                    $upload_path = 'images shop/' . $new_filename;

                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                        $gambar = $new_filename;
                    }
                }
            }

            if ($id > 0) {
                if ($gambar) {
                    $query = "UPDATE produk SET 
                             nama_produk = '$nama_produk',
                             harga = $harga,
                             deskripsi = '$deskripsi',
                             kategori = '$kategori',
                             stok = $stok,
                             gambar = '$gambar'
                             WHERE id = $id";
                } else {
                    $query = "UPDATE produk SET 
                             nama_produk = '$nama_produk',
                             harga = $harga,
                             deskripsi = '$deskripsi',
                             kategori = '$kategori',
                             stok = $stok
                             WHERE id = $id";
                }
            } else {
                $query = "INSERT INTO produk (nama_produk, harga, deskripsi, kategori, stok, gambar) 
                         VALUES ('$nama_produk', $harga, '$deskripsi', '$kategori', $stok, '$gambar')";
            }

            if (mysqli_query($conn, $query)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Produk berhasil disimpan'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . mysqli_error($conn)
                ]);
            }
            break;

        case 'get_product':
            $id = intval($_GET['id']);
            $query = "SELECT * FROM produk WHERE id = $id";
            $result = mysqli_query($conn, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                echo json_encode([
                    'success' => true,
                    'data' => $row
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ]);
            }
            break;

        case 'get_products':
            $query = "SELECT * FROM produk ORDER BY created_at DESC";
            $result = mysqli_query($conn, $query);

            $products = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }

            echo json_encode([
                'success' => true,
                'data' => $products
            ]);
            break;

        case 'delete_product':
            requireLogin();

            $id = intval($_GET['id']);
            $query = "DELETE FROM produk WHERE id = $id";

            if (mysqli_query($conn, $query)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Produk berhasil dihapus'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . mysqli_error($conn)
                ]);
            }
            break;

        // ========== CHECKOUT SYSTEM ==========
        case 'checkout':
            requireLogin();

            // Validasi input
            if (!isset($_POST['items']) || empty($_POST['items'])) {
                throw new Exception('Items tidak boleh kosong');
            }

            if (!isset($_POST['total_harga']) || empty($_POST['total_harga'])) {
                throw new Exception('Total harga tidak boleh kosong');
            }

            if (!isset($_POST['metode_pembayaran']) || empty($_POST['metode_pembayaran'])) {
                throw new Exception('Metode pembayaran harus dipilih');
            }

            $user_id = $_SESSION['user_id'];
            $username = $_SESSION['username'];

            // Bersihkan total harga
            $total_harga = $_POST['total_harga'];
            $total_harga = preg_replace('/[^0-9]/', '', $total_harga);
            $total_harga = floatval($total_harga);

            if ($total_harga <= 0) {
                throw new Exception('Total harga tidak valid');
            }

            $metode_pembayaran = mysqli_real_escape_string($conn, $_POST['metode_pembayaran']);
            $items_json = $_POST['items'];

            // Validasi JSON
            $items_decoded = json_decode($items_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Format items tidak valid: ' . json_last_error_msg());
            }

            if (!is_array($items_decoded) || count($items_decoded) === 0) {
                throw new Exception('Keranjang kosong');
            }

            // Simpan transaksi
            $escaped_items = mysqli_real_escape_string($conn, $items_json);
            $query = "INSERT INTO transaksi (user_id, username, total_harga, metode_pembayaran, items) 
                      VALUES ($user_id, '$username', $total_harga, '$metode_pembayaran', '$escaped_items')";

            if (!mysqli_query($conn, $query)) {
                throw new Exception('Gagal menyimpan transaksi: ' . mysqli_error($conn));
            }

            $transaksi_id = mysqli_insert_id($conn);

            // Kurangi stok produk
            foreach ($items_decoded as $item) {
                if (!isset($item['id']) || !isset($item['quantity'])) {
                    continue;
                }

                $product_id = intval($item['id']);
                $quantity = intval($item['quantity']);

                if ($product_id > 0 && $quantity > 0) {
                    $update_stok = "UPDATE produk SET stok = GREATEST(0, stok - $quantity) WHERE id = $product_id";
                    mysqli_query($conn, $update_stok);
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Checkout berhasil! ID Transaksi: TRX-' . str_pad($transaksi_id, 6, '0', STR_PAD_LEFT),
                'transaksi_id' => $transaksi_id
            ]);
            break;

        case 'get_user_orders':
            requireLogin();

            $user_id = $_SESSION['user_id'];
            $query = "SELECT * FROM transaksi WHERE user_id = $user_id ORDER BY created_at DESC";
            $result = mysqli_query($conn, $query);

            $orders = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $row['items'] = json_decode($row['items'], true);
                $orders[] = $row;
            }

            echo json_encode([
                'success' => true,
                'data' => $orders
            ]);
            break;

        case 'get_transactions':
            requireAdmin();

            $query = "SELECT * FROM transaksi ORDER BY created_at DESC";
            $result = mysqli_query($conn, $query);

            $transactions = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $row['items'] = json_decode($row['items'], true);
                $transactions[] = $row;
            }

            echo json_encode([
                'success' => true,
                'data' => $transactions
            ]);
            break;

        case 'get_transaction':
            requireAdmin();

            $id = intval($_GET['id']);
            $query = "SELECT * FROM transaksi WHERE id = $id";
            $result = mysqli_query($conn, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                $row['items'] = json_decode($row['items'], true);
                echo json_encode([
                    'success' => true,
                    'data' => $row
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan'
                ]);
            }
            break;

        // Tambahkan case ini di switch statement di api.php (setelah case 'get_transaction'):

        case 'update_transaction_status':
            requireAdmin();

            $id = intval($_POST['id']);
            $status = mysqli_real_escape_string($conn, $_POST['status']);

            // Validasi status
            $allowed_statuses = ['pending', 'diproses', 'selesai', 'dibatalkan'];
            if (!in_array($status, $allowed_statuses)) {
                throw new Exception('Status tidak valid');
            }

            $query = "UPDATE transaksi SET status = '$status' WHERE id = $id";

            if (mysqli_query($conn, $query)) {
                // Jika status diubah menjadi selesai, bisa tambahkan log atau notifikasi
                if ($status === 'selesai') {
                    // Log aktivitas
                    $log_query = "INSERT INTO activity_log (admin_id, action, details) 
                         VALUES ({$_SESSION['user_id']}, 'complete_transaction', 
                         'Menyelesaikan transaksi ID: $id')";
                    mysqli_query($conn, $log_query);
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Status transaksi berhasil diperbarui'
                ]);
            } else {
                throw new Exception('Gagal mengupdate status: ' . mysqli_error($conn));
            }
            break;

        // Tambahkan di api.php setelah case 'update_transaction_status':
        case 'complete_transaction':
            requireAdmin();

            $id = intval($_POST['id']);

            // Update status menjadi selesai
            $query = "UPDATE transaksi SET status = 'selesai' WHERE id = $id";

            if (mysqli_query($conn, $query)) {
                // Log aktivitas (opsional)
                echo json_encode([
                    'success' => true,
                    'message' => 'Transaksi berhasil diselesaikan'
                ]);
            } else {
                throw new Exception('Gagal menyelesaikan transaksi: ' . mysqli_error($conn));
            }
            break;

        default:
            throw new Exception('Action tidak valid: ' . $action);
    }

} catch (Exception $e) {
    // Clear any output
    ob_clean();

    // Return error as JSON
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Clean output buffer
ob_end_flush();
mysqli_close($conn);
?>