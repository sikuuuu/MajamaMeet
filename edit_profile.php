<?php
session_start();
require 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user data
$query = "SELECT * FROM shrikant WHERE email = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$error = '';
$success = '';

if (isset($_POST['update'])) {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check old password
    if ($old_password !== $user['password']) {
        $error = "Old password does not match!";
    } else if (!empty($new_password)) {
        // Validate new password
        if ($new_password !== $confirm_password) {
            $error = "New passwords do not match!";
        } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{12,}$/', $new_password)) {
            $error = "New password must meet the strong guidelines!";
        } else {
            $update_sql = "UPDATE shrikant SET Name = ?, email = ?, password = ? WHERE email = ?";
            $stmt2 = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($stmt2, "ssss", $new_name, $new_email, $new_password, $email);

            if (mysqli_stmt_execute($stmt2)) {
                $_SESSION['name'] = $new_name;
                $_SESSION['email'] = $new_email;
                header("Location: profile.php?msg=ProfileUpdated");
                exit();
            } else {
                $error = "Failed to update profile!";
            }
        }
    } else {
        $update_sql = "UPDATE shrikant SET Name = ?, email = ? WHERE email = ?";
        $stmt2 = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt2, "sss", $new_name, $new_email, $email);

        if (mysqli_stmt_execute($stmt2)) {
            $_SESSION['name'] = $new_name;
            $_SESSION['email'] = $new_email;
            header("Location: profile.php?msg=ProfileUpdated");
            exit();
        } else {
            $error = "Failed to update profile!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile | MajamaMeet</title>
    <link rel="icon" href="MajamaMeet_logo_design.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Montserrat&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #FFDEE9, #B5FFFC);
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .edit-profile-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px 30px;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            text-align: center;
            animation: fadeIn 1s ease;
        }

        .edit-profile-card h2 {
            font-family: 'Baloo 2', cursive;
            font-size: 2rem;
            color: #FF6B6B;
            margin-bottom: 20px;
        }

        .edit-profile-card input[type="text"],
        .edit-profile-card input[type="email"],
        .edit-profile-card input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 1rem;
            background: #fff;
            color: #333;
        }

        .edit-profile-card button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #00BFA6, #009e8c);
            border: none;
            color: white;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .edit-profile-card button:hover {
            background: linear-gradient(to right, #009e8c, #00BFA6);
        }

        .error, .success {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.95rem;
        }
        .error {
            background-color: #ffe0e0;
            color: #d10000;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .inputbox {
            position: relative;
        }
        .inputbox i {
            position: absolute;
            right: -4px;
            top: 38%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-30px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<div class="edit-profile-card">
    <h2>Edit Profile</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['Name']); ?>" required placeholder="Full Name">
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required placeholder="Email">

        <div class="inputbox">
            <input type="password" id="old_password" name="old_password" placeholder="Enter Old Password" required>
            <i class="fas fa-eye" id="toggleOldPassword"></i>
        </div>

        <small style="display:block; color:#555; font-size:0.85rem; margin-bottom:10px;">Password must be at least 12 characters long and include uppercase, lowercase, number & symbol.</small>

        <div class="inputbox">
            <input type="password" id="new_password" name="new_password" placeholder="New Password (optional)">
            <i class="fas fa-eye" id="toggleNewPassword"></i>
        </div>

        <div class="inputbox">
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm New Password">
            <i class="fas fa-eye" id="toggleConfirmPassword"></i>
        </div>

        <button type="submit" name="update">Update Profile</button>
    </form>
</div>

<script>
function togglePassword(inputId, toggleId) {
    const input = document.getElementById(inputId);
    const toggle = document.getElementById(toggleId);

    toggle.addEventListener('click', function() {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
}

togglePassword('old_password', 'toggleOldPassword');
togglePassword('new_password', 'toggleNewPassword');
togglePassword('confirm_password', 'toggleConfirmPassword');
</script>

</body>
</html>
