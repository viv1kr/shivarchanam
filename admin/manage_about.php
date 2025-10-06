<?php 
require_once 'admin_header.php'; 

// --- Handle Form Submissions ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_content'])) {
        foreach ($_POST['content'] as $section => $content) {
            $stmt = $conn->prepare("UPDATE about_content SET content = ? WHERE section = ?");
            $stmt->bind_param("ss", $content, $section);
            $stmt->execute();
        }
    }
}

// Fetch all content for the form
$about_content_raw = $conn->query("SELECT * FROM about_content");
$about_content = [];
while($row = $about_content_raw->fetch_assoc()) {
    $about_content[$row['section']] = $row['content'];
}
?>

<div class="content-card">
    <div class="page-header">
        <h2>Manage About Us Page</h2>
    </div>
    <p>Update the text content that appears on the "About Us" page.</p>
    <form method="post">
        <div class="form-group">
            <label for="about_intro">About Introduction</label>
            <textarea id="about_intro" name="content[about_intro]" rows="5"><?php echo htmlspecialchars($about_content['about_intro'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="mission">Our Mission</label>
            <textarea id="mission" name="content[mission]" rows="3"><?php echo htmlspecialchars($about_content['mission'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="vision">Our Vision</label>
            <textarea id="vision" name="content[vision]" rows="3"><?php echo htmlspecialchars($about_content['vision'] ?? ''); ?></textarea>
        </div>
        <button type="submit" name="update_content" class="btn-submit">Save Changes</button>
    </form>
</div>

<?php 
require_once 'admin_footer.php'; 
?>
