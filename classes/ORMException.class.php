<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ORMException
 *
 * @author Animus Inc.
 */

require_once 'ORMEventLogger.class.php';

class ORMException extends Exception{
    
    public function __construct($message, $code = 0, Exception $previous = null) {
            
		parent::__construct($message, $code, $previous);
	}
        
    public function logException($message){
        
        $ORMEventLogger = new ORMEventLogger(ORMROOT."/log/sql.log");
        $ORMEventLogger -> logEvent($message);
        
    }
}

?>
