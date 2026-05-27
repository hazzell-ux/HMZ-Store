<?php
require_once 'config.php';

$query = "SELECT * FROM produk ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMZ Store - Shop</title>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
        }

        body::before {
            position: absolute;
            width: min(1400px, 90vw);
            top: 10%;
            left: 50%;
            height: 90%;
            transform: translateX(-50%);
            content: '';
            background-image: url(images/bg.png);
            background-size: 100%;
            background-repeat: no-repeat;
            background-position: top center;
            pointer-events: none;
        }

        .main-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
        }

        .products {
            padding-bottom: 20px;
        }

        .user-role-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .role-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .role-user {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .role-guest {
            background: #6c757d;
            color: white;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .product-card {
            background-color: #fff;
            width: calc(25% - 20px);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
            position: relative;
        }

        @media (max-width: 1200px) {
            .product-card {
                width: calc(33.333% - 20px);
            }
        }

        @media (max-width: 768px) {
            .product-card {
                width: calc(50% - 20px);
            }
        }

        @media (max-width: 480px) {
            .product-card {
                width: 100%;
            }
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .product-card h4 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #333;
        }

        .product-card p {
            font-size: 1.1rem;
            margin-bottom: 15px;
            color: #667eea;
            font-weight: bold;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .product-actions button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .product-actions .add-to-cart {
            background-color: #667eea;
            color: white;
        }

        .product-actions .add-to-cart:hover {
            background-color: #764ba2;
        }

        .product-actions .favorite {
            background-color: #ff4757;
            color: white;
        }

        .product-actions .favorite:hover {
            background-color: #ff3838;
        }

        .no-products {
            text-align: center;
            padding: 50px;
            font-size: 1.5rem;
            color: #666;
        }

        /* Checkout button styles */
        #checkoutButton {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: not-allowed;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        #checkoutButton:not(:disabled) {
            background-color: #28a745;
            cursor: pointer;
        }

        #checkoutButton:not(:disabled):hover {
            background-color: #218838;
        }

        .cart-modal-content {
            max-height: 80vh;
            overflow-y: auto;
        }

        .payment-method {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .payment-method h3 {
            margin-bottom: 10px;
        }

        .payment-method label {
            display: block;
            margin: 5px 0;
            padding: 5px;
            cursor: pointer;
        }

        .payment-method input[type="radio"] {
            margin-right: 10px;
        }
    </style>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="main-wrapper">
        <nav>
            <div class="logo">
                <img src="images/logo.png" alt="logo" />
                <h1>HMZ Store</h1>
            </div>
            <ul>
                <li>
                    <a href="index.php"><i class="fas fa-home"></i></a>
                </li>
                <li>
                    <a href="about.php"><i class="fas fa-shop"></i></a>
                </li>
                <li>
                    <a href="#" id="cartLink"><i class="fas fa-cart-shopping"></i> (<span id="cartCount">0</span>)</a>
                </li>
                <li>
                    <a href="#" id="favoriteLink"><i class="fa-solid fa-bookmark"></i> (<span
                            id="favCount">0</span>)</a>
                </li>
                <li>
                    <a href="user_orders.php"><i class="fas fa-history"></i> Riwayat Pemesanan</a>
                </li>
                <div class="login">
                    <?php if (isLoggedIn()): ?>
                        <li class="user-role-badge <?php echo 'role-' . getUserRole(); ?>">
                            <i class="fas <?php echo isAdmin() ? 'fa-crown' : 'fa-user'; ?>"></i>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </li>
                        <li>
                            <a href="logout.php">Logout</a>
                        </li>
                        <?php if (isAdmin()): ?>
                            <li>
                                <a href="dashboard.php">Dashboard</a>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li>
                            <a href="login.php">Log In</a>
                        </li>
                        <li>
                            <a href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </div>
            </ul>
            <div class="hamburger">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
            </div>
        </nav>

        <main class="main-content">
            <section class="products">
                <h2 style="text-align: center; margin: 30px 0; font-size: 2.5rem; color: #333;">Our Dragons</h2>
                <div class="product-container">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <div class="product-card" data-id="<?php echo $row['id']; ?>"
                                data-name="<?php echo htmlspecialchars($row['nama_produk']); ?>"
                                data-price="<?php echo $row['harga']; ?>">
                                <?php if (!empty($row['gambar'])): ?>
                                    <img src="images shop/<?php echo $row['gambar']; ?>"
                                        alt="<?php echo htmlspecialchars($row['nama_produk']); ?>"
                                        onerror="this.src='images/dragon_1.jpg'">
                                <?php else: ?>
                                    <img src="images/dragon_1.jpg" alt="Default Dragon">
                                <?php endif; ?>
                                <h4><?php echo htmlspecialchars($row['nama_produk']); ?></h4>
                                <p>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                                <?php if ($row['stok'] > 0): ?>
                                    <p style="color: #28a745; font-size: 0.9rem;">Stok: <?php echo $row['stok']; ?></p>
                                    <div class="product-actions">
                                        <button class="add-to-cart">
                                            <i class="fa fa-cart-shopping"></i> Add to Cart
                                        </button>
                                        <button class="favorite">
                                            <i class="fa-solid fa-bookmark"></i> Favorite
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <p style="color: #dc3545; font-size: 0.9rem;">Habis</p>
                                    <div class="product-actions">
                                        <button class="add-to-cart" disabled>
                                            <i class="fa fa-cart-shopping"></i> Stok Habis
                                        </button>
                                        <button class="favorite">
                                            <i class="fa-solid fa-bookmark"></i> Favorite
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="no-products">
                            <p>Belum ada produk tersedia. Silakan tambah produk di dashboard admin.</p>
                            <?php if (!isLoggedIn()): ?>
                                <a href="login.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; 
                           background: #667eea; color: white; text-decoration: none; border-radius: 5px;">
                                    Login sebagai Admin
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <!-- Favorites Modal -->
        <div id="favoritesModal" class="favorites-modal">
            <div class="favorites-modal-content">
                <h2>Favorites</h2>
                <div id="favoritesList"></div>
                <button id="closeFavoritesButton" class="close-favorites-button">Close</button>
            </div>
        </div>

        <!-- Cart Modal -->
        <!-- Cart Modal -->
        <div id="cartModal" class="cart-modal">
            <div class="cart-modal-content">
                <h2>Keranjang Belanja</h2>
                <div id="cartItemsList">
                    <p style="text-align: center; color: #666; padding: 20px;">Keranjang kosong</p>
                </div>
                <p style="font-size: 1.2rem; font-weight: bold; margin: 20px 0;">
                    Total: Rp <span id="totalPrice">0</span>
                </p>
                <div class="payment-method">
                    <h3>Metode Pembayaran</h3>
                    <label><input type="radio" name="payment" value="Kartu Debit"> Kartu Debit</label><br>
                    <label><input type="radio" name="payment" value="OVO"> OVO</label><br>
                    <label><input type="radio" name="payment" value="Bank Transfer"> Bank Transfer</label><br>
                    <label><input type="radio" name="payment" value="COD"> Cash on Delivery (COD)</label>
                </div>

                <?php if (isLoggedIn()): ?>
                    <button id="checkoutButton" disabled style="background-color: #6c757d; 
                   color: white; 
                   border: none; 
                   padding: 12px 20px; 
                   border-radius: 5px; 
                   font-size: 16px; 
                   font-weight: bold; 
                   width: 100%; 
                   margin-top: 10px;
                   cursor: not-allowed;">
                        Checkout
                    </button>
                <?php else: ?>
                    <a href="login.php" style="text-decoration: none; display: block;">
                        <button style="background-color: #667eea; 
                      color: white; 
                      width: 100%; 
                      padding: 12px 20px; 
                      border: none; 
                      border-radius: 5px; 
                      cursor: pointer;">
                            <i class="fas fa-sign-in-alt"></i> Login untuk Checkout
                        </button>
                    </a>
                <?php endif; ?>

                <button id="closeCartButton" style="background-color: #6c757d; 
                                           color: white; 
                                           width: 100%; 
                                           padding: 12px 20px; 
                                           border: none; 
                                           border-radius: 5px; 
                                           cursor: pointer; 
                                           margin-top: 10px;">
                    Close
                </button>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer" style="margin-top: auto;">
            <section>
                <div class="footer-left">
                    <h3>Payment Method</h3>
                    <div class="credits-cards">
                        <img src="images/Ovo.png" alt="OVO">
                        <img src="images/Dana.png" alt="DANA">
                        <img src="images/Gopay.png" alt="GOPAY">
                    </div>
                </div>
                <div class="footer-center">
                    <div>
                        <i class="fa-solid fa-location-dot"></i>
                        <p><span>Indonesia</span> Jawa Timur, Surabaya</p>
                    </div>
                    <div>
                        <i class="fa fa-phone"></i>
                        <p>+62 877-6413-6431</p>
                    </div>
                    <div>
                        <i class="fa fa-envelope"></i>
                        <p><a href="mailto:Yayasan.An.Nahl@gmail.com">Yayasan.An.Nahl@gmail.com</a></p>
                    </div>
                </div>
                <div class="footer-right">
                    <p class="footer-about">
                        <span>HMZ STORE</span>
                        adalah web Petshop saya harap kalian semua menikmati web saya
                    </p>
                    <div class="footer-media">
                        <a href="https://youtube.com" target="_blank"><i class="fa-brands fa-youtube"></i></a>
                        <a href="https://www.facebook.com/?locale=id_ID" target="_blank"><i
                                class="fa-brands fa-facebook"></i></a>
                        <a href="https://twitter.com/" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://www.whatsapp.com/?lang=id_ID" target="_blank"><i
                                class="fa-brands fa-whatsapp"></i></a>
                    </div>
                    <p class="footer-copyright">2025 HMZ STORE</p>
                </div>
            </section>
        </footer>
    </div>

    <script src="script.js"></script>

    <!-- Footer -->
    <footer class="footer" style="margin-top: auto;">
        <!-- ... footer content tetap sama ... -->
    </footer>
    </div>

    <script src="script.js"></script>
    <script>
        // ... kode JavaScript sebelumnya tetap ...

        // Fungsi untuk mengupdate status transaksi
        async function updateTransactionStatus(transactionId, newStatus) {
            if (!confirm(`Ubah status transaksi #${transactionId} menjadi "${newStatus}"?`)) {
                return;
            }

            try {
                // Tampilkan loading
                showNotification('Mengupdate status transaksi...', 'info');

                // Kirim request ke API
                const response = await fetch('api.php?action=update_transaction_status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        id: transactionId,
                        status: newStatus
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(`Status transaksi #${transactionId} berhasil diubah menjadi "${newStatus}"`, 'success');
                    // Refresh tabel transaksi setelah 1.5 detik
                    setTimeout(() => {
                        loadTransactions();
                    }, 1500);
                } else {
                    showNotification('Error: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error: ' + error.message, 'error');
            }
        }

        // Modifikasi fungsi loadTransactions untuk menambahkan dropdown status
        // Ganti fungsi loadTransactions di dashboard.php dengan ini:
        async function loadTransactions() {
            try {
                const response = await fetch('api.php?action=get_transactions');
                const result = await response.json();

                const tbody = document.getElementById('transactionsBody');

                if (result.success && result.data.length > 0) {
                    tbody.innerHTML = '';

                    result.data.forEach(transaction => {
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
                            case 'selesai':
                                statusClass = 'status-selesai';
                                statusText = 'Selesai';
                                break;
                            case 'dibatalkan':
                                statusClass = 'status-dibatalkan';
                                statusText = 'Dibatalkan';
                                break;
                        }

                        // Buat tombol aksi berdasarkan status
                        let actionButtons = '';
                        if (transaction.status !== 'selesai' && transaction.status !== 'dibatalkan') {
                            actionButtons = `
                        <button class="btn btn-success btn-sm" onclick="completeTransaction(${transaction.id})" style="margin-right:5px;">
                            <i class="fas fa-check-circle"></i> Selesaikan
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="viewTransactionDetails(${transaction.id})">
                            <i class="fas fa-eye"></i> Lihat
                        </button>
                    `;
                        } else {
                            actionButtons = `
                        <button class="btn btn-primary btn-sm" onclick="viewTransactionDetails(${transaction.id})">
                            <i class="fas fa-eye"></i> Lihat
                        </button>
                        <button class="btn btn-info btn-sm" onclick="downloadInvoice(${transaction.id})" style="margin-left:5px;">
                            <i class="fas fa-download"></i> Invoice
                        </button>
                    `;
                        }

                        row.innerHTML = `
                    <td>${transaction.id}</td>
                    <td>${transaction.username}</td>
                    <td>Rp ${parseInt(transaction.total_harga).toLocaleString('id-ID')}</td>
                    <td>${transaction.metode_pembayaran}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td>${new Date(transaction.created_at).toLocaleString('id-ID')}</td>
                    <td>
                        <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                            ${actionButtons}
                        </div>
                    </td>
                `;
                        tbody.appendChild(row);
                    });
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

        // Fungsi sederhana untuk complete transaction
        async function completeTransaction(transactionId) {
            if (!confirm(`Selesaikan transaksi #${transactionId}?\nTransaksi yang sudah selesai akan hilang dari daftar.`)) {
                return;
            }

            try {
                showNotification('Menyelesaikan transaksi...', 'info');

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

                    // Hapus baris transaksi dari tabel (tanpa reload)
                    const row = document.querySelector(`tr:has(button[onclick*="completeTransaction(${transactionId})"])`);
                    if (row) {
                        row.style.opacity = '0.5';
                        setTimeout(() => {
                            row.remove();
                            // Update jumlah transaksi jika perlu
                            updateTransactionCount();
                        }, 500);
                    }

                    // Jika tidak ada row yang ditemukan, refresh tabel
                    setTimeout(() => {
                        loadTransactions();
                    }, 1500);
                } else {
                    showNotification('❌ Error: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('❌ Error: ' + error.message, 'error');
            }
        }

        // Fungsi untuk update jumlah transaksi
        function updateTransactionCount() {
            const tbody = document.getElementById('transactionsBody');
            const rows = tbody.querySelectorAll('tr:not([style*="display: none"])');
            const emptyRow = tbody.querySelector('tr:has(td[colspan="7"])');

            if (rows.length === 0 && emptyRow) {
                // Kosongkan dan tampilkan pesan
                tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px;">
                    <p>Semua transaksi sudah selesai!</p>
                </td>
            </tr>
        `;
            }
        }

        // Tambahkan fungsi downloadInvoice jika belum ada di dashboard.php:
        function downloadInvoice(transactionId) {
            window.open(`invoice.php?id=${transactionId}`, '_blank');
        } catch (error) {
            console.error('Error loading transactions:', error);
            showNotification('Gagal memuat transaksi', 'error');
        }


        // Fungsi untuk mendownload invoice
        function downloadInvoice(transactionId) {
            window.open(`invoice.php?id=${transactionId}`, '_blank');
        }

        // Modifikasi fungsi viewTransactionDetails untuk menampilkan status
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

                    // Status options
                    const statusOptions = {
                        'pending': 'Pending',
                        'diproses': 'Diproses',
                        'selesai': 'Selesai',
                        'dibatalkan': 'Dibatalkan'
                    };

                    let statusSelect = `
                <select id="statusSelect" 
                        onchange="updateTransactionStatus(${transaction.id}, this.value)"
                        style="padding: 8px 12px; border-radius: 5px; border: 1px solid #ddd; margin-left: 10px; background: white; cursor: pointer;">
            `;

                    Object.entries(statusOptions).forEach(([value, label]) => {
                        const selected = transaction.status === value ? 'selected' : '';
                        statusSelect += `<option value="${value}" ${selected}>${label}</option>`;
                    });

                    statusSelect += '</select>';

                    detailsDiv.innerHTML = `
                <div style="margin-bottom: 20px;">
                    <p><strong>ID Transaksi:</strong> ${transaction.id}</p>
                    <p><strong>User:</strong> ${transaction.username}</p>
                    <p><strong>Tanggal:</strong> ${new Date(transaction.created_at).toLocaleString('id-ID')}</p>
                    <p><strong>Metode Pembayaran:</strong> ${transaction.metode_pembayaran}</p>
                    <p><strong>Status:</strong> ${statusSelect}</p>
                    <p><strong>Total Harga:</strong> <span style="font-size: 1.2rem; font-weight: bold; color: #667eea;">Rp ${parseInt(transaction.total_harga).toLocaleString('id-ID')}</span></p>
                </div>
                <div style="margin-top: 20px;">
                    <h4>Detail Item:</h4>
                    ${itemsHtml || '<p>Tidak ada item</p>'}
                </div>
                <div style="margin-top: 30px; display: flex; gap: 10px;">
                    <button onclick="downloadInvoice(${transaction.id})" class="btn btn-primary">
                        <i class="fas fa-download"></i> Download Invoice
                    </button>
                    <button onclick="printTransaction(${transaction.id})" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Print
                    </button>
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

        // Fungsi untuk print transaksi
        function printTransaction(transactionId) {
            const printWindow = window.open(`print_transaction.php?id=${transactionId}`, '_blank');
            printWindow.focus();
        }

        // Panggil loadTransactions saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            loadTransactions();
            // ... kode lainnya tetap ...
        });
        // Inisialisasi tambahan untuk about.php
        document.addEventListener('DOMContentLoaded', function () {
            console.log('About.php loaded - Initializing checkout system');

            // Pastikan payment method listeners bekerja
            const paymentMethods = document.querySelectorAll('input[name="payment"]');
            const checkoutButton = document.getElementById('checkoutButton');

            if (paymentMethods.length > 0 && checkoutButton) {
                console.log('Payment methods and checkout button found');

                // Force update button state based on cart
                setTimeout(() => {
                    const cartItemsList = document.getElementById('cartItemsList');
                    const isEmpty = cartItemsList.innerHTML.includes('Keranjang kosong');

                    if (!isEmpty) {
                        console.log('Cart is not empty');
                        // Cek jika ada payment method yang sudah dipilih
                        const selectedPayment = document.querySelector('input[name="payment"]:checked');
                        if (selectedPayment) {
                            checkoutButton.disabled = false;
                            checkoutButton.style.backgroundColor = '#28a745';
                            checkoutButton.style.cursor = 'pointer';
                        }
                    }
                }, 500);
            }
        });
    </script>

</body>

</html>