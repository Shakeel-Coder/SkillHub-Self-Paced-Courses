<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <title>Course New Create</title>
</head>
<body>

<?php
    require_once '../auth/auth.php';

    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to admins
    checkRole(['admin']);

    include '../config/config.php';

    // Fetch users details
    $stmt = $conn->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $_SESSION['success_message'] = 'User Account deleted successfully.';
        header("Location: manage_users.php");
        exit();
    }
    
}
    ?>


<div class="row">
	<div class="col-3">
	<?php require "sidebar.php" ?>

	</div>
	<div class="col-8">


	<!-- User's list table start here -->


	<div class="mt-5">
            <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
                    . $_SESSION['success_message'] .
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
                unset($_SESSION['success_message']);
            }
            ?>
    <h3>User List</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Serial No.</th>
                <th>User Image</th>
                <th>User Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $serialNo = 1;
            foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $serialNo++; ?></td>
                    <td><img src="images/<?php echo htmlspecialchars($user['profile_picture']); ?>" class="rounded img" alt="User" width="50" height="50"></td>
                    <td><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['roles']); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                        </form>
                        <a href="user_settings.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">Settings</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

        <!-- user list table end here -->



	
    </div>








<?php require "../footer.php" ?>


    
</body>
</html>



