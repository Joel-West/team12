<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_PersonnelList</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>	<!-- Importing Bootstrap files. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple other files -->
		<script type="text/javascript">	
			var userData; //Variable containing data about user.
			var currentPage = "PersonnelList"; //Variable storing the name of the current page, so it can be passed in the URL to the next page as a 'previous page' variable.
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				SetPrivileges(userData) //Enter function that defines what functions are available to user based on status.
				sql = "SELECT * FROM tblPersonnel;"; //Simple query to get all data from table.
				RunQuery(sql); //Runs function get gets data from database and display it in tableDiv.
				WriteTime(); //Function that writes the current time at the top of the page.
			}
			
			function SetPrivileges(userData) //Function that checks if user is an admin or analyst and adjusts available buttons accordingly.
			{
				admin = (userData.split(","))[2]; //Retrieves admin/analyst status from userData that was earlier posted from previous form.
				if (admin == 0)
				{
					$("#inputDiv :input").prop("disabled", true); //If not admin, disable all input fields in the input div.
				}	
			}
			
			function Search() //Function for searching table based on text box input.
			{
				search = true; //Defines whether search will go ahead.
				if (delList.length > 0 || updList.length > 0 || newRowCount > 0)
				{
					if (!confirm("You have unsaved changes to the database. Searching will cause these changes to be cleared. Continue?")) //Warn user about losing data on searching.
					{
						return;
					}
					else
					{
						updList = []; //Clear lists of pending changes.
						delList = [];
						newRowCount = 0;
					}
				}
				str = doc.getElementById(txtSearch).value.toUpperCase();
				sql = "SELECT * FROM tblPersonnel WHERE upper(userID) LIKE '%"+str+"%' OR upper(name) LIKE '%"+str+"%' OR upper(jobTitle) LIKE '%"+str+"%' OR upper(department) LIKE '%"+str+"%' OR upper(telephoneNumber) LIKE '%"+str+"%' OR upper(specialist) LIKE '%"+str+"%';"; //Query that returns all database records with a cell containing search string.
				RunQuery(sql); //Runs function get gets data from database and display it in tableDiv.
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
						htm+="<th onclick='SortTable(4)'scope='col'>Telephone Number</th>";
						htm+="<th onclick='SortTable(5)'scope='col'>Specialist</th></tr>"; //Appending column headers.
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							htm += "<tr id='t" + (i+1) + "' style='background-color:rgb(159, 255, 48);'>"; //Sets colour and ID of row.
							htm +="<td id='id'>"+json[i].userID+"</td>";
							htm +="<td id='name'>"+json[i].name+"</td>";
							htm +="<td id='jobTitle'>"+json[i].jobTitle+"</td>";		
							htm +="<td id='department'>"+json[i].department+"</td>";
							htm +="<td id='telephoneNumber'>"+json[i].telephoneNumber+"</td>";
							htm +="<td id='specialist'>"+json[i].specialist+"</td>";
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
			
			function GetSpecialistAsBool(specialist) //Gets the specialist value from a table as a string and returns a boolean.
			{
				if (specialist == "Yes")
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			
			function GetSpecialistAsString(specialist) //Gets the specialist value from the checkbox as a boolean and returns a string.
			{
				if (specialist == true)
				{
					return "Yes";
				}
				else
				{
					return "No";
				}
			}		
			
			$(document).on('DOMSubtreeModified','td',function() //Function runs when table cell is clicked, helps against SQL injection by validating when cell contents is changed.
			{
				//console.log($(this).attr('id')); //Logs ID (for debugging).
			});
			
			function CheckIfUpdateOrAdd() //The 'add' button into an 'update' button and populate the text boxes, if exactly one row is selected.
			{
				if (selected == 1)
				{
					document.getElementById("btnAdd").value = "Update Item";
					rowNum = GetSelectedRow(); //Gets the row that is selected.
					document.getElementById("txtID").value = document.getElementById("tbl").rows[rowNum].cells[0].innerHTML;
					document.getElementById("txtID").disabled = true;
					document.getElementById("txtName").value = document.getElementById("tbl").rows[rowNum].cells[1].innerHTML;
					document.getElementById("txtJobTitle").value = document.getElementById("tbl").rows[rowNum].cells[2].innerHTML;
					document.getElementById("txtDepartment").value = document.getElementById("tbl").rows[rowNum].cells[3].innerHTML;
					document.getElementById("txtTelephoneNumber").value = document.getElementById("tbl").rows[rowNum].cells[4].innerHTML;
					document.getElementById("chkSpecialist").checked = GetSpecialistAsBool(document.getElementById("tbl").rows[rowNum].cells[5].innerHTML);
				}
				else
				{
					document.getElementById("btnAdd").value = "Add New Item";
					document.getElementById("txtID").value = "";
					document.getElementById("txtID").disabled = false;
					document.getElementById("txtName").value = "";
					document.getElementById("txtJobTitle").value = "";
					document.getElementById("txtDepartment").value = "";
					document.getElementById("txtTelephoneNumber").value = "";
					document.getElementById("chkSpecialist").checked = false;
				}
			}		
			
			function AddRow() //Adds a new row to the table, from data in the text boxes.
			{
				if (document.getElementById("txtID").value == false|| isNaN(document.getElementById("txtID").value) || GetRowWithID(document.getElementById("txtID").value) != -1 || GetRowWithID(document.getElementById("txtID").value + "(new)") != -1)
				{
					alert("Invalid ID"); //Returns error if data input from text box is invalid.
					return;
				}
				else if (document.getElementById("txtName").value == false)
				{
					alert("Invalid name"); //Returns error if data input from text box is invalid.
					return;
				}
				else if (document.getElementById("txtJobTitle").value == false)
				{
					alert("Invalid job title"); //Returns error if data input from text box is invalid.
					return;
				}
				else if (document.getElementById("txtDepartment").value == false)
				{
					alert("Invalid department"); //Returns error if data input from text box is invalid.
					return;
				}
				else if (document.getElementById("txtTelephoneNumber").value == false || isNaN(document.getElementById("txtTelephoneNumber").value))
				{
					alert("Invalid telephone number"); //Returns error if data input from text box is invalid.
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
				cell5 = row.insertCell(5);
				cell5.innerHTML = GetSpecialistAsString(document.getElementById("chkSpecialist").checked);
				document.getElementById("tbl").rows[rows].id = "t" + document.getElementById("tbl").rows[rows-1].id; //Sets ID of new row.
				document.getElementById("tbl").rows[rows].style.backgroundColor = '#9FFF30'; //Sets background colour of new row.
				newRowCount+=1;
				alert("New personnel added."); //Success message.
			}
			
			function UpdateRow() //Function that updates the selected row.
			{
				row = document.getElementById("tbl").rows[GetSelectedRow()]; //Gets the details of the row that is selected.
				row.cells[1].innerHTML = document.getElementById("txtName").value;
				row.cells[2].innerHTML = document.getElementById("txtJobTitle").value;
				row.cells[3].innerHTML = document.getElementById("txtDepartment").value;
				row.cells[4].innerHTML = document.getElementById("txtTelephoneNumber").value;
				row.cells[5].innerHTML = GetSpecialistAsString(document.getElementById("chkSpecialist").checked);
				row.style.backgroundColor = '#9FFF30';
				selected = 0;
				if (!ListContains(updList, row.cells[0].innerHTML) && !row.cells[0].innerHTML.includes("(new)")) //If selected row is not already marked to be updated when changes are saved to the database later and is not a new row.
				{
					updList.push(row.cells[0].innerHTML); //Add the ID of the row to the list of rows to by updated when changes are commited to the actual database.
					console.log(updList);
				}
				alert("Personnel updated successfully.");
			}
			
			function Delete() //Function for deleting selected rows from a table.
			{
				admin = (userData.split(","))[2];
				if (selected == 0 || admin == 0) //if no rows are selected, or if not admin, leave function.
				{
					return;
				}
				if (confirm("If any of these rows are found in the table of users, they will also be deleted. Delete selected rows?")) //Get user confirmation.
				{
					rows = GetRows();
					for (i = rows-1; i > 0; i--) //Iterate through the rows of the table.
					{
						deleteRow = false; //Variable holding if row will actually be deleted.
						if (document.getElementById("tbl").rows[i].style.backgroundColor != 'rgb(159, 255, 48)') //If row is selected.
						{
							deleteRow = true;						
						}
						if (deleteRow == true) //If should be deleted after validation.
						{
							console.log("deleting t" + i);
							if (document.getElementById("tbl").rows[i].cells[0].innerHTML.includes("(new)")) //If row is a new row, decrement number of new rows.
							{
								newRowCount -=1;
							}
							else
							{
								indexInUpdList = updList.indexOf(document.getElementById("tbl").rows[i].cells[0].innerHTML); //Get index of deleted item in update list.
								if (indexInUpdList > -1)
								{
									updList.splice(indexInUpdList, 1); //Delete row from the update list - if record is deleted, it will not need to be updated.
								}
								delList.push(document.getElementById("tbl").rows[i].cells[0].innerHTML); //Add record id to list of rows that will be deleted from the actual database later.
							}
							document.getElementById("tbl").deleteRow(i); //Delete the row.
						}
					}
					selected = 0;
					console.log(delList);
					CheckIfUpdateOrAdd();
				}
			}
			
			function SaveChanges(page) //Function that saves table data back to database.
			{
				admin = (userData.split(","))[2];
				if (admin == 0) //If not admin, action is forbidden.
				{
					return;
				}
				sql = "";
				for (i = 0; i < delList.length; i++) //Iterate through delete list (deletion performed first as it reduces database size, making other operations quicker).
				{
					sql+="DELETE FROM tblPersonnel WHERE userID = " + delList[i] + "; ";
					sql+="DELETE FROM tblUser WHERE userID = " + delList[i] + "; ";
					sql+="DELETE FROM tblSpecialisation WHERE userID = " + delList[i] + "; ";
				}
				for (i = 0; i < updList.length; i++) //Iterate through delete list (deletion performed first as it reduces database size, making other operations quicker).
				{
					//console.log("i = " +i);
					id = updList[i];
					rowNum = GetRowWithID(id); //Gets the row number in the local table that corresponds to the ID in the updList.
					//console.log("rowNum = " + rowNum);
					//console.log("i = " +i);
					if (rowNum != -1) //If row exists.
					{
						row = document.getElementById("tbl").rows[rowNum]; //Get row of local table that is being saved to database.
						sql+="UPDATE tblPersonnel SET ";
						sql+="name = '"+ row.cells[1].innerHTML + "', ";
						sql+="jobTitle = '"+ row.cells[2].innerHTML + "', ";
						sql+="department = '"+ row.cells[3].innerHTML + "', ";
						sql+="telephoneNumber = "+ row.cells[4].innerHTML + ", ";
						sql+="specialist = '"+ row.cells[5].innerHTML + "' ";
						sql+="WHERE userID = " + id + "; ";
					}
				}
				for (i = 0; i < GetRows(); i++) //Iterate through all rows to find new rows.
				{
					row = document.getElementById("tbl").rows[i];
					if (row.cells[0].innerHTML.includes("(new)")) //If record is new.
					{
						row.cells[0].innerHTML = row.cells[0].innerHTML.replace("(new)", '') //Remove the 'new' tag from the record.
						sql+="INSERT INTO tblPersonnel VALUES (";
						sql+=row.cells[0].innerHTML + ", ";
						sql+="'" + row.cells[1].innerHTML + "', ";
						sql+="'" + row.cells[2].innerHTML + "', "
						sql+="'" + row.cells[3].innerHTML +"', "
						sql+=row.cells[4].innerHTML + ", "
						sql+="'" + row.cells[5].innerHTML + "'); ";
					}
				}
				alert(sql);
				if (sql != "") //If there is any SQL to run.
				{
					$.get("Query.php", {'sql':sql, 'returnData':false},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
					{
						if(json && json[0]) //If result of php file was a json array.	
						{				
							alert(json);
							alert(json[0]);
						}
					},'json');
					updList = []; //Clear lists of pending changes.
					delList = [];
					newRowCount = 0;
					alert("Changes saved.");
				}
			}
			
			var selected = 0; //Global variable corresponding to number of highlighted table rows.
		</script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"> <!-- Bootstrap CSS stylesheet. -->
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
		<style>
		.table-wrapper-scroll-y <!-- class for table, allowing it to scroll. -->
		{
			display: block;
			max-height: 700px;
			overflow-y: auto;
			-ms-overflow-style: -ms-autohiding-scrollbar;
		}
		</style>
	</head>
	<body onload="Load()" style="height:100%;">
		<div class="container-fluid"> <!-- Container holds elements together using Bootstrap. -->
			<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
				<input type='text' hidden id="user" name="User"/> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
				@csrf <!--Token to validates requests to server. -->
				<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
					<input type="button" id="btnBack" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" /> <!-- Back button. -->
					<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label> <!-- Label to contain current data/time. -->
					<h2 id="headerId" style="font-weight: bold; style=display:inline-block; font-size:30px;">Personnel</h2> <!-- Heading containing name of page. -->
				</div>
				<br/>
				<div class="row" align="center">
					<div id="tableDiv" class="col-9 table-wrapper-scroll-y"> <!-- Div containing data table. -->
						Loading data...
					</div>
					<br/>
					<div id="rightDiv" align="center" class="col-3">
						<div id="searchDiv">
							<p>
								Search:<input id="txtSearch" type="text"></input> <!-- Box for searching the table for specific strings. -->
								<input type="button" class="btn" id="btnSearch" value="Submit" onclick="Search()"></input> <!-- Submits search on press -->
							</p>
						</div>
						<div id="inputDiv">
							<input type="button" class="btn" id="btnDelete" value="Delete Selected Items" id="del" style="font-size:16px;" onclick="Delete()"/><br/><br/> <!-- Delete button that calls function within ExtraCode.js when pressed. -->
							ID:<br/><input id="txtID" type="text"></input><br/> <!-- Input fields for adding a new row. -->
							Name:<br/><input id="txtName" type="text"></input><br/>
							Job Title:<br/><input id="txtJobTitle" type="text"></input><br/>
							Department:<br/><input id="txtDepartment" type="text"></input><br/>
							Telephone Number:<br/><input id="txtTelephoneNumber" type="text"></input><br/>
							Specialist? <input id="chkSpecialist" type="checkbox"></input><br/>
							<br/><input type="button" class="btn" id="btnAdd" value="Add New Item" style="font-size:16px;" onclick="AddPressed()"></input>	
							<br/>
							<br/>
							<p align="center">
							<input type="button" id="btnSave" class="btn" value="Save Changes" style="font-size:26px; padding: 6px 12px;" onClick="SaveChanges('Home');" /> <!-- Button for submitting changes to table. -->
							</p>
						</div>
					</div>
				</div>
		</form>
		</div>
	</body>
</html>