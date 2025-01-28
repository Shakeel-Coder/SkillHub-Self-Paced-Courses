<?php
include '../config/config.php';

// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['video_id'], $data['course_id'], $data['student_id'])) {
    $videoId = $data['video_id'];
    $courseId = $data['course_id'];
    $studentId = $data['student_id'];

    // Insert or update the video progress
    $stmt = $conn->prepare("
        INSERT INTO video_progress (student_id, course_id, video_id, watched)
        VALUES (?, ?, ?, TRUE)
        ON DUPLICATE KEY UPDATE watched = TRUE
    ");
    $stmt->execute([$studentId, $courseId, $videoId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>