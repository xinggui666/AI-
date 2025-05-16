<?php
require '../lib/Database.php';
require '../lib/AIClient.php';

header('Content-Type: application/json');
session_start();

$db = new Database();
$ai = new AIClient();

$data = json_decode(file_get_contents('php://input'), true);

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("请先登录", 401);
    }
    
    $response = $ai->deepseekChat($data['messages']);
    
    // 保存聊天记录
    $db->query(
        "INSERT INTO chat_logs (user_id, provider, prompt, response) 
        VALUES (?, ?, ?, ?)",
        [
            $_SESSION['user_id'],
            'deepseek-chat',
            $data['messages'][0]['content'],
            $response
        ]
    );
    
    echo json_encode(['response' => $response]);
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['error' => $e->getMessage()]);
}
