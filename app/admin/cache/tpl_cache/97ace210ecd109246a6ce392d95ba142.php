<?php if (!defined('BASE_PATH')) exit;?>﻿<?php if (!defined('BASE_PATH')) exit;?><!DOCTYPE html>
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
			<li class=""><a href="<?php echo $this->url('section/add');?>">添加栏目</a></li>
			<li class="layui-this"><?php echo $sec_item['name']; ?>字段设置</li>
		</ul>
		<style>
		#field_list{float: left; width: 250px; background: #EEE; float: left;}
		#field_list strong{ background: #666; color: #FFF; padding: 10px; display:block;}
		#field_list strong i{padding-left: 10px; font-size: 12px; font-weight: normal; color: #CCC;}
		#field_list ul li,#field_list ol li{ padding: 10px; cursor: pointer; position: relative;}
		#field_list ul li i,#field_list ol li i{ padding-left:6px; color:#666;}
		#field_list ul li:hover,#field_list ol li:hover{ background: #DEDEDE;}
		#field_list ul li.on{ background: #CCC;}
		#field_list ul li.add{ background: #1AA194; color: #FFF; text-align: center;}
		#field_list ul li.add i{font-size:13px; color: #FFF;}
		#field_list ul li em{ position: absolute; right: 3px;top: 3px;  width: 12px; height: 12px; line-height: 12px; background: #F00; color: #FFF; text-align:center; border-radius: 3px; display: none;}
		#field_list ul li a{ position: absolute; right: 3px; bottom: 3px;  width: 12px; height: 12px; line-height: 12px; background: #080; color: #FFF; text-align:center; border-radius: 3px; display: none;}
		#field_list ul li:hover em{ display: block;}
		#field_list ul li:hover a{ display: block;}
		</style>
		<div class="layui-tab-content">
			<div class="layui-tab-item layui-show" id="field_list">
				<strong><?php echo $sec_item['name']; ?><i><?php echo $sec_item['ename']; ?></i></strong>
				<ol>
					<li>自动编号<i>id</i></li>
					<li>分类编号<i>pid</i></li>
					<li>是否禁用<i>is_pass</i></li>
					<li>是否推荐<i>is_best</i></li>
					<li>添加时间<i>add_time</i></li>
				</ol>
				<ul>
					<?php foreach($list as $k=>$v) { ?>
					<li title="点击修改<?php echo $v['name']; ?>字段" data-id="<?php echo $v['id']; ?>"<?php if($id==$v['id']) { ?> class="on"<?php } ?>>
						<?php echo $v['name']; ?>
						<i><?php echo $v['field_name']; ?></i>
						<em data-url="<?php echo $this->url('section/field_del',['pid'=>$pid,'id'=>$v['id']]);?>" class="ajax-delete" title="确定要删除字段<?php echo $v['name']; ?>？">×</em>
						<a href="<?php echo $this->url('section/field_move',['pid'=>$pid,'id'=>$v['id']]);?>" title="向下移动">∨</a>
					</li>
					<?php } ?>
					<li data-id="0" class="add"><i class="layui-icon">&#xe61f;</i> 增加新字段</li>
				</ul>
			</div>
			<div class="layui-tab-item layui-show" style=" float: left; width: 60%;">
				<form id="layui-form form-container" class="layui-form form-container" action="<?php echo $this->url('section/field_save');?>" method="post">
					<input type="hidden" name="pid" value="<?php echo $pid; ?>">
					<input type="hidden" name="id" value="<?php echo $id; ?>">
					<div class="layui-form-item">
						<label class="layui-form-label">字段名称</label>
						<div class="layui-input-block">
							<input type="text" name="name" value="<?php echo $item['name']; ?>" required  lay-verify="required" placeholder="请输入字段名称，确定后不可修改" class="layui-input" id="cn_name"<?php echo $id?' disabled':''; ?>>
							<input type="hidden" name="field_name" value="<?php echo $item['field_name']; ?>" id="en_name">
							<p><?php echo $item['field_name']; ?></p>
						</div>
					</div>
					<div class="layui-form-item">
						<label class="layui-form-label">数据类型</label>
						<div class="layui-input-block">
							<select id="field_type" lay-filter="test" name="field_type" lay-verify="required"<?php echo $id?' disabled':''; ?>>
								<option value="varchar" lang="50" title="长度是指最多存入的字符数"<?php if($item['field_type']=='varchar') { ?> selected<?php } ?>>文本字段</option>
								<option value="tinyint" lang="1" title="用于存取是否或其它短的数字"<?php if($item['field_type']=='tinyint') { ?> selected<?php } ?>>短数字</option>
								<option value="float" lang="5,4" title="用于存取货币类型的（8,2）是指总宽度是5位数，其中还有2位是小数"<?php if($item['field_type']=='float') { ?> selected<?php } ?>>货币类型</option>
								<option value="int" lang="3"<?php if($item['field_type']=='int') { ?> selected<?php } ?>>数字</option>
								<option value="text" lang="65535" title="可以存最大长度65535个字元"<?php if($item['field_type']=='text') { ?> selected<?php } ?>>长文本</option>
								<option value="MediumText" lang="16777215" title="可以存最大长度16777215个字元"<?php if($item['field_type']=='MediumText') { ?> selected<?php } ?>>超长文本</option>
								<option value="LongText" lang="4294967295" title="可以存最大长度4294967295个字元"<?php if($item['field_type']=='LongText') { ?> selected<?php } ?>>最长文本</option>
								<option value="datetime" lang="8"<?php if($item['field_type']=='datetime') { ?> selected<?php } ?>>日期</option>
							</select>
						</div>
					</div>
					<div class="layui-form-item">
						<label class="layui-form-label">长度</label>
						<div class="layui-input-block">
							<input type="text" id="field_long" name="field_long" value="50" placeholder="请填入数据长度，确定后不可修改" class="layui-input"<?php echo $id?' disabled':''; ?>>
						</div>
					</div>
					<div class="layui-form-item">
						<label class="layui-form-label">约束类型</label>
						<div class="layui-input-block">
							<select name="type">
								<option value="input"<?php if($item['type']=='input') { ?> selected<?php } ?>>单行文本</option>
								<option value="hidden"<?php if($item['type']=='hidden') { ?> selected<?php } ?>>隐藏域</option>
								<option value="switch"<?php if($item['type']=='switch') { ?> selected<?php } ?>>开关</option>
								<option value="inputnumber"<?php if($item['type']=='inputnumber') { ?> selected<?php } ?>>单行数字</option>
								<option value="textarea"<?php if($item['type']=='textarea') { ?> selected<?php } ?>>多行文本</option>
								<option value="checkbox"<?php if($item['type']=='checkbox') { ?> selected<?php } ?>>多选框</option>
								<option value="radio"<?php if($item['type']=='radio') { ?> selected<?php } ?>>单选框</option>
								<option value="select"<?php if($item['type']=='select') { ?> selected<?php } ?>>下拉列表</option>
								<option value="upload"<?php if($item['type']=='upload') { ?> selected<?php } ?>>文件上传</option>
								<option value="photo"<?php if($item['type']=='photo') { ?> selected<?php } ?>>图片上传</option>
								<option value="addtime"<?php if($item['type']=='addtime') { ?> selected<?php } ?>>日期格式</option>
								<option value="editor"<?php if($item['type']=='editor') { ?> selected<?php } ?>>内容编辑器</option>
							</select>
						</div>
					</div>
					<div class="layui-form-item">
						<label class="layui-form-label">选项</label>
						<div class="layui-input-block">
							<input type="checkbox" name="is_must" value="1" title="必填项"<?php if($item['is_must']) { ?> checked<?php } ?>>
							<input type="checkbox" name="is_show" value="1" title="列表显示"<?php if($item['is_show']) { ?> checked<?php } ?>>
						</div>
					</div>
					<div class="layui-form-item">
						<label class="layui-form-label">默认值</label>
						<div class="layui-input-block">
							<textarea name="default" class="layui-textarea" style="height:200px;"><?php echo $item['default']; ?></textarea>
							每行可存储一个值
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
</div>
<script type="text/javascript" src="/public/admin/js/pinyin.js"></script>
<script type="text/javascript">
	$(function(){
		$('#field_list ol li').on('click',function(o){
			layer.msg('此类别不可修改');
		});
		$('#field_list ul li').on('click',function(o){
			var id=$(this).data('id');
			var url = '<?php echo $this->url('section/field',['pid'=>$pid,'id'=>'____']);?>';
			url = url.replace('____',id);
			location = url;
		});
		$('#field_list ul em').on('click',function(event){
			event.stopPropagation();
		});
		$('#cn_name').bind('keyup keydown blur',function(){
			$('#en_name').val(pinyin.go($('#cn_name').val(),1));
		});
	
	});



</script>
<script type="text/javascript">
 $lang=""
layui.use('form', function(){
  var form = layui.form();
 form.on('select(test)', function(data){
 	  //console.log(data.elem[data.elem.selectedIndex].lang);
 	  $lang=data.elem[data.elem.selectedIndex].lang;
 		$("#field_long").val($lang);
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