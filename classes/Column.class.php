<?php

/*
**	This is the column class
*/

	class	Column{

		private	$_name;

		private	$_datatype;

		private	$_length;

		private	$_boundvalue;

		private	$_constraintTo;
		
		private	$_discriminator	=	false;

		private	$_fetched	=	array();
                
                private $_def_val;
                
                private $_to_fetch = false;
		
		public	function	__construct(	$_name	){
				
			$this	->	setName($_name);
			

		}
		
		public	function	setName	(	$_name	){
		
			$this	->	_name	=	$_name;
		
		}

		public	function	setDatatype(	$_datatype	){

			$this	->	_datatype	=	$_datatype;

		}

		public	function	setLength(	$_length	){

			$this	->	_length	=	$_length;

		}

		public	function	setBoundValue(	$_boundvalue	){

			$this	->	_boundvalue	=	$_boundvalue;

		}
                
                public  function        setDefaultValue($_value){
                    
                        $this   ->_def_val = $_value;
                }

		public	function	ConstraintTo(	$_entityName	){

			$this	->	_constraintTo	=	$_entityName;

		}

		public	function	setDiscriminator(){

			$this	->	_discriminator	=	true;

		}

		public 	function	setFetchedField(	$_fetched	){


			array_push($this	->	_fetched,	$_fetched);


		}
                
                public function         toFetch(){
                    
                        $this->_to_fetch = true;
                    
                }
                
                public function         isFetchable(){
                    
                    return  $this->_to_fetch;
                    
                }
                
                public function         flush(){
                    
                    $this -> _to_fetch = false;
                    
                    $this -> _fetched = array();
                    
                }

                public	function	getName(){
		
			return	$this	->	_name;

		}
		
		public	function	getBoundValue(){
		
			return	$this	->	_boundvalue;
		
		}

		public	function	getFetchedField(){


			return	$this	->	_fetched;


		}

		public	function	getDatatype(){


			return	$this	->	_datatype;

		}
                
                public  function        getDefaultValue(){
                    
                        return $this   ->_def_val;
                }
                
                public	function	getLength(){

			return $this	->	_length;

		}

		public	function	isConstraintTo(){

			return	$this	->	_constraintTo;

		}
		
		public	function	isDiscriminator(){
		
			return	$this	->	_discriminator;
		
		}
	}

?>