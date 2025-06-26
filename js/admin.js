// Function to handle student edit action
function editStudent(studentId) {
    // No confirmation dialog for edit, just redirect
    window.location.href = `../admin/admin-edit.php?type=student&id=${studentId}`;
}

// Function to handle student delete action
function deleteStudent(studentId) {
    if (confirm("Are you sure you want to delete this student? This action cannot be undone.")) {
        window.location.href = `../admin/admin-delete.php?type=student&id=${studentId}`;
    }
}

// Function to handle question edit action
function editQuestion(questionId) {
    // Redirect directly to java-quiz-edit.php with the question ID
    window.location.href = `java-quiz-edit.php?id=${questionId}`;
}

// Function to handle question delete action
function deleteQuestion(questionId) {
    if (confirm("Are you sure you want to delete this question? This action cannot be undone.")) {
        window.location.href = `java-quiz-delete.php?id=${questionId}`;
    }
}

// Function to switch between tabs
function switchTab(tabId) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Deactivate all tabs
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Activate the selected tab and content
    document.getElementById(tabId).classList.add('active');
    document.querySelector(`.tab[data-tab="${tabId}"]`).classList.add('active');
    
    // Add animation to the activated tab content
    const activeContent = document.getElementById(tabId);
    activeContent.style.opacity = "0";
    activeContent.style.transform = "translateY(20px)";
    
    setTimeout(() => {
        activeContent.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        activeContent.style.opacity = "1";
        activeContent.style.transform = "translateY(0)";
    }, 50);
}

// Hamburger menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('menuBtn');
    const hamburgerMenu = document.getElementById('hamburgerMenu');
    
    // Add animation to menu button
    if (menuBtn) {
        menuBtn.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevent event from bubbling to document
            hamburgerMenu.classList.toggle('show');
            
            // Add animation class to the button
            this.classList.toggle('active');
            
            // Change icon to X when menu is open
            const icon = this.querySelector('i');
            if (icon) {
                if (hamburgerMenu.classList.contains('show')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (hamburgerMenu && hamburgerMenu.classList.contains('show') && 
            !menuBtn.contains(event.target) && 
            !hamburgerMenu.contains(event.target)) {
            
            hamburgerMenu.classList.remove('show');
            
            // Reset the icon
            const icon = menuBtn.querySelector('i');
            if (icon) {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
            
            // Remove active class from button
            menuBtn.classList.remove('active');
        }
    });
    
    // Add click event to menu items for mobile
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            // Close the menu after clicking an item on mobile
            if (window.innerWidth <= 768) {
                hamburgerMenu.classList.remove('show');
                
                // Reset the icon
                const icon = menuBtn.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
                
                // Remove active class from button
                menuBtn.classList.remove('active');
            }
        });
    });
    
    // Tab switching
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            switchTab(tabId);
        });
    });
    
    // Set up automatic fade-out for success messages
    if (document.querySelector('.success-message')) {
        setTimeout(function() {
            const successMessage = document.querySelector('.success-message');
            // Add fade-out animation
            successMessage.style.transition = 'opacity 0.5s ease';
            successMessage.style.opacity = '0';
            
            // Remove element after fade completes
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 500);
        }, 3000); // Message displays for 3 seconds before fading
    }

    // Set up student search functionality
    const studentSearchInput = document.getElementById('studentSearchInput');
    if (studentSearchInput) {
        studentSearchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const studentCards = document.querySelectorAll('.student-card');
            
            studentCards.forEach(card => {
                const studentData = card.textContent.toLowerCase();
                if (studentData.includes(searchTerm)) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = "1";
                        card.style.transform = "translateY(0)";
                    }, 50);
                } else {
                    card.style.opacity = "0";
                    card.style.transform = "translateY(20px)";
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        });
    }

    // Set up question search functionality
    const questionSearchInput = document.getElementById('questionSearchInput');
    if (questionSearchInput) {
        questionSearchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const questionRows = document.querySelectorAll('.questions-table tbody tr');
            
            questionRows.forEach(row => {
                const questionData = row.textContent.toLowerCase();
                if (questionData.includes(searchTerm)) {
                    row.style.display = '';
                    setTimeout(() => {
                        row.style.opacity = "1";
                    }, 50);
                } else {
                    row.style.opacity = "0";
                    setTimeout(() => {
                        row.style.display = 'none';
                    }, 300);
                }
            });
        });
    }
    
    // Add hover effects to stat cards
    const statCards = document.querySelectorAll('.stat-card');
    if (statCards) {
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                const icon = this.querySelector('.stat-icon');
                if (icon) {
                    icon.style.transition = 'transform 0.3s ease';
                    icon.style.transform = 'scale(1.2) translateY(-5px)';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                const icon = this.querySelector('.stat-icon');
                if (icon) {
                    icon.style.transform = 'scale(1) translateY(0)';
                }
            });
        });
    }
    
    // Animate elements on page load
    animateOnLoad();
});

// Function to animate elements when the page loads
function animateOnLoad() {
    // Animate the dashboard header
    const dashboardHeader = document.querySelector('.dashboard-header');
    if (dashboardHeader) {
        dashboardHeader.style.opacity = "0";
        dashboardHeader.style.transform = "translateY(-20px)";
        
        setTimeout(() => {
            dashboardHeader.style.transition = "opacity 0.8s ease, transform 0.8s ease";
            dashboardHeader.style.opacity = "1";
            dashboardHeader.style.transform = "translateY(0)";
        }, 300);
    }
    
    // Animate the tabs with a slight delay for each
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach((tab, index) => {
        tab.style.opacity = "0";
        tab.style.transform = "translateY(20px)";
        
        setTimeout(() => {
            tab.style.transition = "opacity 0.5s ease, transform 0.5s ease";
            tab.style.opacity = "1";
            tab.style.transform = "translateY(0)";
        }, 400 + (index * 100));
    });
    
    // Animate stat cards with staggered delay
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        
        setTimeout(() => {
            card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }, 600 + (index * 150));
    });
    
    // Animate student cards with a staggered effect
    const studentCards = document.querySelectorAll('.student-card');
    studentCards.forEach((card, index) => {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        
        setTimeout(() => {
            card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }, 500 + (index * 100));
    });
}