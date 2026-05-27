// app.js - Updated for Database Login

// Fungsi untuk login
function login() {
    var email = document.getElementById('loginEmail').value;
    var password = document.getElementById('loginPassword').value;
    var messageDiv = document.getElementById('message');
    
    // Validasi input
    if (email === "" || password === "") {
        messageDiv.innerHTML = "Email dan password wajib diisi.";
        messageDiv.style.color = "red";
        return;
    }

    // Send login data to PHP
    fetch('api.php?action=login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageDiv.innerHTML = "Login berhasil!";
            messageDiv.style.color = "green";
            
            // Redirect based on user role
            setTimeout(function() {
                window.location.href = data.redirect || "index.php";
            }, 1000);
        } else {
            messageDiv.innerHTML = data.message || "Email atau password salah";
            messageDiv.style.color = "red";
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.innerHTML = "Terjadi kesalahan. Silakan coba lagi.";
        messageDiv.style.color = "red";
    });
}

// Fungsi untuk sign up
function signUp() {
    var email = document.getElementById('signUpEmail').value;
    var password = document.getElementById('signUpPassword').value;
    var messageDiv = document.getElementById('message');
    
    // Validasi input
    if (email === "" || password === "") {
        messageDiv.innerHTML = "Email dan password wajib diisi.";
        messageDiv.style.color = "red";
        return;
    }

    if (password.length < 6) {
        messageDiv.innerHTML = "Password minimal 6 karakter.";
        messageDiv.style.color = "red";
        return;
    }

    // Send registration data to PHP
    fetch('api.php?action=register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageDiv.innerHTML = "Pendaftaran berhasil!";
            messageDiv.style.color = "green";
            
            setTimeout(function() {
                window.location.href = "login.php";
            }, 1000);
        } else {
            messageDiv.innerHTML = data.message || "Pendaftaran gagal";
            messageDiv.style.color = "red";
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.innerHTML = "Terjadi kesalahan. Silakan coba lagi.";
        messageDiv.style.color = "red";
    });
}

// Auto-focus on first input
document.addEventListener('DOMContentLoaded', function() {
    const firstInput = document.querySelector('input[type="email"], input[type="text"]');
    if (firstInput) {
        firstInput.focus();
    }
});