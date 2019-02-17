<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_CallHistory</title>
		<link rel="icon" href="https://www.goodfreephotos.com/albums/vector-images/screwdriver-and-wrench-vector-clipart.png">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>	<!-- Get JQuery library from google. -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>	<!-- Importing Bootstrap files. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple pages. -->
		<script type="text/javascript">
			var userData; //Variable containing data about user.
			var currentPage = "CallHistory"; //Variable storing the name of the current page, so it can be passed in the URL to the next page as a 'previous page' variable.
			var selected = 0; //Global variable corresponding to number of highlighted table rows.
			
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				SetPrivileges(userData) //Enter function that defines what functions are available to user based on status.
				ResetTable();
				WriteTime(); //Function that writes the current time at the top of the page.
				CheckIfUpdate()
			}
			
			function SetPrivileges(userData) //Function that checks if user is an admin, analyst or specialist and adjusts available buttons accordingly.
			{
				admin = (userData.split(","))[2];
				operator = (userData.split(","))[5]; //Retrieves statuses from userData that was earlier posted from previous form.
				if (admin == 0 && operator == 0)
				{
					$("#inputDiv :input").prop("disabled", true); //If not admin or operator, disable all input fields in the input div.
				}
				SetNavSettings();
			}
			
			function ResetTable()
			{
				if (document.getElementById("txtSearch").value == "") //If not searching anything.
				{
					sql = "SELECT tblCallHistory.*, tblProblem.problem, p1.name AS operatorName, p2.name AS callerName FROM tblCallHistory LEFT JOIN tblProblem ON tblCallHistory.problemNumber = tblProblem.problemNumber LEFT JOIN tblPersonnel p1 ON tblCallHistory.operatorID = p1.userID LEFT JOIN tblPersonnel p2 ON tblCallHistory.CallerID = p2.userID;"; //Query to get all data from table, using left joins to get names in other tables from IDs.
					RunQuery(sql); //Runs function get gets data from database and display it in tableDiv.
				}
			}
			
			function Search() //Function for searching table based on text box input.
			{
				search = true; //Defines whether search will go ahead.
				if (delList.length > 0 || updList.length > 0 || newRowCount > 0) //If there are pending changes.
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
				str = document.getElementById("txtSearch").value.toUpperCase(); //Gets uppercase array of searched text.
				if (str.includes("'")) //If contains ' (if it is SQL injection-prone).
				{
					sql = "SELECT * FROM tblCallHistory WHERE 1 = 0;"; //Get no results.
				}
				else
				{
					
					str = str.replace(", ", ",").split(","); //Split search text by commas.
					sql = "SELECT tblCallHistory.*, tblProblem.problem, p1.name AS operatorName, p2.name AS callerName FROM tblCallHistory LEFT JOIN tblProblem ON tblCallHistory.problemNumber = tblProblem.problemNumber LEFT JOIN tblPersonnel p1 ON tblCallHistory.operatorID = p1.userID LEFT JOIN tblPersonnel p2 ON tblCallHistory.CallerID = p2.userID WHERE ";
					for (i = 0; i < str.length; i++) //Iterates through list of search terms, adding to the SQL query.
					{
						if (i != 0)
						{
							sql+=" OR ";
						}
						sql += "upper(tblCallHistory.callNumber) LIKE '%"+str[i]+"%' OR upper(tblCallHistory.operatorID) LIKE '%"+str[i]+"%' OR upper(tblCallHistory.callerID) LIKE '%"+str[i]+"%' OR upper(tblCallHistory.timeDate) LIKE '%"+str[i]+"%' OR upper(tblCallHistory.problemNumber) LIKE '%"+str[i]+"%' OR upper(tblCallHistory.notes) LIKE '%"+str[i]+"%' OR upper(tblProblem.problem) LIKE '%"+str[i]+"%' OR upper(p1.name) LIKE '%"+str[i]+"%' OR upper(p2.name) LIKE '%"+str[i]+"%'"; //Query that returns all database records with a cell containing search string.
					}
				}
				RunQuery(sql); //Runs function get gets data from database and display it in tableDiv.
			}
			
			function RunQuery(sql) //Function for running a query to the equipment table and getting building a table.
			{
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{				
						var htm = "<table class='table' id='tbl' border='1'>";
						htm+="<tr id='t0'><th onclick='SortTable(0)' scope='col'>#</th>";
						htm+="<th onclick='SortTable(1)' scope='col'>Operator</th>";
						htm+="<th onclick='SortTable(2)' scope='col'>Caller</th>";
						htm+="<th onclick='SortTable(3)' scope='col'>Time/Date</th>";
						htm+="<th onclick='SortTable(4)' scope='col'>Problem</th>";
						htm+="<th onclick='SortTable(5)'scope='col'>Notes</th></tr>"; //Appending column headers.
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							htm += "<tr class='rowDeselected'>"; //Sets class (deselected) of row.
							htm +="<td>"+json[i].callNumber+"</td>";
							if (json[i].operatorID == null) //If there is no operator ID (the personnel has been deleted since the call was recorded).
							{
								htm +="<td>Unidentified</td>";
							}
							else
							{
								htm +="<td>"+json[i].operatorID+" - "+json[i].operatorName+"</td>";
							}
							if (json[i].callerID == null) //If there is no caller ID (the personnel has been deleted since the call was recorded).
							{
								htm +="<td>Unidentified</td>";
							}
							else
							{
								htm +="<td>"+json[i].callerID+" - "+json[i].callerName+"</td>";
							}
							htm +="<td>"+json[i].timeDate+"</td>";
							if (json[i].problemNumber == null) //If there is no problem number (the problem has been deleted since the call was recorded).
							{
								htm +="<td>Unidentified</td>";
							}
							else
							{
								htm +="<td>"+json[i].problemNumber+" - "+json[i].problem+"</td>";
							}
							htm +="<td>"+json[i].notes+"</td>";
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
			
			function CheckIfUpdate() //Prevents user input if more or less than one row is selected.
			{
				if (selected == 1)
				{
					rowNum = GetSelectedRow(); //Gets the row that is selected.
					document.getElementById("btnUpdate").disabled = false;
					document.getElementById("txtNotes").disabled = false;
					document.getElementById("txtNotes").value = document.getElementById("tbl").rows[rowNum].cells[5].innerHTML;
				}
				else
				{
					document.getElementById("btnUpdate").disabled = true;
					document.getElementById("txtNotes").disabled = true;
					document.getElementById("txtNotes").value = "";
				}
			}
			
			function ValidateInput() //Function returns true if the data input box is valid.
			{
				id = "txtNotes";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'") || document.getElementById(id).value.length > 2047)
				{
					alert("Invalid notes."); //Returns error if data input from text box is invalid.
					return false;
				}
				return true;
			}
			
			function UpdateRow() //Function that updates the selected row.
			{
				if (!ValidateInput())
				{
					return;
				}
				row = document.getElementById("tbl").rows[GetSelectedRow()]; //Gets the details of the row that is selected.
				row.cells[5].innerHTML = document.getElementById("txtNotes").value;
				row.classList.replace("rowSelected", "rowDeselected"); //Deselect updated row.
				selected = 0;
				CheckIfUpdate();
				if (!ListContains(updList, row.cells[0].innerHTML)) //If selected row is not already marked to be updated when changes are saved to the database later.
				{
					updList.push(row.cells[0].innerHTML); //Add the ID of the row to the list of rows to be updated when changes are commited to the actual database.
					console.log(updList);
				}
			}
			
			function Delete() //Function for deleting selected rows from a table.
			{
				admin = (userData.split(","))[2];
				if (selected == 0 || admin == 0) //if no rows are selected, or if not admin, leave function.
				{
					return;
				}
				if (confirm("Delete selected rows?")) //Get user confirmation.
				{
					rows = GetRows();
					for (i = rows-1; i > 0; i--) //Iterate through the rows of the table.
					{
						deleteRow = false; //Variable holding if row will actually be deleted.
						if (document.getElementById("tbl").rows[i].classList.contains("rowSelected")) //If row is selected.
						{
							deleteRow = true;						
						}
						if (deleteRow == true) //If should be deleted after validation.
						{
							indexInUpdList = updList.indexOf(document.getElementById("tbl").rows[i].cells[0].innerHTML); //Get index of deleted item in update list.
							if (indexInUpdList > -1)
							{
								updList.splice(indexInUpdList, 1); //Delete row from the update list - if record is deleted, it will not need to be updated.
							}
							delList.push(document.getElementById("tbl").rows[i].cells[0].innerHTML); //Add record id to list of rows that will be deleted from the actual database later.
							document.getElementById("tbl").deleteRow(i); //Delete the row.
						}
					}
					selected = 0;
					console.log(delList);
					CheckIfUpdate();
				}
			}
			
			function SaveChanges() //Function that saves table data back to database.
			{
				admin = (userData.split(","))[2];
				operator = (userData.split(","))[5]; //Retrieves statuses from userData that was earlier posted from previous form.
				if (admin == 0 && operator == 0) //if not admin or operator, action is forbidden.
				{
					return;
				}
				sql = "";
				for (i = 0; i < delList.length; i++) //Iterate through delete list (deletion performed first as it reduces database size, making other operations quicker).
				{
					sql+="DELETE FROM tblCallHistory WHERE callNumber = " + delList[i] + "; ";
				}
				for (i = 0; i < updList.length; i++) //Iterate through the update list.
				{
					callNumber = updList[i];
					rowNum = GetRowWithID(callNumber); //Gets the row number in the local table that corresponds to the call number in the updList.
					if (rowNum != -1) //If row exists.
					{
						row = document.getElementById("tbl").rows[rowNum]; //Get row of local table that is being saved to database.
						sql+="UPDATE tblCallHistory SET ";
						sql+="notes = '"+ row.cells[5].innerHTML + "' ";
						sql+="WHERE callNumber = " + callNumber + "; ";
					}
				}
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
				else
				{
					alert("There are no changes to save.");
				}
			}
		</script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"> <!-- Bootstrap CSS stylesheet. -->
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
		<style>
		.table-wrapper-scroll-y
		{
			display: block;
			max-height:85vh;
			overflow-y: auto;
			-ms-overflow-style: -ms-autohiding-scrollbar;
		}
		</style>
	</head>
	<body onload="Load()" style="height:100%;">
		<header class="navbar flex-column flex-md-row bd-navbar navbar-dark navbar-expand-lg bg-dark"> <!-- Header contains Bootstrap nav-bar. -->
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="navbar-collapse collapse" id="navbarNavDropdown"> <!-- Collapsable menu for nav-bar elements that appears when view width is low. -->
				<ul class='navbar-nav mr-auto'>
				<a class='nav-item nav-link' href='#' onClick='GoToNewPage(document.getElementById(&quot;previous&quot;).value)'>&#x2190 </a> <!-- Back button using unicode backwards arrow character. -->
				<a class='nav-item nav-link' id='Home' href='#' onClick='GoToNewPage(&quot;Home&quot;);'>Home</a>
				<a class='nav-item nav-link' id='NewCaller' href='#' onClick='GoToNewPage(&quot;NewCaller&quot;);'>New Call</a>
				<a class='nav-item nav-link' id='CallHistory' href='#' onClick='GoToNewPage(&quot;CallHistory&quot;);'>Call History</a>
				<a class='nav-item nav-link' id='ProblemList' href='#' onClick='GoToNewPage(&quot;ProblemList&quot;);'>Problems List</a>
				<a class='nav-item nav-link' id='PersonnelList' href='#' onClick='GoToNewPage(&quot;PersonnelList&quot;);'>Personnel</a>
				<a class='nav-item nav-link' id='UserList' href='#' onClick='GoToNewPage(&quot;UserList&quot;);'>Users</a>
				<a class='nav-item nav-link' id='SpecialisationList' href='#' onClick='GoToNewPage(&quot;SpecialisationList&quot;);'>Specialisations</a>
				<a class='nav-item nav-link' id='EquipmentList' href='#' onClick='GoToNewPage(&quot;EquipmentList&quot;);'>Equipment</a>
				<a class='nav-item nav-link'id='ProblemTypeList' href='#' onClick='GoToNewPage(&quot;ProblemTypeList&quot;);'>Problem Type List</a>
				<a class='nav-item nav-link'id='Analytics' href='#' onClick='GoToNewPage(&quot;Analytics&quot;);'>Analytics</a></ul>
			</div>
			<a class='nav-item nav-link' href='#' onClick='GoToNewPage("");'>Logout</a>
			<a class="navbar-brand ml-md-auto" href="#">
			<img src="https://www.goodfreephotos.com/albums/vector-images/screwdriver-and-wrench-vector-clipart.png" width="30" height="30" class="d-inline-block align-top" alt=""> <!-- Loads company icon -->
			  Make-It-All
			</a>
		</header>
		<div autocomplete="off" class="container-fluid"> <!-- Container holds elements together using Bootstrap. -->
			<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
				<input type='text' hidden id="user" name="User"/> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
				<input type='hidden' id='previous' name='Previous' value="<?php echo $_GET['previous']; ?>" /> <!-- Hidden tag holding name of previous page. -->
				@csrf <!--Token to validates requests to server. -->
				<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
					<label id="dtLabel"  class="dtLabel"></label> <!-- Label to contain current date/time. -->
					<h2 id="headerId">Call History</h2> <!-- Heading containing name of page. -->
				</div>
				<br/><br/>
				<div class="row" align="center">
					<div id="tableDiv" class="col-8 table-wrapper-scroll-y"> <!-- Div containing data table. -->
						Loading data...
					</div>
					<br/>
					<div id="rightDiv" align="center" class="col-4">
						<div id="searchDiv">
							<p>
								Search:<input id="txtSearch" type="text" oninput="ResetTable()"></input> <!-- Box for searching the table for specific strings. -->
								<input type="button" class="btn" id="btnSearch" value="Submit" onclick="Search()"></input> <!-- Submits search on press. -->
							</p>
						</div>
						<div id="inputDiv">
							<input type="button" class="btn" id="btnDelete" value="Delete Selected Items" id="del" style="font-size:16px;" onclick="Delete()"/><br/><br/> <!-- Delete button that calls function when pressed. -->
							Notes:<br/><textArea class="form-control text" rows="10" id="txtNotes" maxlength="2048"></textArea><br/> <!-- Input field for updating notes. -->
							<br/><input type="button" class="btn" id="btnUpdate" value="Update Item" style="font-size:16px;" onclick="UpdateRow()"></input>	
							<br/>
							<br/>
							<p align="center">
							<input type="button" id="btnSave" class="btn" value="Save Changes" style="font-size:26px; padding: 6px 12px;" onClick="SaveChanges();" /> <!-- Button for submitting changes to table. -->
							</p>
						</div>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>