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

// Fetch published courses
$stmt = $conn->prepare("
    SELECT courses.id, courses.title, courses.category, courses.level, courses.course_code, users.firstname, users.lastname
    FROM courses
    JOIN users ON courses.instructor_id = users.id
    WHERE courses.status = 'Published' AND courses.instructor_id = ?
");
$stmt->execute([$instructorId]);
$publishedCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch draft courses
$stmt = $conn->prepare("
    SELECT courses.id, courses.title, courses.category, courses.level, courses.course_code, users.firstname, users.lastname
    FROM courses
    JOIN users ON courses.instructor_id = users.id
    WHERE courses.status = 'Draft' AND courses.instructor_id = ?
");
$stmt->execute([$instructorId]);
$draftCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseId = $_POST['course_id'];
    if (isset($_POST['delete'])) {
        // Delete course
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        $_SESSION['success_message'] = 'Course deleted successfully.';
    } elseif (isset($_POST['publish'])) {
        // Publish course
        $stmt = $conn->prepare("UPDATE courses SET status = 'Published' WHERE id = ?");
        $stmt->execute([$courseId]);
        $_SESSION['success_message'] = 'Course published successfully.';
    } elseif (isset($_POST['draft'])) {
        // Draft course
        $stmt = $conn->prepare("UPDATE courses SET status = 'Draft' WHERE id = ?");
        $stmt->execute([$courseId]);
        $_SESSION['success_message'] = 'Course moved to draft successfully.';
    } elseif (isset($_POST['update'])) {
        // Redirect to update course page
        header("Location: update_course.php?id=$courseId");
        exit();
    }
    // Refresh the page to reflect changes
    header("Location: manage_courses.php");
    exit();
}
?>


<div class="row">
    <div class="col-3">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="col-8">
        <div class="container mt-5">
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
                . $_SESSION['success_message'] .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
            unset($_SESSION['success_message']);
        }
        ?>
            <h2>Manage Courses</h2>
            <div class="mb-3">
                <a href="add_course.php" class="btn btn-success">Add New Course</a>
            </div>
            
            <h3>Published Courses</h3>
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
                    <?php if (empty($publishedCourses)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No published courses found.</td>
                        </tr>
                    <?php else: ?>
                    <?php foreach ($publishedCourses as $index => $course): ?>
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
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?');">Delete</button>
                                    <button type="submit" name="draft" class="btn btn-secondary btn-sm" onclick="return confirm('Are you sure you want to unpublish this course?');">Unpublish</button>
                                    <button type="submit" name="update" class="btn btn-primary btn-sm">Update</button>
                                    <a href="course_material.php?id=<?php echo $course['id']; ?>" class="btn btn-info btn-sm">Add Course Material</a>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <h3>Draft Courses</h3>
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
                    <?php if (empty($draftCourses)): ?>
                        <tr>
                            <td colspan="7" class="text-center">No draft courses found.</td>
                        </tr>
                    <?php else: ?>
                    <?php foreach ($draftCourses as $index => $course): ?>
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
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?');">Delete</button>
                                    <button type="submit" name="publish" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure you want to Publish this course?');">Publish</button>
                                    <button type="submit" name="update" class="btn btn-primary btn-sm">Update</button>
                                    <a href="course_material.php?id=<?php echo $course['id']; ?>" class="btn btn-info btn-sm">Add Course Material</a>
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