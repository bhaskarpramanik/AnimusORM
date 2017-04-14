<?php

/**
*	ConfigReader - Reads dbsettings.ini
*/

require_once("ConfigBean.class.php");	// Require the ConfigBean.class file

define('DOMROOT',dirname(dirname(dirname(__FILE__))));

class ConfigReader{

	private static $ConfigBeanContext;
	
	public function __construct(){
			
			$data = file(DOMROOT."/config/dbsettings.ini");	// Read the dbsettings.ini file as an array
			
			foreach($data as $row){	// From the file array, remove the commented lines
			
				if (preg_match('^;^',$row)){
			
					unset($row);
			
					}	// End if
			
				else{
			
					$row = trim($row," ");	// Remove any leading or trailing spaces
			
						if(preg_match("^DB_HOST^",$row)){
			
							$db_host = explode("=",$row);
			
								foreach($db_host as $db_host) $db_host = trim($db_host);							
							}
			
						else if(preg_match("^DB_NAME^",$row)){
			
							$db_name = explode("=",$row);
			
								foreach($db_name as $db_name) $db_name = trim($db_name);
							}
			
						else if(preg_match("^DB_USER^",$row)){
			
							$db_user = explode("=",$row);
			
								foreach($db_user as $db_user) $db_user = trim($db_user);
							}
			
						else if(preg_match("^DB_PASS^",$row)){
			
							$db_pass = explode("=",$row);
			
								foreach($db_pass as $db_pass) $db_pass = trim($db_pass);
							}
						
						else if(preg_match("^DB_PORT^", $row)){

							$db_port = explode("=",$row);

								foreach($db_port as $db_port) $db_port = trim($db_port);
								
							}

						else if(preg_match("^DB_HOST^", $row)){

							$db_host = explode("=",$row);

								foreach($db_host as $db_host) $db_port = trim($db_host);
								
							}
		
							
					}	// 	End Else
			
				}	// End Foreach
		
				$DB_DSN ='mysql:host='.$db_host.';dbname='.$db_name;	// Create an object of the ConfigBean
		
				$this	->	ConfigBeanContext	=	new ConfigBean();
		
				//$this	->	ConfigBeanContext	->	setDB_DSN	(	$DB_DSN		);
		
				$this	->	ConfigBeanContext	->	setDB_USER	(	$db_user	);
		
				$this	->	ConfigBeanContext	->	setDB_PASS	(	$db_pass	);
		
				$this	->	ConfigBeanContext	->	setDB_NAME	(	$db_name	);

				$this	->	ConfigBeanContext	->	setDB_PORT	(	$db_port	);

				$this	->	ConfigBeanContext	->	setDB_HOST	(	$db_host	);
		
		}	// End Function
		
		public function getConfigBeanContext(){
		
			return $this->ConfigBeanContext;
		
		}	
}

?>