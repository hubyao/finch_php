<?php
class file_controller extends comm_controller{
	protected function _initialize() {
		parent::_initialize();
	}

	//普通文件上传
	public function upload(){
		// 文件上传类
		$up = new uploads_class();

		//设置属性(上传的位置， 大小， 类型， 名是是否要随机生成)
		$up -> set("path", "./public/uploads/".date('Ym')."/");
		$up -> set("maxsize", 20000000);
		//$up -> set("allowtype", array("gif","bmp","png","jpg","jpeg","zip","rar","doc","docs","xls","xlsx","ppt"));//指定文件才能上传
		$up -> set("allowtype", array());//任何文件都可以上传
		$up -> set("israndname", true);

		//header('Content-type:text/json');
		$up -> upload("file");
		$r = [
			'path'		=>$up->getPath(),
			'fullpath'	=>$up->getFullPath(),
			'filename'	=>$up->getFileName(),
			'originName'=>$up->getOriginName(),
			'errorNum'	=>$up->getErrorNum(),
			'errorMess'	=>$up->getErrorMsg(),

			//用于编辑器
			'code'		=>0, //0表示成功，其它失败
			'msg'		=>$up->getErrorMsg(), //提示信息 //一般上传失败后返回
			'data'=>[
				'src'	=>$up->getFullPath(),
				'title'	=>$up->getOriginName(),
			]
		];
		die(json_encode($r));
	}
}
?>