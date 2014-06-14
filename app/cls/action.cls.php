<?php
abstract class Action {
    private $data; //模板变量
    
	/**
	 * 模板显示
	 * @param string $tpl
	 */
	function display($tpl=""){
		$module = MODULE;
		$action = ACTION;
		
		if($tpl){
			if(strpos($tpl, ":")){
				list($module, $action) = explode(":", $tpl);
			}else{
				$action = $tpl;
			}
		}
		if($this->data){
		    foreach ($this->data as $name=>$value){
		        $$name = $value;
		    }
		}

		$file = sprintf("%s/%s/%s%s", TPL_PATH, $module, $action, TPL_SUFFIX);
		if(is_file($file)){
    		include $file;
		}else{
		    die("模板文件不存在！".$file);
		}
	}
	
	/**
	 * 模板赋值
	 */
	public function assign($name, $value){
	    $this->data[$name] = $value;
	}
	
	/**
	 * 当前模块
	 */
	public function get_module(){
	    return MODULE;
	}

	/**
	 * 当前操作
	 */
	public function get_action(){
	    return ACTION;
	}
	
    /**
     * 获取$_GET参数
     * @param $key     获取的键值
     * @param $filter  过滤器
     * @param $default 默认值
     */
    function get($key, $filter=NULL, $default=false){
        return $this->input_filter($_GET,$key, $filter, $default);
    }
    
    /**
     * 获取$_POST参数
     * @param $key     获取的键值
     * @param $filter  过滤器
     * @param $default 默认值
     */
    function post($key, $filter=NULL, $default=false){
        return $this->input_filter($_POST,$key, $filter, $default);
    }
    
    /**
     * 过滤输入参数
     * @param $key     获取的键值
     * @param $filter  过滤器
     * @param $default 默认值
     */
    function input_filter($input, $key, $filter=NULL, $default=false){
        if(isset($input[$key])){
            $data = $input[$key];
            if(!function_exists($filter)){
                $filter = "default_filter";
            }
            $data   =   is_array($data)?array_map($filter,$data):$filter($data); // 参数过滤
            return $data;
        }else{
            return $default;//返回默认值
        }
    }

    /**
	 * session
	 */
	public function session($name, $value=""){
	    if(is_null($value)){
	        unset($_SESSION[$name]);
	        return ;
	    }
	    if(!$value){
	        if(isset($_SESSION[$name])){
	            return $_SESSION[$name];
	        }else{
	            return false;
	        }
	    }else{
	        $_SESSION[$name] = $value;
	    }
	}
	
	/**
	 * 成功跳转
	 */
	public function success($msg,$time=3, $url="javascript:history.back(-1)"){
	    $this->assign("msg", $msg);
	    $this->assign("time",$time);
	   	$this->assign("url", $url);
	    $this->display("public:success");
	    $this->display("public:footer");
	}
	
	/**
	 * 失败跳转
	 */
	public function error($msg,$time=3, $url="javascript:history.back(-1)"){
	    $this->assign("msg", $msg);
	    $this->assign("time",$time);
	   	$this->assign("url", $url);
	    $this->display("public:error");
	    $this->display("public:footer");
	    exit;
	}

	/**
	 * 
	 * Ajax json返回
	 * @param $array
	 */
	public function ajax_return($array){
		echo json_encode($array);
		exit;
	}
}

?>