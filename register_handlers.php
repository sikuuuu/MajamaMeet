<?php
session_start();
require 'db.php'; // Connect to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];

    // Check if passwords match
    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    // Check if the email already exists
    $check = mysqli_query($conn, "SELECT * FROM shrikant WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Email already registered. Please login.'); window.location.href = 'login.php';</script>";
        exit();
    }

    // Save password in plain text
    $insert = mysqli_query($conn, "INSERT INTO shrikant (Name, email, password) VALUES ('$name', '$email', '$password')");

    if ($insert) {
        echo "<script>alert('Registration successful! Please login.'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again.'); window.history.back();</script>";
    }
}
?>
