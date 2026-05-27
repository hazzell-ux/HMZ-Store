<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMZ</title>
    <link rel="shortcut icon" href="images/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
    <style>
        
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body{
            background-color: #D2D2D2;
            background-image:
            repeating-linear-gradient(
                to right, transparent 0 100px,
                #25283b22 100px 101px
            ),
            repeating-linear-gradient(
                to bottom, transparent 0 100px,
                #25283b22 100px 101px
            );
        }
        
        body::before{
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
    </style>
    <link rel="stylesheet" href="style.css">
</head>
    <nav>   
        <div class="logo">
          <img src="images/logo.png" alt="logo" />
          <h1>LOGO</h1>
        </div>
        <ul>
            <li>
                <a href="index.html" i class="fas fa-home "></a>
            </li>
            <li>
                <a href="about.html" i class="fas fa-shop "></a>
            </li>
          <li>
            <a href="#" id="cartLink" i class="fas fa-cart-shopping "></i>(0)</a>
        </li>
        <li>
            <a href="#" id="favoriteLink" class="fa-solid fa-bookmark">(0)</a>
        </li>
          <div class="login"></div>
          <li>
            <a href="login.html">Log In</a>
          </li>
          <li>
            <a href="sign.html">Register</a>
          </li>
        </ul>
        <div class="hamburger">
          <span class="line"></span>
          <span class="line"></span>
          <span class="line"></span>
        </div>
      </nav>
    <!-- Featured Products Section -->
    <section class="products">
      <div class="product-container">
          <div class="product-card" data-id="1" data-name="Naga Angin" data-price="13.000.000">
              <img src="images/dragon_1.jpg" alt="Product 1">
              <h4>Naga Angin</h4>
              <p>Rp.13.000.000</p>
              <button class="add-to-cart">Add to Cart</button>
          </div>
          <div class="product-card" data-id="2" data-name="Naga Hijau" data-price="10.000.000">
              <img src="images/dragon_2.jpg" alt="Product 2">
              <h4>Naga Hijau</h4>
              <p>10.000.000</p>
              <button class="add-to-cart">Add to Cart</button>
          </div>
          <div class="product-card" data-id="3" data-name="Naga Api" data-price="15.000.000">
              <img src="images/dragon_3.jpg" alt="Product 3">
              <h4>Naga Api</h4>
              <p>15.000.000</p>
              <button class="add-to-cart">Add to Cart</button>
          </div>
          <div class="product-card" data-id="4" data-name="Naga Emas" data-price="20.000.000">
              <img src="images/dragon_4.jpg" alt="Product 4">
              <h4>Naga Emas</h4>
              <p>20.000.000</p>
              <button class="add-to-cart">Add to Cart</button>
          </div>
          <div class="product-card" data-id="5" data-name="Naga Ireng" data-price="18.000.000">
              <img src="images/dragon_5.jpg" alt="Product 5">
              <h4>Naga Ireng</h4>
              <p>18.000.000</p>
              <button class="add-to-cart">Add to Cart</button>
          </div>
          <div class="product-card" data-id="6" data-name="Naga Tanah" data-price="23.000.000">
              <img src="images/dragon_6.jpg" alt="Product 6">
              <h4>Naga Tanah</h4>
              <p>23.000.000</p>
              <button class="add-to-cart">Add to Cart</button>
          </div>
          <div class="product-card" data-id="7" data-name="Naga Air" data-price="25.000.000">
              <img src="images/dragon_7.jpg" alt="Product 7">
              <h4>Naga Air</h4>
              <p>25.000.000</p>
              <button class="add-to-cart">Add to Cart</button>
          </div>
          <div class="product-card" data-id="8" data-name="Naga Daun " data-price="17.000.000">
              <img src="images/dragon_8.jpg" alt="Product 8">
              <h4>Naga Daun</h4>
              <p>17.000.000</p>
              <button class="add-to-cart">Add to Cart</button>
          </div>
          <div class="product-card" data-id="9" data-name="Naga Ngamk" data-price="27.000.000">
              <img src="images/dragon_9.jpg" alt="Product 9">
              <h4>Naga Ngamuk</h4>
              <p>27.000.000</p>
              <button class="add-to-cart">Add to Cart</button>
          </div>
          <div class="product-card" data-id="10" data-name="Naga Bonar" data-price="1.000.000.000">
              <img src="images/dragon_10.jpg" alt="Product 10">
              <h4>Naga Bonar</h4>
              <p>1.000.000.000</p>
              <button class="add-to-cart">Add to Cart</button>
          </div>
          <div class="product-card" data-id="10" data-name="Nogo Sari" data-price="1.000.000.000.000">
              <img src="images/dragon_sari.jpg" alt="Product 10">
              <h4>Dragon Sari (The Best)</h4>
              <p>1.000.000.000.000</p>
              <button class="add-to-cart">Add to Cart</button>
          </div>
      </div>
  </section>
  <!-- Favorites Modal -->
 <div id="favoritesModal" class="favorites-modal">
    <div class="favorites-modal-content">
        <h2>Favorites</h2>
        <div id="favoritesList"></div>
        <button id="closeFavoritesButton">Close</button>
    </div>
 </div>
  <!-- Cart Modal-->
  <div id="cartModal" class="cart-modal">
      <div class="cart-modal-content">
          <h2>Keranjang</h2>
          <div id="cartItemsList"></div>
          <p>Total: Rp.<span id="totalPrice">0</span></p>
          <div class="payment-method">
            <h3>Metode Pembayaran</h3>
            <label><input type="radio" name="payment" value="kartu debit"> Kartu Debit<label>
                <label><input type="radio" name="payment" value="ovo"> OVO</label>
                <label><input type="radio" name="payment" value="bank transfer"> Bank Transfer</label>
                <label><input type="radio" name="payment" value="cod"> Cash on Delivery (COD)</label>
        </div>
        <button id="checkoutButton" disabled>Checkout</button>
          <button id="closeCartButton">Close</button>
      </div>
  </div>
  <script src="script.js"></script>
</body>
    </html>