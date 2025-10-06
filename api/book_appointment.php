<?php
require_once '../config/db.php';
header('Content-Type: application/json');

// Get the posted JSON data
$post_data = json_decode(file_get_contents('php://input'), true);

if (!empty($post_data['name']) && !empty($post_data['mobile']) && !empty($post_data['service'])) {
    $name = $post_data['name'];
    $mobile = $post_data['mobile'];
    $service = $post_data['service'];

    $stmt = $conn->prepare("INSERT INTO appointments (name, mobile, service_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $mobile, $service);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Appointment request saved.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error. Could not save appointment.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required information.']);
}
?>