<?php
	/*require_once 'MDB2.php';
	//include "coa123-mysql-connect.php"; //Includes file containing username and password for database.


	$dsn = "mysql://root:@$host/$dbName";
	$db =& MDB2::connect($dsn); //Accesses database.
	if(PEAR::isError($db)){ 
		die($db->getMessage()); //Returns error if not able to access database.
	}
	$db->setFetchMode(MDB2_FETCHMODE_ASSOC);*/
	//$sql = "SELECT name, weekend_price, weekday_price, licensed, venue_id FROM venue WHERE capacity >= $size AND venue_id NOT IN (SELECT DISTINCT venue_id FROM venue_booking WHERE venue_booking.date_booked = '$fDate') AND venue_id IN (SELECT venue_id FROM catering WHERE grade = $grade)";
	//Forms SQL query that gets most details of all venues that are not booked for the selected date, have a greater capacity than the party size and provide catering of the specified grade.
	/*$res =& $db->query($sql);
	if(PEAR::isError($res)){
		die($res->getMessage());
	}
	$value = json_encode($res->fetchAll());
	echo $value; //Returns value of json array back to the form for display.*/
	echo json_encode("Ayyyy1");
	$sql = $_REQUEST['sql'];
	$host='35.204.50.1';
	$username = "root";
	$password = "";
	try 
	{
		echo json_encode("Ayyyy2");
		$con = new PDO("mysql:post=$host;dbname=team12database",$username,$password);
		//$stmt = $con->prepare($sql);
		//$stmt->execute();
		//$res = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	}
	catch(PDOException $e)
	{
		echo json_encode("Connection failed".$e->getMessage());
	}
	echo json_encode($res);
?>