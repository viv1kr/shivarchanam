<?php
// This file is included from index.php, so the $conn variable is available.
// **FIX:** The query now orders by the new 'sort_order' column to reflect custom arrangements.
$history_query = "SELECT * FROM temple_history ORDER BY sort_order ASC";
$history_result = $conn->query($history_query);
$history_events = [];
if ($history_result && $history_result->num_rows > 0) {
    while($row = $history_result->fetch_assoc()) {
        $history_events[] = $row;
    }
}
?>
<style>
    /* --- THEME COLORS (Included for this section) --- */
    :root {
        --primary-red: #9F0102;
        --accent-orange: #FF6D01;
        --light-bg: #FFEFDA;
        --dark-text: #333333;
        --white: #ffffff;
        --gray: #f4f5f7;
        --border-color: #e0e0e0;
    }

    /* --- TEMPLE HISTORY SECTION V5 --- */
    .history-section-v5 {
        padding: 5rem 2rem;
        background-color: var(--white);
    }
    .history-container-v5 {
        max-width: 900px;
        margin: 0 auto;
        text-align: center;
    }
    .section-heading {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--dark-text);
        margin-bottom: 2rem;
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
    .history-intro-v5 p {
        max-width: 600px;
        margin: 0 auto 3rem auto;
        color: #666;
        font-size: 1.1rem;
    }
    
    .timeline-wrapper {
        position: relative;
        width: 100%;
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }
    .timeline-wrapper::before, .timeline-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        height: 100%;
        width: 50px;
        z-index: 3;
        pointer-events: none;
    }
    .timeline-wrapper::before {
        left: 0;
        background: linear-gradient(to right, var(--white), transparent);
    }
    .timeline-wrapper::after {
        right: 0;
        background: linear-gradient(to left, var(--white), transparent);
    }

    .timeline-scroll-container {
        overflow-x: auto;
        -ms-overflow-style: none;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .timeline-scroll-container::-webkit-scrollbar {
        display: none;
    }

    .timeline-v5 {
        position: relative;
        display: inline-flex;
        justify-content: flex-start;
        padding: 1rem 0;
        min-width: 100%;
    }
    
    .timeline-v5::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #ddd;
        transform: translateY(-50%);
        z-index: 1;
    }

    .timeline-event-v5 {
        position: relative;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        z-index: 2;
        padding: 0 2.5rem;
        flex-shrink: 0;
    }
    
    .event-year-v5 {
        font-weight: bold;
        color: #888;
        transition: color 0.3s;
    }
    .event-dot {
        width: 20px;
        height: 20px;
        background-color: #ddd;
        border-radius: 50%;
        transition: all 0.3s;
        border: 4px solid var(--white);
    }
    .timeline-event-v5.active .event-year-v5 {
        color: var(--primary-red);
    }
    .timeline-event-v5.active .event-dot {
        background-color: var(--accent-orange);
        transform: scale(1.2);
    }
    
    .timeline-content-v5 {
        min-height: 100px;
    }
    .content-item-v5 {
        display: none;
        animation: fadeIn 0.5s;
    }
    .content-item-v5.active {
        display: block;
    }
    .content-item-v5 h3 {
        font-size: 1.8rem;
        margin-bottom: 1rem;
        color: var(--primary-red);
    }
    .content-item-v5 p {
        color: #555;
        line-height: 1.7;
    }

    .photo-gallery-container-v5 {
        margin-top: 3rem;
        overflow: hidden;
        position: relative;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        min-height: 220px;
        background: var(--gray);
    }
    .photo-scroll-v5 {
        display: flex;
        gap: 1rem;
        width: fit-content;
        animation: scroll-gallery-v5 30s linear infinite;
    }
    .photo-scroll-v5 img {
        height: 220px;
        border-radius: 10px;
    }
    .photo-gallery-container-v5:hover .photo-scroll-v5 {
        animation-play-state: paused;
    }
    .no-photos-message {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 220px;
        color: #999;
    }

    @keyframes scroll-gallery-v5 {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<section class="history-section-v5">
    <div class="history-container-v5">
        <div class="history-intro-v5">
            <h2 class="section-heading">Our Sacred Journey</h2>
            <p>From a humble beginning to a spiritual landmark, discover the milestones that have shaped Shivarchanam Temple.</p>
        </div>

        <?php if (!empty($history_events)): ?>
        <div class="timeline-wrapper">
            <div class="timeline-scroll-container">
                <div class="timeline-v5">
                    <?php foreach ($history_events as $index => $event): 
                        $images_json = htmlspecialchars($event['image_url'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="timeline-event-v5 <?php echo $index === 0 ? 'active' : ''; ?>" data-event-id="<?php echo $event['id']; ?>" data-images='<?php echo $images_json; ?>'>
                        <div class="event-year-v5"><?php echo $event['year']; ?></div>
                        <div class="event-dot"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="timeline-content-v5">
            <?php foreach ($history_events as $index => $event): ?>
                <div class="content-item-v5 <?php echo $index === 0 ? 'active' : ''; ?>" data-content-id="<?php echo $event['id']; ?>">
                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                    <p><?php echo htmlspecialchars($event['description']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="photo-gallery-container-v5" id="photo-gallery-container">
            <div class="photo-scroll-v5" id="photo-scroll">
                <!-- Images will be dynamically inserted here by JavaScript -->
            </div>
        </div>

        <?php else: ?>
            <p>The temple's history will be updated soon. Please check back later.</p>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const historyContainer = document.querySelector('.history-section-v5');
    if (historyContainer) {
        const timelineEvents = historyContainer.querySelectorAll('.timeline-event-v5');
        const contentItems = historyContainer.querySelectorAll('.content-item-v5');
        const photoScrollContainer = historyContainer.querySelector('#photo-scroll');

        const updateGallery = (imageUrlsJson) => {
            photoScrollContainer.innerHTML = '';
            photoScrollContainer.style.animation = 'none';
            try {
                const imageUrls = JSON.parse(imageUrlsJson);
                if (imageUrls && imageUrls.length > 0) {
                    imageUrls.forEach(url => {
                        const img = document.createElement('img');
                        img.src = `admin/${url}`;
                        img.alt = "Temple history photo";
                        photoScrollContainer.appendChild(img);
                    });
                    if (imageUrls.length > 3) { 
                         imageUrls.forEach(url => {
                            const img = document.createElement('img');
                            img.src = `admin/${url}`;
                            img.alt = "Temple history photo";
                            photoScrollContainer.appendChild(img);
                        });
                    }
                    setTimeout(() => {
                        void photoScrollContainer.offsetWidth; 
                        photoScrollContainer.style.animation = 'scroll-gallery-v5 30s linear infinite';
                    }, 50);
                } else {
                    photoScrollContainer.innerHTML = '<div class="no-photos-message"><p>No photos available for this year.</p></div>';
                }
            } catch (e) {
                photoScrollContainer.innerHTML = '<div class="no-photos-message"><p>No photos available for this year.</p></div>';
            }
        };
        
        const activateEvent = (eventElement) => {
            const eventId = eventElement.dataset.eventId;
            
            timelineEvents.forEach(item => item.classList.remove('active'));
            eventElement.classList.add('active');

            contentItems.forEach(content => {
                content.classList.remove('active');
                if (content.dataset.contentId === eventId) {
                    content.classList.add('active');
                }
            });
            
            updateGallery(eventElement.dataset.images);
        };

        timelineEvents.forEach(event => {
            event.addEventListener('click', () => {
                activateEvent(event);
            });
        });
        
        const firstEvent = historyContainer.querySelector('.timeline-event-v5');
        if(firstEvent){
            activateEvent(firstEvent);
        }
    }
});
</script>

