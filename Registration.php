<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $name = $_POST["name"];

    // Insert logic or redirect after registration
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | MajamaMeet</title>
    <link rel="icon" href="MajamaMeet_logo_design.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Montserrat&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #FEF8F0;
            background-image: radial-gradient(circle at top left, #FFD93D 5%, transparent 20%),
                              radial-gradient(circle at bottom right, #FF6B6B 5%, transparent 20%);
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 400px;
            margin: 80px auto;
            padding: 51px;
            background-color: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            border-radius: 82px;
        }

        h1 {
            text-align: center;
            font-family: 'Baloo 2', cursive;
            color: #FF6B6B;
            margin-bottom: 30px;
        }

        .inputbox {
            margin-bottom: 20px;
            position: relative;
        }

        .inputbox input {
            width: 95%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            padding-right: 40px; /* Space for eye */
        }

        .inputbox small {
            display: block;
            color: #555;
            margin-top: 6px;
            font-size: 0.85rem;
        }

        .btw button {
            width: 100%;
            padding: 12px;
            background-color: #00BFA6;
            border: none;
            color: white;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
        }

        .btw button:hover {
            background-color: #009e8c;
        }

        p {
            text-align: center;
            margin-top: 20px;
            font-size: 0.95rem;
        }

        a.acc {
            color: #FF6B6B;
            text-decoration: none;
            font-weight: bold;
        }

        a.acc:hover {
            text-decoration: underline;
        }

        .inputboxs img {
            text-align: center;
            height: 15vh;
        } 

        .container img {
            display: block;
            margin: auto;
            max-width: 100%;
        }

        /* Eye icon */
        .inputbox i {
            position: absolute;
            top: 18%;
            right: -19px;
            cursor: pointer;
            color: #888;
        }
        #toggleConfirmPassword  {
            position: absolute;
            top: 35%;
            right: -20px;
            cursor: pointer;
            color: #888;
        }

        /* Strength bar */
        #strength-bar {
            height: 5px;
            width: 95%;
            margin-top: 5px;
            background: #ddd;
            border-radius: 4px;
            transition: 0.4s;
        }
    </style>
</head>
<body>

<div class="container">
    <form method="POST" action="register_handlers.php">
        <div class="inputboxs">
            <img src="MajamaMeet_logo_design.png" alt="MajamaMeet Logo">
        </div>

        <h1>REGISTER</h1>

        <div class="inputbox">
            <input type="text" name="name" placeholder="Enter your full Name" required>
        </div>

        <div class="inputbox">
            <input type="email" name="email" placeholder="Enter your email" required>
        </div>
        
        <div class="inputbox">
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <i class="fa-regular fa-eye" id="togglePassword"></i>
            <div id="strength-bar"></div>
            <small>Password must be at least 8 characters long and include uppercase, lowercase, number & symbol.</small>
        </div>

        <div class="inputbox">
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
            <i class="fa-regular fa-eye" id="toggleConfirmPassword"></i>
        </div>

        <div class="btw">
            <button type="submit" name="submit">REGISTER</button>
        </div>

        <p>Already have an account? <a class="acc" href="login.php">Login</a></p>
    </form>
</div>

<script>
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#password");
    const strengthBar = document.getElementById("strength-bar");

    togglePassword.addEventListener("click", function () {
        const type = password.getAttribute("type") === "password" ? "text" : "password";
        password.setAttribute("type", type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    password.addEventListener("input", function () {
        const val = password.value;
        let strength = 0;

        if (val.length >= 8) strength++;
        if (val.match(/[A-Z]/)) strength++;
        if (val.match(/[a-z]/)) strength++;
        if (val.match(/[0-9]/)) strength++;
        if (val.match(/[@$!%*?&]/)) strength++;

        if (strength <= 2) {
            strengthBar.style.background = "red";
            strengthBar.style.width = "40%";
        } else if (strength === 3 || strength === 4) {
            strengthBar.style.background = "orange";
            strengthBar.style.width = "70%";
        } else if (strength >= 5) {
            strengthBar.style.background = "green";
            strengthBar.style.width = "100%";
        } else {
            strengthBar.style.background = "#ddd";
            strengthBar.style.width = "0%";
        }
    });

    const toggleConfirmPassword = document.querySelector("#toggleConfirmPassword");
    const confirmPassword = document.querySelector("#confirm_password");

    toggleConfirmPassword.addEventListener("click", function () {
        const type = confirmPassword.getAttribute("type") === "password" ? "text" : "password";
        confirmPassword.setAttribute("type", type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    // Form validation
    document.querySelector("form").addEventListener("submit", function(e) {
        const pass = password.value;
        const confirm = confirmPassword.value;
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

        if (!regex.test(pass)) {
            alert("Please check guideline for password !!");
            e.preventDefault();
        } else if (pass !== confirm) {
            alert("Both Passwords are not same.");
            e.preventDefault();
        }
    });
</script>

</body>
</html>
