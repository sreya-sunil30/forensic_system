<?php
// logout.php
session_start();
session_unset();
session_destroy();
    header("Location: /forensic_system/index.php"); 
exit();
?>