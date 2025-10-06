<?php 
require_once 'includes/header.php'; 

// Fetch services from the database for the dropdown menu
$services_result = $conn->query("SELECT name FROM services ORDER BY id ASC");
$services = [];
if($services_result) {
    while($row = $services_result->fetch_assoc()) {
        $services[] = $row['name'];
    }
}

$message = '';
$message_type = '';
$form_data = null; // To hold submitted data for the popup

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve form data
    $name = strip_tags($_POST['name']);
    $email = strip_tags($_POST['email']);
    $mobile = strip_tags($_POST['mobile']);
    $dob = strip_tags($_POST['dob']);
    $service = strip_tags($_POST['service']);
    $time = strip_tags($_POST['time']);
    $user_message = strip_tags($_POST['message']);

    // Prepare and execute the database insertion
    $stmt = $conn->prepare("INSERT INTO appointments (name, email, mobile, dob, service_name, preferred_time, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $mobile, $dob, $service, $time, $user_message);

    if ($stmt->execute()) {
        // On success, set a flag and store data for the popup
        $message_type = 'success';
        $form_data = $_POST;
    } else {
        $message = 'There was an error submitting your request. Please try again.';
        $message_type = 'error';
    }
}
?>
<style>
    /* Theme colors needed for this page */
    :root {
        --primary-red: #9F0102;
        --accent-orange: #FF6D01;
        --light-bg: #FFEFDA;
        --white: #ffffff;
        --dark-text: #333333;
    }
    .booking-hero {
        padding: 4rem 2rem;
        background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://i.ibb.co/bJCJ8X7/mandala-bg.png') no-repeat center center/cover;
        text-align: center;
        color: var(--white);
    }
    .booking-hero h1 {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }
    .booking-hero p {
        font-size: 1.2rem;
        max-width: 600px;
        margin: 0 auto;
    }

    .booking-page-section {
        padding: 4rem 2rem;
        background-color: var(--light-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/om.png');
        background-repeat: repeat;
    }
    .booking-card {
        max-width: 700px;
        width: 100%;
        background: var(--white);
        padding: 3rem;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        text-align: center;
    }
    .booking-card-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 2rem;
    }
    .temple-logo-small {
        width: 80px;
        height: 80px;
        margin-bottom: 1rem;
    }
    .booking-card h2 {
        font-size: 2.5rem;
        color: var(--primary-red);
        margin: 0;
    }
    .booking-form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        text-align: left;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.5s ease forwards;
        position: relative;
    }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    .form-group label {
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    .form-group .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 0.8rem;
        font-size: 1rem;
        font-family: inherit;
        width: 100%;
        padding-left: 2.5rem; /* Space for icon */
    }
    .form-group .input-icon {
        position: absolute;
        left: 1rem;
        color: var(--accent-orange);
    }
    .form-group textarea {
        padding-left: 0.8rem;
        resize: vertical;
        min-height: 100px;
    }
    .btn-submit-booking {
        background: var(--accent-orange);
        color: var(--white);
        border: none;
        padding: 1rem;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        font-size: 1.1rem;
        grid-column: 1 / -1;
        margin-top: 1rem;
        transition: background-color 0.3s;
    }
    .btn-submit-booking:hover {
        background: var(--primary-red);
    }
    .form-message.error { background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
    
    /* SUCCESS MODAL */
    .success-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 2000; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s; }
    .success-modal-overlay.active { opacity: 1; visibility: visible; }
    .success-modal { background: var(--white); padding: 2.5rem; border-radius: 15px; width: 90%; max-width: 450px; text-align: center; transform: scale(0.9); transition: transform 0.3s; position: relative; }
    .success-modal-close { position: absolute; top: 1rem; right: 1rem; font-size: 1.5rem; color: #aaa; cursor: pointer; border: none; background: none; }
    .success-modal .fas.fa-check-circle { font-size: 4rem; color: #28a745; margin-bottom: 1rem; }
    .success-modal h3 { font-size: 1.8rem; margin-bottom: 1rem; color: #333; }
    .success-modal p { color: #555; margin-bottom: 1rem; }
    .success-modal .detail-highlight { font-weight: bold; color: var(--primary-red); }
    .success-modal a { display: flex; align-items: center; justify-content: center; gap: 1rem; padding: 1rem; border-radius: 10px; background: #25D366; text-decoration: none; color: var(--white); font-weight: bold; margin-top: 2rem; }
    .success-modal .fab { font-size: 1.5rem; }
    
    @media (max-width: 600px) {
        body { padding-top: 155px !important; }
        .booking-page-section { padding: 0; }
        .booking-card { padding: 2rem 1.5rem; border-radius: 20px 20px 0 0; box-shadow: none; }
        .booking-form { grid-template-columns: 1fr; }
    }
</style>

<main>
    <section class="booking-hero">
        <h1>Book a Sacred Appointment</h1>
        <p>Connect with Aacharya Vyas for personalized spiritual guidance and ceremonies.</p>
    </section>

    <section class="booking-page-section">
        <div class="booking-card">
            <div class="booking-card-header">
                <img src="https://i.ibb.co/wJ4yK1D/temple-logo.png" alt="Temple Logo" class="temple-logo-small">
                <h2>Appointment Form</h2>
            </div>

            <?php if ($message && $message_type == 'error'): ?>
                <div class="form-message error"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
                
            <form class="booking-form" method="post" id="booking-form">
                <div class="form-group" style="animation-delay: 0.1s;">
                    <label for="name">Full Name</label>
                    <div class="input-wrapper">
                         <i class="fas fa-user input-icon"></i>
                        <input type="text" id="name" name="name" required>
                    </div>
                </div>
                <div class="form-group" style="animation-delay: 0.2s;">
                    <label for="email">Email Address</label>
                     <div class="input-wrapper">
                         <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-group" style="animation-delay: 0.3s;">
                    <label for="mobile">Mobile Number</label>
                     <div class="input-wrapper">
                         <i class="fas fa-phone-alt input-icon"></i>
                        <input type="tel" id="mobile" name="mobile" required>
                    </div>
                </div>
                <div class="form-group" style="animation-delay: 0.4s;">
                    <label for="dob">Date of Birth</label>
                    <div class="input-wrapper">
                         <i class="fas fa-calendar-alt input-icon"></i>
                        <input type="date" id="dob" name="dob" required>
                    </div>
                </div>
                <div class="form-group full-width" style="animation-delay: 0.5s;">
                    <label for="service">Service Interested In</label>
                     <div class="input-wrapper">
                        <i class="fas fa-concierge-bell input-icon"></i>
                        <select id="service" name="service" required>
                            <option value="">-- Select a Service --</option>
                            <?php foreach($services as $service_name): ?>
                                <option value="<?php echo htmlspecialchars($service_name); ?>"><?php echo htmlspecialchars($service_name); ?></option>
                            <?php endforeach; ?>
                            <option value="Other">Other Inquiry</option>
                        </select>
                    </div>
                </div>
                <div class="form-group full-width" style="animation-delay: 0.6s;">
                    <label for="time">Preferred Time Slot</label>
                    <div class="input-wrapper">
                        <i class="fas fa-clock input-icon"></i>
                        <input type="text" id="time" name="time" placeholder="e.g., Morning, 4 PM - 6 PM">
                    </div>
                </div>
                <div class="form-group full-width" style="animation-delay: 0.7s;">
                    <label for="message">Additional Message (Optional)</label>
                    <textarea id="message" name="message"></textarea>
                </div>
                <button type="submit" class="btn-submit-booking">Submit Request</button>
            </form>
        </div>
    </section>
</main>

<!-- SUCCESS MODAL -->
<div class="success-modal-overlay" id="success-modal-overlay">
    <div class="success-modal">
        <button id="close-modal-btn" class="success-modal-close">&times;</button>
        <i class="fas fa-check-circle"></i>
        <h3 id="thank-you-name">Thank You!</h3>
        <p>Your request for the <strong id="booked-service-name" class="detail-highlight">Service</strong> has been submitted. We will contact you on your mobile number <strong id="booked-mobile" class="detail-highlight">Mobile</strong> shortly.</p>
        <a href="#" id="whatsapp-link" target="_blank">
            <i class="fab fa-whatsapp"></i>
            <span>Contact Priest on WhatsApp</span>
        </a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tone/14.7.77/Tone.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Play a gentle sound on page load
    const synth = new Tone.Synth().toDestination();
    Tone.start().then(() => {
        synth.triggerAttackRelease("C5", "8n", Tone.now());
        synth.triggerAttackRelease("E5", "8n", Tone.now() + 0.2);
        synth.triggerAttackRelease("G5", "8n", Tone.now() + 0.4);
    }).catch(e => {
        console.log("Audio could not start: ", e);
    });

    <?php if ($message_type == 'success' && $form_data): ?>
        const formData = <?php echo json_encode($form_data); ?>;
        const modalOverlay = document.getElementById('success-modal-overlay');
        const whatsappLink = document.getElementById('whatsapp-link');
        const bookingForm = document.getElementById('booking-form');
        const thankYouName = document.getElementById('thank-you-name');
        const bookedServiceName = document.getElementById('booked-service-name');
        const bookedMobile = document.getElementById('booked-mobile');
        const closeModalBtn = document.getElementById('close-modal-btn');

        // Personalize the popup
        thankYouName.textContent = `Thank You, ${formData.name}!`;
        bookedServiceName.textContent = formData.service;
        bookedMobile.textContent = formData.mobile;
        
        // Create prefilled WhatsApp message
        const prefilledMessage = `Hari Om! I have just submitted an appointment request.\n\nService: ${formData.service}\nName: ${formData.name}\nMobile: ${formData.mobile}\nDOB: ${formData.dob}\nPreferred Time: ${formData.time || 'Not specified'}`;
        const whatsappUrl = `https://wa.me/919925904767?text=${encodeURIComponent(prefilledMessage)}`;
        
        if (whatsappLink) {
            whatsappLink.href = whatsappUrl;
        }

        if (bookingForm) {
            bookingForm.style.display = 'none';
        }

        if(modalOverlay) {
            modalOverlay.classList.add('active');
        }

        function closeModal() {
            modalOverlay.classList.remove('active');
        }

        if(closeModalBtn) {
            closeModalBtn.addEventListener('click', closeModal);
        }
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });
    <?php endif; ?>
});
</script>

<?php require_once 'includes/footer.php'; ?>

