<?php
session_start();
session_unset(); // clear session variables
session_destroy(); // destroy the session
header("Location: login.php"); // send user back to login
exit();
?>
