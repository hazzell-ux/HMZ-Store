let cart = []; // Array untuk menyimpan item keranjang
let favorites = []; // Array untuk menyimpan item favorit

// Fungsi untuk menambah produk ke keranjang
function addToCart(productId, productName, productPrice) {
    const cleanPrice = parseFloat(String(productPrice).replace(/[^\d]/g, ''));
    const productCard = document.querySelector(`.product-card[data-id="${productId}"]`);
    let productImg = '';
    
    if (productCard) {
        const imgElem = productCard.querySelector('img');
        if (imgElem) productImg = imgElem.getAttribute('src');
    }
    
    const product = {
        id: productId,
        name: productName,
        price: cleanPrice,
        quantity: 1,
        img: productImg
    };

    const existingProduct = cart.find(item => item.id === productId);
    if (existingProduct) {
        existingProduct.quantity++;
    } else {
        cart.push(product);
    }

    updateCartDisplay();
    
    // Tampilkan notifikasi
    showNotification(`${productName} ditambahkan ke keranjang!`, 'success');
}

// Fungsi untuk memperbarui tampilan keranjang
function updateCartDisplay() {
    const cartCount = document.getElementById('cartCount');
    const cartItemsList = document.getElementById('cartItemsList');
    const totalPrice = document.getElementById('totalPrice');
    const checkoutButton = document.getElementById('checkoutButton');

    if (cartCount) {
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        cartCount.textContent = totalItems;
    }

    cartItemsList.innerHTML = '';
    let total = 0;
    
    if (cart.length === 0) {
        cartItemsList.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">Keranjang kosong</p>';
        // Disable checkout button
        if (checkoutButton) {
            checkoutButton.disabled = true;
            checkoutButton.style.backgroundColor = '#6c757d';
            checkoutButton.style.cursor = 'not-allowed';
        }
    } else {
        cart.forEach(item => {
            total += item.price * item.quantity;
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.style.display = 'flex';
            cartItem.style.alignItems = 'center';
            cartItem.style.justifyContent = 'space-between';
            cartItem.style.marginBottom = '10px';
            cartItem.style.padding = '10px';
            cartItem.style.border = '1px solid #eee';
            cartItem.style.borderRadius = '5px';
            cartItem.style.backgroundColor = '#f9f9f9';
            
            cartItem.innerHTML = `
                <div style="display: flex; align-items: center; flex: 1;">
                    <img src="${item.img}" alt="${item.name}" style="width:50px;height:50px;object-fit:cover;margin-right:10px;border-radius:8px;">
                    <div style="flex: 1;">
                        <p style="margin:0; font-weight: bold; font-size: 14px;">${item.name}</p>
                        <p style="margin:5px 0; color: #666; font-size: 12px;">${item.quantity} x Rp ${item.price.toLocaleString('id-ID')}</p>
                        <p style="margin:0; font-weight: bold; font-size: 14px; color: #28a745;">Subtotal: Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button class="quantity-btn minus" data-id="${item.id}" style="background: #6c757d; color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; font-weight: bold;">-</button>
                    <span style="font-weight: bold; min-width: 20px; text-align: center;">${item.quantity}</span>
                    <button class="quantity-btn plus" data-id="${item.id}" style="background: #6c757d; color: white; border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; font-weight: bold;">+</button>
                    <button class="remove-btn" data-id="${item.id}" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">Hapus</button>
                </div>
            `;
            cartItemsList.appendChild(cartItem);
        });
        
        // Enable checkout button jika ada item
        if (checkoutButton) {
            checkoutButton.disabled = false;
            checkoutButton.style.backgroundColor = '#28a745';
            checkoutButton.style.cursor = 'pointer';
        }
    }

    if (totalPrice) {
        totalPrice.textContent = total.toLocaleString('id-ID');
    }
}

// Fungsi untuk mengupdate quantity
function updateQuantity(productId, change) {
    const product = cart.find(item => item.id === productId);
    if (product) {
        product.quantity += change;
        if (product.quantity <= 0) {
            removeFromCart(productId);
        } else {
            updateCartDisplay();
        }
    }
}

// Fungsi untuk menghapus produk dari keranjang
function removeFromCart(productId) {
    const product = cart.find(item => item.id === productId);
    if (product) {
        cart = cart.filter(item => item.id !== productId);
        updateCartDisplay();
        showNotification(`${product.name} dihapus dari keranjang`, 'warning');
    }
}

// Fungsi untuk menampilkan notifikasi
function showNotification(message, type = 'info') {
    // Hapus notifikasi lama jika ada
    const oldNotification = document.querySelector('.notification');
    if (oldNotification) {
        oldNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 9999;
        color: white;
        font-weight: bold;
        animation: slideInRight 0.3s ease-out;
        min-width: 300px;
    `;
    
    if (type === 'success') {
        notification.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
        notification.style.borderLeft = '4px solid #155724';
    } else if (type === 'error') {
        notification.style.background = 'linear-gradient(135deg, #dc3545 0%, #e4606d 100%)';
        notification.style.borderLeft = '4px solid #721c24';
    } else if (type === 'warning') {
        notification.style.background = 'linear-gradient(135deg, #ffc107 0%, #ffd654 100%)';
        notification.style.borderLeft = '4px solid #856404';
        notification.style.color = '#212529';
    } else {
        notification.style.background = 'linear-gradient(135deg, #17a2b8 0%, #3dd5f3 100%)';
        notification.style.borderLeft = '4px solid #0c5460';
    }

    notification.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer; margin-left: 15px;">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto remove setelah 3 detik
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

// Fungsi untuk checkout - BUAT GLOBAL
window.checkoutNow = async function() {
    console.log('Checkout function called');
    
    const checkoutButton = document.getElementById('checkoutButton');
    const selectedPayment = document.querySelector('input[name="payment"]:checked');
    
    // Validasi
    if (!selectedPayment) {
        showNotification('Pilih metode pembayaran terlebih dahulu!', 'error');
        return false;
    }
    
    if (cart.length === 0) {
        showNotification('Keranjang kosong!', 'error');
        return false;
    }
    
    const totalPrice = document.getElementById('totalPrice').textContent;
    const confirmCheckout = confirm(
        `CHECKOUT\n` +
        `Items: ${cart.reduce((total, item) => total + item.quantity, 0)}\n` +
        `Total: Rp ${totalPrice}\n` +
        `Payment: ${selectedPayment.value}\n` +
        `Lanjutkan?`
    );
    
    if (!confirmCheckout) return false;
    
    // Show loading
    const originalText = checkoutButton.innerHTML;
    checkoutButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> PROSES...';
    checkoutButton.disabled = true;
    
    try {
        console.log('Sending checkout data...');
        
        // Persiapan data items untuk API
        const itemsData = cart.map(item => ({
            id: item.id,
            name: item.name,
            price: item.price,
            quantity: item.quantity,
            img: item.img
        }));
        
        const response = await fetch('api.php?action=checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                items: JSON.stringify(itemsData),
                total_harga: totalPrice.replace(/[^0-9]/g, ''),
                metode_pembayaran: selectedPayment.value
            })
        });
        
        const result = await response.json();
        console.log('Checkout result:', result);
        
        if (result.success) {
            showNotification('Checkout berhasil! ' + result.message, 'success');
            
            // Clear cart
            cart = [];
            updateCartDisplay();
            
            // Close modal
            const cartModal = document.getElementById('cartModal');
            if (cartModal) cartModal.style.display = 'none';
            
            // Reset payment
            document.querySelectorAll('input[name="payment"]').forEach(m => m.checked = false);
            
            // Reset checkout button
            checkoutButton.disabled = true;
            checkoutButton.innerHTML = 'Checkout';
            checkoutButton.style.backgroundColor = '#6c757d';
            
            // Reload halaman setelah 2 detik
            setTimeout(() => {
                window.location.reload();
            }, 2000);
            
        } else {
            showNotification('Gagal: ' + result.message, 'error');
            checkoutButton.disabled = false;
            checkoutButton.innerHTML = originalText;
            checkoutButton.style.backgroundColor = '#28a745';
        }
        
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error: ' + error.message, 'error');
        checkoutButton.disabled = false;
        checkoutButton.innerHTML = originalText;
        checkoutButton.style.backgroundColor = '#28a745';
    }
    
    return false;
};

// Inisialisasi
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - Cart System Initialized');
    
    // Event listener untuk Add to Cart
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            // Cek apakah produk masih ada stok
            const productCard = this.closest('.product-card');
            const stokInfo = productCard.querySelector('p[style*="color: #28a745"]');
            
            if (stokInfo && this.disabled) {
                showNotification('Produk ini habis stok', 'error');
                return;
            }
            
            const productId = productCard.getAttribute('data-id');
            const productName = productCard.getAttribute('data-name');
            const productPrice = productCard.getAttribute('data-price');
            addToCart(productId, productName, productPrice);
        });
    });

    // Event listener untuk Cart Modal
    const cartLink = document.getElementById('cartLink');
    const cartModal = document.getElementById('cartModal');
    const closeCartButton = document.getElementById('closeCartButton');

    if (cartLink) {
        cartLink.addEventListener('click', function(e) {
            e.preventDefault();
            if (cartModal) {
                cartModal.style.display = 'flex';
                // Update cart display saat modal dibuka
                updateCartDisplay();
            }
        });
    }

    if (closeCartButton) {
        closeCartButton.addEventListener('click', function() {
            if (cartModal) cartModal.style.display = 'none';
        });
    }

    // Payment method selection
    const paymentMethods = document.querySelectorAll('input[name="payment"]');
    const checkoutButton = document.getElementById('checkoutButton');

    if (paymentMethods.length > 0 && checkoutButton) {
        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                console.log('Payment method selected:', this.value);
                
                // Enable checkout button hanya jika ada item di cart
                if (cart.length > 0) {
                    checkoutButton.disabled = false;
                    checkoutButton.style.cursor = 'pointer';
                    checkoutButton.style.backgroundColor = '#28a745';
                }
            });
        });

        // CHECKOUT FUNCTION - Gunakan window.checkoutNow
        checkoutButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            window.checkoutNow();
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const cartModal = document.getElementById('cartModal');
        if (cartModal && event.target === cartModal) {
            cartModal.style.display = 'none';
        }
    });

    // Event delegation untuk tombol quantity dan remove
    document.addEventListener('click', function(e) {
        // Tombol minus
        if (e.target.classList.contains('minus') || e.target.closest('.minus')) {
            const button = e.target.classList.contains('minus') ? e.target : e.target.closest('.minus');
            const productId = button.getAttribute('data-id');
            updateQuantity(productId, -1);
        }
        
        // Tombol plus
        if (e.target.classList.contains('plus') || e.target.closest('.plus')) {
            const button = e.target.classList.contains('plus') ? e.target : e.target.closest('.plus');
            const productId = button.getAttribute('data-id');
            updateQuantity(productId, 1);
        }
        
        // Tombol remove
        if (e.target.classList.contains('remove-btn') || e.target.closest('.remove-btn')) {
            const button = e.target.classList.contains('remove-btn') ? e.target : e.target.closest('.remove-btn');
            const productId = button.getAttribute('data-id');
            removeFromCart(productId);
        }
    });

    // Event listener untuk tombol favorit
    document.querySelectorAll('.favorite').forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const productId = productCard.getAttribute('data-id');
            const productName = productCard.getAttribute('data-name');
            
            // Toggle class aktif
            this.classList.toggle('active');
            
            if (this.classList.contains('active')) {
                showNotification(`${productName} ditambahkan ke favorit`, 'success');
                this.innerHTML = '<i class="fa-solid fa-bookmark"></i> Favorited';
            } else {
                showNotification(`${productName} dihapus dari favorit`, 'warning');
                this.innerHTML = '<i class="fa-solid fa-bookmark"></i> Favorite';
            }
        });
    });

    // Event listener untuk modal favorit
    const favoriteLink = document.getElementById('favoriteLink');
    const favoritesModal = document.getElementById('favoritesModal');
    const closeFavoritesButton = document.getElementById('closeFavoritesButton');
    
    if (favoriteLink && favoritesModal) {
        favoriteLink.addEventListener('click', function(e) {
            e.preventDefault();
            favoritesModal.style.display = 'flex';
        });
    }
    
    if (closeFavoritesButton && favoritesModal) {
        closeFavoritesButton.addEventListener('click', function() {
            favoritesModal.style.display = 'none';
        });
    }

    // Initial cart display
    updateCartDisplay();
    
    // Tambahkan style untuk animasi
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .cart-item:hover {
            background-color: #f0f0f0 !important;
            transition: background-color 0.3s ease;
        }
        
        .favorite.active {
            background-color: #ff4757 !important;
            color: white !important;
        }
        
        .quantity-btn:hover, .remove-btn:hover {
            opacity: 0.9;
            transform: scale(1.05);
            transition: all 0.2s ease;
        }
    `;
    document.head.appendChild(style);
    
    console.log('Cart system fully initialized');
});