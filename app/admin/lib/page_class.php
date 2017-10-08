<?php
class page_class {

	public $url='';//url地址头
	public $page = 1;//当前页码
	public $max_page = 1;//最大页码
	public $total = 0;//数据总数量
	public $limit = 20;//每页数据数量
	public $num_links = 5;//中间按钮数量

	public function init()
	{
		$this->max_page = ceil($this->total/$this->limit);//总页数
		$this->page = $this->page<1?1:$this->page;//纠正页码小于1
	    $this->page = $this->page>$this->max_page?$this->max_page:$this->page;//纠正输入大于总页数
	}

	public function num_page()//生成数字列码 12345...78910
	{
		$output = '';
		if ($this->max_page > 1) {//如果最大页大于1
		   if ($this->max_page <= ($this->num_links)) {
				$start = 1;                       //开始数字1
				$end = $this->max_page;          //结束数字为最大页码
			} else {
				$start = $this->page - floor($this->num_links / 2);  //floor() 函数向下舍入为最接近的整数
				$end = $this->page + floor($this->num_links / 2);

				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}

				if ($end > $this->max_page) {
					$start -= ($end - $this->max_page);
					$end = $this->max_page;
				}
			}

			if ($start > 1) {
				$output .= '<li class="disabled"><a href="#">...</a></li>';
			}

			for ($i = $start; $i <= $end; $i++) {
				if ($this->page == $i) {
					$output .= '<li class="active"><a href="#">' . $i . '</a></li>';
				} else {
					$output .= '<li><a href="' . str_replace('{page}', $i, $this->url) . '">' . $i . '</a></li>';
				}
			}

			if ($end < $this->max_page) {
				$output .= '<li class="disabled"><a href="#">...</a></li>';
			}
		}
		 return $output;
	}

	/**
	 * 分页链接
	 * @param  integer $mode [description]
	 * @return [type]        [description]
	 */
	public function show($mode=1)//$mode 分页模版
	{

		$this->init();//初始化值
		switch ($mode)
		{
			case 1:
				$html = '
					<ul class="pagination pagination-small">
					  <li><a href="'.str_replace('{page}', 1, $this->url).'">首页</a></li>';
					if ($this->page>1){
					  $html .= '<li><a href="'.str_replace('{page}', $this->page - 1, $this->url).'">上一页</a></li>';
					}else{
					  $html .= '<li class="disabled"><a href="#">上一页</a></li>';
					}
                 $html .= $this-> num_page();//列码
                    if ($this->page < $this->max_page) {
					  $html .= '<li><a href="'.str_replace('{page}', $this->page + 1, $this->url).'">下一页</a></li>';
					}else{
					  $html .= '<li class="disabled"><a href="#">下一页</a></li>';
					}
					  $html .= '<li><a href="'. str_replace('{page}', $this->max_page, $this->url).'">末页</a></li>
					  </ul>';
				break;
			case 2://缩减版分页数据
				$html =array(
				  '0'=>$this->page>1?str_replace('{page}', $this->page - 1, $this->url):'#',        //上一页连接
				  '1'=>$this->page>1?false:true,  //上一页状态
				  '2'=>$this->page,        //当前页
				  '3'=>$this->max_page,        //总页数
				  '4'=>$this->page < $this->max_page?str_replace('{page}', $this->page + 1, $this->url):'#',  //下一页状态
				  '5'=>$this->page < $this->max_page?false:true,  //下一页状态
				);
				break;
			default:
			    break;
		}
		return $html;
	}

	/**
	 * 分页URL
	 * @return [type]        [Array]
	 */
	public function url(){
		$this->init();//初始化值
		$a = $this->page;//当前页
		$b = $this->max_page;//最大页
		$c = $this->url;//链接URL
		$tmp_link['first'] = 	($a==1) ? '#' : str_replace('{page}', 1, $c) ;
		$tmp_link['previous'] = ($a>1) ? str_replace('{page}', $a-1, $c) : '#' ;
		$tmp_link['next'] = 	($a<$b) ? str_replace('{page}', $a+1, $c) : '#' ;
		$tmp_link['last'] = 	($a==$b) ? '#' : str_replace('{page}', $b, $c);
		$tmp_link['max'] = 		$b;
		$tmp_link['now'] = 		$a;
		$tmp_link['url'] = 		$c;
		return $tmp_link;
	}
}
?>