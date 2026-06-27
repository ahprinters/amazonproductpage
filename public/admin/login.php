<?php

require_once '../../classes/Database.php';
require_once '../../classes/Auth.php';
require_once '../../classes/Flash.php';

$conn = Database::connect();
$auth = new Auth($conn);

if ($auth->checkAdmin()) {
    header("Location: orders.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $auth->attemptAdminLogin($email, $password);

        Flash::set('success', 'Admin login successful.');

        header("Location: orders.php");
        exit;

    } catch (Exception $e) {
        Flash::set('error', $e->getMessage());

        header("Location: login.php");
        exit;
    }
}
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/flash-message.php'; ?>

<main class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">

    <div class="w-full max-w-md border rounded-lg bg-white p-8 shadow-sm">

        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold">Admin Login</h1>
            <p class="text-sm text-gray-600 mt-2">
                Sign in to manage orders and store operations.
            </p>
        </div>

        <form action="login.php" method="POST" class="space-y-4">

            <div>
                <label class="block text-sm font-medium mb-1">
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    required
                    class="w-full border rounded-md px-3 py-2 text-sm"
                    placeholder="admin@example.com"
                >
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    required
                    class="w-full border rounded-md px-3 py-2 text-sm"
                    placeholder="Enter password"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-gray-900 hover:bg-gray-700 text-white rounded-full py-2 font-semibold"
            >
                Login
            </button>

        </form>

        <div class="mt-6 text-center">
            <a href="../product.php" class="text-sm text-blue-600 hover:text-orange-600">
                Back to Store
            </a>
        </div>

    </div>

</main>

<?php include '../../includes/footer.php'; ?>