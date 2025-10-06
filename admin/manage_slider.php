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
$message_type = ''; // 'success' or 'error'

// Handle Deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_slide'])) {
    $slide_id = $_POST['slide_id'];
    
    // First, get the image path to delete the file from the server
    $stmt = $conn->prepare("SELECT image_url FROM slider_images WHERE id = ?");
    $stmt->bind_param("i", $slide_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result && !empty($result['image_url']) && file_exists($result['image_url'])) {
        unlink($result['image_url']);
    }
    
    // Then, delete the record from the database
    $stmt = $conn->prepare("DELETE FROM slider_images WHERE id = ?");
    $stmt->bind_param("i", $slide_id);
    if ($stmt->execute()) {
        $message = 'Slider image deleted successfully.';
        $message_type = 'success';
    } else {
        $message = 'Error deleting slider image.';
        $message_type = 'error';
    }
}

// Handle Addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_slide'])) {
    $title = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $image_url = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $image_filename = "slider_" . time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
            $stmt = $conn->prepare("INSERT INTO slider_images (title, subtitle, image_url) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $title, $subtitle, $image_url);
            if ($stmt->execute()) {
                $message = 'New slider image added successfully.';
                $message_type = 'success';
            } else {
                $message = 'Database error: Could not add slider image.';
                $message_type = 'error';
            }
        } else {
            $message = 'Error uploading file.';
            $message_type = 'error';
        }
    } else {
        $message = 'Please select an image to upload.';
        $message_type = 'error';
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

// Fetch all slider images
$slider_images = $conn->query("SELECT * FROM slider_images ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hero Slider - Shivarchanam</title>
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
        
        .admin-main-container { display: grid; grid-template-columns: 250px 1fr; padding-top: 75px; min-height: 100vh; }
        .admin-nav { background-color: var(--white); border-right: 1px solid var(--border-color); padding: 2rem 0; }
        .admin-nav ul { list-style: none; }
        .admin-nav ul li a { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; text-decoration: none; color: var(--dark-text); border-left: 4px solid transparent; transition: all 0.3s; }
        .admin-nav ul li a:hover, .admin-nav ul li a.active { background-color: var(--light-bg); border-left-color: var(--primary-red); color: var(--primary-red); }
        .admin-nav ul li a .fas { width: 20px; text-align: center; }
        .admin-content { padding: 2rem; overflow-y: auto; }
        
        .content-card { background: var(--white); padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 1rem; }
        .content-card h2 { margin: 0; border: none; padding: 0; color: var(--primary-red); }
        .btn-back { background-color: var(--accent-orange); color: var(--white); padding: 0.6rem 1.2rem; text-decoration: none; border-radius: 25px; font-weight: 500; font-size: 0.9rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
        .form-group input, .form-group textarea { width: 100%; padding: 0.8rem 1rem; border-radius: 8px; border: 1px solid #ccc; font-size: 1rem; }
        .btn-submit { background-color: var(--primary-red); color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; font-size: 1.1rem; font-weight: bold; }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }

        .item-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; }
        .grid-item { background: #fdfdfd; border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .grid-item img { width: 100%; height: 160px; object-fit: cover; }
        .item-info { padding: 1rem; flex-grow: 1; display: flex; flex-direction: column; }
        .item-info h4 { margin-bottom: 0.5rem; }
        .item-info p { flex-grow: 1; margin-bottom: 1rem; color: #666; font-size: 0.9rem; }
        .btn-delete { background: #dc3545; color: var(--white); border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; width: 100%; }
        
        /* MOBILE NAVIGATION */
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
                    <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Admin">
                </a>
            </div>
        </div>
    </header>

    <div class="admin-main-container">
        <nav class="admin-nav">
             <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="manage_slider.php" class="active"><i class="fas fa-images"></i><span>Slider</span></a></li>
                <li><a href="manage_stories.php"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
                <li><a href="manage_ticker.php"><i class="fas fa-newspaper"></i><span>News Ticker</span></a></li>
                <li><a href="manage_history.php"><i class="fas fa-landmark"></i><span>Temple History</span></a></li>
                <li><a href="manage_services.php"><i class="fas fa-concierge-bell"></i><span>Services</span></a></li>
                <li><a href="all_leads.php"><i class="fas fa-headset"></i><span>All Leads</span></a></li>
                <li><a href="manage_donations.php"><i class="fas fa-donate"></i><span>Donations</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
                <li><a href="change_password.php"><i class="fas fa-key"></i><span>Password</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </nav>
        <main class="admin-content">
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="content-card">
                <div class="page-header">
                    <h2>Manage Hero Slider</h2>
                    <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
                </div>
                <p>Add or remove the main images that appear on the homepage slider.</p>
            </div>

            <div class="content-card">
                <h2>Add New Slider Image</h2>
                <form action="manage_slider.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="subtitle">Subtitle</label>
                        <textarea id="subtitle" name="subtitle" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" name="add_slide" class="btn-submit">Add Slide</button>
                </form>
            </div>

            <div class="content-card">
                <h2>Current Slider Images</h2>
                <div class="item-grid">
                    <?php if($slider_images && $slider_images->num_rows > 0): ?>
                        <?php while($slide = $slider_images->fetch_assoc()): ?>
                            <div class="grid-item">
                                <img src="<?php echo htmlspecialchars($slide['image_url']); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>">
                                <div class="item-info">
                                    <h4><?php echo htmlspecialchars($slide['title']); ?></h4>
                                    <p><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                                    <form action="manage_slider.php" method="post" onsubmit="return confirm('Are you sure you want to delete this slide?');">
                                        <input type="hidden" name="slide_id" value="<?php echo $slide['id']; ?>">
                                        <button type="submit" name="delete_slide" class="btn-delete">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No slider images have been uploaded yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <div class="mobile-overlay" id="mobile-overlay"></div>
    <div class="mobile-side-menu" id="mobile-side-menu">
        <nav class="admin-nav">
             <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="manage_slider.php" class="active"><i class="fas fa-images"></i><span>Slider</span></a></li>
                <li><a href="manage_stories.php"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
                <li><a href="manage_ticker.php"><i class="fas fa-newspaper"></i><span>News Ticker</span></a></li>
                <li><a href="manage_history.php"><i class="fas fa-landmark"></i><span>Temple History</span></a></li>
                <li><a href="manage_services.php"><i class="fas fa-concierge-bell"></i><span>Services</span></a></li>
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
    });
</script>
</body>
</html>

