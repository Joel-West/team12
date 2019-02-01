<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_Analytics</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>	<!-- Importing Bootstrap files. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple other files -->
		<script type="text/javascript">
			function Load() //Function that runs when file loads.
			{
				WriteTime(); //Function that writes the current time at the top of the page.
				GetWorstHardware();
			}
	function GetWorstHardware()	
	{
		sql="select * from tblEquipment;"
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json)
		{
			if(json && json[0])
			{
			document.getElementById("label1").innerHTML="Hardware most problems logged:"+json[0].serialNumber;
			}
			else
			{
			document.getElementById("label1").innerHTML="Can't find appropriate data";
			}
		}
	}	


document.getElementById("label1").innerHTML='Mouse';

			
			
		</script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"> <!-- Bootstrap CSS stylesheet. -->
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
		<style>
		.labelClass
		{
			font-size:24px;
		}
		</style>
	
	</head>
	<body onload="Load()">
		<div class="container"> <!-- Container holds elements together using Bootstrap. -->
			<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
				<input type='hidden' name="User" value="<?php echo $_POST['User']; ?>" /> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
				@csrf <!--Token to validates requests to server. -->
				<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
					<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" /> <!-- Back button. -->
					<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label> <!-- Label to contain current data/time. -->
					<h2 id="headerId" style="style=display:inline-block; font-size:30px;">Analytics</h2> <!-- Heading containing name of page. -->
				</div>
				<br/>
				<br/>
				<div class="row" align="center">
					<div id="analyticsDiv">  <!-- Div containing analytics info. -->
						<!-- Put stuff in here. -->
						<label id="label1" class="labelClass">Hi</label>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>