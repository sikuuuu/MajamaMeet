<?php
include 'header.php'; 
require 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";

// Event Creation Logic
if (isset($_POST['create_event'])) {
    $event_name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);
    $event_time = mysqli_real_escape_string($conn, $_POST['event_time']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $total_tickets = intval($_POST['total_tickets']);
    $host_email = $_SESSION['email'];

    $today = date('Y-m-d');
    $three_years_later = date('Y-m-d', strtotime('+3 years'));

    if ($event_date < $today) {
        $error = "Event date cannot be in the past.";
    } elseif ($event_date > $three_years_later) {
        $error = "Event date cannot be more than 3 years from today.";
    } elseif ($total_tickets < 1) {
        $error = "Total tickets must be at least 1.";
    } else {
        $event_code = strtoupper(substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 12));

        $sql = "INSERT INTO events (event_name, event_date, event_time, location, description, host_email, event_code, total_tickets, remaining_tickets)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssi", $event_name, $event_date, $event_time, $location, $description, $host_email, $event_code, $total_tickets, $total_tickets);

        if (mysqli_stmt_execute($stmt)) {
            $success = "🎉 Event created successfully!<br>Your Event Code is: <b>$event_code</b>. You can find it in the <i>Created Event</i> section.";
        } else {
            $error = "❌ Failed to create event. Please try again.";
        }
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
.create-form {
    background: var(--card-bg);
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
    background: var(--input-bg);
    color: var(--text-color);
}
.create-form button {
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
.create-form button:hover {
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
.toast {
    visibility: hidden;
    min-width: 250px;
    background-color: #00BFA6;
    color: white;
    text-align: center;
    border-radius: 8px;
    padding: 16px;
    position: fixed;
    z-index: 1000;
    top: 30px;
    right: 30px;
    font-size: 17px;
}
.toast.show {
    visibility: visible;
    animation: fadein 0.5s, fadeout 0.5s 2.5s;
}
@keyframes fadein { from {top: 0; opacity: 0;} to {top: 30px; opacity: 1;} }
@keyframes fadeout { from {top: 30px; opacity: 1;} to {top: 0; opacity: 0;} }
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
.date-hint {
    font-size: 0.9rem;
    color: #888;
    margin-top: -10px;
    margin-bottom: 15px;
}
</style>

<?php if (!empty($success)) : ?>
    <div id="toast" class="toast"><?php echo strip_tags($success); ?></div>
    <script>
        document.getElementById("toast").classList.add("show");
        setTimeout(function(){ document.getElementById("toast").classList.remove("show"); }, 10000);
    </script>
<?php endif; ?>

<div class="form-container">
    <form class="create-form" method="POST">
        <h2>Create New Event</h2>

        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

        <input type="text" name="event_name" placeholder="Enter Event Name" required>

        <?php
        $today = date('Y-m-d');
        $max_date = date('Y-m-d', strtotime('+3 years'));
        ?>
        <input type="date" name="event_date" required min="<?php echo $today; ?>" max="<?php echo $max_date; ?>">
        <div class="date-hint">* Select a date between today and <?php echo date('d M Y', strtotime('+3 years')); ?>.</div>

        <input type="time" name="event_time" required>
        <div class="date-hint">* Select appropriate time according to 24-hour format.</div>

        <input type="text" name="location" placeholder="Location" required>
        <textarea name="description" placeholder="Event Description" rows="4" required></textarea>

        <input type="number" name="total_tickets" placeholder="Total Tickets" required min="1">

        <button type="submit" name="create_event">Create Event</button>
    </form>

    <a href="dashboard.php" class="back-btn">&larr; Back to Dashboard</a>
</div>

<?php include 'footer.php'; ?>
