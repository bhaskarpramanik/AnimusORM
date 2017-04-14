<?php

/**
*	Database Adapter Interface
*/

interface	DatabaseAdapter{


	function	connect();

	function	disconnect();

	function	select(	SQL	$SQLQuery	);

	function	insert(	SQL	$SQLQuery	);

	function	update(	SQL	$SQLQuery	);

	function	delete(	SQL	$SQLQuery	);
	
	function	getAffectedRows();

}

?>