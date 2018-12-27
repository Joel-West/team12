<?php
	$sql = $_REQUEST['sql'];
	$host='35.204.50.1';
	$username = "root";
	$password = "";
	//echo json_encode("Ayyyy1");
	
	try 
	{
		$con = new PDO("mysql:host=$host;dbname=team12database;charset=utf8mb4",$username,$password);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$stmt = $con->prepare($sql);
		echo json_encode($stmt);
		$stmt->execute();
		echo json_encode($stmt);
		$res = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		$con = null;
		//echo json_encode("Ayyyy2");
		

	}
	catch(PDOException $e)
	{
		echo json_encode("Connection failed".$e->getMessage());
	}
	//echo json_encode($stmt);
?>