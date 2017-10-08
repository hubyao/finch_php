<?php
//--------------------------------------------------- 框架基础常量以及环境配置 ---------------------------------------------------------------------	
if (version_compare(PHP_VERSION, '5.3.6','<')) {
	header("Content-Type: text/html; charset=UTF-8");
    echo 'PHP环境不能低于5.3.6';
    exit;
}

//默认启动错误报告 但是排除 E_NOTICE类报告
error_reporting(E_ALL ^ E_NOTICE );

//设置系统时区  用在脚本中所有日期/时间函数的默认时区。
date_default_timezone_set('PRC');  // PRC 即 中国时区
  
//开始运行时间和内存使用
define('START_TIME', microtime(true));
define('START_MEM', memory_get_usage());
//版本信息
define('BASE_VERSION', '1.0.0');
//系统常量
defined('DS') or define('DS', DIRECTORY_SEPARATOR);//  DIRECTORY_SEPARATOR 表示 系统分隔符   windows下路径分隔符是\   linux上路径的分隔符是/
defined('CORE_PATH') or define('CORE_PATH', dirname(__FILE__) . DS);//框架核心目录
//在 Windows 上，realpath() 会将 unix 风格的路径改成 Windows 风格的。 在Windows IIS 测试  使用realpath() 路径输出为空  realpath('./')在IIS下是无效的 realpath('/')可以输出D:\
//defined('ROOT_PATH') or define('ROOT_PATH', realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . DS);
defined('ROOT_PATH') or define('ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . DS);// web系统根目录 $_SERVER['SCRIPT_FILENAME'] #当前执行脚本(index.php)的绝对路径名。 dirname() 函数返回路径
defined('BASE_PATH') or define('BASE_PATH', ROOT_PATH);//复制一份系统根目录文件夹路径

//环境常量
define('IS_CGI', strpos(PHP_SAPI, 'cgi') === 0 ? 1 : 0);//web 服务器和 PHP 之间的接口类型 也表示PHP 的工作方式   FastCGI
define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0);//PHP  运行的操作系统
define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0); //cli 是PHP命令行模式  
define('NOW_TIME', $_SERVER['REQUEST_TIME']);//得到请求开始时的时间戳
define('REQUEST_METHOD', IS_CLI ? 'GET' : $_SERVER['REQUEST_METHOD']);//数据提交方式
define('IS_AJAX', (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false);//判断是否是AJAX方式提交	如果是值为 true
define('IS_GET', REQUEST_METHOD == 'GET' ? true : false);
define('IS_POST', REQUEST_METHOD == 'POST' ? true : false);//判断是否是POST提交	
define('IS_PUT', REQUEST_METHOD == 'PUT' ? true : false);
define('IS_DELETE', REQUEST_METHOD == 'DELETE' ? true : false);

//启动session
session_start(); 

//框架基础服务启动
require(CORE_PATH .'core.php'); //加载框架核心类
$app = new core();//实例化单一入口应用控制类
$app->_run();//执行项目
?>