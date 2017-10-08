<?php
/**
 * 数据采集和入库
 */

class api_controller extends comm_controller{

	/**
	 * 初始化类
	 */
	public function __construct(){
	}

	/**
	 * 调用接口发送邮件类
	 * [feedback description]
	 * @return [type] [description]
	 */
	public function feedback(){
		$message=F::input('post.');//获取留言本的内容
		$title=$messge['u_title'];//设置邮件的标题
		$content=$message['u_content'];//设置邮件的内容
		if(empty($title) || strlen($title)>20){
			F::alert("留言标题为空或超过20字");//判断留言标题是否合法
		}else if(empty($content) || strlen($content)>200){
			F::alert("留言内容为空或超过200字");//判断留言内容是否合法
		}
		$result=$this->send_mail($title,$content);//调用comm_contoller.php 的send_email方法发送邮件
	}
}




