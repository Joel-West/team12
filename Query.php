<?php
	$sql = $_REQUEST['sql'];
	$host='35.204.50.1';
	$username = "root";
	$password = "";
	try 
	{
		$con = new PDO("mysql:host=$host;dbname=team12database",$username,$password);
		foreach($con->query('SELECT * from tblPersonnel') as $row) {
			print_r($row);
		}
		$con = null;
	}catch(PDOException $e){
		print "Connection failed".$e->getMessage();
	}	
?>
