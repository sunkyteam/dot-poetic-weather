<?php
require_once 'WeatherPoetryApp.php';

$app = new WeatherPoetryApp();
$message = '';
$result = null;

// 处理GET请求（直接生成）
if ($_GET && isset($_GET['city'])) {
    $cityName = $_GET['city'];
    $result = $app->generateWeatherPoetry($cityName);
    $message = $result['message'];
}

// 处理POST请求（表单提交）
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'generate':
            $cityName = $_POST['city'] ?? '';
            $result = $app->generateWeatherPoetry($cityName);
            $message = $result['message'];
            break;
            
        // 缓存功能已移除
    }
}

// 获取系统状态
$status = $app->getSystemStatus();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>天气古诗生成系统</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group select,
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: #4facfe;
        }
        
        .btn {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
            margin-right: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .result-section {
            background: #fff;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .result-section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        
        .weather-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .poetry-content {
            background: #fff;
            border: 2px solid #4facfe;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .poetry-title {
            font-size: 1.5em;
            color: #4facfe;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .poetry-lines {
            font-size: 1.2em;
            line-height: 2;
            color: #333;
        }
        
        .poetry-lines div {
            margin-bottom: 10px;
        }
        
        .status-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .status-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e1e5e9;
        }
        
        .status-item .label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        
        .status-item .value {
            font-size: 1.2em;
            color: #333;
        }
        
        .config-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .config-link a {
            color: #4facfe;
            text-decoration: none;
            font-weight: bold;
        }
        
        .config-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 10px;
            }
            
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .content {
                padding: 20px;
            }
            
            .status-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌤️ 天气古诗生成系统</h1>
            <p>根据实时天气生成优美的古诗，并推送到您的设备</p>
            <p style="font-size: 0.9em; margin-top: 10px; opacity: 0.8;">
                💡 支持GET请求：<code>?city=北京</code> 或 <code>?city=上海</code>
            </p>
        </div>
        
        <div class="content">
            <?php if ($message): ?>
                <div class="message <?php echo $result && $result['success'] ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-section">
                <h3>🎯 生成天气古诗</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="generate">
                    
                    <div class="form-group">
                        <label for="city">输入城市名称：</label>
                        <input type="text" name="city" id="city" placeholder="例如：北京、上海、广州" required
                               value="<?php echo htmlspecialchars($_POST['city'] ?? $_GET['city'] ?? ''); ?>">
                        <small style="color: #666; font-size: 0.9em; margin-top: 5px; display: block;">
                            💡 支持任意城市名称，系统会自动获取城市代码
                        </small>
                    </div>
                    
                    <button type="submit" class="btn">✨ 生成古诗</button>
                </form>
            </div>
            
            <?php if ($result && $result['success']): ?>
                <div class="result-section">
                    <h3>📊 生成结果</h3>
                    
                    <div class="weather-info">
                        <h4>🌤️ 天气信息</h4>
                        <p><strong>城市：</strong><?php echo htmlspecialchars($result['city']); ?></p>
                        <p><strong>天气：</strong><?php echo htmlspecialchars($result['weather']['text']); ?></p>
                        <p><strong>温度：</strong><?php echo htmlspecialchars($result['weather']['temp']); ?>°C</p>
                    </div>
                    
                    <div class="poetry-content">
                        <div class="poetry-lines">
                            <?php foreach ($result['poetry']['content'] as $line): ?>
                                <div><?php echo htmlspecialchars($line); ?></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <p><strong>推送状态：</strong><?php echo $result['push_result'] ? '✅ 推送成功' : '❌ 推送失败'; ?></p>
                </div>
            <?php endif; ?>
            
            <div class="status-section">
                <h3>📈 系统状态</h3>
                <div class="status-grid">
                    <div class="status-item">
                        <div class="label">API配置状态</div>
                        <div class="value"><?php echo $status['api_keys_configured'] ? '✅ 已配置' : '❌ 未配置'; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="config-link">
                <a href="config_page.php">⚙️ 系统配置</a>
            </div>
        </div>
    </div>
</body>
</html>
