<?php 
// **FIX:** The CSV download logic must run before any HTML is outputted.
// It is moved to the very top of the file.
if (isset($_GET['download']) && $_GET['download'] == 'all') {
    // We need to establish the DB connection here separately for the download.
    require_once '../config/db.php';

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="all_leads_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    
    // Header row for the CSV file
    fputcsv($output, ['Lead Type', 'Name', 'Contact Info', 'Address/Details', 'Date Collected']);
    
    // Fetch and write chatbot leads to the CSV
    $chatbot_result = $conn->query("SELECT name, mobile, address, created_at FROM chatbot_leads ORDER BY created_at DESC");
    if ($chatbot_result) {
        while ($row = $chatbot_result->fetch_assoc()) {
            fputcsv($output, ['Chatbot', $row['name'], $row['mobile'], $row['address'], $row['created_at']]);
        }
    }

    // Fetch and write joined people to the CSV
    $joined_result = $conn->query("SELECT name, email, join_date FROM joined_people ORDER BY join_date DESC");
    if ($joined_result) {
        while ($row = $joined_result->fetch_assoc()) {
            fputcsv($output, ['Joined People', $row['name'], $row['email'], '', $row['join_date']]);
        }
    }

    fclose($output);
    exit; // Stop the script after the download is generated.
}

// This includes the header, which handles security and the main layout for page display.
require_once 'admin_header.php'; 

// --- Handle Deletion Requests ---
$message = '';
$message_type = ''; // 'success' or 'error'
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_chatbot_lead'])) {
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
    if (isset($_POST['delete_joined_lead'])) {
        $lead_id = $_POST['lead_id'];
        $stmt = $conn->prepare("DELETE FROM joined_people WHERE id = ?");
        $stmt->bind_param("i", $lead_id);
        if ($stmt->execute()) {
            $message = 'Joined lead deleted successfully.';
            $message_type = 'success';
        } else {
            $message = 'Error deleting lead.';
            $message_type = 'error';
        }
    }
}

// --- Pagination Logic ---
$records_per_page = 10;

// Pagination for Chatbot Leads
$page_chatbot = isset($_GET['page_chatbot']) ? (int)$_GET['page_chatbot'] : 1;
$offset_chatbot = ($page_chatbot - 1) * $records_per_page;
$total_chatbot_query = $conn->query("SELECT COUNT(*) FROM chatbot_leads");
$total_chatbot_records = $total_chatbot_query->fetch_row()[0];
$total_chatbot_pages = ceil($total_chatbot_records / $records_per_page);
$chatbot_leads = $conn->query("SELECT * FROM chatbot_leads ORDER BY created_at DESC LIMIT $records_per_page OFFSET $offset_chatbot");

// Pagination for Joined People
$page_joined = isset($_GET['page_joined']) ? (int)$_GET['page_joined'] : 1;
$offset_joined = ($page_joined - 1) * $records_per_page;
$total_joined_query = $conn->query("SELECT COUNT(*) FROM joined_people");
$total_joined_records = $total_joined_query->fetch_row()[0];
$total_joined_pages = ceil($total_joined_records / $records_per_page);
$joined_people = $conn->query("SELECT * FROM joined_people ORDER BY join_date DESC LIMIT $records_per_page OFFSET $offset_joined");

// Function to generate pagination links
function generatePagination($total_pages, $current_page, $param_name, $current_tab) {
    if ($total_pages <= 1) return '';
    $html = '<div class="pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
        $active_class = ($i == $current_page) ? 'active' : '';
        $html .= "<a href='?tab={$current_tab}&{$param_name}={$i}' class='{$active_class}'>{$i}</a>";
    }
    $html .= '</div>';
    return $html;
}

// Determine which tab is active
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'chatbot';
?>
<style>
    .tabs-container { display: flex; border-bottom: 2px solid var(--border-color); margin-bottom: 2rem; }
    .tab-link { padding: 1rem 1.5rem; cursor: pointer; text-decoration: none; color: var(--dark-text); font-weight: 500; border-bottom: 3px solid transparent; }
    .tab-link.active { color: var(--primary-red); border-bottom-color: var(--primary-red); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .table-container { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; }
    th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); white-space: nowrap; }
    thead th { background-color: #f9fafa; font-weight: 600; color: var(--dark-text); }
    tbody tr:hover { background-color: #fcf6f0; }
    td { color: #555; }
    .btn-download { background-color: #28a745; color: var(--white); padding: 0.5rem 1rem; border-radius: 25px; text-decoration: none; font-size: 0.9rem; font-weight: 500; }
    .btn-delete-small { background: transparent; border: 1px solid #dc3545; color: #dc3545; border-radius: 5px; cursor: pointer; padding: 0.3rem 0.6rem; font-size: 0.8rem; }
    .pagination { display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem; }
    .pagination a { text-decoration: none; padding: 0.5rem 1rem; border: 1px solid var(--border-color); border-radius: 5px; color: var(--dark-text); }
    .pagination a.active { background-color: var(--primary-red); color: var(--white); border-color: var(--primary-red); }
</style>

<?php if ($message): ?>
    <div class="message <?php echo $message_type; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="content-card">
    <div class="page-header">
        <h2>All Visitor Leads</h2>
        <a href="?download=all" class="btn-download"><i class="fas fa-download"></i> Download All CSV</a>
    </div>
    <p>This page contains a consolidated view of all leads collected from your website.</p>
    
    <div class="tabs-container">
        <a href="?tab=chatbot" class="tab-link <?php echo ($active_tab == 'chatbot') ? 'active' : ''; ?>">Chatbot Leads</a>
        <a href="?tab=joined" class="tab-link <?php echo ($active_tab == 'joined') ? 'active' : ''; ?>">Joined People</a>
    </div>

    <div id="chatbot-content" class="tab-content <?php echo ($active_tab == 'chatbot') ? 'active' : ''; ?>">
        <h3><i class="fas fa-headset"></i> Chatbot Leads</h3>
        <div class="table-container">
             <table>
                <thead>
                    <tr><th>Name</th><th>Mobile</th><th>Address</th><th>Date Collected</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php if ($chatbot_leads && $chatbot_leads->num_rows > 0): while($lead = $chatbot_leads->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($lead['name']); ?></td>
                            <td><?php echo htmlspecialchars($lead['mobile']); ?></td>
                            <td><?php echo htmlspecialchars($lead['address']); ?></td>
                            <td><?php echo date('M j, Y, g:i a', strtotime($lead['created_at'])); ?></td>
                            <td>
                                <form action="all_leads.php?tab=chatbot" method="post" onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="lead_id" value="<?php echo $lead['id']; ?>">
                                    <button type="submit" name="delete_chatbot_lead" class="btn-delete-small">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="5">No leads have been collected from the chatbot yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo generatePagination($total_chatbot_pages, $page_chatbot, 'page_chatbot', 'chatbot'); ?>
    </div>

    <div id="joined-content" class="tab-content <?php echo ($active_tab == 'joined') ? 'active' : ''; ?>">
        <h3><i class="fas fa-user-plus"></i> Joined People</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Date Joined</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php if ($joined_people && $joined_people->num_rows > 0): while($person = $joined_people->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($person['name']); ?></td>
                            <td><?php echo htmlspecialchars($person['email']); ?></td>
                            <td><?php echo date('M j, Y, g:i a', strtotime($person['join_date'])); ?></td>
                            <td>
                                <form action="all_leads.php?tab=joined" method="post" onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="lead_id" value="<?php echo $person['id']; ?>">
                                    <button type="submit" name="delete_joined_lead" class="btn-delete-small">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="4">No one has joined through the website yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
         <?php echo generatePagination($total_joined_pages, $page_joined, 'page_joined', 'joined'); ?>
    </div>
</div>

<?php 
require_once 'admin_footer.php'; 
?>

