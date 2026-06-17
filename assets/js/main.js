/* ================================
   MAIN JAVASCRIPT FILE
   ================================ */

class App {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadTheme();
        this.checkAuth();
    }

    /* ================= SETUP EVENT LISTENERS ================= */
    setupEventListeners() {
        // Theme toggle
        const themeToggle = document.getElementById('theme-toggle');
        if(themeToggle) {
            themeToggle.addEventListener('click', () => this.toggleTheme());
        }

        // Forms
        const forms = document.querySelectorAll('form[data-ajax]');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        });
    }

    /* ================= THEME MANAGEMENT ================= */
    loadTheme() {
        const savedTheme = localStorage.getItem('theme') || 'dark';
        this.setTheme(savedTheme);
    }

    setTheme(theme) {
        if(theme === 'light') {
            document.body.classList.add('light-mode');
        } else {
            document.body.classList.remove('light-mode');
        }
        localStorage.setItem('theme', theme);
    }

    toggleTheme() {
        const currentTheme = localStorage.getItem('theme') || 'dark';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.setTheme(newTheme);
    }

    /* ================= FORM HANDLING ================= */
    async handleFormSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const action = form.dataset.action || form.action;

        try {
            const response = await fetch(action, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if(data.success) {
                this.showNotification('Success!', data.message || 'Operation completed', 'success');
                if(data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                }
            } else {
                this.showNotification('Error', data.error || 'Something went wrong', 'error');
            }
        } catch(error) {
            console.error('Error:', error);
            this.showNotification('Error', 'Network error', 'error');
        }
    }

    /* ================= NOTIFICATIONS ================= */
    showNotification(title, message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <strong>${title}</strong>
                <p>${message}</p>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">×</button>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    /* ================= AUTH CHECK ================= */
    async checkAuth() {
        // This will be implemented based on your auth system
    }
}

// Initialize app
document.addEventListener('DOMContentLoaded', () => {
    window.app = new App();
});

/* ================= UTILITY FUNCTIONS ================= */
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatTime(date) {
    return new Date(date).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

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
