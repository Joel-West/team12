<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_LogIn</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="ExtraCode.js"></script>
		<script type="text/javascript">
			/*$('#form'.validate({
				alert("I am an alert box!");
				rules:
				{
					Username:
					{
						required: true
					}
				},
			    messages:
				{
					Username:
					{
						required: "Mandatory field"
					}
				}
			});
			*/			
			
			function Validate(){
				var Username=document.getElementById("Username").value;
				var Password=document.getElementById("Password").value;
				if (Username == "A" && Password == "P")
				{
					document.getElementById("mainform").submit();
				}
				else
				{
					alert("Uhhhhhh mate what you doing?");
				}
			}
		</script>
		<link href="Styles.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	<!--action="header('Location: http://35.204.60.31/Home');"-->
	<form id="mainform" name="mainform" method="post" action="http://35.204.60.31/Home">
		@csrf
		<h1 class="center">Login</h1>
		<div class="center">
			<input type="text" name="Username" id="Username" placeholder="Username" value=""><br>
			<input type="password" name="Password" id="Password" placeholder="Password" ><br>
			<input type="button" name="btnsubmit" id="btnsubmit" value="Submit" style="font-size:18px;" onclick="Validate();"/><br>
			Save Password: <input type="checkbox" id="checkSave" />
			<!--<input type="submit" style="visibility:hidden"/>-->
			<input type="submit" value="You shouldn't be able to see me..."/>
		</div>
	</form>
	</body>
</html>