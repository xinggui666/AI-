<?php
require '../lib/Database.php';
require '../lib/Auth.php';

session_start();

$db = new Database();
$auth = new Auth($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'register') {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        
        $db->query(
            "INSERT INTO users (username, password_hash) VALUES (?, ?)",
            [$username, $password]
        );
        echo json_encode(['status' => 'success']);
        
    } elseif ($action === 'login') {
        $user = $db->query(
            "SELECT * FROM users WHERE username = ?",
            [$_POST['username']]
        )->fetch();
        
        if ($user && password_verify($_POST['password'], $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(401);
            echo json_encode(['error' => '认证失败']);
        }
    }
}
