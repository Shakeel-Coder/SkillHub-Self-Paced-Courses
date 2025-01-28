
<?php

    require_once '../auth/auth.php';

    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to admins
    checkRole(['instructor']);
    


include '../config/config.php';

// Start session
$courseId = $_GET['id'];

// Fetch course details
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$courseId]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch curriculum
$stmt = $conn->prepare("SELECT * FROM course_curriculum WHERE course_id = ?");
$stmt->execute([$courseId]);
$curriculum = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch videos
$stmt = $conn->prepare("SELECT * FROM course_videos WHERE course_id = ?");
$stmt->execute([$courseId]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recommended books
$stmt = $conn->prepare("SELECT * FROM books WHERE course_id = ?");
$stmt->execute([$courseId]);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch important links
$stmt = $conn->prepare("SELECT * FROM important_links WHERE course_id = ?");
$stmt->execute([$courseId]);
$links = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch resources
$stmt = $conn->prepare("SELECT * FROM resources WHERE course_id = ?");
$stmt->execute([$courseId]);
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch announcements
$stmt = $conn->prepare("SELECT * FROM announcements WHERE course_id = ?");
$stmt->execute([$courseId]);
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_course_details'])) {
        $courseId = $_GET['id'];
        $courseTitle = $_POST['course_title'];
        $courseDescription = $_POST['course_description'];
        $duration = $_POST['duration'];
        $level = $_POST['level'];
        $category = $_POST['category'];
        $courseCode = $_POST['course_code'];
        $status = $_POST['status'];

        // Handle thumbnail image upload
        if (isset($_FILES['thumbnail_image']) && $_FILES['thumbnail_image']['error'] == UPLOAD_ERR_OK) {
            $thumbnailImage = $_FILES['thumbnail_image']['name'];
            $targetDir = "../uploads/";
            $targetFile = $targetDir . basename($thumbnailImage);
            move_uploaded_file($_FILES['thumbnail_image']['tmp_name'], $targetFile);

            // Update course details with thumbnail image
            $stmt = $conn->prepare("UPDATE courses SET title = ?, description = ?, duration = ?, level = ?, category = ?, course_code = ?, status = ?, thumbnail = ? WHERE id = ?");
            $stmt->execute([$courseTitle, $courseDescription, $duration, $level, $category, $courseCode, $status, $thumbnailImage, $courseId]);
        } else {
            // Update course details without thumbnail image
            $stmt = $conn->prepare("UPDATE courses SET title = ?, description = ?, duration = ?, level = ?, category = ?, course_code = ?, status = ? WHERE id = ?");
            $stmt->execute([$courseTitle, $courseDescription, $duration, $level, $category, $courseCode, $status, $courseId]);
        }
        $_SESSION['success_message'] = 'Course details updated successfully.';
    }

    elseif (isset($_POST['update_videos'])) {
        // Handle video updates
        foreach ($_POST['video_titles'] as $index => $videoTitle) {
            if (!empty($_FILES['video_files']['name'][$index])) {
                $videoFile = $_FILES['video_files']['name'][$index];
                $videoTargetFile = "../uploads/" . basename($videoFile);
                move_uploaded_file($_FILES['video_files']['tmp_name'][$index], $videoTargetFile);
                $stmt = $conn->prepare("UPDATE course_videos SET title = ?, file = ? WHERE id = ?");
                $stmt->execute([$videoTitle, $videoTargetFile, $_POST['video_ids'][$index]]);
            } else {
                // Update video title only
                $stmt = $conn->prepare("UPDATE course_videos SET title = ? WHERE id = ?");
                $stmt->execute([$videoTitle, $_POST['video_ids'][$index]]);
            }
        }

        $_SESSION['success_message'] = "Videos update successfully!";

    } elseif (isset($_POST['update_curriculum'])) {
        // Handle curriculum updates
        foreach ($_POST['chapter_titles'] as $index => $chapterTitle) {
            $lesson = $_POST['lessons'][$index];
            $stmt = $conn->prepare("UPDATE course_curriculum SET chapter_title = ?, lessons = ? WHERE id = ?");
            $stmt->execute([$chapterTitle, $lesson, $_POST['curriculum_ids'][$index]]);
        }

        $_SESSION['success_message'] = "Curriculum update successfully!";

    } elseif (isset($_POST['update_books'])) {
        // Handle books updates
        foreach ($_POST['book_titles'] as $index => $bookTitle) {
            $author = $_POST['book_authors'][$index];
            $stmt = $conn->prepare("UPDATE books SET title = ?, author = ? WHERE id = ?");
            $stmt->execute([$bookTitle, $author, $_POST['book_ids'][$index]]);
        }

        $_SESSION['success_message'] = "Books update successfully!";

    } elseif (isset($_POST['update_links'])) {
        // Handle important links updates
        foreach ($_POST['link_titles'] as $index => $linkTitle) {
            $url = $_POST['link_urls'][$index];
            $stmt = $conn->prepare("UPDATE important_links SET title = ?, url = ? WHERE id = ?");
            $stmt->execute([$linkTitle, $url, $_POST['link_ids'][$index]]);
        }

        $_SESSION['success_message'] = "Links update successfully!";

    } elseif (isset($_POST['update_resources'])) {
        // Handle resources updates
        foreach ($_POST['resource_titles'] as $index => $resourceTitle) {
            if (!empty($_FILES['resource_files']['name'][$index])) {
                $resourceFile = $_FILES['resource_files']['name'][$index];
                $resourceTargetFile = "../uploads/" . basename($resourceFile);
                move_uploaded_file($_FILES['resource_files']['tmp_name'][$index], $resourceTargetFile);
                $stmt = $conn->prepare("UPDATE resources SET title = ?, file = ? WHERE id = ?");
                $stmt->execute([$resourceTitle, $resourceTargetFile, $_POST['resource_ids'][$index]]);
            } else {
                // Update resource title only
                $stmt = $conn->prepare("UPDATE resources SET title = ? WHERE id = ?");
                $stmt->execute([$resourceTitle, $_POST['resource_ids'][$index]]);
            }
            
        }
        $_SESSION['success_message'] = "Resources update successfully!";

    } elseif (isset($_POST['update_announcements'])) {
        // Handle announcements updates
        foreach ($_POST['announcement_titles'] as $index => $announcementTitle) {
            $message = $_POST['announcement_messages'][$index];
            $stmt = $conn->prepare("UPDATE announcements SET title = ?, message = ? WHERE id = ?");
            $stmt->execute([$announcementTitle, $message, $_POST['announcement_ids'][$index]]);
        }
        $_SESSION['success_message'] = "Announcements update successfully!";
    }

    // Redirect to update course page
    header("Location: update_course.php?id=$courseId");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Update Course</title>
    <script src="https://cdn.ckeditor.com/4.25.0/standard/ckeditor.js"></script>
</head>
<body>



<div class="row">
    <div class="col-3">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="col-9">

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

            <h2>Update Course: <?php echo htmlspecialchars($course['title']); ?></h2>

            <!-- Basic Course Details Form -->
            <form method="post" enctype="multipart/form-data">
                <h3>Course Details</h3>
                <div class="mb-3">
                    <label for="course_title" class="form-label">Course Title</label>
                    <input type="text" class="form-control" id="course_title" name="course_title" value="<?php echo htmlspecialchars($course['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="course_description" class="form-label">Course Description</label>
                    <textarea class="form-control" id="course_description" name="course_description" rows="4" required><?php echo htmlspecialchars($course['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="duration" class="form-label">Duration</label>
                    <input type="text" class="form-control" id="duration" name="duration" value="<?php echo htmlspecialchars($course['duration']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="level" class="form-label">Level</label>
                    <input type="text" class="form-control" id="level" name="level" value="<?php echo htmlspecialchars($course['level']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Category</label>
                    <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($course['category']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="course_code" class="form-label">Course Code</label>
                    <input type="text" class="form-control" id="course_code" name="course_code" value="<?php echo htmlspecialchars($course['course_code']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="thumbnail_image" class="form-label">Thumbnail Image</label>
                    <input type="file" class="form-control" id="thumbnail_image" name="thumbnail_image">
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="publish" <?php echo $course['status'] == 'publish' ? 'selected' : ''; ?>>Publish</option>
                        <option value="draft" <?php echo $course['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                    </select>
                </div>
                <button type="submit" name="update_course_details" class="btn btn-primary">Update Course Details</button>
            </form>

            <!-- Course Videos Form -->
            <form method="post" enctype="multipart/form-data">
                <h3>Course Videos</h3>
                <?php foreach ($videos as $index => $video): ?>
                    <div class="mb-3">
                        <label for="video_title_<?php echo $index; ?>" class="form-label">Video Title</label>
                        <input type="text" class="form-control" id="video_title_<?php echo $index; ?>" name="video_titles[]" value="<?php echo htmlspecialchars($video['title']); ?>">
                        <label for="video_file_<?php echo $index; ?>" class="form-label">Video File</label>
                        <input type="file" class="form-control" id="video_file_<?php echo $index; ?>" name="video_files[]">
                        <input type="hidden" name="video_ids[]" value="<?php echo $video['id']; ?>">
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="update_videos" class="btn btn-primary">Update Videos</button>
            </form>

            <!-- Curriculum Form -->
            <form method="post">
                <h3>Curriculum</h3>
                <?php foreach ($curriculum as $index => $chapter): ?>
                    <div class="mb-3">
                        <label for="chapter_title_<?php echo $index; ?>" class="form-label">Chapter Title</label>
                        <input type="text" class="form-control" id="chapter_title_<?php echo $index; ?>" name="chapter_titles[]" value="<?php echo htmlspecialchars($chapter['chapter']); ?>">
                        <label for="lesson_<?php echo $index; ?>" class="form-label">Lesson</label>
                        <textarea class="form-control" id="lesson_<?php echo $index; ?>" name="lessons[]"><?php echo htmlspecialchars($chapter['lessons']); ?></textarea>
                        <input type="hidden" name="curriculum_ids[]" value="<?php echo $chapter['id']; ?>">
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="update_curriculum" class="btn btn-primary">Update Curriculum</button>
            </form>

            <!-- Reference Books Form -->
            <form method="post">
                <h3>Reference Books</h3>
                <?php foreach ($books as $index => $book): ?>
                    <div class="mb-3">
                        <label for="book_title_<?php echo $index; ?>" class="form-label">Book Title</label>
                        <input type="text" class="form-control" id="book_title_<?php echo $index; ?>" name="book_titles[]" value="<?php echo htmlspecialchars($book['title']); ?>">
                        <label for="book_author_<?php echo $index; ?>" class="form-label">Author</label>
                        <input type="text" class="form-control" id="book_author_<?php echo $index; ?>" name="book_authors[]" value="<?php echo htmlspecialchars($book['author']); ?>">
                        <input type="hidden" name="book_ids[]" value="<?php echo $book['id']; ?>">
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="update_books" class="btn btn-primary">Update Books</button>
            </form>

            <!-- Important Links Form -->
            <form method="post">
                <h3>Important Links</h3>
                <?php foreach ($links as $index => $link): ?>
                    <div class="mb-3">
                        <label for="link_title_<?php echo $index; ?>" class="form-label">Link Title</label>
                        <input type="text" class="form-control" id="link_title_<?php echo $index; ?>" name="link_titles[]" value="<?php echo htmlspecialchars($link['title']); ?>">
                        <label for="link_url_<?php echo $index; ?>" class="form-label">URL</label>
                        <input type="text" class="form-control" id="link_url_<?php echo $index; ?>" name="link_urls[]" value="<?php echo htmlspecialchars($link['url']); ?>">
                        <input type="hidden" name="link_ids[]" value="<?php echo $link['id']; ?>">
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="update_links" class="btn btn-primary">Update Links</button>
            </form>

            <!-- Resources Form -->
            <form method="post" enctype="multipart/form-data">
                <h3>Resources</h3>
                <?php foreach ($resources as $index => $resource): ?>
                    <div class="mb-3">
                        <label for="resource_title_<?php echo $index; ?>" class="form-label">Resource Title</label>
                        <input type="text" class="form-control" id="resource_title_<?php echo $index; ?>" name="resource_titles[]" value="<?php echo htmlspecialchars($resource['title']); ?>">
                        <label for="resource_file_<?php echo $index; ?>" class="form-label">Resource File</label>
                        <input type="file" class="form-control" id="resource_file_<?php echo $index; ?>" name="resource_files[]">
                        <input type="hidden" name="resource_ids[]" value="<?php echo $resource['id']; ?>">
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="update_resources" class="btn btn-primary">Update Resources</button>
            </form>

            <!-- Announcements Form -->
            <form method="post">
                <h3>Announcements</h3>
                <?php foreach ($announcements as $index => $announcement): ?>
                    <div class="mb-3">
                        <label for="announcement_title_<?php echo $index; ?>" class="form-label">Announcement Title</label>
                        <input type="text" class="form-control" id="announcement_title_<?php echo $index; ?>" name="announcement_titles[]" value="<?php echo htmlspecialchars($announcement['title']); ?>">
                        <label for="announcement_message_<?php echo $index; ?>" class="form-label">Message</label>
                        <textarea class="form-control" id="announcement_message_<?php echo $index; ?>" name="announcement_messages[]"><?php echo htmlspecialchars($announcement['message']); ?></textarea>
                        <input type="hidden" name="announcement_ids[]" value="<?php echo $announcement['id']; ?>">
                    </div>
                <?php endforeach; ?>
                <button type="submit" name="update_announcements" class="btn btn-primary">Update Announcements</button>
            </form>
        </div>

    </div>
</div>






<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

</body>
</html>