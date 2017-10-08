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
	<!--tab标签-->
	<div class="layui-tab layui-tab-brief">
		<ul class="layui-tab-title">
			<li><a href="<?php echo $this->url('user/index');?>">会员中心</a></li>
			<li> <a href="<?php echo $this->url('user/edit');?>">添加会员</a></li>
			<li><a class="layui-this">会员等级管理</a></li>

		</ul>
		<div class="layui-tab-content">
			<div class="layui-tab-item layui-show">
				<table class="layui-table">
				<div style="background:#4caf50;width:90px;height:25px;text-align:center;border-radius:5px;line-height:25px"><a href="javascript:;" style="color:#FFF;" class="level_add">添加会员等级</a></div>
					<thead>
					<tr>
						<th>ID</th>
						<th>会员等级名称</th>
						<th>折扣优惠</th>
						<th class="tc">操作</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($list as $k=>$v) { ?>
					<tr>
						<td><?php echo $v['id']; ?></td>
						<td><?php echo $v['name']; ?></td>
						<td><?php echo $v['privilege']; ?></td>
						<td nowrap>
							<a href="#" data-id="<?php echo $v['id']; ?>" data-name="<?php echo $v['name']; ?>" class="layui-btn layui-btn-normal layui-btn-mini level_edit ">修改</a>
							<a href="#"  data-url="<?php echo $this->url('user/level_del',['id'=>$v['id']]);?>" class="layui-btn layui-btn-danger layui-btn-mini ajax-delete">删除</a>
						</td>
					</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
.chongzhi_html{ padding:20px;}
.chongzhi_html td{ padding: 5px; text-align: right;}
</style>

<script type="text/javascript">
layui.use('layer', function(){
	var $ = layui.jquery,
	layer = layui.layer;

	var openPop = function(html){
		layer.open({
			type: 1,
			content: html
		});
	};

	layer.ready(function(){
		$('.level_edit').click(function(){
			var o=$(this),
				url = "<?php echo $this->url('user/level_edit');?>";
			openPop('<div class="chongzhi_html"><form class="layui-form form-container" action="'+url+'" method="post"><table><tr><td>ID</td><td style="text-align:left;">'+o.data('id')+'<input type="hidden" name="id" value="'+o.data('id')+'"></td></tr><tr><td>会员等级名称</td><td><input type="text" name="name" placeholder="'+o.data('name')+'" class="layui-input"></td></tr><tr><td>折扣优惠</td><td><input type="number" name="privilege" value="" required lay-verify="required" placeholder="请输入折扣优惠" class="layui-input"></td></tr><tr><td></td><td style="text-align:left;"><button type="submit" class="layui-btn">确定修改</button></td></tr></table></form></div>');
		});

		$('.level_add').click(function(){
			var o=$(this),
				url = "<?php echo $this->url('user/level_add');?>";
			openPop('<div class="chongzhi_html"><form class="layui-form form-container" action="'+url+'" method="post"><table><tr><td>会员等级名称</td><td><input type="text" name="name" placeholder="请输入会员等级名称" class="layui-input"></td></tr><tr><td>折扣优惠</td><td><input type="number" name="privilege" required lay-verify="required" placeholder="请输入折扣优惠" class="layui-input"></td></tr><tr><td></td><td style="text-align:left;"><button type="submit" class="layui-btn">确定</button></td></tr></table></form></div>');
		});
	});


});
</script>
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