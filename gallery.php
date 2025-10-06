<?php 
require_once 'includes/header.php'; 

// Fetch all gallery photos and create a list of unique categories
$photos_result = $conn->query("SELECT * FROM gallery_photos ORDER BY upload_date DESC");
$photos = [];
$categories = ['All'];
if ($photos_result) {
    while($row = $photos_result->fetch_assoc()){
        $photos[] = $row;
        if (!in_array($row['category'], $categories)) {
            $categories[] = $row['category'];
        }
    }
}
?>
<style>
    :root {
        --primary-red: #9F0102;
        --accent-orange: #FF6D01;
        --light-bg: #FFEFDA;
        --white: #ffffff;
        --dark-text: #333333;
        --gray: #f4f5f7;
    }
    .gallery-hero {
        padding: 4rem 2rem;
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://i.ibb.co/bJCJ8X7/mandala-bg.png') no-repeat center center/cover;
        text-align: center;
        color: var(--white);
    }
    .gallery-hero h1 { font-size: 3rem; margin-bottom: 0.5rem; }
    .gallery-hero p { font-size: 1.2rem; max-width: 600px; margin: 0 auto; color: #eee; }

    .gallery-section { 
        padding: 4rem 2rem; 
        background: var(--gray); 
    }
    .gallery-container { 
        max-width: 1200px; 
        margin: 0 auto; 
        text-align: center; 
    }
    .gallery-filters { 
        margin-bottom: 3rem; 
        display: flex; 
        flex-wrap: wrap; 
        justify-content: center; 
        gap: 1rem; 
    }
    .filter-btn { 
        background: var(--white); 
        border: 1px solid #ddd; 
        padding: 0.8rem 1.5rem; 
        border-radius: 25px; 
        cursor: pointer; 
        font-weight: 500; 
        transition: all 0.3s; 
    }
    .filter-btn.active, .filter-btn:hover { 
        background: var(--primary-red); 
        color: var(--white); 
        border-color: var(--primary-red); 
    }
    
    .photo-grid {
        column-count: 3; /* Default for desktop */
        column-gap: 1rem;
    }
    .photo-item {
        margin-bottom: 1rem;
        break-inside: avoid;
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        cursor: pointer;
    }
    .photo-item img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.4s ease;
    }
    .photo-item:hover img {
        transform: scale(1.05);
    }
    
    /* Lightbox Styles */
    .lightbox-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); z-index: 3000; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.4s; }
    .lightbox-overlay.active { opacity: 1; visibility: visible; }
    .lightbox-content { position: relative; max-width: 90%; max-height: 90%; }
    .lightbox-img { max-width: 100%; max-height: 100%; border-radius: 10px; }
    .lightbox-close, .lightbox-prev, .lightbox-next { position: absolute; color: var(--white); font-size: 2rem; cursor: pointer; background: none; border: none; }
    .lightbox-close { top: 1rem; right: 1rem; }
    .lightbox-prev, .lightbox-next { top: 50%; transform: translateY(-50%); }
    .lightbox-prev { left: 1rem; }
    .lightbox-next { right: 1rem; }

    @media (max-width: 1024px) {
        .photo-grid { column-count: 2; }
    }
    @media (max-width: 768px) {
        .photo-grid { column-count: 1; }
    }
</style>

<main>
    <section class="gallery-hero">
        <h1>Temple Gallery</h1>
        <p>A collection of sacred moments and divine beauty from Shivarchanam Temple.</p>
    </section>

    <section class="gallery-section">
        <div class="gallery-container">
            <div class="gallery-filters">
                <?php foreach($categories as $category): ?>
                    <button class="filter-btn <?php echo ($category == 'All') ? 'active' : ''; ?>" data-filter="<?php echo htmlspecialchars(strtolower($category)); ?>"><?php echo htmlspecialchars($category); ?></button>
                <?php endforeach; ?>
            </div>
            <div class="photo-grid">
                <?php foreach($photos as $photo): ?>
                <div class="photo-item" data-category="<?php echo htmlspecialchars(strtolower($photo['category'])); ?>" data-src="admin/<?php echo htmlspecialchars($photo['image_url']); ?>">
                    <img src="admin/<?php echo htmlspecialchars($photo['image_url']); ?>" alt="<?php echo htmlspecialchars($photo['caption']); ?>">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<!-- Lightbox HTML -->
<div class="lightbox-overlay" id="lightbox-overlay">
    <button class="lightbox-close">&times;</button>
    <button class="lightbox-prev">&lt;</button>
    <div class="lightbox-content">
        <img src="" alt="Gallery Image" class="lightbox-img">
    </div>
    <button class="lightbox-next">&gt;</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const photoItems = document.querySelectorAll('.photo-item');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            
            photoItems.forEach(item => {
                if (filter === 'all' || item.dataset.category === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    const lightboxOverlay = document.getElementById('lightbox-overlay');
    const lightboxImg = lightboxOverlay.querySelector('.lightbox-img');
    let currentIndex = 0;
    let visibleItems = [];

    const openLightbox = (clickedItem) => {
        visibleItems = Array.from(photoItems).filter(item => item.style.display !== 'none');
        currentIndex = visibleItems.indexOf(clickedItem);
        lightboxImg.src = clickedItem.dataset.src;
        lightboxOverlay.classList.add('active');
    };

    const showNextImage = () => {
        currentIndex = (currentIndex + 1) % visibleItems.length;
        lightboxImg.src = visibleItems[currentIndex].dataset.src;
    };
    
    const showPrevImage = () => {
        currentIndex = (currentIndex - 1 + visibleItems.length) % visibleItems.length;
        lightboxImg.src = visibleItems[currentIndex].dataset.src;
    };
    
    photoItems.forEach((item) => {
        item.addEventListener('click', () => openLightbox(item));
    });

    lightboxOverlay.querySelector('.lightbox-close').addEventListener('click', () => lightboxOverlay.classList.remove('active'));
    lightboxOverlay.querySelector('.lightbox-prev').addEventListener('click', showPrevImage);
    lightboxOverlay.querySelector('.lightbox-next').addEventListener('click', showNextImage);
    lightboxOverlay.addEventListener('click', (e) => {
        if (e.target === lightboxOverlay) {
            lightboxOverlay.classList.remove('active');
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>

