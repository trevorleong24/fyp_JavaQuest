// Select the authentication container
const authContainer = document.getElementById("auth-container");

// Mock user login status
const isLoggedIn = false; // Change to `true` for testing logged-in state

// Render Login Button if user is not logged in
if (!isLoggedIn) {
    authContainer.innerHTML = `
        <button class="login-btn">Login</button>
    `;
} else {
    // Render User Profile if user is logged in
    authContainer.innerHTML = `
        <div class="user-profile">
            <img src="https://via.placeholder.com/40" alt="User Profile" />
            <span>Adrew123</span>
        </div>
    `;
}


