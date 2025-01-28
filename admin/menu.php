<?php 
  $firstName = $_SESSION['firstname'];
  $lastName = $_SESSION['lastname'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Navbar with Dropdown</title>
<style>
  /* Basic Reset */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  /* Navbar styling */
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background-color: #333;
    color: #fff;
  }

  /* User name styling */
  .user-name a {
    font-size: 1.2rem;
    text-decoration: none;
    color: #fff;
  }

  /* Profile icon container */
  .profile-container {
    position: relative;
    cursor: pointer;
  }

  /* Profile icon styling */
  .profile-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #555;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #fff;
  }

  /* Dropdown menu styling */
  .dropdown-menu {
    display: none;
    position: absolute;
    top: 50px;
    right: 0;
    background-color: #fff;
    color: #333;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 5px;
    overflow: hidden;
    min-width: 150px;
  }

  .dropdown-menu.active {
    display: block;
  }

  .dropdown-menu a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #333;
  }

  .dropdown-menu a:hover {
    background-color: #ddd;
  }
</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
  <div class="user-name ">
    <span>Welcome back!</span>
    <h3><?php if (isset($_SESSION['firstname'])) {
    echo $_SESSION['firstname'] . " " . $_SESSION['lastname'];
} else {
    echo "Firstname session variable is not set.";
}?></h3>
  </div>
  <div class="profile-container" onclick="toggleDropdown()">
    <div class="profile-icon"><?php echo $firstLetter = substr($firstName, 0, 1) . $lastLetter = substr($lastName, 0, 1); ?></div>
    <div class="dropdown-menu" id="dropdown-menu">
      <a href="admin_profile.php">Profile</a>
      <a href="profile_setting.php">Settings</a>
      <a href="http://localhost/skillhub/auth/logout.php">Logout</a>
    </div>
  </div>
</div>

<script>
  // Function to toggle dropdown menu
  function toggleDropdown() {
    document.getElementById("dropdown-menu").classList.toggle("active");
  }

  // Close dropdown when clicking outside
  window.onclick = function(event) {
    if (!event.target.matches('.profile-icon')) {
      var dropdowns = document.getElementsByClassName("dropdown-menu");
      for (var i = 0; i < dropdowns.length; i++) {
        if (dropdowns[i].classList.contains('active')) {
          dropdowns[i].classList.remove('active');
        }
      }
    }
  }
</script>

</body>
</html>
