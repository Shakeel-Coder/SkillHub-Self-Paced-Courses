<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/course_create.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/yjoomyk0twspeayki2lu4xygw6h5nbjtd5s9r3ip5kpvlnmk/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <title>Course New Create</title>
</head>
<body>



<?php
     require_once '../auth/auth.php';

     include 'menu.php';
 
     // Restrict to logged-in users
     checkAccess();
 
     // Restrict to admins
     checkRole(['instructor']);
     

$courseAdded = false;
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    include '../config/config.php';

    if (isset($_POST['save_course_details'])) {
        // Course details
        $courseTitle = $_POST['course_title'];
        $courseDescription = $_POST['course_description'];
        $courseDuration = $_POST['course_duration'];
        $courseLevel = $_POST['course_level'];
        $courseCategory = $_POST['course_category'];
        $courseCode = $_POST['course_code'];
        $courseThumbnail = $_FILES['course_thumbnail']['name'];
        $status = $_POST['status'];
        $instructorId = $_SESSION['id'];

        // Upload course thumbnail
        $targetDir = '../uploads/';
        $targetFile = $targetDir . basename($courseThumbnail);
        move_uploaded_file($_FILES['course_thumbnail']['tmp_name'], $targetFile);

        // Insert course details into database
        $stmt = $conn->prepare("INSERT INTO courses (title, description, duration, level, category, course_code, thumbnail, instructor_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$courseTitle, $courseDescription, $courseDuration, $courseLevel, $courseCategory, $courseCode, $courseThumbnail, $instructorId, $status]);

        $courseAdded = true;
    }
}
?>

<div class="row">
    <div class="col-3">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="col-8 border border-primary position-relative mx-4 my-4">
        <?php if ($courseAdded): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Course added successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <!-- Course Details Form -->
        <form id="courseDetailsForm" action="add_course.php" method="POST" enctype="multipart/form-data">
            <div>
                <h4>Course Details</h4>
                <hr>
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label">Course Title</label>
                        <input type="text" class="form-control" name="course_title" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Course Description</label>
                        <textarea class="form-control" name="course_description" rows="3" required></textarea>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Duration</label>
                        <input type="text" class="form-control" name="course_duration" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Level</label>
                        <input type="text" class="form-control" name="course_level" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" name="course_category" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Course Code</label>
                        <input type="text" class="form-control" name="course_code" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Thumbnail Image</label>
                        <input type="file" class="form-control" name="course_thumbnail" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status" required>
                            <option value="Draft">Draft</option>
                            <option value="Published">Published</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-4 mb-3">
                <button type="submit" name="save_course_details" class="btn btn-success">Save Course Details</button>
            </div>
        </form>
    </div>
</div>

<?php include "../footer.php"; ?>

<script>
  tinymce.init({
    selector: 'textarea',
    api_key: 'yjoomyk0twspeayki2lu4xygw6h5nbjtd5s9r3ip5kpvlnmk',
    plugins: [
      // Core editing features
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
      // Your account includes a free trial of TinyMCE premium features
      // Early access to document converters
      'importword', 'exportword', 'exportpdf'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
    
  });
</script>

</body>
</html>