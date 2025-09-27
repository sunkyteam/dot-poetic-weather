<?php
require_once 'config.php';
require_once 'WeatherService.php';
require_once 'PoetryService.php';
require_once 'ImageService.php';

/**
 * 天气古诗生成应用主类
 */
class WeatherPoetryApp {
    private $weatherService;
    private $poetryService;
    private $imageService;
    
    public function __construct() {
        $this->weatherService = new WeatherService();
        $this->poetryService = new PoetryService();
        $this->imageService = new ImageService();
    }
    
    /**
     * 生成天气古诗并推送到设备
     */
    public function generateWeatherPoetry($cityName = '') {
        try {
            // 1. 获取天气信息
            if (empty($cityName)) {
                throw new Exception("请提供城市名称");
            }
            
            $weatherData = $this->weatherService->getWeatherByCity($cityName);
            
            // 2. 生成古诗
            $poetry = $this->poetryService->generatePoetry($weatherData, $cityName);
            
            // 3. 生成图片并推送
            $result = $this->imageService->generateAndPushPoetryImage($poetry, $weatherData, $cityName);
            
            $message = '天气古诗生成成功！';
            if (isset($result['push_result']['success']) && $result['push_result']['success']) {
                $message .= ' 图片已推送到设备。';
            } elseif (isset($result['push_result']['error'])) {
                $message .= ' 图片生成成功，但推送失败：' . $result['push_result']['error'];
            } else {
                $message .= ' 图片已推送到设备。'; // 默认认为推送成功
            }
            
            return [
                'success' => true,
                'city' => $cityName,
                'weather' => $weatherData,
                'poetry' => $poetry,
                'image_path' => $result['image_path'],
                'push_result' => $result['push_result'],
                'message' => $message
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => '生成失败：' . $e->getMessage()
            ];
        }
    }
    
    // 城市列表功能已移除，系统支持任意城市名称输入
    
    // 历史记录和缓存功能已移除，系统不再使用数据库和缓存
    
    /**
     * 获取系统状态
     */
    public function getSystemStatus() {
        $status = [
            'api_keys_configured' => $this->checkApiKeys()
        ];
        
        return $status;
    }
    
    // 缓存相关方法已移除
    
    /**
     * 检查API密钥配置
     */
    private function checkApiKeys() {
        return !empty(QWEATHER_API_KEY) && 
               !empty(GLM_API_KEY) && 
               !empty(DEVICE_API_KEY) &&
               !empty(DEVICE_SERIAL) &&
               QWEATHER_API_KEY !== 'your_qweather_api_key_here' &&
               GLM_API_KEY !== 'your_glm_api_key_here' &&
               DEVICE_API_KEY !== 'your_device_api_key_here' &&
               DEVICE_SERIAL !== 'your_device_serial_here';
    }
}
?>
