<?php
$adminName = $admin['name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Panel</title>

    <link rel="stylesheet" href="/amazon/assets/css/style.css">
</head>

<body class="bg-slate-100 text-slate-900">

<div class="min-h-screen flex">

    <?php include __DIR__ . '/admin-sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0">

        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-6 sticky top-0 z-30">

            <div class="flex items-center gap-3">

                <button
                    type="button"
                    onclick="openMobileSidebar()"
                    class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-xl"
                >
                    ☰
                </button>

                <div>
                    <h1 class="text-lg font-bold">Amazon Admin</h1>
                    <p class="text-xs text-slate-500 hidden sm:block">Manage store operations</p>
                </div>

            </div>

            <div class="flex items-center gap-3 md:gap-4">

                <a href="../product.php"
                   class="hidden md:inline-flex items-center rounded-lg bg-slate-100 hover:bg-slate-200 px-4 py-2 text-sm font-semibold">
                    View Store
                </a>

                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold">
                        <?= htmlspecialchars($adminName) ?>
                    </p>
                    <p class="text-xs text-slate-500">Administrator</p>
                </div>

                <a href="logout.php"
                   class="inline-flex items-center rounded-lg bg-red-500 hover:bg-red-600 text-white px-3 md:px-4 py-2 text-sm font-semibold">
                    Logout
                </a>

            </div>

        </header>

        <main class="flex-1 p-4 md:p-6">