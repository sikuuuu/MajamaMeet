<?php
include 'header.php'; 
require 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: my_events.php");
    exit();
}

$event_id = intval($_GET['id']);
$host_email = $_SESSION['email'];

// Fetch event details
$sql = "SELECT * FROM events WHERE event_id = ? AND host_email = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "is", $event_id, $host_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {
    header("Location: my_events.php");
    exit();
}

$event = mysqli_fetch_assoc($result);

$success = "";
$error = "";

// Update Event Logic
if (isset($_POST['update_event'])) {
    $event_name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);
    $event_time = mysqli_real_escape_string($conn, $_POST['event_time']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $total_tickets = mysqli_real_escape_string($conn, $_POST['total_tickets']);

    $update_sql = "UPDATE events SET event_name=?, event_date=?, event_time=?, location=?, description=?, total_tickets=? WHERE event_id=? AND host_email=?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    if (!$update_stmt) {
        die("Update prepare failed: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($update_stmt, "sssssisi", $event_name, $event_date, $event_time, $location, $description, $total_tickets, $event_id, $host_email);

    if (mysqli_stmt_execute($update_stmt)) {
        $success = "✅ Event updated successfully!";
        $event['event_name'] = $event_name;
        $event['event_date'] = $event_date;
        $event['event_time'] = $event_time;
        $event['location'] = $location;
        $event['description'] = $description;
        $event['total_tickets'] = $total_tickets;
    } else {
        $error = "❌ Failed to update event. Try again.";
    }
}
?>

<style>
.form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    margin-top: 30px;
    width: 100%;
}
.edit-form {
    background: var(--card-bg);
    padding: 30px 40px;
    border-radius: 20px;
    box-shadow: 0px 8px 30px rgba(0,0,0,0.1);
    max-width: 450px;
    width: 90%;
    animation: fadeIn 1s ease;
}
.edit-form h2 {
    font-family: 'Baloo 2', cursive;
    font-size: 2rem;
    margin-bottom: 20px;
    color: #FF6B6B;
    text-align: center;
}
.edit-form input,
.edit-form textarea,
.edit-form select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 10px;
    font-size: 1rem;
    background: var(--input-bg);
    color: var(--text-color);
}
.edit-form button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 25px;
    background-color: #00BFA6;
    color: white;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s;
}
.edit-form button:hover {
    background-color: #009e8c;
}
.success, .error {
    margin: 10px auto;
    padding: 12px;
    border-radius: 8px;
    text-align: center;
    font-weight: bold;
    width: 90%;
    max-width: 450px;
}
.success { background-color: #d4edda; color: #155724; }
.error { background-color: #ffe0e0; color: #d10000; }
.back-btn {
    display: block;
    margin: 20px auto;
    text-align: center;
    text-decoration: none;
    color: #FF6B6B;
    font-weight: bold;
}
:root {
    --card-bg: #fff;
    --input-bg: #fff;
    --text-color: #333;
}
body.dark-mode {
    --card-bg: #333;
    --input-bg: #444;
    --text-color: #fff;
}
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-20px);}
    to {opacity: 1; transform: translateY(0);}
}
</style>

<div class="form-container">
    <form class="edit-form" method="POST">
        <h2>Edit Your Event</h2>

        <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

        <input type="text" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
        <input type="date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
        <input type="time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>
        <input type="text" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>
        <textarea name="description" rows="4" required><?php echo htmlspecialchars($event['description']); ?></textarea>

        <input type="number" name="total_tickets" value="<?php echo htmlspecialchars($event['total_tickets']); ?>" min="0" required>

        <button type="submit" name="update_event">Update Event</button>
    </form>

    <a href="dashboard.php" class="back-btn">&larr; Back to Dashboard</a>
</div>

<?php include 'footer.php'; ?>
