<?php 
// --- CSV DOWNLOAD LOGIC (MUST RUN BEFORE ANY HTML) ---
if (isset($_GET['download']) && $_GET['download'] == 'csv') {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    if (!isset($_SESSION['admin_logged_in'])) { exit('Access Denied.'); }
    require_once '../config/db.php';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="contact_messages_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Subject', 'Message', 'Submission Date']);
    
    $result = $conn->query("SELECT * FROM contact_messages ORDER BY submission_date DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    }
    fclose($output);
    exit;
}

// --- REGULAR PAGE LOGIC ---
// This now correctly includes the header, which handles security, layout, and database connection.
require_once 'admin_header.php'; 

// Handle Deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_message'])) {
    $message_id = $_POST['message_id'];
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
}

$messages = $conn->query("SELECT * FROM contact_messages ORDER BY submission_date DESC");
?>
<style>
    /* Additional styles for this page */
    .table-container {
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1.5rem;
    }
    th, td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }
    thead th {
        background-color: #f9fafa;
        font-weight: 600;
    }
    tbody tr:hover {
        background-color: #fcf6f0;
    }
    .btn-delete-small {
        background: transparent;
        border: 1px solid #dc3545;
        color: #dc3545;
        border-radius: 5px;
        cursor: pointer;
        padding: 0.4rem 0.8rem;
    }

    @media (max-width: 900px) {
        .table-container {
            overflow-x: hidden;
        }
        table, thead, tbody, th, td, tr { 
            display: block; 
        }
        thead {
            display: none;
        }
        tr {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 1rem;
        }
        td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 0.75rem 0;
            white-space: normal;
            text-align: right;
        }
        td:last-child {
            border-bottom: none;
        }
        td::before {
            content: attr(data-label);
            font-weight: 600;
            margin-right: 1rem;
            text-align: left;
            color: var(--dark-text);
        }
    }
</style>

<div class="content-card">
    <div class="page-header">
        <h2>Contact Form Messages</h2>
        <a href="?download=csv" class="btn-download"><i class="fas fa-download"></i> Download CSV</a>
    </div>
    <p>This table contains all messages submitted through the website's contact form.</p>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($messages && $messages->num_rows > 0): ?>
                    <?php while($msg = $messages->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Name"><?php echo htmlspecialchars($msg['name']); ?></td>
                            <td data-label="Email"><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td data-label="Subject"><?php echo htmlspecialchars($msg['subject']); ?></td>
                            <td data-label="Message" style="white-space: normal; min-width: 200px;"><?php echo htmlspecialchars($msg['message']); ?></td>
                            <td data-label="Date"><?php echo date('M j, Y', strtotime($msg['submission_date'])); ?></td>
                            <td data-label="Action">
                                <form method="post" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                    <button type="submit" name="delete_message" class="btn-delete-small">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No messages received yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
// This now correctly includes the footer, which handles the slide-out mobile menu.
require_once 'admin_footer.php'; 
?>

