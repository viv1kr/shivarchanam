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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_service'])) {
    $service_id = $_POST['service_id'];
    
    // First, get the image path to delete the file from the server
    $stmt = $conn->prepare("SELECT image_url FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result && !empty($result['image_url']) && file_exists($result['image_url'])) {
        unlink($result['image_url']);
    }

    // Then, delete the record from the database
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    if ($stmt->execute()) {
        $message = 'Service deleted successfully.';
        $message_type = 'success';
    } else {
        $message = 'Error deleting service.';
        $message_type = 'error';
    }
}

// Handle Addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_service'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $donation_link = $_POST['donation_link'];
    $image_url = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $image_filename = "service_" . time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO services (name, description, image_url, donation_link) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $description, $image_url, $donation_link);
    if ($stmt->execute()) {
        $message = 'New service added successfully.';
        $message_type = 'success';
    } else {
        $message = 'Database error: Could not add service.';
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

// Fetch all services
$services = $conn->query("SELECT * FROM services ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Shivarchanam</title>
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
        .mobile-hamburger { display: none; }
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
                <li><a href="manage_stories.php"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
                <li><a href="manage_ticker.php"><i class="fas fa-newspaper"></i><span>News Ticker</span></a></li>
                <li><a href="manage_history.php"><i class="fas fa-landmark"></i><span>Temple History</span></a></li>
                <li><a href="manage_services.php" class="active"><i class="fas fa-concierge-bell"></i><span>Services</span></a></li>
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
                    <h2>Manage Priest Services</h2>
                    <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
                </div>
                <p>Add or remove the services displayed on the homepage.</p>
            </div>

            <div class="content-card">
                <h2>Add New Service</h2>
                <form action="manage_services.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Service Name</label>
                        <input type="text" id="name" name="name" placeholder="e.g., Puja Ceremony" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Short Description</label>
                        <textarea id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="donation_link">Donation/Booking Link (Optional)</label>
                        <input type="url" id="donation_link" name="donation_link" placeholder="https://example.com/donate">
                    </div>
                    <div class="form-group">
                        <label for="image">Service Photo</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" name="add_service" class="btn-submit">Add Service</button>
                </form>
            </div>

            <div class="content-card">
                <h2>Current Services</h2>
                <div class="item-grid">
                    <?php if ($services && $services->num_rows > 0): ?>
                        <?php while($service = $services->fetch_assoc()): ?>
                            <div class="grid-item">
                                <img src="<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>">
                                <div class="item-info">
                                    <h4><?php echo htmlspecialchars($service['name']); ?></h4>
                                    <p><?php echo htmlspecialchars($service['description']); ?></p>
                                    <form action="manage_services.php" method="post" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                        <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                        <button type="submit" name="delete_service" class="btn-delete">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No services have been added yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <!-- (Your mobile menu HTML and script would go here) -->
</body>
</html>

