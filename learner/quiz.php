
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Quizzes</title>
</head>
<body>

    <?php

    require_once '../auth/auth.php';

    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to admins
    checkRole(['learner']);

// Database connection
include '../config/config.php';

// Fetch quizzes from the database
$stmt = $conn->prepare("
    SELECT 
        q.quiz_id,
        q.quiz_title,
        q.start_date,
        q.closing_date,
        q.total_marks,
        q.course_code,
        qr.submission_time,
        qr.score
    FROM 
        quizzes q
    LEFT JOIN 
        quiz_results qr ON q.quiz_id = qr.quiz_id AND qr.student_id = ?
    WHERE 
        q.status = 'Published'
");
$stmt->execute([$_SESSION['id']]);
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currentTime = new DateTime();

foreach ($quizzes as $quiz) {
    $deadline = new DateTime($quiz['closing_date']);
    $submissionTime = $quiz['submission_time'];

    if ($currentTime > $deadline && !$submissionTime) {
        // Deadline has passed and quiz has not been submitted
        $stmt = $conn->prepare("INSERT INTO quiz_results (quiz_id, student_id, score, submission_time) VALUES (?, ?, 0, NOW())");
        $stmt->execute([$quiz['quiz_id'], $_SESSION['id']]);
        $_SESSION['message'] = 'The quiz deadline has passed. Your score has been recorded as 0 for quiz: ' . htmlspecialchars($quiz['quiz_title']);
    }
}

// Current date
$currentDate = date('Y-m-d');
?>



    <div class="row">
        <div class="col-3">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-8">
            <div class="container mt-5">
                <h2>Quizzes</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Serial Number</th>
                            <th>Quiz Title</th>
                            <th>Take Quiz</th>
                            <th>Start Date</th>
                            <th>Closing Date</th>
                            <th>Total Marks</th>
                            <th>Submission Status</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($quizzes)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No quizzes available.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($quizzes as $index => $quiz): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($quiz['quiz_title']); ?></td>
                                    <?php if ($currentDate > $quiz['closing_date'] || !empty($quiz['submission_time'])): ?>
                                        <td><button class="btn btn-secondary btn-sm" disabled>Take Quiz</button></td>
                                    <?php else: ?>
                                        <td><a href="take_quiz.php?id=<?php echo htmlspecialchars($quiz['quiz_id']); ?>" class="btn btn-primary btn-sm">Take Quiz</a></td>
                                    <?php endif; ?>
                                    <td><?php echo htmlspecialchars($quiz['start_date']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['closing_date']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['total_marks']); ?></td>
                                    <td>
                                        <?php if (!empty($quiz['submission_time'])): ?>
                                            Submitted on <?php echo htmlspecialchars($quiz['submission_time']); ?>
                                        <?php else: ?>
                                            Not Submitted
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($quiz['score']); ?></td>
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