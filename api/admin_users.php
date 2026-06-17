<?php
require_once '../core/init.php';

AdminController::requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

switch($action) {
    case 'list':
        $users = Database::read('users');
        jsonResponse(['success' => true, 'users' => $users]);
        break;
    
    case 'ban':
        if($method !== 'POST') exit;
        $result = AdminController::manageUser(
            $_POST['user_id'],
            'ban',
            ['reason' => $_POST['reason'] ?? '']
        );
        jsonResponse($result);
        break;
    
    case 'unban':
        if($method !== 'POST') exit;
        $result = AdminController::manageUser(
            $_POST['user_id'],
            'unban',
            []
        );
        jsonResponse($result);
        break;
    
    case 'set_points':
        if($method !== 'POST') exit;
        $result = AdminController::manageUser(
            $_POST['user_id'],
            'set_points',
            ['points' => $_POST['points']]
        );
        jsonResponse($result);
        break;
    
    case 'promote':
        if($method !== 'POST') exit;
        $result = AdminController::manageUser(
            $_POST['user_id'],
            'promote',
            []
        );
        jsonResponse($result);
        break;
    
    case 'delete':
        if($method !== 'POST') exit;
        $result = AdminController::manageUser(
            $_POST['user_id'],
            'delete',
            []
        );
        jsonResponse($result);
        break;
    
    default:
        jsonResponse(['error' => 'Invalid action'], 400);
}

?>