<?php
/** 
 * @author Administrator
 * 异常处理类
 * 
 */
class appexception extends Exception {
    public function __construct($message, $code=0, $type = 'html') {
        parent::__construct($message, $code);
        echo $message;
    }
    
    public function __toString(){
    }

}
?>