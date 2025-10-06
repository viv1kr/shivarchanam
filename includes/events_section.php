<?php
// This file is included from index.php, so the $conn variable is available.
// Fetch upcoming events from the database
$events_result = $conn->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC");
$events = [];
if ($events_result && $events_result->num_rows > 0) {
    while($row = $events_result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>
<style>
    /* --- THEME COLORS --- */
    :root {
        --primary-red: #9F0102;
        --accent-orange: #FF6D01;
        --light-bg: #FFEFDA;
        --dark-text: #333333;
        --white: #ffffff;
        --gray: #f4f5f7;
    }

    /* --- EVENTS SECTION V4 STYLES --- */
    .events-section-v4 {
        padding: 5rem 2rem;
        background-color: var(--gray);
    }
    .events-container-v4 {
        max-width: 1200px;
        margin: 0 auto;
        text-align: center;
    }
    .section-heading { /* Reusing from other sections for consistency */
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--dark-text);
        margin-bottom: 3rem;
        position: relative;
        display: inline-block;
    }
    .section-heading::after {
        content: '';
        position: absolute;
        width: 60%;
        height: 3px;
        background: var(--accent-orange);
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
    }
    .events-grid-v4 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); /* Wider cards on desktop */
        gap: 2rem;
        text-align: left;
    }
    .event-card-v4 {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        color: var(--white);
        aspect-ratio: 4 / 5;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.4s ease;
        opacity: 0;
        transform: translateY(20px);
    }
    .event-card-v4.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
    .event-card-v4:nth-child(2) { transition-delay: 0.1s; }
    .event-card-v4:nth-child(3) { transition-delay: 0.2s; }

    .event-card-v4:hover {
        transform: translateY(-10px);
    }
    .event-card-bg-v4 {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 1;
        transition: transform 0.4s ease;
    }
    .event-card-v4:hover .event-card-bg-v4 {
        transform: scale(1.05);
    }
    .event-card-overlay-v4 {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.5) 50%, transparent 100%);
        z-index: 2;
    }
    .event-card-content-v4 {
        position: relative;
        z-index: 3;
    }
    .event-date-box-v4 {
        background: var(--primary-red);
        color: var(--white);
        border-radius: 8px;
        padding: 0.5rem;
        width: 60px;
        text-align: center;
        margin-bottom: 1rem;
    }
    .event-day-v4 {
        font-size: 1.8rem;
        font-weight: bold;
        display: block;
        line-height: 1;
    }
    .event-month-v4 {
        font-size: 0.9rem;
        display: block;
        text-transform: uppercase;
    }
    .event-card-v4 h3 {
        font-size: 1.4rem;
        margin-bottom: 1rem;
    }
    .event-description-v4 {
        font-size: 0.9rem;
        color: #ddd;
        line-height: 1.6;
    }
    
    @media (max-width: 768px) {
        .events-section-v4 {
            padding: 4rem 0; /* Remove side padding on mobile */
        }
        .events-container-v4 {
            padding: 0; /* Remove side padding on mobile */
        }
        .events-grid-v4 {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            gap: 1rem;
            padding: 0 2rem; /* Add padding for start/end spacing */
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .events-grid-v4::-webkit-scrollbar {
            display: none;
        }
        .event-card-v4 {
            flex: 0 0 85%; /* Each card takes up 85% of screen width */
            scroll-snap-align: center;
        }
    }
</style>

<section class="events-section-v4">
    <div class="events-container-v4">
        <h2 class="section-heading">Upcoming Events</h2>

        <?php if (!empty($events)): ?>
        <div class="events-grid-v4">
            <?php foreach ($events as $event): ?>
            <div class="event-card-v4 animate-on-scroll-event">
                <img src="admin/<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-card-bg-v4">
                <div class="event-card-overlay-v4"></div>
                <div class="event-card-content-v4">
                    <?php if(!empty($event['event_date'])): ?>
                        <div class="event-date-box-v4">
                            <span class="event-day-v4"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                            <span class="event-month-v4"><?php echo date('M', strtotime($event['event_date'])); ?></span>
                        </div>
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                    <p class="event-description-v4"><?php echo htmlspecialchars($event['description']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p>No upcoming events scheduled. Please check back soon!</p>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const animatedItems = document.querySelectorAll('.animate-on-scroll-event');
    if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        animatedItems.forEach(item => {
            observer.observe(item);
        });
    }
});
</script>

