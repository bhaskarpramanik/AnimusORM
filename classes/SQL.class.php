<?php

	/**
	*	SQL class
	*/

	require_once	'RelationalEntity.class.php';
	
	class	SQL{

		private	$_query;
		
		private	$_entity;
		
		private	$_queryId;
		
		private	$_queryType;
		
		public	function	__construct(	RelationalEntity	$Request	){
		
			$this	->	setEntity(	$Request	);
		
		}
                
                
                /*
                 **    Data Manipulation Language Queries
                 */
                
		
		/*
		**	Function to generate SELECT type queries
		*/
	
		public	function	rowSelect(	$discriminator	){

			$Request	=	$this	->	getEntity();
			
			$_entityName	=	$Request	->	getName();
			
			$_fields		=	$Request	->	getColumns();
			
			/*
			**	Setting the constraint field
			*/
			
			if	($Request	->	getColumnByName($discriminator)	->	getBoundValue()){
				
				$_queryConstraintField	=	$discriminator;
				
				$_queryConstraintValue	=	$Request	->	getColumnByName(	$_queryConstraintField	)	->	getBoundValue();
			
				foreach	($_fields	as	$field){
                                    
                                        $field -> toFetch();
					
					$fields	.=	$field	->	getName().',';
				}	
				
				$fields	=	rtrim($fields,',');
				
		/*
		**	Generate the query string
		*/
		
		
			$_query	=	"SELECT ".$fields." FROM ".$_entityName." WHERE ".$_queryConstraintField ." = \"".$_queryConstraintValue."\"";

		/*
		**	Set the query string
		*/
	
		$this	->	setSQLQuery($_query);
	
	
		/*
		**	Set the query type
		*/
		
		$this	->	setQueryType("select");
	
		}
			
			else{
				
				try{
					
					throw	new	Exception	("UnexpectedError : Discriminator column for".$this	->	getQueryType()." cannot be empty");
				
				}
				catch(Exception	$e){
				
					echo	$e	->	getMessage();
				
				}
			}
	
	
		}
                
                public	function	select(	$fieldArray , $discriminator ){

			$Request	=	$this	->	getEntity();
			
			$_entityName	=	$Request	->	getName();
                        
                        $_fields        =       $fieldArray;
			
			/*
			**	Setting the constraint field
			*/
			
			if	($Request	->	getColumnByName($discriminator)	->	getBoundValue()){
				
				$_queryConstraintField	=	$discriminator;
				
				$_queryConstraintValue	=	$Request	->	getColumnByName(	$_queryConstraintField	)	->	getBoundValue();
			
				foreach	($_fields	as	$field){
                                    
                                        if($Request -> getColumnByName($field) -> isFetchable())
					
					$fields	.=	$field.",";
				}	
				
				$fields	=	rtrim($fields,',');
				
		/*
		**	Generate the query string
		*/
		
		
			$_query	=	"SELECT ".$fields." FROM ".$_entityName." WHERE ".$_queryConstraintField ." = \"".$_queryConstraintValue."\"";

		/*
		**	Set the query string
		*/
	
		$this	->	setSQLQuery($_query);
	
	
		/*
		**	Set the query type
		*/
		
		$this	->	setQueryType("select");
	
		}
			
			else{
				
				try{
					
					throw	new	Exception	("UnexpectedError : Discriminator column for".$this	->	getQueryType()." cannot be empty");
				
				}
				catch(Exception	$e){
				
					echo	$e	->	getMessage();
				
				}
			}
	
	
		}


		/*
		**	Function to generate INSERT type queries
		*/
		
		public	function	insert(){

			$Request	=	$this	->	getEntity();
			
			$_entityName	=	$Request	->	getName();
			
			$_fields	=	$Request	->	getColumns();

			foreach	($_fields	as	$field){

				
				$fields	.=	" ".$field	->	getName().',';

				$values	.=	" \"".$field	->	getBoundValue()."\"".',';
			}	
			
			$fields	=	rtrim($fields,',');

			$values	=	rtrim($values,',');

		/*
		**	Generate the query string
		*/

		$_query	=	"INSERT INTO ".$_entityName." (".$fields.") VALUES (".$values.")";

		/*
		**	Set the query string
		*/

		$this	->	setSQLQuery(	$_query	);
		
		/*
		**	Set the query type
		*/
		
		$this	->	setQueryType(	"insert" );

		}
		
		public	function	updateQuery( $targetField,	$discriminatorField){
		
			$Request	=	$this	->	getEntity();
			
			$_entityName	=	$Request	->	getName();
                        
                        $target    =       $Request    ->  getColumnByName($targetField);
                        
                        $discriminator  =   $Request    ->  getColumnByName($discriminatorField);
                        

			/*
			**	Building the query string
			*/
			
			if	($discriminator	->	getBoundValue()){
			
				$_query	=	"UPDATE ".$_entityName." SET ".$target	->	getName()." = \"".$target	->	getBoundValue()."\" WHERE ".$discriminator	->	getName()." = \"".$discriminator	->	getBoundValue()."\"";
				
				$this	->	setSQLQuery(	$_query	);
				
				$this	->	setQueryType(	"update");
			}
			else{
				
				try{
					
					
					throw	new	Exception	("UnexpectedError : Discriminator column for".$this	->	getQueryType()." cannot be empty");
				
				}
				catch(Exception	$e){
				
					echo	$e	->	getMessage();
				
				}
			}
		}
                
                public	function	batchUpdate( $targetFieldArray,	$discriminatorField	){
                    
                    
                        $target = array();
		
			$Request	=	$this	->	getEntity();
			
			$_entityName	=	$Request	->	getName();
                        
                        foreach($targetFieldArray as $targetField)
                        
                            $target[$targetField]    =       $Request    ->  getColumnByName($targetField);
                            
                        foreach($target as $field){
                            
                            $updateString .= $field->getName()." = \"".$field->getBoundValue()."\","; 
                            
                        }
                        
                        $updateString = rtrim($updateString,",");
                        
                        
                        $discriminator  =   $Request    ->  getColumnByName($discriminatorField);
                        

			/*
			**	Building the query string
			*/
			
			if	($discriminator	->	getBoundValue()){
			
				$_query	=	"UPDATE ".$_entityName." SET ".$updateString." WHERE ".$discriminator	->	getName()." = \"".$discriminator	->	getBoundValue()."\"";
				
				$this	->	setSQLQuery(	$_query	);
				
				$this	->	setQueryType(	"update"	);
			}
			else{
				
				try{
					
					
					throw	new	Exception	("UnexpectedError : Discriminator column for".$this	->	getQueryType()." cannot be empty");
				
				}
				catch(Exception	$e){
				
					echo	$e	->	getMessage();
				
				}
			}
		}
		
		public	function	delete(	Column	$discriminator	){
		
			$Request	=	$this	->	getEntity();
			
			$_entityName	=	$Request	->	getName();

			/*
			**	Building the query string
			*/
			
			$_query	=	"DELETE FROM ".$_entityName." WHERE ".	$discriminator	->	getName()." = ". $discriminator	->	getBoundValue();
		
			$this	->	setSQLQuery(	$_query	);
			
			$this	->	setQueryType(	"delete"	);
			
		}
                
                
                /*
                 **     Data Definition Language Queries
                 */
                
                public	function	createTable(){
		
			$Request	=	$this	->	getEntity();
			
			$_entityName	=	$Request	->	getName();
			
                        $_columns = $Request -> getColumns();
                        
                        foreach($_columns as $_column){
                            
                            $_column_name = $_column -> getName();
                            $_column_datatype = $_column -> getDatatype();
                            $_column_length = $_column -> getLength();
                            $_column_default_value = $_column -> getDefaultValue();
                            
                            $string .= $_column_name." ".$_column_datatype."(".$_column_length.")" .$_column_default_value.",";
                            
                            if($_column->isDiscriminator()) $p_key = $_column_name;   
                        }

			/*
			**	Building the query string
			*/                        
                        
                       $_query = "CREATE TABLE "." ".$_entityName." (".$string." PRIMARY KEY( ".$p_key." )) ENGINE = ".$Request->getEngine();
		
			$this	->	setSQLQuery($_query);
			
			$this	->	setQueryType("create table");
			
		}
                
                 public	function	dropTable(){
		
			$Request	=	$this	->	getEntity();
			
			$_entityName	=	$Request	->	getName();
                        
                        $_query = "DROP TABLE"." ".$_entityName;
                        
                        $this	->	setSQLQuery($_query);
			
			$this	->	setQueryType("drop table");
                        
                 }
                 
                public function	readEntity(){

                    $Request	=	$this	->	getEntity();

                    $_entityName	=	$Request	->	getName();

                    $_query = "SHOW COLUMNS FROM ".$_entityName;

                    $this	->	setSQLQuery($_query);

                    $this	->	setQueryType("read table");

                }
                
                public  function        alterTable($alterType, $Field){
                    
                    $Request = $this->getEntity();
                    
                    $_entityName	=	$Request	->	getName();
                    
                    switch($alterType){
                        
                        case "drop" : {
                            
                            $operation_on_field = "DROP";
                         
                              $_query = "ALTER TABLE ".$_entityName." ".$operation_on_field." ".$Field;
                            
                            break;
                        
                        }
                        case "add" : {
                            
                             $operation_on_field = "ADD";
                                 
                                $_column_name = $Field -> getName();
                                $_column_datatype = $Field -> getDatatype();
                                $_column_length = $Field -> getLength();
                                $_column_default_value = $Field -> getDefaultValue();
                             
                                $string = $_column_name." ".$_column_datatype."(".$_column_length.")" .$_column_default_value;
                            
                                $_query = "ALTER TABLE ".$_entityName." ".$operation_on_field." ".$string;

                            
                            break;
                        
                        }
                    }
                    
                    $this	->	setSQLQuery($_query);

                    $this	->	setQueryType("alter table");
                    
                    return true;
                    
                }
		
		public	function	setEntity(	RelationalEntity	$Request){
		
			$this	->	_entity	=	$Request;
		
		}
		
		public	function	getEntity(){
		
			return	$this	->	_entity;
		
		}

		public	function	setSQLQuery(	$_query	){

			$this	->	_query	=	$_query;
			
			$this	->	setQueryId();

		}

		public	function	getSQLQuery(){

			return	$this	->	_query;
		}
		
		public	function	setQueryId(){
		
			$this	->	_queryId	=	hash(	"sha1",	$this	->	getSQLQuery()	);
		
		}
		
		public	function	getQueryId(){
		
			return	$this	->	_queryId;
		
		}
		
		public	function	setQueryType(	$_queryType	){
		
			$this	->	_queryType	=	$_queryType;
		
		}
		
		public	function	getQueryType(){
		
			return	$this	->	_queryType;
		
		}
	}

?>