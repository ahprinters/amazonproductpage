<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Flash.php';

$conn = Database::connect();
$auth = new Auth($conn);

if ($auth->checkAdmin()) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $auth->attemptAdminLogin($email, $password);

        Flash::set('success', 'Admin login successful.');

        header("Location: dashboard.php");
        exit;

    } catch (Exception $e) {
        Flash::set('error', $e->getMessage());

        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login</title>

    <link rel="stylesheet" href="/amazon/assets/css/style.css">
</head>

<body class="min-h-screen bg-slate-100 text-slate-900">

    <main class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

        <section class="hidden lg:flex bg-slate-900 text-white items-center justify-center p-12">

            <div class="max-w-lg">
                <div class="w-16 h-16 rounded-2xl bg-orange-500 flex items-center justify-center text-3xl mb-8">
                    🛒
                </div>

                <h1 class="text-5xl font-bold leading-tight">
                    Store Admin Panel
                </h1>

                <p class="text-slate-300 mt-6 text-lg leading-8">
                    Manage orders, customers, invoices, and product operations from one secure dashboard.
                </p>

                <div class="grid grid-cols-2 gap-4 mt-10">

                    <div class="rounded-2xl bg-slate-800 p-5">
                        <p class="text-3xl mb-2">🔐</p>
                        <p class="font-semibold">Secure Login</p>
                        <p class="text-sm text-slate-400 mt-1">Admin access only</p>
                    </div>

                    <div class="rounded-2xl bg-slate-800 p-5">
                        <p class="text-3xl mb-2">📦</p>
                        <p class="font-semibold">Order Control</p>
                        <p class="text-sm text-slate-400 mt-1">Track every order</p>
                    </div>

                </div>
            </div>

        </section>

        <section class="flex items-center justify-center px-4 py-12">

            <div class="w-full max-w-md">

                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">

                    <div class="text-center mb-8">
                        <div class="w-16 h-16 rounded-2xl bg-orange-100 flex items-center justify-center text-3xl mx-auto mb-4">
                            🔐
                        </div>

                        <h2 class="text-3xl font-bold">
                            Admin Login
                        </h2>

                        <p class="text-sm text-slate-500 mt-2">
                            Sign in to continue to your dashboard.
                        </p>
                    </div>

                    <?php include __DIR__ . '/../../includes/flash-message.php'; ?>

                    <form action="login.php" method="POST" class="space-y-5">

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                required
                                placeholder="admin@example.com"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                required
                                placeholder="Enter password"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-slate-900 hover:bg-slate-700 text-white px-5 py-3 text-sm font-bold transition"
                        >
                            Login to Dashboard
                        </button>

                    </form>

                    <div class="mt-6 text-center">
                        <a href="../product.php"
                           class="text-sm font-semibold text-orange-600 hover:text-orange-700">
                            Back to Store
                        </a>
                    </div>

                </div>

                <p class="text-center text-xs text-slate-500 mt-6">
                    © <?= date('Y') ?> Admin Panel. Built with Tailwind CSS.
                </p>

            </div>

        </section>

    </main>

</body>
</html>