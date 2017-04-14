<?php
class ConfigBean{
	
	private $DB_DSN;
	private $DB_USER;
	private $DB_PASS;
	private $DB_NAME;
	private	$DB_PORT;
	private	$DB_HOST;
	
	public function setDB_DSN($DB_DSN){	// Create setter methods
		$this->DB_DSN = $DB_DSN;
	}
	public function setDB_USER($DB_USER){
		$this->DB_USER = $DB_USER;
	}
	public function setDB_PASS($DB_PASS){
		$this->DB_PASS = $DB_PASS;
	}
	public function setDB_NAME($DB_NAME){
		$this->DB_NAME = $DB_NAME;
	}
	public function	setDB_PORT($DB_PORT){
		$this->DB_PORT = $DB_PORT;
		
	}
	public function setDB_HOST($DB_HOST){
		$this->DB_HOST = $DB_HOST;
	}	
	public function getDB_DSN(){	//	Create getter methods
		return $this->DB_DSN;
	}
	public function getDB_USER(){
		return $this->DB_USER;
	}
	public function getDB_PASS(){
		return $this->DB_PASS;
	}
	public function getDB_NAME(){
		return $this->DB_NAME;
	}
	public function getDB_PORT(){
		return $this->DB_PORT;
	}
	public function getDB_Host(){
		return $this->DB_HOST;
	}
	
}	// End Class
?>