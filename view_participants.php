<?php
include 'header.php';
require 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$event_details = null;
$participants = [];
$host_name = "";
$error = "";
$remaining_tickets = null;

if (isset($_POST['search'])) {
    $event_code = mysqli_real_escape_string($conn, $_POST['event_code']);
    $host_email = $_SESSION['email'];

    $sql = "SELECT * FROM events WHERE event_code = ? AND host_email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $event_code, $host_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $event_details = mysqli_fetch_assoc($result);

        $host_sql = "SELECT name FROM shrikant WHERE email = ?";
        $stmt_host = mysqli_prepare($conn, $host_sql);
        mysqli_stmt_bind_param($stmt_host, "s", $host_email);
        mysqli_stmt_execute($stmt_host);
        $result_host = mysqli_stmt_get_result($stmt_host);
        $host_row = mysqli_fetch_assoc($result_host);
        $host_name = $host_row ? $host_row['name'] : $host_email;

        $part_sql = "SELECT name, user_email FROM event_participants WHERE event_code = ?";
        $stmt_part = mysqli_prepare($conn, $part_sql);
        mysqli_stmt_bind_param($stmt_part, "s", $event_code);
        mysqli_stmt_execute($stmt_part);
        $result_part = mysqli_stmt_get_result($stmt_part);
        while ($row = mysqli_fetch_assoc($result_part)) {
            $participants[] = $row;
        }

        $remaining_tickets = $event_details['remaining_tickets'];
    } else {
        $error = "⚠️ You are not the host of this event, so it is not visible under your account.";
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
    background: var(--card-bg);
    padding: 30px 40px;
    border-radius: 20px;
    box-shadow: 0px 8px 30px rgba(0,0,0,0.1);
    max-width: 500px;
    width: 90%;
    text-align: center;
    animation: fadeIn 0.8s ease;
}
.search-form h2 {
    font-family: 'Baloo 2', cursive;
    font-size: 2rem;
    margin-bottom: 20px;
    color: #00BFA6;
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
    background-color: #FF6B6B;
    color: white;
    cursor: pointer;
}
.search-form button:hover {
    background-color: #e05757;
}
.error-message {
    color: red;
    margin-top: 20px;
    font-weight: bold;
}
.event-info, .participants-list {
    background: var(--card-bg);
    margin-top: 30px;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 800px;
    width: 90%;
}
.event-info h3, .participants-list h3 {
    color: #FF6B6B;
    margin-bottom: 15px;
}
.event-info p {
    font-size: 1rem;
    margin: 6px 0;
}
.participants-list table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.participants-list th, .participants-list td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
}
.participants-list th {
    background-color: #f8f8f8;
}
.remove-button {
    background-color: #e74c3c;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.remove-button:hover {
    background-color: #c0392b;
}

/* Styling same as you had before */
</style>

<div class="main-content">
    <div class="search-form">
        <h2>📋 View Event Participants</h2>
        <form method="POST">
            <input type="text" name="event_code" placeholder="Enter your Event Code" required maxlength="12" minlength="12">
            <button type="submit" name="search">Search</button>
        </form>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>

    <?php if ($event_details): ?>
        <div class="event-info">
            <h3><?php echo htmlspecialchars($event_details['event_name']); ?></h3>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($event_details['event_date']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($event_details['location']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($event_details['description']); ?></p>
            <p><strong>Host:</strong> <?php echo htmlspecialchars($host_name); ?></p>
            <p><strong>Total Tickets:</strong> <?php echo $event_details['total_tickets']; ?></p>
            <p><strong>Remaining Tickets:</strong> <?php echo $remaining_tickets; ?></p>
        </div>

        <div class="participants-list">
            <h3>👥 Participants List</h3>
            <?php if (count($participants) > 0): ?>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($participants as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['name']); ?></td>
                            <td><?php echo htmlspecialchars($p['user_email']); ?></td>
                            <td>
                                <form method="POST" action="remove_participant.php" onsubmit="return confirm('Are you sure you want to remove this participant?');">
                                    <input type="hidden" name="event_code" value="<?php echo htmlspecialchars($event_details['event_code']); ?>">
                                    <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($p['user_email']); ?>">
                                    <button type="submit" class="remove-button">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No one has joined yet.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
