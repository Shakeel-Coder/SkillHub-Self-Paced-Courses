<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .sidebar {
            height: 100%;
            width: 250px;
            position: sticky;
            top: 80;
            left: 0;
            background-color: #111;
            padding-top: 20px;
            transition: width 0.3s;
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: flex;
            align-items: center;
            transition: 0.3s;
        }

        .sidebar a i {
            margin-right: 10px;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .sidebar a.active {
            background-color: #555;
            font-weight: bold;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed a {
            justify-content: center;
        }

        .sidebar.collapsed a span {
            display: none;
        }

        .toggle-btn {
            position: absolute;
            top: 10px;
            right: -40px;
            background-color: #111;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <button class="toggle-btn" onclick="toggleSidebar()">&#9776;</button>
    <a href="learner_dashboard.php" class="tab-link" onclick="setActiveTab(this)"><i class="bi bi-house"></i><span>Dashboard</span></a>
    <a href="my_courses.php" class="tab-link" onclick="setActiveTab(this)"><i class="bi bi-book"></i><span>My Courses</span></a>
    <a href="browse_courses.php" class="tab-link" onclick="setActiveTab(this)"><i class="bi bi-search"></i><span>Browse Courses</span></a>
    <a href="assignment.php" class="tab-link" onclick="setActiveTab(this)"><i class="bi bi-pencil"></i><span>Assignments</span></a>
    <a href="quiz.php" class="tab-link" onclick="setActiveTab(this)"><i class="bi bi-question-circle"></i><span>Quizzes</span></a>
</div>

<script>
    // Function to set the active tab
    function setActiveTab(selectedTab) {
        var tabs = document.getElementsByClassName("tab-link");
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].classList.remove("active");
        }
        selectedTab.classList.add("active");
    }

    // Set the active tab based on the current URL
  window.onload = function() {
    var tabs = document.getElementsByClassName("tab-link");
    var currentPath = window.location.pathname.split("/").pop();
    for (var i = 0; i < tabs.length; i++) {
      if (tabs[i].getAttribute("href") === currentPath) {
        tabs[i].classList.add("active");
        break;
      }
    }
  }

    // Function to toggle the sidebar
    function toggleSidebar() {
        var sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("collapsed");
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

</body>
</html>