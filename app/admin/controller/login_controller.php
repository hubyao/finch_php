<?php
class login_controller extends controller{
	public function index(){
		$this->view('login.html');
	}

	public function loginHandle(){
		$data = F::input('post');
		$data['verify'] = md5(strtoupper($data['verify']));
		if($data['verify'] == F::session('adminlogin')) {
			$data = $this->db->table('admin_user')->
				field('id,username,status')->
				where('username=? and password=?',array(
					$data['username'],
					md5($data['password'] . C::get('salt'))
				))->get();
			if ($data) {
				if ($data['status'] != 1) {
					F::json('当前用户已禁用');
				} else {
					F::session('admin_id',$data['id']);
					F::session('admin_name', $data['username']);
					$this->db->table('admin_user')->where('id=?',array($data['id']))->update([
						'last_login_time' => date('Y-m-d H:i:s', time()),
						'last_login_ip'   => F::get_client_ip(),
					]);
					F::redirect('登录成功', '/admin/index',1);
				}
			} else {
				F::redirect('用户名或密码错误','',3);
			}
		}
		else{
			F::redirect('验证码输入错误','',3);
		}
	}

	public function verify(){
		$m = trim($_GET['m']);
		if($m){
			verify_class::buildImageVerify(4,2,'png',100,38,$m);
		}
	}

	public function logout(){
		F::session_del('admin_id');
		F::session_del('admin_name');
		F::redirect('退出成功','/admin/login',1);
	}
}
?>