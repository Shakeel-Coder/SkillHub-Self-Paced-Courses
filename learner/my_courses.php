

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>My Courses</title>
</head>
<body>

<?php

    require_once '../auth/auth.php';
    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to admins
    checkRole(['learner']);

include '../config/config.php';

// Start session
$userId = $_SESSION['id'];

// Fetch enrolled courses
$stmt = $conn->prepare("
    SELECT c.id, c.title, c.category, c.level, c.course_code, u.username AS author, e.status 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.id 
    JOIN users u ON c.instructor_id = u.id 
    WHERE e.student_id = ? AND e.status != 'completed'
");
$stmt->execute([$userId]);
$enrolledCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch completed courses
$stmt = $conn->prepare("
    SELECT c.id, c.title, c.category, c.level, c.course_code, u.username AS author, e.status 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.id 
    JOIN users u ON c.instructor_id = u.id 
    WHERE e.student_id = ? AND e.status = 'completed'
");
$stmt->execute([$userId]);
$completedCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle unenroll
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['unenroll'])) {
    $courseId = $_POST['course_id'];
    $stmt = $conn->prepare("DELETE FROM enrollments WHERE course_id = ? AND student_id = ?");
    $stmt->execute([$courseId, $userId]);
    header("Location: my_courses.php");
    exit();
}

// Handle reset
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset'])) {
    $courseId = $_POST['course_id'];
    $stmt = $conn->prepare("UPDATE enrollments SET status = 'not started' WHERE course_id = ? AND student_id = ?");
    $stmt->execute([$courseId, $userId]);
    header("Location: my_courses.php");
    exit();
}

// Handle resume
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resume'])) {
    $courseId = $_POST['course_id'];
    $stmt = $conn->prepare("UPDATE enrollments SET status = 'in progress' WHERE course_id = ? AND student_id = ?");
    $stmt->execute([$courseId, $userId]);
    header("Location: course_home.php?id=$courseId");
    exit();
}
?>


<div class="row">
    <div class="col-3">
        <?php include'sidebar.php';?>
    </div>
    <div class="col-8">

    <div class="container mt-5">
    <h3>Enrolled Courses</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Course Title</th>
                <th>Category</th>
                <th>Level</th>
                <th>Course Code</th>
                <th>Author</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($enrolledCourses as $index => $course): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($course['title']); ?></td>
                    <td><?php echo htmlspecialchars($course['category']); ?></td>
                    <td><?php echo htmlspecialchars($course['level']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($course['author']); ?></td>
                    <td><?php echo htmlspecialchars($course['status']); ?></td>
                    <td>
                    <form method="post" class="d-inline">
                            <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course['id']); ?>">
                            <button type="submit" name="unenroll" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to unenroll from this course?');">Unenroll</button>
                            <button type="submit" name="resume" class="btn btn-success btn-sm">Resume</button>
                            <button type="submit" name="reset" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to reset the all completed course assessments?');">Reset</button>
                        </form>
                        
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Completed Courses</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Course Title</th>
                <th>Category</th>
                <th>Level</th>
                <th>Course Code</th>
                <th>Author</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($completedCourses as $index => $course): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($course['title']); ?></td>
                    <td><?php echo htmlspecialchars($course['category']); ?></td>
                    <td><?php echo htmlspecialchars($course['level']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($course['author']); ?></td>
                    <td><?php echo htmlspecialchars($course['status']); ?></td>
                    <td>
                        <a href="../course/course_overview.php?id=<?php echo htmlspecialchars($course['id']); ?>" class="btn btn-primary btn-sm">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

    </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

</body>
</html>