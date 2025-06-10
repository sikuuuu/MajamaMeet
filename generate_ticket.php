<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "rajgor";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get event_code from URL parameter
$event_code = $_GET['event_code'] ?? '';
if (empty($event_code)) {
    die("Event code not provided.");
}

// Fetch event details
$sql = "SELECT e.event_name, e.event_date, e.event_time, s.Name AS host_name
        FROM events e
        JOIN shrikant s ON e.host_email = s.email
        WHERE e.event_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $event_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid event code.");
}

$row = $result->fetch_assoc();
$event_name = strtoupper($row['event_name']);
$event_date = $row['event_date'];
$event_time = $row['event_time'];
$host_name = strtoupper($row['host_name']);

// Generate QR Code
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?data=$event_code&size=100x100";
// $qr_image = base64_encode(file_get_contents($qr_url));
// $qr_src = 'data:image/png;base64,' . $qr_image;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Ticket</title>
    <style>
        body {
            background: #eee;
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        .ticket-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .ticket {
            width: 800px;
            height: 300px;
            display: flex;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .left {
            width: 75%;
            background: #fbbd08;
            padding: 30px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .title {
            background: black;
            padding: 15px;
            border-radius: 10px;
            font-size: 26px;
            font-weight: bold;
        }
        .info {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .box {
            background: black;
            flex: 1;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
        }
        .box h5 {
            color: #fbbd08;
            margin: 0 0 10px;
        }
        .box p {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .code {
            background: black;
            text-align: center;
            border-radius: 10px;
            padding: 15px;
        }
        .code small {
            display: block;
            color: #fbbd08;
        }
        .code strong {
            font-size: 18px;
        }

        .right {
            width: 25%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 10px;
            padding: 20px;
        }

        .qr-code {
            width: 100px;
            height: 100px;
        }

        .right-text {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            text-align: center;
        }

        #downloadBtn {
            display: block;
            margin: 0 auto;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            background: #fbbd08;
            color: black;
            border-radius: 8px;
            cursor: pointer;
        }
        #downloadBtn:hover {
            background: #e0a800;
        }
    </style>
</head>
<body>

<div class="ticket-container">
    <div class="ticket" id="ticket">
        <div class="left">
            <div class="title"><?= htmlspecialchars($event_name) ?></div>
            <div class="info">
                <div class="box">
                    <h5>DATE</h5>
                    <p><?= $event_date ?></p>
                </div>
                <div class="box">
                    <h5>TIME</h5>
                    <p><?= $event_time ?></p>
                </div>
                <div class="box">
                    <h5>HOST</h5>
                    <p><?= $host_name ?></p>
                </div>
            </div>
            <div class="code">
                <small>Event Code</small>
                <strong><?= htmlspecialchars($event_code) ?></strong>
            </div>
        </div>
        <div class="right">
            <img src="<?= $qr_src ?>" alt="QR Code" class="qr-code">
            <div class="right-text">MAJAMAMEET</div>
            <div class="right-text">TICKET</div>
        </div>
    </div>
</div>

<button id="downloadBtn">Download Ticket</button>

<!-- html2canvas script -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
    document.getElementById('downloadBtn').addEventListener('click', function () {
        html2canvas(document.getElementById('ticket')).then(function(canvas) {
            const link = document.createElement('a');
            link.download = 'event_ticket.png';
            link.href = canvas.toDataURL();
            link.click();
        });
    });
</script>

</body>
</html>
