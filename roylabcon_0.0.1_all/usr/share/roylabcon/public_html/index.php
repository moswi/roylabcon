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