<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

$action = $_GET['action'] ?? '';
$container = $_GET['container'] ?? '';

// Lab configuration mapping
$labConfig = [
    'neobank-csrf-container' => [
        'name' => 'CSRF',
        'image' => 'csrf-neobank-csrf',
        'port' => '8888:80',
        'path' => '/var/www/html/CSRF',
        'network' => 'labsec-network'
    ],
    'cinemax-xss-container' => [
        'name' => 'XSS',
        'image' => 'xss-cinemax-xss',
        'port' => '8001:80',
        'path' => '/var/www/html/XSS',
        'network' => 'labsec-network'
    ],
    'neohms-sqli-container' => [
        'name' => 'SQLi',
        'image' => 'sqlinjection-neohms-sqli',
        'port' => '8002:80',
        'path' => '/var/www/html/SqlInjection',
        'network' => 'labsec-network'
    ]
];

// Validate container name
if (!isset($labConfig[$container])) {
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
        $config = $labConfig[$container];
        
        // Check if container exists
        $checkResult = execDocker("docker ps -a --filter name=$container --format '{{.Names}}'");
        
        if (trim($checkResult['output']) === $container) {
            // Container exists, just start it
            $result = execDocker("docker start $container");
        } else {
            // Container doesn't exist, need to create it
            // First, check if image exists, if not build it
            $imageCheck = execDocker("docker images -q {$config['image']}");
            
            if (empty(trim($imageCheck['output']))) {
                // Build image first
                $buildCmd = "cd {$config['path']} && docker build -t {$config['image']} .";
                $buildResult = execDocker($buildCmd);
                
                if ($buildResult['code'] !== 0) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to build image',
                        'debug' => $buildResult
                    ]);
                    exit;
                }
            }
            
            // Run container
            $runCmd = "docker run -d --name $container " .
                      "-p {$config['port']} " .
                      "-v {$config['path']}:/var/www/html " .
                      "--network {$config['network']} " .
                      "--restart unless-stopped " .
                      "{$config['image']}";
            
            $result = execDocker($runCmd);
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
