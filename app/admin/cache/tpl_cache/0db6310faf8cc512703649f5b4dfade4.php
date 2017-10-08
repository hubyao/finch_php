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
			<li class="layui-this"><a href="<?php echo $this->url('content/category',['pid'=>$pid]);?>"><?php echo $pid_item['name']; ?>分类管理</a></li>
			<li class=""><a href="#" id="add_category">添加分类</a></li>
		</ul>

		<div class="layui-tab-content">
			<div class="layui-tab-item layui-show">
				<?php if($leveldata) { ?>
				<p>
					<a href="<?php echo $this->url('content/category',['pid'=>$pid]);?>"><?php echo $pid_item['name']; ?></a>
				<?php foreach($leveldata as $k=>$v) { ?>
					﹥ <a href="<?php echo $this->url('content/category',['pid'=>$pid,'fid'=>$v['id']]);?>"><?php echo $v['name']; ?></a>
				<?php } ?>
				</p>
				<?php } ?>

				<table class="layui-table">
					<thead>
					<tr>
						<th style="width: 30px;">ID</th>
						<th>分类名称</th>
						<th>备注</th>
						<th width="1%" style="text-align:center;">操作</th>
					</tr>
					</thead>

					<tbody>
					<?php foreach($list as $k=>$v) { ?>
					<tr>
						<td><?php echo $v['id']; ?></td>
						<td><a href="<?php echo $this->url('content/category',['pid'=>$pid,'fid'=>$v['id']]);?>" title="点击进入<?php echo $v['name']; ?>子分类"><?php echo $v['name']; ?></a><?php if($v['sub_number']>0) { ?> <span title='有<?php echo $v['sub_number']; ?>个子分类'>（<?php echo $v['sub_number']; ?>）</span><?php } ?></td>
						<td><?php echo $v['content']; ?></td>
						<td align="right" nowrap>
							<a href="#" class="layui-btn layui-btn-mini edit_category" data-id="<?php echo $v['id']; ?>" data-content="<?php echo $v['content']; ?>" data-thumb="<?php echo $v['thumb']; ?>" data-name="<?php echo $v['name']; ?>">修改分类</a>
							<a href="#" data-url="<?php echo $this->url('content/category_move',['action'=>'up','pid'=>$pid,'fid'=>$v['fid'],'id'=>$v['id']]);?>" class="layui-btn layui-btn-normal layui-btn-mini ajax-link">向上</a>
							<a href="#" data-url="<?php echo $this->url('content/category_move',['action'=>'down','pid'=>$pid,'fid'=>$v['fid'],'id'=>$v['id']]);?>" class="layui-btn layui-btn-normal layui-btn-mini ajax-link">向下</a>
							<a href="#" data-url="<?php echo $this->url('content/category_del',['pid'=>$pid,'fid'=>$v['fid'],'id'=>$v['id']]);?>" class="layui-btn layui-btn-danger layui-btn-mini ajax-delete">删除</a>
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
.hid_form{ padding:10px;}
.hid_form form{ padding-right: 2px;}
.hid_form input{width:340px; padding:10px; margin:0; margin-bottom: 5px;}
.hid_form input#thumb{width:221px;}
.hid_form textarea{width:340px; padding:10px; height:200px; margin-bottom: 5px;}
.hid_form p{padding:10px;}
.hid_form button{display: none;}
</style>
<div id="addcate" class="hid_form" style="display: none;">

	<form class="layui-form" action="<?php echo $this->url('content/category_save',['pid'=>$pid,'fid'=>$fid]);?>" method="post">
		<textarea name="category_names" placeholder="请输入分类名称"></textarea>
		<p>每行可以录入一个分类名称。</p>
		<button class="layui-btn" lay-submit lay-filter="*" id="btn_addcategory">添加</button>
		<button class="layui-btn" lay-submit lay-filter="*">保存</button>
	</form>

</div>

<div id="editcate" class="hid_form" style="display: none;">
	<form id="edit_form" class='layui-form' action="<?php echo $this->url('content/category_save1',['pid'=>$pid,'fid'=>$fid]);?>" method="post">
		<input type="hidden" name="id" id="id" value="">
		<li><input type="input" name="name" id="name" placeholder="请输入分类名称"></li>
		<li>
			<input type="input" name="thumb" id="thumb" value="" placeholder="点击上传缩略图">
			<input type="file" name="file" class="layui-upload-file">
		</li>
		<li><textarea name="content" class="content" placeholder="备注"></textarea></li>
		<button class="layui-btn" lay-submit lay-filter="*" id="btn_editcategory">保存</button>
	</form>
</div>





<script type="text/javascript">
$(function(){
	$('body').on('click','#add_category',function(){//批量增加分类
		var layer = layui.layer;
		layer.open({
			type:1,
			title:'添加分类',
			resize:false,
			btn: ['确定', '取消'],
			yes: function(index, layero){
				$('#btn_addcategory').click();
				layer.close(index);
			},
			content:$('#addcate')
		});
	}).on('click','.edit_category',function(){//修改分类
		var layer = layui.layer;
		
		
		$('#edit_form #id').val($(this).data('id'));
		$('#edit_form #name').val($(this).data('name'));
		$('#edit_form #thumb').val($(this).data('thumb'));
		$('#edit_form .content').val($(this).data('content'));

		layer.open({
			type:1,
			title:'修改分类',
			resize:false,
			btn: ['确定', '取消'],
			yes: function(index, layero){
				$('#btn_editcategory').click();
				layer.close(index);
			},
			content:$('#editcate')
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