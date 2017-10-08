<?php
class db
{
	static $config; //数据库配置
    private $options = array(); // 查询表达式参数	
	private $pdo = NULL;//PDO连接对象
	protected $cache = NULL;	//缓存对象

	protected $logs = array();//返回所有查询的sql语句
	protected $debug_mode = false;//是否开启调试模式 用于查看sql语句
	
	public function __construct(){//构造函数  
		if(!isset(self::$config)){ //如果配置不存在 
		    self::$config = C::get('DB');//获取配置信息
		}
	}
	
	//数据库连接
	public function connect(){
		static $_conn = array();
		static $_pre = array();
		$dbos = $this->options['os']?$this->options['os']:0;//默认0号主数据库
		if(!isset($_conn[$dbos])){//返回数据库连接对象
			if(isset(self::$config)){
				$dbConfig = self::$config;
				if($dbConfig[$dbos]){//如果对应数据库配置存在
				   $dbDriver = $dbConfig[$dbos];
				}else{
				   $dbDriver = $dbConfig[0]?$dbConfig[0]:$dbConfig; 
				}	
			}
			if($dbDriver['DB_TYPE']){
                $db_type =  strtolower($dbDriver['DB_TYPE']);//数据库类型
				$db_host = $dbDriver['DB_HOST'];//数据库主机
				$db_user = $dbDriver['DB_USER'];//用户名
				$db_pswd = $dbDriver['DB_PSWD'];//密码
				$db_port = $dbDriver['DB_PORT'];//数据库端口
				$db_name = $dbDriver['DB_NAME'];//数据库名
				$db_charset = $dbDriver['DB_CHARSET'];//数据库编码
				$db_params = array(
								PDO::ATTR_CASE              => PDO::CASE_LOWER,//PDO::CASE_NATURAL 默认  //用类似 PDO::CASE_* 的常量强制列名为指定的大小写。 强制列名小写
								PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,//除设置错误码之外，PDO 还将抛出一个 PDOException 异常类并设置它的属性来反射错误码和错误信息
								PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,//在获取数据时将空字符串转换成 SQL 中的 NULL 。
								PDO::ATTR_STRINGIFY_FETCHES => false, //提取的时候将数值转换为字符串
								PDO::ATTR_EMULATE_PREPARES  => false, //（即由MySQL进行变量处理）
				);
				$db_option = is_array($dbDriver['DB_OPTION'])?$dbDriver['DB_OPTION']:$db_params;//PDO 连接选项
				$db_socket = $dbDriver['DB_SOCKET'];// MySQL除了最常见的TCP连接方式外,还提供SOCKET(LINUX默认连接方式)、PIPE和SHARED MEMORY连接方式。
				$db_file = $dbDriver['DB_FILE']?($dbDriver['DB_PATH']=='app'?APP_PATH:BASE_PATH) . ($dbDriver['DB_SPACE']?$dbDriver['DB_SPACE']:$db_type) . DS .$dbDriver['DB_FILE']:'';//SQLite数据库专用	数据库文件地址
				try {
						$commands = array();
						$dsn = '';
						switch ($db_type){
							case 'mariadb':
								$db_type = 'mysql';
							case 'mysql':
								if($db_socket){
									$dsn = $db_type . ':unix_socket=' . $db_socket . ';dbname=' . $db_name;
								}else{
									$dsn = $db_type . ':host=' . $db_host . ($db_port ? ';port=' . $db_port : '') . ';dbname=' . $db_name . ($db_charset ? ';charset=' . $db_charset : '');
								}
								// 环境变量sql_mode,定义了mysql应该支持的sql语法，数据校验等
								$commands[] = 'SET SQL_MODE=ANSI_QUOTES';//启用ANSI_QUOTES后，不能用双引号来引用字符串，因为它被解释为识别符
								break;

							case 'pgsql':
								$dsn = $db_type . ':host=' . $db_host . ($db_port ? ';port=' . $db_port : '') . ';dbname=' . $db_name;
								break;

							case 'sybase':
								$dsn = 'dblib:host=' . $db_host . ($db_port ? ':' . $db_port : '') . ';dbname=' . $db_name;
								break;

							case 'oracle':
								$dbname = $db_host ? '//' . $db_host . ($db_port ? ':' . $db_port : ':1521') . '/' . $db_name : $db_name;
								$dsn = 'oci:dbname=' . $dbname . ($db_charset ? ';charset=' . $db_charset : '');
								break;

							case 'mssql':
								$dsn = strstr(PHP_OS, 'WIN') ?
									'sqlsrv:server=' . $db_host . ($db_port ? ',' . $db_port : '') . ';database=' . $db_name :
									'dblib:host=' . $db_host . ($db_port ? ':' . $db_port : '') . ';dbname=' . $db_name;

								// 当 SET QUOTED_IDENTIFIER 为 ON 时，标识符可以由双引号分隔，而文字必须由单引号分隔。当 SET QUOTED_IDENTIFIER 为 OFF 时，标识符不可加引号，且必须遵守所有 Transact-SQL 标识符规则
								$commands[] = 'SET QUOTED_IDENTIFIER ON';//
								break;

							case 'sqlite':
								$dsn = $db_type . ':' . $db_file;
								$db_user = null;
								$db_pswd = null;
								break;
						}
						if (in_array($db_type, explode(' ', 'mariadb mysql pgsql sybase mssql')) &&$db_charset)
						{
							$commands[] = "SET NAMES '" . $db_charset . "'";//设置数据库编码
						}
						$this->pdo = new PDO($dsn,$db_user,$db_pswd,$db_option);
						foreach ($commands as $value){
							$this->pdo->exec($value);
						}
						$_conn[$dbos] = $this->pdo;
						$_pre[$dbos] = $dbDriver['DB_PREFIX'];//表前缀
				}catch (PDOException $e) {
					F::error('PDO连接错误信息：'.$e->getMessage());
				}		
			}else{
				F::error('请检查数据库配置信息');
			}
        }else{
		   $this->pdo = $_conn[$dbos];
        }
		$this->options['table_pre'] = $_pre[$dbos];//表前缀
	}
	
	
	public function debug($state = true){//开启调试模式可以输出当前执行sql
	    if($state){//如果开启调试模式 就暂时取消缓存功能 但是在连贯操作语句 需要 debug 放在cache后面才生效
		  unset($this->options['cache']);	
		}	
		$this->debug_mode = $state;
		return $this;
	}
	
	public function log(){//输出查询记录
		return $this->logs;
	}
	
	public function last_query()//输出最后一条查询记录
	{
		return end($this->logs);
	}
	
	public function info(){//返回数据库相关信息
		$output = array(
			'server' => 'SERVER_INFO',
			'driver' => 'DRIVER_NAME',
			'client' => 'CLIENT_VERSION',
			'version' => 'SERVER_VERSION',
			'connection' => 'CONNECTION_STATUS'
		);

		foreach ($output as $key => $value)
		{
			$output[ $key ] = $this->pdo->getAttribute(constant('PDO::ATTR_' . $value));
		}
		return $output;
	}
	
	private function _debug($sql, $bind = array()){//调试报告统一处理
		if ($this->debug_mode){
			echo $sql;
			if($bind){
				print_r($bind);
			}	
			$this->debug_mode = false;
			return false;
		}
	}
	
	
	//回调方法，连贯操作的实现
	public function __call($method, $args){
		$method = strtolower($method);
        if ( in_array($method, array('field','where','group','having','order','limit','cache')) ) {
			if($method=='where'){
			  $this->options['where'] =	$args; //接受多参数  
			}else{	
              $this->options[$method] = $args[0];	//只接收第一个参数
			}
			return $this;	//返回对象，连贯查询
        } else{
			F::error($method.' 方法在DB类里不存在');
		}
    }
	
	private function escape($string)//为SQL语句中的  字符串添加引号或者转义特殊字符串 
	{
		return $this->pdo->quote($string);
	}
	
	private function column_escape($string)//过滤表列名 并加上""
	{
		return '"' . str_replace('.', '"."', preg_replace('/(^#|\(JSON\)\s*)/', '', $string)) . '"';
	}
	
	
	private function column_push($columns)//为字段名 支持 字段名(别名)  =>>  字段名 AS 别名  函数目前没有使用
	{
		if ($columns == '*'){
			return $columns;
		}

		if (is_string($columns)){
			$columns = array($columns);
		}

		$stack = array();

		foreach ($columns as $key => $value){
			preg_match('/([a-zA-Z0-9_\-\.]*)\s*\(([a-zA-Z0-9_\-]*)\)/i', $value, $match);//匹配 字段名(别名)  字段名 (别名) 这样的结构 
			if (isset($match[ 1 ], $match[ 2 ])){
				array_push($stack, $this->column_escape( $match[ 1 ] ) . ' AS ' . $this->column_escape( $match[ 2 ] ));
			}else{
				array_push($stack, $this->column_escape( $value ));
			}
		}
		return implode($stack, ',');
	}
	
////////////////////////////////////////////////3种PDO 执行方式////////////////////////////////////////////////////
	
	protected function _prepare($sql, $bind = array()){//预处理语句传递值 执行的SQL语句并返回一个 pdostatement 对象  $sql 语句  $bind 绑定数据
		$this->options['os'] = '';//清除数据库源 
	    $this->options['where_data'] = '';//清除条件语句绑定值
	    $this->_debug($sql,$bind);//调试状态
		array_push($this->logs,array($sql,$bind));
		try {
			$pdostatement = $this->pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));//PDO::CURSOR_SCROLL来请求一个可滚动游标
			foreach ($bind as $key => $val) {
				$param = is_numeric($key)?$key + 1 : ':' . ltrim($key,':');//没有键名时候，用数字键名+1
				if (is_array($val)) {//$val[0] 是值  $val[1] 可指定 值的类型 比如 PDO::PARAM_INT    PDO::PARAM_STR
					$result = $pdostatement->bindValue($param, $val[0], $val[1]);  //PDOStatement::bindValue — 把一个值绑定到一个参数 PDOStatement::bindParam — 把指定的变量名引用值 绑定到一个参数 
				} else{
					$result = $pdostatement->bindValue($param, $val);
				}
				if (!$result) {
					F::error('预处理语句参数绑定错误：对应SQL语句'.$sql);
				}
			}
			$pdostatement->execute();//执行一条预处理语句
		    return $pdostatement;
		} catch (PDOException $e) {
            F::error('预处理语句错误: '.$sql.'语句 =>'. $e->getMessage());
        }
		
	}
	
	
	protected function _query($sql){//执行 SQL 语句，返回PDOStatement对象,可以理解为结果集
		$this->options['os'] = '';//清除数据库源 
		$this->_debug($sql);//调试状态
	    array_push($this->logs, $sql);
		try{
			$sql_query = $this->pdo->query($sql);
		}catch(PDOException $e) {
			F::error('SQL语句错误: '.$sql.'语句 =>'. $e->getMessage());
		}
		return $sql_query;
	}

	protected function _exec($sql){ //执行一条 SQL 语句，并返回受影响的行数
		$this->options['os'] = '';//清除数据库源 
		$this->_debug($sql);//调试状态
		array_push($this->logs, $sql);
		try{
			$sql_exec = $this->pdo->exec($sql);
		}catch(PDOException $e) {
			F::error('SQL语句错误: '.$sql.'语句 =>'. $e->getMessage());
		}
		return $sql_exec;
	}
	
//////////////////////////////////////////////////////////////////////////////////////////////////////////	

	//选择数据库源  
	public function os($rds){
		if(is_numeric($rds)){
		   $this->options['os'] = intval($rds);
	    }
		return $this;
	}
	
	//用于 直接sql 语句查询 给表加上前缀
	public function pre($table){
		$this->connect();//连接数据库
		return $this->options['table_pre'] . $table;
    }
	
	
	//设置表，$$ignore_prefix为true的时候，不加上默认的表前缀
	public function table($table, $ignorePre = false){
		$this->connect();//连接数据库
		if ($ignorePre) {
			$this->options['table'] = $table;
		} else {
			$this->options['table'] = $this->options['table_pre'] . $table;
		}
		return $this;
	}
	
	
	//解析select 类 sql语句
	private function _select_sql(){
		$sql = 'select '.($this->options['field'] ? $this->options['field'] : '*')
				.' from '.$this->options['table']
				.($this->options['where'] ? ' WHERE '.$this->options['where'] : '')
				.($this->options['group'] ? ' GROUP BY '.$this->options['group'] : '')
				.($this->options['having'] ? ' HAVING '.$this->options['having'] : '')
				.($this->options['order'] ? ' ORDER BY '.$this->options['order'] : '')
				.($this->options['limit'] ? ' LIMIT '.$this->options['limit'] : '');
				
		$this->options['table'] = '';		
		$this->options['field'] = '*';	
		$this->options['where'] = '';//拼接完还原数据为空  避免 下一语句 默认了
		$this->options['group'] = '';
		$this->options['having'] = '';
		$this->options['order'] = '';
		$this->options['limit'] = '';
		return $sql;
	}
	
	
	//解析数据  
	private function _parseData($data,$type){
		if(is_array($data) && !empty($data)){//首先检查数据是否存在 并且是个数组
		    $table = $this->options['table'];	//当前表
			$columns = array();	
			$values = array();
			$parameter = array();
			$fields = array();
			foreach ($data as $key => $value){
					array_push($columns, $this->column_escape($key));
					//$columns = array_keys($data); 可以跟下面的已注译 return 匹配  
					switch (gettype($value)){
						case 'NULL':
							$values[] = 'NULL';
							break;
						case 'array':
							preg_match("/\(JSON\)\s*([\w]+)/i", $key, $column_match);
							$values[] = isset($column_match[ 0 ]) ?json_encode($value) :serialize($value);
							break;
						case 'boolean':
							$values[] = ($value ? '1' : '0');
							break;
						case 'integer':
						case 'double':
						case 'string':
							$values[] = $value;
							break;
					}					
					$parameter[] = '?';// insert 问号占位符 结构	
                    $fields[] =  $this->column_escape($key) . ' = ?';		// update 	问号占位符 结构		
			}
			switch($type){
				case 'insert'://新增数据		
					$sql = 'INSERT INTO "'.$table.'"  (' . implode(",", $columns) . ') VALUES (' . implode(",", $parameter) . ') ';
					//$sql = " (`" . implode("`,`", $columns) . "`) VALUES (" . implode(",", $parameter) . ") "; 
					return array($sql,$values);
					break;
				case 'update'://更新数据
					$where = $this->options['where'];//条件语句  命名占位符 跟 问号占位符 不能同时使用的 只能用问号占位符 
					$where_bind = $this->options['where_bind'];
					$sql = 'UPDATE "'.$table.'" SET '.implode(', ', $fields).' WHERE '.$where;
					if(is_array($where_bind)){
						$values = array_merge($values,array_values($where_bind));//使用array_values 把绑定数组统一成 数字键的 数组格式
					}
					return array($sql,$values);
					break;
				default:
				    return false;
					break;
			}
		}
		return false;
	}	
	
	//对条件语句进行处理
	protected function where_clause(){ 
	    $where = $this->options['where'];
		$this->options['where_bind'] = '';//清空上一条的条件语句数据
		$this->options['where'] = $where[0];//第一条sql 语句
	    if(count($where)>1){//参数个数
		    $this->options['where_bind'] = $where[1];//对应where 绑定的数据
			return true;
        }else{
			return false;
        }			
	}
	
	//执行查询类SQL
	private function _query_sql($method){
		$sql_mode = $this->where_clause();
		if(in_array($method, array('get','getlist','operation','has'))){//查询类SQL
		    $sql = $this->_select_sql();//拼接SQL语句
			$sql = $method=='has'?'SELECT EXISTS(' . $sql . ')':$sql;
	        $bind = $sql_mode?$this->options['where_bind']:'';
			$sql_key = $sql_mode?$sql.(is_array($bind)?' '.serialize($bind):''):$sql;//缓存对象KEY
			$this->_readcache($sql_key);//尝试读取缓存并输出
			$query = $sql_mode?$this->_prepare($sql,$bind):$this->_query($sql);//根据条件语句 切换查询方式
			if($query){
				if($method=='operation'||$method=='has'){
					 $data = $query->fetchColumn();//PDOStatement::fetchColumn — 从结果集中的下一行返回单独的一列。
					 $data = is_numeric($data) ? 0 + $data : $data; 
				}else{
					 $data = $query->fetchAll(PDO::FETCH_ASSOC);//PDOStatement::fetchAll — 返回一个包含结果集中所有行的数组   
				}	
				if($method=='get'){
				   $data = isset($data[0])?$data[0]:false;
				}
				$this->_writecache($sql_key,$data);//写入缓存  支持 false 值
			    return $data;
			}else{
				return false;
			}  	
		}
	    //return $query ? $query->fetchAll(PDO::FETCH_ASSOC) : false;
		//PDO::FETCH_COLUMN指定获取方式，从结果集中返回 单列数据 不带列名。
		//PDO::FETCH_ASSOC 指定获取方式，将对应结果集中的每一行作为一个由列名索引的数组返回。如果结果集中包含多个名称相同的列，则PDO::FETCH_ASSOC每个列名只返回一个值
	}
	
	
	
	//原生SQL查询
	public function query($sql,$bind = array()){
		$this->connect();//连接数据库
		if($sql){
			$query_type_6 = substr(trim(strtolower($sql)),0,6);//sql操作方法
			$query_type_4 = substr(trim(strtolower($sql)),0,4);//sql操作方法
			$sql_mode = $bind ? true :false;
			if($query_type_6=='select'){
			   $sql_key = $sql_mode?$sql.(is_array($bind)?serialize($bind):''):$sql;//缓存对象KEY
			   $this->_readcache($sql_key);//尝试读取缓存
			   $query = $sql_mode?$this->_prepare($sql,$bind):$this->_query($sql);//根据条件语句 切换查询方式
			   if($query){
					$data = $query->fetchAll(PDO::FETCH_ASSOC);
					$this->_writecache($sql,$data);//写入缓存  支持 false 值
					return $data;
			    }else{
					return false;
				}
			}
			if($query_type_6=='insert'){
			    $lastId = array();
				if($bind){
					  $this->_prepare($sql,$bind);
				}else{
					  $this->_exec($sql);
				}
				$lastId[] = $this->pdo->lastInsertId();// 返回最后插入行的ID或序列值
			    return count($lastId) > 1 ? $lastId : $lastId[0];
			}
			if($query_type_4=='show'){
			    $query = $this->_query($sql);
				if($query){
					$data = $query->fetchAll(PDO::FETCH_ASSOC);
					return isset($data[0])?count($data[0]):false;
				}else{
					return false;
			    }
			}
			if($bind){
					$query = $this->_prepare($sql,$bind);
					return $query->rowCount();
			}else{
					return $this->_exec($sql);
			}
		}else{
		   return false;
		}	
    }
	
	//只查询一条信息，返回一维数组	
    public function get(){
		$this->options['limit'] = 1;	//限制只查询一条数据
		return $this->_query_sql('get');	
    }
	
	//查询多条信息，返回数组
    public function getlist(){
		return $this->_query_sql('getlist');	
    }
	
	
	//只查询一条信息里单个字段 值
    public function getval($field){
		$this->options['field'] = $field;	//限制只查询一条数据
		$data = $this->get();
		return isset($data[$field])?$data[$field]:false;
    }
	
	//获取数据行数
	public function count(){
		$this->options['field'] = 'count(*)';//查询的字段
		return $this->_query_sql('operation');	
    }
	
	//添加数据
	public function insert($data){
		if(is_array($data)&&!empty($data)){
			$lastId = array();
		    $sql = $this->_parseData($data,'insert');	//对插入的数据 进行处理
			if($sql){//插入数据结构 存在
			   $this->_prepare($sql[0],$sql[1]);//预处理安全执行
			   $lastId[] = $this->pdo->lastInsertId();// 返回最后插入行的ID或序列值
			   return count($lastId) > 1 ? $lastId : $lastId[ 0 ];
			}
		}
		return false;
	}
	
	//更新数据
	public function update($data){
		if(is_array($data)&&!empty($data)){
			$this->where_clause();//条件语句拆分处理
			$where = $this->options['where'];
			if (empty($where)) return false; //修改条件为空时，则返回false，避免不小心将整个表数据修改了
			$sql = $this->_parseData($data,'update');	//对更新的数据 进行格式化处理
			if($sql){
				$query = $this->_prepare($sql[0],$sql[1]);//预处理安全执行
			    return $query->rowCount();//PDOStatement::rowCount — 返回受上一个 SQL 语句影响的行数
			}
		}
		return false;
	}
	
	//删除数据
	public function delete(){
		$sql_mode = $this->where_clause();//条件语句拆分处理
		$table = $this->options['table'];	//当前表
		$where = $this->options['where'];
		if (empty($where)) return false; //修改条件为空时，则返回false，避免不小心将整个表数据都删除了
		$sql = 'DELETE FROM "'.$table.'" WHERE '.$where;
		if($sql_mode){
			$query = $this->_prepare($sql,$this->options['where_bind']);//预处理安全执行
			return $query->rowCount();//PDOStatement::rowCount — 返回受上一个 SQL 语句影响的行数
		}else{	
		    return $this->_exec($sql);
		}
	}
	
////////////////////////////////////////以下是特殊使用函数/////////////////////////////////////////////////////////////////////////////////////////////////////

	//替换数据
	public function replace($data){
		if(is_array($data)&&!empty($data)){
			$this->where_clause();//条件语句拆分处理
			$table = $this->options['table'];	//当前表
			$where = $this->options['where'];
			$replace_query = array();
			$replace_bind = array();
			$where_bind = $this->options['where_bind'];
			foreach ($data as $column => $replacements){
				foreach ($replacements as $replace_search => $replace_replacement){
					$replace_query[] = $column . ' = REPLACE(' . $this->column_escape($column) . ', ? , ?)';
					$replace_bind[] = $replace_search;
					$replace_bind[] = $replace_replacement;
				}
			}
			$replace_query = implode(', ', $replace_query);
			if(is_array($where_bind)){
				$bind = array_merge($replace_bind,array_values($where_bind));//使用array_values 把绑定数组统一成 数字键的 数组格式
			}
			$sql = 'UPDATE "'.$table.'" SET '.$replace_query. ($where?' WHERE '.$where:'');
			$query = $this->_prepare($sql,$bind);//预处理安全执行
			return $query->rowCount();//PDOStatement::rowCount — 返回受上一个 SQL 语句影响的行数
		}
		return false;
	}
	
	
	public function action($actions)//事务事件
	{
		if (is_callable($actions)){// is_callable 验证变量的内容能否作为函数调用
			$this->connect();//连接数据库
			$this->pdo->beginTransaction();//启动一个事务
			$result = $actions($this);
			if ($result === false){
				$this->pdo->rollBack();//回滚一个事务
			}else{
				$this->pdo->commit();//提交一个事务
			}
		}else{
			return false;
		}
	}

	public function has()//判断对应条件数据是否存在
	{
		$this->options['field'] = '';//查询的字段不能有
		return $this->_query_sql('has');
	}
	
	private function operators($field,$operate)//以下4个运算符号通用函数
	{
	    if($field&&$operate){
		    $this->options['field'] = "$operate($field)";//查询的字段
			return $this->_query_sql('operation');	
	    }else{
			return false;
		}	
	}
	
	public function max($field)//返回某字段最大的值
	{
	    return $this->operators($field,'MAX');
	}
	
	public function min($field)//返回某字段最小的值
	{
	    return $this->operators($field,'MIN');
    }
	
	public function avg($field)//返回某字段平均值
	{
		return $this->operators($field,'AVG');
	}

	public function sum($field)//某个列字段相加
	{
		return $this->operators($field,'SUM');
	}	
	
	//////////////////////////////////以下是缓存功能/////////////////////////////////////////////////////////////////////////////////////

	 //初始化缓存类，如果开启缓存，则加载缓存类并实例化
	private function initcache(){
		static $_cache;//单例缓存引擎  
	    if(!($_cache instanceof cache)){ 
     	   require_once(CORE_PATH .'cache.php');//加载缓存引擎类             
		   $_cache = new cache();//实例化缓存引擎                        
		}
        $this->cache = $_cache;//缓存对象        
	}
	
	private  function _readcache($key){ //读取缓存
		if($this->options['cache']){//缓存时间为0 或者空，不读取缓存
		   $this->initcache();//初始化缓存对象
		   $data = $this->cache->get($key);
		   if($data[0]){//如果缓存存在的时候 删除缓存时间设置  这条必须存在 否则直接 unset 掉缓存时间  后面的 写入缓存 里的缓存时间就不存在了
			 unset($this->options['cache']);
			 return $data[1];
		  }
		}
	}
		
	private function _writecache($key,$data) {//写入缓存
		if($this->options['cache']){//缓存时间为0 或者空，不设置缓存
			$this->initcache();//初始化缓存对象
			$expire = $this->options['cache'];
			unset($this->options['cache']);
			return $this->cache->set($key,$data,$expire);
		}		
	}
	
}
?>