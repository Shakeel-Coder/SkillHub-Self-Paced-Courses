<?php
// Database connection
include '../config/config.php';

// Start session
session_start();

// Get course ID from query parameter
$courseId = $_GET['id'];

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: http://localhost/skillhub/auth/login.php?redirect=course/course_overview.php?id=$courseId");
    exit();
}

// Fetch course details from the database
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch instructor details
$stmt = $conn->prepare("SELECT profile_picture, username FROM users WHERE id = ?");
$stmt->execute([$course['instructor_id']]);
$instructor = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the learner is enrolled
$userId = $_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM enrollments WHERE course_id = ? AND student_id = ?");
$stmt->execute([$courseId, $userId]);
$isEnrolled = $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll'])) {
    $stmt = $conn->prepare("INSERT INTO enrollments (course_id, student_id) VALUES (?, ?)");
    $stmt->execute([$courseId, $userId]);
    $isEnrolled = true;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Course Overview</title>
</head>
<body>

<?php include '../header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <img src="../uploads/<?php echo htmlspecialchars($course['thumbnail']); ?>" class="img-fluid rounded mb-4" alt="Course Thumbnail">
            <h3><?php echo htmlspecialchars($course['title']); ?></h3>
            <ul class="list-inline">
                <li class="list-inline-item"><i class="bi bi-clock"></i> <span><?php echo htmlspecialchars($course['duration']); ?></span></li>
                <li class="list-inline-item"><i class="bi bi-bar-chart"></i> <?php echo htmlspecialchars($course['level']); ?></li>
                <li class="list-inline-item"><i class="bi bi-tag"></i> <?php echo htmlspecialchars($course['category']); ?></li>
            </ul>
            <p><?php echo htmlspecialchars($course['description']); ?></p>
            <div class="mt-4">
                <?php if ($isEnrolled): ?>
                    <button class="btn btn-danger" disabled>Enrolled</button>
                <?php else: ?>
                    <form method="post">
                        <button type="submit" name="enroll" class="btn btn-primary">Enroll Now</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="mt-5">
                <h4>Course Curriculum</h4>
                <ul class="list-group">
                    <?php
                    // Fetch course content from the database
                    $stmt = $conn->prepare("SELECT * FROM course_curriculum WHERE course_id = ?");
                    $stmt->execute([$courseId]);
                    $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($contents as $content): ?>
                        <li class="list-group-item">
                            <h5><?php echo htmlspecialchars($content['chapter']); ?></h5>
                            <ul>
                                 <li><?php echo htmlspecialchars($content['lessons']); ?></li>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="mt-5">
                <h4>About Instructor</h4>
                <div class="d-flex align-items-center">
                    <img src="../uploads/<?php echo htmlspecialchars($instructor['profile_picture']); ?>" class="rounded-circle avatar-xs" height="50px" width="50px" alt="Instructor Avatar">
                    <span class="ms-2"><?php echo htmlspecialchars($instructor['username']); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

</body>
</html>