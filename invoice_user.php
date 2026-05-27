<?php
require_once 'config.php';
requireLogin();

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Hanya user yang memiliki transaksi ini yang bisa melihat
$query = "SELECT * FROM transaksi WHERE id = $id AND user_id = $user_id";
$result = mysqli_query($conn, $query);
$transaction = mysqli_fetch_assoc($result);

if (!$transaction) {
    die('Transaksi tidak ditemukan atau akses ditolak');
}

$items = json_decode($transaction['items'], true);
$user_query = "SELECT username, email FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $transaction['id']; ?> - HMZ Store</title>
    <link rel="shortcut icon" href="images/favi.ico" type="image/x-icon">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f5f5f5;
            background-image:
                repeating-linear-gradient(to right, transparent 0 100px,
                    #25283b22 100px 101px),
                repeating-linear-gradient(to bottom, transparent 0 100px,
                    #25283b22 100px 101px);
        }
        
        .invoice-container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 40px; 
            border-radius: 10px; 
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border: 2px solid #667eea;
        }
        
        .header { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 30px; 
            padding-bottom: 20px; 
            border-bottom: 3px solid #667eea; 
        }
        
        .logo h1 { 
            color: #667eea; 
            font-size: 28px; 
            margin-bottom: 5px;
        }
        
        .logo p { 
            color: #666; 
            font-size: 14px; 
            margin: 2px 0;
        }
        
        .invoice-title { 
            text-align: right; 
        }
        
        .invoice-title h2 { 
            color: #333; 
            margin-bottom: 5px; 
            font-size: 24px;
        }
        
        .info-section { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 30px; 
            margin-bottom: 30px; 
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .info-box h3 { 
            color: #667eea; 
            margin-bottom: 10px; 
            border-bottom: 2px solid #667eea; 
            padding-bottom: 5px; 
            font-size: 18px;
        }
        
        .info-box p { 
            margin: 8px 0; 
            font-size: 14px;
        }
        
        .info-box strong { 
            color: #333;
        }
        
        .items-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0; 
        }
        
        .items-table th { 
            background: #667eea; 
            color: white; 
            padding: 15px; 
            text-align: left; 
            font-weight: bold;
            border: none;
        }
        
        .items-table td { 
            padding: 12px; 
            border-bottom: 1px solid #eee; 
            font-size: 14px;
        }
        
        .items-table tr:hover { 
            background: #f9f9f9; 
        }
        
        .total-section { 
            text-align: right; 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 3px solid #667eea; 
        }
        
        .total-section h3 { 
            font-size: 24px; 
            color: #333; 
            margin-bottom: 10px;
        }
        
        .total-section .terbilang {
            color: #666;
            font-style: italic;
            font-size: 14px;
        }
        
        .status-badge { 
            display: inline-block; 
            padding: 8px 15px; 
            border-radius: 20px; 
            font-weight: bold; 
            font-size: 14px;
            margin-top: 5px;
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
        
        .footer { 
            margin-top: 40px; 
            text-align: center; 
            color: #666; 
            font-size: 12px; 
            border-top: 1px solid #eee; 
            padding-top: 20px; 
        }
        
        .print-btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
            text-decoration: none;
        }
        
        .print-btn:hover {
            background: #5a6fd8;
        }
        
        @media print {
            body { 
                background: white; 
                padding: 0; 
            }
            
            .invoice-container { 
                box-shadow: none; 
                border: 1px solid #ddd;
            }
            
            .print-btn { 
                display: none; 
            }
            
            .footer { 
                display: block;
            }
        }
        
        .payment-info {
            background: #e8f4ff;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        
        .payment-info h4 {
            margin: 0 0 10px 0;
            color: #004085;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="logo">
                <h1>HMZ STORE</h1>
                <p>Petshop Naga Terpercaya</p>
                <p>Indonesia, Jawa Timur, Surabaya</p>
                <p>Telp: +62 877-6413-6431</p>
                <p>Email: Yayasan.An.Nahl@gmail.com</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p><strong>No: TRX-<?php echo str_pad($transaction['id'], 6, '0', STR_PAD_LEFT); ?></strong></p>
                <p>Tanggal: <?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></p>
                <span class="status-badge status-<?php echo $transaction['status']; ?>">
                    <?php 
                    $status_text = '';
                    switch($transaction['status']) {
                        case 'pending': $status_text = 'pending'; break;
                        case 'diproses': $status_text = 'Sedang Diproses'; break;
                        case 'selesai': $status_text = 'Selesai'; break;
                        case 'dibatalkan': $status_text = 'Dibatalkan'; break;
                    }
                    echo $status_text;
                    ?>
                </span>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-box">
                <h3>Informasi Pelanggan</h3>
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>ID Customer:</strong> CUST-<?php echo str_pad($user_id, 6, '0', STR_PAD_LEFT); ?></p>
            </div>
            <div class="info-box">
                <h3>Informasi Pembayaran</h3>
                <p><strong>Metode:</strong> 
                    <?php 
                    $payment_methods = [
                        'kartu-debit' => 'Kartu Debit',
                        'ovo' => 'OVO',
                        'bank-transfer' => 'Bank Transfer',
                        'cod' => 'Cash on Delivery (COD)'
                    ];
                    echo $payment_methods[$transaction['metode_pembayaran']] ?? $transaction['metode_pembayaran'];
                    ?>
                </p>
                <p><strong>Status:</strong> 
                    <span style="font-weight: bold;">
                        <?php echo $status_text; ?>
                    </span>
                </p>
                <p><strong>Invoice Date:</strong> <?php echo date('d F Y', strtotime($transaction['created_at'])); ?></p>
            </div>
        </div>
        
        <div class="payment-info">
            <h4>Informasi Pengiriman:</h4>
            <p><strong>Status Pengiriman:</strong> 
                <?php 
                if($transaction['status'] === 'pending') {
                    echo 'pending';
                } elseif($transaction['status'] === 'diproses') {
                    echo 'Pesanan sedang dipersiapkan untuk dikirim';
                } elseif($transaction['status'] === 'selesai') {
                    echo 'Pesanan sudah dikirim dan diterima';
                } else {
                    echo 'Pengiriman dibatalkan';
                }
                ?>
            </p>
        </div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><strong>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="total-section">
            <h3>Total Pembayaran: Rp <?php echo number_format($transaction['total_harga'], 0, ',', '.'); ?></h3>
            <p class="terbilang">Terbilang: <?php echo terbilang($transaction['total_harga']); ?> Rupiah</p>
        </div>
        
        <div class="footer">
            <p><strong>Terima kasih telah berbelanja di HMZ Store</strong></p>
            <p>Invoice ini sah dan diproses oleh sistem</p>
            <p>Untuk pertanyaan atau bantuan, hubungi:</p>
            <p>www.hmzstore.com | Email: Yayasan.An.Nahl@gmail.com | Telp: +62 877-6413-6431</p>
            <p>
                <button onclick="window.print()" class="print-btn">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
                <a href="user_orders.php" class="print-btn" style="background: #28a745; margin-left: 10px;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
                </a>
            </p>
        </div>
    </div>
    
    <script>
        // Auto print jika diinginkan
        // window.print();
    </script>
</body>
</html>