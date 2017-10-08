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
			<li class="layui-this"><?php echo $list_section['name']; ?>管理</li>
		</ul>
		<div class="layui-tab-content">
			<div class="layui-tab-item layui-show">
				<form class="layui-form layui-form-pane" action="<?php echo $this->url('content/index',['pid'=>$pid]);?>" method="get">
					<?php if($tree) { ?>
					<div class="layui-inline">
						<select name="fid" id="fid">
							<option value=""></option>
							<?php foreach($tree as $k=>$v) { ?>
							<option value="<?php echo $v['id']; ?>"><?php echo $v['html']; ?> <?php echo $v['name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<?php } ?>
					<div class="layui-input-inline">
						<input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="请输入关键词" class="layui-input">
					</div>
					<div class="layui-inline">
						<button class="layui-btn">搜索</button>
					</div>
					<div class="rightBtn">
						<a href="<?php echo $this->url('content/add',['pid'=>$pid]);?>" class="layui-btn layui-btn-small layui-btn-normal ajax-link">添加新内容</a>
					</div>
				</form>
				<hr>
				<table class="layui-table">
					<thead>
					<tr>
						<th class='tc' nowrap><a href="#" name="top_checkbox" data-for="ids">选择</a></th>
						<th>分类</th>
						<?php foreach($fieldTitle as $k=>$v) { ?>
						<th><?php echo $v; ?></th>
						<?php } ?>
						<th class='tc' nowrap>日期</th>
						<th class='tc' nowrap>排序</th>
						<th class='tc' nowrap>操作</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($pagelist as $k=>$v) { ?>
					<tr>
						<td align="center"><input type="checkbox" name="ids" value="<?php echo $v['id']; ?>" title="选择"></td>
						<td>
							<a href="<?php echo $this->url('content/index',['pid'=>$pid]);?>"><?php echo $list_section['name']; ?></a>
							<?php foreach($v['category'] as $k1=>$v1) { ?>
							<?php if($v1['fid']) { ?>
							> <a href="<?php echo $this->url('content/index',['pid'=>$pid,'fid'=>$v1['fid']]);?>"><?php echo $v1['name']; ?></a>
							<?php } ?>
						<?php } ?></td>
						<?php foreach($fielden as $k1=>$v1) { ?>
						<td><?php echo $v[$v1]; ?></td>
						<?php } ?>
						<td nowrap><?php echo date('Y-m-d',$v['add_time']);?></td>
						<td align="right" nowrap>
							<a href="#" data-url="<?php echo $this->url('content/content_move',['action'=>'down','pid'=>$pid,'id'=>$v['id']]);?>" class="layui-btn layui-btn-normal layui-btn-mini ajax-link">向上</a>
							<a href="#" data-url="<?php echo $this->url('content/content_move',['action'=>'up','pid'=>$pid,'id'=>$v['id']]);?>" class="layui-btn layui-btn-normal layui-btn-mini ajax-link">向下</a>
						</td>
						<td align="right" nowrap>
							<a href="<?php echo $this->url('content/add',['pid'=>$pid,'id'=>$v['id']]);?>" class="layui-btn layui-btn-mini edit_category">修改</a>
							<a href="#" data-url="<?php echo $this->url('content/delete',['pid'=>$pid,'id'=>$v['id']]);?>" class="layui-btn layui-btn-danger layui-btn-mini ajax-delete">删除</a>
						</td>
					</tr>
					<?php } ?>
					</tbody>
				</table>
				<?php if($pageurl) { ?>
				<div id="splitPage"></div>
				<script type="text/javascript">
				layui.use(['laypage', 'layer'], function(){
					var laypage = layui.laypage,
						layer = layui.layer;
					laypage({
						cont: 'splitPage',
						pages: <?php echo $pageurl['max']; ?>,//总页数
						curr: <?php echo $pageurl['now']; ?>,//当前页
						jump: function(obj,first){//点击页码出发的事件
							if(first!=true){//是否首次进入页面
								var url = '<?php echo $pageurl['url']; ?>'.replace('{page}',obj.curr);//把当前页码转为链接URL
								window.location.href = url;
							}
						}
					});
				});
				</script>
				<?php } ?>
				<div class="layui-form">
					<?php if($tree) { ?>
					<div class="layui-inline">
						<select name="category" id="new_fid">
							<option value=""></option>
							<?php foreach($tree as $k=>$v) { ?>
							<option value="<?php echo $v['id']; ?>"><?php echo $v['html']; ?> <?php echo $v['name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="layui-inline">
						<button class="layui-btn" id="moveTo" data-flag="move" data-url="<?php echo $this->url('content/movedata',['pid'=>$pid]);?>">移动到...</button>
					</div>
					<div class="layui-inline">
						<button class="layui-btn" id="moveTo" data-flag="copy" data-url="<?php echo $this->url('content/movedata',['pid'=>$pid]);?>">复制到...</button>
					</div>
					<?php } ?>
				</div>
				<script type="text/javascript">
				$(function(){
					$('body').on('click','#moveTo',function(){
						var ids = checked_id('ids');
						var newFid = $('#new_fid').val();
						var flag = $(this).data('flag');
						if(ids==''){
							layer.msg('请选择需要操作的数据');
							return false;
						}
						if(newFid==''){
							layer.msg('请选择新分类');
							return false;
						}
						var url = $(this).data('url');
						url += '?flag='+flag+'&ids='+ids+'&newfid='+newFid;
						location.href=url;
					});
				});
				</script>
			</div>
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