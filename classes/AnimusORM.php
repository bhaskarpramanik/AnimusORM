<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


/* 
 * AnimusORM the entry point of ORM. Requires DBDependencyContainer
 */

require_once 'DBDependencyContainer.class.php';

class AnimusORM {
    
    public static function startORM(){
        
        $dbdcontainer = new DBDependencyContainer();
        
        $dbdcontainer ->schemaSync();
        
        return $dbdcontainer;
        
    }
    
}

?>
