<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_LogIn</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script>
		<script type="text/javascript">			
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
			function RunQuery()
			{
				sql = "SELECT * FROM tblPersonnel";
				$.get("Query.php", {'sql':sql},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php was a json array		
					{
						alert(json[0])
					}
					else
					{
						alert("Sorry, no results found..."); //If no results, display error.
					}
				},'json');
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
			<!-- <input type="submit" value="You shouldn't be able to see me..." style="visibility:hidden"/> INVISIBLE SUBMIT BUTTON SO THAT SUBMIT FUNCTION WORKS, DO NOT TOUCH ME-->
		</div>
	</form>
		<input type="button" id="btntest" value="Test" onclick="RunQuery()"/>
	</body>
</html>