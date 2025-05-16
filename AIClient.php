<?php
class AIClient {
    public function deepseekChat($messages, $model = 'deepseek-chat') {
        $apiKey = getenv('DEEPSEEK_KEY');
        $url = 'https://api.deepseek.com/v1/chat/completions';
        
        $response = $this->callAPI($url, [
            'model' => $model,
            'messages' => $messages,
            'stream' => false
        ], $apiKey);
        
        return $response['choices'][0]['message']['content'];
    }
    
    private function callAPI($url, $data, $apiKey) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer '.$apiKey
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true
        ]);
        
        $response = curl_exec($ch);
        return json_decode($response, true);
    }
}
