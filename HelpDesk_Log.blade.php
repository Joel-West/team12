<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_LogIn</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="ExtraCode.js"></script>
		<script type="text/javascript">
			function Validate(){
				var Username=document.getElementById("Username").value;
				var Password=document.getElementById("Password").value;
				if (Username == "Alice" && Password == "Password"){
					location.replace("35.204.60.31/Home");
				}
			}
		</script>
		<link href="Styles.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	<form action="35.204.60.31/Home" method="get">
		<h1 class="center">Login</h1>
		<div class="center">
			<input type="text" name="Username" id="Username" placeholder="Username" "value=""><br>
			<input type="password" name="Password" id="Password" placeholder="Password" ><br>
			<input type="submit" name="submit" id="submit" value="Submit" style="font-size:18px;" onClick="Validate();" /><br>
			Save Password: <input type="checkbox" id="checkSave" />
		</div>
	</form>
	</body>
</html>