<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdapterException
 *
 * @author Animus Inc.
 */

define("ORMROOT",dirname(dirname(__FILE__)));

require_once ORMROOT."/classes/ORMEventLogger.class.php";

class AdapterException extends Exception{
    
    public function __construct($message, $code = 0, Exception $previous = null) {
            
		parent::__construct($message, $code, $previous);
                
        
	}
        
    public function logException($message){
        
        $ORMEventLogger = new ORMEventLogger(ORMROOT."/log/adapter.log");
        $ORMEventLogger -> logEvent($message);
        
    }
}

?>
