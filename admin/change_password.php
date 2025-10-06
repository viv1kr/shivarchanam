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

// --- Password Change Logic ---
$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_id = $_SESSION['admin_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Check if new passwords match
    if ($new_password !== $confirm_password) {
        $message = 'New passwords do not match.';
        $message_type = 'error';
    } else {
        // 2. Get current password hash from DB
        $stmt = $conn->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        // 3. Verify current password
        if ($result && password_verify($current_password, $result['password_hash'])) {
            // 4. Hash new password and update DB
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_password_hash, $admin_id);
            if ($update_stmt->execute()) {
                $message = 'Password updated successfully!';
                $message_type = 'success';
            } else {
                $message = 'Error updating password. Please try again.';
                $message_type = 'error';
            }
        } else {
            $message = 'Incorrect current password.';
            $message_type = 'error';
        }
    }
}


// --- Fetch data for header ---
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
    <title>Change Password - Shivarchanam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-red: #9F0102; --accent-orange: #FF6D01; --light-bg: #FFEFDA;
            --dark-text: #333333; --black: #000000; --white: #ffffff;
            --border-color: #e0e0e0; --body-bg: #f4f5f7;
            --success-green: #28a745; --danger-red: #dc3545;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--body-bg); color: var(--dark-text); min-height: 100vh; }
        .admin-header { background-color: var(--white); padding: 0.75rem 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); position: fixed; width: 100%; top: 0; z-index: 1000; }
        .header-content { display: flex; justify-content: space-between; align-items: center; max-width: 1600px; margin: 0 auto; }
        .header-left h1 { font-size: 1.5rem; color: var(--primary-red); }
        .admin-profile { display: flex; align-items: center; gap: 1.5rem; }
        .profile-link img { width: 45px; height: 45px; border-radius: 50%; border: 2px solid var(--primary-red); object-fit: cover; }
        .logout-btn { color: var(--primary-red); text-decoration: none; background-color: transparent; border: 1px solid var(--primary-red); padding: 0.5rem 1rem; border-radius: 25px; font-weight: bold; }
        .admin-main { display: flex; margin-top: 75px; flex-grow: 1; }
        .admin-nav { background-color: var(--white); width: 250px; flex-shrink: 0; padding-top: 2rem; border-right: 1px solid var(--border-color); }
        .admin-nav ul { list-style: none; }
        .admin-nav ul li a { display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; text-decoration: none; color: var(--dark-text); border-left: 4px solid transparent; transition: all 0.3s; }
        .admin-nav ul li a:hover, .admin-nav ul li a.active { background-color: var(--light-bg); border-left-color: var(--primary-red); color: var(--primary-red); }
        .admin-nav ul li a .fas { width: 20px; text-align: center; }
        .admin-content { flex-grow: 1; padding: 2rem; }
        .content-card { background: var(--white); padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto; }
        .content-card h2 { margin-bottom: 2rem; color: var(--primary-red); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
        .form-group input { width: 100%; padding: 0.8rem 1rem; border-radius: 8px; border: 1px solid #ccc; font-size: 1rem; }
        .btn-submit { background-color: var(--primary-red); color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; font-size: 1.1rem; font-weight: bold; transition: background-color 0.3s; }
        .btn-submit:hover { background-color: #7c0102; }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        @media (max-width: 768px) {
            .admin-header { padding: 0.75rem 1rem; }
            .admin-main { flex-direction: column; margin-top: 70px; }
            .admin-nav { order: 2; width: 100%; height: auto; padding-top: 0; display: flex; overflow-x: auto; box-shadow: 0 -2px 5px rgba(0,0,0,0.05); background: var(--white); position: fixed; bottom: 0; z-index: 1000; }
            .admin-nav ul { display: flex; flex-direction: row; width: 100%; justify-content: space-around; }
            .admin-nav ul li a { border-left: none; border-top: 4px solid transparent; padding: 0.8rem 0.5rem; flex-direction: column; gap: 0.2rem; font-size: 0.7rem; }
            .admin-nav ul li a:hover, .admin-nav ul li a.active { border-top-color: var(--primary-red); }
            .admin-content { order: 1; padding: 1rem; padding-bottom: 80px; }
            .header-left h1 { font-size: 1.2rem; }
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

    <main class="admin-main">
        <nav class="admin-nav">
            <ul>
                <li><a href="dashboard.php" class="<?php echo ($active_page == 'dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="manage_slider.php" class="<?php echo ($active_page == 'manage_slider.php') ? 'active' : ''; ?>"><i class="fas fa-images"></i><span>Slider</span></a></li>
                <li><a href="manage_stories.php" class="<?php echo ($active_page == 'manage_stories.php') ? 'active' : ''; ?>"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
                <li><a href="profile.php" class="<?php echo ($active_page == 'profile.php') ? 'active' : ''; ?>"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
                <li><a href="change_password.php" class="<?php echo ($active_page == 'change_password.php') ? 'active' : ''; ?>"><i class="fas fa-key"></i><span>Password</span></a></li>
            </ul>
        </nav>
        <div class="admin-content">
            <div class="content-card">
                <h2>Change Your Password</h2>
                <?php if ($message): ?>
                    <div class="message <?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form action="change_password.php" method="post">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn-submit">Update Password</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>

