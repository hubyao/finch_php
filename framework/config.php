<?php
//框架默认配置
class C{
	//private标记的构造方法
	private function __construct(){
	  //用new实例化private标记构造函数的类会报错
        //$danli = new Danli();
    }
	//创建__clone方法防止对象被复制克隆
	public function __clone(){
	   //trigger_error('Clone is not allow!',E_USER_ERROR);
	}
	
	public static $config=array(		
        //框架配置
		'CORE' => array(//以下参数 不可外部自定义 即使自定义了也无效
				'APP_SPACE' =>'app',//放置应用的文件夹名    尽量不修改
				'APP_DEFAULT_NAME' =>'default',//放置默认应用的文件夹名  尽量不修改
				'APP_DEPR' => '/',//应用名与模块名(控制器) 之间分隔符   用于获取应用名 
				'LOG_SPACE' =>'log',//错误报告文件夹名   尽量不修改
				'TEMP_SPACE' =>'temp',//其他资源文件夹名  尽量不修改
		),
		'APP' => array(
				//日志和错误调试配置
				'DEBUG' => true,	//是否开启调试模式，true开启，false关闭
				'LOG_ON' => false,//是否开启出错信息保存到文件，true开启，false不开启
				
				//网址配置	
				'URL_MODULE_DEPR' => '/',//模块名(控制器)跟操作名 之间分隔符，一般不需要修改   用于获取模块名
				'URL_ACTION_DEPR' => '/',//操作名于第一参数  之间分隔符，一般不需要修改    用于获取操作名
				'URL_PARAM_DEPR' => '-',//参数组与参数组   参数与值 之间分隔符，一般不需要修改
				'URL_HTML_SUFFIX' => '.html',//伪静态后缀设置，例如 .html ，一般不需要修改
			
				//控制器配置
				'MODULE_SPACE' => 'controller',//模块(控制器)存放文件夹名，一般不需要修改
				'MODULE_SUFFIX' => '_controller.php',//模块(控制器)后缀，一般不需要修改	控制器类名为.php前面的单词 与 自定义类名 组合	
  
				'MODULE_DEFAULT' =>'index',//默认模块(控制器)，一般不需要修改
				'MODULE_EMPTY'=>'empty',//空模块(控制器)	，一般不需要修改
             				
				//操作配置
				'ACTION_DEFAULT'=>'index',//默认操作，一般不需要修改
				'ACTION_EMPTY'=>'_empty',//空操作，一般不需要修改
				
				
				//模型配置
				'MODEL_SPACE' => 'model',//模型存放文件夹名，一般不需要修改
				'MODEL_SUFFIX' => '_model.php',//模型后缀，一般不需要修改   模型类名为.php前面的单词 与 自定义类名 组合	
				
				
				//类库配置
				'LIB_SPACE' => 'lib',//类库存放文件夹名，一般不需要修改
				'LIB_SUFFIX' => '_class.php',//类库后缀，一般不需要修改  类库类名 .php前面的单词 与 自定义类名 组合	 例如page类   类名即 page_Class   文件名page_Class.php
				
				//模板配置	
				'TPL_SPACE' =>'template',//模板存放文件夹名，一般不需要修改
				'TPL_NAME' =>'default',//模板默认风格包文件夹名
				'TPL_SUFFIX'=>'.html',//模板后缀，一般不需要修改
				'TPL_CACHE_SPACE'=>'tpl_cache',//模板缓存存放文件夹名，一般不需要修改
				'TPL_CACHE_SUFFIX'=>'.php',//模板缓存后缀,一般不需要修改
				'TPL_CACHE_TIME'=>1,//模板缓存时间 单位 秒
				
				//总缓存配置
				'CACHE_SPACE'=>'cache',//缓存存放文件夹名，一般不需要修改
		
		        //数据缓存配置
				'DATA_CACHE_SPACE'=>'data',//数据缓存存放文件夹名，一般不需要修改
				'DATA_CACHE_SUFFIX'=>'.php',//数据缓存后缀,一般不需要修改
		),
	);
	
	//获取默认配置
	 public static function get( $name = '' ){
		if(isset(self::$config[$name])) {
			return self::$config[$name];
		} else if(isset(self::$config['CORE'][$name])) {
			return self::$config['CORE'][$name];	
		} else if(isset(self::$config['APP'][$name])) {
			return self::$config['APP'][$name];
		} else if(isset(self::$config['DB'][$name])) {
			return self::$config['DB'][$name];			
		} else {
			return false;
		}
	}
	
	//设置参数
	 public static function set($name, $value = array()){
		return self::$config[$name] = $value;
	}
}
?>