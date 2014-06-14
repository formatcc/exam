<?php
/**
 * 数据库操作类
 * @author Administrator
 *
 */
class db extends PDO{
    private $_dsn;
    private $_table_name;//表名
    private $_where;
    private $_join;
    private $_cols;
    private $_sql;
    private $_order;
    private $_limit;
    private $_query_type; //查询类型
    private $_query_types=array('select','update','insert','delete');
    private $_data;//数组类型，需要保存的数据
	//select * from xx where xxx group by xx limit x
	//update xx set xx=xx where xx=xx
    private $_sql_tpl="";
    private $_symbol = array('eq'=>'=',
    						'gt'=>'>=',
    						'lt'=>'<='
    						);
    
    public function  __construct(){
        $this->_dsn = sprintf("%s:host=%s;dbname=%s;port=%s;charset=%s", DB_TYPE, DB_HOST, DB_NAME, DB_PORT, DB_CHARSET);
        try{
            parent::__construct($this->_dsn, DB_USER, DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
        }catch (Exception $e){
            $msg = array('error'=>1, 'msg'=>'系统错误：数据库连接失败！');
            throw new appexception(json_encode($msg));
        }
    }
    
    /**
     * 多表查询，使用该方法则M的表名无效
     * @param $tables 所有需要使用的表,使用数组方式可不给出完整表名，直接使用sql语句需给出完整表名
     * @return db
     */
    public function table($tables){
    	if(is_array($tables)){
    		//array('a'=>'user', 'b'=>'admin')
			foreach ($tables as $alias=>$table){
				if(is_numeric($alias)){
					$tmp[] = DB_TABLE_PREFIX.$table;
				}else{
					$tmp[] = DB_TABLE_PREFIX.$table.' as '.$alias;
				}
			}
			
			$this->_table_name = implode(',', $tmp);
			
    	}else{
    		//tb_user as a, tb_admin as b
	    	$this->_table_name = $tables;
    	}
		return $this;
    }
    
    /**
     * join 查询
     * @param  $sql 完整join语句，例如：left join b on a.id=b.id
     * @return db
     */
    public function join($sql){
    	$this->_join = $sql;
    	return $this;
    }
    
    public function where($where){
    	if(is_array($where)){
    		$this->_where = $this->_parse_where($where);	
    	}else{
	        $this->_where = $where;
    	}
        return $this;
    }
    
    public function data($data){
    	if(is_array($data)){
    		$this->_data = $data;
    	}else{
    		die("data must be an array!");
    	}
    	return $this;
    }
    
    private function _parse_where($where){
    	foreach ($where as $k=>$v){
    		if(is_string($v[1])){
    			$v[1] = "'{$v[1]}'";
    		}
    		$tmp[] = $k.$this->_symbol[$v[0]].$v[1];
    	}
    	return implode(' and ', $tmp);
    }
    
    
    public function cols($cols){
        $this->_cols = $cols;
        return $this;
    }
    /**
     * order条件
     * @param 需要排序的字段和排序方式，例如：id desc
     * @return db
     */
    public function order($order){
    	$this->_order = $order;
    	return $this;
    }
    /**
     * limit条件
     * @param 完整limit，例如：limit 1,10 
     * @return db
     */
    public function limit($limit){
        $this->_limit= $limit;
        return $this;
    }
    /**
     * 查询，返回关联数组
     */
    public function select(){
        $this->_build_sql();
        $d = $this->query($this->_sql);
        return $d->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 只返回一条数据，失败返回false
     */
    public function find(){
        $this->limit("limit 1")->_build_sql();
        $rs = $this->query($this->_sql);

        if($rs->rowCount()){
            return $rs->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }
    
    /**
     * 获取一个值，失败返回false
     */
    public function getOne(){
        $rs = $this->find();
        if($rs){
           $rs = array_values($rs);
           return $rs[0];
        }else{
            return false;
        }
    }
    /**
     * 更新
     */
    public function update(){
    	$this->_set_query_type("update");
    	$this->_build_sql();
    	$rs = $this->exec($this->_sql);
    	return $rs;
    }
    
    /**
     * 添加
     */
    public function add($data=null){
    	if($data){
    		$this->data($data);
    	}
    	$this->_set_query_type("insert");
    	$this->_build_sql();
    	$rs = $this->exec($this->_sql);
    	return $rs;
    }
    
    /**
     * 删除
     */
    public function delete(){
    	$this->_set_query_type("delete");
    	$this->_build_sql();
    	$rs = $this->exec($this->_sql);
    	return $rs;
    }
    
    private function _set_query_type($type){
    	if(in_array($type, $this->_query_types)){
	    	$this->_query_type = $type;
    	}else{
    		die("unknow query type!");
    	}
    }
    
    private function _get_query_type(){
    	return $this->_query_type?$this->_query_type:"select";
    }
    
    private function _build_sql(){
    	$this->_sql = "";
        $query_type = $this->_get_query_type();
        switch ($query_type){
        	case 'insert':
        		$this->_build_insert();
        		break;
        	case 'delete':
        		$this->_build_delete();
        		break;
        	case 'update':
        		$this->_build_update();
        		break;
        	case 'select':
        		$this->_build_select();
        		break;
        }
    }
    private function _build_update(){
    	//update table set xx=xx where xx
		if(!$this->_where){
			die("update table must be have where conditions!");
		}else{
			if(!$this->_data){
				die("no data for update!");
			}
			foreach ($this->_data as $k=>$v){
				if(is_array($v) && $v[0]=='exp'){ //表达式
					$v = $v[1];
				}else if(is_null($v)){
					$v = "null";
				}else if(!is_numeric($v)){//非数字
					$v = "'{$v}'";
				}
				$tmp[] = $k."=".$v;
			}
			$str = implode(",", $tmp);
			$this->_sql = "update ".$this->get_table_name()." set ".$str." where ".$this->_where;
		}
    }
    
    private function _build_delete(){
    	//delete from table where xx=xx
    	if(!$this->_where){
    		die("delete table must be have where conditions!");
    	}else{
    		$this->_sql = "delete from ".$this->get_table_name()." where ".$this->_where;
    	}
    }
    
    private function _build_select(){
    	$sql[] = "select";
    	if($this->_cols){
    		$sql[] = $this->_cols;
    	}else{
    		$sql[]= "*";
    	}
    	$sql[] = "from";
    	$sql[] = $this->get_table_name();
    	
    	if($this->_join){
    		$sql[] = $this->_join;
    	}
    	
    	if($this->_where){
    		$sql[] = "where ".$this->_where;
    	}
    	
    	if($this->_order){
    		$sql[] = "order by ".$this->_order;
    	}
    	
    	if($this->_limit){
    		$sql[] = $this->_limit;
    	}
    	$this->_sql = implode(" ", $sql);
    }
    
    private function _build_insert(){
    	//insert into table(a,b,c) values(1,2,3)
    		if(!$this->_data){
    			die("no data for add!");
    		}
    		foreach ($this->_data as $k=>$v){
    			if(is_array($v) && $v[0]=='exp'){ //表达式
    				$v = $v[1];
    			}else if(!is_numeric($v)){//非数字
    				$v = "'{$v}'";
    			}
    			$cols[] = $k;
    			$values[] = $v;
    		}
    		$cols = implode(",", $cols);
    		$values = implode(',', $values);
    		$this->_sql = sprintf('insert into %s(%s) values(%s)', $this->get_table_name(), $cols, $values);
    }
    
    public function set_table_name($table){
    	$this->_where='';
    	$this->_join='';
    	$this->_cols='';
    	$this->_sql='';
    	$this->_order='';
    	$this->_limit='';
    	$this->_query_type=''; //查询类型
    	$this->_data='';
    	$this->_sql_tpl='';
    	
    	$this->_table_name = DB_TABLE_PREFIX.$table;
        return $this;
    }
    
    public function get_table_name(){
    	return $this->_table_name;
    }
        
    function __destruct() {
    }
}
?>