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
			<li class=""><a href="<?php echo $this->url('section');?>">栏目管理</a></li>
			<li class="layui-this"><?php echo $id?'更新'.$item['name'].'':'添加栏目'; ?></li>
		</ul>
		<div class="layui-tab-content">
			<div class="layui-tab-item layui-show">
			<form class="layui-form form-container" action="<?php echo $this->url('section/save');?>" method="post">
				<input type="hidden" name="id" value="<?php echo $id; ?>" id="id">
				<div class="layui-form-item">
					<label class="layui-form-label">栏目名称</label>
					<div class="layui-input-block">
						<input type="text" name="name" value="<?php echo $item['name']; ?>" required lay-verify="required" placeholder="请输入栏目名称" class="layui-input" id="cn_name"<?php echo $id?' readonly':''; ?>>
						<input type="hidden" name="ename" value="<?php echo $item['ename']; ?>" id="en_name">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">别名</label>
					<div class="layui-input-block">
						<input type="text" name="alias" value="<?php echo $item['alias']; ?>" placeholder="（选填）请输入栏目别名" class="layui-input">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">图标</label>
					<div class="layui-input-block">
						<input type="text" name="icon" value="<?php echo $item['icon']; ?>" placeholder="（选填）如：fa fa-home" class="layui-input">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">缩略图</label>
					<div class="layui-input-block">
						<input type="text" name="thumb" value="<?php echo $item['thumb']; ?>" class="layui-input layui-input-inline" id="thumb">
						<input type="file" name="file" class="layui-upload-file">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">内容</label>
					<div class="layui-input-block">
						<textarea name="content" lay-verify="content" placeholder="在此输入备注内容" class="layui-textarea" id="content"><?php echo $item['content']; ?></textarea>
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">选项</label>
					<div class="layui-input-block">
						<input type="checkbox" name="is_next" value="1" title="栏目分类"<?php echo $item['is_next']?' checked':''; ?>>
						<input type="checkbox" name="is_add" value="1" title="添加数据"<?php echo $item['is_add']?' checked':''; ?>>
						<input type="checkbox" name="is_delete" value="1" title="删除数据"<?php echo $item['is_delete']?' checked':''; ?>>
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">模板</label>
					<div class="layui-input-block">
						<input type="text" name="template" value="<?php echo $item['template']; ?>" placeholder="（选填）请输入模板文件名" class="layui-input">
					</div>
				</div>
				<div class="layui-form-item">
					<label class="layui-form-label">分页大小</label>
					<div class="layui-input-block">
						<input type="text" name="pagesize" value="<?php echo $item['pagesize']; ?>" class="layui-input" placeholder="（选填）请填写分页大小">
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
<script type="text/javascript" src="/public/admin/js/pinyin.js"></script> 
<script type="text/javascript">
$(function(){
	$('#cn_name').bind('keyup keydown blur',function(){
		$('#en_name').val(pinyin.go($('#cn_name').val(),1));
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