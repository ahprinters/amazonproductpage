<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Variant.php';
require_once __DIR__ . '/../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: colors.php");
    exit;
}

try {
    $colorName = trim($_POST['color_name'] ?? '');
    $colorCode = trim($_POST['color_code'] ?? '');

    if ($colorName === '') {
        throw new Exception("Color name is required.");
    }

    if ($colorCode === '') {
        throw new Exception("Color code is required.");
    }

    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $colorCode)) {
        throw new Exception("Invalid color code.");
    }

    $variantModel = new Variant($conn);
    $variantModel->createColor($colorName, $colorCode);

    Flash::set('success', 'Color created successfully.');

    header("Location: colors.php");
    exit;

} catch (Exception $e) {
    Flash::set('error', $e->getMessage());

    header("Location: colors.php");
    exit;
}