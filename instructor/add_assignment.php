<?php
 
 
    require_once '../auth/auth.php';

    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to admins
    checkRole(['instructor']);
    
    


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    include '../config/config.php';

    // Assignment details
    $assignmentTitle = $_POST['assignment_title'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['closing_date'];
    $totalMarks = $_POST['total_marks'];
    $status = $_POST['status'];
    $courseCode = $_POST['course_code'];
    $instructorId = $_SESSION['id'];

    // File upload
    $targetDir = "../assignments/";
    $targetFile = $targetDir . basename($_FILES["assignment_file"]["name"]);
    move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $targetFile);

    // Insert assignment details into database
    $stmt = $conn->prepare("INSERT INTO assignments (assignment_title, start_date, closing_date, total_marks, status, course_code, assignment_file, instructor_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$assignmentTitle, $startDate, $endDate, $totalMarks, $status, $courseCode, $targetFile, $instructorId]);

    echo "Assignment created successfully!";
    header('Location: http://localhost/skillhub/instructor/manage_assignments.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Manage Assignments</title>
</head>
<body>

    

    <div class="row">
        <div class="col-3">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-8">
            <div class="container mt-5">
                <h2>Manage Assignments</h2>
                <form action="add_assignment.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="assignmentTitle" class="form-label">Assignment Title</label>
                        <input type="text" class="form-control" id="assignmentTitle" name="assignment_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="assignmentFile" class="form-label">Upload File</label>
                        <input type="file" class="form-control" id="assignmentFile" name="assignment_file" required>
                    </div>
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="closingDate" class="form-label">Closing Date</label>
                        <input type="date" class="form-control" id="closingDate" name="closing_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="totalMarks" class="form-label">Total Marks</label>
                        <input type="number" class="form-control" id="totalMarks" name="total_marks" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Published">Published</option>
                            <option value="Draft">Draft</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="courseCode" class="form-label">Course Code</label>
                        <input type="text" class="form-control" id="courseCode" name="course_code" required>
                    </div>
                    <button type="submit" class="btn btn-primary mb-3">Create Assignment</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>