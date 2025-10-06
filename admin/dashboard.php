<?php 
// Start a session only if one isn't already active to prevent errors.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
require_once '../config/db.php';

// Fetch admin details for the header
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT display_name, profile_photo_url FROM admin_users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_details = $stmt->get_result()->fetch_assoc();
$admin_name = $admin_details['display_name'] ?? 'Admin';
$profile_photo = $admin_details['profile_photo_url'] ?? 'assets/images/default-avatar.png';
$active_page = basename($_SERVER['PHP_SELF']);

// --- LIVE DATA FETCHING ---
$chatbot_leads_result = $conn->query("SELECT COUNT(*) as count FROM chatbot_leads");
$total_chatbot_leads = ($chatbot_leads_result) ? $chatbot_leads_result->fetch_assoc()['count'] : 0;
$joined_people_result = $conn->query("SELECT COUNT(*) as count FROM joined_people");
$total_joined_people = ($joined_people_result) ? $joined_people_result->fetch_assoc()['count'] : 0;
$total_leads = $total_chatbot_leads + $total_joined_people;
$visits_result = $conn->query("SELECT COUNT(*) as count FROM site_visits");
$site_visits = ($visits_result) ? $visits_result->fetch_assoc()['count'] : 0;

function formatNumber($number) {
    return number_format($number);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Shivarchanam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-red: #9F0102; --accent-orange: #FF6D01; --light-bg: #FFEFDA;
            --dark-text: #333333; --white: #ffffff; --border-color: #e0e0e0; --body-bg: #f4f5f7;
            --success-green: #28a745; --info-blue: #17a2b8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--body-bg); color: var(--dark-text); min-height: 100vh; }
        
        /* --- HEADER --- */
        .admin-header { background-color: var(--white); padding: 0.75rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); position: fixed; width: 100%; top: 0; z-index: 1000; }
        .header-content { display: flex; justify-content: space-between; align-items: center; max-width: 1600px; margin: 0 auto; }
        .header-left { display: flex; align-items: center; gap: 1rem; }
        .header-left h1 { font-size: 1.5rem; color: var(--primary-red); }
        .admin-profile { display: flex; align-items: center; gap: 1rem; }
        .profile-link img { width: 45px; height: 45px; border-radius: 50%; border: 2px solid var(--primary-red); object-fit: cover; }
        .header-info { display: flex; align-items: center; gap: 1.5rem; }
        .admin-welcome { text-align: right; }
        .admin-welcome strong { display: block; font-size: 1.1rem; }
        .admin-welcome span { font-size: 0.9rem; color: #666; }
        
        /* --- MAIN LAYOUT --- */
        .admin-main-container { display: grid; grid-template-columns: 250px 1fr; padding-top: 75px; min-height: 100vh; }
        .admin-nav { background-color: var(--white); border-right: 1px solid var(--border-color); padding: 2rem 0; }
        .admin-nav ul { list-style: none; }
        .admin-nav ul li a { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; text-decoration: none; color: var(--dark-text); border-left: 4px solid transparent; transition: all 0.3s; }
        .admin-nav ul li a:hover, .admin-nav ul li a.active { background-color: var(--light-bg); border-left-color: var(--primary-red); color: var(--primary-red); }
        .admin-nav ul li a .fas { width: 20px; text-align: center; }
        .admin-content { padding: 2rem; overflow-y: auto; }

        /* --- DASHBOARD CONTENT --- */
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; }
        .summary-card { background: var(--white); padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 1.5rem; text-decoration: none; color: var(--dark-text); transition: transform 0.2s, box-shadow 0.2s; }
        .summary-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .summary-icon { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; flex-shrink: 0; }
        .summary-text strong { font-size: 1.8rem; display: block; }
        .summary-text span { color: #666; }

        /* --- MOBILE NAVIGATION --- */
        .mobile-hamburger { display: none; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--dark-text); }
        .mobile-side-menu { position: fixed; top: 0; right: -100%; width: 280px; height: 100%; background: var(--white); box-shadow: -2px 0 10px rgba(0,0,0,0.2); transition: right 0.4s ease-in-out; z-index: 2001; padding-top: 4rem; }
        .mobile-side-menu.active { right: 0; }
        .mobile-side-menu .admin-nav { padding: 0; border: none; box-shadow: none; display: block !important; }
        .mobile-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); opacity: 0; visibility: hidden; transition: 0.3s; z-index: 2000; }
        .mobile-overlay.active { opacity: 1; visibility: visible; }
        
        @media (max-width: 768px) {
            .admin-main-container { display: block; }
            .admin-header { padding: 0.75rem 1rem; }
            .admin-main-container > .admin-nav { display: none; } 
            .mobile-hamburger { display: block; }
            .admin-welcome { display: none; }
            .header-left h1 { font-size: 1.2rem; }
            .admin-content { padding: 1rem; }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-content">
            <div class="header-left">
                 <button class="mobile-hamburger" id="mobile-hamburger-btn"><i class="fas fa-bars"></i></button>
                <h1>Shivarchanam</h1>
            </div>
            <div class="header-info">
                <div class="admin-welcome">
                    <strong>Hiii, <?php echo htmlspecialchars($admin_name); ?>!</strong>
                    <span id="live-time"></span>
                </div>
                <div class="admin-profile">
                    <a href="profile.php" class="profile-link">
                        <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Admin">
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="admin-main-container">
        <nav class="admin-nav">
             <ul>
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="manage_slider.php"><i class="fas fa-images"></i><span>Slider</span></a></li>
                <li><a href="manage_stories.php"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
                <li><a href="manage_ticker.php"><i class="fas fa-newspaper"></i><span>News Ticker</span></a></li>
                 <li><a href="manage_history.php"><i class="fas fa-landmark"></i><span>Temple History</span></a></li>
                <li><a href="all_leads.php"><i class="fas fa-headset"></i><span>All Leads</span></a></li>
                <li><a href="manage_donations.php"><i class="fas fa-donate"></i><span>Donations</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
                <li><a href="change_password.php"><i class="fas fa-key"></i><span>Password</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </nav>
        <main class="admin-content">
            <div class="summary-grid">
                 <a href="all_leads.php" class="summary-card">
                    <div class="summary-icon" style="background-color: #e0f7fa; color: #007bff;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="summary-text">
                        <strong><?php echo formatNumber($total_leads); ?></strong>
                        <span>Total Leads</span>
                    </div>
                </a>
                 <div class="summary-card">
                    <div class="summary-icon" style="background-color: #e8f5e9; color: #28a745;">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="summary-text">
                        <strong><?php echo formatNumber($site_visits); ?></strong>
                        <span>Total Page Visits</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <div class="mobile-overlay" id="mobile-overlay"></div>
    <div class="mobile-side-menu" id="mobile-side-menu">
        <nav class="admin-nav">
             <ul>
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="manage_slider.php"><i class="fas fa-images"></i><span>Slider</span></a></li>
                <li><a href="manage_stories.php"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
                <li><a href="manage_ticker.php"><i class="fas fa-newspaper"></i><span>News Ticker</span></a></li>
                <li><a href="manage_history.php"><i class="fas fa-landmark"></i><span>Temple History</span></a></li>
                <li><a href="all_leads.php"><i class="fas fa-headset"></i><span>All Leads</span></a></li>
                <li><a href="manage_donations.php"><i class="fas fa-donate"></i><span>Donations</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
                <li><a href="change_password.php"><i class="fas fa-key"></i><span>Password</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </nav>
    </div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timeElement = document.getElementById('live-time');
        function updateTime() {
            if (timeElement) {
                const now = new Date();
                const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                const dateString = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
                timeElement.innerHTML = `${dateString}, ${timeString}`;
            }
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Mobile Menu Logic
        const hamburgerBtn = document.getElementById('mobile-hamburger-btn');
        const mobileMenu = document.getElementById('mobile-side-menu');
        const overlay = document.getElementById('mobile-overlay');

        function toggleMobileMenu() {
            mobileMenu.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        if(hamburgerBtn) {
            hamburgerBtn.addEventListener('click', toggleMobileMenu);
        }
        if(overlay) {
            overlay.addEventListener('click', toggleMobileMenu);
        }
    });
</script>
</body>
</html>

