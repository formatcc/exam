<?php
/**
 * 重定向
 * 
 */
function redirect($action, $param=""){
	if(strpos($action, ":")){
		list($module, $action) = explode(":", $action);
	}else{
	    $module = MODULE;
	    $action = $action;
	}
	$url = sprintf("?m=%s&a=%s",$module, $action);
	header('Location: ' . $url);
	exit;
}

/**
 * 公共函数，生成url
 */
function U($url, $param=""){
    if(strpos($url, ":")){
        list($module, $action) = explode(":", $url);
    }else{
        $module = MODULE;
        $action = $url;
    }
    
    return sprintf("?m=%s&a=%s", $module, $action);
}

/**
 * 创建一个数据连接
 * @param string $table 连接所使用的表
 * @return db
 */
function M($table=""){
    static $_db;//数据库实例
    if(!$_db instanceof db){
        $_db = new db();
    }
    $_db->set_table_name($table);
    return $_db;
}

/**
 * 获取客户端 ip地址
 */
function get_client_ip($type = 0) {
	$type       =  $type ? 1 : 0;
	static $ip  =   NULL;
	if ($ip !== NULL) return $ip[$type];
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		$pos    =   array_search('unknown',$arr);
		if(false !== $pos) unset($arr[$pos]);
		$ip     =   trim($arr[0]);
	}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ip     =   $_SERVER['HTTP_CLIENT_IP'];
	}elseif (isset($_SERVER['REMOTE_ADDR'])) {
		$ip     =   $_SERVER['REMOTE_ADDR'];
	}
	// IP地址合法验证
	$long = sprintf("%u",ip2long($ip));
	$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
	return $ip[$type];
}

/**
 * 默认过滤器
 * @param $data 过滤的内容
 */
function default_filter($data){
    if(is_array($data)){
        $data = array_map('default_filter',$data);
    }else{
        $data = trim($data);
        $data = addslashes($data);
    }
    return $data;
}

/**
 * 分页页数过滤器
 * @param  $num 页数
 * @return 页数
 */
function page_num_filter($num){
	if(is_numeric($num)){
		$num = $num>0?$num:1;
	}else{
		$num=1;
	}
	return $num;
}

/**
 * 关闭魔术引号
 */
function close_magic_quote(){
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

/**
 * 删除转义字符
 * @package $value 需要删除的内容
 */
function stripslashes_deep($value){
    $value = is_array($value) ?array_map('stripslashes_deep', $value):stripslashes($value);
    return $value;
}	
