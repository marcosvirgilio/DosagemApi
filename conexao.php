<?php

//ambiente marcosvirgilio.online
$servername = '193.203.175.91';
$username = 'u683605471_dosagem';
$password = '';
$dbname = 'u683605471_dosagem';

// Create connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

?>
