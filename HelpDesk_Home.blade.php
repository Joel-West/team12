<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_Home</title>
		<link rel="icon" href="https://www.goodfreephotos.com/albums/vector-images/screwdriver-and-wrench-vector-clipart.png">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>	<!-- Importing Bootstrap files. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple pages. -->
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
			
			function SetPrivileges(userData) //Function that checks if user is an admin, analyst or specialist and adjusts available buttons accordingly.
			{
				admin = (userData.split(","))[2]; //Retrieves statuses from userData that was earlier posted from previous form.
				analyst = (userData.split(","))[3];
				specialist = (userData.split(","))[4];
				operator = (userData.split(","))[5];
				if (admin != 1)
				{
					document.getElementById("btnUsers").disabled = true;
					if (operator != 1)
					{
						document.getElementById("btnNewCall").disabled = true;
					}
					if (analyst != 1)
					{
						document.getElementById("btnAnalytics").disabled = true;
					}
					if (specialist != 1)
					{
						document.getElementById("btnSpecialisations").disabled = true;
					}
				}
			}
		</script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"> <!-- Bootstrap CSS stylesheet. -->
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
		<!-- CSS that defines the placement of buttons in a grid.-->
		<style type="text/css">
			.grid-class
			{
				position: relative;
				display: grid;
				grid-template-columns: auto;
			}
		</style>
	</head>
	<body onload="Load()">
		<div class="container-fluid"> <!-- Container holds elements together using Bootstrap. -->
			<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
				@csrf <!--Token to validates requests to server. -->
				<input type='text' hidden id="user" name="User"/> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
				<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
					<input type="button" class="glow-button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('');" /> <!-- Back button. -->
					<label id="dtLabel" class="dtLabel"></label> <!-- Label to contain current data/time. -->
					<h2 id="header">Welcome, <?php echo (explode(",", $_POST['User']))[0]; ?>!</h2> <!-- Heading containing a welcome message to the user. -->
				</div>
				<br/>
				<div class="grid-class"> <!-- Div containing main buttons that link to other pages. -->
					<input class="button glow-button" type="button" id="btnNewCall" value="New Call" onClick="GoToNewPage('NewCaller')" />
					<input class="button glow-button" type="button" value="View Call History" onClick="GoToNewPage('CallHistory');" />
					<input class="button glow-button" type="button" value="View Problems List" onClick="GoToNewPage('ProblemList');" />
					<input class="button glow-button" type="button" value="View Personnel" onClick="GoToNewPage('PersonnelList');" />
					<input class="button glow-button" type="button" id="btnUsers" value="View Users" onClick="GoToNewPage('UserList');" />
					<input class="button glow-button" type="button" id="btnSpecialisations" value="View Specialisations" onClick="GoToNewPage('SpecialisationList');" />
					<input class="button glow-button" type="button" value="View Equipment" onClick="GoToNewPage('EquipmentList');" />
					<input class="button glow-button" type="button" value="View Problem Types" onClick="GoToNewPage('ProblemTypeList');" />
					<input class="button glow-button" type="button" id="btnAnalytics" value="Analytics" onClick="GoToNewPage('Analytics')" />			
				<br>
			</form>
		</div>
	</body>
</html>