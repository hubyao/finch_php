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
			<li class="layui-this"><a><?php echo $pid_item['name']; ?></a></li>
		</ul>
		<div class="layui-tab-content">
			<div class="layui-tab-item layui-show">
			<form class="layui-form form-container" action="<?php echo $this->url('content/save');?>" method="post">
				<input type="hidden" name="id" value="<?php echo $id; ?>" id="id">
				<input type="hidden" name="pid" value="<?php echo $pid; ?>" id="pid">
				<?php if($tree) { ?>
				<div class="layui-form-item">
					<label class="layui-form-label">选择所属分类</label>
					<div class="layui-input-block">
						<div id="multiple_select">
							<input type="hidden" name="fid" id="fid" value="">
							<blockquote><span class='add'>＋</span></blockquote>
							<ul>
							<?php foreach($tree as $k=>$v) { ?>
							<li data-value="<?php echo $v['id']; ?>" data-text="<?php echo $v['name']; ?>"><?php echo $v['html']; ?> <?php echo $v['name']; ?></li>
							<?php } ?>
							</ul>
						</div>
					</div>
				</div>
				<?php } else { ?>
				<input type="hidden" name="fid" value="<?php echo $fid; ?>" id="fid">
				<?php } ?>
				<?php foreach($field_arr as $k=>$v) { ?>
				<?php echo $v['html']; ?>
				<?php } ?>
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
<?php if($tree) { ?>
<style type="text/css">
#multiple_select{ position: relative; z-index: 9999;}
#multiple_select blockquote{ margin: 0; padding: 4px; border: #E6E6E6 solid 1px; border-radius: 2px; float: left; width: 100%; padding-bottom: 0;}
#multiple_select blockquote span{ display: block; height: 29px; line-height: 29px; float: left; border: solid 1px #F90; padding:0 7px; margin: 0 4px 4px 0;}
#multiple_select blockquote span:hover{border: solid 1px #F30;}
#multiple_select blockquote span i{color: #F00; padding-right: 5px; cursor: pointer;}
#multiple_select blockquote span i:hover{color: #A00;}
#multiple_select blockquote span.add{ width:29px; padding: 0; line-height: 27px; font-size: 24px; text-align:center; cursor: pointer;background: #F60; border: solid 1px #F30; color: #FFF; }
#multiple_select blockquote span.add:hover{ background: #F30;}
#multiple_select ul{ display: none; width: 100%; padding:10px 1px; position: absolute; left: 0; background: #FFF; border:#CCC solid 1px; height: 300px; overflow: hidden; overflow-y: auto; }
#multiple_select ul li{ padding: 5px 10px; cursor: pointer;}
#multiple_select ul li.isSelect{ color: #DDD; cursor:not-allowed;}
#multiple_select ul li:hover{ background: #F8F8F8;}
</style>
<script type="text/javascript">
function init_mul_select(default_ids){
	var reCreateHtml = function(){
		var add_span = document.createElement('span'),
			box = $('#multiple_select blockquote'),
			value = '';
		add_span.innerHTML = '＋';
		add_span.className = 'add';
		box.html('');
		$('#multiple_select ul li').each(function(){
			if($(this).hasClass('isSelect')){
				value += ','+$(this).data('value');
				var span = document.createElement('span');
				span.innerHTML = '<i data-value="'+$(this).data('value')+'">×</i>'+$(this).data('text');
				box.append(span);
			}
		});
		box.append(add_span);
		value = value.substring(1);
		$('#multiple_select input[id=fid]').val(value);
	};
	if(default_ids!=''){
		var ids_arr = default_ids.split(',');
		$('#multiple_select ul li').each(function(){
			for(var i=0; i<ids_arr.length; i++){
				if($(this).data('value')==ids_arr[i]){
					$(this).addClass('isSelect');
					break;
				}
			}
		});
		reCreateHtml();
	}

	$('body')
	.on('click','#multiple_select ul li',function(){
		if(!$(this).hasClass('isSelect')){
			$(this).addClass('isSelect');
			$('#multiple_select ul').slideUp();
			reCreateHtml();
		}
	})
	.on('click','#multiple_select blockquote span[class=add]',function(){
		var aa = $('#multiple_select blockquote').height();
		$('#multiple_select ul').css('top',aa+10);
		$('#multiple_select ul').slideToggle();
	})
	.on('click','#multiple_select blockquote span i',function(){
		var v = $(this).data('value');
		$('#multiple_select ul li').each(function(){
			if($(this).data('value')==v){
				$(this).removeClass('isSelect');
				reCreateHtml();
				return;
			}
		});
	});
}
$(function(){
	init_mul_select('<?php echo $item['fid']; ?>');
});
</script>
<?php } ?>
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