<?php

require_once '../classes/Database.php';

$conn = Database::connect();

if ($conn) {
    echo "Database connected successfully.";
}