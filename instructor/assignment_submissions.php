<?php

require_once '../auth/auth.php';

// Restrict to logged-in users
checkAccess();

// Restrict to admins
checkRole(['instructor']);



// Database connection
include '../config/config.php';

// Get assignment ID from query parameter
$assignmentId = $_GET['assignment_id'];

// Handle form submission to update obtained marks
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_marks'])) {
    $submissionId = $_POST['submission_id'];
    $obtainedMarks = $_POST['obtained_marks'];

    $updateStmt = $conn->prepare("UPDATE assignment_submission SET obtained_marks = :obtained_marks WHERE id = :id");
    $updateStmt->bindValue(':obtained_marks', $obtainedMarks, PDO::PARAM_INT);
    $updateStmt->bindValue(':id', $submissionId, PDO::PARAM_INT);
    $updateStmt->execute();
}

// Fetch assignment submissions for the specific assignment
$stmt = $conn->prepare("
    SELECT 
        sub.id AS submission_id,
        a.assignment_title, 
        a.course_code,
        u.firstname AS student_firstname, 
        u.lastname AS student_lastname, 
        sub.submission_time, 
        sub.file_path,
        sub.obtained_marks
    FROM 
        assignment_submission sub
    JOIN 
        assignments a ON sub.assignment_id = a.assignment_id
    JOIN 
        users u ON sub.student_id = u.id
    WHERE 
        sub.assignment_id = ?
        AND sub.submission_time = (
            SELECT MAX(sub2.submission_time)
            FROM assignment_submission sub2
            WHERE sub2.assignment_id = sub.assignment_id
            AND sub2.student_id = sub.student_id
        )
    ORDER BY 
        sub.submission_time DESC
");
$stmt->execute([$assignmentId]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Assignment Submissions</title>
</head>
<body>
   

    <div class="row">
        <div class="col-3">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-9">
            <div class="container mt-5">
                <h2>Assignment Submissions</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sr.No.</th>
                            <th>Assignment Title</th>
                            <th>Course Code</th>
                            <th>Student Name</th>
                            <th>Submission Time</th>
                            <th>Submitted File</th>
                            <th>Obtained Marks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($submissions)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No submissions found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($submissions as $index => $submission): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($submission['assignment_title']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['course_code']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['student_firstname'] . ' ' . $submission['student_lastname']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['submission_time']); ?></td>
                                    <td><a href="<?php echo htmlspecialchars($submission['file_path']); ?>" target="_blank">View File</a></td>
                                    <td><?php echo htmlspecialchars($submission['obtained_marks']); ?></td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="submission_id" value="<?php echo $submission['submission_id']; ?>">
                                            <input type="number" name="obtained_marks" value="<?php echo htmlspecialchars($submission['obtained_marks']); ?>" required>
                                            <button type="submit" name="update_marks" class="btn btn-primary btn-sm mt-2" onclick="return confirm('Are you sure you want to update the obtained marks?');">Submit</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" ></script>

</body>
</html>