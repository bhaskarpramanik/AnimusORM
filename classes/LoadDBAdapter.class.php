<?php

/**	Database Adapter Loader Class
*
*/

define	('DOMROOT',	dirname	(	dirname	(dirname	(__FILE__))));

class	LoadDBAdapter{

	private	$_adapterName;
	
	private	$_adapterClass;
	
	private	$_XMLEntity;

	private	$_siteroot;

	public function	__construct(){

		//define	('SITEROOT',	(dirname	(dirname	(dirname	(__FILE__)))));

		$this	->	setSiteRoot(	DOMROOT	);

		$this	->	setXMLEntity(	new SimpleXMLElement	(	file_get_contents	($this	->	getSiteRoot().'/config/DBAdapters.xml')));

		$this	->	resolveDBAdapter();
	
	}

	
	public	function	resolveDBAdapter(){

		$xmlentity	=	$this	->	getXMLEntity();

		/**
		*	Find the name of the adapter for which usage parameter is true
		*/
		
		foreach	(	$xmlentity	->	children()	->	{'database-adapter'}	as	$adapter	){
			
			if	($this	->	_toBool($adapter	->	{'adapter-usage'})	)	

				$adapterName	=	$adapter	->	{'adapter-name'};
	
		break;
			
		}

		/**
		*	Find out the class file name of the adapter
		*/
		
		
		foreach	(	$xmlentity	->	children()	->	{'database-adapter-mapping'}	as	$adapterMapping	){
			
			if	(	strcmp(	$adapterMapping	->	{'adapter-name'},	$adapterName)	==	0)

				$adapterClass	=	$adapterMapping	->	{'adapter-class'};
	

		break;
			
		}

			$this	->	setAdapterName(	(String)$adapterName->{0}	);
			
			$this	->	setAdapterClass(	(String)$adapterClass	);
	}
	
	public	function	_toBool($_adapterUsage){
	
		switch	(strtolower($_adapterUsage)){
			
			case	'true':	return	true;
			
			case	'false':return	false;
		
		}
	
	}

	public	function	setXMLEntity(SimpleXmlElement	$_xmlentity){

		$this	->	_XMLEntity	=	$_xmlentity;

	}

	public	function	setSiteRoot($SITEROOT){

		$this	->	_siteroot	=	$SITEROOT;

	}
	
		public	function	setAdapterName(	$adapterName	){
	
		$this	->	_adapterName	=	$adapterName;
	
	}
	
	public	function	setAdapterClass(	$adapterClass	){
	
		$this	->	_adapterClass	=	$adapterClass;
		
	}
	
	public	function	getXMLEntity(){

		return	$this	->	_XMLEntity;
	}

	public	function	getSiteRoot(){

		return	$this	->	_siteroot;

	}
	
	public	function	getAdapterName(){
	
		return	$this	->	_adapterName;
	}
	
	public	function	getAdapterClass(){
	
		return	$this	->	_adapterClass;
		
	}
}

?>