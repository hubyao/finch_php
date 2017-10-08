<?php
class send_controller extends controller{
	/**
	 * 网站会员首页
	 * @return [type] [description]
	 */
	public function index(){
		$this->view('send_mail');
	}
}