        </main> <!-- End Admin Content -->
    </div> <!-- End Admin Main Container -->
    
    <!-- Mobile Navigation Bar (Footer) -->
    <nav class="mobile-nav">
         <ul>
            <li><a href="dashboard.php" class="<?php echo ($active_page == 'dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="manage_slider.php" class="<?php echo ($active_page == 'manage_slider.php') ? 'active' : ''; ?>"><i class="fas fa-images"></i><span>Slider</span></a></li>
            <li><a href="manage_stories.php" class="<?php echo ($active_page == 'manage_stories.php') ? 'active' : ''; ?>"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
            <li><a href="manage_ticker.php" class="<?php echo ($active_page == 'manage_ticker.php') ? 'active' : ''; ?>"><i class="fas fa-newspaper"></i><span>Ticker</span></a></li>
            <li><a href="manage_history.php" class="<?php echo ($active_page == 'manage_history.php') ? 'active' : ''; ?>"><i class="fas fa-landmark"></i><span>History</span></a></li>
            <li><a href="chatbot_leads.php" class="<?php echo ($active_page == 'chatbot_leads.php') ? 'active' : ''; ?>"><i class="fas fa-headset"></i><span>Leads</span></a></li>
            <li><a href="manage_donations.php" class="<?php echo ($active_page == 'manage_donations.php') ? 'active' : ''; ?>"><i class="fas fa-donate"></i><span>Donations</span></a></li>
            <li><a href="profile.php" class="<?php echo ($active_page == 'profile.php') ? 'active' : ''; ?>"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
        </ul>
    </nav>

    <!-- This script is only needed for the dashboard page, so we load it conditionally -->
    <?php if (isset($active_page) && $active_page == 'dashboard.php'): ?>
        <script>
            // This global variable is needed by the external JS file and must be defined on the dashboard page itself.
            const festivals = <?php echo json_encode($festivals ?? []); ?>;
        </script>
        <script src="assets/js/dashboard.js"></script>
    <?php endif; ?>
</body>
</html>

