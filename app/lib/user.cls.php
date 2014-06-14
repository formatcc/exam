<?php
class UserAction extends initAction{
	public function _init(){
		define('ROLE_STUDENT', 1);	
		define('ROLE_TEACHER', 2);	
	}
	/**
	 * xx&id=1 get 获取单项
	 * xx&id=1 delete 删除单项
	 * xx	   put  修改保存
	 * xx      post 新增保存
	 */
	public function index(){
		if($_SERVER['REQUEST_METHOD'] == 'PUT'){
			$this->save();
		}else if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$this->add();
		}else if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
			$this->del();
		}else{
			$this->get_all();
		}
	}
	/**
	 * 保存
	 */
	public function save(){
		$data = json_decode(file_get_contents('php://input'), true);
		$where['id']= array('eq', $data['id']);
		$m = M('user');
		if(isset($data['s_password'])){
			if(trim($data['s_password'])==''){
				unset($data['s_password']);
			}else{
				$data['s_password'] = md5($data['s_password']);
			}
		}
	
		$rs = $m->where($where)->data($data)->update();
	
		if(is_numeric($rs)){
			$msg = array('error'=>0, 'msg'=>'保存成功！');
		}else{
			$msg = array('error'=>1, 'msg'=>'保存失败！');
		}
		$this->ajax_return($msg);
	}
	/**
	 * 添加
	 */
	public function add(){
		$data = json_decode(file_get_contents('php://input'), true);
		$m = M('user');
	
		if(isset($data['s_password'])){
			if(trim($data['s_password'])==''){
				$data['s_password'] = md5($data['s_account']);
			}else{
				$data['s_password'] = md5($data['s_password']);
			}
		}
		if(is_numeric($m->add($data))){
			$msg = array('error'=>0, 'msg'=>'保存成功！');
		}else{
			$msg = array('error'=>1, 'msg'=>'保存失败！');
		}
		$this->ajax_return($msg);
	}
	
	public function del(){
		$id = $this->get('id');
		if($id){
			$where['id']= array('eq', $id);
			$m = M('user');
			$rs = $m->where($where)->delete();
	
			if(is_numeric($rs)){
				$msg = array('error'=>0, 'msg'=>'删除成功！');
			}else{
				$msg = array('error'=>1, 'msg'=>'删除失败！');
			}
		}else{
			$msg = array('error'=>1, 'msg'=>'删除失败！');
		}
		$this->ajax_return($msg);
	}
	
	public function get_all(){
		$m = M('user');
		$this->setData_total($m->cols('count(id)')->getOne());
		$m = M('user');
		$list = $m->cols('id, s_account, s_nickname, case when n_role=1 then "学生" when n_role=2 then "教师" end as role, n_login_count,s_email, FROM_UNIXTIME(dt_last_login) as dt_last_login')->limit($this->page_sql())->select();
		$page = array('total'=>$this->getPage_total(), 'cur'=>$this->getPage_num());
		$this->ajax_return(array('data'=>$list, 'page'=>$page));
	
	}
	
	/**
	 * 登录页面
	 */
	public function login(){
	    $this->assign("page_title", "登陆");
		$this->assign("url", U("check_login"));
		$this->display();
		$this->display("public:footer");
	}
	
	/**
	 * 登陆检测
	 */
	public function check_login(){
		if($this->post('random')==1){
			$code = $this->random();
			$this->session('random', $code);
			$this->ajax_return(array('code'=>$code,'error'=>0, 'msg'=>''));
		}
	    if($this->post('name') == ''){
	    	$this->ajax_return(array('error'=>1,  'msg'=>"请输入用户名！"));
	    }
		if($this->post('password') == ''){
	    	$this->ajax_return(array('error'=>1,  'msg'=>"请输入密码！"));
	    }

	    $db = M('user');
	    $where['s_account'] = array("eq", $this->post('name'));

	    $rs = $db->cols('*')->where($where)->find();
	    if($rs){
	    	if($this->post('password') == md5($rs['s_password'].$this->session('random'))){
		    	$w['id']=array('eq', $rs['id']);
		    	$data['dt_last_login'] = time();
		    	$data['s_last_login_ip'] = get_client_ip();
		    	$data['n_login_count'] = array("exp","n_login_count+1");
		    	$db->where($w)->data($data)->update();
		    	unset($rs['s_password']);
		    	$this->session('user', $rs);
	    		$this->ajax_return(array('error'=>0,  'msg'=>"登陆成功！", 'forward'=>U('app:index')));
	    	}else{
	    		$this->ajax_return(array('error'=>1,  'msg'=>"用户名或密码错误！"));
	    	}
	    }else{
	    	$this->ajax_return(array('error'=>1,  'msg'=>"用户名不存在！"));
	    }
	}
	
	/*
	 * 产生随机数
	 */
	public function random(){
		return md5(time()<<2);
	}
	
    
    /**
     * 参加过的考试
     */
    public function examed(){
    	$m = M('user_examed a');
    	$where['a.n_user_id'] = array('eq', $this->user_id);
    	$this->setData_total($m->cols('count(id)')->where($where)->getOne());
    
   		$join = "left join tb_exams b on b.id=a.n_exam_id";
    	$list = $m->cols('a.id, a.f_score, b.s_name, FROM_UNIXTIME(a.dt_start) AS dt_start, FROM_UNIXTIME(a.dt_end) AS dt_end')->join($join)->where($where)->order('dt_start desc')->limit($this->page_sql())->select();
    	$page = array('total'=>$this->getPage_total(), 'cur'=>$this->getPage_num());
    	$this->ajax_return(array('data'=>$list, 'page'=>$page));
    }
    
    /**
     * 考试结果，成绩查询
     */
    public function result(){
    	$m = M('user_examed a');
    	$this->setData_total($m->cols('count(id)')->getOne());
    
    	$join = "left join tb_exams b on b.id=a.n_exam_id left join tb_user c on a.n_user_id= c.id";
    	$list = $m->cols('a.id, c.s_account, c.s_nickname, a.f_score, b.s_name, FROM_UNIXTIME(a.dt_start) AS dt_start, FROM_UNIXTIME(a.dt_end) AS dt_end')->join($join)->order('dt_start desc')->limit($this->page_sql())->select();
    	$page = array('total'=>$this->getPage_total(), 'cur'=>$this->getPage_num());
    	$this->ajax_return(array('data'=>$list, 'page'=>$page));
    }
    
    /**
     * 错题分析
     */
    public function wrong(){
    	if($this->get('id')){
    		$m = M('user_answers a');
    		$where['a.id'] = array('eq', $this->get('id'));
    		
    		$join = "left join tb_questions b on b.id=a.n_question_id";
    		$list = $m->cols('a.s_answer as error_answer, a.f_score as error_score, b.s_title, b.s_analyse, b.s_answer as answer,b.n_sort, b.s_options, b.f_score')->join($join)->where($where)->order('a.id desc')->find();
    		$this->ajax_return($list);
    		
    	}else{
	    	$m = M('user_answers a');
	    	$where['a.n_user_id'] = array('eq', $this->user_id);
	    	$where['a.n_error'] = array('eq', 1);
	    	$this->setData_total($m->cols('count(id)')->where($where)->getOne());
	    	    
	    	$join = "left join tb_questions b on b.id=a.n_question_id";
	    	$list = $m->cols('a.id,a.f_score, b.id as question_id, b.s_title')->join($join)->where($where)->order('a.id desc')->limit($this->page_sql())->select();
	    	foreach ($list as $k=>$v){
	    		$list[$k]['s_title'] = strip_tags($list[$k]['s_title']);
	    	}
	    	
	    	$page = array('total'=>$this->getPage_total(), 'cur'=>$this->getPage_num());
	    	$this->ajax_return(array('data'=>$list, 'page'=>$page));
    	}
    }
    
    /**
     * 用户信息
     */
    public function info(){
    	$user = $this->session('user');
    	$user['dt_last_login'] = date('Y-m-d H:i:s', $user['dt_last_login']);
    	$data['user'] = $user;
    	$this->ajax_return($data);
    }
    
	/**
	 * 退出系统
	 */
	public function logout(){
	    $this->session('user', null);
	    session_destroy();
        redirect("login");
	}
	
	
}
