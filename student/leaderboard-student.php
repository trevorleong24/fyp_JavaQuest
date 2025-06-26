<?php
include("../conn/conn.php");
session_start();

// Check if user is logged in and has student privileges
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login/login.php");
    exit();
}

// Fetch leaderboard data (check if gameprogress table exists first)
$leaderboard_result = false; // Default to false if table doesn't exist
$check_progress_table = "SHOW TABLES LIKE 'gameprogress'";
$progress_table_check = mysqli_query($conn, $check_progress_table);

if ($progress_table_check && mysqli_num_rows($progress_table_check) > 0) {
    // gameprogress table exists, try to query leaderboard data
    $leaderboard_query = "SELECT s.username, s.student_id, s.gender, 
                      MAX(p.level_achieved) AS level_achieved,
                      MAX(p.score) AS highest_score
                      FROM STUDENT s
                      LEFT JOIN gameprogress p ON s.student_id = p.student_id
                      GROUP BY s.student_id
                      ORDER BY highest_score DESC, level_achieved DESC, s.username ASC";

    $leaderboard_result = mysqli_query($conn, $leaderboard_query);
    if (!$leaderboard_result) {
        // Query failed
        $leaderboard_result = false;
    }
} else {
    // If gameprogress table doesn't exist, create a simple query with just student data
    $leaderboard_query = "SELECT username, student_id, gender, 
                          'N/A' as level_achieved, 
                          0 as highest_score 
                          FROM STUDENT
                          ORDER BY username";
    $leaderboard_result = mysqli_query($conn, $leaderboard_query);
}

// Get total students count
$student_count = 0;
if ($leaderboard_result) {
    $student_count = mysqli_num_rows($leaderboard_result);
}

// Get current user's rank
$current_user_rank = 0;
$current_user_id = $_SESSION['student_id'];

if ($leaderboard_result) {
    // Reset the result pointer
    mysqli_data_seek($leaderboard_result, 0);
    $rank = 1;
    while ($row = mysqli_fetch_assoc($leaderboard_result)) {
        if ($row['student_id'] == $current_user_id) {
            $current_user_rank = $rank;
            break;
        }
        $rank++;
    }
    // Reset the pointer again for display
    mysqli_data_seek($leaderboard_result, 0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JavaQuest - Leaderboard</title>
    <link rel="stylesheet" href="../css/leaderboard-student.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body <?php if (isset($_SESSION['username'])) echo 'class="logged-in"'; ?>>
    <header>
        <div class="logo-nav">
            <a href="../student/student-homepage.php" class="logo">JavaQuest</a>
            <div class="nav-links">
                <a href="../about-us/about-us.php" class="nav-link">About</a>
                <a href="../term-privacy/term-privacy.php" class="nav-link">Terms & Privacy</a>
            </div>
        </div>
        <div class="auth-social">
            <?php if (isset($_SESSION['username'])): ?>
                <button class="menu-btn" id="menuBtn">
                    <i class="fas fa-bars"></i>
                </button>
            <?php else: ?>
                <a href="../login/login.php" class="login-btn">Login</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Hamburger Menu with improved implementation -->
    <div class="hamburger-menu" id="hamburgerMenu">
        <a href="../student/student-homepage.php" class="menu-item"><i class="fas fa-home"></i>Home</a>
        <a href="leaderboard-student.php" class="menu-item"><i class="fas fa-trophy"></i>Leaderboard</a>
        <a href="progress-student.php" class="menu-item"><i class="fas fa-chart-line"></i>Progress</a>
        <a href="../student/student-profile.php" class="menu-item"><i class="fas fa-user"></i>Profile</a>
        <a href="../logout/logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i>Log Out</a>
    </div>

    <div class="main-container">
        <div class="dashboard-header">
            <h1>Leaderboard</h1>
            <p class="dashboard-subtitle">See how you rank against other JavaQuest players</p>
        </div>

        <div class="content-container">
            <div class="search-container">
                <input type="text" id="leaderboardSearchInput" class="search-bar" placeholder="Search players...">
                <button class="search-button" id="searchBtn">
                    <i class="fas fa-search"></i> Search
                </button>
            </div>

            <div id="searchResultsInfo" class="search-results-info" style="display: none;">
                <i class="fas fa-info-circle"></i> Found <span id="resultCount">0</span> players matching "<span id="searchTerm"></span>"
                <a href="#" id="clearSearch" class="reset-search"><i class="fas fa-times"></i> Clear search</a>
            </div>

            <?php if ($current_user_rank > 0): ?>
            <div class="user-stats">
                <div class="user-rank">Your Current Rank: <span class="highlight">#<?php echo $current_user_rank; ?></span></div>
            </div>
            <?php endif; ?>

            <div class="leaderboard-info">
                <p>Total Players: <span class="highlight"><?php echo $student_count; ?></span></p>
            </div>

            <div class="leaderboard-container">
                <table class="leaderboard-table" id="leaderboardTable">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Player</th>
                            <th>Level Achieved</th>
                            <th>Highest Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($leaderboard_result && mysqli_num_rows($leaderboard_result) > 0) {
                            $rank = 1;
                            while ($row = mysqli_fetch_assoc($leaderboard_result)) {
                                $rankClass = ($rank <= 3) ? "rank-$rank" : "";
                                $isCurrentUser = ($row['student_id'] == $current_user_id) ? "current-user" : "";
                                $gender = strtolower($row['gender']);
                                $avatar = ($gender === 'male') ? '../image/male.png' : 
                                         (($gender === 'female') ? '../image/female.png' : '../image/default.png');
                                
                                // Handle NULL values
                                $level = ($row['level_achieved'] ? htmlspecialchars($row['level_achieved']) : 'N/A');
                                $score = ($row['highest_score'] ? $row['highest_score'] : '0');
                        ?>
                            <tr class="<?php echo $isCurrentUser; ?>">
                                <td class="rank <?php echo $rankClass; ?>"><?php echo $rank; ?></td>
                                <td>
                                    <div class="student-info">
                                        <img src="<?php echo $avatar; ?>" alt="Player Avatar">
                                        <span><?php echo htmlspecialchars($row['username']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo $level; ?></td>
                                <td><?php echo $score; ?></td>
                            </tr>
                        <?php
                                $rank++;
                            }
                        } else {
                            echo '<tr><td colspan="4" style="text-align: center;">No leaderboard data available yet.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination Controls -->
    <div class="pagination-container">
        <button id="prevPageBtn" class="page-btn"><i class="fas fa-chevron-left"></i> Previous</button>
        <span id="pageInfo">Page <span id="currentPage">1</span> of <span id="totalPages">1</span></span>
        <button id="nextPageBtn" class="page-btn">Next <i class="fas fa-chevron-right"></i></button>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-top">
                <div class="footer-contact">
                    <p>support@javaquest.com</p>
                    <p>We're here whenever you need help, just send us an email.</p>
                </div>
            </div>
            
            <div class="footer-links">
                <div class="footer-section">
                    <h3>About Us</h3>
                    <ul>
                        <li><a href="../about-us/about-us.php">About JavaQuest</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Resources</h3>
                    <ul>
                        <li><a href="https://www.geeksforgeeks.org/java-cheat-sheet/" target="_blank">Java Cheatsheet</a></li>
                        <li><a href="../term-privacy/term-privacy.php">Terms & Privacy</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="leaderboard-student.php">Leaderboard</a></li>
                        <li><a href="progress-student.php">Progress</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-copyright">
                <p>Copyright &copy; 2025 JavaQuest. All rights reserved</p>
            </div>
        </div>
    </footer>

    <script>
    // Improved hamburger menu JavaScript matching admin.php
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('menuBtn');
        const hamburgerMenu = document.getElementById('hamburgerMenu');
        
        // Toggle menu when menu button is clicked
        menuBtn.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevent event from bubbling to document
            hamburgerMenu.classList.toggle('show');
            
            // Add active class to button
            this.classList.toggle('active');
            
            // Change icon to X when menu is open
            const icon = this.querySelector('i');
            if (hamburgerMenu.classList.contains('show')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Close menu when clicking outside of it
        document.addEventListener('click', function(event) {
            if (hamburgerMenu.classList.contains('show') && 
                !hamburgerMenu.contains(event.target) && 
                !menuBtn.contains(event.target)) {
                
                hamburgerMenu.classList.remove('show');
                
                // Reset icon and button style
                const icon = menuBtn.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
                
                // Remove active class from button
                menuBtn.classList.remove('active');
            }
        });
        
        // Initialize search visibility attribute for all rows
        document.querySelectorAll('#leaderboardTable tbody tr').forEach(row => {
            row.dataset.searchVisible = 'true';
        });
        
        // Leaderboard search functionality
        document.getElementById('searchBtn').addEventListener('click', function() {
            const searchInput = document.getElementById('leaderboardSearchInput').value.toLowerCase();
            const tableRows = document.querySelectorAll('#leaderboardTable tbody tr');
            
            tableRows.forEach(row => {
                const studentName = row.querySelector('.student-info span');
                if (studentName) {
                    const studentText = studentName.textContent.toLowerCase();
                    if (studentText.includes(searchInput)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
            
            // Focus on the search input after clicking the search button
            document.getElementById('leaderboardSearchInput').focus();
        });
        
        // Add keyboard shortcut (Enter key) for search
        document.getElementById('leaderboardSearchInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('searchBtn').click();
                e.preventDefault();
            }
        });
        
        // Reset search when input is cleared
        document.getElementById('clearSearch').addEventListener('click', function(e) {
            e.preventDefault();
            const searchInput = document.getElementById('leaderboardSearchInput');
            searchInput.value = '';
            
            // Trigger the input event to reset the table
            const inputEvent = new Event('input', { bubbles: true });
            searchInput.dispatchEvent(inputEvent);
            
            // Focus back on search input
            searchInput.focus();
        });
        
        // Live search as user types
        document.getElementById('leaderboardSearchInput').addEventListener('input', function() {
            const searchInput = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#leaderboardTable tbody tr');
            
            let visibleCount = 0;
            tableRows.forEach(row => {
                const studentName = row.querySelector('.student-info span');
                if (studentName) {
                    const studentText = studentName.textContent.toLowerCase();
                    if (studentText.includes(searchInput)) {
                        row.style.display = '';
                        row.dataset.searchVisible = 'true';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                        row.dataset.searchVisible = 'false';
                    }
                }
            });
            
            // Show or hide search results info
            const searchResultsInfo = document.getElementById('searchResultsInfo');
            if (searchInput.length > 0) {
                document.getElementById('resultCount').textContent = visibleCount;
                document.getElementById('searchTerm').textContent = searchInput;
                searchResultsInfo.style.display = 'block';
                
                // Disable pagination during search
                document.querySelector('.pagination-container').style.display = 'none';
            } else {
                searchResultsInfo.style.display = 'none';
                // Reset all rows to visible when search is cleared
                tableRows.forEach(row => {
                    row.style.display = '';
                    row.dataset.searchVisible = 'true';
                });
                
                // Re-enable pagination when search is cleared
                document.querySelector('.pagination-container').style.display = 'flex';
                setupPagination();
            }
        });
        
        // Pagination functionality
        const rowsPerPage = 10;
        let currentPage = 1;
        
        function setupPagination() {
            const tableRows = document.querySelectorAll('#leaderboardTable tbody tr');
            const visibleRows = Array.from(tableRows).filter(row => 
                row.dataset.searchVisible !== 'false'
            );
            const totalRows = visibleRows.length;
            const totalPages = Math.max(1, Math.ceil(totalRows / rowsPerPage));
            
            // Adjust current page if necessary
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }
            
            document.getElementById('totalPages').textContent = totalPages;
            document.getElementById('currentPage').textContent = currentPage;
            
            // Enable/disable pagination buttons
            document.getElementById('prevPageBtn').disabled = (currentPage === 1);
            document.getElementById('nextPageBtn').disabled = (currentPage === totalPages || totalPages === 0);
            
            // Hide all rows first
            tableRows.forEach(row => {
                if (row.dataset.searchVisible !== 'false') {
                    row.classList.add('pagination-hidden');
                }
            });
            
            // Show only rows for current page that pass the search filter
            const startIndex = (currentPage - 1) * rowsPerPage;
            const endIndex = Math.min(startIndex + rowsPerPage, totalRows);
            
            for (let i = startIndex; i < endIndex; i++) {
                if (visibleRows[i]) {
                    visibleRows[i].classList.remove('pagination-hidden');
                }
            }
        }
        
        // Add pagination CSS dynamically
        const style = document.createElement('style');
        style.textContent = `
            .pagination-hidden {
                display: none !important;
            }
        `;
        document.head.appendChild(style);
        
        // Initialize pagination
        setupPagination();
        
        // Pagination event listeners
        document.getElementById('prevPageBtn').addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                setupPagination();
            }
        });
        
        document.getElementById('nextPageBtn').addEventListener('click', function() {
            const tableRows = document.querySelectorAll('#leaderboardTable tbody tr');
            const totalPages = Math.ceil(tableRows.length / rowsPerPage);
            
            if (currentPage < totalPages) {
                currentPage++;
                setupPagination();
            }
        });
        
        // Update pagination when search filtering occurs
        function updatePaginationAfterSearch() {
            currentPage = 1; // Reset to first page after search
            setupPagination();
        }
        
        // Modify the existing search functions to update pagination
        const originalSearchBtnClick = document.getElementById('searchBtn').onclick;
        document.getElementById('searchBtn').onclick = function() {
            if (originalSearchBtnClick) originalSearchBtnClick();
            updatePaginationAfterSearch();
        };
        
        const originalInputHandler = document.getElementById('leaderboardSearchInput').oninput;
        document.getElementById('leaderboardSearchInput').oninput = function() {
            if (originalInputHandler) originalInputHandler.call(this);
            updatePaginationAfterSearch();
        };
    });
    </script>
</body>
</html> 