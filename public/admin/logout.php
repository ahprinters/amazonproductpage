<?php

require_once '../../classes/Database.php';
require_once '../../classes/Auth.php';
require_once '../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->logout();

Flash::set('success', 'Logged out successfully.');

header("Location: login.php");
exit;