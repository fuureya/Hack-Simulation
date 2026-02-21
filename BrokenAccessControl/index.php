<?php
session_start();

// Simple credentials for simulation
$users = [
    'user' => 'password123',
    'guest' => 'guest123'
];

$error = '';

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($users[$username]) && $users[$username] === $password) {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = 'user'; // Default role
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Portal - Broken Access Control Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h1 class="text-2xl font-bold mb-6 text-center text-blue-600">Member Portal</h1>

        <?php if (!isset($_SESSION['user'])): ?>
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <button type="submit" name="login" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full focus:outline-none focus:shadow-outline">
                    Login
                </button>
            </form>
            <p class="mt-4 text-xs text-gray-500 text-center">Hint: user / password123</p>
        <?php else: ?>
            <div class="text-center">
                <p class="text-lg">Welcome, <span class="font-bold"><?= htmlspecialchars($_SESSION['user']) ?></span>!</p>
                <p class="text-sm text-gray-500 mb-6">You are logged in as a standard member.</p>
                
                <div class="space-y-4">
                    <a href="profile.php" class="block bg-gray-200 hover:bg-gray-300 py-2 px-4 rounded">My Profile</a>
                    
                    <!-- THE VULNERABILITY: This link is "hidden" but accessible if you know the URL -->
                    <!-- In a real scenario, an attacker might find this via directory brute-forcing or source code info -->
                    <a href="admin.php" class="block text-blue-500 hover:underline text-sm">Go to Admin Settings (Restricted)</a>
                </div>

                <hr class="my-6">
                <a href="?logout=1" class="text-red-500 hover:underline">Logout</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
