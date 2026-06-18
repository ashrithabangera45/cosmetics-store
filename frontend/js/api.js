// API Configuration
const API_BASE_URL = '../../../backend/api';

// Helper function to make API calls
async function apiCall(endpoint, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        }
    };

    if (data) {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(`${API_BASE_URL}/${endpoint}`, options);
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Auth API calls
const Auth = {
    login: (email, password) => apiCall('auth.php', 'POST', { action: 'login', email, password }),
    register: (name, email, password, phone = '') => apiCall('auth.php', 'POST', { action: 'register', name, email, password, phone }),
    logout: () => apiCall('auth.php', 'POST', { action: 'logout' })
};

// Products API calls
const Products = {
    getAll: (limit = 10, offset = 0) => apiCall(`products.php?limit=${limit}&offset=${offset}`),
    getById: (id) => apiCall(`products.php?id=${id}`),
    getByCategory: (categoryId, limit = 10, offset = 0) => apiCall(`products.php?category=${categoryId}&limit=${limit}&offset=${offset}`),
    search: (keyword, limit = 10, offset = 0) => apiCall(`products.php?search=${keyword}&limit=${limit}&offset=${offset}`),
    create: (data) => apiCall('products.php', 'POST', { action: 'create', ...data }),
    update: (id, data) => apiCall('products.php', 'POST', { action: 'update', id, ...data }),
    delete: (id) => apiCall('products.php', 'POST', { action: 'delete', id })
};

// Categories API calls
const Categories = {
    getAll: () => apiCall('categories.php'),
    getById: (id) => apiCall(`categories.php?id=${id}`),
    create: (data) => apiCall('categories.php', 'POST', { action: 'create', ...data }),
    update: (id, data) => apiCall('categories.php', 'POST', { action: 'update', id, ...data }),
    delete: (id) => apiCall('categories.php', 'POST', { action: 'delete', id })
};

// Orders API calls
const Orders = {
    getUserOrders: () => apiCall('orders.php?action=get_user_orders'),
    getById: (id) => apiCall(`orders.php?id=${id}`),
    getAll: (limit = 10, offset = 0) => apiCall(`orders.php?limit=${limit}&offset=${offset}`),
    create: (data) => apiCall('orders.php', 'POST', { action: 'create', ...data }),
    updateStatus: (id, status) => apiCall('orders.php', 'POST', { action: 'update_status', id, status })
};

// Users API calls
const Users = {
    getAll: (limit = 10, offset = 0) => apiCall(`users.php?limit=${limit}&offset=${offset}`),
    getById: (id) => apiCall(`users.php?id=${id}`),
    search: (keyword, limit = 10, offset = 0) => apiCall(`users.php?search=${keyword}&limit=${limit}&offset=${offset}`),
    update: (id, data) => apiCall('users.php', 'POST', { action: 'update', id, ...data }),
    delete: (id) => apiCall('users.php', 'POST', { action: 'delete', id })
};