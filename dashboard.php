<?php
require_once 'config.php';
requireAdmin();

// Get all products
$query = "SELECT * FROM produk ORDER BY id DESC";

$result = mysqli_query($conn, $query);

// Get stats
$total_products = mysqli_num_rows($result);
$query_stock = "SELECT SUM(stok) as total_stok FROM produk";
$result_stock = mysqli_query($conn, $query_stock);
$total_stock = mysqli_fetch_assoc($result_stock)['total_stok'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HMZ Store</title>
    <link rel="shortcut icon" href="images/favi.ico" type="image/x-icon">
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
        }

        nav ul li a {
            text-decoration: none;
            color: #000;
            font-size: 1rem;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        nav ul li a:hover {
            background-color: #f0f0f0;
        }

        nav ul li:last-child a {
            background: #dc3545;
            color: white;
        }

        nav ul li:last-child a:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        /* Dashboard Container */
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #667eea;
        }

        .stat-card h3 {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
        }

        /* CRUD Section */
        .crud-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .section-header h2 {
            color: #333;
            font-size: 1.8rem;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
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
        }

        .btn-warning {
            background: #ffc107;
            color: #333;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .product-image-small {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-available {
            background: #d4edda;
            color: #155724;
        }

        .status-soldout {
            background: #f8d7da;
            color: #721c24;
        }

        /* Modal Sidebar - Tampil dari Samping Kanan */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: flex-end;
            /* Ubah ke flex-end untuk dari kanan */
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 500px;
            height: 100vh;
            /* Full height */
            border-radius: 0;
            /* Hilangkan border radius */
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            /* Mulai dari luar layar */
            animation: slideIn 0.3s forwards;
            overflow-y: auto;
            /* Tambahkan scroll jika konten panjang */
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(100%);
            }
        }

        /* Modal Header Tetap di Atas */
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: white;
            z-index: 2;
        }

        .modal-header h3 {
            margin: 0;
            color: #333;
            font-size: 1.5rem;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: #666;
            padding: 5px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }

        .close-modal:hover {
            background: #f5f5f5;
        }

        /* Modal Body - Isi Form */
        .modal-body {
            padding: 20px;
            flex: 1;
            /* Ambil sisa space */
            overflow-y: auto;
        }

        /* Modal Footer Tetap di Bawah */
        .modal-footer {
            padding: 20px;
            border-top: 1px solid #dee2e6;
            background: white;
            position: sticky;
            bottom: 0;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            z-index: 2;
        }

        /* Tombol Aksi */
        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        /* Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 300px;
            max-width: 400px;
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .notification-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-left: 4px solid #155724;
        }

        .notification-error {
            background: linear-gradient(135deg, #dc3545 0%, #e4606d 100%);
            color: white;
            border-left: 4px solid #721c24;
        }

        .notification-info {
            background: linear-gradient(135deg, #17a2b8 0%, #3dd5f3 100%);
            color: white;
            border-left: 4px solid #0c5460;
        }

        .notification-content {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .notification-content i {
            font-size: 1.2rem;
        }

        .notification-close {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px;
            margin-left: 15px;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .notification-close:hover {
            opacity: 1;
        }

        /* Responsive notification */
        @media (max-width: 480px) {
            .notification {
                left: 20px;
                right: 20px;
                min-width: auto;
                max-width: none;
            }
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
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

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
            font-family: inherit;
        }

        /* Select Styling */
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            padding-right: 45px;
            cursor: pointer;
        }

        /* File Upload Styling */
        .file-upload {
            position: relative;
            overflow: hidden;
        }

        .file-upload input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: block;
            padding: 12px 15px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #6c757d;
        }

        .file-upload-label:hover {
            background: #e9ecef;
            border-color: #667eea;
        }

        .file-upload-label i {
            margin-right: 8px;
            color: #667eea;
        }

        /* Image Preview */
        .preview-container {
            margin-top: 15px;
            text-align: center;
        }

        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .preview-placeholder {
            width: 100%;
            height: 150px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }


        .category-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .category-option {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .category-option:hover {
            border-color: #667eea;
            background: #f8f9fa;
        }

        .category-option input[type="radio"] {
            margin-right: 10px;
            accent-color: #667eea;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .modal-content {
                width: 100%;
                max-width: 100%;
            }

            .category-options {
                grid-template-columns: repeat(2, 1fr);
            }

            .modal-footer {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            .modal-header {
                padding: 15px;
            }

            .modal-body {
                padding: 15px;
            }

            .modal-footer {
                padding: 15px;
            }

            .category-options {
                grid-template-columns: 1fr;
            }
        }

        .modal.closing .modal-content {
            animation: slideOut 0.3s forwards;
        }


        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-diproses {
            background: #cce5ff;
            color: #004085;
        }

        .status-selesai {
            background: #d4edda;
            color: #155724;
        }

        .status-dibatalkan {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-success {
            background: #28a745 !important;
            color: white !important;
            border: none !important;
            padding: 8px 15px !important;
            border-radius: 5px !important;
            cursor: pointer !important;
            font-weight: 600 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            transition: all 0.3s !important;
        }

        .btn-success:hover {
            background: #218838 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3) !important;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav>
        <div class="logo">
            <img src="images/logo.png" alt="logo" />
            <h1>HMZ Store - Admin Panel</h1>
        </div>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="about.php"><i class="fas fa-shop"></i> Shop</a></li>
            <li style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; 
            border-radius: 20px; padding: 5px 12px; font-size: 0.9rem; font-weight: 600; margin-left: 10px;
                 font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                <b><i class="fas fa-crown"></i> <?php echo $_SESSION['username']; ?></b>
            </li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Stats -->
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <h3>Total Produk</h3>
                <div class="number"><?php echo $total_products; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-layer-group"></i>
                <h3>Total Stok</h3>
                <div class="number"><?php echo $total_stock; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-user"></i>
                <h3>Admin</h3>
                <div class="number"><?php echo $_SESSION['username'] ?? 'Admin'; ?></div>
            </div>
        </div>

        <!-- CRUD Section -->
        <div class="crud-section">
            <div class="section-header">
                <h2><i class="fas fa-dragon"></i> Kelola Produk Naga</h2>
                <div style="display: flex; gap: 10px;">
                    <a href="admin_management.php" class="btn" style="background: #17a2b8; color: white;">
                        <i class="fas fa-users-cog"></i> Manage Users
                    </a>
                    <button class="btn btn-primary" onclick="openModal()">
                        <i class="fas fa-plus"></i> Tambah Produk
                    </button>
                </div>
            </div>

            <div class="table-container">
                <table id="productsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td>
                                        <?php if (!empty($row['gambar'])): ?>
                                            <img src="images shop/<?php echo $row['gambar']; ?>" class="product-image-small"
                                                alt="<?php echo $row['nama_produk']; ?>">
                                        <?php else: ?>
                                            <i class="fas fa-image" style="color: #ccc;"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo $row['stok']; ?></td>
                                    <td>
                                        <span
                                            class="status-badge <?php echo $row['stok'] > 0 ? 'status-available' : 'status-soldout'; ?>">
                                            <?php echo $row['stok'] > 0 ? 'Tersedia' : 'Habis'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-warning btn-sm"
                                                onclick="editProduct(<?php echo $row['id']; ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="deleteProduct(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['nama_produk'])); ?>')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px;">
                                    <p>Tidak ada produk. Silakan tambah produk baru.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Sidebar for Add/Edit Product -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Tambah Produk Naga</h3>
                <button class="close-modal" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="productForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="productId" name="id">

                    <!-- Nama Produk -->
                    <div class="form-group">
                        <label for="nama_produk">
                            <i class="fas fa-dragon"></i> Nama Naga *
                        </label>
                        <input type="text" id="nama_produk" name="nama_produk" class="form-control" required
                            placeholder="Contoh: Naga Api, Naga Emas, dll.">
                    </div>

                    <!-- Harga -->
                    <div class="form-group">
                        <label for="harga">
                            <i class="fas fa-tag"></i> Harga (Rp) *
                        </label>
                        <input type="number" id="harga" name="harga" class="form-control" min="0" required
                            placeholder="Contoh: 15000000">
                    </div>

                    <!-- Deskripsi -->
                    <div class="form-group">
                        <label for="deskripsi">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"
                            placeholder="Deskripsi tentang naga ini..."></textarea>
                    </div>

                    <!-- Kategori dengan Radio Buttons -->
                    <div class="form-group">
                        <label>
                            <i class="fas fa-layer-group"></i> Kategori
                        </label>
                        <div class="category-options">
                            <label class="category-option">
                                <input type="radio" name="kategori" value="Fire Dragon" checked>
                                <span>🔥 Fire Dragon</span>
                            </label>
                            <label class="category-option">
                                <input type="radio" name="kategori" value="Water Dragon">
                                <span>💧 Water Dragon</span>
                            </label>
                            <label class="category-option">
                                <input type="radio" name="kategori" value="Earth Dragon">
                                <span>🌍 Earth Dragon</span>
                            </label>
                            <label class="category-option">
                                <input type="radio" name="kategori" value="Air Dragon">
                                <span>💨 Air Dragon</span>
                            </label>
                            <label class="category-option">
                                <input type="radio" name="kategori" value="Lightning Dragon">
                                <span>⚡ Lightning Dragon</span>
                            </label>
                            <label class="category-option">
                                <input type="radio" name="kategori" value="Ice Dragon">
                                <span>❄️ Ice Dragon</span>
                            </label>
                            <label class="category-option">
                                <input type="radio" name="kategori" value="Legendary Dragon">
                                <span>🌟 Legendary Dragon</span>
                            </label>
                        </div>
                    </div>

                    <!-- Stok -->
                    <div class="form-group">
                        <label for="stok">
                            <i class="fas fa-box"></i> Stok *
                        </label>
                        <input type="number" id="stok" name="stok" class="form-control" min="0" value="1" required
                            placeholder="Jumlah stok yang tersedia">
                    </div>

                    <!-- Upload Gambar -->
                    <div class="form-group">
                        <label>
                            <i class="fas fa-image"></i> Gambar Naga
                        </label>

                        <div class="file-upload">
                            <input type="file" id="gambar" name="gambar" accept="image/*"
                                onchange="previewImage(event)">
                            <label for="gambar" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span id="fileLabel">Pilih File Gambar</span>
                            </label>
                        </div>

                        <div class="preview-container">
                            <div id="imagePlaceholder" class="preview-placeholder">
                                <i class="fas fa-image fa-2x"></i>
                                <p style="margin-top: 10px; font-size: 0.9rem;">
                                    Preview gambar akan muncul di sini
                                </p>
                            </div>
                            <img id="imagePreview" class="preview-image" alt="Preview Gambar">
                        </div>

                        <small style="display: block; margin-top: 8px; color: #6c757d;">
                            <i class="fas fa-info-circle"></i>
                            Ukuran maksimal 2MB. Format: JPG, PNG, GIF, WebP
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab Transaksi -->
    <div class="crud-section">
        <div class="section-header">
            <h2><i class="fas fa-receipt"></i> Daftar Transaksi</h2>
            <button class="btn btn-primary" onclick="loadTransactions()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>

        <div class="table-container">
            <table id="transactionsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody id="transactionsBody">
                    <!-- Data transaksi akan diisi via JavaScript -->
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px;">
                            <p>Loading data transaksi...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Detail Transaksi -->
    <div id="transactionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="transactionModalTitle">Detail Transaksi</h3>
                <button class="close-modal" onclick="closeTransactionModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="transactionDetails">
                    <p>Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeTransactionModal()">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk preview gambar
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const fileLabel = document.getElementById('fileLabel');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();

                // Update file label
                fileLabel.textContent = file.name;

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                }

                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
                placeholder.style.display = 'flex';
                fileLabel.textContent = 'Pilih File Gambar';
            }
        }

        // Fungsi buka modal dengan animasi
        function openModal(productId = null) {
            const modal = document.getElementById('productModal');
            const title = document.getElementById('modalTitle');
            const form = document.getElementById('productForm');

            // Hapus class closing jika ada
            modal.classList.remove('closing');

            if (productId) {
                title.textContent = 'Edit Produk Naga';
                loadProductData(productId);
            } else {
                title.textContent = 'Tambah Produk Naga';
                form.reset();
                resetImagePreview();
            }

            modal.style.display = 'flex';

            // Scroll ke atas modal
            setTimeout(() => {
                modal.querySelector('.modal-body').scrollTop = 0;
            }, 100);
        }

        // Fungsi reset preview gambar
        function resetImagePreview() {
            const preview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const fileLabel = document.getElementById('fileLabel');

            preview.style.display = 'none';
            placeholder.style.display = 'flex';
            fileLabel.textContent = 'Pilih File Gambar';
            preview.src = '';

            // Reset file input
            document.getElementById('gambar').value = '';
        }

        // Fungsi tutup modal dengan animasi
        function closeModal() {
            const modal = document.getElementById('productModal');

            // Tambah class untuk animasi keluar
            modal.classList.add('closing');

            // Tunggu animasi selesai baru sembunyikan
            setTimeout(() => {
                modal.style.display = 'none';
                modal.classList.remove('closing');
            }, 300);
        }

        // Fungsi load data produk untuk edit
        async function loadProductData(productId) {
            try {
                const response = await fetch(`api.php?action=get_product&id=${productId}`);
                const result = await response.json();

                if (result.success) {
                    const product = result.data;

                    document.getElementById('productId').value = product.id;
                    document.getElementById('nama_produk').value = product.nama_produk;
                    document.getElementById('harga').value = product.harga;
                    document.getElementById('deskripsi').value = product.deskripsi || '';
                    document.getElementById('stok').value = product.stok;

                    // Set kategori radio button
                    const categoryRadios = document.querySelectorAll('input[name="kategori"]');
                    categoryRadios.forEach(radio => {
                        radio.checked = (radio.value === product.kategori);
                    });

                    // Set image preview jika ada gambar
                    const preview = document.getElementById('imagePreview');
                    const placeholder = document.getElementById('imagePlaceholder');
                    const fileLabel = document.getElementById('fileLabel');

                    if (product.gambar) {
                        preview.src = `images shop/${product.gambar}`;
                        preview.style.display = 'block';
                        placeholder.style.display = 'none';
                        fileLabel.textContent = product.gambar;
                    } else {
                        resetImagePreview();
                    }
                }
            } catch (error) {
                alert('Error loading product data: ' + error.message);
            }
        }

        // Handle form submission
        document.getElementById('productForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            // Validasi form
            const nama_produk = document.getElementById('nama_produk').value.trim();
            const harga = document.getElementById('harga').value;
            const stok = document.getElementById('stok').value;

            if (!nama_produk) {
                alert('Nama produk tidak boleh kosong');
                return;
            }

            if (harga <= 0) {
                alert('Harga harus lebih dari 0');
                return;
            }

            if (stok < 0) {
                alert('Stok tidak boleh negatif');
                return;
            }

            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            try {
                const response = await fetch('api.php?action=save_product', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Tampilkan notifikasi sukses
                    showNotification('Produk berhasil disimpan!', 'success');

                    // Tutup modal setelah delay
                    setTimeout(() => {
                        closeModal();
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('Error: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('Error: ' + error.message, 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Simpan';
            }
        });

        // Fungsi untuk menampilkan notifikasi
        function showNotification(message, type = 'info') {
            // Hapus notifikasi sebelumnya jika ada
            const oldNotification = document.querySelector('.notification');
            if (oldNotification) {
                oldNotification.remove();
            }

            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

            document.body.appendChild(notification);

            // Auto remove setelah 5 detik
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById('productModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('productModal');
                if (modal.style.display === 'flex') {
                    closeModal();
                }
            }
        });

        // Fungsi untuk edit produk
        function editProduct(productId) {
            openModal(productId);
        }

        // Fungsi untuk menghapus produk
        async function deleteProduct(productId, productName) {
            if (!confirm(`Apakah Anda yakin ingin menghapus produk "${productName}"?`)) {
                return;
            }

            try {
                // Tampilkan loading
                showNotification('Menghapus produk...', 'info');

                const response = await fetch(`api.php?action=delete_product&id=${productId}`);
                const result = await response.json();

                if (result.success) {
                    showNotification('Produk berhasil dihapus!', 'success');
                    // Reload halaman setelah 1.5 detik
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('Error: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('Error: ' + error.message, 'error');
            }
        }

        // Fungsi untuk memuat transaksi
        async function loadTransactions() {
            try {
                const response = await fetch('api.php?action=get_transactions');
                const result = await response.json();

                const tbody = document.getElementById('transactionsBody');

                if (result.success && result.data.length > 0) {
                    tbody.innerHTML = '';

                    const pendingTransactions = result.data.filter(transaction =>
                        transaction.status === 'pending' || transaction.status === 'diproses'
                    );

                    if (pendingTransactions.length > 0) {
                        pendingTransactions.forEach(transaction => {
                            const row = document.createElement('tr');

                            // Format status badge
                            let statusClass = '';
                            let statusText = '';
                            switch (transaction.status) {
                                case 'pending':
                                    statusClass = 'status-pending';
                                    statusText = 'Pending';
                                    break;
                                case 'diproses':
                                    statusClass = 'status-diproses';
                                    statusText = 'Diproses';
                                    break;
                                default:
                                    statusClass = '';
                                    statusText = transaction.status;
                            }

                            row.innerHTML = `
                        <td>${transaction.id}</td>
                        <td>${transaction.username}</td>
                        <td>Rp ${parseInt(transaction.total_harga).toLocaleString('id-ID')}</td>
                        <td>${transaction.metode_pembayaran}</td>
                        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        <td>${new Date(transaction.created_at).toLocaleString('id-ID')}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button class="btn btn-success btn-sm" onclick="completeTransaction(${transaction.id})">
                                    <i class="fas fa-check-circle"></i> Selesaikan
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="viewTransactionDetails(${transaction.id})">
                                    <i class="fas fa-eye"></i> Lihat
                                </button>
                            </div>
                        </td>
                    `;
                            tbody.appendChild(row);
                        });
                    } else {
                        tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px;">
                            <p>Tidak ada transaksi yang perlu diselesaikan.</p>
                            <small>Semua transaksi sudah selesai!</small>
                        </td>
                    </tr>
                `;
                    }
                } else {
                    tbody.innerHTML = `
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px;">
                        <p>Tidak ada transaksi.</p>
                    </td>
                </tr>
            `;
                }
            } catch (error) {
                console.error('Error loading transactions:', error);
                showNotification('Gagal memuat transaksi', 'error');
            }
        }
        // Fungsi untuk menyelesaikan transaksi
        async function completeTransaction(transactionId) {
            if (!confirm(`Yakin ingin menyelesaikan transaksi #${transactionId}?\nTransaksi akan hilang dari daftar.`)) {
                return;
            }

            try {
                // Tampilkan loading
                showNotification('Menyelesaikan transaksi...', 'info');

                // Kirim request ke API
                const response = await fetch('api.php?action=complete_transaction', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        id: transactionId
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(`✅ Transaksi #${transactionId} selesai!`, 'success');

                    // Hapus baris dari tabel tanpa reload
                    const rows = document.querySelectorAll('#transactionsBody tr');
                    rows.forEach(row => {
                        const idCell = row.cells[0];
                        if (idCell && idCell.textContent == transactionId) {
                            // Animasi fade out
                            row.style.transition = 'opacity 0.5s';
                            row.style.opacity = '0';

                            setTimeout(() => {
                                row.remove();

                                // Cek jika tabel kosong
                                const remainingRows = document.querySelectorAll('#transactionsBody tr');
                                if (remainingRows.length === 0 ||
                                    (remainingRows.length === 1 && remainingRows[0].querySelector('td[colspan="7"]'))) {
                                    // Tampilkan pesan kosong
                                    const tbody = document.getElementById('transactionsBody');
                                    tbody.innerHTML = `
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 30px;">
                                        <p>Tidak ada transaksi yang perlu diselesaikan.</p>
                                        <small>Semua transaksi sudah selesai!</small>
                                    </td>
                                </tr>
                            `;
                                }
                            }, 500);
                        }
                    });
                } else {
                    showNotification('❌ Error: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('❌ Error: ' + error.message, 'error');
            }
        }

        // Fungsi untuk melihat detail transaksi
        async function viewTransactionDetails(transactionId) {
            try {
                const response = await fetch(`api.php?action=get_transaction&id=${transactionId}`);
                const result = await response.json();

                if (result.success) {
                    const transaction = result.data;
                    const detailsDiv = document.getElementById('transactionDetails');
                    const title = document.getElementById('transactionModalTitle');

                    title.textContent = `Detail Transaksi #${transaction.id}`;

                    let itemsHtml = '';
                    if (transaction.items && Array.isArray(transaction.items)) {
                        itemsHtml = transaction.items.map(item => `
                    <div style="display: flex; align-items: center; margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                        <img src="${item.img || 'images/dragon_1.jpg'}" alt="${item.name}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 5px;">
                        <div>
                            <p style="margin: 0; font-weight: bold;">${item.name}</p>
                            <p style="margin: 0; color: #666;">${item.quantity} x Rp ${parseInt(item.price).toLocaleString('id-ID')}</p>
                            <p style="margin: 0; font-weight: bold;">Subtotal: Rp ${(item.quantity * item.price).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                `).join('');
                    }

                    detailsDiv.innerHTML = `
                <div style="margin-bottom: 20px;">
                    <p><strong>ID Transaksi:</strong> ${transaction.id}</p>
                    <p><strong>User:</strong> ${transaction.username}</p>
                    <p><strong>Tanggal:</strong> ${new Date(transaction.created_at).toLocaleString('id-ID')}</p>
                    <p><strong>Metode Pembayaran:</strong> ${transaction.metode_pembayaran}</p>
                    <p><strong>Status:</strong> <span class="status-badge ${transaction.status === 'selesai' ? 'status-available' : transaction.status === 'pending' ? 'status-soldout' : ''}">${transaction.status}</span></p>
                    <p><strong>Total Harga:</strong> <span style="font-size: 1.2rem; font-weight: bold; color: #667eea;">Rp ${parseInt(transaction.total_harga).toLocaleString('id-ID')}</span></p>
                </div>
                <div style="margin-top: 20px;">
                    <h4>Detail Item:</h4>
                    ${itemsHtml || '<p>Tidak ada item</p>'}
                </div>
            `;

                    // Tampilkan modal
                    document.getElementById('transactionModal').style.display = 'flex';
                }
            } catch (error) {
                console.error('Error loading transaction details:', error);
                showNotification('Gagal memuat detail transaksi', 'error');
            }
        }

        // Fungsi untuk menutup modal transaksi
        function closeTransactionModal() {
            const modal = document.getElementById('transactionModal');
            modal.style.display = 'none';
        }

        // Panggil loadTransactions saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            loadTransactions();

            // Tambahkan event listener untuk modal transaksi
            const transactionModal = document.getElementById('transactionModal');
            window.onclick = function (event) {
                if (event.target === transactionModal) {
                    closeTransactionModal();
                }
            }
        });
    </script>

</body>

</html>