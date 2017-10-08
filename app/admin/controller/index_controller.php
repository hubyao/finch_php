<?php
class index_controller extends comm_controller{
	public function index(){
		$column = $this->db->table('section')->getlist();
		foreach($column as $k=>$v){
			$column[$k]['num']=$this->db->table(''.$v['ename'].'')->count();
		}
		$where = $this->db->table('user')->group('user_level')->getlist();
		$num = array();
		foreach ($where as $k => $v) {
			$user_level=$this->db->table('user_level')->where('id='.$v['user_level'])->getval('name');
			$num["{$user_level}"][] = $this->db->table('user')->where('user_level='.$v['user_level'])->count();
		}
		$this->view['num']=$num;
		$this->view['column'] = $column;
		$this->view['i'] =$i=1;
		$this->view['j'] =$i=1;
		$this->view('index.html');
	}
}
?>