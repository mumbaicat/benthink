## Benthink 基于Thinkhphp5.0.11快速的开发框架
==== 
### 开发前准备:
 * PHP >= 5.6 (5.4)
 * 熟悉Thinkphp5.0.11
 * 开启PATH_INFO,如果开启则修改index.php里的__SITE__对应值.
 * 修改application/database.php 的数据配置
 * 将网站的目录设置为 public 目录下
 * 如果上一条不能设置则将public下的 index.php 和 .htaccess 复制到根目录,并修改index.php内容.
 <pre>
 	// 定义应用目录
	define('APP_PATH', __DIR__ . '/application/');
	// 加载框架引导文件
	require __DIR__ . '/thinkphp/start.php';
 </pre>  

### 使用的extend扩展:
