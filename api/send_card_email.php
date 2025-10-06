<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer files from the 'phpmailer' directory
require '../phpmailer/Exception.php';
require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';

header('Content-Type: application/json');
$post_data = json_decode(file_get_contents('php://input'), true);

if ($post_data && isset($post_data['email'])) {
    $mail = new PHPMailer(true);

    try {
        // --- SMTP DEBUGGING IS ENABLED ---
        // This will show detailed error messages on the screen to help diagnose the problem.
        // **IMPORTANT:** After you fix the issue and emails are sending, change this back to 0.
        $mail->SMTPDebug = 2; 
        
        // --- YOUR HOSTINGER EMAIL SERVER SETTINGS ---
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'no-reply@shivarchanam.com';
        $mail->Password   = 'SHREEhanuman@123!';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        // --- END OF CONFIGURATION ---

        //Recipients
        $mail->setFrom('no-reply@shivarchanam.com', 'Shivarchanam Temple');
        $mail->addAddress($post_data['email'], $post_data['name']); // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Shivarchanam Temple - Your Membership Card';
        
        // Construct the full URL for the image
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domain_name = $_SERVER['HTTP_HOST'];
        $photo_full_url = $protocol . $domain_name . '/' . $post_data['photo_url'];
        $logo_full_url = $protocol . $domain_name . '/admin/uploads/logo.png';


        $joinDate = new DateTime();
        $validTillDate = (clone $joinDate)->modify('+1 year')->format('d M, Y');
        $formattedJoinDate = $joinDate->format('d M, Y');

        $mail->Body    = '
            <div style="font-family: sans-serif; max-width: 400px; margin: auto; background: #f4f5f7; padding: 20px;">
                <div style="background: linear-gradient(45deg, #ffefda, #ffd28f); border-radius: 15px; color: #333; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div style="background: linear-gradient(45deg, #9F0102, #FF6D01); padding: 1rem; border-radius: 15px 15px 0 0; display: flex; align-items: center; gap: 1rem;">
                        <img src="' . $logo_full_url . '" alt="Logo" style="width: 40px; height: 40px; filter: brightness(0) invert(1);">
                        <h3 style="color: white; font-size: 1.2rem; margin: 0;">SHIVARCHANAM TEMPLE</h3>
                    </div>
                    <div style="padding: 1.5rem; text-align: left; display: flex; gap: 1.5rem; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.1);">
                        <img src="' . $photo_full_url . '" alt="Member Photo" style="width: 100px; height: 100px; border-radius: 10px; object-fit: cover; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                        <div>
                            <h2 style="font-size: 1.4rem; font-weight: bold; margin: 0;">' . $post_data['name'] . '</h2>
                            <p style="font-size: 0.9rem; color: #555; margin-top: 0.25rem;">' . $post_data['address'] . '</p>
                            <div style="background: #1D1D1B; color: white; padding: 0.3rem 0.8rem; border-radius: 25px; display: inline-block; margin-top: 1rem; font-size: 0.8rem;">' . $post_data['membership_code'] . '</div>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; padding: 1.5rem; text-align: left;">
                        <div><strong style="display: block; font-size: 0.8rem; color: #888;">Date of Birth</strong><span style="display: block; font-size: 0.9rem;">' . $post_data['dob'] . '</span></div>
                        <div><strong style="display: block; font-size: 0.8rem; color: #888;">Joined On</strong><span style="display: block; font-size: 0.9rem;">' . $formattedJoinDate . '</span></div>
                        <div style="grid-column: 1 / -1; text-align: center;"><strong>Valid Till</strong><span style="font-size: 1.2rem; color: #9F0102; display: block;">' . $validTillDate . '</span></div>
                    </div>
                </div>
            </div>';
        $mail->AltBody = 'Thank you for joining Shivarchanam Temple! Your Membership Code is ' . $post_data['membership_code'];

        $mail->send();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Return the detailed error message in the JSON response when debug is on
        echo json_encode(['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
}
?>

