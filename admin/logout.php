<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

logout_admin();
header("Location: login.php");
exit;
?>
