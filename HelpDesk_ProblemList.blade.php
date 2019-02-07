<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_ProblemList</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple other files -->
		<script type="text/javascript">	
			var userData; //Variable containing data about user.
			var currentPage = "ProblemList"; //Variable storing the name of the current page, so it can be passed in the URL to the next page as a 'previous page' variable.
			var selected = 0; //Global variable corresponding to number of highlighted table rows.
			var extraCells = 2; //Refers to the numbers of extra cells in the table for the current problem category (software = 2, hardware = 1, network = 0).
			
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				SetPrivileges(userData) //Enter function that defines what functions are available to user based on status.
				ResetTable();
				WriteTime(); //Function that writes the current time at the top of the page.
				CheckIfUpdate()
				ChangeTab("Hardware");
			}
			
			function SetPrivileges(userData) //Function that checks if user is an admin or analyst and adjusts available buttons accordingly.
			{
				admin = (userData.split(","))[2];
				analyst = (userData.split(","))[3]; //Retrieves admin and analyst status from userData that was earlier posted from previous form.
				if (admin == 0 && analyst == 1)
				{
					$("#inputDiv :input").prop("disabled", true); //If not admin or operator, disable all input fields in the input div.
					$("#inputDiv :textArea").prop("disabled", true);
				}	
			}
			
			function ResetTable()
			{
				if (document.getElementById("txtSearch").value == "") //If not searching anything.
				{
					sql = "SELECT * FROM tblProblem"; //Simple query to get all data from table.
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
					sql = "SELECT * FROM tblProblem WHERE 1 = 0;"; //Get no results.
				}
				else
				{	
					str = str.replace(", ", ",").split(","); //Split search text by commas.
					sql = "SELECT * FROM tblProblem WHERE ";
					for (i = 0; i < str.length; i++) //Iterates through list of search terms, adding to the SQL query.
					{
						if (i != 0)
						{
							sql+=" OR ";
						}
						sql += "upper(problemNumber) LIKE '%"+str[i]+"%' OR upper(problem) LIKE '%"+str[i]+"%' OR upper(problemType) LIKE '%"+str[i]+"%' OR upper(problemSubType) LIKE '%"+str[i]+"%' OR upper(serialNumber) LIKE '%"+str[i]+"%' OR upper(operatingSystem) LIKE '%"+str[i]+"%' OR upper(softwareConcerned) LIKE '%"+str[i]+"%' OR upper(specialistID) LIKE '%"+str[i]+"%' OR upper(resolved) LIKE '%"+str[i]+"%' OR upper(dateTimeResolved) LIKE '%"+str[i]+"%' OR upper(solution) LIKE '%"+str[i]+"%'"; //Query that returns all database records with a cell containing search string.
					}
					console.log(sql);
				}
				RunQuery(sql); //Runs function get gets data from database and display it in tableDiv.
			}
			
			function RunQuery(sql) //Function for running a query to the personnel table and getting building a table.
			{
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{				
						var htm = "<table class='table' id='tbl' border='1'>";
						htm+="<tr id='t0'><th onclick='SortTable(0)' scope='col'>Num</th>";
						htm+="<th onclick='SortTable(1)' scope='col'>Problem</th>";
						htm+="<th onclick='SortTable(2)'scope='col'>Problem Type</th>";;
						htm+="<th onclick='SortTable(3)'scope='col'>Operating System</th>";
						htm+="<th onclick='SortTable(4)'scope='col'>Software Concerned</th>";
						htm+="<th onclick='SortTable(" + 3+extraCells + ")'scope='col'>Specialist</th>";
						htm+="<th onclick='SortTable(" + 4+extraCells + ")'scope='col'>Resolved</th>";
						htm+="<th onclick='SortTable(" + 5+extraCells + ")'scope='col'>Date/Time Resolved</th>";
						htm+="<th onclick='SortTable(" + 6+extraCells + ")'scope='col'>Solution</th></tr>"; //Appending column headers.
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							htm += "<tr style='background-color:rgb(159, 255, 48);'>"; //Sets colour and ID of row.
							htm +="<td>"+json[i].problemNumber+"</td>";
							htm +="<td>"+json[i].problem+"</td>";
							htm +="<td>"+json[i].problemSubType+"</td>";		
							htm +="<td>"+json[i].operatingSystem+"</td>";
							htm +="<td>"+json[i].softwareConcerned+"</td>";
							htm +="<td>"+json[i].specialistID+"</td>";
							htm +="<td>"+json[i].resolved+"</td>";
							htm +="<td>"+json[i].dateTimeResolved+"</td>";
							htm +="<td>"+json[i].solution+"</td>";
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
			
			function GetResolvedAsBool(resolved) //Gets the resolved value from a table as a string and returns a boolean.
			{
				if (resolved == "Yes")
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			
			function GetResolvedAsString(resolved) //Gets the resolved value from the checkbox as a boolean and returns a string.
			{
				if (resolved == true)
				{
					return "Yes";
				}
				else
				{
					return "No";
				}
			}
			
			function ChangeTab(tab)
			{
				switch (tab)
				{
					default: break;
				}
			}
			
			function CheckIfUpdate() //Prevents user input if more or less than one row is selected.
			{
				if (selected == 1)
				{
					rowNum = GetSelectedRow(); //Gets the row that is selected.
					document.getElementById("btnUpdate").disabled = false;
					document.getElementById("txtSolution").disabled = false;
					document.getElementById("txtSolution").value = document.getElementById("tbl").rows[rowNum].cells[6+extraCells].innerHTML;
				}
				else
				{
					document.getElementById("btnUpdate").disabled = true;
					document.getElementById("txtSolution").disabled = true;
					document.getElementById("txtSolution").value = "";
				}
			}
			
			function ValidateInput() //Function returns true if the data input box is valid.
			{
				id = "txtSolution";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'") || document.getElementById(id).value.length > 2047)
				{
					alert("Invalid solution."); //Returns error if data input from text box is invalid.
					return false;
				}
				return true;
			}
		</script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"> <!-- Bootstrap CSS stylesheet. -->
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
		<style>
		.table-wrapper-scroll-y
		{
			display: block;
			max-height:88vh;
			overflow-y: auto;
			-ms-overflow-style: -ms-autohiding-scrollbar;
		}
		</style>
	</head>
	<body onload="Load()" style="height:100%;">
		<div autocomplete="off" class="container-fluid"> <!-- Container holds elements together using Bootstrap. -->
			<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
				<input type='text' hidden id="user" name="User"/> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
				@csrf <!--Token to validates requests to server. -->
				<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
					<input type="button" id="btnBack" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" /> <!-- Back button. -->
					<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label> <!-- Label to contain current data/time. -->
					<h2 id="headerId" style="font-weight:bold; style=display:inline-block; font-size:30px;">Problem List</h2> <!-- Heading containing name of page. -->
				</div>
				<br/><br/>
				<div class="row" align="center">
					<div id="tableDiv" class="col-9 table-wrapper-scroll-y"> <!-- Div containing data table. -->
						Loading data...
					</div>
					<br/>
					<div id="rightDiv" align="center" class="col-3">
						<div id="searchDiv">
							<p>
								Search:<input id="txtSearch" type="text" oninput="ResetTable()"></input> <!-- Box for searching the table for specific strings. -->
								<input type="button" class="btn" id="btnSearch" value="Submit" onclick="Search()"></input> <!-- Submits search on press. -->
							</p>
						</div>
						<div id="inputDiv">
							<input type="button" class="btn" id="btnDelete" value="Delete Selected Items" id="del" style="font-size:16px;" onclick="Delete()"/><br/><br/> <!-- Delete button that calls function when pressed. -->
							Solution:<br/><textArea class="form-control" rows="10" id="txtSolution" maxlength="2048"></textArea><br/> <!-- Input fields for adding a new row. -->
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