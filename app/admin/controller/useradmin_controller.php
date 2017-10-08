<?php
class useradmin_controller extends comm_controller{
	private $table = 'admin_user';
	/**
	 * 网站管理员首页
	 * @return [type] [description]
	 */
	public function index(){
		$list = $this->db->table($this->table)->getlist();
		$this->view['list']=$list;
		$this->view('useradmin.html');
	}

	/**
	 * 增加，修改管理员
	 */
	public function add(){
		$id = F::input_int('get.id');
		if($id){
			$item = $this->db->table($this->table)->where('id='.$id)->get();
			$item['user_power'] = explode(',',$item['user_power']);
			$this->view['edit'] = 1;
			$this->view['item'] = $item;
		}
		$this->view('useradmin_add.html');
	}

	public function save(){
		$data = F::input('post.');
		$data['user_power'] = is_array($data['user_power'])?implode(',',$data['user_power']):$data['user_power'];
		$data['status'] = $data['status']?1:0;
		if(!$data['username']){
			F::redirect('登陆用户不能为空','',1);
		}
		$id = $data['id'];
		unset($data['id']);
		$result=$this->db->table($this->table)->where("username='".$data['username']."'")->get();
		if($result){
			if($id>0){
				if($data['password']){
					$data['password'] = md5($data['password'].C::get('salt'));
				}else{
					unset($data['password']);
				 }
				if($result['id']){
				 	if($result['id']==$id){
						$this->db->table($this->table)->where('id='.$id)->update($data);
						F::redirect('修改成功',$this->url('useradmin/index'),1);
					}else{
						F::redirect('该用户已注册','',1);
					 }
				}
			}
		}
		if(!$data['password']){
				F::redirect('请输入登陆密码','',1);
		}
		$data['create_time'] = time();
		$data['last_login_time'] = time();
		$data['last_login_ip'] = F::get_client_ip();
		if($result){
				F::redirect('该用户名已注册','',1);
		}else{
			if ($this->db->table($this->table)->insert($data)) {
				F::redirect('注册成功',$this->url('useradmin/index'),1);
			}
		 }
	}

	/**
	 * 删除管理员
	 * @return [type] [description]
	 */
	public function delete(){
		$id = F::input_int('get.id');
		if($id){
			if('3'==$id){
				F::redirect('无法删除该数据',$this->url('useradmin'),1);
			}
			$this->db->table($this->table)->where('id='.$id)->delete();
			F::redirect('删除成功',$this->url('useradmin'),1);
		}
		else{
			F::redirect('无法删除数据',$this->url('useradmin'),1);
		}
	}
}
?>