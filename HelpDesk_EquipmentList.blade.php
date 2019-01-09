<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_EquipmentList</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple other files -->
		<script type="text/javascript">
			function Load() //Function that runs when file loads.
			{
				rows = GetRows();
				for (i = 0; i < rows; i++) //Iterates through all rows of table, assigning ID.
				{
					document.getElementById("tbl").rows[i].style.backgroundColor = '#9FFF30';
					document.getElementById("tbl").rows[i].id = "t" + i;
				}
				WriteTime(); //Function that writes the current time at the top of the page.
			}
			
			function AddNewRow() //Function to add new row to the local data table.
			{
				if (document.getElementById("txtSerial").value == false || document.getElementById("txtType").value == false || document.getElementById("txtMake").value == false)
				{
					alert("Invalid input"); //Returns error if data input from text boxes is invalid.
					return;
				}
				rows = GetRows(); //Gets number of rows.
				table = document.getElementById("tbl");
				row = table.insertRow(rows); //Adds new empty row.
				cell0 = row.insertCell(0); //Inserts and modifies each cell of the new row in turn.
				cell0.innerHTML = document.getElementById("txtSerial").value;
				cell1 = row.insertCell(1);
				cell1.innerHTML = document.getElementById("txtType").value;
				cell2 = row.insertCell(2);
				cell2.innerHTML = document.getElementById("txtMake").value;
				document.getElementById("tbl").rows[rows].id = "t" + document.getElementById("tbl").rows[rows-1].id; //Sets ID of new row.
				document.getElementById("tbl").rows[rows].style.backgroundColor = '#9FFF30'; //Sets background colour of new row.
				alert("New equipment added."); //Success message.
			}
			
			function SaveChanges(page) //Function that saves table data back to database.
			{
				alert("Changes saved.");
			}
			
			var selected = 0; //Global variable corresponding to number of highlighted table rows.
		</script>
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
	</head>
	<body onload="Load()" id="body">
	<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
		<input type='hidden' name="Username" value="<?php echo $_POST['Username']; ?>" /> <!-- Hidden tag used to store posted username so that it can later be posted back to the home page. -->
		@csrf <!--Token to validates requests to server. -->
		<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
			<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" /> <!-- Back button. -->
			<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label> <!-- Label to contain current data/time. -->
			<h2 id="headerId" style="style=display:inline-block; font-size:30px;">Equipment</h2> <!-- Heading containing name of page. -->
		</div>
		<div id="tableDiv"> <!-- Div containing data table. -->
			<table id="tbl" border="1">
				<tr>
					<th>Serial Number</th>
					<th>Equipment Type</th>
					<th>Equipment Make</th>
				</tr>
				<tr>
					<td>K45nQkCo</td>
					<td>Keyboard</td>
					<td>Dell</td>
				</tr>
				<tr>
					<td>aMAnEozQ</td>
					<td>Headset with microphone</td>
					<td>Plantronics</td>
				</tr>
				<tr>
					<td>FLoDZzQF</td>
					<td>Speakers (pair)</td>
					<td>Logitech</td>
				</tr>
				<tr>
					<td>z9e3BMDT</td>
					<td>Desktop computer</td>
					<td>Lenovo</td>
				</tr>
				<tr>
					<td>mf33tLqF</td>
					<td>Ergonomic mouse</td>
					<td>Logitech</td>
				</tr>
			</table>
		</div>
		<br/>
		<div align="center">
			<input type="button" value="Delete Selected Items" id="del" style="font-size:16px;" onclick="Delete()"/><br/><br/> <!-- Delete button that calls function within ExtraCode.js when pressed. -->
			Serial Number:<input id="txtSerial" type="text"></input><br/> <!-- Input fields for adding a new row. -->
			Equipment Type:<input id="txtType" type="text"></input><br/>
			Equipment Make:<input id="txtMake" type="text"></input><br/>
			<input type="button" value="Add New Item" style="font-size:16px;" onclick="AddNewRow()"></input> <!-- Button for submitting data concerning adding a new row. -->
		</div>
		<p align="center">
			<input type="button" value="Save Changes" style="font-size:26px; padding: 6px 12px;" onClick="SaveChanges('Home');" /> <!-- Button for submitting changes to table. -->
		</p>
	</form>
	</body>
</html>