<?php
require_once 'config.php';

/**
 * 图片生成和推送服务类
 * 适配设备分辨率：296x152px
 */
class ImageService {
    private $devicePushUrl;
    private $deviceApiKey;
    private $deviceSerial;
    
    public function __construct() {
        $this->devicePushUrl = DEVICE_PUSH_URL;
        $this->deviceApiKey = DEVICE_API_KEY;
        $this->deviceSerial = DEVICE_SERIAL;
    }
    
    /**
     * 生成古诗图片并推送到设备
     */
    public function generateAndPushPoetryImage($poetry, $weatherData, $cityName = '') {
        // 生成图片数据
        $imageData = $this->generatePoetryImageData($poetry, $weatherData, $cityName);
        
        // 推送到设备
        $pushResult = $this->pushImageDataToDevice($imageData);
        
        return [
            'image_path' => null, // 不保存文件
            'push_result' => $pushResult
        ];
    }
    
    /**
     * 生成古诗图片数据（296x152分辨率）
     */
    private function generatePoetryImageData($poetry, $weatherData, $cityName = '') {
        $width = 296;  // 严格使用296px
        $height = 152; // 严格使用152px
        
        // 创建画布
        $image = imagecreatetruecolor($width, $height);
        
        // 设置背景色（白色）
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgColor);
        
        // 获取字体路径
        $fontPath = $this->getFontPath();
        
        // 绘制天气信息（顶部）
        $this->drawWeatherHeader($image, $weatherData, $cityName, $fontPath, $width, $height);
        
        // 绘制古诗内容（只显示两句，居中）
        $this->drawPoetryContent($image, $poetry, $fontPath, $width, $height);
        
        // 获取图片数据
        ob_start();
        imagepng($image, null, 9);
        $imageData = ob_get_contents();
        ob_end_clean();
        
        imagedestroy($image);
        
        return $imageData;
    }
    
    /**
     * 绘制天气信息头部（左上角紧凑布局）
     */
    private function drawWeatherHeader($image, $weatherData, $cityName, $fontPath, $width, $height) {
        $textColor = imagecolorallocate($image, 0, 0, 0);
        $tempColor = imagecolorallocate($image, 0, 0, 0);
        $cityColor = imagecolorallocate($image, 0, 0, 0);
        
        // 天气描述（左上角）
        $weatherText = $weatherData['text'];
        $weatherSize = 12;
        $weatherX = 8;
        $weatherY = 16;
        
        if ($fontPath) {
            imagettftext($image, $weatherSize, 0, $weatherX, $weatherY, $textColor, $fontPath, $weatherText);
        } else {
            imagestring($image, 2, $weatherX, $weatherY - 8, $weatherText, $textColor);
        }
        
        // 城市名称（天气描述下方）
        if ($cityName) {
            $cityText = $cityName;
            $citySize = 10;
            $cityX = 8;
            $cityY = 31;
            
            if ($fontPath) {
                imagettftext($image, $citySize, 0, $cityX, $cityY, $cityColor, $fontPath, $cityText);
            } else {
                imagestring($image, 1, $cityX, $cityY - 6, $cityText, $cityColor);
            }
        }
        
        // 温度（右上角，往左移动更多）
        $tempText = $weatherData['temp'] . '°C';
        $tempSize = 16;
        $tempX = $width - 70;
        $tempY = 20;
        
        if ($fontPath) {
            imagettftext($image, $tempSize, 0, $tempX, $tempY, $tempColor, $fontPath, $tempText);
        } else {
            imagestring($image, 3, $tempX, $tempY - 10, $tempText, $tempColor);
        }
    }
    
    /**
     * 绘制古诗内容（两句诗词大字体居中，上下排列）
     */
    private function drawPoetryContent($image, $poetry, $fontPath, $width, $height) {
        $textColor = imagecolorallocate($image, 0, 0, 0);
        
        // 取前两句古诗，清理内容
        $poetryLines = array_slice($poetry['content'], 0, 2);
        
        // 处理每行诗词，确保每行最多7字，并添加标点符号
        $processedLines = [];
        foreach ($poetryLines as $index => $line) {
            $cleanLine = $this->cleanPoetryLine($line);
            if (!empty($cleanLine)) {
                $splitLines = $this->splitPoetryLine($cleanLine, 7);
                
                // 为每句诗词添加标点符号
                if (count($poetryLines) == 2) {
                    if ($index == 0) {
                        // 第一句最后一行加逗号
                        $lastIndex = count($splitLines) - 1;
                        $splitLines[$lastIndex] .= '，';
                    } else {
                        // 第二句最后一行加句号
                        $lastIndex = count($splitLines) - 1;
                        $splitLines[$lastIndex] .= '。';
                    }
                }
                
                $processedLines = array_merge($processedLines, $splitLines);
            }
        }
        
        if (empty($processedLines)) {
            return; // 如果没有内容就不显示
        }
        
        // 计算可用空间（左右下留出空间）
        $margin = 20; // 左右下边距
        $availableWidth = $width - 2 * $margin;
        $availableHeight = $height - $margin - 70; // 减去顶部天气信息空间和底部边距
        
        // 动态调整字体大小以适应屏幕，增大最大字体
        $contentSize = $this->calculateOptimalFontSize($processedLines, $availableWidth, $availableHeight, $fontPath);
        $lineHeight = $contentSize + 12; // 增大行间距
        
        // 计算总高度
        $totalHeight = count($processedLines) * $lineHeight;
        
        // 诗词位置往下移更多，不与地址在同一行
        $startY = 80 + ($availableHeight - $totalHeight) / 2; // 从80px开始，避免与天气信息重叠
        
        foreach ($processedLines as $index => $line) {
            if ($fontPath) {
                $lineBox = imagettfbbox($contentSize, 0, $fontPath, $line);
                $lineX = ($width - $lineBox[4]) / 2;
                $lineY = $startY + $index * $lineHeight;
                imagettftext($image, $contentSize, 0, $lineX, $lineY, $textColor, $fontPath, $line);
            } else {
                $lineX = ($width - strlen($line) * ($contentSize / 2)) / 2;
                $lineY = $startY + $index * $lineHeight;
                imagestring($image, 3, $lineX, $lineY - ($contentSize / 2), $line, $textColor);
            }
        }
    }
    
    /**
     * 计算最佳字体大小以适应屏幕
     */
    private function calculateOptimalFontSize($lines, $maxWidth, $maxHeight, $fontPath) {
        $maxFontSize = 35; // 增大最大字体大小
        $minFontSize = 14; // 增大最小字体大小
        
        for ($fontSize = $maxFontSize; $fontSize >= $minFontSize; $fontSize--) {
            $lineHeight = $fontSize + 10;
            $totalHeight = count($lines) * $lineHeight;
            
            if ($totalHeight > $maxHeight) {
                continue;
            }
            
            $fitsWidth = true;
            foreach ($lines as $line) {
                if ($fontPath) {
                    $lineBox = imagettfbbox($fontSize, 0, $fontPath, $line);
                    $lineWidth = $lineBox[4];
                } else {
                    $lineWidth = strlen($line) * ($fontSize / 2);
                }
                
                if ($lineWidth > $maxWidth) {
                    $fitsWidth = false;
                    break;
                }
            }
            
            if ($fitsWidth) {
                return $fontSize;
            }
        }
        
        return $minFontSize;
    }
    
    /**
     * 清理诗词内容，移除数字和标点符号
     */
    private function cleanPoetryLine($line) {
        // 移除数字
        $cleanLine = preg_replace('/[0-9]/u', '', $line);
        // 移除标点符号
        $cleanLine = preg_replace('/[，。！？；：]/u', '', $cleanLine);
        $cleanLine = preg_replace('/["""\'\']/u', '', $cleanLine);
        $cleanLine = preg_replace('/[（）【】《》]/u', '', $cleanLine);
        // 移除多余空格
        $cleanLine = preg_replace('/\s+/u', '', $cleanLine);
        return trim($cleanLine);
    }
    
    /**
     * 将诗词行按指定长度分割
     */
    private function splitPoetryLine($line, $maxLength = 7) {
        $result = [];
        $chars = preg_split('//u', $line, -1, PREG_SPLIT_NO_EMPTY);
        
        $currentLine = '';
        foreach ($chars as $char) {
            if (mb_strlen($currentLine . $char, 'UTF-8') <= $maxLength) {
                $currentLine .= $char;
            } else {
                if (!empty($currentLine)) {
                    $result[] = $currentLine;
                }
                $currentLine = $char;
            }
        }
        
        if (!empty($currentLine)) {
            $result[] = $currentLine;
        }
        
        return $result;
    }
    
    /**
     * 获取天气图标
     */
    private function getWeatherIcon($weatherText) {
        $iconMap = [
            '晴' => '☀',
            '多云' => '⛅',
            '阴' => '☁',
            '雨' => '🌧',
            '雪' => '❄',
            '雾' => '🌫',
            '霾' => '😷',
            '雷' => '⚡'
        ];
        
        return $iconMap[$weatherText] ?? '☀';
    }
    
    /**
     * 获取字体路径
     */
    private function getFontPath() {
        // 检查项目目录下的字体文件
        $localFonts = [
            __DIR__ . '/font/SanJiXingKaiJianTi-Cu-2.ttf',  // 用户上传的字体
            __DIR__ . '/fonts/simhei.ttf',
            __DIR__ . '/fonts/simsun.ttc',
            __DIR__ . '/fonts/msyh.ttc',
            __DIR__ . '/fonts/arial.ttf'
        ];
        
        foreach ($localFonts as $fontPath) {
            if (file_exists($fontPath)) {
                return $fontPath;
            }
        }
        
        // 如果没有找到字体，返回空字符串（使用默认字体）
        return '';
    }
    
    /**
     * 推送图片数据到设备
     */
    private function pushImageDataToDevice($imageData) {
        $base64Image = base64_encode($imageData);
        
        // 准备推送数据（按照API文档格式）
        $postData = [
            'refreshNow' => true,
            'deviceId' => $this->deviceSerial,
            'image' => $base64Image,
            'link' => 'https://dot.mindreset.tech',
            'border' => 0,
            'ditherType' => 'NONE'  // 文字图片建议关闭抖动
        ];
        
        // 发送推送请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->devicePushUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->deviceApiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("设备推送cURL错误: " . $error);
            return ['success' => false, 'error' => 'cURL错误: ' . $error];
        }
        
        if ($httpCode !== 200) {
            error_log("设备推送失败，状态码: " . $httpCode . "，响应: " . substr($response, 0, 500));
            return ['success' => false, 'error' => '推送失败，状态码: ' . $httpCode . '，响应: ' . substr($response, 0, 200)];
        }
        
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("设备推送响应解析失败: " . json_last_error_msg() . "，原始响应: " . substr($response, 0, 200));
            return ['success' => false, 'error' => '响应解析失败: ' . json_last_error_msg()];
        }
        
        return $result;
    }
}
?>