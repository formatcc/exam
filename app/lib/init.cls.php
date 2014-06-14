<?php
/**
 * 初始化
 * @author yangtao
 *
 */
class initAction extends Action{
    protected $app_name = "在线考试系统";
    protected $page_size = 8;
    protected $page_num =1;
    protected $page_total;
    protected $data_total;
    public  $user_id;
    
	//不需要认证的模块
	private $noAuth = array(
		"user:login",
	    "user:check_login"
	);
	
	public function __construct(){
	    $this->check_login();
		$user = $this->session('user');
	    $this->assign('user', $user['s_nickname']);
	    $this->assign('userCenter', U('user:center'));
	    $this->assign('questionsList', U('questions:index'));
	    $this->assign('logout', U('user:logout'));
	    $this->setPage_num($this->get('p','page_num_filter',1));
	    $this->user_id = $user['id'];
	}
	
	public function page_sql(){
		$start = $this->getPage_size()*($this->getPage_num()-1);
		return " limit ".$start.",".$this->getPage_size();
	}
	
	/**
	 * @return the $data_total
	 */
	public function getData_total() {
		return $this->data_total;
	}

	/**
	 * @param field_type $data_total
	 */
	public function setData_total($data_total) {
		$this->data_total = $data_total;
	}

	/**
	 * @return the $page_size
	 */
	public function getPage_size() {
		return $this->page_size;
	}

	/**
	 * @return the $page_num
	 */
	public function getPage_num() {
		return $this->page_num;
	}

	/**
	 * @return the $page_total
	 */
	public function getPage_total() {
		$this->page_total = ceil($this->getData_total()/$this->getPage_size());
		return $this->page_total;
	}

	/**
	 * @param number $page_size
	 */
	public function setPage_size($page_size) {
		$this->page_size = $page_size;
	}

	/**
	 * @param number $page_num
	 */
	public function setPage_num($page_num) {
		$this->page_num = $page_num;
	}

	/**
	 * @param field_type $page_total
	 */
	public function setPage_total($page_total) {
		$this->page_total = $page_total;
	}

	/**
	 * 登陆检测
	 */
	private function check_login(){
		$cur_action = strtolower(MODULE.":".ACTION);
		if(!$this->session('user') && !in_array($cur_action, $this->noAuth)){
			if($this->is_ajax()){
				
			}else{
				redirect('user:login');
//				$this->error('请先登录！',3, U('user:login'));
			}
		}
	}
	
	/**
	 * 是否ajax请求
	 */
	public function is_ajax(){
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
			return true;
		}else{
			return false;
		}
	}
	
}