<?php 
// --- CSV DOWNLOAD LOGIC (MUST RUN BEFORE ANY HTML) ---
if (isset($_GET['download']) && $_GET['download'] == 'csv') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['admin_logged_in'])) { exit('Access Denied.'); }
    require_once '../config/db.php';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="appointments_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Mobile', 'Date of Birth', 'Service', 'Preferred Time', 'Message', 'Status', 'Date Submitted']);
    
    $result = $conn->query("SELECT * FROM appointments ORDER BY created_at DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    }
    fclose($output);
    exit;
}

// --- REGULAR PAGE LOGIC ---
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
require_once '../config/db.php';

// --- Handle Form Submissions ---
$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle Deletion
    if (isset($_POST['delete_appointment'])) {
        $appointment_id = $_POST['appointment_id'];
        $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->bind_param("i", $appointment_id);
        if ($stmt->execute()) {
            $message = 'Appointment deleted successfully.';
            $message_type = 'success';
        }
    }
    // Handle Status Update
    if (isset($_POST['update_status'])) {
        $appointment_id = $_POST['appointment_id'];
        $new_status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $appointment_id);
        if ($stmt->execute()) {
            $message = 'Status updated successfully.';
            $message_type = 'success';
        }
    }
}

// --- Fetch Data & Pagination ---
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;
$total_records_query = $conn->query("SELECT COUNT(*) FROM appointments");
$total_records = $total_records_query->fetch_row()[0];
$total_pages = ceil($total_records / $records_per_page);
$appointments = $conn->query("SELECT * FROM appointments ORDER BY created_at DESC LIMIT $records_per_page OFFSET $offset");

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
    <title>Manage Appointments - Shivarchanam</title>
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
        
        .content-card { background: var(--white); padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 1rem; }
        h2 { margin: 0; border: none; padding: 0; color: var(--primary-red); }
        .btn-download { background-color: #28a745; color: var(--white); padding: 0.6rem 1.2rem; text-decoration: none; border-radius: 25px; font-weight: 500; font-size: 0.9rem; }
        .message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; }
        .message.success { background-color: #d4edda; color: #155724; }
        
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); white-space: nowrap; }
        thead th { background-color: #f9fafa; font-weight: 600; color: var(--dark-text); }
        tbody tr:hover { background-color: #fcf6f0; }
        .status-select { padding: 0.5rem; border-radius: 5px; border: 1px solid #ccc; font-weight: bold; }
        .status-New-Client { color: #007bff; border-color: #007bff; }
        .status-Talked { color: #28a745; border-color: #28a745; }
        .btn-delete-small { background: transparent; border: 1px solid #dc3545; color: #dc3545; border-radius: 5px; cursor: pointer; padding: 0.4rem 0.8rem; }
        .pagination { display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem; }
        .pagination a { text-decoration: none; padding: 0.5rem 1rem; border: 1px solid var(--border-color); border-radius: 5px; color: var(--dark-text); }
        .pagination a.active { background-color: var(--primary-red); color: var(--white); border-color: var(--primary-red); }
        
        /* MOBILE NAVIGATION */
        .mobile-hamburger { display: none; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--dark-text); }
        .mobile-side-menu { position: fixed; top: 0; right: -100%; width: 280px; height: 100%; background: var(--white); box-shadow: -2px 0 10px rgba(0,0,0,0.2); transition: right 0.4s ease-in-out; z-index: 2001; padding-top: 4rem; }
        .mobile-side-menu.active { right: 0; }
        .mobile-side-menu .admin-nav { padding: 0; border: none; box-shadow: none; display: block !important; }
        .mobile-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); opacity: 0; visibility: hidden; transition: 0.3s; z-index: 2000; }
        .mobile-overlay.active { opacity: 1; visibility: visible; }
        
        @media (max-width: 900px) { /* Widen breakpoint for table */
            thead { display: none; }
            tr { display: block; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 1rem; padding: 1rem; }
            td { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding: 0.75rem 0; text-align: right; white-space: normal; }
            td:last-child { border-bottom: none; }
            td::before { content: attr(data-label); font-weight: 600; margin-right: 1rem; text-align: left; color: var(--dark-text); }
        }
        @media (max-width: 768px) {
            .admin-main-container { display: block; }
            .admin-header { padding: 0.75rem 1rem; }
            .admin-main-container > .admin-nav { display: none; } 
            .mobile-hamburger { display: block; }
            .header-left h1 { font-size: 1.2rem; }
            .admin-content { padding: 1rem; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
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
                <li><a href="manage_appointments.php" class="active"><i class="fas fa-calendar-check"></i><span>Appointments</span></a></li>
                <li><a href="manage_slider.php"><i class="fas fa-images"></i><span>Slider</span></a></li>
                <li><a href="manage_stories.php"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
                <li><a href="manage_ticker.php"><i class="fas fa-newspaper"></i><span>News Ticker</span></a></li>
                <li><a href="manage_history.php"><i class="fas fa-landmark"></i><span>Temple History</span></a></li>
                <li><a href="manage_services.php"><i class="fas fa-concierge-bell"></i><span>Services</span></a></li>
                <li><a href="all_leads.php"><i class="fas fa-headset"></i><span>All Leads</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
                <li><a href="change_password.php"><i class="fas fa-key"></i><span>Password</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </nav>
        <main class="admin-content">
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="content-card">
                <div class="page-header">
                    <h2>Appointment Requests</h2>
                    <a href="?download=csv" class="btn-download"><i class="fas fa-download"></i> Download CSV</a>
                </div>
                <p>This table contains all appointment requests submitted through the website.</p>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr><th>Name</th><th>Mobile</th><th>Service</th><th>Date</th><th>Status</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($appointments && $appointments->num_rows > 0): ?>
                                <?php while($appt = $appointments->fetch_assoc()): ?>
                                    <tr>
                                        <td data-label="Name"><?php echo htmlspecialchars($appt['name']); ?></td>
                                        <td data-label="Mobile"><?php echo htmlspecialchars($appt['mobile']); ?></td>
                                        <td data-label="Service"><?php echo htmlspecialchars($appt['service_name']); ?></td>
                                        <td data-label="Date"><?php echo date('M j, Y, g:i a', strtotime($appt['created_at'])); ?></td>
                                        <td data-label="Status">
                                            <form method="post" class="status-form">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                                <select name="status" class="status-select status-<?php echo str_replace(' ', '-', $appt['status']); ?>" onchange="this.form.submit()">
                                                    <option value="New Client" <?php echo ($appt['status'] == 'New Client') ? 'selected' : ''; ?>>New Client</option>
                                                    <option value="Talked" <?php echo ($appt['status'] == 'Talked') ? 'selected' : ''; ?>>Talked</option>
                                                </select>
                                                <input type="hidden" name="update_status">
                                            </form>
                                        </td>
                                        <td data-label="Action">
                                            <form method="post" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                                <button type="submit" name="delete_appointment" class="btn-delete-small">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6">No appointment requests yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php 
                if ($total_pages > 1) {
                    echo '<div class="pagination">';
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<a href='?page={$i}' class='" . ($i == $page ? 'active' : '') . "'>{$i}</a>";
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </main>
    </div>
    
    <div class="mobile-overlay" id="mobile-overlay"></div>
    <div class="mobile-side-menu" id="mobile-side-menu">
        <nav class="admin-nav">
             <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="manage_appointments.php" class="active"><i class="fas fa-calendar-check"></i><span>Appointments</span></a></li>
                <li><a href="manage_slider.php"><i class="fas fa-images"></i><span>Slider</span></a></li>
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

