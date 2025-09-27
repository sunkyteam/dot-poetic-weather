<?php
require_once 'config.php';

/**
 * GLM-4-Flash古诗生成服务类
 */
class PoetryService {
    private $apiKey;
    private $baseUrl;
    private $model;
    
    public function __construct() {
        $this->apiKey = GLM_API_KEY;
        $this->baseUrl = GLM_BASE_URL;
        $this->model = GLM_MODEL;
    }
    
    /**
     * 根据天气信息生成古诗
     */
    public function generatePoetry($weatherData, $cityName = '') {
        $weatherDescription = $this->formatWeatherForAI($weatherData);
        
        $prompt = $this->buildPoetryPrompt($weatherDescription, $cityName);
        
        $response = $this->callGLMAPI($prompt);
        
        return $this->parsePoetryResponse($response);
    }
    
    /**
     * 构建古诗生成的提示词
     */
    private function buildPoetryPrompt($weatherDescription, $cityName) {
        $prompt = "请根据以下天气信息，生成一首符合当前环境的古诗：\n\n";
        $prompt .= "天气描述：{$weatherDescription}\n";
        
        if ($cityName) {
            $prompt .= "城市：{$cityName}\n";
        }
        
        $prompt .= "\n要求：\n";
        $prompt .= "• 古诗要符合当前天气的意境和氛围\n";
        $prompt .= "• 语言优美，富有诗意\n";
        $prompt .= "• 格式为七言绝句或五言绝句\n";
        $prompt .= "• 包含标题\n";
        $prompt .= "• 体现天气特点和季节感\n";
        $prompt .= "• 如果是特定城市，可以融入城市特色\n";
        $prompt .= "• 不要包含任何数字、标点符号或特殊字符\n";
        $prompt .= "• 只输出纯净的古诗文字内容\n\n";
        $prompt .= "请直接输出古诗，格式如下：\n";
        $prompt .= "【标题】\n";
        $prompt .= "诗句1\n";
        $prompt .= "诗句2\n";
        $prompt .= "诗句3\n";
        $prompt .= "诗句4\n";
        $prompt .= "—— 作者名";
        
        return $prompt;
    }
    
    /**
     * 格式化天气信息供AI使用
     */
    private function formatWeatherForAI($weatherData) {
        $temp = $weatherData['temp'];
        $text = $weatherData['text'];
        $windDir = $weatherData['windDir'];
        $windScale = $weatherData['windScale'];
        $humidity = $weatherData['humidity'];
        $vis = $weatherData['vis'];
        
        // 根据天气状况添加诗意描述
        $weatherMap = [
            '晴' => '阳光明媚，万里无云',
            '多云' => '云卷云舒，天空如画',
            '阴' => '阴云密布，天色朦胧',
            '雨' => '细雨绵绵，润物无声',
            '雪' => '雪花纷飞，银装素裹',
            '雾' => '薄雾缭绕，如诗如画',
            '霾' => '雾霾笼罩，朦胧不清'
        ];
        
        $weatherDesc = $weatherMap[$text] ?? $text;
        
        $description = "{$weatherDesc}，温度{$temp}°C";
        
        // 根据温度添加季节感
        if ($temp < 0) {
            $description .= "，寒冬时节";
        } elseif ($temp < 10) {
            $description .= "，初春或深秋";
        } elseif ($temp < 20) {
            $description .= "，春秋时节";
        } elseif ($temp < 30) {
            $description .= "，初夏或初秋";
        } else {
            $description .= "，盛夏时节";
        }
        
        // 添加风力描述
        if ($windScale > 0) {
            $windDesc = $this->getWindDescription($windScale);
            $description .= "，{$windDesc}";
        }
        
        return $description;
    }
    
    /**
     * 获取风力描述
     */
    private function getWindDescription($windScale) {
        $windMap = [
            1 => '微风轻拂',
            2 => '清风徐来',
            3 => '和风习习',
            4 => '清风阵阵',
            5 => '风起云涌',
            6 => '疾风劲吹',
            7 => '狂风大作',
            8 => '暴风骤起',
            9 => '狂风暴雨',
            10 => '飓风肆虐'
        ];
        
        return $windMap[$windScale] ?? "风力{$windScale}级";
    }
    
    /**
     * 调用GLM API
     */
    private function callGLMAPI($prompt) {
        $data = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => '你是一位精通古典诗词的AI助手，擅长根据天气和环境创作优美的古诗。'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.8,
            'max_tokens' => 1000,
            'stream' => false
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception("GLM API调用失败，状态码: " . $httpCode);
        }
        
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("GLM API响应解析失败: " . json_last_error_msg());
        }
        
        if (!isset($result['choices'][0]['message']['content'])) {
            throw new Exception("GLM API响应格式错误");
        }
        
        return $result['choices'][0]['message']['content'];
    }
    
    /**
     * 解析古诗响应
     */
    private function parsePoetryResponse($response) {
        $lines = explode("\n", trim($response));
        $poetry = [
            'title' => '',
            'content' => [],
            'author' => '',
            'raw' => $response
        ];
        
        $isTitle = false;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // 检查是否是标题
            if (preg_match('/^【(.+)】$/', $line, $matches)) {
                $poetry['title'] = $matches[1];
                $isTitle = true;
            } 
            // 检查是否是作者（以——开头）
            elseif (preg_match('/^——\s*(.+)$/', $line, $matches)) {
                $poetry['author'] = $matches[1];
            }
            // 其他行作为诗句内容
            elseif ($isTitle) {
                $poetry['content'][] = $line;
            }
        }
        
        // 如果没有找到标题，使用第一行作为标题
        if (empty($poetry['title']) && !empty($poetry['content'])) {
            $poetry['title'] = array_shift($poetry['content']);
        }
        
        return $poetry;
    }
}
?>
