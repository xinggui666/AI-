<?php
require '../lib/Database.php';

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'generate_cards') {
        $count = $_POST['count'];
        $cards = [];
        
        for ($i = 0; $i < $count; $i++) {
            $card = bin2hex(random_bytes(8));
            $db->query(
                "INSERT INTO recharge_cards (card_key) VALUES (?)",
                [$card]
            );
        }
        echo json_encode(['generated' => $count]);
        
    } elseif ($action === 'redeem') {
        $card = $_POST['card_key'];
        $result = $db->query(
            "UPDATE recharge_cards SET used=1 WHERE card_key=? AND used=0",
            [$card]
        );
        
        if ($result->rowCount() > 0) {
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => '卡密无效']);
        }
    }
}
