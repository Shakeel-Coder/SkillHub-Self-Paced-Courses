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

    
    ?>


<?php

// Get assignment ID from query parameter
$courseId = $_GET['id'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    include '../config/config.php';

    if (isset($_POST['save_videos'])) {
        // Course videos

        $targetDir = '../uploads/';
        
        foreach ($_FILES['video_files']['name'] as $key => $videoFile) {
            $videoTitle = $_POST['video_titles'][$key];
            $videoFileName = $_FILES['video_files']['name'][$key];
            $targetVideoFile = $targetDir . basename($videoFileName);
            move_uploaded_file($_FILES['video_files']['tmp_name'][$key], $targetVideoFile);

            // Insert video details into database
            $stmt = $conn->prepare("INSERT INTO course_videos (course_id, title, file) VALUES (?, ?, ?)");
            $stmt->execute([$courseId, $videoTitle, $videoFileName]);
        }
        $_SESSION['success_message'] = "Videos saved successfully!";
    }

    if (isset($_POST['save_curriculum'])) {
        // Course curriculum
        foreach ($_POST['chapter_titles'] as $key => $chapterTitle) {
            $lessons = $_POST['lessons'][$key];

            // Insert curriculum details into database
            $stmt = $conn->prepare("INSERT INTO course_curriculum (course_id, chapter, lessons) VALUES (?, ?, ?)");
            $stmt->execute([$courseId, $chapterTitle, $lessons]);
        }
        $_SESSION['success_message'] = "Curriculum saved successfully!";
    }

    if (isset($_POST['save_books'])) {
        // Reference books
        foreach ($_POST['book_titles'] as $key => $bookTitle) {
            $author = $_POST['book_authors'][$key];

            // Insert book details into database
            $stmt = $conn->prepare("INSERT INTO books (course_id, title, author) VALUES (?, ?, ?)");
            $stmt->execute([$courseId, $bookTitle, $author]);
        }
        $_SESSION['success_message'] = "Books saved successfully!";
    }

    if (isset($_POST['save_links'])) {
        // Important links
        foreach ($_POST['link_titles'] as $key => $linkTitle) {
            $url = $_POST['link_urls'][$key];

            // Insert link details into database
            $stmt = $conn->prepare("INSERT INTO important_links (course_id, title, url) VALUES (?, ?, ?)");
            $stmt->execute([$courseId, $linkTitle, $url]);
        }
        $_SESSION['success_message'] = "Links saved successfully!";
    }

    if (isset($_POST['save_resources'])) {
        // Resources
        $targetResources = '../resources/';
        foreach ($_FILES['resource_files']['name'] as $key => $resourceFile) {
            $resourceTitle = $_POST['resource_titles'][$key];
            $resourceFileName = $_FILES['resource_files']['name'][$key];
            $targetResourceFile = $targetResources . basename($resourceFileName);
            move_uploaded_file($_FILES['resource_files']['tmp_name'][$key], $targetResourceFile);

            // Insert resource details into database
            $stmt = $conn->prepare("INSERT INTO resources (course_id, title, file) VALUES (?, ?, ?)");
            $stmt->execute([$courseId, $resourceTitle, $resourceFileName]);
        }
        $_SESSION['success_message'] = "Resources saved successfully!";
    }

    if (isset($_POST['save_announcements'])) {
        // Announcements
        foreach ($_POST['announcement_titles'] as $key => $announcementTitle) {
            $message = $_POST['announcement_messages'][$key];

            // Insert announcement details into database
            $stmt = $conn->prepare("INSERT INTO announcements (course_id, title, message) VALUES (?, ?, ?)");
            $stmt->execute([$courseId, $announcementTitle, $message]);
        }
        $_SESSION['success_message'] = "Announcements saved successfully!";
    }

    header("Location: course_material.php?id=$courseId");
    exit();
}
?>




<div class="row">
    <div class="col-3">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="col-8 border border-primary position-relative mx-4 my-4">
    <?php

    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
            . $_SESSION['success_message'] .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
        unset($_SESSION['success_message']);
    }
    ?>
        <!-- Course Videos Form -->
        <form id="courseVideosForm" action="course_material.php?id=<?php echo $courseId; ?>" method="post" enctype="multipart/form-data">
            <div class="mt-5">
                <h4>Course Videos</h4>
                <hr>
                <div id="videoContainer">
                    <div class="row g-4 video-item">
                        <div class="col-10">
                            <label class="form-label">Video Title</label>
                            <input type="text" class="form-control" name="video_titles[]" required>
                        </div>
                        <div class="col-10">
                            <label class="form-label">Video File</label>
                            <input type="file" class="form-control" name="video_files[]" required>
                        </div>
                        <div class="col-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-video">Delete</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success mt-3" id="addVideo">Add Video</button>
            </div>
            <div class="mt-4 mb-3">
                <button type="submit" name="save_videos" class="btn btn-success">Save Videos</button>
            </div>
        </form>

        <!-- Curriculum Form -->
        <form id="curriculumForm" action="course_material.php?id=<?php echo $courseId; ?>" method="post" enctype="multipart/form-data">
            <div class="mt-5">
                <h4>Course Curriculum</h4>
                <hr>
                <div id="curriculumContainer">
                    <div class="row g-4 curriculum-item">
                        <div class="col-10">
                            <label class="form-label">Chapter Title</label>
                            <input type="text" class="form-control" name="chapter_titles[]" required>
                        </div>
                        <div class="col-10">
                            <label class="form-label">Lessons</label>
                            <textarea class="form-control" name="lessons[]" rows="3" required></textarea>
                        </div>
                        
                    </div>
                </div>
                
            </div>
            <div class="mt-4 mb-3">
                <button type="submit" name="save_curriculum" class="btn btn-success">Save Curriculum</button>
            </div>
        </form>

        <!-- Reference Books Form -->
        <form id="booksForm" action="course_material.php?id=<?php echo $courseId; ?>" method="post" enctype="multipart/form-data">
            <div class="mt-5">
                <h4>Reference Books</h4>
                <hr>
                <div id="booksContainer">
                    <div class="row g-4 book-item">
                        <div class="col-10">
                            <label class="form-label">Book Title</label>
                            <input type="text" class="form-control" name="book_titles[]" required>
                        </div>
                        <div class="col-10">
                            <label class="form-label">Author</label>
                            <input type="text" class="form-control" name="book_authors[]" required>
                        </div>
                        <div class="col-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-book">Delete</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success mt-3 mb-3" id="addBook">Add Book</button>
            </div>
            <div class="mt-4 mb-3">
                <button type="submit" name="save_books" class="btn btn-success">Save Books</button>
            </div>
        </form>

        <!-- Important Links Form -->
        <form id="linksForm" action="course_material.php?id=<?php echo $courseId; ?>" method="post" enctype="multipart/form-data">
            <div class="mt-5">
                <h4>Important Links</h4>
                <hr>
                <div id="linksContainer">
                    <div class="row g-4 link-item">
                        <div class="col-10">
                            <label class="form-label">Link Title</label>
                            <input type="text" class="form-control" name="link_titles[]" required>
                        </div>
                        <div class="col-10">
                            <label class="form-label">URL</label>
                            <input type="text" class="form-control" name="link_urls[]" required>
                        </div>
                        <div class="col-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-link">Delete</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success mt-3 mb-3" id="addLink">Add Link</button>
            </div>
            <div class="mt-4 mb-3">
                <button type="submit" name="save_links" class="btn btn-success">Save Links</button>
            </div>
        </form>

        <!-- Resources Form -->
        <form id="resourcesForm" action="course_material.php?id=<?php echo $courseId; ?>" method="post" enctype="multipart/form-data">
            <div class="mt-5">
                <h4>Resources</h4>
                <hr>
                <div id="resourcesContainer">
                    <div class="row g-4 resource-item">
                        <div class="col-10">
                            <label class="form-label">Resource Title</label>
                            <input type="text" class="form-control" name="resource_titles[]" required>
                        </div>
                        <div class="col-10">
                            <label class="form-label">Resource File</label>
                            <input type="file" class="form-control" name="resource_files[]" required>
                        </div>
                        <div class="col-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-resource">Delete</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success mt-3 mb-3" id="addResource">Add Resource</button>
            </div>
            <div class="mt-4 mb-3">
                <button type="submit" name="save_resources" class="btn btn-success">Save Resources</button>
            </div>
        </form>

        <!-- Announcements Form -->
        <form id="announcementsForm" action="course_material.php?id=<?php echo $courseId; ?>" method="post" enctype="multipart/form-data">
            <div class="mt-5">
                <h4>Announcements</h4>
                <hr>
                <div id="announcementsContainer">
                    <div class="row g-4 announcement-item">
                        <div class="col-10">
                            <label class="form-label">Announcement Title</label>
                            <input type="text" class="form-control" name="announcement_titles[]" required>
                        </div>
                        <div class="col-10">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" name="announcement_messages[]" rows="3" required></textarea>
                        </div>
                        
                    </div>
                </div>
                
            </div>
            <div class="mt-4 mb-3">
                <button type="submit" name="save_announcements" class="btn btn-success">Save Announcements</button>
            </div>
        </form>
    </div>
</div>

<?php include "../footer.php"; ?>

<script src="../js/tynmice.js"></script>




</body>
</html>