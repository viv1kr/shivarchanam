<?php 
require_once 'admin_header.php'; 

$message = '';
$message_type = '';

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_stream'])) {
    $url = $_POST['youtube_url'];
    $title = $_POST['title'];
    $is_live = isset($_POST['is_live']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE live_stream SET youtube_url = ?, title = ?, is_live = ? WHERE id = 1");
    $stmt->bind_param("ssi", $url, $title, $is_live);
    if ($stmt->execute()) {
        $message = 'Live stream details updated successfully.';
        $message_type = 'success';
    } else {
        $message = 'Error updating details.';
        $message_type = 'error';
    }
}

// Fetch current stream details
$stream_details = $conn->query("SELECT * FROM live_stream WHERE id = 1")->fetch_assoc();
?>

<?php if ($message): ?>
    <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="content-card">
    <div class="page-header">
        <h2>Manage Live Stream</h2>
    </div>
    <p>Update the YouTube Live URL and set the status to show or hide the live video section on the homepage.</p>
</div>

<div class="content-card">
    <h2>Live Stream Settings</h2>
    <form action="manage_live.php" method="post">
        <div class="form-group">
            <label for="youtube_url">YouTube Live URL</label>
            <input type="url" id="youtube_url" name="youtube_url" value="<?php echo htmlspecialchars($stream_details['youtube_url']); ?>" required>
        </div>
        <div class="form-group">
            <label for="title">Stream Title</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($stream_details['title']); ?>" required>
        </div>
        <div class="form-group" style="display: flex; align-items: center; gap: 1rem;">
            <input type="checkbox" id="is_live" name="is_live" value="1" <?php echo ($stream_details['is_live']) ? 'checked' : ''; ?> style="width: auto;">
            <label for="is_live" style="margin: 0;"><strong>Show as LIVE on homepage</strong></label>
        </div>
        <button type="submit" name="update_stream" class="btn-submit">Update Stream</button>
    </form>
</div>

<?php 
require_once 'admin_footer.php'; 
?>
