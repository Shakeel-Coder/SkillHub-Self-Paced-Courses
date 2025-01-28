<?php


    require_once '../auth/auth.php';

    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to admins
    checkRole(['instructor']);
    

// Database connection
include '../config/config.php';

// Get quiz ID from query parameter
$quizId = $_GET['quiz_id'];

// Fetch quiz submissions
$stmt = $conn->prepare("
    SELECT 
        q.quiz_title, 
        q.course_code, 
        u.firstname AS student_firstname, 
        u.lastname AS student_lastname, 
        qr.submission_time, 
        qr.score
    FROM 
        quiz_results qr
    JOIN 
        quizzes q ON qr.quiz_id = q.quiz_id
    JOIN 
        users u ON qr.student_id = u.id
    WHERE 
        qr.quiz_id = ?
    ORDER BY 
        qr.submission_time DESC
");
$stmt->execute([$quizId]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Quiz Submissions</title>
</head>
<body>
   

    <div class="row">
        <div class="col-3">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-8">
            <div class="container mt-5">
                <h2>Quiz Submissions</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Quiz Title</th>
                            <th>Course Code</th>
                            <th>Student Name</th>
                            <th>Submit Time</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($submissions)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No submissions found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($submissions as $index => $submission): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($submission['quiz_title']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['course_code']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['student_firstname'] . ' ' . $submission['student_lastname']); ?></td>
                                    <td><?php echo htmlspecialchars($submission['submission_time']); ?></td>
                                    <td><?php echo isset($submission['score']) ? htmlspecialchars($submission['score'])  : 'N/A'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXlHj1/5j4U6i5t9e7k5t5hB5g5i5t9e7k5t5hB5g5i5" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGkQ5Y6z6p+6i5t9e7k5t5hB5g5i5t9e7k5t5hB5g5i5" crossorigin="anonymous"></script>

</body>
</html>