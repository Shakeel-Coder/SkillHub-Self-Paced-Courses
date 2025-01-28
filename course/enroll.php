<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "bestskillhub";

$connection = new mysqli($servername, $username, $password, $database);
if ($connection->connect_error) {
    die("Cannot connect to database: " . $connection->connect_error);
}

if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to enroll in a course.']);
    exit();
}

$email = $_SESSION['email'];

if (isset($_POST['course_id'])) {
    $courseId = intval($_POST['course_id']); 

    $checkStmt = $connection->prepare("SELECT * FROM enrollments WHERE learner_email = ? AND course_id = ?");
    $checkStmt->bind_param("si", $email, $courseId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'You are already enrolled in this course.']);
        $checkStmt->close();
        exit();
    }

    $checkStmt->close();

    $stmt = $connection->prepare("INSERT INTO enrollments (learner_email, course_id) VALUES (?, ?)");
    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'SQL prepare failed: ' . $connection->error]);
        exit();
    }

    $stmt->bind_param("si", $email, $courseId);

    if ($stmt->execute()) {
        
        echo json_encode(['status' => 'success', 'message' => 'Enrollment successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Enrollment failed: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Course ID not provided.']);
}


$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Courses</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>

<div class="container">
    <h2 class="text-center p-5">Available Courses</h2>

    <div class="row">
        <?php
        
        $connection = new mysqli($servername, $username, $password, $database);
        if ($connection->connect_error) {
            die("Cannot connect to database: " . $connection->connect_error);
        }

        $sql = "SELECT id, ctitle, cdes, iname, duration, file_path FROM upload";
        $result = $connection->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($row['file_path']); ?>" class="card-img-top" alt="Course Image">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo htmlspecialchars($row['ctitle']); ?></h4>
                            <p class="card-text"><?php echo htmlspecialchars($row['cdes']); ?></p>
                            <h5>Course Duration: <?php echo htmlspecialchars($row['duration']); ?></h5>
                            <h5>Instructor Name: <?php echo htmlspecialchars($row['iname']); ?></h5>
                        </div>
                        <div class="footer text-center pb-3">
                            <button type="button" class="btn btn-primary enroll-btn" data-course-id="<?php echo htmlspecialchars($row['id']); ?>">Enroll</button>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<div class='col-12'><p class='text-center'>No courses found.</p></div>";
        }

        
        $connection->close();
        ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        
        $('.enroll-btn').click(function(e) {
            e.preventDefault(); 

            const courseId = $(this).data('course-id'); 

            $.ajax({
                url: 'enroll.php', 
                type: 'POST',
                data: { course_id: courseId }, 
                dataType: 'json', 
                success: function(response) {
                    
                    if (response.status === 'success') {
                        alert(response.message); 
                    } else {
                        alert(response.message); 
                    }
                },
                error: function(xhr, status, error) {
            
                    alert('An error occurred: ' + error);
                }
            });
        });
    });
</script>

</body>
</html>
