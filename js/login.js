// Get references to DOM elements
const loginTab = document.getElementById('login-tab');
const signupTab = document.getElementById('signup-tab');
const loginContainer = document.getElementById('login-container');
const studentSignupContainer = document.getElementById('student-signup-container');
const switchToSignup = document.getElementById('switch-to-signup');
const switchToLogin = document.getElementById('switch-to-login');

// Set the login form as the default view on page load
window.addEventListener('DOMContentLoaded', () => {
    loginContainer.classList.add('active');
    studentSignupContainer.classList.remove('active');
    loginTab.classList.add('active');
    signupTab.classList.remove('active');
});

// Utility to clear error message (optional styling hook)
function clearErrorMessage() {
    const errorMessage = document.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
}

// Switch to Sign-Up Tab
signupTab.addEventListener('click', (e) => {
    e.preventDefault();
    loginContainer.classList.remove('active');
    studentSignupContainer.classList.add('active');
    signupTab.classList.add('active');
    loginTab.classList.remove('active');
    clearErrorMessage();
});

// Switch to Login Tab
loginTab.addEventListener('click', (e) => {
    e.preventDefault();
    loginContainer.classList.add('active');
    studentSignupContainer.classList.remove('active');
    loginTab.classList.add('active');
    signupTab.classList.remove('active');
    clearErrorMessage();
});

// Footer link: "Create an account"
switchToSignup.addEventListener('click', (e) => {
    e.preventDefault();
    signupTab.click();
});

// Footer link: "Log in"
switchToLogin.addEventListener('click', (e) => {
    e.preventDefault();
    loginTab.click();
});
