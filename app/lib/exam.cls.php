<?php
/**
 * 试卷模块
 * @author Administrator
 *
 */
class examAction extends initAction {
	private $questions_info;//所有试题信息
	private $type = array(1=>'单选题',2=>'多选题',3=>'填空题',4=>'判断题', 5=>'简单题');

	public function _init(){

		define('TYPE_SINGLE', 1);
		define('TYPE_MULTIPLE', 2);
		define('TYPE_FILL_BLANK', 3);
		define('TYPE_JUDGE', 4);
		define('TYPE_SHORT_ANSWER', 5);
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
			if($this->get('id')){
				$this->get_by_id($this->get('id'));		
			}else{
				$this->get_all();
			}
		}
	}
	/**
	 * 保存
	 */
	public function save(){
		$data = json_decode(file_get_contents('php://input'), true);
		$where['id']= array('eq', $data['id']);
		$m = M('exams');
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
		$data['dt_create'] = time();
		$data['n_user_id'] = $this->user_id;
		$m = M('exams');
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
			$m = M('exams');
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
		$m = M('exams');
		$this->setData_total($m->cols('count(id)')->getOne());

//		$join = "left join tb_question_sort b on b.id=a.n_sort  left join tb_subjects c on a.n_subject_id=c.id";
		$list = $m->cols('*, FROM_UNIXTIME(dt_create) AS dt_create')->order('dt_create desc')->limit($this->page_sql())->select();
		$page = array('total'=>$this->getPage_total(), 'cur'=>$this->getPage_num());
		$this->ajax_return(array('data'=>$list, 'page'=>$page));
		
	}
	
	public function get_by_id($id){
		$m = M('exams');
		$where['id'] = array('eq', $id);
		$res = $m->where($where)->find();
		if($res){
			$res['infos']=json_encode($this->get_exam_questions($res['s_content']), true);
		}
		$this->ajax_return($res);
	}
	/**
	 * 考试
	 */
	function examing(){
		$m = M('exams');
		$id = $this->get('id');
		$where['id'] = array('eq', $id);
		$res = $m->where($where)->find();

		$questions = json_decode($res['s_content'], true);
		$infos = $this->get_exam_questions($res['s_content']);
		$_SESSION['questions_info'] = $infos; //保存所有试卷试题信息
		if($this->session('examing_id') != $id || !$this->session('dt_start_examing') || !$this->session('dt_end_examing')){
			$start_time = time();
			$_SESSION['dt_start_examing'] = $start_time; //考试开始时间
			$_SESSION['dt_end_examing'] = $start_time+$res['n_spend']; //考试结束时间
			$_SESSION['examing_id'] = $id;
		}
		
		
		$lefttime = $this->session('dt_end_examing')-time();
 		$user = $this->session('user');
		$this->assign('user', $user);
		$this->assign('page_title', $this->app_name);
		$this->assign('questions', $questions);
		$this->assign('infos', $infos);
		$this->assign('exam', $res);
		$this->assign('lefttime', $lefttime);
		$this->display();
	}

	/**
	 * 交卷
	 */
	function submit(){
		
		$answer = $this->post('answer');
		$questions_info = $this->session('questions_info');
		$no_answer = 0; //未作答题数
		$all = count($questions_info); //总题数
		$total_score = 0; //总分
		$score = 0; //得分
		$dt_end_examing = time(); //交卷时间
		$dt_spent = ceil(($dt_end_examing - $this->session('dt_start_examing'))/60); //考试耗时
		
		$result = array();
		for($n=1; $n<=5;$n++){
			$result[$n]['type']=$this->type[$n]; //分类
			$result[$n]['num']=0; //分类总题数
			$result[$n]['total_score']=0;//分类总分值
			$result[$n]['score']=0;//分类总得分
			$result[$n]['true']=0;//分类正确题数
		}
				
		$m = M('user_answers');
		foreach ($questions_info as $id=>$info){
			$in_data = null;
			$in_data['s_examing'] = $this->examing_number();
			$in_data['n_exam_id'] = $this->get('id');
			$in_data['n_question_id'] = $id;
				
			$type = $info['n_sort'];
			$result[$type]['num']++; //分类题目数
			$result[$type]['total_score']+=$info['f_score']; //分类总分
			$total_score += $info['f_score']; //试卷总分

			if(isset($answer[$id])){
				$data = $answer[$id];
				$sort = $info['n_sort'];
					
				switch($sort){
					case TYPE_SINGLE:{
						$in_data['s_answer'] = $data;
						if($info['s_answer'] == $data){
							$result[TYPE_SINGLE]['true']++;
							$result[TYPE_SINGLE]['score']+=$info['f_score'];
							$score += $info['f_score'];

							$in_data['f_score'] = $info['f_score'];
							$in_data['n_error'] = 0;
						}else{
							$in_data['f_score'] = 0;
							$in_data['n_error'] = 1;
						}
						break;
					};
					case TYPE_MULTIPLE:{
						$data = implode('', $data);
						$in_data['s_answer'] = $data;
						if($info['s_answer'] == $data){
							$result[TYPE_MULTIPLE]['true']++;
							$result[TYPE_MULTIPLE]['score']+=$info['f_score'];
							$score += $info['f_score'];

							$in_data['f_score'] = $info['f_score'];
							$in_data['n_error'] = 0;
						}else{
							$in_data['f_score'] = 0;
							$in_data['n_error'] = 1;
						}
				
						break;
					};
				
					case TYPE_FILL_BLANK:{
						$result[TYPE_FILL_BLANK]['true']='-';
						$result[TYPE_FILL_BLANK]['score']='-';
						
						foreach ($data as $i=>$d){
							$key = chr(65+$i);
							$data[$key] = $d;
							unset($data[$i]);
						}
						
						$in_data['s_answer'] = json_encode($data);
						$in_data['f_score'] = 0;
						$in_data['n_error'] = 0;
						
						if(str_replace(' ', '', implode('', $data))==''){
							$no_answer++;
							$in_data['n_error'] = 1;
						}
						
						break;
					};
				
					case TYPE_JUDGE:{
						$in_data['s_answer'] = $data;
						if($info['s_answer'] == $data){
							$result[TYPE_JUDGE]['true']++;
							$result[TYPE_JUDGE]['score']+=$info['f_score'];
							$score += $info['f_score'];

							$in_data['f_score'] = $info['f_score'];
							$in_data['n_error'] = 0;
						}else{
							$in_data['f_score'] = 0;
							$in_data['n_error'] = 1;
						}
				
						break;
					};
					case TYPE_SHORT_ANSWER:{
						$result[TYPE_SHORT_ANSWER]['true']='-';
						$result[TYPE_SHORT_ANSWER]['score']='-';

						$in_data['s_answer'] = $data;
						$in_data['f_score'] = 0;
						$in_data['n_error'] = 0;
						
						if(str_replace(' ', '', $data)==''){
							$no_answer++;
							$in_data['n_error'] = 1;
						}
						break;
					};
				}
				
			}else{
				$no_answer++;
				$in_data['s_answer'] = '';
				$in_data['f_score'] = 0;
				$in_data['n_error'] = 1;
			}
			$in_data['n_user_id']=$this->user_id;
			$m->data($in_data)->add();
				
		}

		$_SESSION['all']= $all;
		$_SESSION['total_score']= $total_score;
		$_SESSION['dt_spent']= $dt_spent;
		$_SESSION['result']= $result;
		$_SESSION['no_answer']= $no_answer;
		$m = M('user_examed');
		$data = null;
		$data['n_exam_id'] = $this->get('id');
		$data['n_user_id'] = $this->user_id;
		$data['dt_start'] = $this->session('dt_start_examing');
		$data['dt_end'] = $dt_end_examing;
		$data['f_score'] = $score;
		$data['s_examing'] = $this->examing_number();
		$m->data($data)->add();
		unset($_SESSION['dt_start_examing']);
		unset($_SESSION['dt_end_examing']);
		unset($_SESSION['questions_info']);
		unset($_SESSION['examing_id']);
		$this->display();
	}
	
	/**
	 * 考试结果
	 */
	function result(){
		$this->assign('all', $this->session('all'));
		$this->assign('total_score', $this->session('total_score'));
		$this->assign('dt_spent', $this->session('dt_spent'));
		$this->assign('result', $this->session('result'));
		$this->assign('no_answer', $this->session('no_answer'));
		$this->assign('page_title', $this->app_name);
		$this->display();
	}
	
	/**
	 * 获取唯一参考序号
	 */
	public function examing_number(){
		if($this->session('examing_number')){
			$num = $this->session('examing_number');
		}else{
			$r = time();
			$num = substr(md5($r), 0, 10).$r;
		}
		return $num;
	}
	
	/**
	 * 根据试卷内容获取所有试题信息
	 * @param unknown $exam 试卷内容json
	 * @return unknown|boolean
	 */
	function get_exam_questions($exam){
			$data = json_decode($exam, true);
			$res['infos']=null;
			$ids=array();
			foreach($data as $item){
				if($item['data']){
					foreach ($item['data'] as $k){
						$ids[] = $k['id'];
					}
				}
			}
			if($ids){
				$ids = implode(',', $ids);
				$q = M('questions');
				$info = $q->where('id in('.$ids.')')->select();
				$infos = array();
				foreach ($info as $v){
					$infos[$v['id']] = $v;
				}
				return $infos;
			}
			return false;
	}
	/**
	 * 是否重复参考
	 */
	public function is_examed(){
		$m = M('user_examed');		
		$where['n_exam_id'] = array('eq', $this->get('id'));
		$where['n_user_id'] = array('eq', $this->user_id);
		$res = $m->cols('count(*)')->where($where)->getOne();
		if($res){
			$msg = array('code' => 1);
		}else{
			$msg = array('code' => 0);
		}
		$this->ajax_return($msg);
	}
	
	/**
	 * 阅卷列表
	 */
	public function verify(){
		$m = M('user_examed a');

		$this->setData_total($m->cols('count(id)')->getOne());
		
		$join = "left join tb_exams b on b.id=a.n_exam_id left join tb_user c on a.n_user_id = c.id";
		$list = $m->cols('a.id, b.s_name, c.s_nickname as student, FROM_UNIXTIME(a.dt_start) AS dt_start, FROM_UNIXTIME(a.dt_end) AS dt_end, case when a.n_verifyed=1 then "已批阅" when a.n_verifyed=0 then "待批阅" end as state  ')->join($join)->order('dt_end desc')->limit($this->page_sql())->select();
		$page = array('total'=>$this->getPage_total(), 'cur'=>$this->getPage_num());
		$this->ajax_return(array('data'=>$list, 'page'=>$page));
	}
	
	
	/**
	 * 阅卷页面
	 */
	public function verifying(){
		$id = $this->get('id');
		$m = M('user_answers a');
		$join = 'left join tb_user_examed b on a.s_examing = b.s_examing left join tb_questions c on a.n_question_id=c.id left join tb_question_sort d on c.n_sort = d.id';
		$where['b.id'] = array('eq', $id);
		$res = $m->cols('c.id as q_id, a.id as a_id, b.id as e_id, a.s_examing, c.s_title, c.s_options, c.s_answer as answer, c.s_analyse, c.f_score, a.f_score as score, a.s_answer as user_answer, d.s_name as sort,c.n_sort')->join($join)->where($where)->order('a.id asc')->select();
		
		$user = M('user a')->cols('a.s_account, a.s_nickname')->join('left join tb_user_examed b on b.n_user_id=a.id')->where($where)->find();
//		var_dump($res);
		
		$this->assign('questions', $res);
		$this->assign('id', $id);
		$this->assign('user', $user);
		$this->assign('page_title', "批改试卷-".$this->app_name);
		$this->display();
	}
	
	/**
	 * 保存阅卷
	 */
	public function save_verifying(){
		$scores = $this->post('score');
		$id = $this->get('id');

		$m = M('user_examed');
		$where['id'] = array('eq', $id);
		$data['f_score'] = array_sum($scores);
		$data['n_verifyed'] = 1;
		$m->data($data)->where($where)->update();
		
		$n = M('user_answers');
		$data = null;
		$data['n_verifyed'] = 1;
		$ref_score = $this->post('ref_score');
		
		foreach ($scores as $a_id=>$score){
			$w['id'] = array('eq', $a_id);
			$data['f_score'] = $score;			
			if($score == $ref_score[$a_id]){
				$data['n_error'] = 0;
			}else{
				$data['n_error'] = 1;
			}
			$m->data($data)->where($w)->update();
		}
		$this->display('verifying_submit');
	}
	
	/**
	 * 获取下一个未批阅试卷信息
	 */
	public function verifying_next(){
		$m = M('user_examed a');		
		$join = "left join tb_exams b on b.id=a.n_exam_id left join tb_user c on a.n_user_id = c.id";
		$where['n_verifyed'] = array('eq', 0);
		$info = $m->cols('a.id, b.s_name, c.s_nickname as student')->join($join)->where($where)->find();
		$this->ajax_return($info);
	}
	
	/**
	 * 查阅已考试卷
	 */
	public function examed(){
		$id = $this->get('id');
		$m = M('user_answers a');
		$join = 'left join tb_user_examed b on a.s_examing = b.s_examing left join tb_questions c on a.n_question_id=c.id left join tb_question_sort d on c.n_sort = d.id';
		$where['b.id'] = array('eq', $id);
		$res = $m->cols('c.id as q_id, a.id as a_id, b.id as e_id, a.s_examing, c.s_title, c.s_options, c.s_answer as answer, c.s_analyse, c.f_score, a.f_score as score, a.s_answer as user_answer, d.s_name as sort,c.n_sort')->join($join)->where($where)->order('a.id asc')->select();
		
		$user = M('user a')->cols('a.s_account, a.s_nickname, b.f_score')->join('left join tb_user_examed b on b.n_user_id=a.id')->where($where)->find();
		
		$this->assign('questions', $res);
		$this->assign('id', $id);
		$this->assign('user', $user);
		$this->assign('page_title', "查看试卷-".$this->app_name);
		$this->display('examed_show');
	}
	
}
?>