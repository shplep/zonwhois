<?php
require_once '../includes/config.php';
require_once '../includes/security.php';

logout_admin();
header('Location: login.php');
exit;
?> 