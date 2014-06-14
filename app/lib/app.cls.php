<?php

class AppAction extends initAction{
	public function _init(){
		define('ROLE_STUDENT', 1);	
		define('ROLE_TEACHER', 2);	
	}
	
	/**
	 * 系统主界面
	 */
	public function index(){
	    $user = $this->session('user');
	    $this->assign("app_name", $this->app_name);
	    $this->assign("page_title", $this->app_name);
	    $this->assign('nav', $this->get_nav());

	    //所有科目-编辑试题
	    $w['n_user']=array('eq', $user['id']);
	    $subject = M('subjects')->where($w)->select();
	    $this->assign('subjects', $subject);
	    
	    $this->display('layout');
	}
	
    /**
     * 左侧导航
     */
    public function get_nav(){
        	//用户模块
    	$nav[]=array('title'=>'用户中心', 'sub'=>array(
			array('cat'=>'examing','title'=>'参加考试'),
			array('cat'=>'examed','title'=>'我参加过的考试'),
    		array('cat'=>'wrong', 'title'=>'错题分析'),
			array('cat'=>'infos', 'title'=>'个人信息')
		));
    	$user = $this->session('user');
    	if($user['n_role'] == ROLE_TEACHER){
			//试题模块
			$nav[]=array('title'=>'试题模块', 'sub'=>array(
				array('cat'=>'question', 'title'=>'所有试题'),
				array('cat'=>'question/add', 'title'=>'添加试题'),
			));
			//试卷模块
			$nav[]=array('title'=>'试卷模块', 'sub'=>array(
				array('cat'=>'exam', 'title'=>'所有试卷'),
				array('cat'=>'exam/add', 'title'=>'添加试卷'),
				array('cat'=>'verifying', 'title'=>'阅卷'),
			));
			
			//用户模块
			$nav[]=array('title'=>'用户管理', 'sub'=>array(
				array('cat'=>'users', 'title'=>'用户管理'),
				array('cat'=>'results', 'title'=>'成绩查询'),
			));
    	}
		return $nav;
    }
    	
}
