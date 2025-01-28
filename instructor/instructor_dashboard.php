
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Instructor Dashboard</title>
</head>
<body>

<?php
    
    require_once '../auth/auth.php';
    require "menu.php";

    // Restrict to logged-in users
    checkAccess();
    
    // Restrict to admins
    checkRole(['instructor']);
    
    ?>

   
<?php

if(session_status()=== PHP_SESSION_NONE){
    session_start();
}

// Database connection
include '../config/config.php';

$instructorId = $_SESSION['id'];

// Fetch total courses created by this instructor
$stmt = $conn->prepare("SELECT COUNT(*) AS total_courses FROM courses WHERE instructor_id = ?");
$stmt->execute([$instructorId]);
$totalCourses = $stmt->fetch(PDO::FETCH_ASSOC)['total_courses'];


// Fetch total number of students
$stmt = $conn->prepare("SELECT COUNT(*) AS total_students FROM users WHERE roles = 'learner'");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$totalStudents = $result['total_students'];


// Fetch total number of enrolled students in the teacher's courses
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT enrollments.student_id) AS total_enrolled_students
    FROM enrollments
    JOIN courses ON enrollments.course_id = courses.id
    WHERE courses.instructor_id = ?
");
$stmt->execute([$instructorId]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$totalEnrolledStudents = $result['total_enrolled_students'];

// Fetch courses associated with the logged-in teacher
$stmt = $conn->prepare("
    SELECT courses.title, courses.category, courses.level, courses.course_code, users.firstname, users.lastname
    FROM courses
    JOIN users ON courses.instructor_id = users.id
    WHERE courses.instructor_id = ?
");
$stmt->execute([$instructorId]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>






<div class="row">
        <div class="col-3">
        <?php require "sidebar.php" ?>

        </div>
        <div class="col-8">
            <div class=" container text-center position-relative mx-4 my-4">
                <div class="row align-items-start">
                            <div class="col text-bg-primary p-3">
                                <h6>Total Courses</h6>
                                <p><?php echo $totalCourses; ?></p>
                            </div>
                            <div class="col text-bg-info p-3">
                                <h6>Total Students</h6>
                                <p><?php echo $totalStudents; ?></p>
                            </div>
                            <div class="col text-bg-success p-3">
                                <h6>Enrolled Students</h6>
                                <p><?php echo $totalEnrolledStudents; ?></p>
                            </div>
                </div>

                <div class="mt-5">
            <h3>My Courses List</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Course Title</th>
                        <th>Category</th>
                        <th>Level</th>
                        <th>Course Code</th>
                        <th>Author</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($courses as $index => $course): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo htmlspecialchars($course['title']); ?></td>
                <td><?php echo htmlspecialchars($course['category']); ?></td>
                <td><?php echo htmlspecialchars($course['level']); ?></td>
                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                <td><?php echo htmlspecialchars($course['firstname'] . ' ' . $course['lastname']); ?></td>
            </tr>
        <?php endforeach; ?>
                </tbody>
            </table>
        </div>


            </div>

        </div>
    
</div>




  



<?php include "../footer.php"; ?>

</body>
</html>