<?php
// Start a session only if one isn't already active to prevent errors.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
// Correct path to the database configuration file
require_once '../config/db.php';

// Fetch admin details for the header
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT display_name, profile_photo_url FROM admin_users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_details = $stmt->get_result()->fetch_assoc();
$admin_name = $admin_details['display_name'] ?? 'Admin';
$profile_photo = $admin_details['profile_photo_url'] ?? 'assets/images/default-avatar.png';

// Determine the currently active page for highlighting the navigation link
$active_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Shivarchanam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin_style.css">
</head>
<body>
    <header class="admin-header">
        <div class="header-content">
            <div class="header-left">
                <button class="mobile-hamburger" id="mobile-hamburger-btn"><i class="fas fa-bars"></i></button>
                <h1>Shivarchanam</h1>
            </div>
            <div class="admin-profile">
                <a href="profile.php" class="profile-link">
                    <img src="../<?php echo htmlspecialchars($profile_photo); ?>" alt="Admin">
                </a>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </header>

    <div class="admin-main-container">
        <nav class="admin-nav">
             <ul>
                <li><a href="dashboard.php" class="<?php echo ($active_page == 'dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <hr style="margin: 1rem 1.5rem; border: none; border-top: 1px solid #f0f0f0;">
                <li><a href="manage_appointments.php" class="<?php echo ($active_page == 'manage_appointments.php') ? 'active' : ''; ?>"><i class="fas fa-calendar-check"></i><span>Appointments</span></a></li>
                <li><a href="manage_memberships.php" class="<?php echo ($active_page == 'manage_memberships.php') ? 'active' : ''; ?>"><i class="fas fa-users"></i><span>Memberships</span></a></li>
                <li><a href="all_leads.php" class="<?php echo ($active_page == 'all_leads.php') ? 'active' : ''; ?>"><i class="fas fa-headset"></i><span>All Leads</span></a></li>
                <li><a href="manage_contacts.php" class="<?php echo ($active_page == 'manage_contacts.php') ? 'active' : ''; ?>"><i class="fas fa-envelope-open-text"></i><span>Contact Messages</span></a></li>
                <hr style="margin: 1rem 1.5rem; border: none; border-top: 1px solid #f0f0f0;">
                <li><a href="manage_about.php" class="<?php echo ($active_page == 'manage_about.php') ? 'active' : ''; ?>"><i class="fas fa-info-circle"></i><span>About Page</span></a></li>
                <li><a href="manage_slider.php" class="<?php echo ($active_page == 'manage_slider.php') ? 'active' : ''; ?>"><i class="fas fa-images"></i><span>Slider</span></a></li>
                <li><a href="manage_stories.php" class="<?php echo ($active_page == 'manage_stories.php') ? 'active' : ''; ?>"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
                <li><a href="manage_ticker.php" class="<?php echo ($active_page == 'manage_ticker.php') ? 'active' : ''; ?>"><i class="fas fa-newspaper"></i><span>News Ticker</span></a></li>
                <li><a href="manage_live.php" class="<?php echo ($active_page == 'manage_live.php') ? 'active' : ''; ?>"><i class="fas fa-video"></i><span>Live Stream</span></a></li>
                <li><a href="manage_events.php" class="<?php echo ($active_page == 'manage_events.php') ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i><span>Events</span></a></li>
                <li><a href="manage_gallery.php" class="<?php echo ($active_page == 'manage_gallery.php') ? 'active' : ''; ?>"><i class="fas fa-photo-video"></i><span>Gallery</span></a></li>
                <li><a href="manage_history.php" class="<?php echo ($active_page == 'manage_history.php') ? 'active' : ''; ?>"><i class="fas fa-landmark"></i><span>Temple History</span></a></li>
                <li><a href="manage_services.php" class="<?php echo ($active_page == 'manage_services.php') ? 'active' : ''; ?>"><i class="fas fa-concierge-bell"></i><span>Services</span></a></li>
                 <li><a href="manage_guide.php" class="<?php echo ($active_page == 'manage_guide.php') ? 'active' : ''; ?>"><i class="fas fa-map-signs"></i><span>Visitor Guide</span></a></li>
                <li><a href="manage_donations.php" class="<?php echo ($active_page == 'manage_donations.php') ? 'active' : ''; ?>"><i class="fas fa-donate"></i><span>Donations</span></a></li>
                 <hr style="margin: 1rem 1.5rem; border: none; border-top: 1px solid #f0f0f0;">
                <li><a href="profile.php" class="<?php echo ($active_page == 'profile.php') ? 'active' : ''; ?>"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
                <li><a href="change_password.php" class="<?php echo ($active_page == 'change_password.php') ? 'active' : ''; ?>"><i class="fas fa-key"></i><span>Password</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </nav>
        <main class="admin-content">

