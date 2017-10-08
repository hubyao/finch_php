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
			<li class="layui-this">会员积分列表</li>
		</ul>
		<div class="layui-tab-content">
			<div class="layui-tab-item layui-show">
				<h3 style="color:#F00">
					剩余积分：<?php echo $total?$total:0; ?>；　总共充值：<?php echo $total_in?$total_in:0; ?>；　共消费：<?php echo $total_out; ?>　
					<a href="#" data-userid="<?php echo $item['id']; ?>" data-usermobile="<?php echo $item['mobile']; ?>" class="layui-btn layui-btn-normal layui-btn-mini chongzhi">立即充值</a>
				</h3>
				<table class="layui-table">
					<thead>
					<tr>
						<th class='tc' nowrap><a href="#" name="top_checkbox" data-for="ids">选择</a></th>
						<th>消费类型</th>
						<th>积分</th>
						<th>时间</th>
						<th>备注</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($list as $k=>$v) { ?>
					<tr>
						<td align="center"><input type="checkbox" name="ids" value="<?php echo $v['id']; ?>" title="选择"></td>
						<td>

						<?php if($v['gold_type']==1) { ?>
							<span style="color:#00F">充值</span>
						<?php } else { ?>
							<span style="color:#00F">消费</span>
						<?php } ?>
						</td>
						<td><?php echo $v['gold']; ?></td>
						<td><?php echo date('Y-m-d H:i:s',$v['add_time']);?></td>
						<td><?php echo $v['content']; ?></td>
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
		$('.chongzhi').click(function(){
			var o=$(this),
				url = "<?php echo $this->url('user/add_gold');?>";
			openPop('<div class="chongzhi_html"><form class="layui-form form-container" action="'+url+'" method="post"><table><tr><td>充值给</td><td style="text-align:left;">'+o.data('usermobile')+'<input type="hidden" name="user_id" value="'+o.data('userid')+'"></td></tr><tr><td>充值积分</td><td><input type="number" name="gold" value="" required lay-verify="required" placeholder="请输入积分数量" class="layui-input"></td></tr><tr><td valign="top">备注</td><td><textarea name="content" placeholder="在此可输入备注信息" class="layui-textarea"></textarea></td></tr><tr><td></td><td style="text-align:left;"><button type="submit" class="layui-btn">确定增加</button></td></tr></table></form></div>');
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