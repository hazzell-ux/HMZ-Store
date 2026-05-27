<?php
require_once 'config.php';
requireAdmin();

$id = intval($_GET['id']);
$query = "SELECT * FROM transaksi WHERE id = $id";
$result = mysqli_query($conn, $query);
$transaction = mysqli_fetch_assoc($result);

if (!$transaction) {
    die('Transaksi tidak ditemukan');
}

$items = json_decode($transaction['items'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $transaction['id']; ?> - HMZ Store</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .invoice-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #667eea; padding-bottom: 20px; }
        .logo h1 { color: #667eea; font-size: 24px; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { color: #333; margin-bottom: 5px; }
        .info-section { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .info-box h3 { color: #667eea; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .info-box p { margin: 5px 0; }
        .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .items-table th { background: #667eea; color: white; padding: 12px; text-align: left; }
        .items-table td { padding: 12px; border-bottom: 1px solid #eee; }
        .items-table tr:hover { background: #f9f9f9; }
        .total-section { text-align: right; margin-top: 30px; padding-top: 20px; border-top: 2px solid #667eea; }
        .total-section h3 { font-size: 20px; color: #333; }
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 20px; font-weight: bold; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-diproses { background: #cce5ff; color: #004085; }
        .status-selesai { background: #d4edda; color: #155724; }
        .status-dibatalkan { background: #f8d7da; color: #721c24; }
        .footer { margin-top: 40px; text-align: center; color: #666; font-size: 14px; border-top: 1px solid #eee; padding-top: 20px; }
        @media print {
            body { background: white; padding: 0; }
            .invoice-container { box-shadow: none; }
            .no-print { display: none; }
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
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p>No: TRX-<?php echo str_pad($transaction['id'], 6, '0', STR_PAD_LEFT); ?></p>
                <p>Tanggal: <?php echo date('d/m/Y H:i', strtotime($transaction['created_at'])); ?></p>
                <span class="status-badge status-<?php echo $transaction['status']; ?>">
                    <?php echo ucfirst($transaction['status']); ?>
                </span>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-box">
                <h3>Informasi Pelanggan</h3>
                <p><strong>Nama:</strong> <?php echo htmlspecialchars($transaction['username']); ?></p>
                <p><strong>ID User:</strong> <?php echo $transaction['user_id']; ?></p>
            </div>
            <div class="info-box">
                <h3>Informasi Pembayaran</h3>
                <p><strong>Metode:</strong> <?php echo $transaction['metode_pembayaran']; ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst($transaction['status']); ?></p>
                <p><strong>Invoice Date:</strong> <?php echo date('d F Y', strtotime($transaction['created_at'])); ?></p>
            </div>
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
                    <td>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="total-section">
            <h3>Total Pembayaran: Rp <?php echo number_format($transaction['total_harga'], 0, ',', '.'); ?></h3>
            <p>Terbilang: <?php echo terbilang($transaction['total_harga']); ?> Rupiah</p>
        </div>
        
        <div class="footer">
            <p>Terima kasih telah berbelanja di HMZ Store</p>
            <p>Invoice ini sah dan diproses oleh sistem</p>
            <p>www.hmzstore.com | Email: Yayasan.An.Nahl@gmail.com</p>
            <p class="no-print">
                <button onclick="window.print()" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px;">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
            </p>
        </div>
    </div>
    
    <script>
        // Auto print jika diinginkan
        // window.print();
    </script>
</body>
</html>