<?php
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$container = $_GET['container'] ?? '';

function execDocker($command) {
    exec($command . ' 2>&1', $output, $returnCode);
    return ['output' => implode("\n", $output), 'code' => $returnCode];
}

function getContainerStatus($container) {
    $result = execDocker("docker inspect -f '{{.State.Running}}' $container");
    if ($result['code'] === 0 && trim($result['output']) === 'true') {
        return 'running';
    }
    return 'stopped';
}

switch ($action) {
    case 'status':
        echo json_encode(['status' => getContainerStatus($container)]);
        break;

    case 'start':
        $result = execDocker("docker start $container");
        echo json_encode(['success' => $result['code'] === 0, 'message' => $result['output']]);
        break;

    case 'stop':
        $result = execDocker("docker stop $container");
        echo json_encode(['success' => $result['code'] === 0, 'message' => $result['output']]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>
