<?php 
require_once 'includes/header.php'; 

// Fetch content for the About Us page
$about_content_raw = $conn->query("SELECT * FROM about_content");
$about = [];
while($row = $about_content_raw->fetch_assoc()) {
    $about[$row['section']] = $row['content'];
}

// Fetch gallery photos for the slider
$gallery_photos_result = $conn->query("SELECT image_url, caption FROM gallery_photos ORDER BY upload_date DESC LIMIT 10");
$gallery_photos = [];
if($gallery_photos_result) {
    while($row = $gallery_photos_result->fetch_assoc()) {
        $gallery_photos[] = $row;
    }
}

// Fetch trustees
$trustees_result = $conn->query("SELECT * FROM trustees ORDER BY id ASC");
$trustees = [];
if($trustees_result) {
    while($row = $trustees_result->fetch_assoc()) {
        $trustees[] = $row;
    }
}
?>
<style>
    .about-hero {
        padding: 5rem 2rem;
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://i.ibb.co/bJCJ8X7/mandala-bg.png') no-repeat center center/cover;
        text-align: center;
        color: var(--white);
    }
    .about-hero h1 { font-size: 3.5rem; margin-bottom: 1rem; }
    .about-hero p { font-size: 1.2rem; max-width: 600px; margin: 0 auto; color: #eee; }

    .about-section { padding: 4rem 2rem; background: var(--light-bg); }
    .about-container { max-width: 900px; margin: 0 auto; text-align: center; }
    .about-container h2 { font-size: 2.5rem; color: var(--primary-red); margin-bottom: 1rem; }
    .about-container p { color: #555; font-size: 1.1rem; line-height: 1.8; }

    .mission-vision-section {
        padding: 4rem 2rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    .mission-vision-card { text-align: center; }
    .mission-vision-card .fas { font-size: 3rem; color: var(--accent-orange); margin-bottom: 1rem; }
    .mission-vision-card h3 { font-size: 1.8rem; color: var(--primary-red); margin-bottom: 1rem; }
    .mission-vision-card p { color: #555; line-height: 1.7; }
    
    .gallery-slider-section { padding: 4rem 0; background: var(--gray); overflow: hidden; }
    .gallery-slider-track { display: flex; width: fit-content; animation: scroll-gallery 60s linear infinite; }
    .gallery-slide { flex-shrink: 0; width: 400px; height: 300px; margin: 0 1rem; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .gallery-slide img { width: 100%; height: 100%; object-fit: cover; }
    @keyframes scroll-gallery { from { transform: translateX(0); } to { transform: translateX(-50%); } }

    .trustee-section { padding: 4rem 2rem; }
    .trustee-container { max-width: 1200px; margin: 0 auto; text-align: center; }
    .trustee-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 3rem; }
    .trustee-card { background: var(--white); padding: 2rem; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    .trustee-photo { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary-red); margin-bottom: 1rem; }
    .trustee-card h4 { font-size: 1.4rem; }
    .trustee-card p { color: var(--accent-orange); font-weight: 500; }

    @media (max-width: 768px) {
        .mission-vision-section { grid-template-columns: 1fr; }
    }
</style>

<main>
    <section class="about-hero">
        <h1>About Shivarchanam Temple</h1>
        <p>A Sacred Space for Spiritual Growth and Community</p>
    </section>

    <section class="about-section">
        <div class="about-container">
            <h2>Our Story</h2>
            <p><?php echo htmlspecialchars($about['about_intro'] ?? ''); ?></p>
        </div>
    </section>
    
    <section class="mission-vision-section">
        <div class="mission-vision-card">
            <i class="fas fa-bullseye"></i>
            <h3>Our Mission</h3>
            <p><?php echo htmlspecialchars($about['mission'] ?? ''); ?></p>
        </div>
        <div class="mission-vision-card">
            <i class="fas fa-eye"></i>
            <h3>Our Vision</h3>
            <p><?php echo htmlspecialchars($about['vision'] ?? ''); ?></p>
        </div>
    </section>

    <?php if(!empty($gallery_photos)): ?>
    <section class="gallery-slider-section">
        <div class="gallery-slider-track">
            <?php foreach ($gallery_photos as $photo): ?>
            <div class="gallery-slide"><img src="admin/<?php echo $photo['image_url']; ?>" alt="<?php echo $photo['caption']; ?>"></div>
            <?php endforeach; ?>
            <!-- Duplicate for seamless loop -->
            <?php foreach ($gallery_photos as $photo): ?>
            <div class="gallery-slide"><img src="admin/<?php echo $photo['image_url']; ?>" alt="<?php echo $photo['caption']; ?>"></div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="trustee-section">
        <div class="trustee-container">
            <h2>Our Temple Trustees</h2>
            <div class="trustee-grid">
                <?php foreach($trustees as $trustee): ?>
                <div class="trustee-card">
                    <img src="<?php echo htmlspecialchars($trustee['photo_url']); ?>" alt="<?php echo htmlspecialchars($trustee['name']); ?>" class="trustee-photo">
                    <h4><?php echo htmlspecialchars($trustee['name']); ?></h4>
                    <p><?php echo htmlspecialchars($trustee['title']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
