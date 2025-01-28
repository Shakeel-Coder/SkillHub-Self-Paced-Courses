

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Instructor Profile</title>
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
// Database connection
include '../config/config.php';


// Fetch logged-in user details
$userId = $_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="row">
    <div class="col-md-4">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="col-md-8">
        <h2>Instructor Profile</h2>
        <img src="../uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="rounded-circle img-thumbnail" width="150" height="150">
        <table class="table table-bordered">
            <tr>
                <th>First Name</th>
                <td><?php echo htmlspecialchars($user['firstname']); ?></td>
            </tr>
            <tr>
                <th>Last Name</th>
                <td><?php echo htmlspecialchars($user['lastname']); ?></td>
            </tr>
            <tr>
                <th>Username</th>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?php echo htmlspecialchars($user['phone']); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo htmlspecialchars($user['address']); ?></td>
            </tr>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXlHj1/5j4U6i5t9e7k5t5hB5g5i5t9e7k5t5hB5g5i5" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGkQ5Y6z6p+6i5t9e7k5t5hB5g5i5t9e7k5t5hB5g5i5" crossorigin="anonymous"></script>

</body>
</html>