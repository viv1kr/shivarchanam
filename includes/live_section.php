<?php
// This file is included from index.php, so the $conn variable is available.
$stream_result = $conn->query("SELECT * FROM live_stream WHERE id = 1 AND is_live = 1");
$live_stream = ($stream_result && $stream_result->num_rows > 0) ? $stream_result->fetch_assoc() : null;

function getYoutubeVideoId($url) {
    parse_str(parse_url($url, PHP_URL_QUERY), $vars);
    return isset($vars['v']) ? $vars['v'] : null;
}
?>

<?php if ($live_stream): 
    $video_id = getYoutubeVideoId($live_stream['youtube_url']);
    if ($video_id):
?>
<style>
    .live-section-v2 {
        padding: 6rem 2rem;
        background: linear-gradient(160deg, #120e2c 0%, #3a1a2e 100%);
        position: relative;
        overflow: hidden;
    }
    #stars, #twinkling {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        width: 100%; height: 100%; display: block;
    }
    #stars {
        background: transparent url('https://www.script-tutorials.com/demos/360/images/stars.png') repeat top center;
        z-index: 1;
    }
    #twinkling {
        background: transparent url('https://www.script-tutorials.com/demos/360/images/twinkling.png') repeat top center;
        z-index: 2;
        animation: move-twink-back 200s linear infinite;
    }
    @keyframes move-twink-back {
        from {background-position:0 0;}
        to {background-position:-10000px 5000px;}
    }

    .live-container-v2 {
        max-width: 900px;
        margin: 0 auto;
        text-align: center;
        position: relative;
        z-index: 3;
    }
    .section-heading-live-v2 {
        font-size: 2.8rem;
        font-weight: bold;
        color: #FFC107; /* Gold color */
        margin-bottom: 1rem;
    }
    .live-intro-v2 {
        color: #d1d1d1;
        font-size: 1.1rem;
        margin-bottom: 3rem;
    }
    .video-player-wrapper-v2 {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 0 40px rgba(255, 100, 0, 0.3);
        border: 2px solid rgba(245, 142, 88, 0.3);
    }
    #player {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .unmute-overlay-v2 {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        background: rgba(0,0,0,0.5);
        z-index: 10;
        cursor: pointer;
        opacity: 1;
        transition: opacity 0.4s ease;
    }
    .unmute-overlay-v2.hidden {
        opacity: 0;
        pointer-events: none;
    }
    .unmute-button-v2 {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 1rem 2rem;
        border-radius: 50px;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .live-indicator-v2 {
        position: absolute; top: 1rem; left: 1rem;
        background: #9F0102; color: white; padding: 0.4rem 0.8rem;
        border-radius: 25px; font-size: 0.8rem; font-weight: bold;
        display: flex; align-items: center; gap: 0.5rem; z-index: 11;
    }
    .blinking-dot-live {
        width: 8px; height: 8px; background: white;
        border-radius: 50%; animation: blink 1.5s infinite;
    }
    @keyframes blink { 50% { opacity: 0.5; } }
    
    .video-controls {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 11;
    }
    #fullscreen-btn {
        background: rgba(0,0,0,0.5); border: none; color: white;
        width: 40px; height: 40px; border-radius: 50%; cursor: pointer;
    }

    .animated-line-v2 {
        height: 2px;
        background: linear-gradient(to right, transparent, #FF6D01, transparent);
        margin: 2.5rem auto;
        width: 80%;
        opacity: 0.5;
    }

    .virtual-actions-v2 {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    .virtual-action-btn-v2 {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(245, 142, 88, 0.3);
        color: #FFC107;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .virtual-action-btn-v2:hover {
        background: #FF6D01;
        color: white;
    }
    .virtual-action-btn-v2.donate-btn {
        color: #28a745;
        border-color: #28a745;
    }
     .virtual-action-btn-v2.donate-btn:hover {
        background: #28a745;
        color: white;
    }
    .virtual-action-btn-v2 span {
        font-size: 0.7rem;
        margin-top: 5px;
    }

    /* Animation Effects Container */
    .animation-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; overflow: hidden; z-index: 20; }
    .petal { position: absolute; background: #FF6D01; border-radius: 50% 0; width: 15px; height: 20px; animation: petal-fall 5s linear forwards; }
    @keyframes petal-fall { to { transform: translateY(100vh) rotate(360deg); opacity: 0; } }
    .aarti-flame { position: absolute; width: 50px; height: 70px; background: radial-gradient(circle, #ffd700, #ff8c00, transparent 70%); border-radius: 50% 50% 20% 20%; filter: blur(5px); animation: aarti-move 3s ease-in-out forwards, flicker 0.1s infinite alternate; }
    @keyframes aarti-move { 0% { bottom: 10%; left: 10%; opacity: 1; } 25% { bottom: 50%; left: 80%; } 50% { bottom: 80%; left: 50%; } 75% { bottom: 50%; left: 20%; } 100% { bottom: 10%; left: 10%; opacity: 0; } }
    @keyframes flicker { to { transform: scale(1.1); } }
    .namaskar-hands { position: absolute; top: 50%; left: 50%; font-size: 5rem; color: #FFC107; opacity: 0; transform: translate(-50%, -50%) scale(0.5); animation: namaskar-fade 2s ease-out forwards; }
    @keyframes namaskar-fade { 0%, 100% { opacity: 0; transform: translate(-50%, -50%) scale(0.5); } 50% { opacity: 0.8; transform: translate(-50%, -50%) scale(1); } }
    .smoke-plume { position: absolute; bottom: 0; left: 20%; width: 5px; height: 5px; background: rgba(255, 255, 255, 0.3); border-radius: 50%; animation: smoke-rise 4s ease-out forwards; }
    @keyframes smoke-rise { to { transform: translateY(-200px) translateX(50px) scale(8); opacity: 0; } }

    @media (max-width: 768px) {
        .virtual-action-btn-v2 { width: 60px; height: 60px; font-size: 1.2rem; }
        .virtual-action-btn-v2 span { font-size: 0.6rem; }
    }
</style>

<section class="live-section-v2">
    <div id="stars"></div>
    <div id="twinkling"></div>
    <div class="live-container-v2">
        <h2 class="section-heading-live-v2">Live Darshan</h2>
        <p class="live-intro-v2">Experience the divine atmosphere of Shivarchanam Temple, live from anywhere in the world.</p>
        
        <div class="video-player-wrapper-v2" id="video-player-wrapper">
            <div class="live-indicator-v2">
                <span class="blinking-dot-live"></span> LIVE
            </div>
            <div class="video-controls">
                 <button id="fullscreen-btn" title="Full Screen"><i class="fas fa-expand"></i></button>
            </div>
            <div id="player"></div> <!-- YouTube Player will be inserted here -->
            <div class="animation-container" id="animation-container"></div>
            <div class="unmute-overlay-v2" id="unmute-overlay">
                <button class="unmute-button-v2">
                    <i class="fas fa-volume-mute"></i> <span>Click to Unmute</span>
                </button>
            </div>
        </div>
        
        <div class="animated-line-v2"></div>
        
        <div class="virtual-actions-v2">
            <button class="virtual-action-btn-v2" id="aarti-btn" title="Virtual Aarti">
                <i class="fas fa-fire"></i>
                <span>Aarti</span>
            </button>
             <button class="virtual-action-btn-v2" id="flower-btn" title="Virtual Flower Offering">
                <i class="fas fa-fan"></i>
                <span>Flower</span>
            </button>
             <button class="virtual-action-btn-v2" id="namaskar-btn" title="Virtual Namaskar">
                <i class="fas fa-praying-hands"></i>
                <span>Namaskar</span>
            </button>
            <button class="virtual-action-btn-v2" id="incense-btn" title="Virtual Incense">
                <i class="fas fa-stream"></i>
                <span>Incense</span>
            </button>
            <a href="#" class="virtual-action-btn-v2 donate-btn" title="Live Donation">
                <i class="fas fa-donate"></i>
                <span>Donate</span>
            </a>
        </div>
    </div>
</section>

<!-- YouTube Iframe API -->
<script src="https://www.youtube.com/iframe_api"></script>
<script>
    let player;
    function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
            videoId: '<?php echo $video_id; ?>',
            playerVars: {
                'autoplay': 1, 'controls': 0, 'mute': 1, 'loop': 1,
                'playlist': '<?php echo $video_id; ?>', 'rel': 0, 'showinfo': 0,
                'iv_load_policy': 3, 'modestbranding': 1
            },
            events: { 'onReady': onPlayerReady }
        });
    }

    function onPlayerReady(event) {
        event.target.playVideo();
        const unmuteOverlay = document.getElementById('unmute-overlay');
        const unmuteButton = unmuteOverlay.querySelector('.unmute-button-v2');

        unmuteOverlay.addEventListener('click', () => {
            if (player.isMuted()) {
                player.unMute();
                unmuteButton.innerHTML = '<i class="fas fa-volume-up"></i> <span>Sound On</span>';
            }
            unmuteOverlay.classList.add('hidden');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const animationContainer = document.getElementById('animation-container');
        const flowerBtn = document.getElementById('flower-btn');
        const aartiBtn = document.getElementById('aarti-btn');
        const namaskarBtn = document.getElementById('namaskar-btn');
        const incenseBtn = document.getElementById('incense-btn');
        const fullscreenBtn = document.getElementById('fullscreen-btn');
        const videoWrapper = document.getElementById('video-player-wrapper');

        // Flower Animation
        if(flowerBtn) {
            flowerBtn.addEventListener('click', () => {
                for(let i=0; i<15; i++) {
                    const petal = document.createElement('div');
                    petal.classList.add('petal');
                    petal.style.left = Math.random() * 100 + '%';
                    petal.style.animationDuration = Math.random() * 2 + 3 + 's';
                    petal.style.animationDelay = Math.random() * 2 + 's';
                    animationContainer.appendChild(petal);
                    setTimeout(() => petal.remove(), 5000);
                }
            });
        }
        
        // Aarti Animation
        if(aartiBtn) {
            aartiBtn.addEventListener('click', () => {
                const flame = document.createElement('div');
                flame.classList.add('aarti-flame');
                animationContainer.appendChild(flame);
                setTimeout(() => flame.remove(), 3000);
            });
        }

        // Namaskar Animation
        if(namaskarBtn) {
            namaskarBtn.addEventListener('click', () => {
                const hands = document.createElement('div');
                hands.classList.add('namaskar-hands');
                hands.innerHTML = '<i class="fas fa-praying-hands"></i>';
                animationContainer.appendChild(hands);
                setTimeout(() => hands.remove(), 2000);
            });
        }

        // Incense Animation
        if(incenseBtn) {
            incenseBtn.addEventListener('click', () => {
                 for(let i=0; i<10; i++) {
                    const smoke = document.createElement('div');
                    smoke.classList.add('smoke-plume');
                    smoke.style.left = (20 + Math.random() * 10) + '%';
                    smoke.style.animationDelay = (i * 0.3) + 's';
                    animationContainer.appendChild(smoke);
                    setTimeout(() => smoke.remove(), 4000);
                }
            });
        }

        if(fullscreenBtn && videoWrapper) {
            fullscreenBtn.addEventListener('click', () => {
                if (videoWrapper.requestFullscreen) {
                    videoWrapper.requestFullscreen();
                } else if (videoWrapper.webkitRequestFullscreen) { /* Safari */
                    videoWrapper.webkitRequestFullscreen();
                }
            });
        }
    });

</script>
<?php endif; endif; ?>

