document.addEventListener('DOMContentLoaded', function () {
    
    // --- LIVE DATE & TIME FOR HEADER ---
    const datetimeElement = document.getElementById("live-datetime");
    function updateDateTime() {
        if (datetimeElement) {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Kolkata', hour12: true };
            datetimeElement.textContent = now.toLocaleString('en-IN', options);
        }
    }
    if(datetimeElement){
        updateDateTime();
        setInterval(updateDateTime, 1000);
    }

    // --- HEADER SCROLL BEHAVIOR ---
    const body = document.body;
    if (document.getElementById("site-header")) {
        window.addEventListener("scroll", () => {
            if (window.scrollY > 50) {
                body.classList.add("header-scrolled");
            } else {
                body.classList.remove("header-scrolled");
            }
        });
    }

    // --- MOBILE SIDE MENU (for Hamburger) ---
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
    
    // --- HERO SLIDER ---
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
            dot.addEventListener("click", () => {
                showSlide(i);
                resetInterval();
            });
            if(dotsContainer) dotsContainer.appendChild(dot);
            dots.push(dot);
        });

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle("active", i === index);
            });
            if(dots.length > 0){
                dots.forEach((dot, i) => {
                    dot.classList.toggle("active", i === index);
                });
            }
            currentSlide = index;
        }

        function nextSlide() {
            let newIndex = (currentSlide + 1) % slides.length;
            showSlide(newIndex);
        }

        function prevSlide() {
            let newIndex = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(newIndex);
        }

        function startInterval() {
            slideInterval = setInterval(nextSlide, 5000);
        }

        function resetInterval() {
            clearInterval(slideInterval);
            startInterval();
        }

        if (nextBtn) nextBtn.addEventListener('click', () => {
            nextSlide();
            resetInterval();
        });
        if (prevBtn) prevBtn.addEventListener('click', () => {
            prevSlide();
            resetInterval();
        });

        startInterval();
    }





    // --- AI CHATBOT ---
    // (Your complete, functional chatbot JavaScript would be included here to avoid conflicts)


    // --- AI CHATBOT LOGIC ---
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
            if (isHtml) {
                messageBubble.innerHTML = text;
            } else {
                messageBubble.textContent = text;
            }
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
        
        const renderHoroscopeForm = () => {
            addMessage('To provide your horoscope, please enter your name and date of birth.', 'bot');
            chatInputContainer.innerHTML = `
                <form class="info-form" id="horoscope-form">
                     <div class="form-fields">
                        <input type="text" id="horoscope-name" placeholder="Your Name" value="${userInfo.name || ''}" required>
                        <input type="date" id="horoscope-dob" required>
                    </div>
                    <button type="submit">Get Horoscope</button>
                </form>`;
            
            document.getElementById('horoscope-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const name = document.getElementById('horoscope-name').value;
                const dob = document.getElementById('horoscope-dob').value;
                addMessage(`Getting horoscope for ${name}, born on ${dob}...`, 'user');
                const response = await callChatbotAPI('get_horoscope', { name, dob });
                if (response.success) {
                    const { sign, report, luckyNumber, luckyColor } = response.data;
                    const horoscopeHtml = `<strong>Horoscope for ${name} (${sign}):</strong><p>${report}</p><p><strong>Lucky Number:</strong> ${luckyNumber}</p><p><strong>Lucky Color:</strong> ${luckyColor}</p>`;
                    addMessage(horoscopeHtml, 'bot', true);
                } else {
                    addMessage(response.message || 'Could not fetch horoscope.', 'bot');
                }
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
                if (response.success) addMessage(response.html, 'bot', true);
                else addMessage(response.message, 'bot');
            } else if (lowerCaseIntent.includes('horoscope')) {
                chatState = 'AWAITING_HOROSCOPE_DETAILS';
                renderHoroscopeForm();
            } else if (lowerCaseIntent.includes('donate')) {
                addMessage("Your support is vital. Here are some ways you can contribute:", 'bot');
                const response = await callChatbotAPI('get_donations');
                 if (response.success) addMessage(response.html, 'bot', true);
                 else addMessage(response.message, 'bot');
            } else if (lowerCaseIntent.includes('whatsapp')) {
                 const whatsappUrl = 'https://wa.me/910000000000?text=Hari%20Om!%20I%20have%20a%20question.';
                 addMessage(`You can chat directly with a priest on WhatsApp. <a href="${whatsappUrl}" target="_blank">Click here to start the chat.</a>`, 'bot', true);
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
                if (!response.ok) return { success: false, message: 'Sorry, the server is not responding.' };
                return await response.json();
            } catch (error) {
                return { success: false, message: 'Sorry, there was a connection error.' };
            }
        };

        const callGeminiAPI = async (userQuery) => {
            addMessage('Thinking...', 'bot');
            const apiKey = "";
            const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-05-20:generateContent?key=${apiKey}`;
            const systemPrompt = "You are a helpful assistant for a Hindu temple named Shivarchanam. Be respectful, brief, and focus on spirituality or temple activities. Politely decline unrelated questions.";
            const payload = { contents: [{ parts: [{ text: userQuery }] }], systemInstruction: { parts: [{ text: systemPrompt }] }, };
            try {
                const response = await fetch(apiUrl, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
                const result = await response.json();
                const thinkingMessage = Array.from(chatMessages.querySelectorAll('.chat-message.bot .message-bubble')).pop();
                if(thinkingMessage && thinkingMessage.textContent === 'Thinking...') thinkingMessage.parentElement.remove();
                const botResponse = result.candidates?.[0]?.content?.parts?.[0]?.text;
                if (botResponse) addMessage(botResponse, 'bot');
                else addMessage("Sorry, I couldn't process that. I can help with Panchang, Horoscopes, and Donations.", 'bot');
            } catch (error) {
                addMessage("Sorry, I'm having trouble connecting right now.", 'bot');
            }
        };
    }
});

