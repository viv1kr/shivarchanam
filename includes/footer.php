<style>
    /* --- THEME COLORS --- */
    :root {
        --primary-red: #9F0102;
        --accent-orange: #FF6D01;
        --light-bg: #FFEFDA;
        --dark-text: #333333;
        --white: #ffffff;
    }

    /* --- REDESIGNED FOOTER --- */
    .footer-redesigned {
        position: relative;
        padding: 5rem 2rem;
        background-color: #1a1a1a;
        color: #ccc;
        overflow: hidden;
    }
    .footer-bg-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/double-bubble-outline.png');
        opacity: 0.05;
        z-index: 1;
    }
    .footer-content {
        position: relative;
        z-index: 2;
        max-width: 1200px;
        margin: 0 auto;
    }
    .footer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Adjusted for 4 columns */
        gap: 2rem;
        margin-bottom: 3rem;
    }
    .footer-heading {
        color: var(--accent-orange);
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
    }
    .footer-heading::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 40px;
        height: 3px;
        background-color: var(--primary-red);
    }
    .footer-text {
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    .social-icons {
        display: flex;
        gap: 1rem;
    }

    
    .social-link {
        color: #ccc;
        font-size: 1.2rem;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #444;
        border-radius: 50%;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    .social-link:hover {
        color: var(--white);
        background-color: var(--accent-orange);
        border-color: var(--accent-orange);
        transform: translateY(-3px);
    }
    .footer-links, .footer-contact {
        list-style: none;
        padding: 0;
    }
    .footer-links li a {
        color: #ccc;
        text-decoration: none;
        padding: 0.5rem 0;
        display: inline-block;
        position: relative;
        transition: color 0.3s;
    }
    .footer-links li a:hover {
        color: var(--white);
    }
    .footer-contact li {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .footer-contact li .fas {
        color: var(--accent-orange);
        margin-top: 5px;
    }
    .footer-bottom {
        text-align: center;
        padding-top: 2rem;
        border-top: 1px solid #444;
    }
    .map-container-minimal {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #444;
        margin-top: 1rem;
    }
    .map-container-minimal iframe {
        display: block;
        border: none;
        height: 120px;
        width: 100%;
    }
    .btn-directions-minimal {
        display: block;
        background: var(--primary-red);
        color: var(--white);
        padding: 0.75rem;
        text-align: center;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s;
    }
    .btn-directions-minimal:hover {
        background-color: var(--accent-orange);
    }

    /* --- CHATBOT STYLES --- */
    #chatbot-toggle {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 64px;
        height: 64px;
        background: linear-gradient(45deg, var(--primary-red), var(--accent-orange));
        color: var(--white);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        z-index: 1001;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: pulse 2s infinite;
    }
    #chatbot-toggle:hover {
        transform: scale(1.1);
        animation: none;
    }
    
    #chatbot-window {
        position: fixed;
        bottom: 100px;
        right: 25px;
        width: 90%;
        max-width: 370px;
        height: 70vh;
        max-height: 550px;
        background: var(--white);
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        z-index: 1000;
        transform-origin: bottom right;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    #chatbot-window.hidden {
        transform: scale(0);
        opacity: 0;
    }

    .chatbot-header {
        background: var(--primary-red);
        color: var(--white);
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chatbot-header h3 { font-size: 1.2rem; }
    #chatbot-close {
        background: none;
        border: none;
        color: var(--white);
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    #chatbot-close:hover { opacity: 1; }

    #chat-messages {
        flex-grow: 1;
        padding: 1rem;
        overflow-y: auto;
        background-color: var(--light-bg);
        display: flex;
        flex-direction: column;
    }
    .chat-message { margin-bottom: 1rem; max-width: 85%; display: flex; flex-direction: column; }
    .chat-message.bot { align-self: flex-start; }
    .chat-message.user { align-self: flex-end; margin-left: auto; }
    .message-bubble { padding: 0.75rem 1rem; border-radius: 18px; line-height: 1.4; }
    .chat-message.bot .message-bubble { background: var(--white); border-top-left-radius: 4px; }
    .chat-message.user .message-bubble { background: var(--accent-orange); color: var(--white); border-top-right-radius: 4px; }
    
    #chat-input-container { padding: 1rem; border-top: 1px solid #ddd; background: var(--white); }
    #chat-form, .info-form { display: flex; flex-direction: column; gap: 0.5rem; }
    .form-fields { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.5rem; }
    #chat-input, .info-form input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ccc;
        border-radius: 25px;
    }
    #chat-submit-btn, .info-form button {
        background: var(--primary-red);
        color: var(--white);
        border: none;
        border-radius: 25px;
        padding: 0.75rem;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
    }
    #chat-form { flex-direction: row; align-items: center; }
    #chat-submit-btn { width: 45px; height: 45px; border-radius: 50%; padding: 0; }
    
    .quick-replies { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem; }
    .quick-reply-btn { background: var(--white); border: 1px solid var(--accent-orange); color: var(--accent-orange); padding: 0.5rem 1rem; border-radius: 25px; cursor: pointer; }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 109, 1, 0.7); }
        70% { box-shadow: 0 0 0 15px rgba(255, 109, 1, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 109, 1, 0); }
    }
</style>

<footer class="footer-redesigned">
    <div class="footer-bg-image"></div>
    <div class="footer-content">
        <div class="footer-grid">
            <div class="footer-column">
                <h3 class="footer-heading">Shivarchanam</h3>
                <p class="footer-text">Experience divinity, peace, and culture. A sacred space for spiritual growth and community connection.</p>
                <div class="social-icons">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h3 class="footer-heading">Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#events">Events</a></li>
                    <li><a href="#gallery">Gallery</a></li>
                    <li><a href="sitemap.php">Sitemap</a></li>
                </ul>
            </div>
             <div class="footer-column">
                <h3 class="footer-heading">Information</h3>
                <ul class="footer-links">
                    <li><a href="privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="refund-policy.php">Refund Policy</a></li>
                    <li><a href="terms-of-service.php">Terms of Service</a></li>
                </ul>
            </div>
             <div class="footer-column">
                <h3 class="footer-heading">Contact & Location</h3>
                <ul class="footer-contact">
                    <!-- <li><i class="fas fa-phone-alt"></i> +91 00000 00000</li>
                    <li><i class="fas fa-envelope"></i> contact@shivarchanam.org</li> -->
                    <li><i class="fas fa-map-marker-alt"></i> 123 Spiritual Road, Divine City</li>
                </ul>
                <div class="map-container-minimal">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3691.828135992928!2d72.8607236154005!3d22.69469908518201!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e5d6a7d5d4d3b%3A0x4f2b5d4b4f6b4b3b!2sNadiad%2C%2G%2G%20Gujarat%2C%20India!5e0!3m2!1sen!2sus!4v1633026193742!5m2!1sen!2sus" width="100%" height="150" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    <a href="https://maps.google.com" target="_blank" class="btn-directions-minimal">Get Directions</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> Shivarchanam Temple. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<!-- CHATBOT SECTION -->
<div id="chatbot-toggle">
    <i class="fas fa-comment-dots"></i>
</div>

<div id="chatbot-window" class="hidden">
    <div class="chatbot-header">
        <h3>Shivarchanam Assistant</h3>
        <button id="chatbot-close"><i class="fas fa-times"></i></button>
    </div>
    <div id="chat-messages"></div>
    <div id="chat-input-container"></div>
</div>

<script>
// --- CONSOLIDATED SCRIPT FOR ALL HOMEPAGE FUNCTIONALITY ---
document.addEventListener('DOMContentLoaded', function () {
    
    // 1. NAVBAR SCROLL EFFECT
    const nav = document.getElementById("navbar");
    if (nav) {
        window.addEventListener("scroll", () => {
            if (window.scrollY > 50) {
                nav.classList.remove("transparent");
                nav.classList.add("solid");
            } else {
                nav.classList.add("transparent");
                nav.classList.remove("solid");
            }
        });
    }

    // 2. MOBILE SIDE MENU (HAMBURGER)
    const hamburger = document.getElementById("hamburger-button");
    const sideMenu = document.getElementById("sideMenu");
    const overlay = document.getElementById("overlay");
    function toggleMenu() {
        if(hamburger && sideMenu && overlay) {
            hamburger.classList.toggle("is-active");
            sideMenu.classList.toggle("active");
            overlay.classList.toggle("active");
        }
    }
    if (hamburger) hamburger.addEventListener('click', toggleMenu);
    if (overlay) overlay.addEventListener('click', toggleMenu);

    // 3. HERO SLIDER
    const slides = document.querySelectorAll(".slide");
    const dotsContainer = document.getElementById("dots");
    const nextBtn = document.getElementById('next-slide');
    const prevBtn = document.getElementById('prev-slide');
    let currentSlide = 0;
    let slideInterval;
    if (slides.length > 0) {
        const dots = [];
        slides.forEach((_, i) => {
            const dot = document.createElement("div");
            dot.classList.add("dot");
            if (i === 0) dot.classList.add("active");
            dot.addEventListener("click", () => { showSlide(i); resetInterval(); });
            if(dotsContainer) dotsContainer.appendChild(dot);
            dots.push(dot);
        });
        function showSlide(index) {
            slides.forEach((slide, i) => slide.classList.toggle("active", i === index));
            if(dots.length > 0) dots.forEach((dot, i) => dot.classList.toggle("active", i === index));
            currentSlide = index;
        }
        function nextSlide() { showSlide((currentSlide + 1) % slides.length); }
        function prevSlide() { showSlide((currentSlide - 1 + slides.length) % slides.length); }
        function startInterval() { slideInterval = setInterval(nextSlide, 5000); }
        function resetInterval() { clearInterval(slideInterval); startInterval(); }
        if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); resetInterval(); });
        if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); resetInterval(); });
        startInterval();
    }

    // 4. AI CHATBOT LOGIC
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatMessages = document.getElementById('chat-messages');
    const chatInputContainer = document.getElementById('chat-input-container');

    if (chatbotToggle) {
        let chatState = 'AWAITING_USER_INFO'; 
        let userInfo = {};
        const savedUserInfo = localStorage.getItem('shivarchanamChatUserInfo');
        if (savedUserInfo) {
            userInfo = JSON.parse(savedUserInfo);
            chatState = 'MAIN_MENU';
        }

        const toggleChatbot = () => {
            chatbotWindow.classList.toggle('hidden');
            if (!chatbotWindow.classList.contains('hidden')) {
                chatMessages.innerHTML = ''; 
                if (chatState === 'AWAITING_USER_INFO') {
                    renderUserInfoForm();
                } else {
                    addMessage(`Welcome back, ${userInfo.name}! How can I assist you today?`, 'bot');
                    renderChatInput();
                }
            }
        };
        chatbotToggle.addEventListener('click', toggleChatbot);
        if(chatbotClose) chatbotClose.addEventListener('click', toggleChatbot);

        const addMessage = (text, sender, isHtml = false) => {
            const messageWrapper = document.createElement('div');
            messageWrapper.className = `chat-message ${sender}`;
            const messageBubble = document.createElement('div');
            messageBubble.className = 'message-bubble';
            if (isHtml) { messageBubble.innerHTML = text; } 
            else { messageBubble.textContent = text; }
            messageWrapper.appendChild(messageBubble); 
            chatMessages.appendChild(messageWrapper);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        };
        
        const renderChatInput = () => {
            chatInputContainer.innerHTML = `
                <div class="quick-replies">
                    <button class="quick-reply-btn" data-intent="panchang">Today's Panchang</button>
                    <button class="quick-reply-btn" data-intent="horoscope">My Horoscope</button>
                    <button class="quick-reply-btn" data-intent="donation">Make a Donation</button>
                    <button class="quick-reply-btn" data-intent="whatsapp">Chat on WhatsApp</button>
                </div>
                <form id="chat-form">
                    <input type="text" id="chat-input" placeholder="Type a message..." autocomplete="off">
                    <button type="submit" id="chat-submit-btn"><i class="fas fa-paper-plane"></i></button>
                </form>`;
            document.getElementById('chat-form').addEventListener('submit', handleChatSubmit);
            document.querySelectorAll('.quick-reply-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const intent = btn.getAttribute('data-intent');
                    addMessage(btn.textContent, 'user');
                    handleIntent(intent);
                });
            });
        };
        
        const renderUserInfoForm = () => {
            addMessage('Welcome to the Shivarchanam Assistant! To help you better, please provide some details.', 'bot');
            chatInputContainer.innerHTML = `
                <form class="info-form" id="user-info-form">
                    <div class="form-fields">
                        <input type="text" id="user-name" placeholder="Your Name" required>
                        <input type="tel" id="user-mobile" placeholder="Mobile Number" required>
                        <input type="text" id="user-address" placeholder="City / Address" required>
                    </div>
                    <button type="submit">Start Chat</button>
                </form>`;
            document.getElementById('user-info-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                userInfo.name = document.getElementById('user-name').value;
                userInfo.mobile = document.getElementById('user-mobile').value;
                userInfo.address = document.getElementById('user-address').value;
                localStorage.setItem('shivarchanamChatUserInfo', JSON.stringify(userInfo));
                await callChatbotAPI('save_lead', userInfo);
                chatState = 'MAIN_MENU';
                addMessage(`Thank you, ${userInfo.name}! How can I assist you today?`, 'bot');
                renderChatInput();
            });
        };
        
        const handleChatSubmit = (e) => {
            e.preventDefault();
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            if (message) {
                addMessage(message, 'user');
                input.value = '';
                handleIntent(message);
            }
        };
        
        const handleIntent = async (intent) => {
            const lowerCaseIntent = intent.toLowerCase();
            if (lowerCaseIntent.includes('panchang')) {
                addMessage('Fetching today\'s Panchang...', 'bot');
                const response = await callChatbotAPI('get_panchang');
                if (response.success) { addMessage(response.html, 'bot', true); } 
                else { addMessage(response.message, 'bot'); }
            } else if (lowerCaseIntent.includes('horoscope')) {
                 // Horoscope logic here...
            } else if (lowerCaseIntent.includes('donate')) {
                // Donation logic here...
            } else if (lowerCaseIntent.includes('whatsapp')) {
                // WhatsApp logic here...
            } else {
                callGeminiAPI(intent);
            }
        };

        const callChatbotAPI = async (action, data = {}) => {
            try {
                const response = await fetch(`api/chatbot_handler.php?action=${action}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                if (!response.ok) { 
                    return { success: false, message: 'Sorry, the server is not responding. Please try again later.' };
                }
                return await response.json();
            } catch (error) {
                return { success: false, message: 'Sorry, there was a connection error. Please try again.' };
            }
        };

        const callGeminiAPI = async (userQuery) => {
            // ... (Gemini API logic)
        };
    }
});
</script>
</body>
</html>

