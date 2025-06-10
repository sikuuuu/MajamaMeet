<?php
include 'header.php';
require 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['email'];
$name = $_SESSION['name'];

$sql = "SELECT e.event_name, e.event_code, e.host_email, e.event_date 
        FROM event_participants ep 
        JOIN events e ON ep.event_code = e.event_code 
        WHERE ep.user_email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $user_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$events = [];
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}
?>

<style>
    .main-content {
        padding: 40px 20px;
        background-color: #FEF8F0;
        min-height: 100vh;
    }
    h2 {
        font-family: 'Baloo 2', cursive;
        color: #FF6B6B;
        font-size: 2rem;
        margin-bottom: 30px;
        text-align: center;
    }
    .event-table {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
        border-collapse: collapse;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .event-table th, .event-table td {
        border: 1px solid #ddd;
        padding: 15px;
        text-align: center;
    }
    .event-table th {
        background-color: #FF6B6B;
        color: white;
    }
    .event-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .event-table tr:hover {
        background-color: #ffe6e6;
    }
    .back-link {
        display: block;
        text-align: center;
        margin-top: 30px;
        text-decoration: none;
        color: #00BFA6;
        font-weight: bold;
    }
    .back-link:hover {
        text-decoration: underline;
    }
    .countdown {
        font-weight: bold;
        color: #FF6B6B;
    }
</style>

<div class="main-content">
    <h2>👥 Joined Events for <?php echo strtoupper($name); ?></h2>

    <?php if (!empty($events)): ?>
        <table class="event-table">
            <tr>
                <th>Event Name</th>
                <th>Event Code</th>
                <th>Host Email</th>
                <th>Date</th>
                <th>Countdown</th>
            </tr>
            <?php foreach ($events as $index => $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                <td><?php echo htmlspecialchars($row['event_code']); ?></td>
                <td><?php echo htmlspecialchars($row['host_email']); ?></td>
                <td><?php echo date("d M Y", strtotime($row['event_date'])); ?></td>
                <td><span class="countdown" id="countdown-<?php echo $index; ?>"></span></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center; color: #555;">You haven't joined any events yet.</p>
    <?php endif; ?>

    <a class="back-link" href="dashboard.php">← Back to Dashboard</a>
</div>

<script>
<?php foreach ($events as $index => $row): 
    $eventDateTime = $row['event_date'] . " 00:00:00";
?>
let countdown<?php echo $index; ?> = new Date("<?php echo $eventDateTime; ?>").getTime();
let countdownElem<?php echo $index; ?> = document.getElementById("countdown-<?php echo $index; ?>");

let interval<?php echo $index; ?> = setInterval(function () {
    let now = new Date().getTime();
    let distance = countdown<?php echo $index; ?> - now;

    if (distance < 0) {
        countdownElem<?php echo $index; ?>.innerHTML = "Event Started!";
        clearInterval(interval<?php echo $index; ?>);
    } else {
        let days = Math.floor(distance / (1000 * 60 * 60 * 24));
        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);
        countdownElem<?php echo $index; ?>.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
    }
}, 1000);
<?php endforeach; ?>
</script>

<?php include 'footer.php'; ?>
