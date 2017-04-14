<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EventLogger
 *
 * @author Animus Inc.
 */

class ORMEventLogger {
   
    private $_log_path;
    
    private $_log_handle;


    public function  __construct($path){
       
        $this ->setLogPath($path);
        
        $this ->getLogHandle();
        
        return;
    }
    
    public function setLogPath($path){
        
        $this->_log_path = $path;
        
        return;
        
    }
    
    public function getLogHandle(){
        
        $this->_log_handle = fopen($this->_log_path,"a+");
        
        return;
    }
    
    public function logEvent($message){

        $date = date("d/m/Y H:i:s");
        
        $log_string = $message." ".$date."\r\n";
        
        fwrite($this->_log_handle, $log_string);
        
        fclose($this ->_log_handle);
        
        return;
    }
    
}

?>
