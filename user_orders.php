<?php
require_once 'config.php';
requireLogin();

// Get user's orders
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM transaksi WHERE user_id = $user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Get user info
$user_query = "SELECT username, email FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan - HMZ Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="shortcut icon" href="images/favi.ico" type="image/x-icon">
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
            min-height: 100vh;
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

        .user-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            color: #333;
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info {
            text-align: right;
        }

        .user-info p {
            margin: 5px 0;
            color: #666;
        }

        .user-info strong {
            color: #333;
        }

        /* Orders List */
        .orders-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .orders-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .orders-header h2 {
            color: #333;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .order-count {
            background: #667eea;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Order Cards */
        .order-card {
            border: 1px solid #e1e5e9;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background: white;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e1e5e9;
        }

        .order-id {
            font-weight: bold;
            color: #333;
            font-size: 1.1rem;
        }

        .order-date {
            color: #666;
            font-size: 0.9rem;
        }

        .order-status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
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

        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 5px;
        }

        .detail-value {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }

        .payment-method {
            background: #e8f4ff;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #667eea;
        }

        .payment-method .label {
            color: #004085;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .payment-method .method {
            color: #333;
            font-weight: bold;
        }

        /* Items List */
        .items-list {
            margin-top: 15px;
        }

        .items-list h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .item {
            display: flex;
            align-items: center;
            padding: 10px;
            background: white;
            border-radius: 8px;
            margin-bottom: 8px;
            border: 1px solid #e1e5e9;
        }

        .item-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 15px;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .item-details {
            display: flex;
            gap: 15px;
            color: #666;
            font-size: 0.9rem;
        }

        .item-quantity {
            color: #667eea;
            font-weight: 600;
        }

        .item-price {
            color: #28a745;
            font-weight: 600;
        }

        /* Order Total */
        .order-total {
            text-align: right;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #e1e5e9;
        }

        .total-label {
            font-size: 1rem;
            color: #666;
            margin-bottom: 5px;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .empty-state p {
            margin-bottom: 20px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
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
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            justify-content: flex-end;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .order-details {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">
            <img src="images/logo.png" alt="logo" />
            <h1>HMZ Store - Riwayat Pemesanan</h1>
        </div>
        <ul>
            <li class="user-badge">
                <i class="fas fa-user"></i>
                <?php echo htmlspecialchars($user['username']); ?>
            </li>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="about.php"><i class="fas fa-shop"></i> Shop</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="page-header">
            <div>
                <h1><i class="fas fa-history"></i> Riwayat Pemesanan</h1>
                <p style="color: #666; margin-top: 5px;">Lihat semua pesanan yang telah Anda checkout</p>
            </div>
            <div class="user-info">
                <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p style="margin-top: 10px;">
                    <a href="about.php" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Lanjut Belanja
                    </a>
                </p>
            </div>
        </div>

        <div class="orders-container">
            <div class="orders-header">
                <h2><i class="fas fa-receipt"></i> Daftar Pesanan</h2>
                <span class="order-count"><?php echo mysqli_num_rows($result); ?> pesanan</span>
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($order = mysqli_fetch_assoc($result)): 
                    $items = json_decode($order['items'], true);
                ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-id">Pesanan #TRX-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></div>
                                <div class="order-date">Tanggal: <?php echo date('d F Y H:i', strtotime($order['created_at'])); ?></div>
                            </div>
                            <div>
                                <span class="order-status status-<?php echo $order['status']; ?>">
                                    <?php 
                                    $status_text = '';
                                    switch($order['status']) {
                                        case 'pending': $status_text = 'pending'; break;
                                        case 'diproses': $status_text = 'Sedang Diproses'; break;
                                        case 'selesai': $status_text = 'Selesai'; break;
                                        case 'dibatalkan': $status_text = 'Dibatalkan'; break;
                                        default: $status_text = ucfirst($order['status']);
                                    }
                                    echo $status_text;
                                    ?>
                                </span>
                            </div>
                        </div>

                        <div class="order-details">
                            <div class="detail-item">
                                <span class="detail-label">ID Transaksi</span>
                                <span class="detail-value">TRX-<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Total Pembayaran</span>
                                <span class="detail-value" style="color: #28a745; font-weight: bold;">
                                    Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?>
                                </span>
                            </div>
                        </div>

                        <div class="payment-method">
                            <div class="label">Metode Pembayaran:</div>
                            <div class="method">
                                <i class="fas fa-credit-card"></i>
                                <?php 
                                $payment_methods = [
                                    'kartu-debit' => 'Kartu Debit',
                                    'ovo' => 'OVO',
                                    'bank-transfer' => 'Bank Transfer',
                                    'cod' => 'Cash on Delivery (COD)'
                                ];
                                echo $payment_methods[$order['metode_pembayaran']] ?? $order['metode_pembayaran'];
                                ?>
                            </div>
                        </div>

                        <?php if ($items && is_array($items)): ?>
                            <div class="items-list">
                                <h4><i class="fas fa-box"></i> Items yang Dipesan:</h4>
                                <?php foreach ($items as $item): ?>
                                    <div class="item">
                                        <img src="<?php echo $item['img'] ?? 'images/dragon_1.jpg'; ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                             class="item-img">
                                        <div class="item-info">
                                            <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                            <div class="item-details">
                                                <span class="item-quantity">Jumlah: <?php echo $item['quantity']; ?></span>
                                                <span class="item-price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?> per item</span>
                                                <span>Subtotal: <strong>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></strong></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="order-total">
                            <div class="total-label">Total yang dibayarkan:</div>
                            <div class="total-amount">Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></div>
                        </div>

                        <div class="action-buttons">
                            <a href="javascript:void(0)" onclick="printInvoice(<?php echo $order['id']; ?>)" 
                               class="btn btn-secondary btn-sm">
                                <i class="fas fa-print"></i> Cetak Invoice
                            </a>
                            <?php if ($order['status'] === 'diproses'): ?>
                                <button class="btn btn-primary btn-sm" onclick="trackOrder(<?php echo $order['id']; ?>)">
                                    <i class="fas fa-truck"></i> Lacak Pengiriman
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <h3>Belum ada riwayat pemesanan</h3>
                    <p>Anda belum melakukan checkout. Mulai belanja produk naga favorit Anda!</p>
                    <a href="about.php" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> Mulai Belanja
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        
        function printInvoice(orderId) {
            window.open(`invoice_user.php?id=${orderId}`, '_blank');
        }

        function orderDetails(orderId) {
            alert(`Detail pesanan #${orderId} akan ditampilkan di sini.\n\nFitur ini sedang dalam pengembangan.`);
        }

        
        function payOrder(orderId) {
            if (confirm(`Lanjutkan pembayaran untuk pesanan #${orderId}?`)) {
              
                alert(`Simulasi pembayaran untuk pesanan #${orderId}\n\nAnda akan diarahkan ke halaman pembayaran.`);

            }
        }

       
        function trackOrder(orderId) {
            const trackingNumber = 'TRK-' + Math.random().toString(36).substr(2, 9).toUpperCase();
            alert(`Lacak Pengiriman Pesanan #${orderId}\n\nNomor Resi: ${trackingNumber}\nStatus: Dalam pengiriman\nEstimasi: 3-5 hari kerja`);
        }

        function filterOrders(status) {
            const orderCards = document.querySelectorAll('.order-card');
            orderCards.forEach(card => {
                const cardStatus = card.querySelector('.order-status').classList[1];
                if (status === 'all' || cardStatus === `status-${status}`) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>