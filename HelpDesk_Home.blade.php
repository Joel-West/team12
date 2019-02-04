<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_Home</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple other files -->
		<script type="text/javascript">
			var userData; //Variable containing data about user.
			var currentPage = "Home";
			function Load()
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				SetPrivileges(userData) //Enter function that defines what functions are available to user based on status.
				//Fun();
				WriteTime(); //Function that writes the current time at the top of the page.
			}
			
			function SetPrivileges(userData) //Function that checks if user is an admin or analyst and adjusts available buttons accordingly.
			{
				admin = (userData.split(","))[2]; //Retrieves admin/analyst status from userData that was earlier posted from previous form.
				analyst = (userData.split(","))[3];
				if (admin == 0 && analyst == 0)
				{
					document.getElementById("btnAnalytics").disabled = true;
				}
				else if (admin == 0 && analyst == 1)
				{
					document.getElementById("btnNewCall").disabled = true;
				}
			}
		</script>
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
		<!-- CSS that defines appearance and placement of main buttons.-->
		<style type="text/css">
			.mainButton
			{
				font-size:40px;
				font-weight:bold;
				padding: 12px 25px;
				width: 80%;
				margin: auto;
			}
			.subButton
			{
				font-size:32px;
				font-weight:bold;
				padding: 12px 25px;
				width: 40%;
				margin: auto;
			}
			.grid-class
			{
				position: relative;
				display: grid;
				grid-template-columns: auto;
			}
		</style>
	</head>
	<body onload="Load()">
	<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
		@csrf <!--Token to validates requests to server. -->
		<input type='text' hidden id="user" name="User"/> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
		<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
			<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('');" /> <!-- Back button. -->
			<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label> <!-- Label to contain current data/time. -->
			<h2 id="header" style="style=display:inline-block; font-size:36px;">Welcome, <?php echo (explode(",", $_POST['User']))[0]; ?>!</h2> <!-- Heading containing a welcome message to the user. -->
		</div>	
		<div class="grid-class"> <!-- Div containing main buttons that link to other pages. -->
			<input class="mainButton" type="button" id="btnNewCall" value="New Call" onClick="GoToNewPage('NewCaller')" />
			<input class="mainButton" type="button" value="View Call History" onClick="GoToNewPage('CallHistory');" />
			<input class="mainButton" type="button" value="View Problems List" onClick="GoToNewPage('ProblemList');" />
			<input class="mainButton" type="button" value="View Personnel" onClick="GoToNewPage('PersonnelList');" />
			<input class="mainButton" type="button" value="View Equipment" onClick="GoToNewPage('EquipmentList');" />
			<input class="mainButton" type="button" id="btnAnalytics" value="Analytics" onClick="GoToNewPage('Analytics')" />
		</div>
		<input class="subButton" type="button" value="Users" onClick="GoToNewPage('UserList');" />
		<input class="subButton" type="button" value="Specialisations" onClick="GoToNewPage('SpecialisationList');" />
		<br>
		<!--Fun mode (don't click if you have epilepsy...): <input id="checkFun" type="checkbox" onclick="Fun()"/>-->
	</form>
	</body>
</html>