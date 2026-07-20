/**
 * Common JavaScript Functions for Admin Panel
 */

// CSRF Token
let csrfToken = '';

// Get CSRF token from session
async function getCSRFToken() {
    try {
        const response = await fetch('api/csrf.php');
        const data = await response.json();
        csrfToken = data.token;
        return csrfToken;
    } catch (error) {
        console.error('Error getting CSRF token:', error);
        return '';
    }
}

// Check authentication
function checkAuth() {
    // Simple check - will be validated server-side
    fetch('api/statistics.php', {
        credentials: 'include'
    }).then(response => {
        if (response.status === 401) {
            window.location.href = 'pintu-masuk-rahasia.html';
        } else {
            // Load user info
            loadUserInfo();
            // Get CSRF token
            getCSRFToken();
        }
    }).catch(() => {
        window.location.href = 'pintu-masuk-rahasia.html';
    });
}

// Load user info
async function loadUserInfo() {
    try {
        const response = await fetch('api/users.php?id=current', {
            credentials: 'include'
        });
        const data = await response.json();
        
        if (data.success && data.data) {
            const user = data.data;
            
            // Update UI
            const userName = document.getElementById('userName');
            const userRole = document.getElementById('userRole');
            
            if (userName) userName.textContent = user.nama_lengkap || user.username;
            if (userRole) userRole.textContent = user.role_name || 'User';

            // Show/hide admin menus
            const usersMenu = document.getElementById('usersMenu');
            if (usersMenu && ['super_admin', 'admin'].includes(user.role_name)) {
                usersMenu.classList.remove('hidden');
            }

            // Store user info
            window.currentUser = user;
        }
    } catch (error) {
        console.error('Error loading user info:', error);
    }
}

// Fetch API with credentials and CSRF
async function fetchAPI(url, options = {}) {
    const defaultOptions = {
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        }
    };

    const mergedOptions = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...options.headers
        }
    };

    const response = await fetch(url, mergedOptions);
    
    if (response.status === 401) {
        window.location.href = 'pintu-masuk-rahasia.html';
        throw new Error('Unauthorized');
    }

    return response.json();
}

// Logout
async function logout() {
    try {
        await fetch('api/logout.php', {
            method: 'POST',
            credentials: 'include'
        });
    } catch (error) {
        console.error('Logout error:', error);
    }
    localStorage.removeItem('khk_token');
    window.location.href = 'pintu-masuk-rahasia.html';
}

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', (e) => {
            if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                sidebar.classList.add('-translate-x-full');
            }
        });
    }

    // Logout button
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', logout);
    }
});

// Format date
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Truncate text
function truncate(text, length = 100) {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
}

// Show toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    toast.className = `fixed bottom-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Confirm dialog
function confirmDialog(message) {
    return new Promise((resolve) => {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-xl p-6 max-w-md mx-4">
                <p class="text-gray-800 mb-6">${message}</p>
                <div class="flex justify-end gap-3">
                    <button class="cancel-btn px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                    <button class="confirm-btn px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Hapus</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        modal.querySelector('.cancel-btn').addEventListener('click', () => {
            modal.remove();
            resolve(false);
        });
        
        modal.querySelector('.confirm-btn').addEventListener('click', () => {
            modal.remove();
            resolve(true);
        });
    });
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
