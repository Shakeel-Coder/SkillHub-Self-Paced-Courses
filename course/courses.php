<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>All Courses</title>
</head>
<body>

<?php include '../header.php'; ?>

<div class="container">
  <h2>All Courses</h2>
  <div class="row">
    <?php
    // Database connection
    include '../config/config.php';

 // Start session if not already started
 if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['id']);
$userId = $isLoggedIn ? $_SESSION['id'] : null;

    // Fetch courses from the database
    $stmt = $conn->prepare("
        SELECT c.*, u.profile_picture, u.username 
        FROM courses c 
        JOIN users u ON c.instructor_id = u.id
    ");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($courses as $course): 
    // Check if the learner is enrolled
    $stmt = $conn->prepare("SELECT * FROM enrollments WHERE course_id = ? AND student_id = ?");
    $stmt->execute([$course['id'], $userId]);
    $isEnrolled = $stmt->fetch(PDO::FETCH_ASSOC) ? true : false; ?>


      <div class="col-md-4">
        <div class="card mb-4">
          <a href="course_overview.php?id=<?php echo htmlspecialchars($course['id']); ?>">
            <img src="../uploads/<?php echo htmlspecialchars($course['thumbnail']); ?>" alt="course" class="card-img-top">
          </a>
          <div class="card-body">
            <h4 class="mb-2 text-deco text-truncate-line-2">
              <a href="course_overview.php?id=<?php echo htmlspecialchars($course['id']); ?>" class="text-inherit text-decoration-none text-dark">
                <?php echo htmlspecialchars($course['title']); ?>
              </a>
            </h4>
            <ul class="mb-3 list-inline">
              <li class="list-inline-item"><i class="bi bi-clock"></i> <span><?php echo htmlspecialchars($course['duration']); ?></span></li>
              <li class="list-inline-item"><i class="bi bi-bar-chart"></i> <?php echo htmlspecialchars($course['level']); ?></li>
              <li class="list-inline-item"><i class="bi bi-tag"></i> <?php echo htmlspecialchars($course['category']); ?></li>
            </ul>
            <hr>
            <div class="row align-items-center g-0">
              <div class="col-auto">
                <img src="../uploads/<?php echo htmlspecialchars($course['profile_picture']); ?>" class="rounded-circle avatar-xs" height="50px" width="50px" alt="avatar">
              </div>
              <div class="col ms-2">
                <span><?php echo htmlspecialchars($course['username']); ?></span>
              </div>
              <div class="col-auto">
                <?php if ($isEnrolled): ?>
                  <button class="btn btn-danger" disabled>Enrolled</button>
                <?php else: ?>
                  <a href="course_overview.php?id=<?php echo htmlspecialchars($course['id']); ?>" class="btn btn-primary">Enroll Now</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php include "../footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

</body>
</html>