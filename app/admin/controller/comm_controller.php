<?php
class comm_controller extends controller{
	public function __construct(){
		$this->check_login();
		$this->init();
	}

	/*
	初始化系统
	*/
	public function init(){
		$this->view['session_id'] = session_id();
		$this->view['section'] = $this->db->table('section')->getlist();
		$this->view['admin_name'] = F::session('admin_name');
		$_POWER = [
			['text'=>'系统管理',	'value'=>'-1',],
			['text'=>'栏目管理',	'value'=>'-2',],
			['text'=>'管理员管理',	'value'=>'-3',],
			['text'=>'用户中心管理','value'=>'-4',],
		];
	}

	/**
	 * [检查当前管理员的权限]
	 * @return 当前管理员是否有指定的权限
	 */
	public function admin_power($power_val){
		$is_power = false;
		$aid = F::session('admin_id');
		$power_list = explode(',',$this->db->table('admin_user')->where('id='.$aid)->getval('user_power'));
		foreach($power_list as $t){
			if($power_val==$t){
				$is_power = true;
				break;
			}
		}
		return $is_power;
	}

	/*
	返回一维数组
	相当于get()
	 */
	public function D1($tb_name,$tb_where){
		$w_type = gettype($tb_where);
		if($w_type=='integer'){//以ID为主键查找
			$con1 = 'id=?';
			$con2 = array($tb_where);
		}
		elseif($w_type=='string'){//自定义的搜索条件
			$con1 = $tb_where;
			$con2 = false;
		}
		elseif($w_type=='array'){//以数组为条件
			$con1 = $tb_where[0];
			$con2 = isset($tb_where[1]) ? $tb_where[1] : false;
		}
		if($con2){
			return $this->db->table($tb_name)->where($con1,$con2)->get();
		}
		else{
			return $this->db->table($tb_name)->where($con1)->get();
		}
	}
	/*
	返回一维数组
	相当于get()
	 */
	public function D2($tb_name,$tb_where=''){
		if($tb_where){
			$w_type = gettype($tb_where);
			if($w_type=='integer'){//以ID为主键查找
				$con1 = 'id=?';
				$con2 = array($tb_where);
			}
			elseif($w_type=='string'){//自定义的搜索条件
				$con1 = $tb_where;
				$con2 = false;
			}
			elseif($w_type=='array'){//以数组为条件
				$con1 = $tb_where[0];
				$con2 = isset($tb_where[1]) ? $tb_where[1] : false;
			}
			if($con2){
				return $this->db->table($tb_name)->where($con1,$con2)->getlist();
			}
			else{
				return $this->db->table($tb_name)->where($con1)->getlist();
			}
		}
		else{
			return $this->db->table($tb_name)->getlist();
		}
	}
	/*
	执行SQL
	*/
	public function exec($sql){
		return $this->db->query($sql);
	}
	/*
	插入数据
	*/
	public function ins($tb_name,$tb_data){
		return $this->db->table($tb_name)->insert($tb_data);
	}
	/*
	获取字段数据
	*/
	public function val($tb_name,$field_name,$cond){
		return $this->db->table($tb_name)->where($cond)->getval($field_name);
	}

	/*
	删除数据
	*/
	public function del($tb_name,$ids){
		return $this->db->table($tb_name)->where('id in ('.$ids.')')->delete();
	}

	public function check_login(){
		if (!F::session('admin_id')) {
			F::go($this->url('login'));
		}
	}

	public function getpage($arr){
		$r_page = array();
		$limit = intval($arr['limit'])?intval($arr['limit']):10;
		$pager = new page_class();//分页类
		$pager->total = $this->db->table($arr['table'])->where($arr['where'])->count();
		$pager->page = F::input_int('get.page',1);//当前页码
		$pager->url = $this->page_url();
		$pager->limit = $limit;//一页多少条 可设置
		$limit = ($pager->page-1)*$pager->limit.','.$pager->limit;
		$r_page['list'] = $this->db->table($arr['table'])->where($arr['where'])->order($arr['order'])->  limit($limit)->getlist();
		$r_page['url'] = $pager->url();
		return $r_page;
	}

}
?>