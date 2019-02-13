<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_LogIn</title>
		<link rel="icon" href="https://www.goodfreephotos.com/albums/vector-images/screwdriver-and-wrench-vector-clipart.png">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple pages. -->
		<script type="text/javascript">
		
			$(document).keypress(function(e){
    				if (e.which == 13){
        				$("#btnsubmit").click();
    				}
			});
			
			function Validate() //Function to check if username/password are valid.
			{
				var Username=document.getElementById("Username").value; //Get username and password from text boxes.
				var Password=document.getElementById("Password").value;
				if (Username.includes("'")) //Protects against SQL injection.
				{
					return;
				}
				sql = "SELECT tblUser.password, tblUser.admin, tblPersonnel.name, tblPersonnel.department, tblPersonnel.userID FROM tblUser INNER JOIN tblPersonnel ON tblUser.userID = tblPersonnel.userID WHERE tblUser.username = '" + Username + "'"; //Query retrieves password, admin status, name, ID and department associated with input username.
				
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls Query.php, which handles the SQL query and sorting of result data.
				{
					valid = true;
					if (json && json[0]) //If any data has been retrieved.
					{
						if (json[0].password == Password) //If input password is valid.
						{
							analysis = 0;
							if (json[0].department == "Analysis") //Checks if user is in the analytics department.
							{
								analysis = 1;
							}
							document.getElementById("User").value =  (json[0].name).split(' ')[0]+ "," + json[0].userID + "," + json[0].admin + "," + analysis; //Sets user data to be posted (name, ID and admin/analysis status).
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
		</script>
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
	</head>
	<body>
	<form id="mainform" name="mainform" method="post" action="http://35.204.60.31/Home"> <!-- This form will post data to the home page when submitted. -->
		@csrf <!--Token to validates requests to server. -->
		<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
			<h2 id="header">Login</h2>
		</div>
		<div class="center">
			<input type="text" name="Username" id="Username" placeholder="Username" style="font-size:5vw;" value=""><br>  <!-- HTML input fields for form data. -->
			<input type="password" name="Password" id="Password" placeholder="Password" style="font-size:5vw;"><br><br>
			<input type="button" name="btnsubmit" class="button, glow-button" id="btnsubmit" value="Submit" style="font-size:5vw; width:30%;" onclick="Validate();"/><br> <!-- Rather than submitting form straight away, the submit button runs function to check if username/password is valid.-->
		</div>
		<input type='hidden' id="User" name="User" value="" /> <!-- Hidden tag used to store posted user data before it is submitted. -->
	</form>
		<!--<input type="button" id="btntest" value="Test" onclick="RunQuery()"/><div id="tableDiv"></div>-->
	</body>
</html>