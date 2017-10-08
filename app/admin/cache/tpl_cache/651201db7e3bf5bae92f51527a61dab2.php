<?php if (!defined('BASE_PATH')) exit;?><?php if (!defined('BASE_PATH')) exit;?><!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<title>后台管理系统</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/public/admin/layui/css/layui.css" media="all">	    
	<link rel="stylesheet" href="/public/admin/css/font-awesome.min.css">
	<!--JS引用-->
	<script src="/public/admin/js/jquery.min.js"></script>
	<script src="/public/admin/layui/layui.js"></script>
	<script type="text/javascript" charset="utf-8" src="/public/admin/js/highcharts.js"></script>
	<script type="text/javascript" charset="utf-8" src="/public/admin/js/highcharts-3d.js"></script>
	<script type="text/javascript" charset="utf-8" src="/public/admin/js/exporting.js"></script>
	<!--CSS引用-->
	<link rel="stylesheet" href="/public/admin/css/admin.css">
	<!--[if lt IE 9]>
	<script src="/public/admin/js/html5shiv.min.js"></script>
	<script src="/public/admin/js/respond.min.js"></script>
	<![endif]-->
</head>
<body>
<div class="layui-layout layui-layout-admin">
	<!--头部-->
	<div class="layui-header header">
		<a href="" class="logo">网站后台管理系统</a>
		<div class="user-action">
			<a href="javascript:;"><i class="fa fa-user"></i> <?php echo $admin_name; ?></a>
			<a href="/" target="_blank"><i class="fa fa-home"></i> 网站首页</a>
			<a href="/<?php echo APP_NAME;?>"><i class="fa fa-paper-plane"></i> 后台概览</a>
			<a href="#" data-url="<?php echo $this->url('system/clear');?>" class="ajax-link"><i class="fa fa-trash-o"></i> 清除缓存</a>
			<a href="#" data-url="<?php echo $this->url('login/logout');?>" class="ajax-link" data-confirm='确实要退出系统？'><i class="fa fa-power-off"></i> 退出</a>
		</div>
	</div>
<?php if (!defined('BASE_PATH')) exit;?>	<!--侧边栏-->
	<div class="layui-side layui-bg-black">
		<div class="layui-side-scroll">
			<ul class="layui-nav layui-nav-tree">
				<li class="layui-nav-item layui-nav-title"><a>网站内容管理</a></li>
				<?php foreach($section as $k=>$v) { ?>
				<li class="layui-nav-item">
					<dl>
					<dd<?php if(MODULE=='content' && $pid==$v['id']) { ?> class="layui-this"<?php } ?>><a href="<?php echo $this->url('content/index',['pid'=>$v['id']]);?>"><?php echo $v['name']; ?></a>
					<?php if($v['is_next']) { ?>
					<a href="<?php echo $this->url('content/category',['pid'=>$v['id']]);?>" class="a_class" title="点击设置<?php echo $v['name']; ?>分类"><i class="layui-icon">&#xe62a;</i></a>
					<?php } ?>
					</dd>
					</dl>
				</li>
				<?php } ?>
				<li class="layui-nav-item layui-nav-itemed"> <a href="javascript:;"><i class="fa fa-gears"></i> 系统配置</a>
					<dl class="layui-nav-child">
						<dd<?php if(MODULE=='system') { ?> class="layui-this"<?php } ?>><a href="<?php echo $this->url('system');?>"><i class="layui-icon">&#xe614;</i> 站点配置</a> </dd>
					</dl>
					<dl class="layui-nav-child">
						<dd<?php if(MODULE=='section') { ?> class="layui-this"<?php } ?>> <a href="<?php echo $this->url('section');?>"><i class="layui-icon">&#xe622;</i> 栏目管理</a> </dd>
					</dl>
				</li>
				<li class="layui-nav-item layui-nav-itemed"> <a href="javascript:;"><i class="fa fa-users"></i> 用户管理</a>
					<dl class="layui-nav-child">
						<dd<?php if(MODULE=='user') { ?> class="layui-this"<?php } ?>> <a href="<?php echo $this->url('user/index');?>"> 用户中心</a> </dd>
					</dl>
					<dl class="layui-nav-child">
						<dd<?php if(MODULE=='useradmin') { ?> class="layui-this"<?php } ?>> <a href="<?php echo $this->url('useradmin/index');?>"> 管理员</a> </dd>
					</dl>
				</li>
			</ul>
		</div>
	</div>
<div class="layui-body">
	<div class="layui-tab layui-tab-brief">
		<ul class="layui-tab-title">
			<li><a href="<?php echo $this->url('user/index');?>">会员中心</a></li>
			<li class="layui-this">添加会员</li>
			<li><a href="<?php echo $this->url('user/user_level');?>">会员等级管理</a></li>
		</ul>
		<div class="layui-tab-content">
			<div class="layui-tab-item layui-show">
			<form class="layui-form form-container" action="<?php echo $this->url('user/save');?>" method="post">
				<input type="hidden" name="id" value="<?php echo $item['id']; ?>" id="id">
				<div class="layui-form-item">
					<label class="layui-form-label">登陆手机</label>
					<div class="layui-input-block">
						<input type="text" name="mobile" value="<?php echo $item['mobile']; ?>" required lay-verify="required" placeholder="请输入手机号码" class="layui-input">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">密码</label>
					<div class="layui-input-block">
						<input type="password" name="password" value="" placeholder="请输入登陆密码，不修改请留空" class="layui-input">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">支付密码</label>
					<div class="layui-input-block">
						<input type="password" name="pincode" value="" placeholder="请输入支付密码，不修改请留空" class="layui-input">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">用户名</label>
					<div class="layui-input-block">
						<input type="text" name="username" value="<?php echo $item['username']; ?>" required lay-verify="required" placeholder="请输入会员登陆名" class="layui-input">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">邮箱</label>
					<div class="layui-input-block">
						<input type="text" name="email" value="<?php echo $item['email']; ?>" placeholder="请输入邮箱地址" class="layui-input">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">会员等级:</label>
					<div class="layui-input-block">
						<select name="user_level">
							<?php foreach($list as $k => $v) { ?>
							<option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="layui-form-item">
					<label class="layui-form-label">状态</label>
					<div class="layui-input-block">
						<input type="checkbox" value="1" name="status" lay-skin="switch"<?php if($item['status']) { ?> checked<?php } ?>>
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">注册时间</label>
					<div class="layui-input-block">
						<input type="text" value="<? date('Y-m-d H:i:s',$v['create_time'])?date('Y-m-d H:i:s',$v['last_login_time']):"" ?>" disabled class="layui-input">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">最后登陆</label>
					<div class="layui-input-block">
						<input type="text" value="<? date('Y-m-d H:i:s',$v['last_login_time'])?date('Y-m-d H:i:s',$v['last_login_time']):"" ?>" disabled class="layui-input">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">IP地址</label>
					<div class="layui-input-block">
						<input type="text" value="<?php echo $v['last_login_ip']; ?>" disabled class="layui-input">
					</div>
				</div>
				</div>
				<div class="layui-form-item">
					<div class="layui-input-block">
						<button class="layui-btn" lay-submit lay-filter="*">保存</button>
						<button type="reset" class="layui-btn layui-btn-primary">重置</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php if (!defined('BASE_PATH')) exit;?>	<!--底部-->
	<div class="layui-footer footer">
		<div class="layui-main">
			<p>2016 &copy; Mosquito's WEB</p>
			<p id="status"></p>
		</div>
	</div>
</div>
<script>
layui.config({
	base: '/public/admin/js/'
}).use('admin');
</script>
</body>
</html>