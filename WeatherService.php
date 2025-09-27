<?php
require_once 'config.php';

/**
 * 和风天气API服务类
 */
class WeatherService {
    private $apiKey;
    private $baseUrl;
    
    public function __construct() {
        $this->apiKey = QWEATHER_API_KEY;
        $this->baseUrl = QWEATHER_BASE_URL;
    }
    
    /**
     * 根据城市名称获取天气信息
     */
    public function getWeatherByCity($cityName) {
        // 首先获取城市代码
        $cityCode = $this->getCityCode($cityName);
        if (!$cityCode) {
            throw new Exception("未找到城市: " . $cityName);
        }
        
        return $this->getWeatherByCode($cityCode);
    }
    
    /**
     * 根据城市代码获取天气信息
     */
    public function getWeatherByCode($cityCode) {
        $url = $this->baseUrl . "/weather/now?location=" . $cityCode;
        
        $response = $this->makeRequest($url);
        
        if ($response['code'] !== '200') {
            throw new Exception("天气API调用失败: " . $response['code']);
        }
        
        return $response['now'];
    }
    
    /**
     * 获取城市代码
     */
    public function getCityCode($cityName) {
        // 使用GeoAPI v2进行城市查询，支持精确到区
        $baseUrl = str_replace('/v7', '', $this->baseUrl);
        $url = $baseUrl . "/geo/v2/city/lookup?location=" . urlencode($cityName) . "&number=1";
        
        $response = $this->makeRequest($url);
        
        if ($response['code'] !== '200' || empty($response['location'])) {
            return null;
        }
        
        return $response['location'][0]['id'];
    }
    
    /**
     * 获取3天天气预报
     */
    public function getWeatherForecast($cityCode) {
        $url = $this->baseUrl . "/weather/3d?location=" . $cityCode;
        
        $response = $this->makeRequest($url);
        
        if ($response['code'] !== '200') {
            throw new Exception("天气预报API调用失败: " . $response['code']);
        }
        
        return $response['daily'];
    }
    
    /**
     * 发送HTTP请求
     */
    private function makeRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-QW-Api-Key: ' . $this->apiKey,
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL错误: " . $error);
        }
        
        if ($httpCode !== 200) {
            throw new Exception("HTTP请求失败，状态码: " . $httpCode . "，URL: " . $url . "，响应: " . $response);
        }
        
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON解析失败: " . json_last_error_msg());
        }
        
        return $data;
    }
    
    /**
     * 格式化天气信息为描述性文本
     */
    public function formatWeatherDescription($weatherData) {
        $temp = $weatherData['temp'];
        $text = $weatherData['text'];
        $windDir = $weatherData['windDir'];
        $windScale = $weatherData['windScale'];
        $humidity = $weatherData['humidity'];
        $vis = $weatherData['vis'];
        
        $description = "当前天气：{$text}，温度{$temp}°C";
        $description .= "，风向{$windDir}，风力{$windScale}级";
        $description .= "，湿度{$humidity}%，能见度{$vis}公里";
        
        return $description;
    }
}
?>
