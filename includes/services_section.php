<?php
// This file is included from index.php, so the $conn variable is available.
$services_result = $conn->query("SELECT * FROM services ORDER BY id ASC");
$services = [];
if ($services_result && $services_result->num_rows > 0) {
    while($row = $services_result->fetch_assoc()) {
        $services[] = $row;
    }
}
?>
<style>
    /* --- THEME COLORS --- */
    :root {
        --astro-bg-dark: #1D1D1B;
        --astro-accent: #F58E58;
        --white: #ffffff;
        --astro-text-light: #d1d1d1;
        --astro-card-bg: #2a2a28;
    }

    /* --- SERVICES SECTION V12 STYLES --- */
    .services-section-v12 {
        padding: 6rem 2rem;
        background: linear-gradient(160deg, var(--astro-bg-dark) 0%, #41322A 100%);
        position: relative;
        overflow: hidden;
    }
    .services-section-v12::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/constellations.png');
        opacity: 0.1;
        z-index: 1;
    }

    .services-container-v12 {
        max-width: 900px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
        text-align: center;
    }
    
    .services-heading-v12 {
        color: var(--white);
        margin-bottom: 4rem;
    }
    .services-heading-v12 h2 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .services-heading-v12 p {
        font-size: 1.2rem;
        color: var(--astro-text-light);
        max-width: 600px;
        margin: 0 auto;
    }
    
    .service-carousel-wrapper-v12 {
        position: relative;
        height: 420px;
    }
    
    .service-carousel-v12 {
        position: relative;
        width: 100%;
        height: 100%;
        perspective: 1200px;
    }

    .service-card-v12 {
        position: absolute;
        width: 320px;
        height: 400px;
        top: 0;
        left: 50%;
        background: var(--astro-card-bg);
        border-radius: 20px;
        border: 1px solid rgba(245, 142, 88, 0.2);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2rem;
        text-align: center;
        color: var(--white);
        transition: transform 0.8s ease, opacity 0.8s ease, z-index 0.8s;
        cursor: grab;
    }
    
    /* Carousel states */
    .service-card-v12.active { transform: translateX(-50%) translateZ(0); opacity: 1; z-index: 10; }
    .service-card-v12.prev { transform: translateX(-120%) translateZ(-250px) rotateY(45deg); opacity: 0.4; z-index: 5; }
    .service-card-v12.next { transform: translateX(20%) translateZ(-250px) rotateY(-45deg); opacity: 0.4; z-index: 5; }
    .service-card-v12.hidden-prev { transform: translateX(-150%) translateZ(-350px) rotateY(60deg); opacity: 0; z-index: 1; }
    .service-card-v12.hidden-next { transform: translateX(50%) translateZ(-60deg); opacity: 0; z-index: 1; }
    
    .service-image-v12 {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        margin-bottom: 1.5rem;
        border: 3px solid var(--astro-accent);
        flex-shrink: 0;
    }
    .service-image-v12 img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .service-card-v12 h3 { font-size: 1.5rem; color: var(--white); margin-bottom: 1rem; }
    .service-description-v12 { color: var(--astro-text-light); font-size: 0.95rem; line-height: 1.6; flex-grow: 1; overflow-y: auto; margin-bottom: 1.5rem; padding-right: 10px; }
    
    /* Custom Scrollbar */
    .service-description-v12::-webkit-scrollbar { width: 6px; }
    .service-description-v12::-webkit-scrollbar-track { background: rgba(0,0,0,0.2); border-radius: 10px; }
    .service-description-v12::-webkit-scrollbar-thumb { background: var(--astro-accent); border-radius: 10px; }

    .btn-book-service-v12 { background: var(--astro-accent); color: var(--astro-bg-dark); padding: 0.8rem 2rem; border-radius: 50px; text-decoration: none; font-weight: bold; transition: transform 0.3s ease; display: inline-block; border: none; cursor: pointer; }
    .btn-book-service-v12:hover { transform: scale(1.05); }

    .carousel-nav-v12 { margin-top: 2.5rem; display: flex; justify-content: center; align-items: center; gap: 1.5rem; }
    .carousel-arrow-v12 { background: none; border: 1px solid var(--astro-accent); color: var(--astro-accent); width: 50px; height: 50px; border-radius: 50%; cursor: pointer; font-size: 1.2rem; transition: all 0.3s ease; }
    .carousel-arrow-v12:hover { background-color: var(--astro-accent); color: var(--astro-bg-dark); }
    
    /* --- BOOKING MODAL --- */
    .booking-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 2000; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s; }
    .booking-modal-overlay.active { opacity: 1; visibility: visible; }
    .booking-modal { background: var(--astro-card-bg); color: var(--white); padding: 2rem; border-radius: 15px; width: 90%; max-width: 400px; text-align: center; border: 1px solid rgba(245, 142, 88, 0.2); transform: scale(0.9); transition: transform 0.3s; }
    .booking-modal-overlay.active .booking-modal { transform: scale(1); }
    .booking-modal-header { margin-bottom: 1.5rem; }
    .booking-modal-header img { width: 100px; height: 100px; border-radius: 50%; border: 3px solid var(--astro-accent); object-fit: cover; margin-bottom: 1rem; }
    .booking-modal-header h3 { font-size: 1.5rem; margin-bottom: 0.5rem; }
    .booking-modal-header p { color: var(--astro-accent); }
    .booking-options a { display: flex; align-items: center; justify-content: center; gap: 1rem; padding: 1rem; border-radius: 10px; background: rgba(255,255,255,0.1); margin-bottom: 1rem; text-decoration: none; color: var(--white); transition: background-color 0.3s; }
    .booking-options a:hover { background: rgba(255,255,255,0.2); }
    .booking-options .fas, .booking-options .fab { font-size: 1.5rem; }
    .info-form-v12 { display: flex; flex-direction: column; gap: 1rem; }
    .info-form-v12 input { background: rgba(0,0,0,0.2); border: 1px solid #555; border-radius: 8px; padding: 0.8rem; color: var(--white); }
    .btn-confirm-booking { background: var(--astro-accent); color: var(--astro-bg-dark); border: none; padding: 0.8rem; border-radius: 8px; font-weight: bold; cursor: pointer; }

    @media (max-width: 600px) {
        .services-section-v12 { padding: 4rem 1rem; }
        .service-carousel-wrapper-v12 { height: 380px; }
        .service-card-v12 { width: 280px; height: 360px; padding: 1.5rem; }
        .service-card-v12.prev { transform: translateX(-80%) translateZ(-100px) rotateY(30deg); }
        .service-card-v12.next { transform: translateX(-20%) translateZ(-100px) rotateY(-30deg); }
    }
</style>

<section class="services-section-v12">
    <div class="services-container-v12">
        <div class="services-heading-v12">
            <h2>Our Sacred Services</h2>
            <p>Explore our sacred offerings, performed by Aacharya Vyas to bring balance, harmony, and divine blessings into your life.</p>
        </div>

        <?php if (!empty($services)): ?>
        <div class="service-carousel-wrapper-v12">
            <div class="service-carousel-v12" id="service-carousel">
                <?php foreach ($services as $index => $service): ?>
                <div class="service-card-v12" data-index="<?php echo $index; ?>" data-service-name="<?php echo htmlspecialchars($service['name']); ?>">
                    <div class="service-image-v12">
                        <img src="admin/<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>">
                    </div>
                    <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                    <div class="service-description-v12">
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                    </div>
                    <button class="btn-book-service-v12">Book Now</button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="carousel-nav-v12">
            <button class="carousel-arrow-v12" id="carousel-prev"><i class="fas fa-arrow-left"></i></button>
            <button class="carousel-arrow-v12" id="carousel-pause-play"><i class="fas fa-pause"></i></button>
            <button class="carousel-arrow-v12" id="carousel-next"><i class="fas fa-arrow-right"></i></button>
        </div>
        <?php else: ?>
            <p style="color: var(--astro-text-light);">Services will be listed here soon. Please check back later.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Booking Modal HTML -->
<div class="booking-modal-overlay" id="booking-modal-overlay">
    <div class="booking-modal" id="booking-modal">
        <div id="booking-step-1">
            <div class="booking-modal-header">
                <h3 id="booking-service-title-form">Service Name</h3>
                <p>Please provide your details to proceed.</p>
            </div>
            <form id="booking-info-form" class="info-form-v12">
                <input type="text" id="booking-name" placeholder="Your Name" required>
                <input type="text" id="booking-location" placeholder="Your City/Location" required>
                <button type="submit" class="btn-confirm-booking">Continue</button>
            </form>
        </div>
        <div id="booking-step-2" style="display: none;">
             <div class="booking-modal-header">
                <img src="https://i.ibb.co/yYXZc8T/priest-pooja.jpg" alt="Aacharya Kalpesh Vyas">
                <h3 id="booking-service-title-final">Service Name</h3>
                <p>Contact Aacharya Vyas to schedule your appointment.</p>
            </div>
            <div class="booking-options">
                <a href="tel:+919925904767">
                    <i class="fas fa-phone-alt"></i>
                    <span>Call Directly</span>
                </a>
                <a href="#" id="whatsapp-booking-link" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                    <span>Chat on WhatsApp</span>
                </a>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('service-carousel');
    if (carousel) {
        const cards = carousel.querySelectorAll('.service-card-v12');
        const prevBtn = document.getElementById('carousel-prev');
        const nextBtn = document.getElementById('carousel-next');
        const pausePlayBtn = document.getElementById('carousel-pause-play');
        const bookingModalOverlay = document.getElementById('booking-modal-overlay');
        const bookingStep1 = document.getElementById('booking-step-1');
        const bookingStep2 = document.getElementById('booking-step-2');
        const bookingForm = document.getElementById('booking-info-form');
        const bookingTitleForm = document.getElementById('booking-service-title-form');
        const bookingTitleFinal = document.getElementById('booking-service-title-final');
        const whatsappBookingLink = document.getElementById('whatsapp-booking-link');
        let currentIndex = 0;
        let autoRotateInterval;
        let isPaused = false;
        let selectedServiceName = '';

        function updateCarousel(newIndex, isAuto = false) {
            if (!isAuto) clearInterval(autoRotateInterval);
            currentIndex = (newIndex + cards.length) % cards.length;
            cards.forEach((card, i) => {
                card.classList.remove('active', 'prev', 'next', 'hidden-prev', 'hidden-next');
                let newPos = (i - currentIndex + cards.length) % cards.length;
                if (newPos === 0) card.classList.add('active');
                else if (newPos === 1) card.classList.add('next');
                else if (newPos === cards.length - 1) card.classList.add('prev');
                else if (newPos === 2) card.classList.add('hidden-next');
                else if (newPos === cards.length - 2) card.classList.add('hidden-prev');
                else card.classList.add('hidden-next');
            });
            if (!isAuto && !isPaused) startAutoRotate();
        }
        
        function startAutoRotate() {
            clearInterval(autoRotateInterval);
            autoRotateInterval = setInterval(() => updateCarousel(currentIndex + 1, true), 4000);
            isPaused = false;
            if(pausePlayBtn) pausePlayBtn.innerHTML = '<i class="fas fa-pause"></i>';
        }
        
        function pauseAutoRotate() {
            clearInterval(autoRotateInterval);
            isPaused = true;
            if(pausePlayBtn) pausePlayBtn.innerHTML = '<i class="fas fa-play"></i>';
        }

        if(prevBtn) prevBtn.addEventListener('click', () => updateCarousel(currentIndex - 1));
        if(nextBtn) nextBtn.addEventListener('click', () => updateCarousel(currentIndex + 1));
        if(pausePlayBtn) pausePlayBtn.addEventListener('click', () => {
            if (isPaused) startAutoRotate();
            else pauseAutoRotate();
        });

        // Mobile Swipe
        let touchStartX = 0;
        carousel.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; clearInterval(autoRotateInterval); }, { passive: true });
        carousel.addEventListener('touchend', e => {
            const touchEndX = e.changedTouches[0].clientX;
            if (touchStartX - touchEndX > 50) updateCarousel(currentIndex + 1);
            else if (touchEndX - touchStartX > 50) updateCarousel(currentIndex - 1);
            if (!isPaused) startAutoRotate();
        });

        // Booking Modal Logic
        cards.forEach(card => {
            const bookBtn = card.querySelector('.btn-book-service-v12');
            bookBtn.addEventListener('click', () => {
                selectedServiceName = card.dataset.serviceName;
                bookingTitleForm.textContent = selectedServiceName;
                bookingStep1.style.display = 'block';
                bookingStep2.style.display = 'none';
                bookingModalOverlay.classList.add('active');
            });
        });
        
        bookingForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('booking-name').value;
            const location = document.getElementById('booking-location').value;

            bookingTitleFinal.textContent = selectedServiceName;
            
            const prefilledMessage = `Hari Om! I would like to book an appointment for the "${selectedServiceName}" service. My name is ${name} from ${location}.`;
            const whatsappUrl = `https://wa.me/919925904767?text=${encodeURIComponent(prefilledMessage)}`;
            if (whatsappBookingLink) {
                whatsappBookingLink.href = whatsappUrl;
            }

            bookingStep1.style.display = 'none';
            bookingStep2.style.display = 'block';
        });
        
        bookingModalOverlay.addEventListener('click', (e) => {
            if (e.target === bookingModalOverlay) {
                bookingModalOverlay.classList.remove('active');
            }
        });

        updateCarousel(0);
        startAutoRotate();
    }
});
</script>

