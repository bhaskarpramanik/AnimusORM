<?php

/**
* Database connection parameters injection file
*/

define	('DOMROOT',	dirname	(	dirname	(dirname	(__FILE__))));
		
define	('ORMROOT',		DOMROOT.'/AnimusORM');

require_once	'DBDependencyBean.class.php';
require_once    'SQL.class.php';
require_once    'SchemaLoader.class.php';
require_once    'ORMException.class.php';
require_once    'AdapterException.class.php';

class DBDependencyContainer{

	private	$_dbdbean;
        private $_adapter_errors;
        private $_sql_errors;
        private $_schema_loader;
	
	public	function	__construct(){
		
		$this	->	setDBDBean	(	new	DBDependencyBean()	);
		$this	->	getDBDBean()	->	setSiteRoot( DOMROOT );
		$this	->	getDBDBean()	->	setORMRoot(	ORMROOT	);
		$this	->	invokeDBConfigReader();		
		$this	->	invokeDBAdapter();
                $this   ->      _schema_loader = new SchemaLoader();
                $flag = $this	->	getDBDBean()	->	getAdapterInstance() ->	connect();
                    if(!$flag) $this->setSQLErrors ("Couldn't connect to the database check DB connection");
                    else if($flag) $this	->	getDBDBean()	->	setConn(	$this	->	getDBDBean()	->	getAdapterInstance()	->	getConnection()	);
		return;
	}


	public	function	invokeDBConfigReader(){
		require_once(	$this	->	getDBDBean()	->	getORMRoot().'/classes/ConfigReader.class.php'	);
		$this	->	getDBDBean()	->	setConfigReader(		new	ConfigReader(	$this	->	getDBDBean()	->	getSiteRoot()	));
		return;
	}

	public	function	invokeDBAdapter(){
		require_once(	$this	->	getDBDBean()	->	getORMRoot().'/classes/LoadDBAdapter.class.php');		
		$loader	=	new	LoadDBAdapter();
		require_once	(DOMROOT.$loader	->	getAdapterClass());
		$adapterName	=	$loader	->	getAdapterName();
		$this	->	getDBDBean()	->	setAdapterInstance(	new	$adapterName(	$this	->	getDBDBean()	->	getConfigReader()	->	getConfigBeanContext()	)	);
		return;
	}
	
	public	function	setDBDBean(DBDependencyBean	$Object){
		$this	->	_dbdbean	=	$Object;
	}
        
        public function         setAdapterException($error){
            $this ->_adapter_errors = $error; 
            try{
                throw new AdapterException($error);
            }
            catch(AdapterException $e){  
                $e  ->logException($e->getMessage());
            }
        }
        
        public function         setSQLErrors($error){
            $this ->_sql_errors = $error;
            try{
                throw new ORMException($error);
            }
            catch(ORMException $e){       
                $e  ->logException($e->getMessage());
            }
        }
	
	public	function	getDBDBean(){
		return	$this	->	_dbdbean;
	}
        
        public function         getAdapter(){
            return $this->_dbdbean->getAdapterInstance();
        }
        
        public function getSQLErrors(){
            return $this->_sql_errors;
        }
        
        public function getAdapterException(){            
            return $this->_adapter_erors;
        }
        
        public function getRelationalEntity($_name){
            return $this -> _schema_loader ->getEntity($_name);
        }
        
        public function schemaSync(){
            $db_adapter = $this -> getAdapter();
            $schemaLoader = $this -> _schema_loader;
            $RelationalEntities = $schemaLoader -> getEntities();
            $schema = array();
            $adapter_flag = false;
            foreach($RelationalEntities as $RelationalEntity){
                $sql = new SQL($RelationalEntity);
                $sql -> readEntity();
                $adapter_flag = $db_adapter -> readEntity($sql);
                $schema[$RelationalEntity -> getName()] = $db_adapter -> getSchemaDef();
                if($adapter_flag){
                    if($schema[$RelationalEntity -> getName()]){ // The entity is already present
                    $Fields_existing = $schema[$RelationalEntity -> getName()]; // These columns already exist
                    $Fields_defined = $RelationalEntity -> getColumns();
                    foreach($Fields_defined as $Field){
                        if(in_array($Field -> getName(), $Fields_existing)){

                        // Nothing needs to be done in this case, so continue to the next iteration
                        // As the Field is already synchronized in the database
    
                        }
                        else{
                            $sql -> alterTable("add", $Field);
                            $adapter_flag_sub = $db_adapter -> alterTable($sql);
                            if(!$adapter_flag_sub){
                                $this ->setSQLErrors($db_adapter -> getSQLError());
                            }
                        }
                    }
                }   
            }
            else{ // The table doesn't exist in the database, create the table
                $sql -> createTable();
                $adapter_flag = $db_adapter -> createTable($sql);
                if(!$adapter_flag){        
                    $this ->setAdapterException($db_adapter -> getAdapterException());
                    $this ->setSQLErrors($db_adapter -> getSQLError());
                }
            } 
        }
       }
       
       public function select(RelationalEntity $RelationalEntity, $target, $discriminator){
           if(is_array($target)) $field_array = $target;
           else{
               $field_array = array();
               array_push($field_array, $target);   
           }
           $SQL = new SQL($RelationalEntity);
           $SQL ->select($field_array, $discriminator);
           $db = $this->getAdapter();
           $exec_flag = $db -> select($SQL);
           if($exec_flag){
               $RelationalEntity_return = $SQL ->getEntity ();
               return $RelationalEntity_return;
           }
           else{
               $this ->setAdapterException($db -> getAdapterException());
               $this ->setSQLErrors($db -> getSQLError());
               return false;   
           }
       }
       
       public function insert($RelationalEntity){
           $SQL = new SQL($RelationalEntity);
           $SQL -> insert();
           $db = $this->getAdapter();
           $exec_flag = $db -> insert($SQL);
           if($exec_flag){
               $RelationalEntity_return = $SQL ->getEntity ();
               return $RelationalEntity_return;
           }
           else{
               $this ->setAdapterException($db -> getAdapterException());
               $this ->setSQLErrors($db -> getSQLError());
               return false;
           }
       }
       
       public function update($RelationalEntity, $target, $discriminator){
           
           
       }
       
       public function batchUpdate($RelationalEntity, $target, $discriminator){
           
       }
       
       public function delete($RelationalEntity, $target, $discriminator){
           
           
       }

}
?>