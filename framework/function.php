<?php
class F {

    //private标记的构造方法
	private function __construct(){
	   //用new实例化private标记构造函数的类会报错
        //$danli = new Danli();
    }
	//创建__clone方法防止对象被复制克隆
	public function __clone(){
	  // trigger_error('Clone is not allow!',E_USER_ERROR);
	}

//====================================公共静态函数库==============================================
 // public static function 跟  static  public  function 写法没有什么区别

	//输出错误信息
	public static function error($msg){
		if(C::get('LOG_ON')){
          self::log_write($msg);//生成错误日志文件
		}
	    header("Content-type: text/html; charset=utf-8");
	    die($msg);
    }


	//输出JSON
	public static function json($msg){
		header('Content-type:text/json');
		die(json_encode($msg));
	}

	public static function alert($msg, $url = NULL, $charset='utf-8'){
		header("Content-type: text/html; charset={$charset}");
		$alert_msg="alert('$msg');";
		if( empty($url) ) {
			$go_url = 'history.go(-1);';
		}else{
			$go_url = "window.location.href = '{$url}'";
		}
		echo "<script>$alert_msg $go_url</script>";
		exit;
	}

	public static function is_ajax(){
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	public static function guid(){
		if(function_exists('com_create_guid')){
			return com_create_guid();//window下
		}else{//非windows下
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 andup.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);//字符 "-"
			$uuid = chr(123)//字符 "{"
			.substr($charid, 0, 8).$hyphen
			.substr($charid, 8, 4).$hyphen
			.substr($charid,12, 4).$hyphen
			.substr($charid,16, 4).$hyphen
			.substr($charid,20,12)
			.chr(125);//字符 "}"
			return $uuid;
		}
	}


	private static function log_write($msg)	//写入日志文件
	{
		 if(!is_dir(LOG_PATH)){//检查日志记录目录是否存在
			@mkdir(LOG_PATH, 0777, true);//创建日志记录目录
		 }
		 $time=date('Y-m-d H:i:s');
		 $ip= self::get_client_ip();
		 $destination = LOG_PATH  . date("Y-m-d-") . md5(LOG_PATH) . ".log";//加md5 避免日志文件被普通人下载
       	 @error_log("{$time} | {$ip} | {$_SERVER['PHP_SELF']} |{$msg}\r\n", 3,$destination);//写入文件，记录错误信息
    }

	public static function get_client_ip()//获取客户端IP地址
	{
		static $ip;
		if (isset($_SERVER)){
			if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			}else if(isset($_SERVER["HTTP_CLIENT_IP"])){
				$ip = $_SERVER["HTTP_CLIENT_IP"];
			}else if(isset($_SERVER["REMOTE_ADDR"])){
				$ip = $_SERVER["REMOTE_ADDR"];
			}else{
				$ip = "unknown";
			}
		}else{
			if(getenv("HTTP_X_FORWARDED_FOR")&& strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
				$ip = getenv("HTTP_X_FORWARDED_FOR");
			}else if(getenv("HTTP_CLIENT_IP")&& strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
				$ip = getenv("HTTP_CLIENT_IP");
			}else if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
				$ip = getenv("REMOTE_ADDR");
			}else{
				$ip = "unknown";
			}
		}
		return $ip;
    }

	public static function get_server_domain()//获取服务端域名
	{
		if(isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == 1)){//判断访问方式
			$scheme = 'https://';
		}else{
			$scheme = 'http://';
		}
		$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']=='80' ? '' : ':'.$_SERVER['SERVER_PORT']));//当前域名
		return $scheme.$host;
	}

	public static function get_use_time($dec = 6)//统计从开始到统计时的时间（微秒）使用情况 $dec 小数位
    {
        return number_format((microtime(true) - START_TIME), $dec);
    }

	public static function get_use_mem($dec = 2)//统计从开始到统计时的内存使用情况 $dec 小数位
    {
        $size = memory_get_usage() - START_MEM;
        $a    = array('B', 'KB', 'MB', 'GB', 'TB');
        $pos  = 0;
        while ($size >= 1024) {
            $size /= 1024;
            $pos++;
        }
        return round($size, $dec) . " " . $a[$pos];
    }

	public static function in($data)//对数据输入进行安全过滤
	{
		// htmlspecialchars()转换  htmlspecialchars_decode()还原  (&和号,双引号,单引号,大小于号)(ENT_QUOTES - 只对 双引号和单引号。) //可防止被挂马，跨站攻击
		// addslashes()转换   stripslashes()还原 //    (双引号,单引号,反斜杠,NULL)之前添加反斜杠    //
	    if (empty($data)){return $data;}
	    if (is_array($data)){//如果是数组
	        foreach ((array) $data as $k => $v){
				unset ($data[$k]);
				$k = addslashes(htmlspecialchars($k, ENT_QUOTES, 'UTF-8'));
				if (is_array($v)){
					$data[$k] = self::in($v);
				}else{
					$data[$k] = addslashes(htmlspecialchars($v, ENT_QUOTES, 'UTF-8'));
				}
		    }
	    }else{//单字符串
		     $data = addslashes(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
	    }
	    return $data;
	}

	public static function out($data){//用来还原字符串和字符串数组，把已经转义的字符还原回来
		if (empty($data)){return $data;}
		if (is_array($data)){
			foreach ((array) $data as $k => $v){
				unset ($data[$k]);
				$k = stripslashes($k);
				if (is_array($v)){
					$data[$k] = self::out($v);
				}else{
					$data[$k] = stripslashes($v);
				}
			}
		}else{
			$data = stripslashes($data);
		}
		return $data;
	}

	//文本输入
	public static function text_in($str){
		$str = strip_tags($str,'<br>');// 函数剥去字符串中的 HTML、XML 以及 PHP 的标签 保留<br>
		$str = str_replace(" ", "&nbsp;", $str);
		$str = str_replace("\n", "<br>", $str);
		$str = addslashes($str);
		return $str;
	}

	//文本输出
	public static function text_out($str){
		$str = str_replace("&nbsp;", " ", $str);
		$str = str_replace("<br>", "\n", $str);
		$str = stripslashes($str);
		return $str;
	}

	//html代码输入
	public static function html_in($str){
		$search = array ("'<script[^>]*?>.*?</script>'si",  // 去掉 javascript
						 "'<iframe[^>]*?>.*?</iframe>'si", // 去掉iframe
						);
		$replace = array ("",
						  "",
						);
		$str= @preg_replace ($search, $replace, $str);
		$str= htmlspecialchars($str);
		$str = addslashes($str);
	    return $str;
	}

	//html代码输出
	public static function html_out($str){
		if(function_exists('htmlspecialchars_decode')){
			$str = htmlspecialchars_decode($str);
		}else{
			$str = html_entity_decode($str);
		}
		$str = stripslashes($str);
		return $str;
	}

	//ip访问控制 根据对应$ipfile文件里定义ip 判断
	public static function ip_auth($ipfile='',$url='')
	{
	    if($ipfile){
		    if(is_file($ipfile)){
				$iptxt = file($ipfile);
				foreach($iptxt as $line => $content){
				   $iptxt[$line] = rtrim($content);//返回的数组中每一行都包括了行结束符，因此如果不需要行结束符时还需要使用 rtrim() 函数。
				}
				$ip = self::get_client_ip();
				if(!in_array($ip,$iptxt)){
					if($url){//默认不跳转  注意避免产生逻辑上的 死循环
					  header('location:' . $url);
					}
					exit;
				}
		    }
	    }
	}

   //递归创建多级目录
	public static function dir_create($dir){
	   return @is_dir($dir) or (self::dir_create(@dirname($dir)) and @mkdir($dir, 0777));
	}


	//删除文件夹以及下面内部所有文件
	public static function dir_delete($dir){
		$dh=@opendir($dir);
		while ($file=@readdir($dh)){
			if($file!="." && $file!=".."){
				$fullpath = $dir.DS.$file;
				if(!is_dir($fullpath)) {
					  @unlink($fullpath);
				} else {
					  self::dir_delete($fullpath);
				}
			}
		}
		@closedir($dh);
		if(@rmdir($dir)){ //删除当前文件夹：
			return true;
		} else {
			return false;
		}
	}

//	---------------------------以下是我增加的函数功能---------------------------


	// 检测手机访问
	static public function check_mobile() {
		$_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
		$mobile_browser = '0';
		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$mobile_browser++;
		}

		if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)) {
			$mobile_browser++;
		}

		if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
			$mobile_browser++;
		}

		if (isset($_SERVER['HTTP_PROFILE'])) {
			$mobile_browser++;
		}

		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array(
			'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
			'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
			'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
			'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
			'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
			'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
			'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
			'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
			'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-',
		);
		if (in_array($mobile_ua, $mobile_agents)) {
			$mobile_browser++;
		}

		if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) {
			$mobile_browser++;
		}

		// Pre-final check to reset everything if the user is on Windows
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) {
			$mobile_browser = 0;
		}

		// But WP7 is also Windows, with a slightly different characteristic
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) {
			$mobile_browser++;
		}

		if ($mobile_browser > 0) {
			return true;
		} else {
			return false;
		}
	}

	public static function input($str = ''){
		if($str){
			$arr = explode('.',$str);
			$i_type = $arr[0];
			$i_val = $arr[1];
			if($i_type=='get'){
				return $i_val ? trim($_GET[$i_val]) : $_GET ;
			}
			elseif($i_type=='post'){
				return $i_val ? trim($_POST[$i_val]) : $_POST ;
			}
		}
		else{
			return '';
		}
	}

	public static function input_int($str,$default=0){
		$int_str = F::input($str);
		return is_numeric($int_str)?intval($int_str):$default;
	}

	public static function session($key,$val=''){
		if($val){
			session_start();
			$_SESSION[$key] = $val;
		}
		else{
			return $_SESSION[$key];
		}
	}

	public static function session_del($key){
		session_start();
		unset($_SESSION[$key]);
		session_destroy();
	}

	public static function go($url){
		header('Location:'.$url);
		die();
	}

	public static function redirect($msg='',$url='',$time=2,$code=1){
		if(F::is_ajax()){
			header('Content-type:text/json');
			$r = [
				'msg'=>$msg,
				'code'=>$code,
				'time'=>$time,
				'url'=>$url
			];
			die(json_encode($r));
		}
		else{
			header("Content-type: text/html; charset=utf-8");
			$html = "<style>h1{ display:block; height:40px; line-height:40px; font-size:40px; padding:0; margin:0; padding-bottom:10px;}</style><h1>$msg</h1><a href='".($url?$url:"javascript:history.go(-1);")."'>点击返回</a>";
			$html .= "<script>setTimeout(function(){".($url?"location.href='".$url."';":"history.go(-1);")."},".$time."000);</script>";
			echo $html;
		}
		die();
	}
}




function p($var){
	$output = print_r($var, true);
	$output = "<pre>" . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
	echo $output;
	die();
}
?>