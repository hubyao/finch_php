<?php

//APP配置
$config['DEBUG']= true;	//是否开启调试模式，true开启PHP系统错误报告，false关闭PHP系统错误报告  注意=非代码里自定义的错误
$config['LOG_ON']= true;	//是否生成错误日志文件   true 生成  false 不生成

//APP模板配置
$config['TPL_NAME'] = '';//模板默认风格包名
$config['TPL_CACHE_TIME'] = 1 ;//模板缓存时间 单位 秒
$config['salt'] = '1dFlxLhiuLpnUZ79kA'; //加密字串


//数据库配置 支持多数据库 多连   支持 MySQL, MSSQL, SQLite, MariaDB, Oracle, Sybase, PostgreSQL  等数据库.
$config['DB'] = array(//0号 为主数据库 既默认连接数据库
    'DB_TYPE'=>'mysql',//数据库类型  mysql  mssql  sqlite  mariadb  oracle sybase  postgresql
    'DB_HOST'=>'webyao.cn',//数据库主机，一般不需要修改
    'DB_USER'=>'root',//数据库用户名
    //'DB_PSWD'=>'Aa1236547890',//数据库密码
    'DB_PSWD'=>'',//数据库密码
	'DB_PORT'=>3306,//数据库端口，mysql默认是3306，一般不需要修改
	'DB_NAME'=>'finch',//数据库名
	'DB_PREFIX'=>'f_',//数据库表前缀
    'DB_CHARSET'=>'utf8',//数据库编码，一般不需要修改
	//'DB_FILE' =>'',//SQLite数据库专用	数据库文件地址
	//'DB_OPTION'=>'',//PDO 连接选项 值为数组 默认 array(PDO::ATTR_CASE => PDO::CASE_NATURAL,)保留数据库驱动返回的列名。
	//'DB_SOCKET'=>'',//MySQL除了最常见的TCP连接方式外,还提供SOCKET(LINUX默认连接方式)、PIPE和SHARED MEMORY连接方式。
);

?>