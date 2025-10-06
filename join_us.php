<?php 
require_once 'includes/header.php'; 

$message = '';
$message_type = '';
$member_data = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['oath'])) {
        $message = 'You must take the oath to help the temple to become a member.';
        $message_type = 'error';
    } else {
        $name = strip_tags($_POST['name']);
        $email = strip_tags($_POST['email']);
        $mobile = strip_tags($_POST['mobile']);
        $address = strip_tags($_POST['address']);
        $city = strip_tags($_POST['city']);
        $state = strip_tags($_POST['state']);
        $pincode = strip_tags($_POST['pincode']);
        $dob = strip_tags($_POST['dob']);
        $occupation = strip_tags($_POST['occupation']);
        
        $reason = $_POST['reason'];
        if($reason === 'Other') {
            $reason = "Other: " . strip_tags($_POST['other_reason']);
        }

        $photo_url = 'assets/images/default-avatar.png';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
            $target_dir = "uploads/members/";
            if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
            $photo_filename = "member_" . time() . '_' . basename($_FILES["photo"]["name"]);
            $target_file = $target_dir . $photo_filename;
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                $photo_url = $target_file;
            }
        }

        $membership_code = 'SHIV-' . strtoupper(substr(uniqid(), -6));

        $stmt = $conn->prepare("INSERT INTO memberships (name, email, mobile, address, city, state, pincode, dob, occupation, photo_url, reason_to_join, membership_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssss", $name, $email, $mobile, $address, $city, $state, $pincode, $dob, $occupation, $photo_url, $reason, $membership_code);

        if ($stmt->execute()) {
            $message_type = 'success';
            $member_data = [
                'name' => $name,
                'email' => $email,
                'membership_code' => $membership_code,
                'photo_url' => $photo_url,
                'join_date' => date("d M, Y"),
                'dob' => date("d M, Y", strtotime($dob)),
                'address' => $city . ", " . $state,
            ];
        } else {
            $message = 'There was an error processing your membership. Please try again.';
            $message_type = 'error';
        }
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
        --success-green: #28a745;
    }
    .join-us-hero {
        padding: 5rem 2rem;
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://i.ibb.co/bJCJ8X7/mandala-bg.png') no-repeat center center;
        background-size: cover;
        text-align: center;
        color: var(--white);
    }
    .join-us-hero h1 { font-size: 3.5rem; margin-bottom: 1rem; }
    .join-us-hero p { font-size: 1.2rem; max-width: 600px; margin: 0 auto; color: #eee; }
    
    .join-us-section {
        padding: 4rem 2rem;
        background-color: var(--light-bg);
        background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/om.png');
        display: flex;
        justify-content: center;
    }
    .join-container { display: flex; flex-direction: column; align-items: center; gap: 3rem; }
    .join-card { max-width: 600px; width: 100%; background: var(--white); padding: 3rem; border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.1); text-align: center; }
    .join-card h2 { font-size: 2.5rem; color: var(--primary-red); margin-bottom: 1rem; }
    .join-card p.intro-text { color: #555; margin-bottom: 2.5rem; font-size: 1.1rem; }
    .join-form { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; text-align: left; }
    .form-group { position: relative; opacity: 0; transform: translateY(20px); animation: fadeInUp 0.5s ease forwards; }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    .form-group.full-width { grid-column: 1 / -1; }
    .form-group label { margin-bottom: 0.5rem; font-weight: 500; }
    .input-wrapper { position: relative; }
    .input-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--accent-orange); }
    .validation-icon { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: var(--success-green); opacity: 0; transition: opacity 0.3s; }
    .form-group input, .form-group textarea, .form-group select {
        background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px;
        padding: 0.8rem 0.8rem 0.8rem 2.8rem; font-size: 1rem; width: 100%;
    }
    .form-group input:valid + .validation-icon { opacity: 1; }
    .form-group textarea { padding: 0.8rem; }
    #age-display { font-size: 0.9rem; color: var(--primary-red); font-weight: bold; margin-top: 0.5rem; height: 1em; }
    .oath-group { grid-column: 1 / -1; display: flex; align-items: center; gap: 0.5rem; }
    .oath-group input[type="checkbox"] { width: 20px; height: 20px; }
    .btn-submit-join { background: var(--accent-orange); color: var(--white); border: none; padding: 1rem; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1.1rem; grid-column: 1 / -1; margin-top: 1rem; }
    .form-message.error { background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
    .photo-upload { text-align: center; border: 2px dashed #ddd; border-radius: 8px; padding: 1rem; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    #photo-preview { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 0.5rem; display: none; }
    #photo-upload-icon { font-size: 2rem; color: var(--accent-orange); }

    /* SUCCESS MODAL */
    .success-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 239, 218, 0.9); backdrop-filter: blur(10px); z-index: 2000; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: opacity 0.3s; padding: 1rem; }
    .success-modal-overlay.active { opacity: 1; visibility: visible; }
    .modal-content-wrapper { position: relative; width: 100%; max-width: 400px; text-align: center; }
    .modal-close-btn { position: absolute; top: -15px; right: -15px; background: var(--white); border: none; color: #333; font-size: 1.2rem; cursor: pointer; width: 35px; height: 35px; border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
    
    #membership-card { 
        background: linear-gradient(45deg, #ffefda, #ffd28f); 
        width: 100%; 
        border-radius: 15px; 
        color: var(--dark-text); 
        font-family: sans-serif;
    }
    .card-header-bg { background: linear-gradient(45deg, var(--primary-red), var(--accent-orange)); padding: 1rem; border-radius: 15px 15px 0 0; display: flex; align-items: center; gap: 1rem; }
    .card-header-bg .logo-small { width: 40px; height: 40px; filter: brightness(0) invert(1); }
    .card-header-bg h3 { color: var(--white); font-size: 1.2rem; }
    
    .card-body { padding: 1.5rem; text-align: left; display: flex; gap: 1.5rem; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.1); }
    .member-photo { width: 100px; height: 100px; border-radius: 10px; object-fit: cover; border: 4px solid var(--white); box-shadow: 0 5px 15px rgba(0,0,0,0.2); flex-shrink: 0; }
    .member-details h2 { font-size: 1.4rem; font-weight: bold; }
    .member-details p { font-size: 0.9rem; color: #555; margin-top: 0.25rem; }
    .member-details .member-code { background: var(--dark-text); color: var(--white); padding: 0.3rem 0.8rem; border-radius: 25px; display: inline-block; margin-top: 1rem; font-size: 0.8rem; }
    
    .card-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; padding: 1.5rem; text-align: left; }
    .info-item strong { display: block; font-size: 0.8rem; color: #888; margin-bottom: 0.25rem; }
    .info-item span { font-size: 0.9rem; font-weight: 500; }
    
    .card-footer { background: var(--dark-text); padding: 1rem; text-align: center; border-radius: 0 0 15px 15px; display: flex; justify-content: space-between; align-items: center; }
    .card-footer-text a { color: var(--accent-orange); text-decoration: none; font-size: 0.8rem; }
    #qrcode { display: inline-block; padding: 4px; background: white; border-radius: 4px; }
    
    .modal-actions { margin-top: 2rem; }
    .modal-actions p { font-size: 0.9rem; color: var(--dark-text); margin-bottom: 1.5rem; }
    .social-share { display: flex; justify-content: center; gap: 1.5rem; margin-bottom: 1.5rem; }
    .social-share a { color: var(--dark-text); font-size: 1.8rem; }
    .modal-buttons { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
    .btn-modal-action { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.8rem 1.5rem; text-decoration: none; border-radius: 25px; border: none; cursor: pointer; }
    #download-id-btn { background-color: var(--accent-orange); color: #1D1D1B; font-weight: bold; }
    #whatsapp-link { background: #25D366; color: var(--white); font-weight: bold; }

    .terms-section { max-width: 600px; width: 100%; background: #fff5e8; padding: 2rem; border-radius: 20px; border: 1px solid var(--light-bg); }
    .terms-section h3 { color: var(--primary-red); margin-bottom: 1rem; }
    .terms-section p { text-align: left; color: #555; line-height: 1.6; font-size: 0.9rem; }
    @media (max-width: 600px) { .join-card { padding: 2rem 1.5rem; } .join-form { grid-template-columns: 1fr; } }
</style>

<main>
    <section class="join-us-hero">
        <h1>Join Our Sacred Community</h1>
        <p>Become an integral part of the Shivarchanam family and embark on a fulfilling spiritual journey with us.</p>
    </section>
    <section class="join-us-section">
        <div class="join-container">
            <div class="join-card">
                <form class="join-form" method="post" enctype="multipart/form-data" id="join-form">
                    <div class="form-group full-width" style="animation-delay: 0.1s;"><label for="name">Full Name</label><div class="input-wrapper"><i class="fas fa-user input-icon"></i><input type="text" id="name" name="name" required><i class="fas fa-om validation-icon"></i></div></div>
                    <div class="form-group" style="animation-delay: 0.2s;"><label for="email">Email</label><div class="input-wrapper"><i class="fas fa-envelope input-icon"></i><input type="email" id="email" name="email" required><i class="fas fa-om validation-icon"></i></div></div>
                    <div class="form-group" style="animation-delay: 0.3s;"><label for="mobile">Mobile</label><div class="input-wrapper"><i class="fas fa-phone-alt input-icon"></i><input type="tel" id="mobile" name="mobile" required><i class="fas fa-om validation-icon"></i></div></div>
                    <div class="form-group" style="animation-delay: 0.4s;"><label for="dob">Date of Birth</label><input type="date" id="dob" name="dob" required style="padding-left: 1rem;"><div id="age-display"></div></div>
                    <div class="form-group" style="animation-delay: 0.5s;"><label for="occupation">Occupation</label><div class="input-wrapper"><i class="fas fa-briefcase input-icon"></i><input type="text" id="occupation" name="occupation" required><i class="fas fa-om validation-icon"></i></div></div>
                    <div class="form-group full-width" style="animation-delay: 0.6s;"><label for="address">Street Address</label><textarea id="address" name="address" rows="2" required></textarea></div>
                    <div class="form-group" style="animation-delay: 0.7s;"><label for="city">City</label><div class="input-wrapper"><i class="fas fa-city input-icon"></i><input type="text" id="city" name="city" required><i class="fas fa-om validation-icon"></i></div></div>
                    <div class="form-group" style="animation-delay: 0.8s;"><label for="state">State</label><div class="input-wrapper"><i class="fas fa-map-marked-alt input-icon"></i><input type="text" id="state" name="state" required><i class="fas fa-om validation-icon"></i></div></div>
                    <div class="form-group full-width" style="animation-delay: 0.9s;"><label for="pincode">Pincode</label><div class="input-wrapper"><i class="fas fa-map-pin input-icon"></i><input type="text" id="pincode" name="pincode" required><i class="fas fa-om validation-icon"></i></div></div>
                    <div class="form-group full-width" style="animation-delay: 1s;"><label for="reason">Why do you want to join?</label><div class="input-wrapper"><i class="fas fa-question-circle input-icon"></i><select id="reason" name="reason" required onchange="toggleOtherReason(this)"><option value="">-- Select --</option><option value="Temple Seva">Temple Seva</option><option value="Pooja Help">Pooja Help</option><option value="Temple Repairing">Temple Repairing</option><option value="Other">Other</option></select></div></div>
                    <div class="form-group full-width" id="other-reason-group" style="display: none;"><label for="other_reason">Please specify</label><input type="text" id="other_reason" name="other_reason"></div>
                    <div class="form-group full-width" style="animation-delay: 1.1s;"><label>Your Photo</label><div class="photo-upload"><label for="photo"><img id="photo-preview"><i class="fas fa-camera" id="photo-upload-icon"></i><div>Click to upload a photo</div></label><input type="file" id="photo" name="photo" accept="image/*" style="display:none;" onchange="previewPhoto(event)"></div></div>
                    <div class="form-group oath-group full-width" style="animation-delay: 1.2s;"><input type="checkbox" id="oath" name="oath" required><label for="oath">I take an oath to support the temple's values and contribute to its well-being.</label></div>
                    <button type="submit" class="btn-submit-join">Submit Application</button>
                </form>
            </div>
            <div class="terms-section">
                <h3>Membership Terms & Transparency</h3>
                <p>By joining, you agree to uphold the values of our community. Membership is free and voluntary. All data is kept confidential and used only for communication about temple events and activities. We are committed to full transparency in all our operations.</p>
            </div>
        </div>
    </section>
</main>

<!-- SUCCESS MODAL for Membership Card -->
<div class="success-modal-overlay" id="success-modal-overlay">
    <div class="modal-content-wrapper">
        <button id="modal-close-btn" class="modal-close-btn">&times;</button>
        <div id="membership-card">
            <div class="card-header-bg">
                <img src="/admin/uploads/logo.png" alt="Logo" class="logo-small">
                <h3>SHIVARCHANAM TEMPLE</h3>
            </div>
            <div class="card-body">
                <img src="#" alt="Member Photo" class="member-photo" id="modal-member-photo">
                <div class="member-details">
                    <h2 class="member-name" id="modal-member-name">Member Name</h2>
                    <p id="modal-member-address">Address</p>
                    <div class="member-code" id="modal-member-code">SHIV-XXXXXX</div>
                </div>
            </div>
            <div class="card-info-grid">
                <div class="info-item"><strong>Date of Birth</strong><span id="modal-dob"></span></div>
                <div class="info-item"><strong>Joined On</strong><span id="modal-join-date"></span></div>
                 <div class="info-item qr-code-wrapper">
                    <div id="qrcode"></div>
                </div>
                <div class="info-item" style="text-align: right;">
                    <strong>Valid Till</strong>
                    <span id="modal-valid-till" style="font-size: 1.2rem; color: var(--primary-red);"></span>
                </div>
            </div>
            <div class="card-footer">
                <div class="card-footer-text">
                    <a href="https://www.shivarchanam.com">www.shivarchanam.com</a> | Insta: <a href="https://instagram.com/shivarchanam">@shivarchanam</a>
                </div>
            </div>
        </div>
        <div class="modal-actions">
            <p><strong>Heartfelt thanks for joining our spiritual family!</strong> Your ID has been sent to your email. You can also download it now.</p>
             <p>Share your new membership on social media!</p>
             <div class="social-share">
                <a href="#" id="share-whatsapp"><i class="fab fa-whatsapp"></i></a>
                <a href="#" id="share-facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" id="share-twitter"><i class="fab fa-twitter"></i></a>
            </div>
            <div class="modal-buttons">
                <button id="download-id-btn" class="btn-modal-action">Download ID Card</button>
                <a href="#" id="whatsapp-link" target="_blank" class="btn-modal-action">
                    <i class="fab fa-whatsapp"></i> Contact Priest
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tone/14.7.77/Tone.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script>
function previewPhoto(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('photo-preview');
        preview.src = reader.result;
        preview.style.display = 'block';
        document.getElementById('photo-upload-icon').style.display = 'none';
    }
    reader.readAsDataURL(event.target.files[0]);
}
function toggleOtherReason(select) {
    const otherReasonGroup = document.getElementById('other-reason-group');
    otherReasonGroup.style.display = select.value === 'Other' ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
     const synth = new Tone.Synth().toDestination();
    Tone.start().then(() => {
        synth.triggerAttackRelease("C4", "8n", Tone.now());
        synth.triggerAttackRelease("G4", "8n", Tone.now() + 0.2);
    }).catch(e => console.log("Audio could not start"));

    const dobInput = document.getElementById('dob');
    if(dobInput) {
        dobInput.addEventListener('change', function() {
            const dob = new Date(this.value);
            const ageDifMs = Date.now() - dob.getTime();
            const ageDate = new Date(ageDifMs);
            const age = Math.abs(ageDate.getUTCFullYear() - 1970);
            const ageDisplay = document.getElementById('age-display');
            if (age > 0) {
                ageDisplay.textContent = `Your age: ${age} years`;
            } else {
                ageDisplay.textContent = '';
            }
        });
    }

    <?php if ($message_type == 'success' && $member_data): ?>
        const memberData = <?php echo json_encode($member_data); ?>;
        const modalOverlay = document.getElementById('success-modal-overlay');
        
        document.getElementById('modal-member-photo').src = memberData.photo_url;
        document.getElementById('modal-member-name').textContent = memberData.name;
        document.getElementById('modal-member-code').textContent = memberData.membership_code;
        document.getElementById('modal-join-date').textContent = memberData.join_date;
        document.getElementById('modal-dob').textContent = memberData.dob;
        document.getElementById('modal-member-address').textContent = memberData.address;

        const joinDate = new Date();
        const validTillDate = new Date(joinDate.setFullYear(joinDate.getFullYear() + 1));
        document.getElementById('modal-valid-till').textContent = `Valid Till: ${validTillDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}`;
        
        const qrCodeText = `Name: ${memberData.name}\nCode: ${memberData.membership_code}\nEmail: ${memberData.email}`;
        
        const qrcodeContainer = document.getElementById("qrcode");
        qrcodeContainer.innerHTML = "";
        new QRCode(qrcodeContainer, {
            text: qrCodeText,
            width: 80,
            height: 80,
        });

        const prefilledMessage = `Hari Om! I have just registered as a new member. My membership code is ${memberData.membership_code}. Please let me know when I can collect my card.`;
        const whatsappUrl = `https://wa.me/919925904767?text=${encodeURIComponent(prefilledMessage)}`;
        document.getElementById('whatsapp-link').href = whatsappUrl;

        document.getElementById('join-form').style.display = 'none';
        
        setTimeout(() => { // Delay showing the modal to allow QR code to render
             modalOverlay.classList.add('active');
        }, 500);
        
        const closeModalBtn = document.getElementById('modal-close-btn');
        if(closeModalBtn) closeModalBtn.addEventListener('click', () => modalOverlay.classList.remove('active'));

        document.getElementById('download-id-btn').addEventListener('click', function() {
            html2canvas(document.getElementById('membership-card'), { backgroundColor: null, scale: 3 }).then(canvas => {
                const link = document.createElement('a');
                link.download = `Shivarchanam_Membership_Card_${memberData.membership_code}.png`;
                link.href = canvas.toDataURL();
                link.click();
            });
        });
    <?php endif; ?>
});
</script>

<?php require_once 'includes/footer.php'; ?>

