<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SchemaLoader
 *
 * @author Animus Inc.
 */

// Constants

define('DOMROOT',dirname(dirname(dirname(__FILE__))));
define('ORMROOT', DOMROOT.'/AnimusORM');

//Imports

require_once ORMROOT.'/classes/RelationalEntity.class.php';
require_once ORMROOT.'/classes/Column.class.php';
require_once ORMROOT.'/classes/SQL.class.php';

class SchemaLoader {

    private $_entity = array();
    
    public function __construct() {
        
        $schema_def = new SimpleXMLElement(file_get_contents((DOMROOT.'/config/schema.xml')));
        
        foreach ( $schema_def->children() -> {'relational-entity'} as $_entity){
            
            $entity_name = (string)$_entity->name;
            $entity_constraint = (string)$_entity->constraint;
            $entity_engine = (string)$_entity->engine;
            
            $entity = new RelationalEntity($entity_name);
            $entity->setEngine($entity_engine);
            
            foreach($_entity -> columns as $columns){
                                
                    foreach($columns as $column){

                    $_colname = (string)$column[name];
                    $_defaultvalue = (string)$column[default_value];
                    $_datatype = (string)$column[datatype];
                    $_length = (string)$column[length];

                    $entity->addRecordField($_colname, $_defaultvalue, $_datatype, $_length);
                    
                }
                
            }
            
            $entity->setConstraint($entity_constraint);
            $this->_entity[$entity->getName()] = $entity;
            
        }

    }
    
    public function getEntity($_name){
            
            return $this->_entity[$_name];
            
    }
    
    public function getEntities(){
        
        return $this->_entity;
        
    }
}

?>
