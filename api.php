<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

$action = $_GET['action'] ?? '';
$container = $_GET['container'] ?? '';

// Validate container name to prevent injection
$allowedContainers = ['neobank-csrf-container', 'cinemax-xss-container', 'neohms-sqli-container'];
if (!in_array($container, $allowedContainers)) {
    echo json_encode(['error' => 'Invalid container name']);
    exit;
}

function execDocker($command) {
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    return [
        'output' => implode("\n", $output), 
        'code' => $returnCode,
        'command' => $command
    ];
}

function getContainerStatus($container) {
    $result = execDocker("docker inspect -f '{{.State.Running}}' $container");
    
    // If container doesn't exist or error
    if ($result['code'] !== 0) {
        return 'stopped';
    }
    
    if (trim($result['output']) === 'true') {
        return 'running';
    }
    return 'stopped';
}

switch ($action) {
    case 'status':
        $status = getContainerStatus($container);
        echo json_encode([
            'status' => $status,
            'container' => $container
        ]);
        break;

    case 'start':
        // Check if container exists first
        $checkResult = execDocker("docker ps -a --filter name=$container --format '{{.Names}}'");
        
        if (trim($checkResult['output']) !== $container) {
            // Container doesn't exist, need to create it first
            $labPath = '';
            if ($container === 'neobank-csrf-container') {
                $labPath = '/var/www/html/CSRF';
            } elseif ($container === 'cinemax-xss-container') {
                $labPath = '/var/www/html/XSS';
            } elseif ($container === 'neohms-sqli-container') {
                $labPath = '/var/www/html/SqlInjection';
            }
            
            $result = execDocker("cd $labPath && docker-compose up -d 2>&1");
        } else {
            $result = execDocker("docker start $container");
        }
        
        echo json_encode([
            'success' => $result['code'] === 0,
            'message' => $result['output'],
            'debug' => $result
        ]);
        break;

    case 'stop':
        $result = execDocker("docker stop $container");
        echo json_encode([
            'success' => $result['code'] === 0,
            'message' => $result['output'],
            'debug' => $result
        ]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>
