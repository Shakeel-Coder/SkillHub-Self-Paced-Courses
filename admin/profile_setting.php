

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Profile Settings</title>
</head>
<body>
    
<?php
    require_once '../auth/auth.php';

    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to admins
    checkRole(['admin']);

   
    ?>


<?php
// Database connection
include '../config/config.php';

// Fetch logged-in user details
$userId = $_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $profilePicture = $_FILES['profile_picture']['name'];

    // Handle profile picture upload
    if ($profilePicture) {
        $targetDir = '../uploads/';
        $targetFile = $targetDir . basename($profilePicture);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile);
    } else {
        $profilePicture = $user['profile_picture'];
    }

    // Update user details in the database
    if ($password) {
        $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, username = ?, email = ?, password = ?, phone = ?, address = ?, profile_picture = ? WHERE id = ?");
        $stmt->execute([$firstName, $lastName, $username, $email, password_hash($password, PASSWORD_DEFAULT), $phone, $address, $profilePicture, $userId]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, username = ?, email = ?, phone = ?, address = ?, profile_picture = ? WHERE id = ?");
        $stmt->execute([$firstName, $lastName, $username, $email, $phone, $address, $profilePicture, $userId]);
    }

    // Refresh the user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['success_message'] = "Profile update successfully!";
}
?>



<div class="row">
    <div class="col-3">
        <?php require "sidebar.php" ?>
    </div>
    <div class="col-8">
        <div class="container mt-5">
        <?php

        if (isset($_SESSION['success_message'])) {
            echo '<div class="d-flex justify-content-center align-items-center">
                <div class="alert alert-success alert-dismissible fade show" role="alert">'
                . $_SESSION['success_message'] .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            </div>';
            unset($_SESSION['success_message']);
        }
        ?>
            <h2>Profile Settings</h2>
            <form action="profile_setting.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="profile_picture" class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                    <?php if ($user['profile_picture']): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail mt-2" width="150">
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <small class="form-text text-muted">Leave blank if you don't want to change the password.</small>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

</body>
</html>