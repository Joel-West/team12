<?php
	$sql = $_REQUEST['sql'];
	$host='35.204.50.1';
	$username = "root";
	$password = "";
	try 
	{
		$con = new PDO("mysql:host=$host;dbname=team12database;charset=utf8mb4",$username,$password);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $con->query($sql);
		echo json_encode($stmt);
		foreach ($stmt as $row)
		{
			//echo json_encode($row);
		}
		$con = null;
		

	}
	catch(PDOException $e)
	{
		echo json_encode("Connection failed".$e->getMessage());
	}
?>