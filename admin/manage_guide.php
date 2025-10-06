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

// --- Handle Form Submissions ---
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save_order'])) {
        $order = json_decode($_POST['order'], true);
        if (is_array($order)) {
            $stmt = $conn->prepare("UPDATE visitor_guide SET sort_order = ? WHERE id = ?");
            foreach ($order as $index => $id) {
                $stmt->bind_param("ii", $index, $id);
                $stmt->execute();
            }
            $message = 'Guide order saved successfully.';
            $message_type = 'success';
        }
    } elseif (isset($_POST['delete_item'])) {
        $item_id = $_POST['item_id'];
        $stmt = $conn->prepare("DELETE FROM visitor_guide WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        if ($stmt->execute()) {
            $message = 'Guide item deleted successfully.';
            $message_type = 'success';
        } else {
            $message = 'Error deleting item.';
            $message_type = 'error';
        }
    } elseif (isset($_POST['add_item'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $icon_class = $_POST['icon_class'];
        $stmt = $conn->prepare("INSERT INTO visitor_guide (title, content, icon_class) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $content, $icon_class);
        if ($stmt->execute()) {
             $message = 'New guide item added successfully.';
             $message_type = 'success';
        }
    }
}

// Fetch admin details for header
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT display_name, profile_photo_url FROM admin_users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_details = $stmt->get_result()->fetch_assoc();
$admin_name = $admin_details['display_name'] ?? 'Admin';
$profile_photo = $admin_details['profile_photo_url'] ?? 'assets/images/default-avatar.png';
$active_page = basename($_SERVER['PHP_SELF']);

// Fetch all guide items
$guide_items = $conn->query("SELECT * FROM visitor_guide ORDER BY sort_order ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Visitor Guide - Shivarchanam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-red: #9F0102; --accent-orange: #FF6D01; --light-bg: #FFEFDA;
            --dark-text: #333333; --white: #ffffff; --border-color: #e0e0e0; --body-bg: #f4f5f7;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--body-bg); color: var(--dark-text); min-height: 100vh; }
        .admin-header { background-color: var(--white); padding: 0.75rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); position: fixed; width: 100%; top: 0; z-index: 1000; }
        .header-content { display: flex; justify-content: space-between; align-items: center; max-width: 1600px; margin: 0 auto; }
        .header-left { display: flex; align-items: center; gap: 1rem; }
        .header-left h1 { font-size: 1.5rem; color: var(--primary-red); }
        .admin-profile { display: flex; align-items: center; gap: 1.5rem; }
        .profile-link img { width: 45px; height: 45px; border-radius: 50%; border: 2px solid var(--primary-red); object-fit: cover; }
        .logout-btn { color: var(--primary-red); text-decoration: none; font-weight: bold; }
        
        .admin-main-container { display: grid; grid-template-columns: 250px 1fr; padding-top: 75px; min-height: 100vh; }
        .admin-nav { background-color: var(--white); border-right: 1px solid var(--border-color); padding: 2rem 0; }
        .admin-nav ul { list-style: none; }
        .admin-nav ul li a { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; text-decoration: none; color: var(--dark-text); border-left: 4px solid transparent; transition: all 0.3s; }
        .admin-nav ul li a:hover, .admin-nav ul li a.active { background-color: var(--light-bg); border-left-color: var(--primary-red); color: var(--primary-red); }
        .admin-nav ul li a .fas { width: 20px; text-align: center; }
        .admin-content { padding: 2rem; overflow-y: auto; }
        
        .content-card { background: var(--white); padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 1rem; }
        h2 { margin: 0; color: var(--primary-red); }
        .btn-back { background-color: var(--accent-orange); color: var(--white); padding: 0.6rem 1.2rem; text-decoration: none; border-radius: 25px; font-weight: 500; font-size: 0.9rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.8rem 1rem; border-radius: 8px; border: 1px solid #ccc; font-size: 1rem; }
        
        .icon-select-wrapper { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 1rem; }
        .icon-option { display: block; position: relative; }
        .icon-option input { display: none; }
        .icon-content { display: flex; flex-direction: column; align-items: center; padding: 1rem; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.2s ease; }
        .icon-content .fas { font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--dark-text); }
        .icon-content span { font-size: 0.8rem; color: #666; }
        .icon-option input:checked + .icon-content { border-color: var(--accent-orange); background-color: var(--light-bg); transform: scale(1.05); }
        .icon-option input:checked + .icon-content .fas { color: var(--accent-orange); }

        .btn-submit { background-color: var(--primary-red); color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; font-size: 1.1rem; font-weight: bold; }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; }
        .message.success { background-color: #d4edda; color: #155724; }
        .draggable-list { list-style: none; padding: 0; }
        .draggable-item { display: flex; align-items: center; gap: 1.5rem; padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 1rem; background: #fdfdfd; cursor: move; }
        .draggable-item .handle { color: #aaa; font-size: 1.2rem; }
        .draggable-item .item-icon { font-size: 1.2rem; color: var(--accent-orange); }
        .draggable-item .item-details { flex-grow: 1; }
        .btn-delete-small { background: transparent; border: 1px solid #dc3545; color: #dc3545; border-radius: 5px; cursor: pointer; padding: 0.4rem 0.8rem; }
        .sortable-ghost { background: var(--light-bg); opacity: 0.5; }
        
        /* MOBILE NAVIGATION */
        .mobile-hamburger { display: none; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--dark-text); }
        .mobile-side-menu { position: fixed; top: 0; right: -100%; width: 280px; height: 100%; background: var(--white); box-shadow: -2px 0 10px rgba(0,0,0,0.2); transition: right 0.4s ease-in-out; z-index: 2001; }
        .mobile-side-menu.active { right: 0; }
        .mobile-side-menu .admin-nav { padding-top: 4rem; border: none; box-shadow: none; display: block !important; }
        .mobile-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); opacity: 0; visibility: hidden; transition: 0.3s; z-index: 2000; }
        .mobile-overlay.active { opacity: 1; visibility: visible; }
        
        @media (max-width: 768px) {
            .admin-main-container { display: block; }
            .admin-header { padding: 0.75rem 1rem; }
            .admin-main-container > .admin-nav { display: none; } 
            .mobile-hamburger { display: block; }
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
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="manage_guide.php" class="active"><i class="fas fa-map-signs"></i><span>Visitor Guide</span></a></li>
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
                <!-- Other menu items -->
            </ul>
        </nav>
        <main class="admin-content">
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="content-card">
                <div class="page-header">
                    <h2>Manage Visitor Guide</h2>
                </div>
                <p>Add, remove, and reorder the information displayed in the "Visitor Guide" section on the homepage.</p>
            </div>

            <div class="content-card">
                <h2>Add New Guide Item</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content / Details</label>
                        <textarea id="content" name="content" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Select an Icon</label>
                        <div class="icon-select-wrapper">
                            <?php 
                            $icons = ['fas fa-clock', 'fas fa-fire', 'fas fa-utensils', 'fas fa-praying-hands', 'fas fa-info-circle', 'fas fa-book-open', 'fas fa-om', 'fas fa-gopuram'];
                            foreach ($icons as $icon): ?>
                            <label class="icon-option">
                                <input type="radio" name="icon_class" value="<?php echo $icon; ?>" required>
                                <div class="icon-content">
                                    <i class="<?php echo $icon; ?>"></i>
                                    <span><?php echo ucwords(str_replace(['fas fa-', '-'], ['', ' '], $icon)); ?></span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="submit" name="add_item" class="btn-submit">Add Item</button>
                </form>
            </div>

            <div class="content-card">
                <h2>Current Guide Items</h2>
                <p>You can drag and drop these items to reorder them.</p>
                <form method="post" id="reorder-form">
                    <input type="hidden" name="order" id="order-input">
                    <button type="submit" name="save_order" class="btn-submit">Save Order</button>
                </form>
                <ul class="draggable-list" id="guide-list" style="margin-top: 1rem;">
                    <?php if ($guide_items && $guide_items->num_rows > 0):
                        while($item = $guide_items->fetch_assoc()): ?>
                        <li class="draggable-item" data-id="<?php echo $item['id']; ?>">
                            <i class="fas fa-grip-vertical handle"></i>
                            <i class="<?php echo htmlspecialchars($item['icon_class']); ?> item-icon"></i>
                            <div class="item-details">
                                <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                            </div>
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="delete_item" class="btn-delete-small">Delete</button>
                            </form>
                        </li>
                    <?php endwhile; endif; ?>
                </ul>
            </div>
        </main>
    </div>
    
    <div class="mobile-overlay" id="mobile-overlay"></div>
    <div class="mobile-side-menu" id="mobile-side-menu">
        <nav class="admin-nav">
             <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="manage_guide.php" class="active"><i class="fas fa-map-signs"></i><span>Visitor Guide</span></a></li>
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
                <!-- Full mobile menu items -->
            </ul>
        </nav>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Logic
            const hamburgerBtn = document.getElementById('mobile-hamburger-btn');
            const mobileMenu = document.getElementById('mobile-side-menu');
            const overlay = document.getElementById('mobile-overlay');
            function toggleMobileMenu() {
                mobileMenu.classList.toggle('active');
                overlay.classList.toggle('active');
            }
            if(hamburgerBtn) hamburgerBtn.addEventListener('click', toggleMobileMenu);
            if(overlay) overlay.addEventListener('click', toggleMobileMenu);

            // Draggable List Logic
            const list = document.getElementById('guide-list');
            if (list) {
                new Sortable(list, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    handle: '.handle'
                });
            }
            
            const reorderForm = document.getElementById('reorder-form');
            if(reorderForm) {
                reorderForm.addEventListener('submit', function(e) {
                    const orderInput = document.getElementById('order-input');
                    const items = list.querySelectorAll('.draggable-item');
                    const order = Array.from(items).map(item => item.dataset.id);
                    orderInput.value = JSON.stringify(order);
                });
            }
        });
    </script>
</body>
</html>

