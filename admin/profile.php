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

// --- Handle Form Submission for Profile Update ---
$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_id = $_SESSION['admin_id'];
    $display_name = $_POST['display_name'];
    // Start with the existing photo URL, in case no new photo is uploaded
    $profile_photo_url = $_POST['existing_photo_url'];

    // Check if a new profile photo was uploaded
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $target_dir = "assets/images/";
        // Create the directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        // Create a unique filename to prevent overwriting files
        $photo_filename = "admin_" . $admin_id . "_" . time() . "_" . basename($_FILES["profile_photo"]["name"]);
        $target_file = $target_dir . $photo_filename;
        
        // Attempt to move the uploaded file
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
            $profile_photo_url = $target_file;
        } else {
            $message = 'Error uploading new profile photo.';
            $message_type = 'error';
        }
    }

    // If there was no upload error, proceed to update the database
    if (empty($message)) {
        $stmt = $conn->prepare("UPDATE admin_users SET display_name = ?, profile_photo_url = ? WHERE id = ?");
        $stmt->bind_param("ssi", $display_name, $profile_photo_url, $admin_id);
        
        if ($stmt->execute()) {
            $message = 'Profile updated successfully.';
            $message_type = 'success';
        } else {
            $message = 'Error updating profile in the database.';
            $message_type = 'error';
        }
    }
}

// Fetch current admin details to display in the form and header
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT display_name, profile_photo_url FROM admin_users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_details = $stmt->get_result()->fetch_assoc();
$admin_name = $admin_details['display_name'] ?? 'Admin';
$profile_photo = $admin_details['profile_photo_url'] ?? 'assets/images/default-avatar.png';
$active_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Shivarchanam</title>
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
        .header-left h1 { font-size: 1.5rem; color: var(--primary-red); }
        .admin-profile { display: flex; align-items: center; gap: 1.5rem; }
        .profile-link img { width: 45px; height: 45px; border-radius: 50%; border: 2px solid var(--primary-red); object-fit: cover; }
        .logout-btn { color: var(--primary-red); text-decoration: none; background-color: transparent; border: 1px solid var(--primary-red); padding: 0.5rem 1rem; border-radius: 25px; font-weight: bold; }
        
        .admin-main-container { display: grid; grid-template-columns: 250px 1fr; padding-top: 75px; min-height: 100vh; }
        .admin-nav { background-color: var(--white); border-right: 1px solid var(--border-color); padding: 2rem 0; }
        .admin-nav ul { list-style: none; }
        .admin-nav ul li a { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; text-decoration: none; color: var(--dark-text); border-left: 4px solid transparent; transition: all 0.3s; }
        .admin-nav ul li a:hover, .admin-nav ul li a.active { background-color: var(--light-bg); border-left-color: var(--primary-red); color: var(--primary-red); }
        .admin-nav ul li a .fas { width: 20px; text-align: center; }
        .admin-content { padding: 2rem; overflow-y: auto; }
        
        .content-card { background: var(--white); padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 1rem; }
        .content-card h2 { margin: 0; border: none; padding: 0; }
        .btn-back { background-color: var(--accent-orange); color: var(--white); padding: 0.6rem 1.2rem; text-decoration: none; border-radius: 25px; font-weight: 500; font-size: 0.9rem; }
        
        .profile-layout { display: flex; gap: 2rem; align-items: flex-start; }
        .profile-picture-section { text-align: center; flex-basis: 200px; flex-shrink: 0; }
        .profile-picture-section img { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid var(--light-bg); margin-bottom: 1rem; }
        .profile-form-section { flex-grow: 1; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
        .form-group input { width: 100%; padding: 0.8rem 1rem; border-radius: 8px; border: 1px solid #ccc; font-size: 1rem; }
        .btn-submit { background-color: var(--primary-red); color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; font-size: 1.1rem; font-weight: bold; }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
        
        .mobile-nav { display: none; }
        @media (max-width: 768px) {
            .admin-main-container { display: block; padding-bottom: 70px; }
            .admin-header { padding: 0.75rem 1rem; }
            .admin-nav { display: none; }
            .mobile-nav { display: block; position: fixed; bottom: 0; left: 0; width: 100%; background: var(--white); box-shadow: 0 -2px 5px rgba(0,0,0,0.1); z-index: 1000; }
            .mobile-nav ul { display: flex; justify-content: space-around; list-style: none; }
            .mobile-nav ul li a { display: flex; flex-direction: column; align-items: center; gap: 0.2rem; padding: 0.8rem 0.5rem; text-decoration: none; color: var(--dark-text); font-size: 0.7rem; }
            .mobile-nav ul li a.active { color: var(--primary-red); }
            .admin-content { padding: 1rem; }
            .header-left h1 { font-size: 1.2rem; }
            .profile-layout { flex-direction: column; align-items: center; }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-content">
            <div class="header-left">
                <h1>Shivarchanam</h1>
            </div>
            <div class="admin-profile">
                <a href="profile.php" class="profile-link">
                    <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="Admin">
                </a>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </header>

    <div class="admin-main-container">
        <nav class="admin-nav">
             <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="manage_slider.php"><i class="fas fa-images"></i><span>Slider</span></a></li>
                <li><a href="manage_stories.php"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
                <li><a href="manage_ticker.php"><i class="fas fa-newspaper"></i><span>News Ticker</span></a></li>
                <li><a href="all_leads.php"><i class="fas fa-headset"></i><span>All Leads</span></a></li>
                <li><a href="profile.php" class="active"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
                <li><a href="change_password.php"><i class="fas fa-key"></i><span>Password</span></a></li>
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
                    <h2>My Profile</h2>
                    <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
                </div>
                
                <form action="profile.php" method="post" enctype="multipart/form-data">
                    <div class="profile-layout">
                        <div class="profile-picture-section">
                            <img src="<?php echo htmlspecialchars($profile_photo && file_exists($profile_photo) ? $profile_photo : 'assets/images/default-avatar.png'); ?>" alt="Current Profile Photo">
                            <label for="profile_photo">Change Photo</label>
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                            <input type="hidden" name="existing_photo_url" value="<?php echo htmlspecialchars($profile_photo); ?>">
                        </div>
                        <div class="profile-form-section">
                            <div class="form-group">
                                <label for="display_name">Display Name</label>
                                <input type="text" id="display_name" name="display_name" value="<?php echo htmlspecialchars($admin_name); ?>" required>
                            </div>
                             <button type="submit" class="btn-submit">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <nav class="mobile-nav">
         <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="manage_slider.php"><i class="fas fa-images"></i><span>Slider</span></a></li>
            <li><a href="manage_stories.php"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
            <li><a href="manage_ticker.php"><i class="fas fa-newspaper"></i><span>Ticker</span></a></li>
            <li><a href="all_leads.php"><i class="fas fa-headset"></i><span>Leads</span></a></li>
            <li><a href="profile.php" class="active"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
        </ul>
    </nav>
</body>
</html>

