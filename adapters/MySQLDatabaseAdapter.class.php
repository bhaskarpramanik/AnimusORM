<?php
/**
*	MySQL Database Adapter implements DatabaseAdapter
*       Based on MySQLi
*/

define	('DOMROOT',	dirname	(	dirname	(dirname	(__FILE__))));

require_once	(DOMROOT.'/AnimusORM/interfaces/DataBaseAdapter.interface.php');
require_once	(DOMROOT.'/AnimusORM/classes/SQL.class.php');


class	MySQLDatabaseAdapter implements DataBaseAdapter{

	private $_configBeanContext;
	
	private	$_connectionObject;
	
	private	$_affectedRows;
        
        private $_sql_error;
        
        private $_adapter_exception;
        
        private $_schema_def;
        
        private $_entity;

	public function	__construct	(ConfigBean $ConfigBeanContext){

		$this	->	_configBeanContext	=	$ConfigBeanContext;
		
	}

	public	function	connect(){

            $_conn_obj = 		@new	mysqli(	$this	->	_configBeanContext	->	getDB_HOST(),	$this	->	_configBeanContext	->	getDB_USER(),	$this	->	_configBeanContext	->	getDB_PASS(),	$this	->	_configBeanContext	->	getDB_NAME());
            
            if(!isset($_conn_obj->connect_error)){
            
                $this	->	setConnection($_conn_obj);
                
                return true;
            }
            
            else    return false;
	}
	
	public	function	disconnect(){
	
		$this	->	getConnection	()	->	close();
	
	}
	
	public	function	setConnection(	mysqli	$object	){
	
		$this	->	_connectionObject	=	$object;
	
	}
	
	public	function	setAffectedRows(	$count	){
	
		$this	->	_affectedRows	=	$count;
	
	}
	
	public	function	getAffectedRows(){
	
		return	$this	->	_affectedRows;
	
	}
	
	public	function	getConnection(){
	
		return	$this	->	_connectionObject;
	
	}
        
        public function         setSQLError($error){
            
            $this->_sql_error = $error;
            
        }
        
        public function        getSQLError(){
            
            return $this->_sql_error;
            
        }
        
        public function     setAdapterException($AdapterException){
            
            $this->_adapter_exception = $AdapterException;
            
        }
        
        public function     setEntity(RelationalEntity $RelationEntity){
            
            $this ->_entity = $RelationalEntity;
            
        }
        
        public function     getAdapterException(){
            
            return $this->_adapter_exception;
        }
        
        public function     getSchemaDef(){
            
            return $this -> _schema_def;
            
        }
        
        public function     getEntity(){
            
            return $this -> _entity;
            
        }
        
        // Data Manipulation Language Commands
	
	public	function	insert(	SQL	$SQLQuery	){
	
		$_conn	=	$this	->	getConnection();
                
                if	(	isset($_conn)){

			if(	strcmp($SQLQuery	->	getQueryType(),"insert")	==	0){
				
				/*
				**	Prepare the query
				*/
			
				if($_stmt	=	$_conn	->	prepare(	$SQLQuery	->	getSQLQuery())){
				
				
				/*	Bind Params
				**
				*/
				
				$paramsArray	=	array();
				
				foreach(	$SQLQuery	->	getEntity()	->	getColumns()	as	$Column){
				
					$colDatatype	=	$Column	->	getDatatype();
					
						switch($colDatatype){
						
							case	"integer":	$type	=	"i";
								
								break;
							
							case	"varchar":	$type	=	"s";
							
								break;
								
							case	"boolean":	$type	=	"b";
							
								break;

						}

						
					$paramType	.=	$type;
										
				}
				
				$paramsArray[]	=	&$paramType;
				
				foreach	(	$SQLQuery	->	getEntity()	->	getColumns()	as	$Column	){
				
					$colName	=	$Column	->	getName();				
					
					$paramsArray[$colName]	=	&$colName;

				}
				
				/*
				**	Suppress warning and call $stmt::bind_param using call_user_func_array
				*/
				
				@call_user_func_array(array($_stmt,'bind_param'),	$paramsArray);
				
			       if($_stmt	->	execute()){
                                    
                                        	$this	->	setAffectedRows(	$_conn	->	affected_rows	);
                                        
                                        	return true;
                                        }
                                    
                                        else{
                                            try{
					
						throw	new	Exception("Unexpected Error : Requested operation couldn't be completed");
					
                                            }
					catch(Exception	$e){
					
                                                $this->setAdapterException($e->getMessage());
                                                
                                                $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
                                                $this->getSQLError();
                                                
						return false;
					}
                                   }
				}
				else{
				
					try{
					
						throw	new	Exception("Unexpected Error : Expecting ".'"delete" type query, instead "'.$SQLQuery	->	getQueryType().'" type query provided');
					
					}
					catch(Exception	$e){
					
						$this->setAdapterException($e	->	getMessage());
                                                
                                                $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
						return false;
					}
				}
			}
			
			else{
			
				try{
					
					throw	new	Exception("Unexpected Error : Expecting ".'"insert" type query, instead "'.$SQLQuery	->	getQueryType().'" type query provided');
				}

				catch(Exception	$e1){
				
					$this->setAdapterException($e1	->	getMessage());
                                           
                                        return false;
				}
			
			}
		}	
	}
	
	public	function	select(	SQL $SQLQuery	){
            
		$_conn	=	$this	->	getConnection();
		
                    if	(isset($_conn)){ // If connection to the database is available
		
			if(	strcmp($SQLQuery	->	getQueryType(),"select")	==	0){
		
				/**
				*	Prepare the query
				*/
                            
				if($_stmt	=	$_conn	->	prepare(	$SQLQuery	->	getSQLQuery())){
			
					/**
					*	Bind the results
					*/
					
					$paramsArray	=	array();
				
					foreach	(	$SQLQuery	->	getEntity()	->	getColumns()	as	$Column	){
					
						if($Column -> isFetchable()){
                                                    
                                                    $colName	=	$Column	->	getName();
					
                                                    $paramsArray[$colName]	=	&$$colName;
                                                    
                                                }

					}	
					
					/*
					**	Suppress warning and call $stmt::bind_result using call_user_func_array
					*/
                                        
					if($_stmt	->	execute()){
                         
                                        @call_user_func_array(array($_stmt,'bind_result'),	$paramsArray);
                                    
                                        $this	->	setAffectedRows(	$_conn	->	affected_rows	);
                                        
                                            while(	$_stmt	->	fetch()){

                                                    foreach(	$SQLQuery	->	getEntity()	->	getColumns()	as	$Column){

                                                            if($Column -> isFetchable()){
                                                                
                                                                $Column	->	setFetchedField(	$paramsArray[$Column	->	getName()]);
                                                                
                                                            }
                                                    }
                                            }    
                                    
                                        return true;
                                        }
                                    
                                        else{
                                            try{
					
						throw	new	Exception("Unexpected Error : Requested operation couldn't be completed");
					
                                            }
					catch(Exception	$e){
					
                                                $this->setAdapterException($e->getMessage());
                                                
                                                $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
						return false;
					}
                                   }
				}

				else{
				
					try{
					
						throw	new	Exception("Unexpected Error : Expecting ".'"select" type query, instead "'.$SQLQuery	->	getQueryType().'" type query provided');
					
					}
					catch(Exception	$e){
					
						$this->setAdapterException($e	->	getMessage());
                                                
                                                $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
						return false;
					}
				}
				
			}	
				
			else{
			
				try{
					
					throw	new	Exception("Unexpected Error : Expecting ".'"select" type query, instead "'.$SQLQuery	->	getQueryType().'" type query provided');
				}

				catch(Exception	$e1){
				
					$this->setAdapterException($e1	->	getMessage());
                                           
                                        return false;
				}
		
			}
		}	
	}

	public	function	update(	SQL	$SQLQuery	){
	
		$_conn	=	$this	->	getConnection();
		
		if	(isset($_conn)){
		
			if(	strcmp($SQLQuery	->	getQueryType(),"update")	==	0){
			
				/*
				**	Prepare the query
				*/
			
				if($_stmt	=	$_conn	->	prepare(	$SQLQuery	->	getSQLQuery())){
				
					/*	Bind Params
					**
					*/
				
					$paramsArray	=	array();
					
					foreach(	$SQLQuery	->	getEntity()	->	getColumns()	as	$Column){
					
						$colDatatype	=	$Column	->	getDatatype();
						
							switch($colDatatype){
							
								case	"integer":	$type	=	"i";
									
									break;
								
								case	"varchar" :	$type	=	"s";
								
									break;
									
								case	"boolean":	$type	=	"b";
								
									break;

							}

							
					$paramType	.=	$type;
					
					}
				
					$paramsArray[]	=	&$paramType;
				
					foreach	(	$SQLQuery	->	getEntity()	->	getColumns()	as	$Column	){
					
						$colName	=	$Column	->	getName();				
						
						$paramsArray[$colName]	=	&$colName;

					}
				
					/*
					**	Suppress warning and call $stmt::bind_param using call_user_func_array
					*/
					
					@call_user_func_array(array($_stmt,'bind_param'),	$paramsArray);
					
				if($_stmt	->	execute()){
                                    
                                        $this	->	setAffectedRows(	$_conn	->	affected_rows	);
                                        
                                        return true;
                                        }
                                    
                                        else{
                                            try{
					
						throw	new	Exception("Unexpected Error : Requested operation couldn't be completed");
					
                                            }
					catch(Exception	$e){
					
                                                $this->setAdapterException($e->getMessage());
                                                
                                                $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
                                                $this->getSQLError();
                                                
						return false;
					}
                                   }					
				}
				
				else{
				
					try{
					
						throw	new	Exception("Unexpected Error : Expecting ".'"update" type query, instead "'.$SQLQuery	->	getQueryType().'" type query provided');
					}
					catch(Exception	$e){
					
						$this->setAdapterException($e	->	getMessage());
                                                
                                                $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
						return false;
					}
				}
			}

			else{
				
					try{
					
						throw	new	Exception("Unexpected Error : Expecting ".'"update" type query, instead "'.$SQLQuery	->	getQueryType().'" type query provided');
					
					}
					catch(Exception	$e1){
					
						$this->setAdapterException($e1	->	getMessage());
                                           
                                                return false;
					}
				}
			
		}
	}

	public	function	delete(	SQL	$SQLQuery	){
	
		$_conn	=	$this	->	getConnection();
	
		if	(isset($_conn)){    // If connection to the database is available
		
			if(	strcmp($SQLQuery	->	getQueryType(),"delete")	==	0){
				
				/*
				**	Prepare the query
				*/
					
				if($_stmt	=	$_conn	->	prepare(	$SQLQuery	->	getSQLQuery())){	
					
					/*	Bind Params
					**
					*/
					
					
					$paramsArray	=	array();
				
					foreach(	$SQLQuery	->	getEntity()	->	getColumns()	as	$Column){
					
						$colDatatype	=	$Column	->	getDatatype();
						
							switch($colDatatype){
							
								case	"integer":	$type	=	"i";
									
									break;
								
								case	"varchar" :	$type	=	"s";
								
									break;
									
								case	"boolean":	$type	=	"b";
								
									break;

							}

							
						$paramType	.=	$type;
						
					}
				
					$paramsArray[]	=	&$paramType;
					
					foreach	(	$SQLQuery	->	getEntity()	->	getColumns()	as	$Column	){
					
						$colName	=	$Column	->	getName();				
						
						$paramsArray[$colName]	=	&$colName;

					}
					
					/*
					**	Suppress warning and call $stmt::bind_param using call_user_func_array
					*/
				
					@call_user_func_array(array($_stmt,'bind_param'),	$paramsArray);
					
					if($_stmt	->	execute()){
                                    
                                        $this	->	setAffectedRows(	$_conn	->	affected_rows	);
                                        
                                        return true;
                                        }
                                    
                                        else{
                                            try{
					
						throw	new	Exception("Unexpected Error : Requested operation couldn't be completed");
					
                                            }
					catch(Exception	$e){
					
                                                $this->setAdapterException($e->getMessage());
                                                
                                                $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
                                                $this->getSQLError();
                                                
						return false;
					}
                                   }
			
				}
				
				else{
				
					try{
					
						throw	new	Exception("Unexpected Error : Expecting ".'"delete" type query, instead "'.$SQLQuery	->	getQueryType().'" type query provided');
					
					}
					catch(Exception	$e){
					
						$this->setAdapterException($e	->	getMessage());
                                                
                                                $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
						return false;
					}
				}
				
			}
			
			else{
				
					try{
					
						throw	new	Exception("Unexpected Error : Expecting ".'"create table" type query, instead "'.$SQLQuery	->	getQueryType().'" type query provided');
					
					}
					catch(Exception	$e1){
					
						$this->setAdapterException($e1	->	getMessage());
                                           
                                                return false;
					}
			}
		}
	}
        
        // Data Definition Language Commands
        
        public function createTable(SQL $SQLQuery){
            
            $_conn	=	$this	->	getConnection();
            
            if(isset($_conn)){   // If connection to the database is available
                
                if(	strcmp($SQLQuery	->	getQueryType(),"create table")	==	0){
                
                    /*
		    **	Prepare the query
		    */
		
				if($_stmt	=	$_conn	->	prepare(	$SQLQuery	->	getSQLQuery())){
                                    
                                  if($_stmt	->	execute()){
                                    
                                        $this	->	setAffectedRows(	$_conn	->	affected_rows	);
                                        
                                        return true;
                                    }
                                    
                                  else{
                                        try{
					
						throw	new	Exception("Unexpected Error : Requested operation couldn't be completed");
					
					}
					catch(Exception	$e){
					
                                                $this->setAdapterException($e->getMessage());
                                                
                                                $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
						return false;
					}
                                    }
                                    
                                }
                                else{
				
					try{
					
						throw	new	Exception("Unexpected Error : Requested action couldn't be completed");
					
					}
					catch(Exception	$e){
					
						$this->setAdapterException($e	->	getMessage());
                                                
                                                $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
						return false;
					}
				}
                                    
                }
                else{
				
					try{
					
						throw	new	Exception("Unexpected Error : Expecting ".'"create table" type query, instead "'.$SQLQuery	->	getQueryType().'" type query provided');
					
					}
					catch(Exception	$e1){
					
						$this->setAdapterException($e1	->	getMessage());
                                           
                                                return false;
					}
				}
            }
        }
        
        public function dropTable(SQL $SQLQuery){
            
            $_conn	=	$this	->	getConnection();
            
            if(isset($_conn)){   // If connection to the database is available
                
                if(	strcmp($SQLQuery	->	getQueryType(),"drop table")	==	0){
                
                    /*
		    **	Prepare the query
		    */
		
                    if($_stmt	=	$_conn	->	prepare(	$SQLQuery	->	getSQLQuery())){
                                    
                                    
                    if($_stmt	->	execute()){
                                    
                    $this	->	setAffectedRows(	$_conn	->	affected_rows	);
                                        
                    return true;
                }
                                    
                else{
                    try{
					
                        throw	new	Exception("Unexpected Error : Requested operation couldn't be completed");
					
                    }
                    catch(Exception	$e){
					
                        $this->setAdapterException($e->getMessage());
                                                
                        $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
                        $this->getSQLError();
                                                
                        return false;
                    }
                }                                   
            }
            else{
				
                    try{
					
                        throw	new	Exception("Unexpected Error : Query couldn't be executed");
					
                    }
                    catch(Exception	$e){

                        $this->setAdapterException($e	->	getMessage());
                        
                        $this->setSQLError("SQL Error : ". $_conn -> error);

                        return false;
                    }
                }
                                    
            }
                else{
				
                    try{
					
                        throw	new	Exception("Unexpected Error : Expecting ".'"drop table" type query, instead "'.$SQLQuery	->	getQueryType().'" type query provided');
					
                    }
                    catch(Exception	$e1){

                        $this->setAdapterException($e1	->	getMessage());

                        return false;
                        
                    }
		}
            }
        }
        
public function readEntity(SQL $SQLQuery){
            
            $_conn	=	$this	->	getConnection();
            
            $colname_array = array();
            
            if(isset($_conn)){   // If connection to the database is available
                
                if(	strcmp($SQLQuery	->	getQueryType(),"read table")	==	0){
                
                    /*
		    **	Prepare the query
		    */
		
                    if($_stmt	=	$_conn	->	prepare(	$SQLQuery	->	getSQLQuery())){  
                                    
                                    
                    	if($_stmt	->	execute()){
                            
                            $_stmt->bind_result($colname,$coltype,$colnull,$colkey,$coldefault,$colextra);
                                    
                            
		            $this	->	setAffectedRows(	$_conn	->	affected_rows	);
                
			   while(	$_stmt	->	fetch()){
                        
                               array_push($colname_array, $colname);
                   	    }
    
                            $this ->_schema_def = $colname_array;
                            
                            return true;
                       
                        }
                                    
                   else{
                   
                       try{
					
                        throw	new	Exception("Unexpected Error : Requested operation couldn't be completed");
					
                       }
                       catch(Exception	$e){
					
                        $this->setAdapterException($e->getMessage());
                                                
                        $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
                        return false;
                       }
                }                                   
            }
            else{
				
                    try{
					
                        throw	new	Exception("Unexpected Error : Query couldn't be executed");
					
                    }
                    catch(Exception	$e){

                        $this->setAdapterException($e	->	getMessage());
                        
                        $this->setSQLError("SQL Error : ". $_conn -> error);

                        return false;
                    }
                }
                                    
            }
                else{
				
                    try{
					
                        throw	new	Exception("Unexpected Error : Expecting ".'"read table" type query, instead "'.$SQLQuery	->	
getQueryType().'" type query provided');
					
                    }
                    catch(Exception	$e1){

                        $this->setAdapterException($e1	->	getMessage());

                        return false;
                        
                    }
		}
            }
        }
        
        public function alterTable(SQL $SQLQuery){
            
            $_conn	=	$this	->	getConnection();
            
            if(isset($_conn)){   // If connection to the database is available
                
                if(	strcmp($SQLQuery	->	getQueryType(),"alter table")	==	0){
                
                    /*
		    **	Prepare the query
		    */
		
                    if($_stmt	=	$_conn	->	prepare(	$SQLQuery	->	getSQLQuery())){
                        
                        if($_stmt	->	execute()){
                                    
                            $this	->	setAffectedRows(	$_conn	->	affected_rows	);
                                        
                            return true;
                        
                        }
                                    
                        else{
                    
                            try{
					
                                throw	new	Exception("Unexpected Error : Requested operation couldn't be completed");
					
                            }
                            catch(Exception	$e) {
					
                                $this->setAdapterException($e->getMessage());
                                                
                                $this->setSQLError("SQL Error : ". $_conn -> error);
                                                
                                $this->getSQLError();
                                                
                                return false;
                           }
                        }                                   
                    }
                
                    else{
				
                        try{
					
                            throw	new	Exception("Unexpected Error : Query couldn't be executed");
					
                        }
                        catch(Exception	$e){

                            $this->setAdapterException($e	->	getMessage());
                        
                            $this->setSQLError("SQL Error : ". $_conn -> error);

                            return false;
                        }
                    }
                                    
                }
                else{
				
                    try{
					
                        throw	new	Exception("Unexpected Error : Expecting ".'"alter table" type query, instead "'.$SQLQuery	->	
getQueryType().'" type query provided');
					
                    }
                    catch(Exception	$e1){

                        $this->setAdapterException($e1	->	getMessage());

                        return false;
                        
                    }
		}
            }
        }
       
}

?>