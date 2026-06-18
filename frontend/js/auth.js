// Authentication Helper Functions

function isLoggedIn() {
    return localStorage.getItem('user') !== null;
}

function isAdmin() {
    const admin = localStorage.getItem('admin');
    return admin !== null;
}

function getCurrentUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

function getCurrentAdmin() {
    const admin = localStorage.getItem('admin');
    return admin ? JSON.parse(admin) : null;
}

function logout() {
    localStorage.removeItem('user');
    localStorage.removeItem('admin');
    localStorage.removeItem('cart');
    window.location.href = isAdmin() ? 'admin-login.html' : 'login.html';
}

// Update user menu based on login status
function updateUserMenu() {
    const userMenu = document.getElementById('user-menu');
    if (!userMenu) return;

    const user = getCurrentUser();
    if (user) {
        userMenu.innerHTML = `
            <a href="#" onclick="showUserMenu()">👤 ${user.name}</a>
            <div id="user-dropdown" class="dropdown" style="display:none;">
                <a href="order-status.html">My Orders</a>
                <a href="#" onclick="logout()">Logout</a>
            </div>
        `;
    }
}

function showUserMenu() {
    const dropdown = document.getElementById('user-dropdown');
    if (dropdown) {
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }
}

// Update cart count on page load
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = cart.length;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    updateUserMenu();
    updateCartCount();
});