<?php
include 'header.php'; 
require 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$host_email = $_SESSION['email'];

// Fetch Events
$sql = "SELECT * FROM events WHERE host_email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $host_email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Handle Delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $del_query = "DELETE FROM events WHERE event_id = ? AND host_email = ?";
    $del_stmt = mysqli_prepare($conn, $del_query);
    mysqli_stmt_bind_param($del_stmt, "is", $delete_id, $host_email);
    if (mysqli_stmt_execute($del_stmt)) {
        echo "<script>
                alert('✅ Event Deleted Successfully!');
                window.location.href='my_events.php';
              </script>";
        exit();
    }
}
?>

<style>
.main-content {
    flex: 1;
    padding: 40px;
    overflow-x: auto;
}
h2 {
    font-family: 'Baloo 2', cursive;
    font-size: 2.5rem;
    color: #FF6B6B;
    text-align: center;
    margin-bottom: 30px;
}
.table-container {
    background: var(--card-bg);
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0px 8px 30px rgba(0,0,0,0.1);
    overflow-x: auto;
}
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 1rem;
    border-radius: 12px;
    overflow: hidden;
}
th, td {
    padding: 14px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}
th {
    background: linear-gradient(to right, #ff9a9e, #fad0c4);
    color: #333;
    font-size: 1.1rem;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
}
tr:hover {
    background-color: #ffecec;
}
.btn-edit, .btn-delete {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    cursor: pointer;
    margin: 5px;
}
.btn-edit {
    background-color: #00BFA6;
    color: white;
}
.btn-edit:hover {
    background-color: #009e8c;
}
.btn-delete {
    background-color: #ff4d4d;
    color: white;
}
.btn-delete:hover {
    background-color: #cc0000;
}
.no-events {
    text-align: center;
    font-size: 1.2rem;
    margin-top: 40px;
    color: var(--text-color);
}
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}
</style>

<div class="main-content">
    <h2>📋 My Created Events</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
    <div class="table-container" style="animation: fadeIn 1s ease;">
        <table>
            <tr>
                <th>Event Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th>Description</th>
                <th>Event Code</th>
                <th>Tickets</th>
                <th>Action</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                <td><?php echo htmlspecialchars(date('h:i A', strtotime($row['event_time']))); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><b><?php echo htmlspecialchars($row['event_code']); ?></b></td>
                <td><b><?php echo htmlspecialchars($row['total_tickets']); ?></b></td>
                <td>
                    <a href="edit_event.php?id=<?php echo $row['event_id']; ?>"><button class="btn-edit">Edit</button></a>
                    <a href="my_events.php?delete_id=<?php echo $row['event_id']; ?>" onclick="return confirm('Are you sure you want to delete this event?');"><button class="btn-delete">Delete</button></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php else: ?>
        <div class="no-events">😔 You have not created any events yet!</div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>