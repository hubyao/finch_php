<?php
//--------------------------------------------------- 框架核心类 ---------------------------------------------------------------------
class core
{
    //private表示受保护的，只有本类中可以访问
	private $appConfig = array(); //配置
	private $appUrl; //URL路径

    public function __construct()//初始化框架
	{
	    //应用基础常量
		require_once(CORE_PATH .'config.php'); //框架配置文件
		defined('APP_SPACE') or define('APP_SPACE', BASE_PATH . C::get('APP_SPACE') . DS);//放置应用的目录
		defined('APP_DEFAULT_NAME') or define('APP_DEFAULT_NAME', C::get('APP_DEFAULT_NAME'));//默认应用文件夹名 也就是默认站点  default 也不可以作为后面的模块名(既控制器名)
        defined('APP_DEPR') or define('APP_DEPR', C::get('APP_DEPR'));//应用名 与 控制器 之间分隔符
		defined('LOG_PATH') or define('LOG_PATH', BASE_PATH . C::get('LOG_SPACE') . DS);//错误报告文件夹路径
		defined('TEMP_PATH') or define('TEMP_PATH', BASE_PATH . C::get('TEMP_SPACE') . DS);//其他资源文件 比如上传目录 等

	   //先获取当前APP应用名
	    $this->_parseApp();//解析APP应用名

	   //加载配置文件
	    $config = array();
        if(is_file(BASE_PATH .'config.php')){//先判断根目录下是否有全局配置
		   require_once (BASE_PATH .'config.php');
		}else if(is_file(APP_PATH .'config.php')){//如果APP有自定义配置 执行自定义配置
		   require_once (APP_PATH .'config.php');
		}

		$this->appConfig = array_merge(C::get('APP'), $config);// 合并APP 配置文件
		C::set('APP', $this->appConfig);//

		//加载框架核心函数类
		if(is_file(CORE_PATH .'function.php') ) {
			 require_once(CORE_PATH .'function.php');
		}

		//PHP系统错误报告设置
		if($this->appConfig['DEBUG']){
			ini_set("display_errors", 1);
			error_reporting( E_ALL ^ E_NOTICE );//除了notice提示，其他类型的错误都报告
		} else {
			ini_set("display_errors", 0);
			error_reporting(0);//把错误报告，全部屏蔽
		}

	    spl_autoload_register(array($this, 'autoload'));	 //注册类的自动加载
	}


	public function _run()//框架运行执行 //根据  APP路径  模块名（控制器） 操作名  执行对应文件
	{
		$this->_parseUrl();//解析模块（控制器）和操作

		$err_tip =  APP_NAME.'应用里:'.$this->appConfig['MODULE_SPACE'].DS.MODULE.$this->appConfig['MODULE_SUFFIX'];//统一错误提示内容前言

		if($this->_checkModuleExists(MODULE) ) {
			$module = MODULE;
		} else if ($this->_checkModuleExists( $this->appConfig['MODULE_EMPTY'] ) ) {//如果指定模块（控制器）不存在，则检查是否存在空模块
			$module = $this->appConfig['MODULE_EMPTY'];
		} else {
			F::error($err_tip .'不存在');//指定模块（控制器）和空模块都不存在，则显示出错信息，并退出程序。
		}


		$suffix_arr = explode('.', $this->appConfig['MODULE_SUFFIX'], 2);
		$class_name = $module . $suffix_arr[0];//模块名+模块后缀组成完整类名

		if($class_name == ACTION){
			F::error($err_tip . ACTION .' 方法名不能跟 '.$class_name.' 类名相同');
		}
		if(!class_exists($class_name, false)) {//class_exists — 检查类是否已定义  false是否默认调用 类的自动加载
			F::error($err_tip .' 文件中 '.$class_name.' 类未定义');
		}

		$object = new $class_name();//初始化模块（控制器）类

		if(method_exists($object, ACTION)){
			$action =  ACTION; //通过赋值给变量 实现方法执行操作
		} else if ( method_exists($object, $this->appConfig['ACTION_EMPTY']) ) {
			$action = $this->appConfig['ACTION_EMPTY'];//默认空操作
		} else {
			F::error($err_tip .' 文件中不存在 '.ACTION.' 方法');
		}

		//执行指定模块的指定操作
		$object->$action();
    }

	private function _parseApp()//获取当前应用名
	{
		//开始解析
		$script_name = $_SERVER["SCRIPT_NAME"];//获取当前文件的路径
		$url = urldecode($_SERVER["REQUEST_URI"]);//获取完整的网址路径 ，包含"?"之后的字符串
		//去除url包含的当前文件的路径信息 $url 里包含 /index.php 的时候
		if($url && ($pos =@strpos($url,$script_name,0))!== false){   //@strpos查找$script_name字符串在另一字符串$url中第一次出现的位置  如果没找到，将返回 FALSE。
			if($pos == 0){//避免http://www.k7.com/admin/index.php?1 此类路径  admin/index.php 还是保留的 只替换最开始的/index.php
              $url = substr($url,strlen($script_name)); //substr() 函数返回字符串的一部分 从何处开始放回
			}
		}

		$url = ltrim($url,'/');//第一个字符是'/'，则去掉

		//获取第一个参数名 通过判断第一个参数名 是否是APP应用名
		if($url && ($pos = @strpos($url, APP_DEPR ,1)) >0 ) {
			$first_param = substr($url,0,$pos);//参数名
		} else {	//如果找不到分隔符，以当前网址为参数名
			$first_param = $url;
		}

		if(is_dir(APP_SPACE)){ //如果应用空间目录存在
            $app_list = array_diff(scandir(APP_SPACE),array('.','..')); //app 里 文件夹名 列表  剔除 . ..
		}

		$app_name = APP_DEFAULT_NAME;//默认站

	    if($first_param&&$app_list){//如果第一个参数名存在  并且应用空间也存在
			if(in_array($first_param,$app_list)){//如果第一个参数名 是另个应用的应用文件名
				$app_name = $first_param;// 应用名
				$url = substr($url,strlen($app_name)+1);//除去应用名，剩下的url字符串
			}
		}

		$this->appUrl = $url;//传递URL 到 _parseUrl  继续解析

		defined('APP_NAME') or define('APP_NAME', strip_tags($app_name));//app名  使用  strip_tags过滤安全字符
		defined('APP_PATH') or define('APP_PATH', APP_SPACE . APP_NAME . DS);//app 对应目录
	}

    private function _parseUrl()//进行网址解析 获取 模块控制器名 操作名
	{
		//首先剔除$GET自动生成的 无用的键值
		$unset_get = urldecode($_SERVER['QUERY_STRING']);///如果参数带中文 进行编码处理
		$unget = str_replace('.','_',$unset_get);//如果带点 替换  因为$_GET 键名 自动会把.替换成_ 只要为了执行下面的删除 其他无用
		unset($_GET[$unget]);

		$url = $this->appUrl;//接_parseApp 继续解析

		//如果路径里带?的参数提交方式
		if(@strpos($url,'?')>=0){
		    $url_param=explode('?', $url);
		    if(isset($url_param[1])){
				$url_param_s = explode('&', $url_param[1]);
				foreach($url_param_s as $v){
					if($v){
						$arg = explode('=', $v);
						$_GET[strip_tags($arg[0])] = strip_tags($arg[1]); //strip_tags过滤安全字符
					}
		        }
			}
		}

		//去除问号后面的查询字符串
		if($url && ($pos = @strrpos($url, '?')) !==false){
			$url = substr($url,0,$pos);
		}

		//去除后缀
		if($url&&($pos = strrpos($url,$this->appConfig['URL_HTML_SUFFIX'])) > 0) {
			$url = substr($url,0,$pos);
		}

		//获取模块名称
		$flag=0;
		//获取模块名称
		if ($url && ($pos = @strpos($url, $this->appConfig['URL_MODULE_DEPR'], 1))>0 ) {
			$module = substr($url,0,$pos);//模块
			$url = substr($url,$pos+1);//除去模块名称，剩下的url字符串
			$flag = 1;//标志可以正常查找到模块
		} else {	//如果找不到模块分隔符，以当前网址为模块名
			$module = $url;
		}

		$flag2=0;//用来表示是否需要解析参数
		//获取操作方法名称
		if($flag&&$url&&($pos=@strpos($url,$this->appConfig['URL_ACTION_DEPR'],1))>0) {
			$action = substr($url, 0, $pos);//模块
			$url = substr($url, $pos+1);
			$flag2 = 1;//表示需要解析参数
		} else {
			//只有可以正常查找到模块之后，才能把剩余的当作操作来处理
			//因为不能找不到模块，已经把剩下的网址当作模块处理了
			if($flag){
				$action=$url;
			}
		}

		//解析参数
		if($flag2) {
			$param = explode($this->appConfig['URL_PARAM_DEPR'], $url);
			for($i=0; $i<count($param); $i=$i+2) {
				 $_GET[strip_tags($param[$i])] = strip_tags($param[$i+1]);//把参数于参数值 对应 赋值给$_GET strip_tags过滤安全字符
	        }
		}

		if ($module=='favicon.ico'){  //防止Chrome 自动请求根目录下favicon.ico 并且 favicon.ico 又不存在 导致启动两次应用
			@header("HTTP/1.1 404 Not Found");
			exit;
		}

		if(is_numeric($module)||is_numeric($action)){//如果模块或者方法是数字
			$_GET[0] =  strip_tags($module); //可以通过  $_GET[0] 获取到模块位置的数字
			if($action){
			$_GET[1] =  strip_tags($action); //可以通过  $_GET[1] 获取到方法位置的数字
			}
	    }

		$module = empty($module)||is_numeric($module)||strpos($module,'.')!== false? $this->appConfig['MODULE_DEFAULT']:trim($module); //设置默认模块
	    $action = empty($action)||is_numeric($action)||strpos($action,'.')!== false? $this->appConfig['ACTION_DEFAULT']:trim($action); //设置默认方法

		//应用运行常量
		defined('MODULE') or define('MODULE', strip_tags($module)); //模块名
		defined('ACTION') or define('ACTION', strip_tags($action));	//操作方法
	}


	//检查模块（控制器）文件是否存在
	private function _checkModuleExists($module){
		$script_path = APP_PATH . $this->appConfig['MODULE_SPACE']. DS . $module . $this->appConfig['MODULE_SUFFIX'];
		if(is_file($script_path)){
			require_once($script_path);//加载模块文件
			return true;
		} else {
			return false;
		}
	}

	//实现类的自动加载
	private function autoload($class)
	{
		//print_r($class);
	    $class_path = array(
			'controller' => APP_PATH.$this->appConfig['MODULE_SPACE'].DS,//控制器自动加载 比如继承
            'model' => APP_PATH.$this->appConfig['MODEL_SPACE'].DS,//模型自动加载 比如模型间继承
			'lib' =>APP_PATH.$this->appConfig['LIB_SPACE'].DS,//类库
		);
		foreach($class_path as $path) {
			$file_path = $path . $class . '.php';//拼接成文件完整路径
			if(is_file($file_path)){//判断文件是否存在
				require_once($file_path);
                return true;
			}
		}
	    F::error(APP_NAME.'应用里:'.$class.'类不存在'); //如果上面全部都没有找到 就提示错误
		return false;
	}

}

/*
                                                                                         $_SERVER["SCRIPT_NAME"]    $_SERVER["REQUEST_URI"]                     $_SERVER['QUERY_STRING']       $GET
http://www.k7.com/                                                                       /index.php                 /                                           空
http://www.k7.com/index.php                                                              /index.php                 /index.php
http://www.k7.com/index.php?                                                             /index.php                 /index.php?
http://www.k7.com/index.php?ceshi=1                                                      /index.php                 /index.php?ceshi=1                          ceshi=1                        Array ( [ceshi] => 1 )
http://www.k7.com/index.php?index/search/i/2                                             /index.php                 /index.php?index/search/i/2                 index/search/i/2               Array ( [index/search/i/2] => )
http://www.k7.com/index.php?1                                                            /index.php                 /index.php?1                                1                              Array ( [1] => )
http://www.k7.com/index.php/search?s_type=1&s_keyword=包&s_btn=1&search-btn=提交         /index.php                 /index.php/search?s_type=1&s_keyword=...    s_type=1&s_keyword=..          Array ( [s_type] => 1 [s_keyword] => 鍖� [s_btn] => 1 [search-btn] => 鎻愪氦 )
http://www.k7.com/index.php/index/search/i/2                                             /index.php                 /index.php/index/search/i/2
http://www.k7.com/index.php/index-search-i-2.html                                        /index.php                 /index.php/index-search-i-2.html


http://www.k7.com/search?s_type=1&s_keyword=包&s_btn=1&search-btn=提交                   /index.php                 /search?s_type=1&s_keyword=...                /search                      Array ( [/search] => )
http://www.k7.com/index/search?s_type=1&s_keyword=包&s_btn=1&search-btn=提交             /index.php                 /index/search?s_type=1&s_keyword=...          /index/search                Array ( [/index/search] => )
http://www.k7.com/index/search-s_type-1                                                  /index.php                 /index/search-s_type-1                        /index/search-s_type-1       Array ( [/index/search-s_type-1] => )
http://www.k7.com/index/search-s_type-1.html                                             /index.php                 /index/search-s_type-1.html                   /index/search-s_type-1.html  Array ( [/index/search-s_type-1_html] => )
http://www.k7.com/index-search-i-2.html                                                  /index.php                 /index-search-i-2.html                        /index-search-i-2.html       Array ( [/index-search-i-2_html] => )
http://www.k7.com/search-i-2.html                                                        /index.php                 /search-i-2.html                              /search-i-2.html             Array ( [/search-i-2_html] => )
http://www.k7.com/?1                                                                     /index.php                 /?1                                           1                            Array ( [1] => )
http://www.k7.com//1                                                                     /index.php                 //1                                           /1                           Array ( [/1] => )
http://www.k7.com/search.php?1                                                           /index.php                 /search.php?1                                 /search.php                  Array ( [/search_php] => )

http://www.k7.com/admin/index.php?1(实际存在)                                            /admin/index.php           /admin/index.php?1                            1                            Array ( [1] => )
http://www.k7.com/admin/index.php?1(伪静态实际不存在)                                    /index.php                 /admin/index.php?1                            /admin/index.php             Array ( [/admin/index_php] => )

*/

//-----------------------------------------------控制器基类 controller目录下面的控制器都继承于这个类-------------------------------------------------------------------------
class controller
{
	protected $config = array();
	protected $view = array();

	public function __construct()//构造函数  必须有 避免 子类  parent::__construct();
	{

	}

	private function _init()
	{
		if(!$this->config){
          $this->config = C::get('APP');//获取配置信息
		}
	}

	private function db()
	{
		static $_db;//单例数据库引擎
	    if(!($_db instanceof db)){
     	   require_once(CORE_PATH .'db.php');//加载数据库引擎类
		   $_db = new db();//实例化数据库引擎
		}
		return $_db;//数据库模型对象
	}

	private function cache()
	{
		static $_cache;//单例缓存引擎
	    if(!($_cache instanceof cache)){
     	   require_once(CORE_PATH .'cache.php');//加载缓存引擎类
		   $_cache = new cache();//实例化缓存引擎
		}
		return $_cache;//缓存对象
	}


    protected function model($name)//加载模型
    {
		$this->_init();
		static $_model = array();//单例模型引擎
		if(!isset($_model[$name])){
			$suffix_arr = explode('.', $this->config['MODEL_SUFFIX'], 2);
		    $class_name = $name . $suffix_arr[0];//模型名+模型后缀组成完整类名
			$script_path = APP_PATH.$this->config['MODEL_SPACE'].DS.$name.$this->config['MODEL_SUFFIX'];
			if(is_file($script_path)){
			   require_once($script_path);//加载模型文件
			   if(class_exists($class_name, false)){//如果由 class_name 所指的类已经定义，此函数返回 TRUE，否则返回 FALSE。
				    $_model[$name] = new $class_name();
			   }else{
				   F::error($script_path.' 模型中不存在'.$class_name.'类');
			   }
		    }else{
			   F::error($script_path.' 模型文件不存在');
			}
		}
		return $_model[$name];
	}

	protected function view($path='',$output=1)//加载视图模板引擎
    {
		$this->_init();
		static $_tpl;//单例模板引擎
		!$path && $path = MODULE . DS . ACTION. $this->config['TPL_SUFFIX'];//如果没有填写模板 直接拼接 模块+方法+后缀
		if(substr($path,strlen($path)-strlen($this->config['TPL_SUFFIX']))!=$this->config['TPL_SUFFIX']){//如果没有填写后缀
			$path = $path. $this->config['TPL_SUFFIX'];
		}
		$this->view && extract($this->view);//PHP extract() 函数从数组中把变量导入到当前的符号表中
		if(!($_tpl instanceof template)){//判断$_tpl 是否是 template 类的实例
			require_once(CORE_PATH .'template.php');
		    $_tpl  = new template();
		}
		$tpl_file =$_tpl->build($path, 0);
		if($output == 1){
			header("Content-Type:text/html;charset=utf-8");
			require_once($tpl_file);
			return;
		}else{
			ob_start();//打开缓冲区
			require_once($tpl_file);
			$output = ob_get_contents();//得到缓冲区的内容并且赋值给$output
			ob_end_clean();//删除内部缓冲区的内容，并且关闭内部缓冲区
			return $output;
		}
	}

	public function url($url = '', $vars = '', $app = '', $suffix = true, $domain = false) {//URL地址生成
	    $this->_init();
		$app = (!$app && APP_NAME != APP_DEFAULT_NAME )? APP_NAME : $app;	//这里重新定义APP 支持（二级目录的）除了默认应用外的 APP应用 路径
        // 解析URL
		$url_arr = explode('/', $url);
		if($vars){//如果没有参数的时候可以把index 方法名省略了
			$url = ($url_arr[0]?$url_arr[0]:'index').$this->config['URL_MODULE_DEPR'].($url_arr[1]?$url_arr[1]:'index');//只取前两值
		}else{
			$url = ($url_arr[0]?$url_arr[0]:'index').($url_arr[1]?$this->config['URL_MODULE_DEPR']:'').($url_arr[1]?$url_arr[1]:'');//只取前两值
		}
		// 解析APP应用名
        if($app&&is_string($app)){
           $url = $app. APP_DEPR .$url;
        }
		// 解析参数
        if($vars&&is_string($vars)) {//字符串
           $url = $url.$this->config['URL_ACTION_DEPR'].$vars;
        }else if($vars&&is_array($vars)){//数组
		   $url_var = '';
		   $vars_depr = $this->config['URL_PARAM_DEPR'];
		   foreach($vars as $key=>$value){
			  $url_var .= $key.$vars_depr.$value.$vars_depr;//组合参数
		   }
		   $url_var = substr($url_var,0,-strlen($vars_depr));//去掉最后的分隔符
		   $url = $url.$this->config['URL_ACTION_DEPR'].$url_var;
		}

		$url .= $suffix?$this->config['URL_HTML_SUFFIX']:'';//加后缀
		$url = str_replace('index.php','',$_SERVER['SCRIPT_NAME']).$url;//加当前路径
		$url = $domain?F::get_server_domain().$url:$url;//加域名
		return $url;
    }

	public function page_url($page_key='page',$suffix = true)//分页URL地址生成
    {
		$this->_init();
		$page_app = APP_NAME == APP_DEFAULT_NAME ? '':APP_NAME . APP_DEPR;//如果是默认应用 就为空
		$page_param = $_GET;//避免page 丢失问题
		unset($page_param[$page_key]);
		$page_args = '';
		foreach($page_param as $k=>$v){
			$page_args.= $k.$this->config['URL_PARAM_DEPR'].$v.$this->config['URL_PARAM_DEPR'];
		}
	    $url = '/'.$page_app.MODULE.$this->config['URL_MODULE_DEPR'].ACTION.$this->config['URL_ACTION_DEPR'].$page_args.$page_key.$this->config['URL_PARAM_DEPR'].'{page}';
		$url .= $suffix?$this->config['URL_HTML_SUFFIX']:'';//加后缀
		return $url;
	}


	public function __get($field) {//魔术方法__get
       if($field == 'db') {
         return $this->db();
       }
	   if($field == 'cache') {
         return $this->cache();
       }
    }

}

//-----------------------------------------------模型基类  model目录下面的模型都继承于这个类------------------------------------------------------------------------------------------------------------------------
class model extends controller
{
	public function __construct()//构造函数  必须有 避免 子类  parent::__construct();
    {
		parent::__construct();
	}
}

?>