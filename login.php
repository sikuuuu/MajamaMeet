<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM shrikant WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if ($password === $row['password']) { // Or password_verify() if using hashing
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $row['Name'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Wrong password!";
        }
    } else {
        $error = "Email not found register this email !!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MajamaMeet | Login</title>
    <link rel="icon" href="MajamaMeet_logo_design.png" type="image/x-icon">
    <link rel="stylesheet" href="project-1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <!-- FontAwesome for Eye Icon -->

    <style>
        .acc {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #ff6b6b;
            font-weight: bold;
        }

        .error-message {
            background-color: #ffe0e0;
            color: #d10000;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.95rem;
        }

        .wrappers {
            padding: 51px;
            border-radius: 82px;
            animation: <?php if (!empty($error)) echo "shake 0.4s"; else echo "fadeIn 1s ease forwards"; ?>;
            position: relative;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            50% { transform: translateX(10px); }
            75% { transform: translateX(-10px); }
            100% { transform: translateX(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
   }

        /* Eye icon inside password box */
        .inputbox {
            position: relative;
        }

        .inputbox input[type="password"],
        .inputbox input[type="text"] {
            width: 100%;
            padding-right: 40px; /* space for eye icon */
        }

        #togglePassword {
            position: absolute;
            top: 40%;
            right: -8px;
            cursor: pointer;
            color: #888;
        }
        .inputbox input[type="password"], .inputbox input[type="text"]{
            width: 100%;
            padding-right: 13px;
        }
    </style>
</head>
<body>
    <div class="wrappers" id="wrapper">
    <h2 style="font-family: 'Baloo 2', cursive; color: #ff6b6b; margin-bottom: 20px;">Welcome to MajamaMeet!</h2>
        <img src="MajamaMeet_logo_design.png" alt="MajamaMeet Logo">

        <form method="POST">
            <h1>Login</h1>

            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="inputbox">
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="inputbox">
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <i class="fa-regular fa-eye" id="togglePassword"></i>
            </div>

            <div style="margin-bottom: 15px; text-align: right;">
                <input type="checkbox" id="rememberMe" name="rememberMe" style="transform: scale(1.2); margin-right: 5px;">
                <label for="rememberMe" style="font-size: 0.95rem; color: #555;">Remember Me</label>
            </div>

            <div class="btw">
                <button type="submit">Enter Website</button>
            </div>

            <p>Don't have an account?</p>
            <a class="acc" href="Registration.php">Register</a>
        </form>
    </div>

    <!-- Show/Hide Password Script -->
    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function () {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);

            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
