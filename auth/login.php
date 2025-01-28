<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/sign_in.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>User Login</title>
</head>
<body class="text-center">

<?php include '../header.php'; ?>
<?php include '../config/config.php'; ?>

<?php
if (isset($_POST['submit'])) {
    if (empty($_POST['floatingInput']) || empty($_POST['floatingPassword']) || empty($_POST['role'])) {
        echo "Please enter all details correctly!";
    } else {
        $email = $_POST['floatingInput'];
        $password = $_POST['floatingPassword'];
        $role = $_POST['role'];

        try {
            $login = $conn->prepare("SELECT * FROM users WHERE email=:email AND roles=:roles");
            $login->execute([
                'email' => $email,
                'roles' => $role,
            ]);

            $row = $login->fetch(PDO::FETCH_ASSOC);

            if ($row && password_verify($password, $row['pass'])) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_role'] = $row['roles'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['firstname'] = $row['firstname'];
                $_SESSION['lastname'] = $row['lastname'];
                $_SESSION['id'] = $row['id'];


                // Check if there is a redirect parameter
            if (isset($_GET['redirect'])) {
                $redirectUrl = $_GET['redirect'];
                header("Location: http://localhost/skillhub/$redirectUrl");
                exit();
            }
                
                switch ($role) {
                    case 'admin':
                        header("Location: http://localhost/skillhub/admin/admin_dashboard.php");
                        break;
                    case 'learner':
                        header("Location: http://localhost/skillhub/learner/learner_dashboard.php");
                        break;
                    case 'instructor':
                        header("Location: http://localhost/skillhub/instructor/instructor_dashboard.php");
                        break;
                    default:
                        echo "Invalid role.";
                        break;
                }
                exit();
            } else {
                echo "Invalid email, password, or role.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<div class="dev1">
    <div class="dev2">
        <main class="form-signin">
            <form method="POST" action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">
                <img class="mb-4" src="http://localhost/skillhub/uploads/sign-in.png" alt="" width="72" height="57">
                <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

                <div class="form-floating">
                    <input type="email" name="floatingInput" class="form-control" id="floatingInput" placeholder="name@example.com">
                    <label for="floatingInput">Email address</label>
                </div>
                <div class="form-floating">
                    <input type="password" name="floatingPassword" class="form-control" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword">Password</label>
                </div>

                <div class=" mb-3 form-floating form-control">
                    <select name="role" id="role" class="w-100 border border-0">
                        <option value="roles" disabled selected>Please select your role</option>
                        <option value="learner">Learner</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit" name="submit">Sign in</button>
                <a href="registration.php" class="register">Sign Up</a>
            </form>
        </main>
    </div>
</div>

<?php include "../footer.php"; ?>

<script src="javascript.js"></script>
</body>
</html>