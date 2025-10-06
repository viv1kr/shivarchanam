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

// Handle saving/updating slides for a story group
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_slides'])) {
    $story_id = $_POST['story_id'];
    
    $delete_stmt = $conn->prepare("DELETE FROM story_slides WHERE story_id = ?");
    $delete_stmt->bind_param("i", $story_id);
    $delete_stmt->execute();

    if (isset($_POST['slide_title'])) {
        $count = count($_POST['slide_title']);
        $stmt = $conn->prepare("INSERT INTO story_slides (story_id, title, description, button_text, button_link, image_url, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");

        for ($i = 0; $i < $count; $i++) {
            $title = $_POST['slide_title'][$i];
            $description = $_POST['slide_description'][$i];
            $button_text = !empty(trim($_POST['button_text'][$i])) ? trim($_POST['button_text'][$i]) : null;
            $button_link = !empty(trim($_POST['button_link'][$i])) ? trim($_POST['button_link'][$i]) : null;
            $image_url = $_POST['existing_image_url'][$i];
            
            if (isset($_FILES['slide_image']['name'][$i]) && $_FILES['slide_image']['error'][$i] == 0) {
                $target_dir = "uploads/";
                $image_filename = time() . '_' . basename($_FILES["slide_image"]["name"][$i]);
                $target_file = $target_dir . $image_filename;
                
                if (move_uploaded_file($_FILES["slide_image"]["tmp_name"][$i], $target_file)) {
                    $image_url = $target_file;
                }
            }
            
            if (!empty($title) && !empty($image_url)) {
                 $stmt->bind_param("isssssi", $story_id, $title, $description, $button_text, $button_link, $image_url, $i);
                 $stmt->execute();
            }
        }
    }
    header('Location: manage_stories.php?edit=' . $story_id . '&status=success');
    exit;
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

// Fetch data for display
$stories = $conn->query("SELECT * FROM stories ORDER BY id ASC");
$editing_story = null;
$slides = [];

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM stories WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $editing_story = $stmt->get_result()->fetch_assoc();

    $slide_stmt = $conn->prepare("SELECT * FROM story_slides WHERE story_id = ? ORDER BY sort_order ASC");
    $slide_stmt->bind_param("i", $edit_id);
    $slide_stmt->execute();
    $slides_result = $slide_stmt->get_result();
    while($row = $slides_result->fetch_assoc()){
        $slides[] = $row;
    }
}
if(isset($_GET['status']) && $_GET['status'] == 'success') {
    $message = 'Story slides saved successfully!';
    $message_type = 'success';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stories - Shivarchanam</title>
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
        .admin-profile { display: flex; align-items: center; gap: 1rem; }
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
        h2 { margin: 0; border: none; padding: 0; color: var(--primary-red); }
        .btn-back { background-color: var(--accent-orange); color: var(--white); padding: 0.6rem 1.2rem; text-decoration: none; border-radius: 25px; font-weight: 500; font-size: 0.9rem; }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
        
        .item-list { list-style: none; }
        .item-list li { display: flex; align-items: center; gap: 1rem; padding: 1rem; border-bottom: 1px solid var(--border-color); }
        .item-list li:last-child { border-bottom: none; }
        .item-list img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
        .item-list .btn-edit { margin-left: auto; background-color: var(--primary-red); color: var(--white); padding: 0.5rem 1rem; text-decoration: none; border-radius: 25px; font-size: 0.9rem; }
        
        .slide-group { border: 1px solid var(--border-color); border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; background: #fdfdfd; }
        .slide-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;}
        .btn-delete-small { background: transparent; border: 1px solid #dc3545; color: #dc3545; border-radius: 5px; cursor: pointer; padding: 0.3rem 0.6rem; font-size: 0.8rem; }
        .current-image-preview { max-width: 100px; margin-top: 1rem; border-radius: 4px; border: 1px solid #ddd; }
        .btn-add-new { background: #28a745; color: var(--white); border: none; padding: 0.8rem 1rem; border-radius: 8px; cursor: pointer; }
        .btn-submit { background-color: var(--primary-red); color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; font-size: 1.1rem; font-weight: bold; }
        
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
                <li><a href="manage_slider.php"><i class="fas fa-images"></i><span>Slider</span></a></li>
                <li><a href="manage_stories.php" class="active"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
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
                    <h2>Manage Stories</h2>
                    <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
                </div>
                <p>Select a story group to edit its slides. Each group appears as a single circle on the homepage.</p>
                <ul class="item-list">
                    <?php while ($story = $stories->fetch_assoc()): ?>
                        <li>
                            <img src="<?php echo htmlspecialchars($story['thumbnail_url']); ?>" alt="Thumbnail">
                            <strong>Story Group <?php echo $story['id']; ?></strong>
                            <a href="?edit=<?php echo $story['id']; ?>" class="btn-edit">Edit Slides</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <?php if ($editing_story): ?>
                <div class="content-card">
                    <h2>Editing Slides for Story Group <?php echo $editing_story['id']; ?></h2>
                    <form action="manage_stories.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="story_id" value="<?php echo $editing_story['id']; ?>">
                        <div id="slides-container">
                            <?php if (empty($slides)): ?>
                                <p>No slides yet for this story. Add one below!</p>
                            <?php else: ?>
                                <?php foreach($slides as $index => $slide): ?>
                                <div class="slide-group">
                                    <div class="slide-header">
                                        <h4>Slide <?php echo $index + 1; ?></h4>
                                        <button type="button" class="btn-delete-small" onclick="this.parentElement.parentElement.remove()">Remove</button>
                                    </div>
                                    <!-- Form fields for the slide -->
                                    <div class="form-group"><label>Title:</label><input type="text" name="slide_title[]" value="<?php echo htmlspecialchars($slide['title']); ?>" required></div>
                                    <div class="form-group"><label>Description:</label><textarea name="slide_description[]" required><?php echo htmlspecialchars($slide['description']); ?></textarea></div>
                                    <div class="form-group"><label>Button Text (Optional):</label><input type="text" name="button_text[]" value="<?php echo htmlspecialchars($slide['button_text'] ?? ''); ?>"></div>
                                    <div class="form-group"><label>Button Link (Optional):</label><input type="url" name="button_link[]" value="<?php echo htmlspecialchars($slide['button_link'] ?? ''); ?>" placeholder="https://example.com"></div>
                                    <div class="form-group"><label>Image:</label><input type="file" name="slide_image[]" accept="image/*"><input type="hidden" name="existing_image_url[]" value="<?php echo htmlspecialchars($slide['image_url']); ?>"><?php if ($slide['image_url']): ?><img src="<?php echo htmlspecialchars($slide['image_url']); ?>" class="current-image-preview"><?php endif; ?></div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="add-slide" class="btn-add-new">+ Add New Slide</button>
                        <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid var(--border-color);">
                        <button type="submit" name="save_slides" class="btn-submit">Save All Changes</button>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <div class="mobile-overlay" id="mobile-overlay"></div>
    <div class="mobile-side-menu" id="mobile-side-menu">
        <nav class="admin-nav">
             <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="manage_slider.php"><i class="fas fa-images"></i><span>Slider</span></a></li>
                <li><a href="manage_stories.php" class="active"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
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

        // Add New Slide Logic
        const addSlideBtn = document.getElementById('add-slide');
        if(addSlideBtn) {
            addSlideBtn.addEventListener('click', function() {
                const container = document.getElementById('slides-container');
                const newSlide = document.createElement('div');
                newSlide.className = 'slide-group';
                newSlide.innerHTML = `
                    <div class="slide-header"><h4>New Slide</h4><button type="button" class="btn-delete-small" onclick="this.parentElement.parentElement.remove()">Remove</button></div>
                    <div class="form-group"><label>Title:</label><input type="text" name="slide_title[]" required></div>
                    <div class="form-group"><label>Description:</label><textarea name="slide_description[]" required></textarea></div>
                    <div class="form-group"><label>Button Text (Optional):</label><input type="text" name="button_text[]"></div>
                    <div class="form-group"><label>Button Link (Optional):</label><input type="url" name="button_link[]" placeholder="https://example.com"></div>
                    <div class="form-group"><label>Image:</label><input type="file" name="slide_image[]" accept="image/*" required></div>
                    <input type="hidden" name="existing_image_url[]" value="">`;
                container.appendChild(newSlide);
            });
        }
    });
</script>
</body>
</html>

