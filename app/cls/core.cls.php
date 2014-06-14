<?php
/**
 * 核心类
 * @author yangtao
 *
 */
class core {
    private static $_app; 
    private static $_lib;
    private static $_cls;
    
    private function __construct(){}
        
	public static function run(){
		$app = core::get_app();
		$app->init();
		$app->urlDispatcher();
				
		$class = MODULE.".cls.php";
		$module_name = MODULE."action";
		$class_file = strtolower(LIB_PATH.$class);
		
		if(!is_file($class_file)){
			die($class_file."不存在！");
		}else{
			$m = $app->init_class($module_name);
		    
			if(!method_exists($m,ACTION)){
				if(method_exists($m, '_default')){
					$m->_default();
				}else{
					die($module_name.":".ACTION."方法不存在！<br/>");
				}
			}else{
			    call_user_func(array($m, ACTION));
			}
		}
	}
	
	public static function get_app(){
        if(!isset(core::$_app)){
            core::$_app = new core();        
        }
        return core::$_app;
	}
	
	/**
	 * 初始化
	 */
	private function init(){
		session_start();
		//设置时区
		date_default_timezone_set("PRC");
		header('contentType:text/html;charset=utf-8');
		//注册autoload方法
		spl_autoload_register(array($this, "_autoload"));
        // 错误和异常处理
        register_shutdown_function(array($this,'fatal_error'));
        set_error_handler(array($this,'app_error'));
        set_exception_handler(array($this,'app_exception'));

        include APP_PATH."common/config.php";
		include APP_PATH."common/common.php";
		defined("CLS_PATH") or define('CLS_PATH',APP_PATH."cls/");
		defined("LIB_PATH") or define("LIB_PATH", APP_PATH."lib/");
		defined("TPL_PATH") or define("TPL_PATH", APP_PATH."tpl/");
	}
	
	public function fatal_error(){
	    $e = error_get_last();
	    if($e){
	        $this->app_error($e['type'], $e['message'], $e['file'], $e['line']);
	    }
	}
	
	public function app_error($errno, $errstr, $errfile, $errline){
	    echo "系统发生错误！<br/>";
	    echo "错误信息：".$errstr."<br/>";
	    echo "错误文件：".$errfile."<br/>";
		echo "错误行号：".$errline."<br/>";
	}
	
	public function app_exception(){
	
	}
	
    /**
     * 初始化一个类
     * @param $class_name 类名
     */
    public function init_class($class_name){
        if(!isset(core::$_lib[$class_name])){
            core::$_lib[$class_name] = new $class_name();//实例化
        	//初始化方法
        	if(method_exists(core::$_lib[$class_name], "_init")){
        		core::$_lib[$class_name]->_init();
        	}
        }
        return core::$_lib[$class_name];
    }

	/**
	 * URL解析
	 */
	private function urlDispatcher(){
		$module = DEFAULT_MODULE;
		$action = DEFAULT_ACTION;

		if(isset($_GET['m'])){
			$module = $_GET['m'];
		}
		if(isset($_GET['a'])){
			$action = $_GET['a'];
		}
		define("MODULE", $module);
		define("ACTION", $action);

	}
	
	/**
	 * 自动加载
	 */
	private function _autoload($class_name){
		$class_name = strtolower($class_name);
		//当前目录下action规则类
		if(preg_match("/(.+)action$/i", $class_name, $class)){
			$class_file = LIB_PATH.$class[1].".cls.php";
		}else{
			$class_file = CLS_PATH.$class_name.".cls.php";
		}
		if(is_file($class_file)){
			include $class_file;
		}else{
			die($class_file." does not exist！");
		}
	}

}
