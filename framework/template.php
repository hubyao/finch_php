<?php
class template
{
	private $config;
	public $tpl_dir = '';
	public $tpl_path = '';
	public $tpl_cache_path = '';
	public $tpl_cache_time = 60;//精确到秒 这里是60秒
	
	public function __construct()//构造函数  必须有 避免 子类  parent::__construct();
	{
		$this->config = C::get('APP');//获取配置信息
		$this->tpl_dir = APP_PATH . $this->config['TPL_SPACE'].DS.$this->config['TPL_NAME'].DS;//实际模板文件夹
		$this->tpl_cache_path = APP_PATH . $this->config['CACHE_SPACE'].DS.$this->config['TPL_CACHE_SPACE'].DS;//缓存模板路径
		$this->tpl_cache_time = $this->config['TPL_CACHE_TIME'];//模板缓存时间		
        $this->tpl_cache_suffix	= $this->config['TPL_CACHE_SUFFIX'];//模板缓存后缀
		$this->tpl_path =  '/'.C::get('APP_SPACE').'/'.APP_NAME.'/'.$this->config['TPL_SPACE'].'/'.$this->config['TPL_NAME'].'/';//主要是替换模板文件里的风格地址 如果 css js文件的绝对地址 浏览器可以识别/
	}
	
	private function parse($str)//解析模板标签
	{
		$str = str_replace('{TPL_PATH}', $this->tpl_path, $str);//定义固定变量值
        
		//php标签
		/*
			{php echo phpinfo();}	=>	<?php echo phpinfo(); ?>
		 */
		$str = preg_replace ( "/\{php\s+(.+)\}/", "<?php \\1?>", $str);
	
		//加载模版
		/*
			{include top.html}	=>	<?php $this->build('top.html') ?>
		 */
		
		//$str = preg_replace("/\{include\s+(.+?)\}/ies", "\$this->build('\\1');", $str);///e 修饰符已经被弃用了。使用 preg_replace_callback() 代替
		/*$str = preg_replace_callback("/\{include\s+(.+?)\}/is", function($r){return $this->build($r[1]);}, $str);//php5.4 以上版本支持  */	
		$str = preg_replace_callback("/\{include\s+(.+?)\}/is", array($this, 'build'), $str);//5.3版本写法
		
		//if 标签
		/*
			{if $name==1}		=>	<?php if ($name==1){ ?>
			{elseif $name==2}	=>	<?php } elseif ($name==2){ ?>
			{else}				=>	<?php } else { ?>
			{/if}				=>	<?php } ?>
		 */
        $str = preg_replace("/\{if\s+(.+?)\}/is", "<?php if(\\1) { ?>", $str);
		$str = preg_replace("/\{elseif\s+(.+?)\}/is", "<?php } elseif(\\1) { ?>", $str);
		$str = preg_replace("/\{else\}/i", "<?php } else { ?>", $str);
		$str = preg_replace("/\{\/if\}/i", "<?php } ?>", $str);
		
		//for 标签
		/*
			{for $i=0;$i<10;$i++}	=>	<?php for($i=0;$i<10;$i++) { ?>
			{/for}					=>	<?php } ?>
		 */
		$str = preg_replace("/\{for\s+(.+?)\}/is","<?php for(\\1) { ?>",$str);
		$str = preg_replace("/\{\/for\}/i","<?php } ?>",$str);
		
		
		//foreach 标签
		//写法一
		/*
			{foreach $array $value}     	=>	<?php $n=1; if (is_array($array) foreach($array as $value) { ?>
			{foreach $array $key $value}	=>	<?php $n=1; if (is_array($array) foreach($array as $key=>$value) { ?>
			{/foreach}					    =>	<?php $n++;}unset($n) ?>
		 
		$str = preg_replace ( "/\{foreach\s+(\S+)\s+(\S+)\}/is", "<?php \$n=1;if(is_array(\\1)) foreach(\\1 AS \\2) { ?>", $str);
		$str = preg_replace ( "/\{foreach\s+(\S+)\s+(\S+)\s+(\S+)\}/is", "<?php \$n=1; if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>", $str);
		$str = preg_replace ( "/\{\/foreach\}/i", "<?php \$n++;}unset(\$n); ?>", $str);
		*/
		
		//写法二
		/*
			{foreach $array as $value}	=>	<?php foreach($array as $value) { ?>
			{foreach $array as $key=>$value}	=>	<?php foreach($array as $key=>$value) { ?>
			{/foreach}					=>	<?php } ?>
		 */
		$str = preg_replace("/\{foreach\s+(.+?)\}/is", "<?php foreach(\\1) { ?>", $str);
		$str = preg_replace("/\{\/foreach\}/i", "<?php } ?>", $str);
		
			
		/*$str = preg_replace("/\{eval\s+(.+?)\}/is", "<?php \\1 ?>", $str);//执行代码太危险 屏蔽*/
		/*$str = preg_replace("/\{L:(.+?)\}/is", '<?php echo lang("\\1"); ?>', $str);*/
		
		
		//数组标签
		/*{$info.xxx}替换成 <?php echo $info['xxx'] ?>*/
		
		$str = preg_replace ( "/\{(\\$[a-z0-9_]+)\.([a-z0-9_]+)\}/i", "<?php echo $1['$2']; ?>", $str);
		$str = preg_replace ( "/\{(\\$[a-z0-9_]+)\.([a-z0-9_]+)\.([a-z0-9_]+)\}/i", "<?php echo $1[\'$2\'][\'$3\']; ?>", $str);
		
		
		//变量/常量 标签
		/*
		    {:time()}	=>	<?php echo time(); ?> 取消
			{CONSTANCE}	=> <?php echo CONSTANCE;?>
			{$name}	=>	<?php echo $name; ?>
		 */
		
		$str = str_replace('{:time()}', time(), $str);   //替换time()
		$str = preg_replace ( "/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>", $str );	//｛$变量｝ 正则 说包含是   首字母是A-Z全大写  后面可以是 大写字母 数字 ，以及 ASCII 字符从 127 到 255（0x7f-0xff）。
		$str = preg_replace("/\{\\$(.+?)\}/i", "<?php echo $\\1; ?>", $str);//｛$变量｝这语句包含特广所以放最下面  上面一条匹配更精确些
		
		//函数 标签
		/*
		    {:url(参数)}                    => 地址解析 
			{date('Y-m-d H:i:s',time())}	=>	<?php echo date('Y-m-d H:i:s',time());?> 
		 */
		$str = preg_replace("/\{:(url[a-zA-Z0-9_\x7f-\xff]*\(([^{}]*)\))\}/", "<?php echo \$this->\\1;?>", $str);
		$str = preg_replace("/\{(date[a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str);//date 专用
		/*$str = preg_replace ( "/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str ); */ // 会匹配 .bg{background:url("img/storage.png")} 这样的不规则CSS .bg{background:url("img/storage.png");} 多个分号既正确
		
		return $str;
	}
	
	public function build($path, $include=1)//输出解析后模板内容 或者 模板缓存文件路径
	{
		if(is_array($path)){//如果是数组 用于 preg_replace_callback 里的回调函数
			$path = $path[1];
		}
		$path = str_replace('/',DS,trim($path));
		if(!$path || $path==DS || $path=='.'){
			F::error($path.' 模板路径错误');
		}
		$cache_file = $this->tpl_cache_path. md5($path) .$this->tpl_cache_suffix;//缓存文件路径
		
		if(is_file($cache_file)&&((time()-filemtime($cache_file))<$this->tpl_cache_time)){//如果缓存文件存在 并且没有过期
			$is_new = 0;
	    }else{
		    $is_new = 1;
		}
		 
		if($is_new==1){//如果缓存文件不存在 或 已过期    
		    !is_dir($this->tpl_cache_path) && mkdir($this->tpl_cache_path, 0777, true);//判断并新建模板缓存目录
			$tpl_file = $this->tpl_dir.$path;//获取原模板路径 组成完整路径
			if(is_file($tpl_file)){//判断模板文件是否存在
			    $tpl_str = file_get_contents($tpl_file);//file_get_contents() 函数把整个文件读入一个字符串中
			    $tpl_str = $this->parse($tpl_str);//模板替换标签
				file_put_contents($cache_file, "<?php if (!defined('BASE_PATH')) exit;?>" . $tpl_str);//file_put_contents() 函数把一个字符串写入文件中
			}else{
				F::error($tpl_file.' 模板不存在');
			}	
		}
		
		if($include == 1){
			return file_get_contents($cache_file);//输出模板内容
		}else{
			return $cache_file;//输出模板缓存路径
		}
	}
	
}
?>
