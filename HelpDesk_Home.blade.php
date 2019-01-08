<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>HelpDesk_Home</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script>
		<script type="text/javascript">
			function Load()
			{
				//Fun();

				WriteTime();
			}			
		</script>
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css">
		<style type="text/css">
			.mainButton
			{
				font-size:40px;
				font-weight:bold;
				padding: 12px 25px;
				width: 80%;
				margin: auto;
			}
			.grid-class
			{
				display: grid;
				grid-template-columns: auto;
			}
		</style>
	</head>
	<body onload="Load()">
	<form id="mainform" name="mainform" method="post" action="">
		@csrf
		
		<div class="titleDiv">
			<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('');" />
			<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label>
			<h2 id="header" style="style=display:inline-block; font-size:36px;"></h2>	
		</div>	
		<div class="grid-class">
			<input class="mainButton" type="button" disabled value="New Call" onClick="GoToNewPage('NewCaller')" />
			<input class="mainButton" type="button" value="View Call History" onClick="GoToNewPage('CallHistory');" />
			<input class="mainButton" type="button" value="View Problems List" onClick="GoToNewPage('ProblemList');" />
			<input class="mainButton" type="button" value="View Personnel" onClick="GoToNewPage('PersonnelList');" />
			<input class="mainButton" type="button" value="View/Edit Equipment" onClick="GoToNewPage('EquipmentList');" />
		</div>
		<!--Fun mode (don't click if you have epilepsy...): <input id="checkFun" type="checkbox" onclick="Fun()"/>-->
	</form>
	</body>
</html>