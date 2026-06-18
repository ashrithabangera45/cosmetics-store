// Load featured products on home page
async function loadFeaturedProducts() {
    try {
        const data = await Products.getAll(6);
        if (data.success && data.products) {
            const container = document.getElementById('featured-products');
            if (container) {
                container.innerHTML = data.products.map(product => `
                    <div class="product-card">
                        <div class="product-image">
                            <img src="${product.image_url || 'https://via.placeholder.com/250x200'}" alt="${product.name}">
                        </div>
                        <div class="product-info">
                            <div class="product-name">${product.name}</div>
                            <div class="product-category">${product.category_name}</div>
                            <div class="product-price">$${parseFloat(product.price).toFixed(2)}</div>
                            <div class="product-actions">
                                <button class="btn btn-primary btn-sm" onclick="addToCart(${product.id}, '${product.name}', ${product.price})">Add to Cart</button>
                                <button class="btn btn-secondary btn-sm" onclick="viewProduct(${product.id})">View</button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }
    } catch (error) {
        console.error('Error loading featured products:', error);
    }
}

// Load categories
async function loadCategories() {
    try {
        const data = await Categories.getAll();
        if (data.success && data.categories) {
            const container = document.getElementById('categories');
            if (container) {
                container.innerHTML = data.categories.map(cat => `
                    <div class="category-card" onclick="filterByCategory(${cat.id})">
                        <h3>${cat.name}</h3>
                        <p>${cat.description || 'Explore more'}</p>
                    </div>
                `).join('');
            }
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

// Add to cart
function addToCart(productId, productName, price) {
    const user = getCurrentUser();
    if (!user) {
        alert('Please login to add items to cart');
        window.location.href = 'login.html';
        return;
    }

    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: price,
            quantity: 1
        });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    alert('Item added to cart!');
}

// View product details
function viewProduct(productId) {
    window.location.href = `product-detail.html?id=${productId}`;
}

// Filter by category
function filterByCategory(categoryId) {
    window.location.href = `products.html?category=${categoryId}`;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadFeaturedProducts();
    loadCategories();
});