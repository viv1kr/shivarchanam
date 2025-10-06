<?php 
require_once 'admin_header.php'; 

$message = '';
$message_type = '';

// Handle Deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_photo'])) {
    $photo_id = $_POST['photo_id'];
    
    // Get the image path to delete the file from the server
    $stmt = $conn->prepare("SELECT image_url FROM gallery_photos WHERE id = ?");
    $stmt->bind_param("i", $photo_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result && !empty($result['image_url']) && file_exists($result['image_url'])) {
        unlink($result['image_url']);
    }

    // Delete the record from the database
    $stmt = $conn->prepare("DELETE FROM gallery_photos WHERE id = ?");
    $stmt->bind_param("i", $photo_id);
    if ($stmt->execute()) {
        $message = 'Photo deleted successfully.';
        $message_type = 'success';
    } else {
        $message = 'Error deleting photo.';
        $message_type = 'error';
    }
}

// Handle Addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_photo'])) {
    $category = $_POST['category'];
    $caption = $_POST['caption'];
    $image_url = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/gallery/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
        $image_filename = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
            $stmt = $conn->prepare("INSERT INTO gallery_photos (category, caption, image_url) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $category, $caption, $image_url);
            if ($stmt->execute()) {
                $message = 'New photo added to the gallery successfully.';
                $message_type = 'success';
            }
        }
    } else {
        $message = 'Please select an image to upload.';
        $message_type = 'error';
    }
}

$photos = $conn->query("SELECT * FROM gallery_photos ORDER BY upload_date DESC");
?>

<?php if ($message): ?>
    <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="content-card">
    <div class="page-header">
        <h2>Manage Temple Gallery</h2>
    </div>
    <p>Add or remove photos from the temple's public gallery.</p>
</div>

<div class="content-card">
    <h2>Add New Photo</h2>
    <form action="manage_gallery.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" id="category" name="category" placeholder="e.g., Festivals, Temple, Deities" required>
        </div>
        <div class="form-group">
            <label for="caption">Caption (Optional)</label>
            <input type="text" id="caption" name="caption">
        </div>
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" id="image" name="image" accept="image/*" required>
        </div>
        <button type="submit" name="add_photo" class="btn-submit">Add Photo</button>
    </form>
</div>

<div class="content-card">
    <h2>Current Gallery Photos</h2>
    <div class="item-grid">
        <?php if ($photos && $photos->num_rows > 0): ?>
            <?php while($photo = $photos->fetch_assoc()): ?>
                <div class="grid-item">
                    <img src="<?php echo htmlspecialchars($photo['image_url']); ?>" alt="<?php echo htmlspecialchars($photo['caption']); ?>">
                    <div class="item-info">
                        <h4><?php echo htmlspecialchars($photo['category']); ?></h4>
                        <p><?php echo htmlspecialchars($photo['caption']); ?></p>
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this photo?');">
                            <input type="hidden" name="photo_id" value="<?php echo $photo['id']; ?>">
                            <button type="submit" name="delete_photo" class="btn-delete">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No photos have been added to the gallery yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php 
require_once 'admin_footer.php'; 
?>

