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
				sql = "SELECT * FROM tblUser WHERE username = '" + Username"'";
				$.get("Query.php", {'sql':sql},function(json) //Calls Query.php, which handles the SQL query and sorting of result data.
				{
					valid = true;
					if (json)
					{
						if (json[0].password == Password)
						{
							document.getElementById("mainform").submit();
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
					if (!valid)
					{
						alert("Invalid username or password.");
					}
				},'json');
			}
			function RunQuery()
			{
				//sql = "SELECT * FROM tblUser WHERE username = '" + document.getElementById("Username").value + "'";
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
				*/
			}
		</script>
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css">
	</head>
	<body>
	<form id="mainform" name="mainform" method="post" action="http://35.204.60.31/Home">
		@csrf
		<h1 class="center">Login</h1>
		<div class="center">
			<input type="text" name="Username" id="Username" placeholder="Username" value=""><br>
			<input type="password" name="Password" id="Password" placeholder="Password" ><br>
			<input type="button" name="btnsubmit" id="btnsubmit" value="Submit" style="font-size:18px;" onclick="Validate();"/><br>
			Save Password: <input type="checkbox" id="checkSave" />
		</div>
		<div id="tableDiv"></div>
	</form>
		<!--<input type="button" id="btntest" value="Test" onclick="RunQuery()"/>-->
	</body>
</html>