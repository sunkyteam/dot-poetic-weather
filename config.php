<?php
/**
 * 天气古诗生成系统配置文件
 * 支持环境变量配置，优先使用.env文件中的值
 */

// 加载环境变量
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, '#') === 0 || empty($line)) continue; // 跳过注释行和空行
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // 移除引号
            if (strlen($value) >= 2 && 
                (($value[0] === '"' && $value[-1] === '"') || 
                 ($value[0] === "'" && $value[-1] === "'"))) {
                $value = substr($value, 1, -1);
            }
            $_ENV[$key] = $value;
        }
    }
}

// 和风天气API配置
define('QWEATHER_API_KEY', $_ENV['QWEATHER_API_KEY'] ?? '');
define('QWEATHER_BASE_URL', $_ENV['QWEATHER_BASE_URL'] ?? '');

// GLM-4-Flash模型配置
define('GLM_API_KEY', $_ENV['GLM_API_KEY'] ?? '');
define('GLM_BASE_URL', $_ENV['GLM_BASE_URL'] ?? '');
define('GLM_MODEL', $_ENV['GLM_MODEL'] ?? '');

// 设备推送配置
define('DEVICE_PUSH_URL', $_ENV['DEVICE_PUSH_URL'] ?? '');
define('DEVICE_API_KEY', $_ENV['DEVICE_API_KEY'] ?? '');
define('DEVICE_SERIAL', $_ENV['DEVICE_SERIAL'] ?? '');

// 数据库配置已移除，系统不再使用数据库

// 图片生成配置（固定分辨率，设备要求）
define('IMAGE_WIDTH', 296);
define('IMAGE_HEIGHT', 152);
define('IMAGE_QUALITY', 90);
?>
