<?php
// This file is included from index.php, so the $conn variable should already be available.
// We fetch the active news ticker items from the database.
$ticker_items_query = "SELECT message FROM news_ticker WHERE is_active = 1 ORDER BY created_at DESC";
$ticker_result = $conn->query($ticker_items_query);
$ticker_items = [];
if ($ticker_result && $ticker_result->num_rows > 0) {
    while($row = $ticker_result->fetch_assoc()) {
        $ticker_items[] = $row['message'];
    }
}
?>
<style>
    /* --- THEME COLORS (Included for this section) --- */
    :root {
        --primary-red: #9F0102;
        --accent-orange: #FF6D01;
        --dark-text: #333333;
        --astro-bg-dark: #1D1D1B;
        --white: #ffffff;
    }

    /* --- NEWS TICKER STYLES --- */
    .news-ticker-wrapper-v2 {
        background-color: var(--astro-bg-dark);
        color: var(--white);
        padding: 0.75rem 0;
        overflow: hidden;
        position: relative;
        border-top: 1px solid #444;
        border-bottom: 1px solid #444;
        display: flex;
        align-items: center;
    }
    
    .ticker-container-v2 {
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 1600px;
        margin: 0 auto;
        padding: 0 2rem;
    }
    
    .ticker-label-v2 {
        background-color: var(--accent-orange);
        color: var(--astro-bg-dark);
        padding: 0.6rem 1.2rem;
        font-weight: bold;
        border-radius: 8px;
        margin-right: 1.5rem;
        flex-shrink: 0;
        z-index: 2;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
    }

    .ticker-mask {
        flex-grow: 1;
        overflow: hidden;
    }

    .ticker-track-v2 {
        display: flex;
        animation: scroll-ticker-v2 40s linear infinite;
        white-space: nowrap;
    }
    
    .ticker-item-v2 {
        padding: 0 2.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1rem;
    }
    
    .ticker-item-v2 .fas {
        color: var(--accent-orange);
    }
    
    .news-ticker-wrapper-v2:hover .ticker-track-v2 {
        animation-play-state: paused;
    }

    @keyframes scroll-ticker-v2 {
        0% { transform: translateX(0%); }
        100% { transform: translateX(-50%); } /* Scrolls half the width due to duplicated content */
    }

    @keyframes blink-animation {
        50% {
            opacity: 0.5;
            box-shadow: 0 0 8px var(--accent-orange);
        }
    }
    
    @media (max-width: 768px) {
        .ticker-container-v2 {
            padding: 0 1rem;
        }
        .ticker-label-v2 {
            font-size: 0; /* Hide the text */
            width: 15px;   /* Make it a circle */
            height: 15px;
            padding: 0;
            border-radius: 50%;
            margin-right: 1rem;
            animation: blink-animation 1.5s infinite; /* Add blinking animation */
        }
        .ticker-item-v2 {
            padding: 0 1.5rem;
            font-size: 0.9rem;
        }
        .ticker-track-v2 {
            animation-duration: 25s; /* Increase speed on mobile */
        }
    }
</style>

<section class="news-ticker-wrapper-v2">
    <div class="ticker-container-v2">
        <div class="ticker-label-v2">Live Updates</div>
        <div class="ticker-mask">
            <div class="ticker-track-v2">
                <?php if (!empty($ticker_items)): ?>
                    <?php foreach ($ticker_items as $item): ?>
                        <div class="ticker-item-v2"><i class="fas fa-om"></i> <?php echo htmlspecialchars($item); ?></div>
                    <?php endforeach; ?>
                    <!-- Duplicate items for a seamless scrolling loop -->
                    <?php foreach ($ticker_items as $item): ?>
                        <div class="ticker-item-v2"><i class="fas fa-om"></i> <?php echo htmlspecialchars($item); ?></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="ticker-item-v2">Welcome to Shivarchanam. Stay tuned for the latest updates.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

