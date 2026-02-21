<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$userData = [
    'user' => ['full_name' => 'Basic User', 'email' => 'user@example.com'],
    'guest' => ['full_name' => 'Guest Visitor', 'email' => 'guest@example.com']
];

$user = $userData[$_SESSION['user']];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Broken Access Control Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">

    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl">
        <div class="md:flex">
            <div class="p-8 w-full">
                <div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold">User Profile</div>
                <h1 class="block mt-1 text-lg leading-tight font-medium text-black"><?= htmlspecialchars($user['full_name']) ?></h1>
                <p class="mt-2 text-gray-500">Email: <?= htmlspecialchars($user['email']) ?></p>
                <p class="mt-2 text-gray-500">Role: <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded"><?= htmlspecialchars($_SESSION['role']) ?></span></p>
                
                <div class="mt-6 border-t pt-6">
                    <a href="index.php" class="text-indigo-600 hover:text-indigo-900 font-bold">← Back to Portal</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
