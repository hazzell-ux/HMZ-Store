<?php
require_once 'config.php';

$query = "SELECT * FROM produk ORDER BY created_at DESC LIMIT 11";
$result = mysqli_query($conn, $query);
$products = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMZ Store - Home</title>
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

        .admin-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.3s;
        }

        .admin-btn:hover {
            transform: translateY(-3px);
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

        .flash-message {
            position: fixed;
            top: 100px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            animation: slideInRight 0.3s ease-out;
            max-width: 400px;
        }

        .flash-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-left: 4px solid #155724;
        }

        .flash-error {
            background: linear-gradient(135deg, #dc3545 0%, #e4606d 100%);
            color: white;
            border-left: 4px solid #721c24;
        }

        .flash-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ffd654 100%);
            color: #212529;
            border-left: 4px solid #856404;
        }
    </style>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>

<body>
    <nav>
        <div class="logo">
            <img src="images/logo.png" alt="logo" />
            <h1>HMZ STORE</h1>
        </div>
        <ul>
            <li>
                <a href="index.php" i class="fas fa-home"></i></a>
            </li>
            <li>
                <a href="about.php" i class="fas fa-shop"></i></a>
            </li>
            <li>
                <a href="#" id="cartLink" i class="fas fa-cart-shopping"></i>(0)</a>
            </li>
            <li>
                <a href="#" id="favoriteLink" class="fa-solid fa-bookmark">(0)</a>
            </li>
            <li>
                <a href="user_orders.php"><i class="fas fa-history"></i> Riwayat Pemesanan</a>
            </li>
            <div class="login">
                <?php if (isLoggedIn()): ?>
                    <li class="user-role-badge <?php echo 'role-' . getUserRole(); ?>">
                        <i class="fas <?php echo isAdmin() ? 'fa-crown' : 'fa-user'; ?>"></i>
                        <?php echo $_SESSION['username']; ?>
                    </li>

                    <li>
                        <a href="logout.php">Logout</a>
                    </li>
                    <li>
                        <a href="dashboard.php">Dashboard</a>
                    </li>
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

    <?php
    $flash = getFlashMessage();
    if ($flash): ?>
        <div class="flash-message flash-<?php echo $flash['type']; ?>">
            <i class="fas <?php echo $flash['type'] === 'success' ? 'fa-check-circle' :
                ($flash['type'] === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'); ?>"></i>
            <?php echo $flash['message']; ?>
        </div>
        <script>
            setTimeout(function () {
                document.querySelector('.flash-message').remove();
            }, 5000);
        </script>
    <?php endif; ?>

    <div class="banner">
        <div class="slider" style="--quantity: 10">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $index => $product): ?>
                    <?php if ($index < 10): ?>
                        <div class="item" style="--position: <?php echo $index + 1; ?>">
                            <img src="images shop/<?php echo $product['gambar'] ?? 'default.jpg'; ?>"
                                alt="<?php echo $product['nama_produk']; ?>">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <div class="item" style="--position: <?php echo $i; ?>">
                        <img src="images/dragon_<?php echo $i; ?>.jpg" alt="Dragon <?php echo $i; ?>">
                    </div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
        <div class="content">
            <h1 data-content="--Dragons--">--Dragons--</h1>
            <div class="author">
                <h2>_Petshop</h2>
                <p><b>Eggs Dragon</b></p>
                <p>_Milikilah Nagamu Sendiri</p>
            </div>
            <div class="model"></div>
        </div>
    </div>

    <?php if (!isLoggedIn()): ?>
        <a href="login.php" class="admin-btn">
            <i class="fas fa-lock"></i> Admin Login
        </a>
    <?php endif; ?>


    <div id="favoritesModal" class="favorites-modal">
        <div class="favorites-modal-content">
            <h2>Favorites</h2>
            <div id="favoritesList"></div>
            <button class="favorites-modal-button-close" id="closeFavoritesButton">Close</button>
        </div>
    </div>

    <div id="cartModal" class="cart-modal">
        <div class="cart-modal-content">
            <h2>Keranjang</h2>
            <div id="cartItemsList"></div>
            <p>Total: Rp.<span id="totalPrice">0</span></p>
            <div class="payment-method">
                <h3>Pilih Metode Pembayaran</h3>
                <label><input type="radio" name="payment" value="kartu-debit"> Kartu Debit</label>
                <label><input type="radio" name="payment" value="ovo"> OVO</label>
                <label><input type="radio" name="payment" value="bank-transfer"> Bank Transfer</label>
                <label><input type="radio" name="payment" value="cod"> Cash on Delivery (COD)</label>
            </div>
            <button id="checkoutButton" disabled>Checkout</button>
            <button id="closeCartButton">Close</button>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>