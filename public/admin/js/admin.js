/**
 * 后台JS主入口
 */

/**
 * 获取选择的ID合
 * @param  {[type]} name [Checkbox名字]
 * @return {[type]}      [String]
 */
var checked_id = function(name){
	var ids = '';
	$('input[name='+name+']:checked').each(function(){
		if(ids=='')
			ids = $(this).val();
		else
			ids += ','+$(this).val();
	});
	return ids;
};


layui.define(['form', 'layer', 'element', 'layedit', 'laydate', 'upload'], function (exports) {
	var layer = layui.layer,
		element = layui.element(),
		layedit = layui.layedit,
		laydate = layui.laydate,
		form = layui.form();

	/**
	 * AJAX全局设置
	 */
	$.ajaxSetup({
		type: "post",
		dataType: "json"
	});

	$('a[name=top_checkbox]').on('click',function(){
		var name=$(this).data('for');
		var list = document.getElementsByName(name);
		for(var i=0;i<list.length;i++){
			list[i].checked = !list[i].checked;
		}
	});

	/**
	 * 通用日期时间选择
	 */
	$('#datetime').on('click', function () {
		laydate({
			elem: this,
			istime: true,
			format: 'YYYY-MM-DD hh:mm:ss'
		})
	});

	/**
	 * 通用表单提交(AJAX方式)
	 */
	form.on('submit(*)', function (data) {
		// return true;//如需AJAX提交，请将此行注释
		// 同名Input重
		var _data = data.field;
		for(var key in _data){
			if(key.substring(key.length,key.length-2)=='[]'){
				var keyname = key.substring(0,key.length-2);
				var tmpArr = Array();
				var input = $('#'+keyname+' input')[0];
				if($(input).attr('type')=='checkbox'){
					$('#'+keyname+' input:checked').each(function(){
						tmpArr.push($(this).val());
					});
				}
				else{
					$('#'+keyname+' input').each(function(){
						if($(this).val()!='') tmpArr.push($(this).val());
					});
				}
				_data[key]=tmpArr;
			}
		}

		var edit_i=1;
		$('textarea').each(function(){
			if($(this).attr('lang')=='editor'){
				data.field[$(this).attr('id')] = layedit.getContent(edit_i);
				edit_i++;
			}
		});
		$.ajax({
			url: data.form.action,
			type: data.form.method,
			data: _data,
			success: function (info) {
				layer.msg(info.msg);
				if(info.url){
					setTimeout(function () {
						location.href = info.url;
					}, 2000);
				}
			}
		});
		return false;
	});

	/**
	 * 通用批量处理（批量审核、取消审核、删除）
	 */
	$('.ajax-action').on('click', function () {
		var _action = $(this).data('action');
		$.ajax({
			url: _action,
			data: $('.ajax-form').serialize(),
			success: function (info) {
				if (info.code === 1) {
					setTimeout(function () {
						location.href = info.url;
					}, 1000);
				}
				layer.msg(info.msg);
			}
		});
		return false;
	});

	/**
	 * 通用全选
	 */
	$('.check-all').on('click', function () {
		$(this).parents('table').find('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
	});

	/**
	 * 通用删除
	 */
	$('.ajax-delete').on('click', function () {
		var _href = $(this).data('url'),
			_title = $(this).attr('title') || '确定要删除数据？';
		layer.open({
			shade: false,
			content: _title,
			btn: ['确定', '取消'],
			yes: function (index) {
				$.ajax({
					url: _href,
					type: "get",
					success: function (info) {
						if (info.code == 1) {
							setTimeout(function () {
								location.href = info.url;
							}, 2000);
						}
						layer.msg(info.msg);
					}
				});
				layer.close(index);
			}
		});
		return false;
	});

	/**
	 * 通用缩略图上传
	 */
	layui.upload({
		url: "/admin/file/upload",
		success: function (result,input) {
			var ret = $(input).data('for')+'';
			var input_names = $(input).data('names')+'';
			var ul_id = $(input).data('ulid')+'';
			if(ret=='undefined') ret = '';
			if(input_names=='undefined') input_names = '';
			if(ul_id=='undefined') ul_id= '';

			var img_url = result.path+result.filename;

			//将上传结果放入文本框
			if(ret.length>0){
				$('#'+ret).val(img_url);
			}
			//将上传的结果展示出来
			if(input_names.length>0 && ul_id.length>0){
				var html = $('<li><img src="'+img_url+'"><a class="cancel_node">删除</a><input type="hidden" name="'+input_names+'[]" value="'+img_url+'"></li>');
				$('#'+ul_id).append(html);
			}
		}
	});

	//删除节点
	$('body').on('click','.cancel_node',function(){
		$(this).parent().remove();
	})

	/**
	 * 通用AJAX链接
	 */
	$('.ajax-link').on('click', function (event) {
		event.preventDefault();
		var _url = $(this).data('url')+"";
		var _confirm = $(this).data('confirm')+"";
		if(_url == 'undefined'){
			location.href = $(this).attr('href');
			return;
		}
		var aj = {
			url: _url,
			success: function (data) {
				if(data.code === 1){
					setTimeout(function () {
						location.href = data.url ? data.url : location.pathname;
					}, 1000);
				}
				layer.msg(data.msg);
			}
		};

		if(_confirm=='undefined'){ $.ajax(aj); }
		else{
			if(confirm(_confirm)){ $.ajax(aj); }
		}
		return false;
	});

	exports('admin', {});
});