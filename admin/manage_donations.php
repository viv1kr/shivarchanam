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

// Handle Deletion of a donation product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    
    // First, get the image path to delete the actual file from the server
    $stmt = $conn->prepare("SELECT image_url FROM donation_products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result && !empty($result['image_url']) && file_exists($result['image_url'])) {
        unlink($result['image_url']);
    }
    
    // Then, delete the record from the database
    $delete_stmt = $conn->prepare("DELETE FROM donation_products WHERE id = ?");
    $delete_stmt->bind_param("i", $product_id);
    if ($delete_stmt->execute()) {
        $message = 'Donation product deleted successfully.';
        $message_type = 'success';
    } else {
        $message = 'Error deleting donation product.';
        $message_type = 'error';
    }
}

// Handle Addition of a new donation product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $image_url = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $image_filename = "donation_" . time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        } else {
             $message = 'Error uploading file.';
             $message_type = 'error';
        }
    }
    
    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO donation_products (name, description, amount, image_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $description, $amount, $image_url);
        if ($stmt->execute()) {
             $message = "New donation product added successfully.";
             $message_type = "success";
        } else {
            $message = "Database error: Could not add product.";
            $message_type = "error";
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

// Fetch all existing donation products to display on the page
$products = $conn->query("SELECT * FROM donation_products ORDER BY amount ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Donations - Shivarchanam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-red: #9F0102; --accent-orange: #FF6D01; --light-bg: #FFEFDA;
            --dark-text: #333333; --black: #000000; --white: #ffffff;
            --border-color: #e0e0e0; --body-bg: #f4f5f7;
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
        
        .content-card { background: var(--white); padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 1rem; }
        .content-card h2 { margin: 0; border: none; padding: 0; color: var(--primary-red); }
        .btn-back { background-color: var(--accent-orange); color: var(--white); padding: 0.6rem 1.2rem; text-decoration: none; border-radius: 25px; font-weight: 500; font-size: 0.9rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
        .form-group input, .form-group textarea { width: 100%; padding: 0.8rem 1rem; border-radius: 8px; border: 1px solid #ccc; font-size: 1rem; }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .btn-submit { background-color: var(--primary-red); color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; font-size: 1.1rem; font-weight: bold; }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
        
        .item-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; }
        .grid-item { background: #fdfdfd; border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .grid-item img { width: 100%; height: 150px; object-fit: cover; background-color: #eee; }
        .item-info { padding: 1rem; flex-grow: 1; display: flex; flex-direction: column; }
        .item-info h4 { margin-bottom: 0.5rem; }
        .item-info p { flex-grow: 1; margin-bottom: 1rem; color: #666; font-size: 0.9rem; }
        .btn-delete { background: #dc3545; color: var(--white); border: none; padding: 0.5rem 1rem; border-radius: 5px; cursor: pointer; width: 100%; }
        
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
                <li><a href="manage_ticker.php" ><i class="fas fa-newspaper"></i><span>News Ticker</span></a></li>
                <li><a href="manage_history.php"><i class="fas fa-landmark"></i><span>Temple History</span></a></li>
                <li><a href="manage_services.php" ><i class="fas fa-concierge-bell"></i><span>Services</span></a></li>
                <li><a href="all_leads.php"><i class="fas fa-headset"></i><span>All Leads</span></a></li>
                <li><a href="manage_donations.php" class="active" ><i class="fas fa-donate"></i><span>Donations</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
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
                    <h2>Manage Donation Products</h2>
                </div>
                <p>Add or remove the donation options that will be shown to users in the website chatbot.</p>
            </div>

            <div class="content-card">
                <h2>Add New Donation Product</h2>
                <form action="manage_donations.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Donation Name (e.g., Anna Daan)</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Reason for Donation (Short Description)</label>
                        <textarea id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount (INR)</label>
                        <input type="number" step="0.01" id="amount" name="amount" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image (Optional)</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                    <button type="submit" name="add_product" class="btn-submit">Add Product</button>
                </form>
            </div>

            <div class="content-card">
                <h2>Current Donation Products</h2>
                <div class="item-grid">
                    <?php if ($products && $products->num_rows > 0): ?>
                        <?php while($product = $products->fetch_assoc()): ?>
                            <div class="grid-item">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="item-info">
                                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                    <p><strong>Amount:</strong> ₹<?php echo htmlspecialchars($product['amount']); ?></p>
                                    <form action="manage_donations.php" method="post" onsubmit="return confirm('Are you sure you want to delete this donation product?');">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" name="delete_product" class="btn-delete">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No donation products have been added yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <nav class="mobile-nav">
         <ul>
            <li><a href="dashboard.php" class="<?php echo ($active_page == 'dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="manage_slider.php" class="<?php echo ($active_page == 'manage_slider.php') ? 'active' : ''; ?>"><i class="fas fa-images"></i><span>Slider</span></a></li>
            <li><a href="manage_stories.php" class="<?php echo ($active_page == 'manage_stories.php') ? 'active' : ''; ?>"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
            <li><a href="chatbot_leads.php" class="<?php echo ($active_page == 'chatbot_leads.php') ? 'active' : ''; ?>"><i class="fas fa-headset"></i><span>Leads</span></a></li>
            <li><a href="manage_donations.php" class="<?php echo ($active_page == 'manage_donations.php') ? 'active' : ''; ?>"><i class="fas fa-donate"></i><span>Donations</span></a></li>
            <li><a href="profile.php" class="<?php echo ($active_page == 'profile.php') ? 'active' : ''; ?>"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
        </ul>
    </nav>
</body>
</html>

