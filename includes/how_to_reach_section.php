<style>
    /* --- THEME COLORS --- */
    :root {
        --primary-red: #9F0102;
        --accent-orange: #FF6D01;
        --light-bg: #FFEFDA;
        --dark-text: #333333;
        --white: #ffffff;
    }

    /* --- HOW TO REACH SECTION V8 STYLES --- */
    .how-to-reach-section-v8 {
        padding: 5rem 2rem;
        background-color: var(--light-bg);
        background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/om.png');
        position: relative;
        overflow: hidden;
    }

    .how-to-reach-container-v8 {
        max-width: 1100px;
        margin: 0 auto;
        text-align: center;
        position: relative;
        z-index: 2;
    }
    .section-heading-v8 {
        font-size: 2.8rem;
        font-weight: bold;
        color: var(--primary-red);
        margin-bottom: 4rem;
        display: inline-flex;
        align-items: center;
        gap: 1rem;
    }
    .section-heading-v8 .fas {
        font-size: 2.5rem;
        color: var(--accent-orange);
    }

    .reach-layout-v8 {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 4rem;
        align-items: center;
        text-align: left;
    }

    .map-col-wrapper-v8 {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        background: var(--white);
        padding: 10px;
        border: 1px solid transparent;
        background-clip: padding-box;
        position: relative;
    }
    .map-col-wrapper-v8::before {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        z-index: -1;
        margin: -2px; /* Border width */
        border-radius: inherit;
        background: linear-gradient(to right, var(--primary-red), var(--accent-orange));
    }

    .map-col-wrapper-v8 iframe {
        display: block;
        width: 100%;
        height: 350px;
        border: none;
        border-radius: 15px;
    }
    .map-info-v8 {
        padding: 1.5rem 0.5rem 0.5rem 0.5rem;
    }
    .map-info-v8 h4 {
        font-size: 1.3rem;
        margin-bottom: 0.5rem;
        color: var(--dark-text);
    }
    .map-info-v8 p {
        color: #555;
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }
    .directions-buttons-v8 {
        display: flex;
        gap: 1rem;
    }
    .btn-directions-v8 {
        flex: 1;
        background: var(--primary-red);
        color: var(--white);
        padding: 0.8rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        text-align: center;
        transition: background-color 0.3s;
    }
     .btn-directions-v8.bike {
        background-color: var(--accent-orange);
    }
    .btn-directions-v8:hover {
        opacity: 0.9;
    }

    .steps-timeline-v8 {
        position: relative;
        padding-left: 40px;
    }
    .timeline-line-v8 {
        position: absolute;
        left: 18px;
        top: 20px;
        bottom: 20px;
        width: 4px;
        background: #ddd;
        border-radius: 2px;
    }
    .timeline-line-progress-v8 {
        width: 100%;
        height: 0%;
        background: var(--accent-orange);
        transition: height 1s ease-out;
    }
    .steps-timeline-v8.is-visible .timeline-line-progress-v8 {
        height: 100%;
    }

    .step-item-v8 {
        position: relative;
        margin-bottom: 3.5rem;
        opacity: 0;
        transform: translateX(20px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .step-item-v8.is-visible {
        opacity: 1;
        transform: translateX(0);
    }
    .step-item-v8:nth-child(2) { transition-delay: 0.2s; }
    .step-item-v8:nth-child(3) { transition-delay: 0.4s; }

    .step-item-v8:last-child {
        margin-bottom: 0;
    }
    .step-icon-v8 {
        position: absolute;
        left: -40px;
        top: 0;
        background: var(--white);
        color: var(--primary-red);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        border: 3px solid var(--accent-orange);
        box-shadow: 0 0 0 4px var(--light-bg);
    }

    .step-item-v8.animate-on-scroll-reach.is-visible {
    padding-left: 20px;
    }
    .step-item-v8 h4 {
        font-size: 1.4rem;
        color: var(--dark-text);
        margin-bottom: 0.5rem;
    }
    .step-item-v8 p {
        color: #555;
        line-height: 1.7;
    }
    .step-item-v8 .distance {
        font-weight: bold;
        color: var(--primary-red);
    }

    @media (max-width: 900px) {
        .reach-layout-v8 {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="how-to-reach-section-v8">
    <div class="how-to-reach-container-v8">
        <h2 class="section-heading-v8"><i class="fas fa-map-signs"></i> How to Reach Us</h2>
        <div class="reach-layout-v8">
            <div class="steps-col">
                <div class="steps-timeline-v8" id="steps-timeline">
                    <div class="timeline-line-v8"><div class="timeline-line-progress-v8"></div></div>
                    <div class="step-item-v8 animate-on-scroll-reach">
                        <div class="step-icon-v8"><i class="fas fa-train"></i></div>
                        <h4>From Surat Railway Station</h4>
                        <p>Exit from the main entrance of the railway station. You will find plenty of auto-rickshaws and taxis available.</p>
                    </div>
                    <div class="step-item-v8 animate-on-scroll-reach">
                        <div class="step-icon-v8"><i class="fas fa-taxi"></i></div>
                        <h4>Hire an Auto-rickshaw or Taxi</h4>
                        <p>Ask the driver to take you to Ram Chowk in Athwalines. The journey is approx. <span class="distance">20-25 minutes</span>.</p>
                    </div>
                    <div class="step-item-v8 animate-on-scroll-reach">
                        <div class="step-icon-v8"><i class="fas fa-gopuram"></i></div>
                        <h4>Arrival at the Temple</h4>
                        <p>You will arrive at Vighneshwar Mahadev Temple, easily visible from the main road.</p>
                    </div>
                </div>
            </div>
            <div class="map-col">
                <div class="map-col-wrapper-v8">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3720.648999863813!2d72.8183983154019!3d21.16644298592471!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be04e5b5a755555%3A0x4259d3403a5c202b!2sVighneshwar%20Mahadev%20Temple!5e0!3m2!1sen!2sin!4v1665000000000!5m2!1sen!2sin" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div class="map-info-v8">
                        <h4>Vighneshwar Mahadev Temple</h4>
                        <p>Ram Chowk, Athwalines, Athwa, Surat, Gujarat 395001</p>
                        <div class="directions-buttons-v8">
                            <a href="https://maps.app.goo.gl/e7eyjnrBSbHE1WZd8" target="_blank" class="btn-directions-v8"><i class="fas fa-car"></i> Car</a>
                            <a href="https://maps.app.goo.gl/e7eyjnrBSbHE1WZd8" target="_blank" class="btn-directions-v8 bike"><i class="fas fa-motorcycle"></i> Bike</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const animatedItems = document.querySelectorAll('.animate-on-scroll-reach');
    const timeline = document.getElementById('steps-timeline');

    if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                }
            });
        }, { threshold: 0.5 });

        animatedItems.forEach(item => {
            observer.observe(item);
        });
        if (timeline) {
            observer.observe(timeline);
        }
    }
});
</script>

