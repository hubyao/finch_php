<?php
class user_controller extends comm_controller{
	/**
	 * 网站会员首页
	 * @return [type] [description]
	 */
	public function index(){
		$list = $this->db->table('user')->getlist();
		foreach($list as $k=>$v){
			$list[$k]['gold'] = $this->db->table('user_gold')->where('user_id='.$v['id'])->sum('gold');
			if(!$list[$k]['gold']) $list[$k]['gold'] = 0;

			//会员等级
			$list[$k]['user_level'] = $this->db->table('user_level')->where('id='.$v['user_level'])->getval('name');
		}
		$this->view['list']=$list;
		$this->view('user.html');
	}
	/**
	 * 修改用户资料
	 * 添加用户
	 * @return [type] [description]
	 */
	public function edit(){
		$id = F::input_int('get.id');
		if($id){
			$this->view['item'] = $this->db->table('user')->where('id='.$id)->get();
			$this->view['edit'] = 1;
		}

		$list = $this->db->table('user_level')->getlist();
		$this->view['list'] = $list;
		$this->view('user_edit');
	}

	public function save(){
		$data 	= F::input('post.');
		$id 	= $data['id'];
		unset($data['id']);

		//修改用户
		if($id>0){
			if($data['password']){
				$data['password'] = md5($data['password'].C::get('salt'));
			}
			else{
				unset($data['password']);
			}
			if($data['pincode']){
				$data['pincode'] = md5($data['pincode'].C::get('salt'));
			}
			else{
				unset($data['pincode']);
			}
			$data['status'] = $data['status']?1:0;
			if($this->db->table('user')->where('mobile='."'{$data['mobile']}'".' and '.'id!='.$id)->getlist()){
				F::redirect('该手机已被注册','',1);
			}else if($this->db->table('user')->where('username='."'{$data['username']}'".' and '.'id!='.$id)->getlist()){
				F::redirect('该名称已被注册','',1);
			}else if($this->db->table('user')->where('email='."'{$data['email']}'".' and '.'id!='.$id)->getlist()){
				F::redirect('该邮箱已被注册','',1);
			}
			$this->db->table('user')->where('id='.$id)->update($data);
			F::redirect('修改成功',$this->url('user/index'),1);
 		}
 		//添加用户
 		else{
			if(!$data['password']){
				F::redirect('请输入登陆密码','',1);
			}
			if($this->db->table('user')->where('mobile='."'{$data['mobile']}'")->getlist()){
				F::redirect('该手机已被注册','',1);
			}else if($this->db->table('user')->where('username='."'{$data['username']}'")->getlist()){
				F::redirect('该名称已被注册','',1);
			}else if($this->db->table('user')->where('email='."'{$data['email']}'")->getlist()){
				F::redirect('该邮箱已被注册','',1);
			}

			$data['create_time'] = time();
			$data['last_login_time'] = time();
			$data['last_login_ip'] = F::get_client_ip();

			//验证手机号格式
			if (strlen($data['mobile']) != "11"&&!preg_match_all("/13[123569]{1}\d{8}|15[1235689]\d{8}|188\d{8}/", $data['mobile'])) {
				F::redirect('请输入正确的电话号码','',1);
			}
			//验证格式是否正确
			$email_address = $data['email'];
    		$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
    		if (!preg_match( $pattern, $email_address)){
    			F::redirect("您输入的电子邮件地址不合法",'',1);
    		}


			if ($this->db->table('user')->insert($data)) {
				F::redirect('保存成功',$this->url('user/index'),1);
			} else {
				F::redirect('保存失败',$this->url('user'),1);
			}
		}
	}
	/**
	 * 会员积分列表
	 * @return [type] [description]
	 */
	public function goldlist(){
		$uid = F::input_int('get.id');
		if($uid>0){
			$list = $this->db->table('user_gold')->where('user_id='.$uid)->order('id desc')->getlist();
			$this->view['list']=$list;

			$this->view['item']=$this->db->table('user')->where('id='.$uid)->get();
			$this->view['total']=$this->db->table('user_gold')->where('user_id='.$uid)->sum('gold');

			$this->view['total_in']=$this->db->table('user_gold')->where('gold_type=1 and user_id='.$uid)->sum('gold');;
			$this->view['total_out']=abs($this->db->table('user_gold')->where('gold_type=0 and user_id='.$uid)->sum('gold'));
			$this->view('goldlist.html');
		}
	}

	/**
	 * 充值积分操作
	 */
	public function add_gold(){
		$data = F::input('post.');
		if($data['gold']>0){
			$data['gold_type']=1;
			$str='积分增加成功';
		}else{
			$data['gold_type']=0;
			$str='积分扣除成功';
		}
		
		$data['add_time']=time();
		if(!$data['user_id']){
			F::alert('用户ID错误');
		}
		if(!$data['gold']){
			F::alert('请输入充值积分');
		}
		if($this->db->table('user_gold')->insert($data)){
			F::alert($str);
		}
		else{
			F::alert('积分增加失败，请重试');
		}

	}

	public function delete(){
		$id = F::input_int('get.id');
		if($id>0){
			$this->db->table('user')->where('id='.$id)->delete();
			$str = '会员删除成功';
		}
		else{
			$str = '参数错误';
		}
		F::redirect($str,$this->url('user'),1);
	}


	/**
	 * 会员等级管理
	 * [user_level description]
	 * @return [type] [description]
	 */
	public function user_level(){
		$list = $this->db->table('user_level')->order('privilege desc')->getlist();
		$this->view['list']=$list;
		$this->view('user_level');


	}

	public function level_add(){
		$data = F::input('post.');
		if(!$data['name']){
			F::alert('请输入会员等级名称');
		}
		if(!$data['privilege']){
			F::alert('请输入优惠折扣');
		}
		if($this->db->table('user_level')->insert($data)){
			F::alert('添加成功');
		}
		else{
			F::alert('添加失败，请重试');
		}
	}
	/**
	 * 删除会员等级
	 * @return [type] [description]
	 */
	public function level_del(){
		//删除该等级
		$id = F::input_int('get.id');
		if($this->db->table('user')->where('user_level='.$id)->getlist()){
			$str = '存在该等级的会员,等级不能删除';
		}
		else if($id>0){
			$this->db->table('user_level')->where('id='.$id)->delete();
			$str = '该等级删除成功'.$id;
		}
		else{
			$str = '参数错误';
		}
		F::redirect($str,$this->url('user/user_level'),1);
	}


	/**
	 * 修改会员等级
	 * @return [type] [description]
	 */
	public function level_edit(){
		$data = F::input('post.');

		if(!$data['id']){
			F::alert('会员等级ID错误');
		}
		if(!$data['name']){
			F::alert('请输入会员等级名称');
		}
		if(!$data['privilege']){
			F::alert('请输入优惠折扣');
		}
		if($this->db->table('user_level')->where('id='.$data['id'])->update($data)){
			F::alert('修改成功');
		}
		else{
			F::alert('修改失败，请重试');
		}
	}
}
?>