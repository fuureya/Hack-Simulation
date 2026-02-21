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
    ],
    'shop-bac-container' => [
        'name' => 'BAC',
        'image' => 'bac-shop-bac',
        'port' => '8009:80',
        'path' => '/var/www/html/BrokenAccessControl',
        'network' => 'labsec-network'
    ],
    'ticket-idor-container' => [
        'name' => 'IDOR',
        'image' => 'idor-ticket-idor',
        'port' => '8010:80',
        'path' => '/var/www/html/Idoor',
        'network' => 'labsec-network'
    ],
    // New OWASP Labs
    'safevault-cryptofail-container' => [
        'name' => 'CryptoFail',
        'image' => 'cryptofail-safevault',
        'port' => '8003:80',
        'path' => '/var/www/html/CryptoFail',
        'network' => 'labsec-network',
        'env' => 'DB_HOST=labsec-db DB_NAME=safevault_crypto DB_USER=root DB_PASS=labsec_root_2026'
    ],
    'quickloan-insecuredesign-container' => [
        'name' => 'InsecureDesign',
        'image' => 'insecuredesign-quickloan',
        'port' => '8004:80',
        'path' => '/var/www/html/InsecureDesign',
        'network' => 'labsec-network',
        'env' => 'DB_HOST=labsec-db DB_NAME=quickloan_insecure DB_USER=root DB_PASS=labsec_root_2026'
    ],
    'devops-secmisconfig-container' => [
        'name' => 'SecMisconfig',
        'image' => 'secmisconfig-devops',
        'port' => '8005:80',
        'path' => '/var/www/html/SecMisconfig',
        'network' => 'labsec-network',
        'env' => 'DB_HOST=labsec-db DB_NAME=devops_misconfig DB_USER=root DB_PASS=labsec_root_2026'
    ],
    'securelogin-authfail-container' => [
        'name' => 'AuthFail',
        'image' => 'authfail-securelogin',
        'port' => '8006:80',
        'path' => '/var/www/html/AuthFail',
        'network' => 'labsec-network',
        'env' => 'DB_HOST=labsec-db DB_NAME=securelogin_auth DB_USER=root DB_PASS=labsec_root_2026'
    ],
    'audittrail-loggingfail-container' => [
        'name' => 'LoggingFail',
        'image' => 'loggingfail-audittrail',
        'port' => '8007:80',
        'path' => '/var/www/html/LoggingFail',
        'network' => 'labsec-network',
        'env' => 'DB_HOST=labsec-db DB_NAME=audittrail_log DB_USER=root DB_PASS=labsec_root_2026'
    ],
    'webfetcher-ssrf-container' => [
        'name' => 'SSRF',
        'image' => 'ssrf-webfetcher',
        'port' => '8008:80',
        'path' => '/var/www/html/SSRF',
        'network' => 'labsec-network',
        'env' => ''
    ],
    'insecurelibrary-legacycms-container' => [
        'name' => 'InsecureLibrary',
        'image' => 'insecurelibrary-legacycms',
        'port' => '8011:80',
        'path' => '/var/www/html/InsecureLibrary',
        'network' => 'labsec-network',
        'env' => 'DB_HOST=labsec-db DB_NAME=insecure_library DB_USER=root DB_PASS=labsec_root_2026'
    ],
    'integrityfail-objectrelay-container' => [
        'name' => 'IntegrityFail',
        'image' => 'integrityfail-objectrelay',
        'port' => '8012:80',
        'path' => '/var/www/html/IntegrityFail',
        'network' => 'labsec-network',
        'env' => ''
    ],
];

// Validate container name
if (!isset($labConfig[$container])) {
    echo json_encode(['error' => 'Invalid container name']);
    exit;
}

function execDocker($command)
{
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    return [
        'output' => implode("\n", $output),
        'code' => $returnCode,
        'command' => $command
    ];
}

function getContainerStatus($container)
{
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

            // Environment variables string to flags
            $envFlags = "";
            if (isset($config['env']) && !empty($config['env'])) {
                $envs = explode(" ", $config['env']);
                foreach ($envs as $env) {
                    $envFlags .= "-e " . escapeshellarg($env) . " ";
                }
            }

            // Run container
            $runCmd = "docker run -d --name $container " .
                $envFlags .
                "-p {$config['port']} " .
                "--network {$config['network']} " .
                "--restart unless-stopped " .
                "{$config['image']}";

            $result = execDocker($runCmd);

            if ($result['code'] !== 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to run container: ' . $result['output'],
                    'debug' => $result
                ]);
                exit;
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Container started successfully',
            'debug' => isset($result) ? $result : ['status' => 'already_running']
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