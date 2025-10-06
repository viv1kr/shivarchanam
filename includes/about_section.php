<style>
    /* --- THEME COLORS --- */
    :root {
        --primary-red: #9F0102;
        --accent-orange: #FF6D01;
        --light-bg: #FFEFDA;
        --dark-text: #333333;
        --white: #ffffff;
        --gray: #f9f9f9;
    }

    /* --- ABOUT SECTION V3 STYLES --- */
    .about-temple-section-v3 {
        padding: 5rem 2rem;
        background-color: var(--gray);
        background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/om.png');
        overflow: hidden;
    }
    .about-temple-container-v3 {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
        gap: 4rem;
    }
    .about-temple-image-col-v3 {
        position: relative;
        text-align: center;
    }
    .about-temple-image-frame {
        background: linear-gradient(var(--primary-red), var(--accent-orange));
        border-radius: 20px;
        padding: 10px;
        display: inline-block;
        box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    }
    .about-temple-image-v3 {
        width: 100%;
        max-width: 400px;
        display: block;
        border-radius: 15px;
        border: 4px solid var(--white);
    }

    .about-temple-content-col-v3 {
        text-align: left;
        opacity: 0;
        transform: translateY(30px);
        animation: fadeInUpAbout 0.8s ease forwards;
    }
    @keyframes fadeInUpAbout {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .about-temple-content-col-v3 .sub-heading {
        color: var(--accent-orange);
        font-weight: bold;
        margin-bottom: 0.5rem;
        display: block;
    }
    .about-temple-content-col-v3 .section-heading {
        font-size: 2.8rem;
        color: var(--primary-red);
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }
    .about-description-scrollable {
        color: #555;
        line-height: 1.8;
        margin-bottom: 2rem;
        max-height: 200px; /* Increased height */
        overflow-y: auto;
        padding-right: 15px;
    }
    /* Custom Scrollbar */
    .about-description-scrollable::-webkit-scrollbar { width: 8px; }
    .about-description-scrollable::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .about-description-scrollable::-webkit-scrollbar-thumb { background: var(--accent-orange); border-radius: 10px; }
    .about-description-scrollable::-webkit-scrollbar-thumb:hover { background: var(--primary-red); }

    .btn-read-more {
        background: var(--primary-red);
        color: var(--white);
        padding: 0.8rem 2rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }
    .btn-read-more:hover {
        background: var(--accent-orange);
    }

    @media (max-width: 900px) {
        .about-temple-container-v3 {
            grid-template-columns: 1fr;
            gap: 3rem;
        }
        .about-temple-content-col-v3 {
            text-align: center;
        }
    }
</style>

<section class="about-temple-section-v3">
    <div class="about-temple-container-v3">
        <div class="about-temple-image-col-v3">
            <div class="about-temple-image-frame">
                <img src="https://picsum.photos/id/1047/600/700" alt="Shivarchanam Temple" class="about-temple-image-v3">
            </div>
        </div>
        <div class="about-temple-content-col-v3">
            <span class="sub-heading">About Our Temple</span>
            <h2 class="section-heading">A Sacred Space for Devotion & Community</h2>
            <div class="about-description-scrollable">
                <p>
                    Shivarchanam Temple is a sanctuary of peace and spirituality, founded on the principles of Vedic traditions. Our mission is to provide a sacred space for devotees to connect with the divine, foster community spirit, and preserve our rich cultural heritage for future generations. We welcome all who seek solace and a deeper understanding of Dharma.
                    <br><br>
                    Throughout the year, we host a variety of festivals and cultural events that bring our community together in celebration. From the vibrant colors of Holi to the luminous glow of Diwali, each festival is an opportunity to experience our traditions and create lasting memories. Our doors are always open to those who wish to participate in daily aartis, special pujas, and spiritual discourses led by our esteemed priest.
                </p>
            </div>
            <a href="about_us.php" class="btn-read-more">Read More</a>
        </div>
    </div>
</section>

