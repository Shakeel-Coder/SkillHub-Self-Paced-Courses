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

    // Quiz details
    $quizTitle = $_POST['quiz_title'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $courseCode = $_POST['course_code'];
    $totalMarks = $_POST['total_marks'];
    $status = $_POST['status'];
    $instructorId = $_SESSION['id'];

    // Insert quiz details into database
    $stmt = $conn->prepare("INSERT INTO quizzes (quiz_title, start_date, closing_date, course_code, total_marks, status, instructor_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$quizTitle, $startDate, $endDate, $courseCode, $totalMarks, $status, $instructorId]);
    $quizId = $conn->lastInsertId();

    // Insert questions and options
    foreach ($_POST['questions'] as $index => $question) {
        $questionText = $question['question_text'];
        $optionA = $question['options'][0];
        $optionB = $question['options'][1];
        $optionC = $question['options'][2];
        $optionD = $question['options'][3];
        $correctOption = $question['correct_option'];

        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$quizId, $questionText, $optionA, $optionB, $optionC, $optionD, $correctOption]);
    }

    echo "Quiz created successfully!";
    header('Location: http://localhost/skillhub/instructor/manage_quizzes.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <title>Add New Quiz</title>
</head>
<body>
   

    <div class="row">
        <div class="col-3">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-8">
            <div class="container mt-5">
                <h2>Add New Quiz</h2>
                <form action="add_quiz.php" method="post">
                    <div class="mb-3">
                        <label for="quizTitle" class="form-label">Quiz Title</label>
                        <input type="text" class="form-control" id="quizTitle" name="quiz_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="courseCode" class="form-label">Course Code</label>
                        <input type="text" class="form-control" id="courseCode" name="course_code" required>
                    </div>
                    <div id="questions-container">
                        <div class="question mb-3">
                            <label for="questionText" class="form-label">Question</label>
                            <input type="text" class="form-control" name="questions[0][question_text]" required>
                            <div class="mb-3">
                                <label for="option1" class="form-label">Option 1</label>
                                <input type="text" class="form-control" name="questions[0][options][0]" required>
                            </div>
                            <div class="mb-3">
                                <label for="option2" class="form-label">Option 2</label>
                                <input type="text" class="form-control" name="questions[0][options][1]" required>
                            </div>
                            <div class="mb-3">
                                <label for="option3" class="form-label">Option 3</label>
                                <input type="text" class="form-control" name="questions[0][options][2]" required>
                            </div>
                            <div class="mb-3">
                                <label for="option4" class="form-label">Option 4</label>
                                <input type="text" class="form-control" name="questions[0][options][3]" required>
                            </div>
                            <div class="mb-3">
                                <label for="correctOption" class="form-label">Correct Option</label>
                                <select class="form-control" name="questions[0][correct_option]" required>
                                    <option value="0">Option 1</option>
                                    <option value="1">Option 2</option>
                                    <option value="2">Option 3</option>
                                    <option value="3">Option 4</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-secondary" onclick="addQuestion()">Add Another Question</button>
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
                    
                    <button type="submit" class="btn btn-primary">Create Quiz</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let questionIndex = 1;

        function addQuestion() {
            const questionsContainer = document.getElementById('questions-container');
            const questionTemplate = `
                <div class="question mb-3">
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
                </div>
            `;
            questionsContainer.insertAdjacentHTML('beforeend', questionTemplate);
            questionIndex++;
        }
    </script>

</body>
</html>