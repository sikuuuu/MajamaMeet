<?php
include 'header.php';
require 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch events the user created
$created_events_sql = "SELECT * FROM events WHERE host_email = ? ORDER BY event_date DESC";
$created_stmt = mysqli_prepare($conn, $created_events_sql);
mysqli_stmt_bind_param($created_stmt, "s", $email);
mysqli_stmt_execute($created_stmt);
$created_events_result = mysqli_stmt_get_result($created_stmt);

// Fetch events the user joined
$joined_events_sql = "SELECT e.* FROM events e 
                      JOIN event_participants ep ON e.event_code = ep.event_code 
                      WHERE ep.user_email = ? ORDER BY e.event_date DESC";
$joined_stmt = mysqli_prepare($conn, $joined_events_sql);
mysqli_stmt_bind_param($joined_stmt, "s", $email);
mysqli_stmt_execute($joined_stmt);
$joined_events_result = mysqli_stmt_get_result($joined_stmt);
?>

<style>
.main-content {
    flex: 1;
    padding: 30px 40px;
    background: #f9f9f9;
}
.section-title {
    font-size: 1.6rem;
    color: #FF6B6B;
    margin-bottom: 20px;
    font-family: 'Baloo 2', cursive;
}
.event-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}
.event-card h3 {
    margin: 0;
    font-size: 1.2rem;
    color: #333;
}
.event-card p {
    margin: 5px 0;
    font-size: 0.95rem;
    color: #555;
}
.section-divider {
    margin: 40px 0 20px;
    border-top: 2px dashed #ccc;
}
</style>

<div class="main-content">
    <h2 class="section-title">📝 Your Created Events</h2>
    <?php if (mysqli_num_rows($created_events_result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($created_events_result)): ?>
            <div class="event-card">
                <h3><?php echo htmlspecialchars($row['event_name']); ?></h3>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($row['event_date']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                <p><strong>Status:</strong> Created</p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No created events found.</p>
    <?php endif; ?>

    <div class="section-divider"></div>

    <h2 class="section-title">✅ Events You've Joined</h2>
    <?php if (mysqli_num_rows($joined_events_result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($joined_events_result)): ?>
            <div class="event-card">
                <h3><?php echo htmlspecialchars($row['event_name']); ?></h3>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($row['event_date']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                <p><strong>Status:</strong> Joined</p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No joined events found.</p>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
