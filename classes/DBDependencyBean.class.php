<?php

/**
*	The DBDependency Bean File
*/

        require_once "ORMException.class.php";
        
	class	DBDependencyBean{

		private	$_siteroot;

		private	$_ormroot;
		
		private	$_configReader;
		
		private	$_adapterInstance;
		
		private	$_conn;

		public	function	__construct(){

		/**
		*	Return an Instance of the DBDependency Bean
		*/
		}

		public	function	setSiteRoot(	$SITEROOT	){
	
		$this	->	_siteroot	=	$SITEROOT;
	
		}
	
		public	function	setORMRoot(	$ORMROOT	){
	
		$this	->	_ormroot	=	$ORMROOT;
	
		}
		
		public	function	setConfigReader(	ConfigReader	$configReader	){
	
		$this	->	_configReader	=	$configReader;
	
		}
		
		public	function	setAdapterInstance(	MySQLDatabaseAdapter	$adapterInstance){
	
		$this	->	_adapterInstance	=	$adapterInstance;
	
		}
		
		public	function	setConn($Object){
				
                    $this	->	_conn	=	$Object;

		}
		
		public	function	getConn(){
	
		return	$this	->	_conn;
	
		}
		
		public	function	getSiteRoot(){

		return	$this	->	_siteroot;	

		}

		public	function	getORMRoot(){
	
		return	$this	->	_ormroot;
	
		}
		
		public	function	getConfigReader(){
	
		return	$this	->	_configReader;
	
		}
		
		public	function	getAdapterInstance(){
		
		return	$this	->	_adapterInstance;
	
		}
}
?>