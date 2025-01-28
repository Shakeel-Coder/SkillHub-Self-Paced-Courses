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

// Fetch quiz details
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE quiz_id = ?");
$stmt->execute([$quizId]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch quiz questions and options
$stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
$stmt->execute([$quizId]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete_question'])) {
    // Quiz details
    $quizTitle = $_POST['quiz_title'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $courseCode = $_POST['course_code'];
    $totalMarks = $_POST['total_marks'];
    $status = $_POST['status'];

    // Update quiz details in database
    $stmt = $conn->prepare("UPDATE quizzes SET quiz_title = ?, start_date = ?, closing_date = ?, course_code = ?, total_marks = ?, status = ? WHERE quiz_id = ?");
    $stmt->execute([$quizTitle, $startDate, $endDate, $courseCode, $totalMarks, $status, $quizId]);

    // Update existing questions and options
    foreach ($_POST['questions'] as $index => $question) {
        if (isset($question['question_id'])) {
            // Update existing question
            $questionId = $question['question_id'];
            $questionText = $question['question_text'];
            $optionA = $question['options'][0];
            $optionB = $question['options'][1];
            $optionC = $question['options'][2];
            $optionD = $question['options'][3];
            $correctOption = $question['correct_option'];

            $stmt = $conn->prepare("UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ? WHERE question_id = ?");
            $stmt->execute([$questionText, $optionA, $optionB, $optionC, $optionD, $correctOption, $questionId]);
        } else {
            // Insert new question
            $questionText = $question['question_text'];
            $optionA = $question['options'][0];
            $optionB = $question['options'][1];
            $optionC = $question['options'][2];
            $optionD = $question['options'][3];
            $correctOption = $question['correct_option'];

            $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$quizId, $questionText, $optionA, $optionB, $optionC, $optionD, $correctOption]);
        }
    }

    echo "Quiz updated successfully!";
    header('Location: manage_quizzes.php');
    exit();
}

// Handle question deletion via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_question'])) {
    $questionId = $_POST['question_id'];
    $stmt = $conn->prepare("DELETE FROM questions WHERE question_id = ?");
    $stmt->execute([$questionId]);
    echo "Question deleted successfully!";
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
    <title>Update Quiz</title>
</head>
<body>
  

    <div class="row">
        <div class="col-3">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-8">
            <div class="container mt-5">
                <h2>Update Quiz</h2>
                <form action="update_quiz.php?quiz_id=<?php echo $quizId; ?>" method="post">
                    <div class="mb-3">
                        <label for="quizTitle" class="form-label">Quiz Title</label>
                        <input type="text" class="form-control" id="quizTitle" name="quiz_title" value="<?php echo htmlspecialchars($quiz['quiz_title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" value="<?php echo htmlspecialchars($quiz['start_date']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="end_date" value="<?php echo htmlspecialchars($quiz['closing_date']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="courseCode" class="form-label">Course Code</label>
                        <input type="text" class="form-control" id="courseCode" name="course_code" value="<?php echo htmlspecialchars($quiz['course_code']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="totalMarks" class="form-label">Total Marks</label>
                        <input type="number" class="form-control" id="totalMarks" name="total_marks" value="<?php echo htmlspecialchars($quiz['total_marks']); ?>" required>
                    </div>
                    <div id="questions-container">
                        <?php foreach ($questions as $index => $question): ?>
                            <div class="question mb-3" id="question-<?php echo $question['question_id']; ?>">
                                <input type="hidden" name="questions[<?php echo $index; ?>][question_id]" value="<?php echo $question['question_id']; ?>">
                                <label for="questionText" class="form-label">Question</label>
                                <input type="text" class="form-control" name="questions[<?php echo $index; ?>][question_text]" value="<?php echo htmlspecialchars($question['question_text']); ?>" required>
                                <div class="mb-3">
                                    <label for="option1" class="form-label">Option 1</label>
                                    <input type="text" class="form-control" name="questions[<?php echo $index; ?>][options][0]" value="<?php echo htmlspecialchars($question['option_a']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="option2" class="form-label">Option 2</label>
                                    <input type="text" class="form-control" name="questions[<?php echo $index; ?>][options][1]" value="<?php echo htmlspecialchars($question['option_b']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="option3" class="form-label">Option 3</label>
                                    <input type="text" class="form-control" name="questions[<?php echo $index; ?>][options][2]" value="<?php echo htmlspecialchars($question['option_c']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="option4" class="form-label">Option 4</label>
                                    <input type="text" class="form-control" name="questions[<?php echo $index; ?>][options][3]" value="<?php echo htmlspecialchars($question['option_d']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="correctOption" class="form-label">Correct Option</label>
                                    <select class="form-control" name="questions[<?php echo $index; ?>][correct_option]" required>
                                        <option value="0" <?php echo $question['correct_option'] == '0' ? 'selected' : ''; ?>>Option 1</option>
                                        <option value="1" <?php echo $question['correct_option'] == '1' ? 'selected' : ''; ?>>Option 2</option>
                                        <option value="2" <?php echo $question['correct_option'] == '2' ? 'selected' : ''; ?>>Option 3</option>
                                        <option value="3" <?php echo $question['correct_option'] == '3' ? 'selected' : ''; ?>>Option 4</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-danger" onclick="deleteQuestion(<?php echo $question['question_id']; ?>)">Delete Question</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-secondary" onclick="addQuestion()">Add Another Question</button>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Published" <?php echo $quiz['status'] == 'Published' ? 'selected' : ''; ?>>Published</option>
                            <option value="Draft" <?php echo $quiz['status'] == 'Draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Quiz</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let questionIndex = <?php echo count($questions); ?>;

        function addQuestion() {
            const questionsContainer = document.getElementById('questions-container');
            const questionTemplate = `
                <div class="question mb-3" id="question-new-${questionIndex}">
                    <label for="questionText" class="form-label">Question</label>
                    <input type="text" class="form-control" name="questions[${questionIndex}][question_text]" required>
                    <div class="mb-3">
                        <label for="option1" class="form-label">Option 1</label>
                        <input type="text" class="form-control" name="questions[${questionIndex}][options][0]" required>
                    </div>
                    <div class="mb-3">
                        <label for="option2" class="form-label">Option 2</label>
                        <input type="text" class="form-control" name="questions[${questionIndex}][options][1]" required>
                    </div>
                    <div class="mb-3">
                        <label for="option3" class="form-label">Option 3</label>
                        <input type="text" class="form-control" name="questions[${questionIndex}][options][2]" required>
                    </div>
                    <div class="mb-3">
                        <label for="option4" class="form-label">Option 4</label>
                        <input type="text" class="form-control" name="questions[${questionIndex}][options][3]" required>
                    </div>
                    <div class="mb-3">
                        <label for="correctOption" class="form-label">Correct Option</label>
                        <select class="form-control" name="questions[${questionIndex}][correct_option]" required>
                            <option value="0">Option 1</option>
                            <option value="1">Option 2</option>
                            <option value="2">Option 3</option>
                            <option value="3">Option 4</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-danger" onclick="removeNewQuestion(${questionIndex})">Delete Question</button>
                </div>
            `;
            questionsContainer.insertAdjacentHTML('beforeend', questionTemplate);
            questionIndex++;
        }

        function deleteQuestion(questionId) {
            if (confirm('Are you sure you want to delete this question?')) {
                const questionElement = document.getElementById(`question-${questionId}`);
                questionElement.remove();

                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_quiz.php?quiz_id=<?php echo $quizId; ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        console.log(xhr.responseText);
                    }
                };
                xhr.send('delete_question=1&question_id=' + questionId);
            }
        }

        function removeNewQuestion(index) {
            const questionElement = document.getElementById(`question-new-${index}`);
            questionElement.remove();
        }
    </script>

</body>
</html>