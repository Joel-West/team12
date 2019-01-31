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
			
			function RunQuery(sql) //Function for running a query to the personnel table and getting building a table.
			{
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{				
						var htm = "<table class='table' id='tbl' border='1'>";
						htm+="<tr id='t0'><th onclick='SortTable(0)' scope='col'>userID</th>";
						htm+="<th onclick='SortTable(1)' scope='col'>Name</th>";
						htm+="<th onclick='SortTable(2)'scope='col'>Job Title</th>";
						htm+="<th onclick='SortTable(3)'scope='col'>Department</th>";
						htm+="<th onclick='SortTable(4)'scope='col'>Telephone Number</th></tr>"; //Appending column headers.
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
					newRowCount = 0;
				},'json');
			}
			
			function AddRow() //Adds a new row to the table, from data in the text boxes.
			{
				   
				if (document.getElementById("txtID").value == false|| isNaN(document.getElementById("txtID").value) || GetRowWithID(document.getElementById("txtID").value) != -1)
				{
					alert("Invalid ID"); //Returns error if data input from text boxes is invalid.
					return;
				}
				else if (document.getElementById("txtName").value == false)
				{
					alert("Invalid name"); //Returns error if data input from text boxes is invalid.
					return;
				}
				else if (document.getElementById("txtJobTitle").value == false)
				{
					alert("Invalid job title"); //Returns error if data input from text boxes is invalid.
					return;
				}
				else if (document.getElementById("txtDepartment").value == false)
				{
					alert("Invalid department"); //Returns error if data input from text boxes is invalid.
					return;
				}
				else if (document.getElementById("txtTelephoneNumber").value == false || isNaN(document.getElementById("txtTelephoneNumber").value))
				{
					alert("Invalid telephone number"); //Returns error if data input from text boxes is invalid.
					return;
				}
				rows = GetRows(); //Gets number of rows.
				table = document.getElementById("tbl");
				row = table.insertRow(rows); //Adds new empty row.
				cell0 = row.insertCell(0); //Inserts and modifies each cell of the new row in turn.
				cell0.innerHTML = document.getElementById("txtID").value + "(new)"; //Until it has been added to the database, the first field is given a '(new)' tag.
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
				newRowCount+=1;
				//alert("New personnel added."); //Success message.
			}
			
			function UpdateRow() //Function that updates the selected row.
			{
				row = document.getElementById("tbl").rows[GetSelectedRow()]; //Gets the details of the row that is selected.
				row.cells[1].innerHTML = document.getElementById("txtName").value;
				row.cells[2].innerHTML = document.getElementById("txtJobTitle").value;
				row.cells[3].innerHTML = document.getElementById("txtDepartment").value;
				row.cells[4].innerHTML = document.getElementById("txtTelephoneNumber").value;
				if (!ListContains(updList, row.cells[0].innerHTML) && !row.cells[0].innerHTML.indexOf("(new)") != -1) //If selected row is not already marked to be updated when changes are saved to the database later and is not a new row.
				{
					updList.push(row.cells[0].innerHTML); //Add the ID of the row to the list of rows to by updated when changes are commited to the actual database.
					console.log(updList);
				}
				//alert("Personnel updated successfully");
			}
			
			function Search() //Function for searching table based on text box input.
			{
				search = true; //Defines whether search will go ahead.
				if (delList.length > 0 || updList.length > 0 || newRowCount > 0)
				{
					
					if (!confirm("You have unsaved changes to the database. Searching will cause these changes to be cleared. Continue?")) //Warn user about losing data on searching.
					{
						alert("No searchy for you!");
					}
				}
			}
			
			function SaveChanges(page) //Function that saves table data back to database.
			{
				sql = "";
				for (i = 0; i < delList.length; i++) //Iterate through delete list (deletion performed first as it reduces database size, making other operations quicker).
				{
					sql+="DELETE FROM tblPersonnel WHERE userID = " + delList[i] + "; ";
				}
				for (i = 0; i < updList.length; i++) //Iterate through delete list (deletion performed first as it reduces database size, making other operations quicker).
				{
					id = updList[i]
					rowNum = GetRowWithID(id); //Gets the row number in the local table that corresponds to the ID in the updList.
					if (rowNum != -1) //If row exists.
					{
						row = document.getElementById("tbl").rows[rowNum]; //Get row of local table that is being saved to database.
						sql+="UPDATE tblPersonnel SET ";
						sql+="name = '"+ row.cells[1].innerHTML + "', ";
						sql+="jobTitle = '"+ row.cells[2].innerHTML + "', ";
						sql+="department = '"+ row.cells[3].innerHTML + "', ";
						sql+="telephoneNumber = "+ row.cells[4].innerHTML + ", ";
						sql+="WHERE userID = " + id + "; ";
					}
				}
				for (i = 0; i < GetRows(); i++) //Iterate through all rows to find new rows.
				{
					row = document.getElementById("tbl").rows[i];
					if (row.cells[0].innerHTML.indexOf("(new)") != -1) //If record is new.
					{
						row.cells[0].innerHTML = row.cells[0].innerHTML.replace("(new)", '') //Remove the 'new' tag from the record.
						sql+="INSERT INTO tblPersonnel VALUES (";
						sql+=row.cells[0].innerHTML + ", ";
						sql+="'" + row.cells[1].innerHTML + "', ";
						sql+="'" + row.cells[2].innerHTML + "', "
						sql+="'" + row.cells[3].innerHTML +"', "
						sql+=row.cells[4].innerHTML + "); ";
					}
				}
				alert(sql);
				/*
				$.get("Query.php", {'sql':sql, 'returnData':false},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{				
						alert(json);
						alert(json[0]);
					}
				},'json');
				alert("Changes saved.");
				updList = "";
				delList = "";
				*/
			}
			
			var selected = 0; //Global variable corresponding to number of highlighted table rows.
		</script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
		<style>
		.table-wrapper-scroll-y
		{
		display: block;
		max-height: 850px;
		overflow-y: auto;
		-ms-overflow-style: -ms-autohiding-scrollbar;
		}
		</style>
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
		<div id="tableDiv" class="col-8 table-wrapper-scroll-y"> <!-- Div containing data table. -->
			Loading data...
		</div>
		<br/>
		<div id="inputDiv" align="center" class="col-4">
			<p>
				Search:<input type="text"></input> <!-- Box for searching the table for specific strings. -->
				<input type="button" class="btn" value="Submit" onclick="Search()"></input> <!-- Submits search on press -->
			</p>
			<input type="button" class="btn" value="Delete Selected Items" id="del" style="font-size:16px;" onclick="Delete()"/><br/><br/> <!-- Delete button that calls function within ExtraCode.js when pressed. -->
			ID:<input id="txtID" type="text"></input><br/> <!-- Input fields for adding a new row. -->
			Name:<input id="txtName" type="text"></input><br/>
			Job Title:<input id="txtJobTitle" type="text"></input><br/>
			Department:<input id="txtDepartment" type="text"></input><br/>
			Telephone Number:<input id="txtTelephoneNumber" type="text"></input><br/>
			<input type="button" class="btn" id="btnAdd" value="Add New Item" style="font-size:16px;" onclick="AddPressed()"></input>	
			<br/>
			<br/>
			<p align="center">
			<input type="button" class="btn" value="Save Changes" style="font-size:26px; padding: 6px 12px;" onClick="SaveChanges('Home');" /> <!-- Button for submitting changes to table. -->
			</p>
		</div>
		</div>
	</form>
	</div>
	</body>
</html>