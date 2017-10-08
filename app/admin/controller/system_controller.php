<?php
class system_controller extends comm_controller{
	public function index(){
		$data = $this->D1('system',array(
			'name=?',
			array('site_config')
		));
		$data = unserialize($data['value']);
		$this->view['site_config']=$data;
		$this->view('system.html');
	}

	public function systemHandle(){
		if(IS_POST){
			$data = F::input('post');
			$email_sender = $data['site_config']['site_email_sender'];
    		$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
    		//验证邮件发送者邮箱格式是否正确
    		if (!preg_match( $pattern, $email_sender)){
    			F::redirect("您输入的电子邮件地址不合法",'/admin/system',1);
    		}
    		//用“空格、,;”分隔多个邮箱
    		$email_receiver=preg_split("/[\s,;]+/",$data['site_config']['site_email_receiver']); 
    		//循环输出多邮箱
    		   	foreach( $email_receiver as $key=>$var){
    		   		//验证邮件接收者邮箱格式是否正确
    					if (!preg_match( $pattern, $var)){
    						F::redirect($var."邮箱不可用,",'/admin/system',1);
    					}
    			}
    			//设置邮件发送者
			$data_email['email_sender']=$data['site_config']['site_email_sender'];
			//设置邮件smtp密码
			$data_email['email_password']=$data['site_config']['site_email_password'];
			//设置邮件smtp端口
			$data_email['email_smtp']=$data['site_config']['site_email_smtp'];
			foreach ($email_receiver as $k => $v) {
				//验证是否有多个邮箱
				if($k==sizeof($email_receiver)-1){
					$data_email['email_receiver'].=$v;
				}else{
					//用“,”分隔多邮箱
					$data_email['email_receiver'].=$v.',';
				}
			}
			//查询表中是否有记录
			$result=$this->db->table('email')->where('id=1')->get();
			//如果没有，则插入数据
			if(!$result){
				$this->db->table('email')->insert($data_email);
			}else{//否则更新数据
				$this->db->table('email')->where("id=1")->update($data_email);
			}
			$data = serialize($data['site_config']);
			$this->db->table('system')->where("name='site_config'")->update(array('value'=>$data));
			F::redirect('保存成功','/admin/system',1);
		}
	}

	public function clear(){
		$cache_dir = './app/'.APP_NAME.'/cache';
		if(F::dir_delete($cache_dir)){
			F::redirect('清除成功','',1);
		}
		else{
			F::redirect('操作失败，请稍候重试','',1);
		}
	}
}
?>