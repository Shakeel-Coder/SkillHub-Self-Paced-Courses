

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Take Quiz</title>
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

// Get quiz ID from query parameter
$quizId = $_GET['id'];

// Fetch quiz details
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
$stmt->execute([$quizId]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch quiz questions and options
$stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY question_id ASC");
$stmt->execute([$quizId]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studentId = $_SESSION['id'];
    $totalQuestions = count($questions);
    $correctAnswers = 0;

    foreach ($questions as $index => $question) {
        $selectedOption = $_POST['question_' . $question['question_id']];
        if ($selectedOption == $question['correct_option']) {
            $correctAnswers++;
        }

        // Save the student's answer in the student_answers table
        $stmt = $conn->prepare("INSERT INTO student_answers (student_id, quiz_id, question_id, selected_option) VALUES (?, ?, ?, ?)");
        $stmt->execute([$studentId, $quizId, $question['question_id'], $selectedOption]);
    }

    $score = ($correctAnswers / $totalQuestions) * 100;

    // Save the result into quiz_results table
    $stmt = $conn->prepare("INSERT INTO quiz_results (student_id, quiz_id, total_questions, correct_answers, score) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$studentId, $quizId, $totalQuestions, $correctAnswers, $score]);

    // Show result in popup
    echo "<script>
        alert('Quiz Result:\\nTotal Questions: $totalQuestions\\nCorrect Answers: $correctAnswers\\nScore: $score%');
        window.location.href = 'quiz.php';
    </script>";
    exit();
}
?>

    <div class="container mt-5">
        <h2><?php echo htmlspecialchars($quiz['quiz_title']); ?></h2>
        <form action="take_quiz.php?id=<?php echo $quizId; ?>" method="post">
            <?php foreach ($questions as $index => $question): ?>
                <div class="mb-3">
                    <label class="form-label"><?php echo ($index + 1) . '. ' . htmlspecialchars($question['question_text']); ?></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="question_<?php echo $question['question_id']; ?>" value="0" required>
                        <label class="form-check-label"><?php echo htmlspecialchars($question['option_a']); ?></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="question_<?php echo $question['question_id']; ?>" value="1" required>
                        <label class="form-check-label"><?php echo htmlspecialchars($question['option_b']); ?></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="question_<?php echo $question['question_id']; ?>" value="2" required>
                        <label class="form-check-label"><?php echo htmlspecialchars($question['option_c']); ?></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="question_<?php echo $question['question_id']; ?>" value="3" required>
                        <label class="form-check-label"><?php echo htmlspecialchars($question['option_d']); ?></label>
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary">Submit Quiz</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXlHj1/5j4U6i5t9e7k5t5hB5g5i5t9e7k5t5hB5g5i5" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGkQ5Y6z6p+6i5t9e7k5t5hB5g5i5t9e7k5t5hB5g5i5" crossorigin="anonymous"></script>

</body>
</html>