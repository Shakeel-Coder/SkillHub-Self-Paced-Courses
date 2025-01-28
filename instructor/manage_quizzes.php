
<?php


    require_once '../auth/auth.php';

    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to admins
    checkRole(['instructor']);
    
    

// Database connection
include '../config/config.php';

$instructorId = $_SESSION['id'];

// Fetch published quizzes
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE status = 'Published' AND instructor_id = ?");
$stmt->execute([$instructorId]);
$publishedQuizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch draft quizzes
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE status = 'Draft' AND instructor_id = ?");
$stmt->execute([$instructorId]);
$draftQuizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quizId = $_POST['quiz_id'];
    if (isset($_POST['delete'])) {
        // Delete quiz
        $stmt = $conn->prepare("DELETE FROM quizzes WHERE quiz_id = ?");
        $stmt->execute([$quizId]);
    } elseif (isset($_POST['publish'])) {
        // Publish quiz
        $stmt = $conn->prepare("UPDATE quizzes SET status = 'Published' WHERE quiz_id = ?");
        $stmt->execute([$quizId]);
    } elseif (isset($_POST['unpublish'])) {
        // Unpublish quiz
        $stmt = $conn->prepare("UPDATE quizzes SET status = 'Draft' WHERE quiz_id = ?");
        $stmt->execute([$quizId]);
    } elseif (isset($_POST['update'])) {
        // Redirect to update quiz page
        header("Location: update_quiz.php?quiz_id=$quizId");
        exit();
    } elseif (isset($_POST['submission'])) {
        // Redirect to quiz submissions page
        header("Location: quiz_submissions.php?quiz_id=$quizId");
        exit();
    }
    // Refresh the page to reflect changes
    header("Location: manage_quizzes.php");
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
    <title>Manage Quizzes</title>
</head>
<body>

    <div class="row">
        <div class="col-3">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-8">
            <div class="container mt-5">
                <h2>Manage Quizzes</h2>
                <div class="mb-3">
                    <a href="add_quiz.php" class="btn btn-success">Add New Quiz</a>
                </div>

                <h3>Published Quizzes</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Serial Number</th>
                            <th>Quiz Title</th>
                            <th>Start Date</th>
                            <th>Closing Date</th>
                            <th>Total Marks</th>
                            <th>Course Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($publishedQuizzes)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No published quizzes found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($publishedQuizzes as $index => $quiz): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($quiz['quiz_title']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['start_date']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['closing_date']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['total_marks']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['course_code']); ?></td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="quiz_id" value="<?php echo $quiz['quiz_id']; ?>">
                                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this quiz?');">Delete</button>
                                            <button type="submit" name="unpublish" class="btn btn-secondary btn-sm" onclick="return confirm('Are you sure you want to un-publish this quiz?');">Unpublish</button>
                                            <button type="submit" name="update" class="btn btn-primary btn-sm">Update</button>
                                            <button type="submit" name="submission" class="btn btn-info btn-sm">Submissions</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <h3>Draft Quizzes</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Serial Number</th>
                            <th>Quiz Title</th>
                            <th>Start Date</th>
                            <th>Closing Date</th>
                            <th>Total Marks</th>
                            <th>Course Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($draftQuizzes)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No draft quizzes found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($draftQuizzes as $index => $quiz): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($quiz['quiz_title']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['start_date']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['closing_date']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['total_marks']); ?></td>
                                    <td><?php echo htmlspecialchars($quiz['course_code']); ?></td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="quiz_id" value="<?php echo $quiz['quiz_id']; ?>">
                                            <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this quiz?');">Delete</button>
                                            <button type="submit" name="publish" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure you want to publish this quiz?');">Publish</button>
                                            <button type="submit" name="update" class="btn btn-primary btn-sm">Update</button>
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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXlHj1/5j4U6i5t9e7k5t5hB5g5i5t9e7k5t5hB5g5i5" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGkQ5Y6z6p+6i5t9e7k5t5hB5g5i5t9e7k5t5hB5g5i5" crossorigin="anonymous"></script>

</body>
</html>