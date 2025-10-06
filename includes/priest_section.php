<style>
    /* --- THEME COLORS --- */
    :root {
        --primary-red: #9F0102;
        --accent-orange: #FF6D01;
        --light-bg: #FFEFDA;
        --dark-text: #333333;
        --white: #ffffff;
        --astro-bg-dark: #1D1D1B;
        --astro-card-bg: #2a2a28;
    }

    /* --- ASTROLOGER SECTION V15 STYLES --- */
    .astrologer-section-v15 {
        padding: 6rem 2rem;
        background: linear-gradient(160deg, var(--astro-bg-dark) 0%, #41322A 100%);
        position: relative;
        overflow: hidden;
    }
    .astrologer-section-v15::before {
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

    .astrologer-container-v15 {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        align-items: center;
        gap: 4rem;
        position: relative;
        z-index: 2;
    }
    
    /* --- Photo Slider Column with Stack Effect --- */
    .astrologer-card-stack-v15 {
        position: relative;
        width: 100%;
        max-width: 380px;
        height: 475px;
        margin: 0 auto;
    }

    .photo-card-v15 {
        position: absolute;
        width: 100%;
        height: 100%;
        background: var(--astro-card-bg);
        border-radius: 20px;
        border: 1px solid rgba(245, 142, 88, 0.2);
        overflow: hidden;
        cursor: grab;
        transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        transform-origin: center bottom; /* Tilt from bottom */
    }
    
    /* Card states for animation */
    .photo-card-v15.active {
        transform: rotate(0deg) scale(1) translateY(0);
        z-index: 3;
        opacity: 1;
    }
    .photo-card-v15.next {
        transform: rotate(6deg) scale(0.95);
        z-index: 2;
        opacity: 1;
    }
    .photo-card-v15.next-next {
        transform: rotate(12deg) scale(0.9);
        z-index: 1;
        opacity: 1;
    }
    .photo-card-v15.hidden-card {
        transform: rotate(0deg) scale(0.8) translateY(20px);
        opacity: 0;
        z-index: 0;
    }

    .photo-card-v15 img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .photo-text-overlay-v15 {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 4rem 1.5rem 1.5rem 1.5rem;
        background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%);
        color: var(--white);
        text-align: left;
    }
    .photo-text-overlay-v15 h4 { font-size: 1.2rem; margin-bottom: 0.5rem; }
    .photo-text-overlay-v15 p { font-size: 0.9rem; color: #d1d1d1; }
    
    .no-image-slide { padding: 2rem; text-align: center; }

    .slider-controls-v15 {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
    }
    .slider-arrow-v15 {
        background: none;
        border: 1px solid var(--accent-orange);
        color: var(--accent-orange);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }
    .slider-arrow-v15:hover {
        background-color: var(--accent-orange);
        color: var(--astro-bg-dark);
    }
    
    /* --- Astrologer Details Column --- */
    .astrologer-details-column-v15 { color: var(--white); }
    .astrologer-photo-wrapper-v15 { width: 150px; height: 150px; border-radius: 50%; margin-bottom: 1.5rem; position: relative; }
    .astrologer-photo-v15 { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid var(--accent-orange); }
    .astrologer-name-v15 { font-size: 2.5rem; margin-bottom: 0.5rem; }
    .astrologer-title-v15 { font-size: 1.1rem; color: var(--accent-orange); margin-bottom: 1.5rem; }
    .astrologer-bio-v15 { font-size: 1rem; color: #d1d1d1; line-height: 1.7; margin-bottom: 2rem; }
    .btn-contact-astro-v15 { background: var(--accent-orange); color: var(--astro-bg-dark); padding: 0.8rem 2rem; border-radius: 50px; text-decoration: none; font-weight: bold; transition: transform 0.3s ease; display: inline-block; }
    .btn-contact-astro-v15:hover { transform: scale(1.05); }

    @media (max-width: 900px) {
        .astrologer-container-v15 { grid-template-columns: 1fr; }
        .astrologer-details-column-v15 { text-align: center; display: flex; flex-direction: column; align-items: center; order: -1; margin-bottom: 3rem; }
        .astrologer-card-stack-v15 { max-width: 320px; height: 400px; }
    }
</style>

<section class="astrologer-section-v15">
    <div class="astrologer-container-v15">
        <div class="astrologer-card-stack-container">
            <div class="astrologer-card-stack-v15" id="card-stack">
                <div class="photo-card-v15">
                    <img src="https://i.ibb.co/yYXZc8T/priest-pooja.jpg" alt="Aacharya Kalpesh Vyas performing puja">
                    <div class="photo-text-overlay-v15">
                        <h4>Sacred Puja Ceremony</h4>
                        <p>Invoking divine blessings for peace and prosperity.</p>
                    </div>
                </div>
                <div class="photo-card-v15">
                    <img src="https://i.ibb.co/9v0G6F3/priest-photo.jpg" alt="Aacharya Kalpesh Vyas">
                     <div class="photo-text-overlay-v15">
                        <h4>Personal Consultation</h4>
                        <p>Providing spiritual guidance through ancient Vedic wisdom.</p>
                    </div>
                </div>
                 <div class="photo-card-v15 no-image-slide">
                     <div class="photo-text-overlay-v15" style="background: none;">
                        <h4 style="font-size: 1.5rem; color: var(--accent-orange);">Expertise in Vastu</h4>
                        <p>Harmonize your living spaces with Vastu Shastra principles for a balanced life.</p>
                    </div>
                </div>
            </div>
            <div class="slider-controls-v15">
                <button class="slider-arrow-v15" id="prev-card-btn"><i class="fas fa-arrow-left"></i></button>
                <button class="slider-arrow-v15" id="pause-play-btn"><i class="fas fa-pause"></i></button>
                <button class="slider-arrow-v15" id="next-card-btn"><i class="fas fa-arrow-right"></i></button>
            </div>
        </div>

        <div class="astrologer-details-column-v15">
            <div class="astrologer-photo-wrapper-v15">
                <img src="https://i.ibb.co/9v0G6F3/priest-photo.jpg" alt="Aacharya Kalpesh Vyas" class="astrologer-photo-v15">
            </div>
            <h3 class="astrologer-title-v15">Head Priest & Vedic Astrologer</h3>
            <h2 class="astrologer-name-v15">Aacharya Kalpesh Vyas</h2>
            <p class="astrologer-bio-v15">
               With profound wisdom in Vedic traditions, Aacharya Vyas is the spiritual cornerstone of our community, offering clarity and guidance through sacred astrological practices.
            </p>
            <a href="#" class="btn-contact-astro-v15">Contact Astrologer</a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cardStack = document.getElementById('card-stack');
    if (cardStack) {
        const cards = Array.from(cardStack.querySelectorAll('.photo-card-v15'));
        const nextBtn = document.getElementById('next-card-btn');
        const prevBtn = document.getElementById('prev-card-btn');
        const pausePlayBtn = document.getElementById('pause-play-btn');
        let currentIndex = 0;
        let autoSlide;
        let isPaused = false;
        
        function updateCards(manual = false) {
            if (manual) {
                clearInterval(autoSlide);
            }
            
            cards.forEach((card, index) => {
                card.classList.remove('active', 'next', 'next-next', 'hidden-card');
                
                let pos = (index - currentIndex + cards.length) % cards.length;
                
                if (pos === 0) card.classList.add('active');
                else if (pos === 1) card.classList.add('next');
                else if (pos === 2) card.classList.add('next-next');
                else card.classList.add('hidden-card');
            });

            if (manual && !isPaused) {
                startAutoRotate();
            }
        }
        
        function showNext(manual = false) {
            currentIndex = (currentIndex + 1) % cards.length;
            updateCards(manual);
        }
        
        function showPrev(manual = false) {
            currentIndex = (currentIndex - 1 + cards.length) % cards.length;
            updateCards(manual);
        }

        function startAutoRotate() {
            clearInterval(autoSlide);
            autoSlide = setInterval(() => showNext(false), 4000);
            isPaused = false;
            if(pausePlayBtn) pausePlayBtn.innerHTML = '<i class="fas fa-pause"></i>';
        }

        function pauseAutoRotate() {
            clearInterval(autoSlide);
            isPaused = true;
            if(pausePlayBtn) pausePlayBtn.innerHTML = '<i class="fas fa-play"></i>';
        }

        nextBtn.addEventListener('click', () => showNext(true));
        prevBtn.addEventListener('click', () => showPrev(true));
        pausePlayBtn.addEventListener('click', () => {
            if(isPaused) startAutoRotate();
            else pauseAutoRotate();
        });

        // Mobile Swipe
        let touchStartX = 0;
        cardStack.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            clearInterval(autoSlide);
        }, { passive: true });
        cardStack.addEventListener('touchend', (e) => {
            const touchEndX = e.changedTouches[0].clientX;
            if(touchStartX - touchEndX > 50) showNext(true);
            if(touchEndX - touchStartX > 50) showPrev(true);
            if(!isPaused) startAutoRotate();
        });

        updateCards();
        startAutoRotate();
    }
});
</script>



