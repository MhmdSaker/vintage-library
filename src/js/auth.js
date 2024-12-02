// Simulated user store
let currentUser = null;

// Auth Modal HTML templates
const loginTemplate = `
    <div class="modal-header">
        <h5 class="modal-title font-playfair">Welcome Back</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <form id="loginForm">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" required>
            </div>
            <button type="submit" class="btn vintage-btn w-100">Sign In</button>
        </form>
        <p class="text-center mt-3">
            Don't have an account? 
            <a href="#" onclick="switchAuthMode('register')">Sign Up</a>
        </p>
    </div>
`;

const registerTemplate = `
    <div class="modal-header">
        <h5 class="modal-title font-playfair">Create Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <form id="registerForm">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" required>
            </div>
            <button type="submit" class="btn vintage-btn w-100">Create Account</button>
        </form>
        <p class="text-center mt-3">
            Already have an account? 
            <a href="#" onclick="switchAuthMode('login')">Sign In</a>
        </p>
    </div>
`;

// Auth Modal functionality
function openAuthModal() {
    const modal = new bootstrap.Modal(document.getElementById('authModal'));
    document.querySelector('#authModal .modal-content').innerHTML = loginTemplate;
    modal.show();
    
    // Add form submit handlers
    const form = document.querySelector('#loginForm');
    if (form) {
        form.addEventListener('submit', handleLogin);
    }
}

function switchAuthMode(mode) {
    const content = document.querySelector('#authModal .modal-content');
    content.innerHTML = mode === 'login' ? loginTemplate : registerTemplate;
    
    // Add form submit handlers
    const form = document.querySelector(`#${mode}Form`);
    if (form) {
        form.addEventListener('submit', mode === 'login' ? handleLogin : handleRegister);
    }
}

function handleLogin(e) {
    e.preventDefault();
    const form = e.target;
    const email = form.querySelector('input[type="email"]').value;
    const password = form.querySelector('input[type="password"]').value;

    // Simulate login
    currentUser = {
        email,
        username: email.split('@')[0],
        borrowedBooks: [],
        favoriteBooks: []
    };

    // Save to localStorage
    localStorage.setItem('currentUser', JSON.stringify(currentUser));

    updateAuthUI();
    bootstrap.Modal.getInstance(document.getElementById('authModal')).hide();
}

function handleRegister(e) {
    e.preventDefault();
    const form = e.target;
    const username = form.querySelector('input[type="text"]').value;
    const email = form.querySelector('input[type="email"]').value;
    const password = form.querySelector('input[type="password"]').value;

    // Simulate registration (replace with real authentication)
    currentUser = {
        email,
        username,
        borrowedBooks: [],
        favoriteBooks: []
    };

    updateAuthUI();
    bootstrap.Modal.getInstance(document.getElementById('authModal')).hide();
}

function updateAuthUI() {
    const authButtons = document.getElementById('authButtons');
    const profileButton = document.getElementById('profileButton');
    
    if (currentUser) {
        authButtons.innerHTML = `
            <div class="dropdown">
                <button class="btn btn-outline-light" type="button" data-bs-toggle="dropdown" data-bs-toggle="tooltip" data-bs-placement="bottom" title="${currentUser.username}">
                    <i data-lucide="user"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="borrowed.html">Borrowed Books</a></li>
                    <li><a class="dropdown-item" href="favorites.html">Favorites</a></li>
                    <li><a class="dropdown-item" href="reading-list.html">Reading List</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="handleSignOut()">Sign Out</a></li>
                </ul>
            </div>
        `;
        profileButton.style.display = 'block';
    } else {
        authButtons.innerHTML = `
            <button class="btn btn-outline-light" onclick="openAuthModal()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Sign In">
                <i data-lucide="log-in"></i>
            </button>
        `;
        profileButton.style.display = 'none';
    }
    lucide.createIcons();
    
    // Reinitialize tooltips after updating the DOM
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function handleSignOut() {
    currentUser = null;
    localStorage.removeItem('currentUser');
    updateAuthUI();
} 