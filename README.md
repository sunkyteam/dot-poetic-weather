# 🌤️ dot-poetic-weather

一个基于PHP的智能天气古诗生成系统，能够根据实时天气信息生成优美的古诗，并直接推送到指定设备显示。

## ✨ 功能特点

- 🌤️ **实时天气获取** - 基于和风天气API获取准确天气信息
- 🎨 **AI古诗生成** - 使用GLM-4-Flash模型生成符合天气意境的古诗
- 📱 **设备推送** - 直接将生成的古诗图片推送到指定设备
- 🎯 **智能布局** - 优化的图片布局，诗词居中显示，信息分布合理
- 🔧 **环境配置** - 支持.env文件配置，安全可靠
- 🌐 **多城市支持** - 支持任意城市名称输入，自动获取城市代码

## 🖼️ 界面预览
![1](https://github.com/user-attachments/assets/72d1b56a-58ed-4ef1-bf8c-63578608b893)
![2](https://github.com/user-attachments/assets/61f87496-8039-4356-8ee8-2f814839477b)
<img width="1882" height="907" alt="image" src="https://github.com/user-attachments/assets/cc8f932c-fc00-4e1a-87d3-b8135fcf5b78" />
<img width="1873" height="913" alt="image" src="https://github.com/user-attachments/assets/3609969f-81c3-4f87-9a99-bf5af7839754" />


生成的图片包含：
- 左上角：天气描述和城市名称
- 右上角：温度显示
- 中央：两句古诗（每行最多7字，自动换行）
- 
## 🚀 快速开始

### 1. 环境要求

- PHP 7.4+ 
- GD扩展（用于图片生成）
- cURL扩展（用于API调用）
- 支持TTF字体的环境

### 2. 安装步骤

```bash
# 克隆项目
git clone https://github.com/sunkyteam/dot-poetic-weather.git

# 复制环境配置文件
cp env.example .env

# 编辑配置文件
nano .env
```

### 3. 配置说明

编辑 `.env` 文件，填入以下配置：

```env
# 天气诗韵系统环境变量配置模板
# 复制此文件为 .env 并填入真实的配置值

# 和风天气API配置
QWEATHER_API_KEY=your_qweather_api_key_here
QWEATHER_BASE_URL=https://your-domain.re.qweatherapi.com/v7

# GLM-4-Flash模型配置
GLM_API_KEY=your_glm_api_key_here
GLM_BASE_URL=https://open.bigmodel.cn/api/paas/v4/chat/completions
GLM_MODEL=GLM-4-Flash-250414

# 设备推送配置
DEVICE_PUSH_URL=https://dot.mindreset.tech/api/open/image
DEVICE_API_KEY=your_device_api_key_here
DEVICE_SERIAL=your_device_serial_here

```

## 🔑 API密钥获取

### 和风天气API
1. 访问 [和风天气开发者平台](https://dev.qweather.com/)
2. 注册账号并创建应用
3. 获取API Key和域名
4. 将API Key填入 `QWEATHER_API_KEY`
5. 将域名填入 `QWEATHER_BASE_URL`

### GLM-4-Flash API
1. 访问 [智谱AI开放平台](https://open.bigmodel.cn/)
2. 注册账号并创建应用
3. 获取API Key
4. 将API Key填入 `GLM_API_KEY`

### Dot. App获取
1. 获取api密钥 [获取 API 密钥](https://dot.mindreset.tech/docs/server/studio/api/get_api) 填入 `DEVICE_API_KEY`
2. 获取设备序列号 [获取设备序列号](https://dot.mindreset.tech/docs/server/studio/api/get_device_id) 填入 `DEVICE_SERIAL`

## 📖 使用方法

### 1. Web界面使用

访问 `index.php` 页面：
- 输入城市名称（支持任意城市）
- 点击"生成古诗"按钮
- 系统会自动生成古诗并推送到设备

### 2. API调用

支持GET请求直接调用：
```
http://your-domain.com/index.php?city=北京
http://your-domain.com/index.php?city=上海
```

### 3. 系统配置

访问 `config_page.php` 查看系统配置状态和API连接情况。

## 🏗️ 项目结构

```
weather-dot/
├── index.php              # 主页面
├── config.php             # 配置文件
├── config_page.php        # 配置页面
├── WeatherPoetryApp.php   # 主应用类
├── WeatherService.php     # 天气服务类
├── PoetryService.php      # 古诗生成服务类
├── ImageService.php       # 图片生成服务类
├── font/                  # 字体文件目录
│   └── SanJiXingKaiJianTi-Cu-2.ttf
├── .env                   # 环境配置文件
├── env.example            # 环境配置模板
└── README.md              # 项目说明文档
```

## 🎨 自定义配置

### 字体配置
将字体文件放置在 `font/` 目录下，系统会自动检测并使用。

### 调用方式
#### 方法1：直接访问index.php手动调用

#### 方法2：Shell命令

##### 基本调用
```bash
curl "http://your-domain.com/index.php?city=山东"
```

##### 使用wget
```bash
wget -qO- "http://your-domain.com/index.php?city=山东"
```

## 参数说明

| 参数 | 类型 | 必填 | 说明 | 示例 |
|------|------|------|------|------|
| city | string | 是 | 城市名称 | 北京、上海、山东 |


## 🔧 技术特点

- **无数据库依赖** - 系统不依赖数据库，轻量化部署
- **无文件缓存** - 图片直接推送到设备，不保存本地文件
- **环境变量配置** - 所有敏感信息通过环境变量管理
- **API动态获取** - 城市代码通过API动态获取，支持任意城市
- **智能布局** - 自动调整字体大小适应屏幕
- **错误处理** - 完善的错误处理和状态反馈

## 🐛 故障排除

### 常见问题

1. **API调用失败**
   - 检查API密钥是否正确
   - 确认网络连接正常
   - 查看API配额是否充足

2. **图片生成失败**
   - 确认GD扩展已安装
   - 检查字体文件是否存在
   - 确认图片尺寸配置正确

3. **设备推送失败**
   - 检查设备API密钥和序列号
   - 确认设备在线状态
   - 检查推送URL是否正确



## 🤝 贡献

欢迎提交 Issue 和 Pull Request！



---

⭐ 如果这个项目对您有帮助，请给个Star支持一下！
