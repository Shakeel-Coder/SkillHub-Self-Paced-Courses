<?php

    require_once '../auth/auth.php';

    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to admins
    checkRole(['instructor']);

    

// Database connection
include '../config/config.php';

// Get assignment ID from query parameter
$assignmentId = $_GET['assignment_id'];

// Fetch assignment details
$stmt = $conn->prepare("SELECT * FROM assignments WHERE assignment_id = ?");
$stmt->execute([$assignmentId]);
$assignment = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assignmentTitle = $_POST['assignment_title'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['closing_date'];
    $totalMarks = $_POST['total_marks'];
    $status = $_POST['status'];
    $courseCode = $_POST['course_code'];

    // Check if a new file is uploaded
    if (!empty($_FILES['assignment_file']['name'])) {
        // Save the new file
        $targetDir = "../assignments/";
        $targetFile = $targetDir . basename($_FILES["assignment_file"]["name"]);
        move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $targetFile);

        // Update assignment details in database with new file
        $stmt = $conn->prepare("UPDATE assignments SET assignment_title = ?, start_date = ?, closing_date = ?, total_marks = ?, course_code = ?, assignment_file = ?, status = ? WHERE assignment_id = ?");
        $stmt->execute([$assignmentTitle, $startDate, $endDate, $totalMarks, $courseCode, $targetFile, $status, $assignmentId]);
    } else {
        // Update assignment details in database without changing the file
        $stmt = $conn->prepare("UPDATE assignments SET assignment_title = ?, start_date = ?, closing_date = ?, total_marks = ?, course_code = ?, status = ? WHERE assignment_id = ?");
        $stmt->execute([$assignmentTitle, $startDate, $endDate, $totalMarks, $courseCode, $status, $assignmentId]);
    }

    // Redirect to manage assignments page
    header("Location: manage_assignments.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Update Assignment</title>
</head>
<body>
   

    <div class="row">
        <div class="col-3">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-8">
            <div class="container mt-5">
                <h2>Update Assignment</h2>
                <form action="update_assignment.php?assignment_id=<?php echo $assignmentId; ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="assignmentTitle" class="form-label">Assignment Title</label>
                        <input type="text" class="form-control" id="assignmentTitle" name="assignment_title" value="<?php echo htmlspecialchars($assignment['assignment_title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assignment File</label>
                        <?php if (!empty($assignment['assignment_file'])): ?>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="<?php echo $assignment['assignment_file']; ?>" target="_blank">View Current File</a>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="assignmentFile" class="form-label">Upload File</label>
                        <input type="file" class="form-control" id="assignmentFile" name="assignment_file">
                    </div>
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" value="<?php echo htmlspecialchars($assignment['start_date']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="closing_date" value="<?php echo htmlspecialchars($assignment['closing_date']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="totalMarks" class="form-label">Total Marks</label>
                        <input type="number" class="form-control" id="totalMarks" name="total_marks" value="<?php echo htmlspecialchars($assignment['total_marks']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="courseCode" class="form-label">Course Code</label>
                        <input type="text" class="form-control" id="courseCode" name="course_code" value="<?php echo htmlspecialchars($assignment['course_code']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Published" <?php echo $assignment['status'] == 'Published' ? 'selected' : ''; ?>>Published</option>
                            <option value="Draft" <?php echo $assignment['status'] == 'Draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Assignment</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>