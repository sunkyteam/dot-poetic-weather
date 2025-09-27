<?php
require_once 'config.php';
require_once 'WeatherPoetryApp.php';

$app = new WeatherPoetryApp();
$status = $app->getSystemStatus();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统配置 - 天气古诗生成系统</title>
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
            max-width: 800px;
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
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4facfe;
        }
        
        .form-group .help-text {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
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
        
        .status-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .status-item:last-child {
            border-bottom: none;
        }
        
        .status-label {
            font-weight: bold;
            color: #333;
        }
        
        .status-value {
            color: #666;
        }
        
        .status-value.success {
            color: #28a745;
        }
        
        .status-value.error {
            color: #dc3545;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #4facfe;
            text-decoration: none;
            font-weight: bold;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .api-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .api-info h4 {
            color: #1976d2;
            margin-bottom: 10px;
        }
        
        .api-info p {
            color: #666;
            margin-bottom: 5px;
        }
        
        .api-info a {
            color: #1976d2;
            text-decoration: none;
        }
        
        .api-info a:hover {
            text-decoration: underline;
        }
        
        .config-status {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .config-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .config-item:last-child {
            border-bottom: none;
        }
        
        .config-label {
            font-weight: bold;
            color: #333;
        }
        
        .config-value {
            color: #28a745;
            font-weight: bold;
        }
        
        .config-note {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .config-note h4 {
            color: #1976d2;
            margin-bottom: 10px;
        }
        
        .config-note p {
            color: #666;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚙️ 系统配置</h1>
            <p>配置API密钥和系统参数</p>
        </div>
        
        <div class="content">
            <div class="api-info">
                <h4>📋 配置说明</h4>
                <p><strong>🔒 安全配置：</strong> 系统已配置为使用环境变量文件(.env)存储敏感信息</p>
                <p style="margin-left: 20px;">• 所有API密钥已预配置在.env文件中</p>
                <p style="margin-left: 20px;">• 如需修改配置，请编辑.env文件</p>
                <p style="margin-left: 20px;">• 参考env.example文件了解配置格式</p>
                <p><strong>📝 API文档：</strong></p>
                <p style="margin-left: 20px;">• <a href="https://dev.qweather.com/" target="_blank">和风天气API</a></p>
                <p style="margin-left: 20px;">• <a href="https://open.bigmodel.cn/" target="_blank">GLM-4-Flash API</a></p>
                <p style="margin-left: 20px;">• <a href="https://dot.mindreset.tech/docs/server/studio/api/image_api" target="_blank">设备推送API</a></p>
            </div>
            
            <div class="form-section">
                <h3>🔧 当前配置状态</h3>
                <div class="config-status">
                    <div class="config-item">
                        <span class="config-label">和风天气API：</span>
                        <span class="config-value">✅ 已配置</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">GLM-4-Flash API：</span>
                        <span class="config-value">✅ 已配置</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">设备推送API：</span>
                        <span class="config-value">✅ 已配置</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">设备序列号：</span>
                        <span class="config-value">✅ 已配置</span>
                    </div>
                </div>
                
                <div class="config-note">
                    <h4>📝 配置说明</h4>
                    <p>• 所有敏感配置已存储在.env文件中，确保安全性</p>
                    <p>• 如需修改配置，请编辑项目根目录下的.env文件</p>
                    <p>• 修改配置后需要刷新页面才能生效</p>
                    <p>• 参考env.example文件了解配置格式</p>
                    <p>• 系统不再保存图片到本地，直接推送到设备</p>
                </div>
            </div>
            
            <div class="status-section">
                <h3>📊 系统状态</h3>
                <div class="status-item">
                    <span class="status-label">API配置状态</span>
                    <span class="status-value <?php echo $status['api_keys_configured'] ? 'success' : 'error'; ?>">
                        <?php echo $status['api_keys_configured'] ? '✅ 已配置' : '❌ 未配置'; ?>
                    </span>
                </div>
            </div>
            
            <div class="back-link">
                <a href="index.php">← 返回主页</a>
            </div>
        </div>
    </div>
</body>
</html>
