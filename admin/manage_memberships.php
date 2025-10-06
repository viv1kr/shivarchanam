<?php 
// --- CSV DOWNLOAD LOGIC (MUST RUN BEFORE ANY HTML) ---
if (isset($_GET['download']) && $_GET['download'] == 'csv') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['admin_logged_in'])) { exit('Access Denied.'); }
    require_once '../config/db.php';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="memberships_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Mobile', 'Address', 'City', 'State', 'Pincode', 'DOB', 'Occupation', 'Reason to Join', 'Membership Code', 'Join Date']);
    
    $result = $conn->query("SELECT * FROM memberships ORDER BY join_date DESC");
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

// --- Handle Deletion ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_member'])) {
    $member_id = $_POST['member_id'];
    $stmt = $conn->prepare("DELETE FROM memberships WHERE id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
}

// --- Fetch Data & Filtering ---
$search_sql = "";
$search_params = [];
$param_types = "";
if (!empty($_GET['search_name'])) {
    $search_sql .= " AND name LIKE ?";
    $param_types .= "s";
    $search_params[] = "%" . $_GET['search_name'] . "%";
}
if (!empty($_GET['search_code'])) {
    $search_sql .= " AND membership_code LIKE ?";
    $param_types .= "s";
    $search_params[] = "%" . $_GET['search_code'] . "%";
}

$members_query = "SELECT * FROM memberships WHERE 1" . $search_sql . " ORDER BY join_date DESC";
$stmt = $conn->prepare($members_query);
if (!empty($search_params)) {
    $stmt->bind_param($param_types, ...$search_params);
}
$stmt->execute();
$members = $stmt->get_result();

// Fetch admin details for header
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT display_name, profile_photo_url FROM admin_users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_details = $stmt->get_result()->fetch_assoc();
$profile_photo = $admin_details['profile_photo_url'] ?? 'assets/images/default-avatar.png';
$active_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Memberships - Shivarchanam</title>
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
        
        .content-card { background: var(--white); padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem; }
        h2 { margin: 0; color: var(--primary-red); }
        .btn-download { background-color: #28a745; color: var(--white); padding: 0.6rem 1.2rem; text-decoration: none; border-radius: 25px; font-weight: 500; font-size: 0.9rem; }
        .filter-form { display: flex; gap: 1rem; margin: 2rem 0; flex-wrap: wrap; }
        .filter-form input { padding: 0.8rem; border: 1px solid #ccc; border-radius: 8px; }
        .btn-filter { background: var(--primary-red); color: var(--white); border: none; padding: 0.8rem 1.5rem; border-radius: 8px; cursor: pointer; }
        
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); white-space: nowrap; }
        thead th { background-color: #f9fafa; }
        tbody tr:hover { background-color: #fcf6f0; }
        .status-badge { padding: 0.3rem 0.6rem; border-radius: 25px; font-size: 0.8rem; font-weight: bold; }
        .status-active { background-color: #d4edda; color: #155724; }
        .status-expired { background-color: #f8d7da; color: #721c24; }
        .action-buttons { display: flex; gap: 0.5rem; align-items: center; }
        .btn-delete-small { background: transparent; border: 1px solid #dc3545; color: #dc3545; border-radius: 5px; cursor: pointer; padding: 0.4rem 0.8rem; }
        .btn-view, .btn-download-card { border: none; padding: 0.4rem 0.8rem; border-radius: 5px; cursor: pointer; color: white; }
        .btn-view { background: #17a2b8; }
        .btn-download-card { background: #007bff; }
        .btn-download-card:disabled { background: #ccc; cursor: not-allowed; }

        /* MODAL STYLES */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(29, 29, 27, 0.8); backdrop-filter: blur(10px); z-index: 2000; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.3s; padding: 1rem; }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        .details-modal { background: var(--white); padding: 2.5rem; border-radius: 15px; text-align: left; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; position: relative; }
        .modal-close-btn { position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #aaa; }
        .details-header { text-align: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); }
        .details-header img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--light-bg); margin-bottom: 1rem; }
        .details-header h3 { font-size: 1.8rem; }
        .details-header p { color: #555; }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .detail-item { padding-bottom: 1rem; border-bottom: 1px solid #f0f0f0; }
        .detail-item strong { display: block; color: #888; font-size: 0.9rem; margin-bottom: 0.25rem; }
        .detail-item span { font-size: 1.1rem; }
        .detail-item.full-width { grid-column: 1 / -1; }
        #membership-card-modal { max-width: 380px; width: 100%; }
        
        /* MOBILE NAVIGATION */
        .mobile-hamburger { display: none; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--dark-text); }
        .mobile-side-menu { position: fixed; top: 0; right: -100%; width: 280px; height: 100%; background: var(--white); box-shadow: -2px 0 10px rgba(0,0,0,0.2); transition: right 0.4s ease-in-out; z-index: 2001; padding-top: 4rem; }
        .mobile-side-menu.active { right: 0; }
        .mobile-side-menu .admin-nav { padding: 0; border: none; box-shadow: none; display: block !important; }
        .mobile-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); opacity: 0; visibility: hidden; transition: 0.3s; z-index: 2000; }
        .mobile-overlay.active { opacity: 1; visibility: visible; }
        
        @media (max-width: 900px) {
            .admin-main-container { display: block; }
            .admin-header { padding: 0.75rem 1rem; }
            .admin-main-container > .admin-nav { display: none; } 
            .mobile-hamburger { display: block; }
            .header-left h1 { font-size: 1.2rem; }
            .admin-content { padding: 1rem; }
            thead { display: none; }
            tr { display: block; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 1rem; padding: 1rem; }
            td { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding: 0.75rem 0; }
            td:last-child { border-bottom: none; }
            td::before { content: attr(data-label); font-weight: 600; margin-right: 1rem; }
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
                <li><a href="manage_memberships.php" class="active"><i class="fas fa-users"></i><span>Memberships</span></a></li>
                <li><a href="manage_appointments.php"><i class="fas fa-calendar-check"></i><span>Appointments</span></a></li>
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
            <div class="content-card">
                <div class="page-header">
                    <h2>Temple Memberships</h2>
                    <a href="?download=csv" class="btn-download"><i class="fas fa-download"></i> Download All CSV</a>
                </div>
                <p>This table contains all members who have registered through the website.</p>
                
                <form class="filter-form" method="get">
                    <input type="text" name="search_name" placeholder="Search by Name..." value="<?php echo htmlspecialchars($_GET['search_name'] ?? ''); ?>">
                    <input type="text" name="search_code" placeholder="Search by Code..." value="<?php echo htmlspecialchars($_GET['search_code'] ?? ''); ?>">
                    <button type="submit" class="btn-filter">Filter</button>
                </form>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr><th>Photo</th><th>Name</th><th>Membership Code</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($members && $members->num_rows > 0): ?>
                                <?php while($member = $members->fetch_assoc()): 
                                    $join_date = new DateTime($member['join_date']);
                                    $expiry_date = (clone $join_date)->modify('+1 year');
                                    $now = new DateTime();
                                    $is_active = $now < $expiry_date;
                                    $interval = $now->diff($expiry_date);
                                    $days_left = $interval->format('%a');
                                ?>
                                    <tr>
                                        <td data-label="Photo"><img src="../<?php echo htmlspecialchars($member['photo_url']); ?>" width="50" style="border-radius: 50%; height: 50px; object-fit: cover;"></td>
                                        <td data-label="Name"><?php echo htmlspecialchars($member['name']); ?></td>
                                        <td data-label="Code"><?php echo htmlspecialchars($member['membership_code']); ?></td>
                                        <td data-label="Status">
                                            <?php if ($is_active): ?>
                                                <span class="status-badge status-active">Active (<?php echo $days_left; ?> days left)</span>
                                            <?php else: ?>
                                                <span class="status-badge status-expired">Expired</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Actions">
                                            <div class="action-buttons">
                                                <button class="btn-view" data-member='<?php echo json_encode($member, JSON_HEX_APOS); ?>'>View</button>
                                                <button class="btn-download-card" data-member='<?php echo json_encode($member, JSON_HEX_APOS); ?>' <?php if(!$is_active) echo 'disabled'; ?>>Card</button>
                                                <form method="post" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                                    <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                                    <button type="submit" name="delete_member" class="btn-delete-small">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5">No members found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <div class="mobile-overlay" id="mobile-overlay"></div>
    <div class="mobile-side-menu" id="mobile-side-menu">
        <nav class="admin-nav">
             <ul>
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
                <li><a href="manage_memberships.php" class="active"><i class="fas fa-users"></i><span>Memberships</span></a></li>
                <li><a href="manage_appointments.php"><i class="fas fa-calendar-check"></i><span>Appointments</span></a></li>
                <li><a href="manage_slider.php"><i class="fas fa-images"></i><span>Slider</span></a></li>
                <li><a href="manage_stories.php"><i class="fas fa-book-open"></i><span>Stories</span></a></li>
                <li><a href="manage_ticker.php"><i class="fas fa-newspaper"></i><span>News Ticker</span></a></li>
                <li><a href="manage_history.php"><i class="fas fa-landmark"></i><span>Temple History</span></a></li>
                <li><a href="manage_services.php"><i class="fas fa-concierge-bell"></i><span>Services</span></a></li>
                <li><a href="all_leads.php"><i class="fas fa-headset"></i><span>All Leads</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user-circle"></i><span>Profile</span></a></li>
                <li><a href="change_password.php"><i class="fas fa-key"></i><span>Password</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
                <!-- ... other menu items for mobile ... -->
            </ul>
        </nav>
    </div>

    <div class="modal-overlay" id="modal-overlay">
        <!-- Details & Card modals will be rendered here by JS -->
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
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

        // Modal Logic
        const modalOverlay = document.getElementById('modal-overlay');
        document.querySelectorAll('.btn-view').forEach(button => {
            button.addEventListener('click', function() {
                const memberData = JSON.parse(this.dataset.member);
                modalOverlay.innerHTML = `
                    <div class="modal-content-wrapper">
                        <div class="details-modal">
                            <button class="modal-close-btn">&times;</button>
                            <div class="details-header">
                                <img src="../${memberData.photo_url}" alt="Member Photo">
                                <h3>${memberData.name}</h3>
                                <p>${memberData.membership_code}</p>
                            </div>
                            <div class="details-grid">
                                <div class="detail-item"><strong>Email</strong><span>${memberData.email}</span></div>
                                <div class="detail-item"><strong>Mobile</strong><span>${memberData.mobile}</span></div>
                                <div class="detail-item"><strong>Date of Birth</strong><span>${new Date(memberData.dob).toLocaleDateString('en-GB')}</span></div>
                                <div class="detail-item"><strong>Occupation</strong><span>${memberData.occupation}</span></div>
                                <div class="detail-item full-width"><strong>Address</strong><span>${memberData.address}, ${memberData.city}, ${memberData.state} - ${memberData.pincode}</span></div>
                                <div class="detail-item full-width"><strong>Reason to Join</strong><span>${memberData.reason_to_join}</span></div>
                            </div>
                        </div>
                    </div>`;
                modalOverlay.classList.add('active');
            });
        });
        
        document.querySelectorAll('.btn-download-card').forEach(button => {
            button.addEventListener('click', function() {
                if(this.disabled) return;
                const memberData = JSON.parse(this.dataset.member);
                const joinDate = new Date(memberData.join_date);
                const validTillDate = new Date(joinDate.setFullYear(joinDate.getFullYear() + 1));
                const formattedJoinDate = new Date(memberData.join_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                const formattedValidTill = validTillDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                const formattedDob = new Date(memberData.dob).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                const fullAddress = `${memberData.city}, ${memberData.state}`;
                
                modalOverlay.innerHTML = `
                    <div class="modal-content-wrapper">
                         <div id="membership-card-modal" style="background: linear-gradient(45deg, #ffefda, #ffd28f); width: 380px; border-radius: 15px; color: #333; font-family: sans-serif;">
                            <div style="background: linear-gradient(45deg, #9F0102, #FF6D01); padding: 1rem; border-radius: 15px 15px 0 0; display: flex; align-items: center; gap: 1rem;">
                                <img src="/admin/uploads/logo.png" alt="Logo" style="width: 40px; height: 40px; filter: brightness(0) invert(1);">
                                <h3 style="color: white; font-size: 1.2rem; margin: 0;">SHIVARCHANAM TEMPLE</h3>
                            </div>
                            <div style="padding: 1.5rem; text-align: left; display: flex; gap: 1.5rem; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.1);">
                                <img src="../${memberData.photo_url}" alt="Member Photo" style="width: 100px; height: 100px; border-radius: 10px; object-fit: cover; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.2); flex-shrink: 0;">
                                <div>
                                    <h2 style="font-size: 1.4rem; font-weight: bold; margin: 0;">${memberData.name}</h2>
                                    <p style="font-size: 0.9rem; color: #555; margin-top: 0.25rem;">${fullAddress}</p>
                                    <div style="background: #1D1D1B; color: white; padding: 0.3rem 0.8rem; border-radius: 25px; display: inline-block; margin-top: 1rem; font-size: 0.8rem;">${memberData.membership_code}</div>
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; padding: 1.5rem; text-align: left;">
                                <div><strong style="display: block; font-size: 0.8rem; color: #888;">Date of Birth</strong><span style="display: block; font-size: 0.9rem;">${formattedDob}</span></div>
                                <div><strong style="display: block; font-size: 0.8rem; color: #888;">Joined On</strong><span style="display: block; font-size: 0.9rem;">${formattedJoinDate}</span></div>
                                <div style="grid-column: 1 / -1; text-align: center;"><strong>Valid Till</strong><span style="font-size: 1.2rem; color: #9F0102; display: block;">${formattedValidTill}</span></div>
                            </div>
                            <div style="background: #1D1D1B; padding: 1rem; text-align: center; border-radius: 0 0 15px 15px; display: flex; justify-content: space-between; align-items: center;">
                                <div style="color: #aaa; font-size: 0.8rem;"><a href="https://www.shivarchanam.com" style="color: #F58E58; text-decoration: none;">www.shivarchanam.com</a></div>
                                <div id="qrcode-modal" style="padding: 4px; background: white; border-radius: 4px;"></div>
                            </div>
                        </div>
                    </div>`;
                
                const qrCodeText = `Name: ${memberData.name}\nCode: ${memberData.membership_code}\nEmail: ${memberData.email}`;
                new QRCode(modalOverlay.querySelector("#qrcode-modal"), { text: qrCodeText, width: 60, height: 60 });

                modalOverlay.classList.add('active');

                setTimeout(() => {
                    html2canvas(modalOverlay.querySelector('#membership-card-modal'), { backgroundColor: null, scale: 3 })
                    .then(canvas => {
                        const link = document.createElement('a');
                        link.download = `Membership_Card_${memberData.membership_code}.png`;
                        link.href = canvas.toDataURL();
                        link.click();
                        modalOverlay.classList.remove('active');
                    });
                }, 500);
            });
        });
        
        modalOverlay.addEventListener('click', (e) => {
             if (e.target === modalOverlay || e.target.classList.contains('modal-close-btn')) {
                modalOverlay.classList.remove('active');
            }
        });
    });
    </script>
</body>
</html>

