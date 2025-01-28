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
    checkRole(['admin']);
    

    include '../config/config.php';

    // Fetch total courses
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_courses FROM courses");
    $stmt->execute();
    $totalCourses = $stmt->fetch(PDO::FETCH_ASSOC)['total_courses'];

    // Fetch total instructors
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_instructors FROM users WHERE roles = 'instructor'");
    $stmt->execute();
    $totalInstructors = $stmt->fetch(PDO::FETCH_ASSOC)['total_instructors'];

    // Fetch total learners
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_learners FROM users WHERE roles = 'learner'");
    $stmt->execute();
    $totalLearners = $stmt->fetch(PDO::FETCH_ASSOC)['total_learners'];

    // Fetch courses and their instructors
    $stmt = $conn->prepare("
    SELECT c.*, u.firstname, u.lastname 
    FROM courses c 
    JOIN users u ON c.instructor_id = u.id
    ");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Fetch users details
    $stmt = $conn->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

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
                        <p><?php echo $totalCourses; ?>+</p>
                    </div>
                    <div class="col text-bg-info p-3">
                        <h6>Total Instructors</h6>
                        <p><?php echo $totalInstructors; ?>+</p>
                    </div>
                    <div class="col text-bg-success p-3">
                        <h6>Total Learners</h6>
                        <p><?php echo $totalLearners; ?>+</p>
                    </div>
        </div>
        <div class="mt-5">
            <h3>Course List</h3>
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
                <td><?php echo htmlspecialchars($course['firstname'] . ' ' . htmlspecialchars($course['lastname'])); ?></td>
            </tr>
        <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        

        <!-- User list table start here -->

        <div class="mt-5">
    <h3>User List</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Serial No.</th>
                <th>User Image</th>
                <th>User Name</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $serialNo = 1;
            foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $serialNo++; ?></td>
                    <td><img src="../uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" class="rounded img" alt="User" width="50" height="50"></td>
                    <td><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['roles']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

        <!-- user list table end here -->


   </div>

    </div>
</div>





<?php include "../footer.php"; ?>



</body>
</html>