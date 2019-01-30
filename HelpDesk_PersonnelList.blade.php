<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_PersonnelList</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>	
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple other files -->
		<script type="text/javascript">	
			function Load() //Function that runs when file loads.
			{
				sql = "SELECT * FROM tblPersonnel;"; //Simple query to get all data from table.
				RunQuery(sql); //Runs function get gets data from database and display it in tableDiv.
				WriteTime(); //Function that writes the current time at the top of the page.
			}
			
			function RunQuery(sql)
			{
				$.get("Query.php", {'sql':sql},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{				
						var htm = "<table id='tbl' border='1'><tr id='t0'><th>userID</th><th>Name</th><th>Job Title</th><th>Department</th><th>Telephone Number</th></tr>"; //Appending column headers.
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							htm += "<tr id='t" + (i+1) + "' style='background-color:rgb(159, 255, 48);'>"; //Sets colour and ID of row.
							htm +="<td>"+json[i].userID+"</td>";
							htm +="<td>"+json[i].name+"</td>";
							htm +="<td>"+json[i].jobTitle+"</td>";		
							htm +="<td>"+json[i].department+"</td>";
							htm +="<td>"+json[i].telephoneNumber+"</td>";
							htm += "</tr>";							
						}
					}
					else
					{
						var htm = "Sorry, no results found..."; //If no results, display error.
					}
					document.getElementById("tableDiv").innerHTML = htm; //Appends HTML to tableDiv.
				},'json');
			}
			
			function AddRow()
			{
				if (document.getElementById("txtName").value == false || document.getElementById("txtJobTitle").value == false || document.getElementById("txtDepartment").value == false || document.getElementById("txtTelephoneNumber").value == false)
				{
					alert("Invalid input"); //Returns error if data input from text boxes is invalid.
					return;
				}
				rows = GetRows(); //Gets number of rows.
				table = document.getElementById("tbl");
				row = table.insertRow(rows); //Adds new empty row.
				cell0 = row.insertCell(0); //Inserts and modifies each cell of the new row in turn.
				cell0.innerHTML = "-"; //Until it has been added to the database, the first field (the auto-number) is left as null.
				cell1 = row.insertCell(1);
				cell1.innerHTML = document.getElementById("txtName").value;
				cell2 = row.insertCell(2);
				cell2.innerHTML = document.getElementById("txtJobTitle").value;
				cell3 = row.insertCell(3);
				cell3.innerHTML = document.getElementById("txtDepartment").value;
				cell4 = row.insertCell(4);
				cell4.innerHTML = document.getElementById("txtTelephoneNumber").value;
				document.getElementById("tbl").rows[rows].id = "t" + document.getElementById("tbl").rows[rows-1].id; //Sets ID of new row.
				document.getElementById("tbl").rows[rows].style.backgroundColor = '#9FFF30'; //Sets background colour of new row.
				//alert("New personnel added."); //Success message.
			}
			
			function UpdateRow() //Function that updates the selected row.
			{
				row = document.getElementById("tbl").rows[GetSelectedRow()]; //Gets the details of the row that is selected.
				row.cells[1].innerHTML = document.getElementById("txtName").value;
				row.cells[2].innerHTML = document.getElementById("txtJobTitle").value;
				row.cells[3].innerHTML = document.getElementById("txtDepartment").value;
				row.cells[4].innerHTML = document.getElementById("txtTelephoneNumber").value;
				if (!ListContains(updList, row.cells[0].innerHTML)) //if selected row is not already marked to be updated when changes are saved to the database later.
				{
					updList.push(row.cells[0].innerHTML); //Add the ID of the row to the list of rows to by updated when changes are commited to the actual database.
					console.log(updList);
				}
				//alert("Personnel updated successfully");
			}
			
			function SaveChanges(page) //Function that saves table data back to database.
			{
				alert("Changes saved.");
			}
			
			var selected = 0; //Global variable corresponding to number of highlighted table rows.
		</script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
	</head>
	<body onload="Load()" style="height:100%;"> <div class="container">
	<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
		<input type='hidden' name="User" value="<?php echo $_POST['User']; ?>" /> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
		@csrf <!--Token to validates requests to server. -->
		<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
			<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" /> <!-- Back button. -->
			<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label> <!-- Label to contain current data/time. -->
			<h2 id="headerId" style="font-weight: bold; style=display:inline-block; font-size:30px;">Personnel</h2> <!-- Heading containing name of page. -->
		</div>
		<br/>
		<div class="row" align="center">
		<div id="tableDiv" class="col-8" style="overflow-y: scroll; height:90%;"> <!-- Div containing data table. -->
			Loading data...
		</div>
		<br/>
		<div id="inputDiv" align="center" class="col-4">
			<p>
				Search:<input type="text"></input> <!-- Box for searching the table for specific strings. -->
				<input type="button" value="Submit"></input> <!-- Submits search on press -->
			</p>
			<input type="button" value="Delete Selected Items" id="del" style="font-size:16px;" onclick="Delete()"/><br/><br/> <!-- Delete button that calls function within ExtraCode.js when pressed. -->
			Name:<input id="txtName" type="text"></input><br/> <!-- Input fields for adding a new row. -->
			Job Title:<input id="txtJobTitle" type="text"></input><br/>
			Department:<input id="txtDepartment" type="text"></input><br/>
			Telephone Number:<input id="txtTelephoneNumber" type="text"></input><br/>
			<input type="button" id = "btnAdd" value="Add New Item" style="font-size:16px;" onclick="AddPressed()"></input>	
			<br/>
			<br/>
			<p align="center">
			<input type="button" value="Save Changes" style="font-size:26px; padding: 6px 12px;" onClick="SaveChanges('Home');" /> <!-- Button for submitting changes to table. -->
			</p>
		</div>
		</div>
	</form>
	</div>
	</body>
</html>