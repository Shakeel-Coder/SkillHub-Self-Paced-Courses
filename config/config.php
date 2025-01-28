
<?php 

try {
    $host = "localhost";
    $dbname = "skillhub";
    $user = "root";
    $pass = '';

    // Create a new PDO instance and set the error mode to exception
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Connection successful message (Optional)
    
} catch (PDOException $e) {
    // Display the exception message if the connection fails
    echo "Connection failed: " . $e->getMessage();
}
?>