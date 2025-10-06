<?php 
// --- CSV DOWNLOAD LOGIC (MUST RUN BEFORE ANY HTML) ---
if (isset($_GET['download']) && $_GET['download'] == 'csv') {
    // Start session and check for login status for security
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['admin_logged_in'])) {
        exit('Access Denied.');
    }
    require_once '../config/db.php';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="chatbot_leads_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Mobile', 'Address', 'Date Collected']);
    
    $result = $conn->query("SELECT id, name, mobile, address, created_at FROM chatbot_leads ORDER BY created_at DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    }
    fclose($output);
    exit;
}


// --- REGULAR PAGE LOGIC ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
require_once '../config/db.php';

// --- Handle Deletion Request ---
$message = '';
$message_type = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_lead'])) {
    $lead_id = $_POST['lead_id'];
    $stmt = $conn->prepare("DELETE FROM chatbot_leads WHERE id = ?");
    $stmt->bind_param("i", $lead_id);
    if ($stmt->execute()) {
        $message = 'Chatbot lead deleted successfully.';
        $message_type = 'success';
    } else {
        $message = 'Error deleting lead.';
        $message_type = 'error';
    }
}

// --- Pagination Logic ---
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;
$total_records_query = $conn->query("SELECT COUNT(*) FROM chatbot_leads");
$total_records = $total_records_query->fetch_row()[0];
$total_pages = ceil($total_records / $records_per_page);
$leads = $conn->query("SELECT * FROM chatbot_leads ORDER BY created_at DESC LIMIT $records_per_page OFFSET $offset");

// Fetch admin details for header
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
    <title>Chatbot Leads - Shivarchanam</title>
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
        .content-card { background: var(--white); padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 1rem; }
        .content-card h2 { margin: 0; border: none; padding: 0; color: var(--primary-red); }
        .btn-download { background-color: #28a745; color: var(--white); padding: 0.6rem 1.2rem; text-decoration: none; border-radius: 25px; font-weight: 500; font-size: 0.9rem; }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
        .table-container { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); white-space: nowrap; }
        thead th { background-color: #f9fafa; font-weight: 600; color: var(--dark-text); }
        tbody tr:hover { background-color: #fcf6f0; }
        td { color: #555; }
        .btn-delete-small { background: transparent; border: 1px solid #dc3545; color: #dc3545; border-radius: 5px; cursor: pointer; padding: 0.3rem 0.6rem; font-size: 0.8rem; }
        .pagination { display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem; }
        .pagination a { text-decoration: none; padding: 0.5rem 1rem; border: 1px solid var(--border-color); border-radius: 5px; color: var(--dark-text); }
        .pagination a.active { background-color: var(--primary-red); color: var(--white); border-color: var(--primary-red); }
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
                <li><a href="manage_ticker.php"><i class="fas fa-newspaper"></i><span>News Ticker</span></a></li>
                <li><a href="chatbot_leads.php" class="active"><i class="fas fa-headset"></i><span>Chatbot Leads</span></a></li>
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
                    <h2>Chatbot Visitor Leads</h2>
                    <a href="?download=csv" class="btn-download"><i class="fas fa-download"></i> Download CSV</a>
                </div>
                <p>This table contains the information collected from visitors via the website chatbot.</p>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Address</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($leads && $leads->num_rows > 0): ?>
                                <?php while($lead = $leads->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $lead['id']; ?></td>
                                        <td><?php echo htmlspecialchars($lead['name']); ?></td>
                                        <td><?php echo htmlspecialchars($lead['mobile']); ?></td>
                                        <td><?php echo htmlspecialchars($lead['address']); ?></td>
                                        <td><?php echo date('M j, Y, g:i a', strtotime($lead['created_at'])); ?></td>
                                        <td>
                                             <form action="chatbot_leads.php" method="post" onsubmit="return confirm('Are you sure you want to delete this lead?');">
                                                <input type="hidden" name="lead_id" value="<?php echo $lead['id']; ?>">
                                                <button type="submit" name="delete_lead" class="btn-delete-small">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">No leads have been collected yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php 
                // Render pagination links
                if ($total_pages > 1) {
                    echo '<div class="pagination">';
                    for ($i = 1; $i <= $total_pages; $i++) {
                        $active_class = ($i == $page) ? 'active' : '';
                        echo "<a href='?page={$i}' class='{$active_class}'>{$i}</a>";
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </main>
    </div>
    
    <nav class="mobile-nav">
         <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="manage_slider.php"><i class="fas fa-images"></i><span>Slider</span></a></li>
            <li><a href="manage_stories.php"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
            <li><a href="manage_ticker.php"><i class="fas fa-newspaper"></i><span>Ticker</span></a></li>
            <li><a href="chatbot_leads.php" class="active"><i class="fas fa-headset"></i><span>Leads</span></a></li>
            <li><a href="profile.php"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
        </ul>
    </nav>
</body>
</html>

