<?php
require 'db.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['event_code'])) {
    $event_code = $_POST['event_code'];
    $user_email = $_SESSION['email'];

    // 1. Get user's name from shrikant
    $name_sql = "SELECT name FROM shrikant WHERE email = ?";
    $stmt_name = mysqli_prepare($conn, $name_sql);
    if (!$stmt_name) {
        die("Name prepare failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt_name, "s", $user_email);
    mysqli_stmt_execute($stmt_name);
    $result_name = mysqli_stmt_get_result($stmt_name);

    $user_name = "Unknown";
    if ($row = mysqli_fetch_assoc($result_name)) {
        $user_name = $row['name'];
    }

    // 🔒 Check if the user is the host of the event
    $host_sql = "SELECT host_email FROM events WHERE event_code = ?";
    $stmt_host = mysqli_prepare($conn, $host_sql);
    if (!$stmt_host) {
        die("Host prepare failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt_host, "s", $event_code);
    mysqli_stmt_execute($stmt_host);
    $result_host = mysqli_stmt_get_result($stmt_host);

    if ($row = mysqli_fetch_assoc($result_host)) {
        $host_email = $row['host_email'];
        if ($user_email === $host_email) {
            echo "<script>alert('⚠️ You are the host of this event and cannot join it as a participant!'); window.location.href = 'join_event.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('❌ Event not found.'); window.location.href = 'join_event.php';</script>";
        exit();
    }

    // 2. Check if already joined
    $check_sql = "SELECT * FROM event_participants WHERE event_code = ? AND user_email = ?";
    $stmt_check = mysqli_prepare($conn, $check_sql);
    if (!$stmt_check) {
        die("Check prepare failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt_check, "ss", $event_code, $user_email);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        echo "<script>alert('⚠️ You have already joined this event!'); window.location.href = 'join_event.php';</script>";
        exit();
    }

    // 3. Check remaining tickets
    $ticket_sql = "SELECT remaining_tickets FROM events WHERE event_code = ?";
    $stmt_ticket = mysqli_prepare($conn, $ticket_sql);
    if (!$stmt_ticket) {
        die("Ticket prepare failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt_ticket, "s", $event_code);
    mysqli_stmt_execute($stmt_ticket);
    $result_ticket = mysqli_stmt_get_result($stmt_ticket);

    if ($row = mysqli_fetch_assoc($result_ticket)) {
        $remaining_tickets = (int)$row['remaining_tickets'];

        if ($remaining_tickets <= 0) {
            echo "<script>alert('❌ Sorry, no tickets left for this event!'); window.location.href = 'join_event.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('❌ Event not found.'); window.location.href = 'join_event.php';</script>";
        exit();
    }

    // 4. Insert participant
    $insert_sql = "INSERT INTO event_participants (event_code, user_email, name) VALUES (?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $insert_sql);
    if (!$stmt_insert) {
        die("Insert prepare failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt_insert, "sss", $event_code, $user_email, $user_name);

    if (mysqli_stmt_execute($stmt_insert)) {
        // 5. Reduce ticket count
        $update_sql = "UPDATE events SET remaining_tickets = remaining_tickets - 1 WHERE event_code = ?";
        $stmt_update = mysqli_prepare($conn, $update_sql);
        if (!$stmt_update) {
            die("Update prepare failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_update, "s", $event_code);
        mysqli_stmt_execute($stmt_update);

        echo "<script>alert('🎉 Successfully joined the event!'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "<script>alert('❌ Failed to join the event. Please try again.'); window.location.href = 'join_event.php';</script>";
    }
} else {
    header("Location: join_event.php");
    exit();
}

// Redirect to ticket after success (not needed if success alert redirects above)
header("Location: generate_ticket.php?event_code=$event_code");
exit();
?>
