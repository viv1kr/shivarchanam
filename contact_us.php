<?php 
require_once 'includes/header.php'; 
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = strip_tags($_POST['name']);
    $email = strip_tags($_POST['email']);
    $subject = strip_tags($_POST['subject']);
    $user_message = strip_tags($_POST['message']);

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $user_message);

    if ($stmt->execute()) {
        $message = 'Thank you for your message! We will get back to you shortly.';
        $message_type = 'success';
    } else {
        $message = 'There was an error sending your message. Please try again.';
        $message_type = 'error';
    }
}
?>
<style>
    :root {
        --primary-red: #9F0102;
        --accent-orange: #FF6D01;
        --light-bg: #FFEFDA;
        --white: #ffffff;
        --dark-text: #333333;
        --gray: #f4f5f7;
    }
    .contact-hero {
        padding: 5rem 2rem;
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://i.ibb.co/bJCJ8X7/mandala-bg.png') no-repeat center center/cover;
        text-align: center;
        color: var(--white);
    }
    .contact-hero h1 { font-size: 3.5rem; margin-bottom: 1rem; }
    .contact-hero p { font-size: 1.2rem; max-width: 600px; margin: 0 auto; color: #eee; }
    
    .contact-section {
        padding: 4rem 2rem;
        background-color: var(--light-bg);
    }
    .contact-container {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 3rem;
    }
    .contact-details-col, .contact-form-col {
        background: var(--white);
        padding: 2.5rem;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }
    .contact-details-col h3 {
        font-size: 1.8rem;
        color: var(--primary-red);
        margin-bottom: 2rem;
        border-bottom: 2px solid var(--accent-orange);
        padding-bottom: 1rem;
        display: inline-block;
    }
    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .contact-item .fas {
        font-size: 1.5rem;
        color: var(--accent-orange);
        margin-top: 5px;
    }
    .contact-item strong {
        display: block;
        font-size: 1.1rem;
        margin-bottom: 0.25rem;
    }
    .contact-item span, .contact-item a {
        color: #555;
        text-decoration: none;
    }
    .map-container { border-radius: 15px; overflow: hidden; margin-top: 2rem; }
    .map-container iframe { border: none; width: 100%; height: 200px; }
    
    .contact-form-col h3 {
        font-size: 1.8rem;
        color: var(--primary-red);
        margin-bottom: 1rem;
    }
    .form-group label { margin-bottom: 0.5rem; font-weight: 500; }
    .form-group input, .form-group textarea {
        background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px;
        padding: 0.8rem; font-size: 1rem; width: 100%; margin-bottom: 1.5rem;
    }
    .btn-submit-contact { background: var(--accent-orange); color: var(--white); border: none; padding: 1rem; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1.1rem; width: 100%; }
    .form-message { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; }
    .form-message.success { background-color: #d4edda; color: #155724; }
    
    @media (max-width: 900px) {
        .contact-container { grid-template-columns: 1fr; }
    }
</style>

<main>
    <section class="contact-hero">
        <h1>Get in Touch</h1>
        <p>We are here to assist you. Reach out to us for any inquiries, services, or spiritual guidance.</p>
    </section>

    <section class="contact-section">
        <div class="contact-container">
            <div class="contact-details-col">
                <h3>Contact Information</h3>
                <div class="contact-item">
                    <i class="fas fa-user-tie"></i>
                    <div>
                        <strong>Acharya Kalpesh Vyas</strong>
                        <a href="tel:+919925904767">+91 99259 04767</a>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-gopuram"></i>
                    <div>
                        <strong>Temple Address</strong>
                        <span>123 Spiritual Road, Divine City, India</span>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <strong>Email Address</strong>
                        <a href="mailto:contact@shivarchanam.com">contact@shivarchanam.com</a>
                    </div>
                </div>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3691.828135992928!2d72.8607236154005!3d22.69469908518201!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e5d6a7d5d4d3b%3A0x4f2b5d4b4f6b4b3b!2sNadiad%2C%2G%20Gujarat%2C%20India!5e0!3m2!1sen!2sus!4v1633026193742!5m2!1sen!2sus"></iframe>
                </div>
            </div>
            <div class="contact-form-col">
                <h3>Send us a Message</h3>
                <?php if ($message): ?>
                    <div class="form-message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
                <?php else: ?>
                    <form method="post">
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Your Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                         <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Your Message</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn-submit-contact">Send Message</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
