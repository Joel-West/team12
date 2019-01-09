<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_LogIn</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple other files -->
		<script type="text/javascript">
			function Validate() //Function to check if username/password are valid.
			{
				var Username=document.getElementById("Username").value; //Get username and password from text boxes.
				var Password=document.getElementById("Password").value;
				sql = "SELECT tblUser.username, tblUser.password, tblUser.admin, tblPersonnel.name FROM tblUser INNER JOIN tblPersonnel WHERE tblUser.username = '" + Username + "'"; //Query retrieves password associated with input username.
				$.get("Query.php", {'sql':sql},function(json) //Calls Query.php, which handles the SQL query and sorting of result data.
				{
					valid = true;
					if (json && json[0]) //If any data has been retrieved.
					{
						if (json[0].password == Password) //If input password is valid.
						{
							document.getElementById("User").value = json[0].name + "," + json[0].admin; //Sets user data to be posted (name and admin status).
							document.getElementById("mainform").submit(); //Submit the form (moving to the home page).
						}
						else
						{
							valid = false;
						}
					}
					else
					{
						valid = false;
					}
					if (!valid) //If either username or password are irrelevant.
					{
						alert("Invalid username or password.");
					}
				},'json');
			}
			/*<<<Example for extracting data creating a database table that is displayed!>>>
			function RunQuery()
			{
				sql = "SELECT * FROM tblUser;";
				$.get("Query.php", {'sql':sql},function(json) //Calls Query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php was a json array		
					{				
						var htm = "<table><tr><td>userID</td><td>username</td><td>Password</td><td>Admin?</td>"; //Appending column headers.
						for (i = 0; i<json.length; i++) //Iterates through the json array.
						{
							col = GetRandomCol(); //Gets a random colour from RGB values.
							htm += '<tr style="background-color:rgb('+col[0]+', '+col[1]+', '+col[2]+');">'; //Assigns colour to a row.
							htm +="<td>"+json[i].userID+"</td>";
							htm +="<td>"+json[i].username+"</td>";
							htm +="<td>"+json[i].password+"</td>";		
							htm +="<td>"+json[i].admin+"</td>";
							htm += "</tr>";
						}
					}
					else
					{
						var htm = "Sorry, no results found..."; //If no results, display error.
					}
					$("#tableDiv").html(htm) //Appends HTML to the results div.
				},'json');
			}*/			
		</script>
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
	</head>
	<body>
	<form id="mainform" name="mainform" method="post" action="http://35.204.60.31/Home"> <!-- This form will post data to the home page when submitted. -->
		@csrf <!--Token to validates requests to server. -->
		<h1 class="center">Login</h1>
		<div class="center">
			<input type="text" name="Username" id="Username" placeholder="Username" value=""><br>  <!-- HTML input fields for form data. -->
			<input type="password" name="Password" id="Password" placeholder="Password" ><br>
			<input type="button" name="btnsubmit" id="btnsubmit" value="Submit" style="font-size:18px;" onclick="Validate();"/><br> <!-- Rather than submitting form straight away, the submit button runs function to check if username/password is valid.-->
			Save Password: <input type="checkbox" id="checkSave" />
		</div>
		<input type='hidden' id="User" name="User" value="" /> <!-- Hidden tag used to store posted user data before it is submitted. -->
	</form>
		<!--<input type="button" id="btntest" value="Test" onclick="RunQuery()"/><div id="tableDiv"></div>-->
	</body>
</html>