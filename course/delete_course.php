<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "bestskillhub";

$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseId = $_POST['course_id'];

    if (!empty($courseId)) {
        $stmt = $connection->prepare("DELETE FROM upload WHERE id = ?");
        $stmt->bind_param("i", $courseId);

        if ($stmt->execute()) {
            echo 'Course deleted successfully!'; 
        } else {
            echo 'Error deleting course: ' . $stmt->error; 
        }

        $stmt->close();
    } else {
        echo 'Invalid course ID.'; 
    }

    $connection->close();
    exit; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses Page</title>
    <script src="js/jquery.js"></script>
    <script>
        function deleteCourse(courseId) {
            
            if (confirm("Are you sure you want to delete this course?")) {
                $.ajax({
                    type: "POST",
                    url: "delete_course.php", 
                    data: { course_id: courseId },
                    success: function(response) {
                        alert(response); 
                        
                        window.location.href = 'mycourses.php'; 
                    },
                    error: function() {
                        alert("An error occurred while deleting the course.");
                    }
                });
            }
        }
    </script>
</head>
<body>

<h1>Courses</h1>

<button onclick="deleteCourse(1)">Delete Course 1</button>
<button onclick="deleteCourse(2)">Delete Course 2</button>

</body>
</html>
