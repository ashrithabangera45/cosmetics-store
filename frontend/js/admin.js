// Admin functions

function requireAdmin() {
    if (!isAdmin()) {
        window.location.href = 'admin-login.html';
    }
}

// Load dashboard stats
async function loadDashboardStats() {
    try {
        requireAdmin();
        const productsData = await Products.getAll(1);
        const ordersData = await Orders.getAll(1);
        const usersData = await Users.getAll(1);
        const categoriesData = await Categories.getAll();

        document.getElementById('total-products').textContent = productsData.total || 0;
        document.getElementById('total-orders').textContent = ordersData.total || 0;
        document.getElementById('total-users').textContent = usersData.total || 0;
        document.getElementById('total-categories').textContent = categoriesData.categories?.length || 0;
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
    }
}

// Load all products for management
async function loadProductsForManagement() {
    try {
        const data = await Products.getAll(100);
        const tbody = document.getElementById('products-body');
        if (tbody && data.products) {
            tbody.innerHTML = data.products.map(product => `
                <tr>
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.category_name}</td>
                    <td>$${parseFloat(product.price).toFixed(2)}</td>
                    <td>${product.quantity}</td>
                    <td>${product.is_active ? 'Active' : 'Inactive'}</td>
                    <td>
                        <button class="btn btn-sm" onclick="editProduct(${product.id})">Edit</button>
                        <button class="btn btn-sm" onclick="deleteProduct(${product.id})" style="background: #dc3545; color: white;">Delete</button>
                    </td>
                </tr>
            `).join('');
        }
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

// Add product form handler
document.addEventListener('DOMContentLoaded', () => {
    const addProductForm = document.getElementById('add-product-form');
    if (addProductForm) {
        requireAdmin();
        loadCategories();
        
        addProductForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(addProductForm);
            const data = {
                category_id: formData.get('category'),
                name: formData.get('name'),
                description: formData.get('description'),
                price: parseFloat(formData.get('price')),
                quantity: parseInt(formData.get('quantity')),
                sku: formData.get('sku'),
                is_active: document.getElementById('active').checked ? 1 : 0
            };

            try {
                const result = await Products.create(data);
                const messageDiv = document.getElementById('message');
                if (result.success) {
                    messageDiv.textContent = 'Product added successfully!';
                    messageDiv.className = 'message success';
                    addProductForm.reset();
                } else {
                    messageDiv.textContent = result.message || 'Failed to add product';
                    messageDiv.className = 'message error';
                }
            } catch (error) {
                document.getElementById('message').textContent = 'Error: ' + error.message;
                document.getElementById('message').className = 'message error';
            }
        });
    }

    const categoryForm = document.getElementById('category-form');
    if (categoryForm) {
        requireAdmin();
        loadCategories();
        
        categoryForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                name: document.getElementById('cat-name').value,
                description: document.getElementById('cat-desc').value
            };

            try {
                const result = await Categories.create(data);
                const messageDiv = document.getElementById('message');
                if (result.success) {
                    messageDiv.textContent = 'Category added successfully!';
                    messageDiv.className = 'message success';
                    categoryForm.reset();
                    loadCategories();
                } else {
                    messageDiv.textContent = result.message || 'Failed to add category';
                    messageDiv.className = 'message error';
                }
            } catch (error) {
                document.getElementById('message').textContent = 'Error: ' + error.message;
                document.getElementById('message').className = 'message error';
            }
        });
    }

    // Initialize based on page
    if (document.getElementById('featured-products')) {
        loadFeaturedProducts();
        loadCategories();
    }

    if (window.location.pathname.includes('admin-dashboard')) {
        loadDashboardStats();
    }

    if (document.getElementById('products-body')) {
        loadProductsForManagement();
    }
});

async function loadCategories() {
    try {
        const data = await Categories.getAll();
        const select = document.getElementById('category');
        if (select && data.categories) {
            select.innerHTML = data.categories.map(cat => `
                <option value="${cat.id}">${cat.name}</option>
            `).join('');
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

function editProduct(id) {
    alert('Edit functionality coming soon');
}

function deleteProduct(id) {
    if (confirm('Are you sure you want to delete this product?')) {
        Products.delete(id).then(result => {
            if (result.success) {
                alert('Product deleted successfully');
                loadProductsForManagement();
            } else {
                alert('Error deleting product: ' + result.message);
            }
        });
    }
}