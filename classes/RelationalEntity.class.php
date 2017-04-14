<?php

/**
*	Relational Entity - The representation of Table entity in DBMS
*/

require_once	'Column.class.php';

class	RelationalEntity{

	private	$_name;
	
	private	$_constraint;
	
	private	$_columns = array();

        private $_engine;
	
	/**
	*	Creating columns and pushing to the array
	*/
	
	public	function	__construct($_name){
	
		$this	->	setName(	$_name	);
	
	}
	
	public	function	addRecordField($_colname, $_defaultvalue, $_datatype, $_length){
	
		$_column	=	new	Column($_colname);
	
		$_column	->	setDataType($_datatype);
                
                $_column        ->      setDefaultValue($_defaultvalue);
		
		$_column	->	setLength($_length);
	
		$this	->	setRecordField($_column);

	}
	
	public	function	bindValue($_name,	$_value	){
		
			$this	->	getColumnByName	($_name)	->	setBoundValue	(	$_value	);
			
	}
	
	public	function	setRecordField(	Column	$_column){
	
		$this	->	_columns[$_column	->	getName()]	=	$_column;
	
	}
	
	public	function	setName(	$_name	){
	
		$this	->	_name	=	$_name	;	
	}
	
	public	function	setConstraint($_constraint){
		
		if($this->getColumnByName($_constraint) instanceof Column){

		 $column = $this->getColumnByName($_constraint);
		 $column->ConstraintTo($this->getName());
                 $column->setDiscriminator();
		
		}
          
		else{
                    try{
                        
                        throw new Exception("Unexpected Exception : Constraint field doesn't exist");
                    }

                    catch(Exception $e){
                        echo $e->getMessage();
                        exit();
                    }
			
                }
	}
        
        public function        setEngine($_engine){
            
            $this->_engine = $_engine;
        }
        
        
	public	function	getName(){
	
		return	$this	->_name;
	
	}
	
	public	function	getColumns(){
	
		return	$this->_columns;
	
	}


        public	function getColumnByName($_name){
	
		return	$this->_columns[$_name];
	
	}
	
	public	function	getColumnCount(){
	
		return	count($this	->	getColumns());
	
	}
	
	public	function	getConstraint(){
		
		return	$this	->	_constraint;
	
	}
        
        public function         getEngine(){
            
            return $this->_engine;
        }
        
        public function         reset(){
            
            foreach($this -> _columns as $_column){
 
                    $_column -> flush();

            }
            
        }

}
?>