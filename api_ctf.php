<?php
header('Content-Type: application/json');
session_start();

// Use db.php from root if available, otherwise define connection
$host = 'labsec-db';
$db   = 'labsec_ctf';
$user = 'root';
$pass = 'labsec_root_2026';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'submit') {
    $lab_id = $_POST['lab_id'] ?? '';
    $level = $_POST['level'] ?? '';
    $flag = trim($_POST['flag'] ?? '');

    if (!$lab_id || !$level || !$flag) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
        exit;
    }

    // Check if flag is correct
    $stmt = $pdo->prepare("SELECT id FROM challenges WHERE lab_id = ? AND level = ? AND flag = ?");
    $stmt->execute([$lab_id, $level, $flag]);
    $challenge = $stmt->fetch();

    if ($challenge) {
        // Record submission
        $stmt = $pdo->prepare("INSERT INTO submissions (challenge_id, submitted_flag, is_correct) VALUES (?, ?, TRUE)");
        $stmt->execute([$challenge['id'], $flag]);
        
        echo json_encode(['success' => true, 'message' => 'Flag Benar! Selamat!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Flag Salah. Coba lagi!']);
    }
} elseif ($action === 'status') {
    $lab_id = $_GET['lab_id'] ?? '';
    
    if (!$lab_id) {
        // Get all progress
        $stmt = $pdo->query("
            SELECT c.lab_id, c.level, MAX(s.is_correct) as completed
            FROM challenges c
            LEFT JOIN submissions s ON c.id = s.challenge_id
            GROUP BY c.lab_id, c.level
        ");
        $results = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $results]);
    } else {
        $stmt = $pdo->prepare("
            SELECT c.level, MAX(s.is_correct) as completed
            FROM challenges c
            LEFT JOIN submissions s ON c.id = s.challenge_id
            WHERE c.lab_id = ?
            GROUP BY c.level
        ");
        $stmt->execute([$lab_id]);
        $results = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $results]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
