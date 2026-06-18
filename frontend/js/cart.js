// Cart management functions

async function loadCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartBody = document.getElementById('cart-body');
    const emptyCart = document.getElementById('empty-cart');
    const checkoutBtn = document.getElementById('checkout-btn');

    if (cart.length === 0) {
        cartBody.style.display = 'none';
        emptyCart.style.display = 'block';
        checkoutBtn.disabled = true;
        return;
    }

    emptyCart.style.display = 'none';
    cartBody.style.display = 'table-row-group';
    checkoutBtn.disabled = false;

    cartBody.innerHTML = cart.map((item, index) => `
        <tr>
            <td>${item.name}</td>
            <td>$${parseFloat(item.price).toFixed(2)}</td>
            <td>
                <input type="number" value="${item.quantity}" min="1" onchange="updateQuantity(${index}, this.value)" style="width: 50px;">
            </td>
            <td>$${(item.price * item.quantity).toFixed(2)}</td>
            <td>
                <button class="btn btn-sm" onclick="removeFromCart(${index})" style="background: #dc3545; color: white;">Remove</button>
            </td>
        </tr>
    `).join('');

    updateCartSummary();
}

function updateCartSummary() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    let subtotal = 0;

    cart.forEach(item => {
        subtotal += item.price * item.quantity;
    });

    const tax = subtotal * 0.1;
    const total = subtotal + tax;

    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '$' + tax.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}

function updateQuantity(index, quantity) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (quantity > 0) {
        cart[index].quantity = parseInt(quantity);
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    loadCart();
}

function removeFromCart(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    loadCart();
}

document.addEventListener('DOMContentLoaded', () => {
    loadCart();

    const checkoutBtn = document.getElementById('checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            const user = getCurrentUser();
            if (!user) {
                alert('Please login to checkout');
                window.location.href = 'login.html';
            } else {
                window.location.href = 'checkout.html';
            }
        });
    }
});