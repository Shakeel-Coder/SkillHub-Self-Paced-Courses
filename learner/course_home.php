<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Course Home</title>
    <style>
        .video-list .active {
            background-color: #d3d3d3;
        }
        .video-list .watched .tick-mark {
            color: green;
            margin-left: 10px;
        }
        .video-list .locked {
            color: #ccc;
            pointer-events: none;
        }
    </style>
</head>
<body>

<?php
    require_once '../auth/auth.php';
    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to learners
    checkRole(['learner']);

    include '../config/config.php';

    // Start session
    $courseId = $_GET['id'];
    $userId = $_SESSION['id'];

    // Fetch course details
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$courseId]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch curriculum
    $stmt = $conn->prepare("SELECT * FROM course_curriculum WHERE course_id = ?");
    $stmt->execute([$courseId]);
    $curriculum = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch videos
    $stmt = $conn->prepare("SELECT * FROM course_videos WHERE course_id = ? ORDER BY id ASC");
    $stmt->execute([$courseId]);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch video progress
    $stmt = $conn->prepare("SELECT video_id FROM video_progress WHERE course_id = ? AND student_id = ? AND watched = TRUE");
    $stmt->execute([$courseId, $userId]);
    $watchedVideos = $stmt->fetchAll(PDO::FETCH_COLUMN);

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
?>

<div class="row">
    <div class="col-3">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="col-8 border border-primary position-relative mx-4 my-4">
        <div class="container mt-5">
            <h2><?php echo htmlspecialchars($course['title']); ?></h2>
            <ul class="nav nav-tabs" id="courseTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos" type="button" role="tab" aria-controls="videos" aria-selected="false">Videos</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reference-tab" data-bs-toggle="tab" data-bs-target="#reference" type="button" role="tab" aria-controls="reference" aria-selected="false">Reference</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="links-tab" data-bs-toggle="tab" data-bs-target="#links" type="button" role="tab" aria-controls="links" aria-selected="false">Important Links</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="resources-tab" data-bs-toggle="tab" data-bs-target="#resources" type="button" role="tab" aria-controls="resources" aria-selected="false">Resources</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="announcements-tab" data-bs-toggle="tab" data-bs-target="#announcements" type="button" role="tab" aria-controls="announcements" aria-selected="false">Announcements</button>
                </li>
            </ul>
            <div class="tab-content" id="courseTabsContent">
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <h3>Course Details</h3>
                    <p><strong>Title:</strong> <?php echo htmlspecialchars($course['title']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars_decode($course['description']); ?></p>
                    <p><strong>Duration:</strong> <?php echo htmlspecialchars($course['duration']); ?></p>
                    <p><strong>Level:</strong> <?php echo htmlspecialchars($course['level']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($course['category']); ?></p>
                    <p><strong>Course Code:</strong> <?php echo htmlspecialchars($course['course_code']); ?></p>
                    <h3>Curriculum</h3>
                    
                        <?php foreach ($curriculum as $item): ?>
                            <h4><?php echo htmlspecialchars($item['chapter']); ?></h4>
                            <p><?php echo htmlspecialchars_decode($item['lessons']); ?></p>
                        <?php endforeach; ?>
                    
                </div>
                <div class="tab-pane fade" id="videos" role="tabpanel" aria-labelledby="videos-tab">
                    <div class="row">
                        <div class="col-md-8">
                            <video id="courseVideo" width="100%" controls>
                                <source src="../uploads/<?php echo htmlspecialchars($videos[0]['file']); ?>" >
                                Your browser does not support the video tag.
                            </video>
                            <div class="mt-2 d-flex justify-content-between">
                                <button id="prevVideoBtn" class="btn btn-secondary">Previous Video</button>
                                <button id="markAsDoneBtn" class="btn btn-success" disabled>Mark as Done</button>
                                <button id="nextVideoBtn" class="btn btn-secondary">Next Video</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h4>Video List</h4>
                            <ul class="list-group video-list">
                                <?php foreach ($videos as $index => $video): ?>
                                    <li class="list-group-item video-item <?php echo in_array($video['id'], $watchedVideos) ? 'watched' : ''; ?>" data-video-id="<?php echo $video['id']; ?>" data-video-src="../uploads/<?php echo htmlspecialchars($video['file']); ?>">
                                        <?php echo ($index + 1) . '. ' . htmlspecialchars($video['title']); ?>
                                        <?php if (in_array($video['id'], $watchedVideos)): ?>
                                            <i class="bi bi-check-circle-fill tick-mark"></i>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="reference" role="tabpanel" aria-labelledby="reference-tab">
                    <h3>Recommended Books</h3>
                    <ol>
                        <?php foreach ($books as $book): ?>
                            <li><?php echo htmlspecialchars($book['title']); ?> by <?php echo htmlspecialchars($book['author']); ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
                <div class="tab-pane fade" id="links" role="tabpanel" aria-labelledby="links-tab">
                    <h3>Important Links</h3>
                    <ol>
                        <?php foreach ($links as $link): ?>
                            <li><a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank"><?php echo htmlspecialchars($link['title']); ?></a></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
                <div class="tab-pane fade" id="resources" role="tabpanel" aria-labelledby="resources-tab">
                    <h3>Resources</h3>
                    <ul>
                        <?php foreach ($resources as $resource): ?>
                            <li><p ><?php echo htmlspecialchars($resource['title']); ?>-<a href="../uploads/<?php echo htmlspecialchars($resource['file']); ?>" download><?php echo htmlspecialchars($resource['title']); ?></a></p></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="tab-pane fade" id="announcements" role="tabpanel" aria-labelledby="announcements-tab">
                    <h3>Announcements</h3>
                    <ol>
                        <?php foreach ($announcements as $announcement): ?>
                            <li><strong><?php echo htmlspecialchars($announcement['title']); ?></strong>: <?php echo $announcement['message']; ?> <small style="float: right;"><b>Published Date:</b> <?php echo htmlspecialchars($announcement['date']); ?></small></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const videoLinks = document.querySelectorAll('.video-item');
        const courseVideo = document.getElementById('courseVideo');
        const prevVideoBtn = document.getElementById('prevVideoBtn');
        const nextVideoBtn = document.getElementById('nextVideoBtn');
        const markAsDoneBtn = document.getElementById('markAsDoneBtn');
        let currentVideoIndex = 0;

        function loadVideo(index) {
            const videoItem = videoLinks[index];
            const videoSrc = videoItem.getAttribute('data-video-src');
            courseVideo.querySelector('source').setAttribute('src', videoSrc);
            courseVideo.setAttribute('data-video-id', videoItem.getAttribute('data-video-id'));
            courseVideo.load();
            highlightCurrentVideo(index);
            markAsDoneBtn.disabled = true;
        }

        function highlightCurrentVideo(index) {
            videoLinks.forEach((link, i) => {
                if (i === index) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        videoLinks.forEach((link, index) => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                currentVideoIndex = index;
                loadVideo(index);
            });
        });

        prevVideoBtn.addEventListener('click', function() {
            if (currentVideoIndex > 0) {
                currentVideoIndex--;
                loadVideo(currentVideoIndex);
            }
        });

        nextVideoBtn.addEventListener('click', function() {
            if (currentVideoIndex < videoLinks.length - 1) {
                currentVideoIndex++;
                loadVideo(currentVideoIndex);
            }
        });

        markAsDoneBtn.addEventListener('click', function() {
            const videoId = courseVideo.getAttribute('data-video-id');
            const courseId = <?php echo $courseId; ?>;
            const studentId = <?php echo $userId; ?>;
            fetch('update_video_progress.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ video_id: videoId, course_id: courseId, student_id: studentId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    markAsDoneBtn.disabled = true;
                    videoLinks[currentVideoIndex].classList.add('watched');
                } else {
                    alert('Failed to mark video as done.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while marking the video as done.');
            });
        });

        courseVideo.addEventListener('ended', function() {
            markAsDoneBtn.disabled = false;
        });

        // Initial load
        if (videoLinks.length > 0) {
            loadVideo(0);
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

</body>
</html>