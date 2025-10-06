<style>
    /* --- THEME COLORS --- */
    :root {
        --primary-red: #9F0102;
        --accent-orange: #FF6D01;
        --dark-text: #333333;
        --white: #ffffff;
        --gray: #f4f5f7;
    }

    /* --- TESTIMONIAL SECTION V3 --- */
    .testimonial-section-v3 {
        padding: 6rem 2rem;
        background-color: var(--gray);
    }
    .testimonial-container-v3 {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 4rem;
        align-items: center;
    }
    .testimonial-intro-v3 {
        text-align: left;
    }
    .section-heading-v3 { 
        font-size: 2.8rem;
        font-weight: bold;
        color: var(--dark-text);
        margin-bottom: 1.5rem; 
        line-height: 1.2;
    }
     .section-heading-v3 span {
        color: var(--accent-orange);
     }
    .testimonial-intro-v3 p {
        font-size: 1.1rem;
        color: #555;
        line-height: 1.7;
    }

    /* --- SCROLLING TESTIMONIALS --- */
    .testimonial-scroll-wrapper {
        height: 500px; /* Fixed height for the scrolling viewport */
        overflow: hidden;
        position: relative;
    }
    /* Feather effect for top and bottom */
    .testimonial-scroll-wrapper::before,
    .testimonial-scroll-wrapper::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        height: 100px; /* Height of the fade */
        z-index: 2;
        pointer-events: none;
    }
    .testimonial-scroll-wrapper::before {
        top: 0;
        background: linear-gradient(to bottom, var(--gray), transparent);
    }
    .testimonial-scroll-wrapper::after {
        bottom: 0;
        background: linear-gradient(to top, var(--gray), transparent);
    }

    .testimonial-scroll-track {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        animation: scroll-up 30s linear infinite;
    }
    .testimonial-scroll-wrapper:hover .testimonial-scroll-track {
        animation-play-state: paused;
    }

    @keyframes scroll-up {
        0% {
            transform: translateY(0);
        }
        100% {
            transform: translateY(-50%); /* Scroll by half the height because content is duplicated */
        }
    }

    .testimonial-card-v3 {
        background: var(--white);
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        text-align: left;
    }
    
    .card-header-v3 {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .testimonial-author-v3 {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .author-photo-v3 {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    .author-details-v3 strong {
        display: block;
        color: var(--primary-red);
    }
    .star-rating-v3 {
        color: #ffc107;
    }
    .testimonial-quote-icon-v3 {
        font-size: 2.5rem;
        color: var(--accent-orange);
        opacity: 0.2;
    }
    .testimonial-text-v3 {
        font-size: 1.1rem;
        color: #555;
        line-height: 1.7;
        font-style: italic;
    }
    
    @media(max-width: 900px) {
        .testimonial-container-v3 {
            grid-template-columns: 1fr;
            text-align: center;
        }
        .testimonial-intro-v3 {
            margin-bottom: 3rem;
        }
    }
</style>

<section class="testimonial-section-v3">
    <div class="testimonial-container-v3">
        <div class="testimonial-intro-v3">
            <h2 class="section-heading-v3">Words from Our <span>Devotees</span></h2>
            <p>Discover how our sacred services and spiritual guidance have brought peace, clarity, and positive transformation into the lives of our community members.</p>
        </div>
        <div class="testimonial-scroll-wrapper">
            <div class="testimonial-scroll-track">
                <!-- Testimonials are duplicated for a seamless loop -->
                <div class="testimonial-card-v3">
                    <div class="card-header-v3">
                        <div class="testimonial-author-v3">
                            <img src="https://picsum.photos/id/237/100" alt="Devotee" class="author-photo-v3">
                            <div class="author-details-v3">
                                <strong>Priya Patel</strong>
                                <div class="star-rating-v3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            </div>
                        </div>
                        <i class="fas fa-quote-right testimonial-quote-icon-v3"></i>
                    </div>
                    <p class="testimonial-text-v3">The guidance I received from Aacharya Vyas was life-changing. His insights brought so much clarity and peace to my life. Truly blessed to have found this temple.</p>
                </div>
                 <div class="testimonial-card-v3">
                    <div class="card-header-v3">
                        <div class="testimonial-author-v3">
                            <img src="https://picsum.photos/id/238/100" alt="Devotee" class="author-photo-v3">
                            <div class="author-details-v3">
                                <strong>Rohan Sharma</strong>
                                 <div class="star-rating-v3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            </div>
                        </div>
                         <i class="fas fa-quote-right testimonial-quote-icon-v3"></i>
                    </div>
                    <p class="testimonial-text-v3">Participating in the Hawan ceremony was a deeply spiritual experience. The atmosphere was pure and divine. I am grateful for the services provided here.</p>
                </div>
                 <div class="testimonial-card-v3">
                    <div class="card-header-v3">
                        <div class="testimonial-author-v3">
                            <img src="https://picsum.photos/id/239/100" alt="Devotee" class="author-photo-v3">
                            <div class="author-details-v3">
                                <strong>Anjali Mehta</strong>
                                 <div class="star-rating-v3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            </div>
                        </div>
                         <i class="fas fa-quote-right testimonial-quote-icon-v3"></i>
                    </div>
                    <p class="testimonial-text-v3">The Vastu consultation for my new home was incredibly helpful. Aacharya Vyas's advice has made our home feel so much more positive and harmonious.</p>
                </div>
                <!-- Duplicate Set -->
                <div class="testimonial-card-v3">
                    <div class="card-header-v3">
                        <div class="testimonial-author-v3">
                            <img src="https://picsum.photos/id/237/100" alt="Devotee" class="author-photo-v3">
                            <div class="author-details-v3">
                                <strong>Priya Patel</strong>
                                <div class="star-rating-v3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            </div>
                        </div>
                        <i class="fas fa-quote-right testimonial-quote-icon-v3"></i>
                    </div>
                    <p class="testimonial-text-v3">The guidance I received from Aacharya Vyas was life-changing. His insights brought so much clarity and peace to my life. Truly blessed to have found this temple.</p>
                </div>
                 <div class="testimonial-card-v3">
                    <div class="card-header-v3">
                        <div class="testimonial-author-v3">
                            <img src="https://picsum.photos/id/238/100" alt="Devotee" class="author-photo-v3">
                            <div class="author-details-v3">
                                <strong>Rohan Sharma</strong>
                                 <div class="star-rating-v3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            </div>
                        </div>
                         <i class="fas fa-quote-right testimonial-quote-icon-v3"></i>
                    </div>
                    <p class="testimonial-text-v3">Participating in the Hawan ceremony was a deeply spiritual experience. The atmosphere was pure and divine. I am grateful for the services provided here.</p>
                </div>
                 <div class="testimonial-card-v3">
                    <div class="card-header-v3">
                        <div class="testimonial-author-v3">
                            <img src="https://picsum.photos/id/239/100" alt="Devotee" class="author-photo-v3">
                            <div class="author-details-v3">
                                <strong>Anjali Mehta</strong>
                                 <div class="star-rating-v3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            </div>
                        </div>
                         <i class="fas fa-quote-right testimonial-quote-icon-v3"></i>
                    </div>
                    <p class="testimonial-text-v3">The Vastu consultation for my new home was incredibly helpful. Aacharya Vyas's advice has made our home feel so much more positive and harmonious.</p>
                </div>
            </div>
        </div>
    </div>
</section>

