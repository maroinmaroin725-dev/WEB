<?php
require_once '../core/init.php';

AdminController::requireAdmin();

$action = $_GET['action'] ?? null;

switch($action) {
    case 'get':
        $limit = intval($_GET['limit'] ?? 100);
        $logs = AdminController::getLogs($limit);
        jsonResponse(['success' => true, 'logs' => $logs]);
        break;
    
    case 'security':
        $security = AdminController::getSecuritySettings();
        jsonResponse(['success' => true, 'security' => $security]);
        break;
    
    case 'report':
        $report = AdminController::generateReport();
        jsonResponse(['success' => true, 'report' => $report]);
        break;
    
    case 'clear':
        if($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
        
        Database::write('logs', []);
        jsonResponse(['success' => true, 'message' => 'Logs cleared']);
        break;
    
    default:
        jsonResponse(['error' => 'Invalid action'], 400);
}

?>