<?php

define("DIAG_TTY", "/dev/ttyS0");

function diag_serial($msg)
{   // outputs msg to serial port
    // usermod -a -G dialout www-data
    $fh_tty = fopen(DIAG_TTY, "w+");
    fwrite($fh_tty, $msg . "\n");
    fclose($fh_tty);
}

diag_serial($_SERVER['REMOTE_ADDR'] . "\t" . $_SERVER['HTTP_USER_AGENT']);

function redirect_and_die($msg)
{
    diag_serial($msg);
    header("Location: ui.php");
    die();
}


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    redirect_and_die('ERR_NO_POST');
}

$post = file_get_contents("php://input");

if (!$post) {
    redirect_and_die('STILL_NO_POST');
}



define("CONFIG_DB_PHP", "/etc/roylabcon/debian-db.php");
if (is_readable(CONFIG_DB_PHP)) {
    require (CONFIG_DB_PHP);
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $mysqli = new mysqli($dbserver, $dbuser, $dbpass, $dbname);

} else {
    diag_serial('CONFIG_DB_PHP');
    die();
}




$json = json_encode($_SERVER);

/* Prepared statement, stage 1: prepare */
$stmt = $mysqli->prepare("INSERT INTO raw_requests (time, server_global, post) VALUES (?, ?, ?)");
$stmt->bind_param("dss", $_SERVER["REQUEST_TIME_FLOAT"], $json, $post);
#    i 	corresponding variable has type int
#    d 	corresponding variable has type float
#    s 	corresponding variable has type string
#    b 	corresponding variable is a blob and will be sent in packets

$stmt->execute();
diag_serial($json);
