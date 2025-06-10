<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MajamaMeet</title>
    <link rel="icon" href="MajamaMeet_logo_design.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Montserrat&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Common Styling */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: #FEF8F0;
            background-image: radial-gradient(circle at top left, #FFD93D 10%, transparent 30%),
                              radial-gradient(circle at bottom right, #FF6B6B 10%, transparent 30%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: 0.4s ease;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(to right, #ff6b6b, #ffd93d);
            padding: 15px 30px;
            display: flex;
            align-items: center;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        #toggleBtn {
            background: none;
            border: none;
            font-size: 24px;
            color: white;
            cursor: pointer;
            position: absolute;
            left: 20px;
        }
        .center-content {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .center-content img { height: 40px; }
        .brand-text {
            font-family: 'Baloo 2', cursive;
            font-size: 1.8rem;
            color: white;
            font-weight: bold;
        }
        .user-icon {
            margin-left: auto;
            margin-right: 20px;
        }
        .user-icon a {
            color: white;
            font-size: 2rem;
            text-decoration: none;
        }
        .user-icon a:hover { opacity: 0.8; }

        /* Sidebar */
        .main { display: flex; flex: 1; }
        .sidebar {
            width: 250px;
            background-color: #fff;
            padding-top: 30px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            transition: width 0.3s;
            min-height: 100vh;
        }
        .sidebar.collapsed { width: 80px; }
        .sidebar a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            transition: background 0.3s;
            gap: 15px;
        }
        .sidebar a:hover {
            background-color: #FF6B6B;
            color: white;
            border-radius: 10px;
        }
        .sidebar.collapsed a .text { display: none; }

        /* Content */
        .content {
            flex: 1;
            padding: 50px 40px;
            background: #ffffff;
            transition: background 0.4s;
            text-align: center;
        }

        /* Dark Mode */
        body.dark-mode {
            background-color: #121212;
            color: white;
        }
        body.dark-mode .navbar {
            background: linear-gradient(to right, #333, #555);
        }
        body.dark-mode .sidebar {
            background-color: #1e1e1e;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.7);
        }
        body.dark-mode .sidebar a { color: white; }
        body.dark-mode .sidebar a:hover { background-color: #555; }
        body.dark-mode .content { background: #1a1a1a; 
        }
        <style>
.form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    margin-top: 30px;
    width: 100%;
}
.create-form {
    background: white;
    padding: 30px 40px;
    border-radius: 20px;
    box-shadow: 0px 8px 30px rgba(0,0,0,0.1);
    max-width: 450px;
    width: 90%;
    animation: fadeIn 1s ease;
}
.create-form h2 {
    font-family: 'Baloo 2', cursive;
    font-size: 2rem;
    margin-bottom: 20px;
    color: #FF6B6B;
    text-align: center;
}
.create-form input, .create-form textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 10px;
    font-size: 1rem;
}
.create-form button {
    width: 100%;
}
/* Sidebar */
.main { display: flex; flex: 1; }
        .sidebar {
            width: 250px;
            background-color: #fff;
            padding-top: 30px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            transition: width 0.3s;
            min-height: 100vh;
        }
        .sidebar.collapsed { width: 80px; }
        .sidebar a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            transition: background 0.3s, color 0.3s;
            gap: 15px;
        }
        .sidebar a:hover {
            background-color: #FF6B6B;
            color: white;
            border-radius: 10px;
        }
        .sidebar.collapsed a .text { display: none; }

        /* ACTIVE Sidebar Link */
        .sidebar a.active {
            background-color: #FF6B6B;
            color: white;
            border-radius: 10px;
            transition: background-color 0.4s, color 0.4s;
        }
        body.dark-mode .sidebar a.active {
            background-color: #555;
            color: white;
            transition: background-color 0.4s, color 0.4s;
        }

</style>

</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <button id="toggleBtn"><i class="fas fa-bars"></i></button>
    <div class="center-content">
        <img src="MajamaMeet_logo_design.png" href="dashboard.php" alt="MajamaMeet Logo">
        <span class="brand-text">MajamaMeet</span>
    </div>
    <div class="user-icon">
        <a href="profile.php"><i class="fas fa-user-circle"></i></a>
    </div>
    <div class="mode-toggle">
        <label class="switch">
            <input type="checkbox" id="modeSwitch">
            <span class="slider"></span>
        </label>
    </div>
</div>

<!-- Main Layout -->
<div class="main">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
    <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
        <i class="fas fa-home"></i> <span class="text">Dashboard</span>
    </a>
    <a href="create_event.php" class="<?php echo ($current_page == 'create_event.php') ? 'active' : ''; ?>">
        <i class="fas fa-calendar-plus"></i> <span class="text">Create Event</span>
    </a>
    <a href="my_events.php" class="<?php echo ($current_page == 'my_events.php') ? 'active' : ''; ?>">
        <i class="fas fa-list"></i> <span class="text">Created Events</span>
    </a>
    <a href="view_participants.php" class="<?php echo ($current_page == 'view_participants.php') ? 'active' : ''; ?>">
    <i class="fas fa-address-book"></i> <span class="text">View Participants</span>
    </a>
    <a href="event_history.php" class="<?php echo ($current_page == 'event_history.php') ? 'active' : ''; ?>">
        <i class="fas fa-history"></i> <span class="text">Event History</span>
    </a>
    <a href="join_event.php" class="<?php echo ($current_page == 'join_event.php') ? 'active' : ''; ?>">
        <i class="fas fa-users"></i> <span class="text">Join Event</span>
    </a>
    <a href="joined_events.php" class="<?php echo ($current_page == 'joined_events.php') ? 'active' : ''; ?>">
        <i class="fas fa-user-friends"></i> <span class="text">Joined Events</span>
    </a>
    <a href="profile.php" class="<?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
        <i class="fas fa-user"></i> <span class="text">Profile</span>
    </a>
    <a href="logout.php" class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">
        <i class="fas fa-sign-out-alt"></i> <span class="text">Logout</span>
    </a>
</div>


    <!-- Content Start -->
    <div class="content">
