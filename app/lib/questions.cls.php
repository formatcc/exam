<?php
/**
 * 试题模块
 * @author Administrator
 *
 */
class questionsAction extends initAction {

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
		$m = M('questions');
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
		$data['dt_insert'] = time();
		$data['n_user_id'] = $this->user_id;
		$m = M('questions');
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
			$m = M('questions');
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
		$m = M('questions a');
//		$this->setPage_size(1);
		$this->setData_total($m->cols('count(id)')->getOne());

		$join = "left join tb_question_sort b on b.id=a.n_sort  left join tb_subjects c on a.n_subject_id=c.id";
		$list = $m->cols('a.id, a.s_title, a.f_score, a.n_sort, b.s_name as question_type, FROM_UNIXTIME(a.dt_insert) AS dt_insert, c.s_name as subject')->join($join)->order('a.id desc')->limit($this->page_sql())->select();
		foreach ($list as $k=>$v){
			$list[$k]['s_title'] = strip_tags($list[$k]['s_title']);
		}
		$page = array('total'=>$this->getPage_total(), 'cur'=>$this->getPage_num());
		$this->ajax_return(array('data'=>$list, 'page'=>$page));
		
	}
	
	public function get_by_id($id){
		$m = M('questions');
		$where['id'] = array('eq', $id);
		$res = $m->where($where)->find();
		$this->ajax_return($res);
	}
    
	public function upload(){
		$config=array(); 
		$config['type']=array("flash","img"); //上传允许type值 
		$config['img']=array("jpg","bmp","gif","png"); //img允许后缀 
		$config['flash']=array("flv","swf"); //flash允许后缀 
		$config['flash_size']=200; //上传flash大小上限 单位：KB 
		$config['img_size']=500; //上传img大小上限 单位：KB 
		$config['message']="上传成功"; //上传成功后显示的消息，若为空则不显示 
		$config['name']=time(); //上传后的文件命名规则 这里以unix时间戳来命名 
		$config['flash_dir']="./app/public/uploads"; //上传flash文件地址 采用绝对地址 方便upload.php文件放在站内的任何位置 后面不加"/" 
		$config['img_dir']="./app/public/uploads"; //上传img文件地址 采用绝对地址 采用绝对地址 方便upload.php文件放在站内的任何位置 后面不加"/" 
		$config['site_url']=""; //网站的网址 这与图片上传后的地址有关 最后不加"/" 可留空 
		//文件上传 
		$this->uploadfile($config); 
	}
	function uploadfile($config) { 
		//判断是否是非法调用 
		if(empty($_GET['CKEditorFuncNum'])){ 
			$this->mkhtml(1,"","错误的功能调用请求");
		}

		$fn=$_GET['CKEditorFuncNum']; 
		if(!in_array($_GET['type'],$config['type'])){
			$this->mkhtml(1,"","错误的文件调用请求"); 
		}
		
		$type=$_GET['type']; 
		if(is_uploaded_file($_FILES['upload']['tmp_name'])){ 
			//判断上传文件是否允许 
			$filearr=pathinfo($_FILES['upload']['name']); 
			$filetype=$filearr["extension"]; 
			if(!in_array($filetype,$config[$type])){ 
				$this->mkhtml($fn,"","错误的文件类型！");
			}
			//判断文件大小是否符合要求 
			if($_FILES['upload']['size']>$config[$type."_size"]*1024){
				$this->mkhtml($fn,"","上传的文件不能超过".$config[$type."_size"]."KB！"); 
			}
	
			$file_abso=$config[$type."_dir"]."/".$config['name'].".".$filetype; 
			$file_host=$file_abso; 

			if(move_uploaded_file($_FILES['upload']['tmp_name'],$file_host)) { 
				$this->mkhtml($fn,$config['site_url'].$file_abso,$config['message']); 
			} 
			else{ 
				$this->mkhtml($fn,"","文件上传失败，请检查上传目录设置和目录读写权限"); 
			} 
		} 
	} 

	//输出js调用 
	function mkhtml($fn,$fileurl,$message) { 
		$str='<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$fn.', \''.$fileurl.'\', \''.$message.'\');</script>'; 
		exit($str); 
	} 	

}
?>