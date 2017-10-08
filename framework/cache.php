<?php
class cache
{
	private $config;
	private $cache_dir;
	
	public function __construct()//构造函数
	{
       $this->config = C::get('APP');//获取配置信息
	   $this->cache_dir = APP_PATH . $this->config['CACHE_SPACE'].DS;
	   $this->data_cache_dir = $this->cache_dir.$this->config['DATA_CACHE_SPACE'].DS;//数据缓存文件夹
	   $this->data_cache_suffix = $this->config['DATA_CACHE_SUFFIX'];//缓存后缀名
	   $this->tpl_cache_dir = $this->cache_dir.$this->config['TPL_CACHE_SPACE'].DS;//模板缓存文件夹
	   $this->tpl_cache_suffix = $this->config['TPL_CACHE_SUFFIX'];//缓存后缀名
	}
	
	public function get($key)// 读取缓存
	{
		$key or F::error('读取缓存参数错误');
		return $this->_cache($key);	
	}	
	
	public function set($key,$value,$expire=3600)//设置缓存
	{
		$key or F::error('设置缓存参数错误');
		return $this->_cache($key,$value,$expire);
	}	
	
	public function clear($key='',$dir='data')//清除缓存 
	{
		if($dir=='data'){
		    $clear_dir = $this->data_cache_dir;
		    $clear_suffix = $this->data_cache_suffix;
		}else{
			$clear_dir = $this->tpl_cache_dir;
		    $clear_suffix = $this->tpl_cache_suffix;
		}	
		if($key){//删除单个缓存文件
			$file = $clear_dir. md5($key) .$clear_suffix;
			@unlink($file);
		}else{
			F::dir_delete($this->cache_dir);//清空整个缓存目录
		}	
	}	
	
	private function _cache($key,$value=null,$expire=3600)//生成缓存//$expire = 3600;//缓存过期时间 单位是 秒    1*60*60=一小时
	{
		$cache_path = $this->data_cache_dir;
		!is_dir($cache_path) && mkdir($cache_path, 0777, true);//如果缓存文件夹不存在
		$file = $cache_path. md5($key) .$this->data_cache_suffix;
		if($value === null){//读取缓存文件
		  if(is_file($file)){//判断文件是否存在
				$content = @file_get_contents($file);
				if( empty($content) ) return array(false,false);//数据不存在
				$expire  =  (int) substr($content, 13, 12);//获取文件内部记录的生成日期   外部读取可以用filemtime($file) 函数返回文件内容上次的修改时间。若成功，则时间以 Unix 时间戳的方式返回。若失败，则返回 false。
				if( time() >= $expire ) return array(false,false);//时间过期
				$md5Sign  =  substr($content, 25, 32);
				$content   =  substr($content, 57);
				if( $md5Sign != md5($content)) return array(false,false);//验证md5签名
				return array(true,@unserialize($content));
		  }else{
				return array(false,false);//数据文件不存在
		  }
		}else{//生成缓存文件
			$value = serialize($value);
			$md5Sign = md5($value);
			$expire = time() + $expire;		
			$content    = '<?php exit;?>' . sprintf('%012d', $expire) . $md5Sign . $value;		
			return @file_put_contents($file, $content, LOCK_EX);  
		}
	}
	
}
?>
