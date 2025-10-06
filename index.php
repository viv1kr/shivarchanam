<?php
// The header now handles session and DB connection, preventing errors.
require_once 'includes/header.php';

// Fetch slider images from the database. The $conn variable is available from the header.
$slider_images = $conn->query("SELECT * FROM slider_images ORDER BY id DESC");

// Fetch the 4 most recent stories for the homepage
$stories = $conn->query("SELECT * FROM stories ORDER BY id DESC LIMIT 4");
?>

<main>
    <!-- HERO SLIDER SECTION -->
    <section class="hero">
        <div class="slides">
            <?php 
            if ($slider_images && $slider_images->num_rows > 0):
                $first = true;
                while($slide = $slider_images->fetch_assoc()): ?>
                    <div class="slide <?php if($first) { echo 'active'; $first = false; } ?>" style="background-image: url('admin/<?php echo htmlspecialchars($slide['image_url']); ?>');">
                        <div class="slide-content">
                            <h1><?php echo htmlspecialchars($slide['title']); ?></h1>
                            <p><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                        </div>
                    </div>
                <?php endwhile;
            else: ?>
                <!-- Fallback content if no slides are in the database -->
                <div class="slide active" style="background-image: url('https://picsum.photos/id/1015/1600/900');">
                    <div class="slide-content">
                        <h1>Welcome to Shivarchanam</h1>
                        <p>Add images to the slider in your admin panel.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Slider Navigation -->
        <div class="slider-controls" id="dots"></div>
        <div class="slider-nav">
            <button class="nav-arrow" id="prev-slide"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-arrow" id="next-slide"><i class="fas fa-chevron-right"></i></button>
        </div>
    </section>

    <!-- NEWS TICKER SECTION -->
    <?php include 'includes/news_ticker.php'; ?>

    <!-- STORIES SECTION -->
    <section class="stories-wrapper">
        <h2 class="section-heading">Daily Stories</h2>
        <div class="stories-container">
            <div class="stories">
                <?php 
                if ($stories && $stories->num_rows > 0):
                    while($story = $stories->fetch_assoc()): ?>
                        <a href="story.php?story_id=<?php echo $story['id']; ?>" class="story-thumb-link">
                            <div class="story-thumb">
                                <img src="admin/<?php echo htmlspecialchars($story['thumbnail_url']); ?>" alt="Story Thumbnail">
                            </div>
                        </a>
                    <?php endwhile;
                else: ?>
                    <p class="no-content-message">No stories available yet. Please add some from the admin panel.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>





    <!--LIVE DARSHAN SECTION -->
    <?php include 'includes/live_section.php'; ?>

    <!-- EVENT SECTION -->
    <?php include 'includes/events_section.php'; ?>

    <!-- ABOUT SECTION -->
    <?php include 'includes/about_section.php'; ?>

     <!-- ABOUT SECTION -->
    <?php include 'includes/how_to_reach_section.php'; ?>

    <!-- ASTROLOGER SECTION -->
    <?php include 'includes/priest_section.php'; ?>

     <!-- SERVICES SECTION -->
    <?php include 'includes/services_section.php'; ?>

     <!-- TEMPLE HISTORY SECTION -->
    <?php include 'includes/temple_history.php'; ?>

     <!-- VISITOR GUIDE SECTION -->
    <?php include 'includes/visitor_guide_section.php'; ?>

     <!-- VISITOR GUIDE SECTION -->
    <?php include 'includes/donations_section.php'; ?>

     <!-- TEMPLE HISTORY SECTION -->
    <?php include 'includes/testimonial_section.php'; ?>

   

</main>

<?php 
// Include the website footer
require_once 'includes/footer.php'; 
?>

