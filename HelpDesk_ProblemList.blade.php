<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_ProblemList</title>
		<link rel="icon" href="https://www.goodfreephotos.com/albums/vector-images/screwdriver-and-wrench-vector-clipart.png">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>	<!-- Importing Bootstrap files. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple pages. -->
		<script type="text/javascript">	
			
			/*switch (extraCells)
			{
				case 0: break;
				case 1: break;
				case 2: break;
			}*/
			
			var userData; //Variable containing data about user.
			var currentPage = "ProblemList"; //Variable storing the name of the current page, so it can be passed in the URL to the next page as a 'previous page' variable.
			var selected = 0; //Global variable corresponding to number of highlighted table rows.
			var extraCells = 1; //Refers to the numbers of extra cells in the table for the current problem category (software = 2, hardware = 1, network = 0).
			var specialists = [];
			var problemTypes = [];
			
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				SetPrivileges(userData) //Enter function that defines what functions are available to user based on status.
				WriteTime(); //Function that writes the current time at the top of the page.
				RunQuery(""); //Runs function get gets data from database and display it in the three tableDivs.
			}
			
			function SetPrivileges(userData) //Function that checks if user is an admin or analyst and adjusts available buttons accordingly.
			{
				admin = (userData.split(","))[2];
				analyst = (userData.split(","))[3]; //Retrieves admin and analyst status from userData that was earlier posted from previous form.
				if (admin == 0 && analyst == 1)
				{
					$("#inputDiv :input").prop("disabled", true); //If not admin or operator, disable all input fields in the input div.
				}	
			}
			
			function ResetTable()
			{
				if (document.getElementById("txtSearch").value == "") //If not searching anything.
				{
					switch (extraCells)
					{
						case 0: sql = "SELECT * FROM tblProblem WHERE problemType = 'Network';"; break;
						case 1: sql = "SELECT * FROM tblProblem WHERE problemType = 'Hardware';"; break;
						case 2: sql = "SELECT * FROM tblProblem WHERE problemType = 'Software';"; break;
					}
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
					switch (extraCells)
					{
						case 0: sql = "SELECT * FROM tblProblem WHERE problemType = 'Network' AND "; break;
						case 1: sql = "SELECT * FROM tblProblem WHERE problemType = 'Hardware' AND "; break;
						case 2: sql = "SELECT * FROM tblProblem WHERE problemType = 'Software' AND "; break;
					}
					for (i = 0; i < str.length; i++) //Iterates through list of search terms, adding to the SQL query.
					{
						if (i != 0)
						{
							sql+=" OR ";
						}
						sql += "(upper(problemNumber) LIKE '%"+str[i]+"%' OR upper(problem) LIKE '%"+str[i]+"%' OR upper(problemType) LIKE '%"+str[i]+"%' OR upper(problemSubType) LIKE '%"+str[i]+"%' OR upper(serialNumber) LIKE '%"+str[i]+"%' OR upper(operatingSystem) LIKE '%"+str[i]+"%' OR upper(softwareConcerned) LIKE '%"+str[i]+"%' OR upper(specialistID) LIKE '%"+str[i]+"%' OR upper(resolved) LIKE '%"+str[i]+"%' OR upper(dateTimeResolved) LIKE '%"+str[i]+"%' OR upper(solution) LIKE '%"+str[i]+"%')"; //Query that returns all database records with a cell containing search string.
					}
				}
				RunQuery(sql); //Runs function get gets data from database and display it in tableDiv.
			}
			
			function RunQuery(sql) //Function for running a query to the personnel table and getting building a table.
			{
				if (sql == "") //If no SQL, this indicates that all 3 tables should be built (when page loads).
				{
					sql = "SELECT * FROM tblProblem";
					$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
					{
						if(json && json[0]) //If result of php file was a json array.	
						{	
							for (tempCells = 0; tempCells < 3; tempCells++) //Iterate 3 times to create all three tables.
							{	
								switch (tempCells)
								{
									case 0: var htm = "<table class='table' id='tblNetwork' border='1'>"; break;
									case 1: var htm = "<table class='table' id='tblHardware' border='1'>"; break;
									case 2: var htm = "<table class='table' id='tblSoftware' border='1'>"; break;
								}				
								htm+="<tr id='t0'><th onclick='SortTable(0)' scope='col'>#</th>";
								htm+="<th onclick='SortTable(1)' scope='col'>Problem</th>";
								htm+="<th onclick='SortTable(2)'scope='col'>Problem Type</th>";;
								if (tempCells == 1)
								{
									htm+="<th onclick='SortTable(3)'scope='col'>Serial Number</th>";
								}
								else if (tempCells == 2)
								{
									htm+="<th onclick='SortTable(3)'scope='col'>Operating System</th>";
									htm+="<th onclick='SortTable(4)'scope='col'>Software Concerned</th>";
								}
								htm+="<th onclick='SortTable(" + (3+tempCells) + ")'scope='col'>Specialist</th>";
								htm+="<th onclick='SortTable(" + (4+tempCells) + ")'scope='col'>Solved</th>";
								htm+="<th onclick='SortTable(" + (5+tempCells) + ")'scope='col'>Date/Time Resolved</th>";
								htm+="<th onclick='SortTable(" + (6+tempCells) + ")'scope='col'>Solution</th></tr>"; //Appending column headers.
								for (i = 0; i<json.length; i++) //Iterates through the json array of results.
								{
									if ((json[i].problemType == "Hardware" && tempCells == 1) || (json[i].problemType == "Software" && tempCells == 2) || (json[i].problemType == "Network" && tempCells == 0)) //If of relevant problem type.
									{
										htm += "<tr style='background-color:rgb(159, 255, 48);'>"; //Sets colour and ID of row.
										htm +="<td>"+json[i].problemNumber+"</td>";
										htm +="<td>"+json[i].problem+"</td>";
										htm +="<td>"+json[i].problemSubType+"</td>";	
										if (tempCells == 1)
										{
											htm +="<td>"+json[i].serialNumber+"</td>";
										}
										else if (tempCells == 2)
										{
											htm +="<td>"+json[i].operatingSystem+"</td>";
											htm +="<td>"+json[i].softwareConcerned+"</td>";
										}
										htm +="<td>"+json[i].specialistID+"</td>";
										htm +="<td>"+json[i].resolved+"</td>";
										htm +="<td>"+json[i].dateTimeResolved+"</td>";
										htm +="<td>"+json[i].solution+"</td>";
										htm += "</tr>";
									}
								}
								switch (tempCells)
								{
									case 0: document.getElementById("tableDivNetwork").innerHTML = htm; break; //Appends HTML to the relevant tableDiv.
									case 1: document.getElementById("tableDivHardware").innerHTML = htm; break;
									case 2: document.getElementById("tableDivSoftware").innerHTML = htm; break;
								}
							}
						}
						else
						{
							var htm = "Sorry, no results found..."; //If no results, display error.
						}
						newRowCount = 0;
						ChangeTab("Hardware");
						CheckIfUpdate();
					},'json');
				}
				else
				{
					$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
					{
						if(json && json[0]) //If result of php file was a json array.	
						{				
							switch (extraCells)
							{
								case 0: var htm = "<table class='table' id='tblNetwork' border='1'>"; break;
								case 1: var htm = "<table class='table' id='tblHardware' border='1'>"; break;
								case 2: var htm = "<table class='table' id='tblSoftware' border='1'>"; break;
							}				
							htm+="<tr id='t0'><th onclick='SortTable(0)' scope='col'>#</th>";
							htm+="<th onclick='SortTable(1)' scope='col'>Problem</th>";
							htm+="<th onclick='SortTable(2)'scope='col'>Problem Type</th>";;
							if (extraCells == 1)
							{
								htm+="<th onclick='SortTable(3)'scope='col'>Serial Number</th>";
							}
							else if (extraCells == 2)
							{
								htm+="<th onclick='SortTable(3)'scope='col'>Operating System</th>";
								htm+="<th onclick='SortTable(4)'scope='col'>Software Concerned</th>";
							}
							htm+="<th onclick='SortTable(" + (3+extraCells) + ")'scope='col'>Specialist</th>";
							htm+="<th onclick='SortTable(" + (4+extraCells) + ")'scope='col'>Solved</th>";
							htm+="<th onclick='SortTable(" + (5+extraCells) + ")'scope='col'>Date/Time Resolved</th>";
							htm+="<th onclick='SortTable(" + (6+extraCells) + ")'scope='col'>Solution</th></tr>"; //Appending column headers.
							for (i = 0; i<json.length; i++) //Iterates through the json array of results.
							{
								htm += "<tr style='background-color:rgb(159, 255, 48);'>"; //Sets colour and ID of row.
								htm +="<td>"+json[i].problemNumber+"</td>";
								htm +="<td>"+json[i].problem+"</td>";
								htm +="<td>"+json[i].problemSubType+"</td>";	
								if (extraCells == 1)
								{
									htm +="<td>"+json[i].serialNumber+"</td>";
								}
								else if (extraCells == 2)
								{
									htm +="<td>"+json[i].operatingSystem+"</td>";
									htm +="<td>"+json[i].softwareConcerned+"</td>";
								}
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
						document.getElementById(GetCurrentTableDivID()).innerHTML = htm; //Appends HTML to tableDiv.
						newRowCount = 0;
					},'json');
				}
			}
			
			function GetArrays() //Function to get array of all the specialists and problem types.
			{
				sql = "SELECT userID, name FROM tblPersonnel WHERE specialist = 'Yes'";
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							specialists[i] = json[i].userID + " - " + json[i].name;
						}
						PopulateSpecialistSelect();
					}
				},'json');
				sql = "SELECT typeName FROM tblProblemType";
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							problemTypes[i] = json[i].typeName
						}
						PopulateProblemTypeSelect();
					}
				},'json');
			}
			
			function GetIDFromSelBoxItem(item) //Takes an item from a selection box (ID + name) and returns just the ID.
			{
				return (item.split(" "))[0]
			}
			
			function IsValidSpecialist(item) //Returns true if specialist is in the list of all specialists.
			{
				for (i = 0; i < specialists.length; i++) //Iterates through all specialist ids that exist in the personnel table.
				{
					if (GetIDFromSelBoxItem(specialists[i]) == item)
					{
						return true;
					}
				}
				return false;
			}
			
			function IsValidProblemType(item) //Returns true if problem type is in the list of all problem types.
			{
				for (i = 0; i < problemTypes.length; i++) //Iterates through all problem types that exists in the problem type table.
				{
					if (problemTypes[i] == item)
					{
						return true;
					}
				}
				return false;
			}
			
			function PopulateSpecialistSelect() //Populates selection box with specialist IDs/names based on searched text.
			{
				specialistBox = document.getElementById("txtSpecialist");
				selBox = document.getElementById("selSpecialist");
				htm = "<option></option>";
				size = 0; //Stores size of selection box.
				matchIndex = -1; //Will be assigned to a natural number if any of the IDs from the specialists list match exactly with the text box input.
				for (i = 0; i < specialists.length; i++) //Iterates through all specialist IDs that exist in the personnel table.
				{
					if (specialists[i].toUpperCase().includes(specialistBox.value.toUpperCase()) || specialistBox.value == "")
					{
						size+=1;
						if (GetIDFromSelBoxItem(specialists[i]) == specialistBox.value)
						{
							matchIndex = size; //If the user has input an exact match, assign the variable defining what the default value for the box will be.
						}
						htm+="<option>"+specialists[i]+"</option>"; //ID can be selected as an ID for a new user.
					}
				}
				selBox.innerHTML=htm; //Appends values to selection vox.
				if (matchIndex != -1)
				{
					selBox.selectedIndex = matchIndex;
				}
				lbl = document.getElementById("lblSpecialistNum");
				if (size == 0) //If there are no results, hide selection box.
				{
					selBox.style.display = "none";
					lbl.style.display = "none";
				}
				else
				{
					selBox.style.display = "inline";
					lbl.style.display = "inline";
				}
				if (specialistBox.value.length > 0) //If the text box contains results, give the label the number of results.
				{
					if (size == 1)
					{
						lbl.innerHTML = "(" + size + " result)";
					}
					else
					{
						lbl.innerHTML = "(" + size + " results)";
					}
				}
				else
				{
					lbl.innerHTML = "";
				}
			}
			
			function PopulateProblemTypeSelect() //Populates selection box with problem types based on searched text.
			{
				problemTypeBox = document.getElementById("txtProblemType");
				selBox = document.getElementById("selProblemType");
				htm = "<option></option>";
				size = 0; //Stores size of selection box.
				matchIndex = -1; //Will be assigned to a natural number if any of the types from the problemTypes list match exactly with the text box input.
				for (i = 0; i < problemTypes.length; i++) //Iterates through all problem types that exist in the problem type table.
				{
					if (problemTypes[i].toUpperCase().includes(problemTypeBox.value.toUpperCase()) || problemTypeBox.value == "")
					{
						size+=1;
						if (problemTypes[i] == problemTypeBox.value)
						{
							matchIndex = size; //If the user has input an exact match, assign the variable defining what the default value for the box will be.
						}
						htm+="<option>"+problemTypes[i]+"</option>"; //ID can be selected as an ID for a new user.
					}
				}
				selBox.innerHTML=htm; //Appends values to selection vox.
				if (matchIndex != -1)
				{
					selBox.selectedIndex = matchIndex;
				}
				lbl = document.getElementById("lblProblemTypeNum");
				if (size == 0) //If there are no results, hide selection box.
				{
					selBox.style.display = "none";
					lbl.style.display = "none";
				}
				else
				{
					selBox.style.display = "inline";
					lbl.style.display = "inline";
				}
				if (specialistBox.value.length > 0) //If the text box contains results, give the label the number of results.
				{
					if (size == 1)
					{
						lbl.innerHTML = "(" + size + " result)";
					}
					else
					{
						lbl.innerHTML = "(" + size + " results)";
					}
				}
				else
				{
					lbl.innerHTML = "";
				}
			}
			
			function MainTypeOptionClicked() //May move record from one tab to another.
			{
				if (selected != 1)
				{
					return; //If there isn't an item selected, leave function.
				}
				box = document.getElementById("selMainType");
				newExtraCells = -1;
				switch (box.value) //Gets new number of extra cells based on the input from the selection box (e.g. what tab the selected record in the table will be moved to).
				{
					case "Hardware Problem": newExtraCells = 1; break;
					case "Software Problem": newExtraCells = 2; break;
					case "Network Problem": newExtraCells = 0; break;
				}
				if (extraCells == newExtraCells)
				{
					return; //If it is on the correct tab already, leave function.
				}
				row = document.getElementById(GetCurrentTableID()).rows[GetSelectedRow()]; //Gets the details of the row that is selected.
				for (i = 0; i<extraCells; i++) //Clears tab-specific fields.
				{
					row.deleteCell(3);
				}
				for (i = 0; i<extraCells; i++) //Add new empty cells based on tab that the record is being moved to.
				{
					row.insertCell(3);
				}
				rowData = document.getElementById(GetCurrentTableID()).rows[GetSelectedRow()].innerHTML; //Gets the details of the row that is selected.
				document.getElementById(GetCurrentTableID()).deleteRow(GetSelectedRow()); //Delete the row from the current tab.
				tableDiv = document.getElementById(GetCurrentTableDivID());

				//Help
				
				TransferRow(rowData);
				if (!ListContains(updList, row.cells[0].innerHTML)) //If moved row is not already marked to be updated when changes are saved to the database later.
				{
					updList.push(row.cells[0].innerHTML); //Add the ID of the row to the list of rows to be updated when changes are commited to the actual database.
					console.log(updList);
				}
			}
			
			function TransferRow(rowData) //Adds row data to new tab after being removed from another tab.
			{
				table = document.getElementById(GetCurrentTableID);
				table.innerHTML += "<tr'>"+rowData+"</tr>";
			}
			
			function SpecialistOptionClicked() //Sets specialist text box value to selected option in selection box.
			{
				document.getElementById("txtSpecialist").value = GetIDFromSelBoxItem(document.getElementById("selSpecialist").value);
			}	
			
			function ProblemTypeOptionClicked() //Sets problem type text box value to selected option in selection box.
			{
				document.getElementById("txtProblemType").value = document.getElementById("selProblemType").value;
			}
			
			function CheckClicked() //Function that checks if the 'resolved' checkbox is selected, and thus if the 'date-time' and 'solution' input boxes should be visible.
			{
				box = document.getElementById("chkResolved");
				div = document.getElementById("solutionDiv");
				if (box.checked)
				{
					div.style.display = "inline";
				}
				else
				{
					div.style.display = "none";
				}
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
			
			function GetCurrentTableID() //Returns the ID of current tab's table.
			{
				console.log(extraCells);
				switch (extraCells)
				{
					case 0: return "tblNetwork"; break;
					case 1: return "tblHardware"; break;
					case 2: return "tblSoftware"; break;
				}
			}
			
			function GetCurrentTableDivID() //Returns the ID of current tab's table div.
			{
				console.log(extraCells);
				switch (extraCells)
				{
					case 0: return "tableDivNetwork"; break;
					case 1: return "tableDivHardware"; break;
					case 2: return "tableDivSoftware"; break;
				}
			}
			
			function ChangeTab(tab, buttonPressed) //Changes the current tab of problems (hardware, software or network).
			{
				if ((extraCells == 0 && tab == "Network") || (extraCells == 1 && tab == "Hardware") || (extraCells == 2 && tab == "Software"))
				{
					return; //If already on selected page, ignore request.
				}
				tableDiv = document.getElementById(GetCurrentTableDivID());
				document.getElementById("btnHardware").style="text-decoration: initial;"
				document.getElementById("btnSoftware").style="text-decoration: initial;"
				document.getElementById("btnNetwork").style="text-decoration: initial;"
				htm="";
				console.log(GetCurrentTableDivID());
				document.getElementById(GetCurrentTableDivID()).style.display = "none";
				switch (tab)
				{
					case 'Hardware':
						extraCells = 1; //There is one extra cell appended to the table when on the hardware tab (serial number).
						document.getElementById("btnHardware").style="text-decoration: underline;"; //Underlines selected tab.
						break;
					case 'Software':
						extraCells = 2; //There are two extra cells appended to the table when on the software tab (operating system, software concerned).
						document.getElementById("btnSoftware").style="text-decoration: underline;"; //Underlines selected tab.
						break;
					case 'Network':
						extraCells = 0; //There are no extra cells appended to the table when on the network tab.
						document.getElementById("btnNetwork").style="text-decoration: underline;"; //Underlines selected tab.
						break;
					default: break;
				}
				document.getElementById(GetCurrentTableDivID()).style.display = "inline";
				document.getElementById("typeSpecificDiv").text = htm; //Appends innerHTML for the input elements that change depending on the tab.
				if (buttonPressed) //If entered via a button press, rather than my changing the tab of a record, set 'selected' to 0. Otherwise, it will remain at 1.
				{
					CheckIfUpdate() //Prevents user input if more or less than one row is selected.
					selected = 0;
				}
				if ((extraCells == 0 && tab == "Network") || (extraCells == 1 && tab == "Hardware") || (extraCells == 2 && tab == "Software"))
				{
					return; //If already on selected page, ignore request.
				}
			}
			
			function CheckIfUpdate() //Prevents user input if more or less than one row is selected.
			{
				if (selected == 1)
				{
					rowNum = GetSelectedRow(); //Gets the row that is selected.
					document.getElementById("btnUpdate").disabled = false;
					document.getElementById("selMainType").disabled = false;
					document.getElementById("txtProblem").disabled = false;
					document.getElementById("txtProblem").value = document.getElementById(GetCurrentTableID()).rows[rowNum].cells[1].innerHTML;
					document.getElementById("txtProblemType").disabled = false;
					document.getElementById("txtProblemType").value = document.getElementById(GetCurrentTableID()).rows[rowNum].cells[2].innerHTML;
					document.getElementById("txtSpecialist").disabled = false;
					document.getElementById("txtSpecialist").value = document.getElementById(GetCurrentTableID()).rows[rowNum].cells[3+extraCells].innerHTML;
					document.getElementById("chkResolved").disabled = false;
					document.getElementById("chkResolved").checked = GetResolvedAsBool(document.getElementById(GetCurrentTableID()).rows[rowNum].cells[4+extraCells].innerHTML);
					document.getElementById("txtDateTime").value = document.getElementById(GetCurrentTableID()).rows[rowNum].cells[5+extraCells].innerHTML;
					document.getElementById("txtSolution").disabled = false;
					document.getElementById("txtSolution").value = document.getElementById(GetCurrentTableID()).rows[rowNum].cells[6+extraCells].innerHTML;
				}
				else
				{
					document.getElementById("btnUpdate").disabled = true;
					document.getElementById("selMainType").disabled = true;
					document.getElementById("txtProblem").disabled = true;
					document.getElementById("txtProblem").value = "";
					document.getElementById("txtProblemType").disabled = true;
					document.getElementById("txtProblemType").value = "";
					document.getElementById("txtSpecialist").disabled = true;
					document.getElementById("txtSpecialist").value = "";
					document.getElementById("chkResolved").disabled = true;
					document.getElementById("chkResolved").checked = false;
					document.getElementById("txtDateTime").value = "";
					document.getElementById("txtSolution").disabled = true;
					document.getElementById("txtSolution").value = "";
				}
				switch (extraCells) //After a selection, in any state of the page, the main type selection box will correlate with the tab.
				{
					case 0: document.getElementById("selMainType").value="Network Problem"; break;
					case 1: document.getElementById("selMainType").value="Hardware Problem"; break;
					case 2: document.getElementById("selMainType").value="Software Problem"; break;
				}
				CheckClicked();
				PopulateSpecialistSelect();
				PopulateProblemTypeSelect();
			}
			
			function ValidateInput() //Function returns true if the data input box is valid.
			{
				id = "txtProblem";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'"))
				{
					alert("Invalid problem name."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtSolution";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'") || document.getElementById(id).value.length > 2047)
				{
					alert("Invalid solution."); //Returns error if data input from text box is invalid.
					return false;
				}
				return true;
			}
			
			function Delete() //Function for deleting selected rows from a table.
			{
				admin = (userData.split(","))[2];
				admin = (userData.split(","))[3];
				if (selected == 0 || (admin == 0 && analyst == 1)) //if no rows are selected, or if not admin/operator, leave function.
				{
					return;
				}
				if (confirm("Delete selected rows?")) //Get user confirmation.
				{
					rows = GetRows();
					for (i = rows-1; i > 0; i--) //Iterate through the rows of the table.
					{
						deleteRow = false; //Variable holding if row will actually be deleted.
						if (document.getElementById(GetCurrentTableID()).rows[i].style.backgroundColor != 'rgb(159, 255, 48)') //If row is selected.
						{
							deleteRow = true;						
						}
						if (deleteRow == true) //If should be deleted after validation.
						{
							indexInUpdList = updList.indexOf(document.getElementById(GetCurrentTableID()).rows[i].cells[0].innerHTML); //Get index of deleted item in update list.
							if (indexInUpdList > -1)
							{
								updList.splice(indexInUpdList, 1); //Delete row from the update list - if record is deleted, it will not need to be updated.
							}
							delList.push(document.getElementById(document.getElementById(GetCurrentTableID())).rows[i].cells[0].innerHTML); //Add record id to list of rows that will be deleted from the actual database later.
							document.getElementById(GetCurrentTableID()).deleteRow(i); //Delete the row.
						}
					}
					selected = 0;
					tableDiv = document.getElementById("tableDiv");
					console.log(delList);
					CheckIfUpdate();
				}
			}
		</script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"> <!-- Bootstrap CSS stylesheet. -->
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
		<style>
		.table-wrapper-scroll-y
		{
			display: block;
			max-height:84vh;
			overflow-y: auto;
			-ms-overflow-style: -ms-autohiding-scrollbar;
		}
		.input-wrapper-scroll-y
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
				<div id="leftDiv" align="center" class="col-9">
						<div id="tabDiv" class="row" align="center" style="text-align:center; display: inline-block;"> <!-- Within this row are three buttons that change the tab of problems listed. -->
							<input type="button" id="btnHardware" class="btn tabButton" value="Hardware" onclick="ChangeTab('Hardware', true)"></input>
							<input type="button" id="btnSoftware" class="btn tabButton" value="Software" onclick="ChangeTab('Software', true)"></input>
							<input type="button" id="btnNetwork" class="btn tabButton" value="Network" onclick="ChangeTab('Network', true)"></input>
						</div>
						<br/>
						<div id="tableDivHardware" class="table-wrapper-scroll-y" style="display:none"> <!-- Div containing hardware data table. -->
							Loading data...
						</div>
						<div id="tableDivSoftware" class="table-wrapper-scroll-y" style="display:none"> <!-- Div containing software data table. -->
							Loading data...
						</div>
						<div id="tableDivNetwork" class="table-wrapper-scroll-y" style="display:none"> <!-- Div containing network data table. -->
							Loading data...
						</div>
					<br/>
				</div>
				<div id="rightDiv" align="center" class="col-3 input-wrapper-scroll-y">
					<div id="searchDiv">
						<p>
							Search:<input id="txtSearch" type="text" oninput="ResetTable()"></input> <!-- Box for searching the table for specific strings. -->
							<input type="button" class="btn" id="btnSearch" value="Submit" onclick="Search()"></input> <!-- Submits search on press. -->
						</p>
					</div>
					<div id="inputDiv">
						<input type="button" class="btn" id="btnDelete" value="Delete Selected Items" id="del" style="font-size:16px;" onclick="Delete()"/><br/><br/> <!-- Delete button that calls function when pressed. -->
						<select id="selMainType" onchange="MainTypeOptionClicked()" class="greenBack"> <!-- Allows user to move record from one tab to another. -->
							<option>Hardware Problem</option>
							<option>Software Problem</option>
							<option>Network Problem</option>
						</select>
						<br/>
						Problem:<br/><input id="txtProblem" type="text"></input><br/> <!-- Input fields for adding a new row. -->
						Problem Type:<br/><input id="txtProblemType" type="text" onkeyup="PopulateProblemTypeSelect()"></input><br/>					
						<select id="selProblemType" onchange="ProblemTypeOptionClicked()" class="greenBack"></select>
						<br/>
						<label id="lblProblemTypeNum"></label>
						<br/>
						<div id="typeSpecificDiv"></div> <!-- This div may contain input boxes, depending on the tab currently selected. -->
						Specialist:<br/><input id="txtSpecialist" type="text" onkeyup="PopulateSpecialistSelect()"></input><br/> <!-- Input fields for adding a new row.-->						
						<select id="selSpecialist" onchange="SpecialistOptionClicked()" class="greenBack"></select>
						<br/>
						<label id="lblSpecialistNum"></label>
						<br/>
						Resolved? <input id="chkResolved" type="checkbox" onclick="CheckClicked()"></input><br/>
						<div id="solutionDiv">
							<input id="txtDateTime" type="text" disabled></input><br/>
							Solution:<br/><textArea class="form-control" rows="10" id="txtSolution" maxlength="2048" style="background-color:rgb(159, 255, 48);"></textArea>
						</div>
						<br/>
						<input type="button" class="btn" id="btnUpdate" value="Update Item" style="font-size:16px;" onclick="UpdateRow()"></input>	
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