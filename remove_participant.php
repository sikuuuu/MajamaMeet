<?php
require 'db.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_code = mysqli_real_escape_string($conn, $_POST['event_code']);
    $user_email = mysqli_real_escape_string($conn, $_POST['user_email']);

    // Check if user is actually in that event
    $check_sql = "SELECT * FROM event_participants WHERE event_code = ? AND user_email = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ss", $event_code, $user_email);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($result) > 0) {
        // Delete participant
        $delete_sql = "DELETE FROM event_participants WHERE event_code = ? AND user_email = ?";
        $stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($stmt, "ss", $event_code, $user_email);

        if (mysqli_stmt_execute($stmt)) {
            // Increase remaining tickets
            $update_sql = "UPDATE events SET remaining_tickets = remaining_tickets + 1 WHERE event_code = ?";
            $stmt_update = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($stmt_update, "s", $event_code);
            mysqli_stmt_execute($stmt_update);
        }
    }
}

header("Location: view_participants.php");
exit();
