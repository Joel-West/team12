<?php
	echo json_encode("Ayyyy1");
	$sql = $_REQUEST['sql'];
	$host='35.204.50.1';
	$username = "root";
	$password = "";
	try 
	{
		$con = new PDO("mysql:host=$host;dbname=team12database;charset=utf8mb4",$username,$password);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		echo json_encode("Ayyyy2");
		/*
		$stmt = $con->prepare($sql);
		$stmt->execute();
		$res = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		*/
	}
	catch(PDOException $e)
	{
		echo json_encode("Connection failed".$e->getMessage());
	}
	//echo json_encode($r);
?>