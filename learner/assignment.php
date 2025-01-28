<?php

    require_once '../auth/auth.php';

    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to admins
    checkRole(['learner']);

// Database connection
include '../config/config.php';

// Fetch published assignments
$stmt = $conn->prepare("SELECT a.*, s.obtained_marks, s.submission_time, s.file_path 
    FROM assignments a
    LEFT JOIN assignment_submission s ON a.assignment_id = s.assignment_id AND s.student_id = ?
    WHERE a.status = 'Published'");
$stmt->execute([$_SESSION['id']]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle file submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_assignment'])) {
    $assignmentId = $_POST['assignment_id'];
    $studentId = $_POST['student_id'];
    $targetDir = "../submissions/";
    $targetFile = $targetDir . basename($_FILES["assignment_file"]["name"]);

    if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $targetFile)) {
        try {
            // Insert new file submission into database
            $stmt = $conn->prepare("INSERT INTO assignment_submission (assignment_id, student_id, file_path) VALUES (?, ?, ?)");
            $stmt->execute([$assignmentId, $studentId, $targetFile]);

            // Set success message
            $successMessage = "File submitted successfully!";
        } catch (PDOException $e) {
            $errorMessage = "Database error: " . $e->getMessage();
        }
    } else {
        $errorMessage = "Failed to upload file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Assignments</title>
</head>
<body>
   

    <div class="row">
        <div class="col-3">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-8">

        <div class="container mt-5">

        <h2>Assignments</h2>
            <?php if (isset($successMessage)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $successMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif (isset($errorMessage)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $errorMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Title</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Marks</th>
                        <th>Actions</th>
                        <th>Result</th>
                        <th>Last Submission</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assignments)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No published assignments found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($assignments as $index => $assignment): ?>
                            <?php
                            $currentTime = new DateTime();
                            $startTime = new DateTime($assignment['start_date']);
                            $endTime = new DateTime($assignment['closing_date']);
                            $endTime->modify('+1 day'); // Extend the end time to the end of the closing date
                            $isDisabled = $currentTime < $startTime || $currentTime >= $endTime;
                            ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($assignment['assignment_title']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['closing_date']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['total_marks']); ?></td>
                                <td>
                                    <form class="assignment-form" action="assignment.php" method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                                        <input type="hidden" name="student_id" value="<?php echo $_SESSION['id']; ?>">
                                        <input type="file" name="assignment_file" <?php echo $isDisabled ? 'disabled' : ''; ?> required>
                                        <button type="submit" name="submit_assignment" class="btn btn-primary btn-sm" <?php echo $isDisabled ? 'disabled' : ''; ?>>Submit</button>
                                    </form>
                                </td>
                                <td><?php echo htmlspecialchars($assignment['obtained_marks']); ?></td>
                                <td>
                                    <?php
                                    $submissionStmt = $conn->prepare("SELECT * FROM assignment_submission WHERE assignment_id = ? ORDER BY submission_time DESC LIMIT 1");
                                    $submissionStmt->execute([$assignment['assignment_id']]);
                                    $submission = $submissionStmt->fetch(PDO::FETCH_ASSOC);
                                    if ($submission) {
                                        echo 'Submitted on ' . htmlspecialchars($submission['submission_time']) . '<br>';
                                        echo '<a href="' . htmlspecialchars($submission['file_path']) . '" target="_blank">View Submitted File</a>';
                                    } else {
                                        echo 'Not Submitted';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>


        </div>

            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" "></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" "></script>

</body>
</html>