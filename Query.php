<?php
	//This file is called whenever an SQL query is required, returning it in JSON format.
	// For the most part, it can be treated as a black box when attempting to get data from the database within the other files.
	$sql = $_REQUEST['sql']; //Gets SQL query from caller form.
	$host='35.204.50.1'; //IP of database.
	$username = "root";
	$password = "";
	try
	{
		$con = new PDO("mysql:host=$host;dbname=team12database;charset=utf8mb4",$username,$password); //Creates connection to the mySQL database via PDO.
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$statement = $con->prepare($sql); //Formats the SQL statement.
		$statement->execute(); //Runs the SQL statement.
		$results = $statement->fetchAll(PDO::FETCH_ASSOC); //Gets results from table.
		echo (json_encode($results)); //Returns the results encoded in JSON (so they can be understood by the JS on the parent form).
		$con = null; //Closes connection.
	}
	catch(PDOException $e)
	{
		echo ("Connection failed".$e->getMessage()); //If cannot connect, display error message.
	}
?>