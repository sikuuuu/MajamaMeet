<?php
include 'header.php'; 
require 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$event_details = null;
$host_name = "";
$error = "";

if (isset($_POST['search'])) {
    $event_code = mysqli_real_escape_string($conn, $_POST['event_code']);

    $sql = "SELECT * FROM events WHERE event_code = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Prepare failed (event): " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "s", $event_code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $event_details = mysqli_fetch_assoc($result);

        // Get host name
        $host_email = $event_details['host_email'];
        $user_sql = "SELECT name FROM shrikant WHERE email = ?";
        $user_stmt = mysqli_prepare($conn, $user_sql);
        if (!$user_stmt) {
            die("Prepare failed (user): " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($user_stmt, "s", $host_email);
        mysqli_stmt_execute($user_stmt);
        $user_result = mysqli_stmt_get_result($user_stmt);
        if (mysqli_num_rows($user_result) > 0) {
            $user = mysqli_fetch_assoc($user_result);
            $host_name = $user['name'];
        } else {
            $host_name = $host_email;
        }
    } else {
        $error = "❌ No event found with that code!";
    }
}
?>

<style>
.main-content {
    flex: 1;
    padding: 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.search-form {
    margin-top: 30px;
    background: var(--card-bg);
    padding: 30px 40px;
    border-radius: 20px;
    box-shadow: 0px 8px 30px rgba(0,0,0,0.1);
    max-width: 500px;
    width: 90%;
    text-align: center;
    animation: fadeIn 1s ease;
}
.search-form h2 {
    font-family: 'Baloo 2', cursive;
    font-size: 2rem;
    margin-bottom: 20px;
    color: #FF6B6B;
}
.search-form input[type="text"] {
    width: 70%;
    padding: 10px;
    margin-right: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    background: var(--input-bg);
    color: var(--text-color);
}
.search-form button {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background-color: #00BFA6;
    color: white;
    cursor: pointer;
}
.search-form button:hover {
    background-color: #009e8c;
}
.error-message {
    color: red;
    margin-top: 20px;
    font-weight: bold;
}
.event-card {
    background: var(--card-bg);
    margin-top: 30px;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 500px;
    width: 90%;
    text-align: left;
}
.event-card h3 {
    color: #FF6B6B;
    margin-bottom: 15px;
}
.event-card p {
    margin: 6px 0;
    font-size: 1rem;
    color: var(--text-color);
}
.join-btn {
    margin-top: 20px;
    padding: 10px 25px;
    background-color: #00BFA6;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    cursor: pointer;
}
.join-btn:hover {
    background-color: #009e8c;
}
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-20px);}
    to {opacity: 1; transform: translateY(0);}
}
</style>

<div class="main-content">
    <div class="search-form">
        <h2>🔎 Find Your Event</h2>
        <form method="POST">
            <input type="text" name="event_code" placeholder="Enter 12-digit Event Code" required maxlength="12" minlength="12">
            <button type="submit" name="search">Search</button>
        </form>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>

    <?php if ($event_details): ?>
        <div class="event-card">
            <h3><?php echo htmlspecialchars($event_details['event_name']); ?></h3>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($event_details['event_date']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($event_details['location']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($event_details['description']); ?></p>
            <p><strong>Host:</strong> <?php echo htmlspecialchars($host_name); ?></p>
            <p><strong>Tickets Left:</strong> <?php echo (int)$event_details['remaining_tickets']; ?></p>

            <?php if ((int)$event_details['remaining_tickets'] > 0): ?>
                <form method="POST" action="join_handler.php">
                    <input type="hidden" name="event_code" value="<?php echo htmlspecialchars($event_details['event_code']); ?>">
                    <button class="join-btn" type="submit">Join Now</button>
                </form>
            <?php else: ?>
                <p style="color: red; font-weight: bold; margin-top: 15px;">❌ Tickets are sold out!</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
