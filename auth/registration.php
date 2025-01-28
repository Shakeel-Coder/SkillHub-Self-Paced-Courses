<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Easiest Way to Add Input Masks to Your Forms</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/sign-up.css">
</head>
<body>
     



<?php
// Include the necessary files
include '../header.php';
include '../config/config.php';

if(isset($_POST["submit"])){
    // Retrieve form data
    if ($_POST['firstname']=='' OR $_POST['lastname']=='' OR $_POST['username']=='' OR $_POST['pass']=='' OR $_POST['email']=='' OR $_POST['roles']=='') {
        echo "Please enter all details correctly!";
    }
    else{

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $pass = $_POST['pass'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $roles = $_POST['roles'];
    }
     
   
        // Prepare SQL query
        $insert = $conn->prepare("INSERT INTO users(firstname, lastname, username, pass, email, phone, roles) VALUES (:firstname,:lastname,:username,:pass,:email,:phone, :roles)");

        // Execute SQL query with parameter bindings
        $insert->execute([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'username' => $username,
            'pass' => password_hash($pass, PASSWORD_DEFAULT), // Hash password for security
            'email' => $email,
            'phone' => $phone,
            'roles' => $roles,
        ]);
        $_SESSION['success_message'] = "Congrationtulation! Your registration data saved successfully.";
 
    }

          
    ?>
        
        


    <div class="registration-form">
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
        <form method="POST" action="registration.php">
            <div class="form-icon">
                <span><i class="icon icon-user"></i></span>
            </div>
            <div class="form-group">
                <input type="text" name="firstname" class="form-control item" id="firstname" placeholder="Firstname">
            </div>
            <div class="form-group">
                <input type="text" name="lastname" class="form-control item" id="lastname" placeholder="Lastname">
            </div>
            <div class="form-group">
                <input type="text" name="username" class="form-control item" id="username" placeholder="Username">
            </div>
            <div class="form-group">
                <input type="password" name="pass" class="form-control item" id="pass" placeholder="Password">
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control item" id="email" placeholder="Email">
            </div>
            <div class="form-group">
            <input type="text" name="phone" class="form-control item" id="phone" placeholder="phone ">
            </div>
            <div class="form-group form-control item mb-3">
                <select class="w-100 border border-0" name="roles" id="roles">
                    <option value="roles" disabled selected>
                        Please select your role
                    </option>
                    <option value="admin">
                        admin
                    </option>
                    <option value="learner">
                        learner
                    </option>
                    <option value="instructor">
                        instructor
                    </option>
                    
                </select>
            </div>
           
            <div class="form-group">
                <button type="submit" name="submit" class="btn btn-block create-account" onclick="return confirm('Are you sure you want to Sign Up?');">Create Account</button>
                
            </div>
            <div class="form-group text-center">Already have an account? <a href="login.php">Sign In</a> </div>
        </form>
        
    </div>

     <?php include '../footer.php';?>

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="js/javascript.js"></script>
</body>
</html>
