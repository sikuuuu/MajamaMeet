<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Get session details
$name = $_SESSION['name'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MajamaMeet | User Profile</title>
    <link rel="icon" href="MajamaMeet_logo_design.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Montserrat&display=swap" rel="stylesheet">
    <style>
       body {
    font-family: 'Montserrat', sans-serif;
    background: linear-gradient(135deg, #FFDEE9, #B5FFFC);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
}

.profile-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    animation: fadeIn 1s ease;
}

.profile-card img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin-bottom: 20px;
    border: 4px solid #FF6B6B;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    object-fit: cover;
}

.profile-card h2 {
    font-family: 'Baloo 2', cursive;
    font-size: 2rem;
    color: #FF6B6B;
    margin-bottom: 10px;
}

.profile-card p {
    font-size: 1.1rem;
    color: #555;
    margin-bottom: 20px;
}

.edit-btn {
    background: linear-gradient(to right, #00BFA6, #009e8c);
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 25px;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.4s;
}

.edit-btn:hover {
    background: linear-gradient(to right, #009e8c, #00BFA6);
}

@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-30px);}
    to {opacity: 1; transform: translateY(0);}
}

    </style>
</head>
<body>

<div class="profile-card">
    <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Profile Picture">
    <h2><?php echo htmlspecialchars($name); ?></h2>
    <p><?php echo htmlspecialchars($email); ?></p>
    <a href="edit_profile.php" class="edit-btn">Edit Profile</a>
</div>

</body>
</html>
