<?php
// api/chatbot_handler.php
require_once '../config/db.php';
header('Content-Type: application/json');

// Get the posted JSON data from the chatbot's fetch request
$post_data = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'save_lead':
        if (!empty($post_data['name']) && !empty($post_data['mobile'])) {
            $stmt = $conn->prepare("INSERT INTO chatbot_leads (name, mobile, address) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $post_data['name'], $post_data['mobile'], $post_data['address']);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error.']);
            }
        } else {
             echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        }
        break;

    case 'get_donations':
        $result = $conn->query("SELECT * FROM donation_products ORDER BY amount ASC");
        $html = '<div class="donation-cards-container">';
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $donation_link = 'donation_page.php?item=' . urlencode($row['name']) . '&amount=' . $row['amount'];
                $html .= '<div class="donation-card">';
                $html .= '<img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                $html .= '<div class="donation-card-info">';
                $html .= '<h5>' . htmlspecialchars($row['name']) . '</h5>';
                $html .= '<p>' . htmlspecialchars($row['description']) . '</p>';
                $html .= '<div class="donation-card-footer">';
                $html .= '<strong>₹' . htmlspecialchars($row['amount']) . '</strong>';
                $html .= '<a href="' . $donation_link . '" target="_blank" class="btn-donate-card">Donate</a>';
                $html .= '</div></div></div>';
            }
        } else {
            $html .= '<p>No donation options are available at the moment.</p>';
        }
        $html .= '</div>';
        echo json_encode(['success' => true, 'html' => $html]);
        break;

    case 'get_panchang':
        $date = date('Y-m-d');
        $lat = '22.6934'; // Nadiad, Gujarat
        $lon = '72.8633';
        $api_url = "https://pavitra.guru/api/panchang?date={$date}&latitude={$lat}&longitude={$lon}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $data = json_decode($response, true);
            if ($data['success']) {
                $p = $data['data'];
                $html = "<strong>Today's Panchang:</strong><table>" .
                        "<tr><td>Tithi:</td><td>{$p['tithi']['name']}</td></tr>" .
                        "<tr><td>Nakshatra:</td><td>{$p['nakshatra']['name']}</td></tr>" .
                        "<tr><td>Sunrise:</td><td>{$p['sunrise']}</td></tr>" .
                        "<tr><td>Sunset:</td><td>{$p['sunset']}</td></tr>" .
                        "</table>";
                echo json_encode(['success' => true, 'html' => $html]);
            } else {
                 echo json_encode(['success' => false, 'message' => 'Could not retrieve Panchang data at this time.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not connect to the astrology service. Please try again later.']);
        }
        break;

    case 'get_horoscope':
        $dob = $post_data['dob'] ?? null;
        if ($dob) {
            $sign = getZodiacSign($dob);
            $horoscope_api_url = "https://pavitra.guru/api/horoscope/daily?sign=" . strtolower($sign);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $horoscope_api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $response = curl_exec($ch);
            curl_close($ch);

            if($response){
                $horoscope_data = json_decode($response, true);
                if($horoscope_data['success']){
                     echo json_encode([
                        'success' => true, 
                        'data' => [
                            'sign' => $sign, 
                            'report' => $horoscope_data['data']['prediction'] ?? 'Prediction not available.', 
                            'luckyNumber' => rand(1, 9), 
                            'luckyColor' => 'Gold'
                        ]
                    ]);
                } else {
                     echo json_encode(['success' => false, 'message' => 'Horoscope data is currently unavailable.']);
                }
            } else {
                 echo json_encode(['success' => false, 'message' => 'Could not connect to the horoscope service.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Date of birth is required.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}

function getZodiacSign($dob) {
    $date = new DateTime($dob);
    $month = (int)$date->format('m');
    $day = (int)$date->format('d');
    if (($month == 3 && $day >= 21) || ($month == 4 && $day <= 19)) return "Aries";
    if (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20)) return "Taurus";
    if (($month == 5 && $day >= 21) || ($month == 6 && $day <= 20)) return "Gemini";
    if (($month == 6 && $day >= 21) || ($month == 7 && $day <= 22)) return "Cancer";
    if (($month == 7 && $day >= 23) || ($month == 8 && $day <= 22)) return "Leo";
    if (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22)) return "Virgo";
    if (($month == 9 && $day >= 23) || ($month == 10 && $day <= 22)) return "Libra";
    if (($month == 10 && $day >= 23) || ($month == 11 && $day <= 21)) return "Scorpio";
    if (($month == 11 && $day >= 22) || ($month == 12 && $day <= 21)) return "Sagittarius";
    if (($month == 12 && $day >= 22) || ($month == 1 && $day <= 19)) return "Capricorn";
    if (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18)) return "Aquarius";
    if (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20)) return "Pisces";
    return "Unknown";
}
?>

