<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <title>Course New Create</title>
</head>
<body>

<?php
    require_once '../auth/auth.php';

    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to admins
    checkRole(['admin']);

    include '../config/config.php';
    
    // Fetch courses and their instructors
$stmt = $conn->prepare("
SELECT courses.*, users.firstname, users.lastname 
FROM courses 
LEFT JOIN users ON courses.instructor_id = users.id
");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_course'])) {
        $courseId = $_POST['course_id'];
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        $_SESSION['success_message'] = 'Course deleted successfully.';
        header("Location: manage_courses.php");
        exit();
    }
}
    ?>


<div class="row">
	<div class="col-3">
	<?php require "sidebar.php" ?>

	</div>
	<div class="col-8">


	<div class="mt-5">
            <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
                    . $_SESSION['success_message'] .
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
                unset($_SESSION['success_message']);
            }
            ?>
            <h3>Course List</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Course Title</th>
                        <th>Category</th>
                        <th>Level</th>
                        <th>Course Code</th>
                        <th>Author</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($courses as $index => $course): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo htmlspecialchars($course['title']); ?></td>
                <td><?php echo htmlspecialchars($course['category']); ?></td>
                <td><?php echo htmlspecialchars($course['level']); ?></td>
                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                <td><?php echo htmlspecialchars($course['firstname'] . ' ' . $course['lastname']); ?></td>
                <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                <button type="submit" name="delete_course" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?');">Delete</button>
                            </form>
                            <a href="update_course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary btn-sm">Update</a>
                </td>
            </tr>
        <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        

	
    </div>

  


<?php require "../footer.php" ?>

