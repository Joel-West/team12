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
			var extraCells = -1; //Refers to the numbers of extra cells in the table for the current problem category (software = 2, hardware = 1, network = 0).
			var specialists = [];
			var problemTypes = [];
			var allSpecialisations = [];
			var allProblemTypes = [];
			var allSerialNumbers = [];
			
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				SetPrivileges(userData) //Enter function that defines what functions are available to user based on status.
				WriteTime(); //Function that writes the current time at the top of the page.
				RunQuery(""); //Runs function get gets data from database and display it in the three tableDivs.
			}
			
			function SetPrivileges(userData) //Function that checks if user is an admin, analyst or specialist and adjusts available buttons accordingly.
			{
				admin = (userData.split(","))[2]; //Retrieves statuses from userData that was earlier posted from previous form.
				analyst = (userData.split(","))[3];
				specialist = (userData.split(","))[4];
				if (admin == 0 && analyst == 1)
				{
					$("#inputDiv :input").prop("disabled", true); //If not admin or operator, disable all input fields in the input div.
				}
				if (specialist != 1)
				{
					document.getElementById("lblAllProblems").style.display = "none";
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
				if (str.includes('"')) //If contains " (if it is SQL injection-prone).
				{
					sql = "SELECT * FROM tblProblem WHERE 1 = 0;"; //Get no results.
				}
				else
				{	
					str = str.replace(", ", ",").split(","); //Split search text by commas.
					switch (extraCells)
					{
						case 0: sql = "SELECT * FROM tblProblem WHERE problemType = 'Network' AND ("; break;
						case 1: sql = "SELECT * FROM tblProblem WHERE problemType = 'Hardware' AND ("; break;
						case 2: sql = "SELECT * FROM tblProblem WHERE problemType = 'Software' AND ("; break;
					}
					for (i = 0; i < str.length; i++) //Iterates through list of search terms, adding to the SQL query.
					{
						if (i != 0)
						{
							sql+=" OR ";
						}
						sql += "upper(problemNumber) LIKE '%"+str[i]+"%' OR upper(problem) LIKE '%"+str[i]+"%' OR upper(problemType) LIKE '%"+str[i]+"%' OR upper(problemSubType) LIKE '%"+str[i]+"%' OR upper(serialNumber) LIKE '%"+str[i]+"%' OR upper(operatingSystem) LIKE '%"+str[i]+"%' OR upper(softwareConcerned) LIKE '%"+str[i]+"%' OR upper(specialistID) LIKE '%"+str[i]+"%' OR upper(resolved) LIKE '%"+str[i]+"%' OR upper(dateTimeResolved) LIKE '%"+str[i]+"%' OR upper(solution) LIKE '%"+str[i]+"%'"; //Query that returns all database records with a cell containing search string.
					}
					sql+=")";
				}
				console.log(sql);
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
										htm += "<tr class='rowDeselected'>"; //Sets class (deselected) of row.
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
						GetArrays();
						if (specialist == 1)
						{
							HideRows(); //If user is a specialist, hide problems that are not assigned to them.
						}
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
								htm += "<tr class='rowDeselected'>"; //Sets class (deselected) of row.
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
			
			function ShowCallHistory() //Displays call history of selected row in a table below the problem table.
			{
				sql = "SELECT tblCallHistory.*, p1.name AS operatorName, p2.name AS callerName FROM tblCallHistory LEFT JOIN tblPersonnel p1 ON tblCallHistory.operatorID = p1.userID LEFT JOIN tblPersonnel p2 ON tblCallHistory.CallerID = p2.userID WHERE tblCallHistory.problemNumber = " + document.getElementById(GetCurrentTableID(extraCells)).rows[GetSelectedRow()].cells[0].innerHTML + ";"; //Query to get all data from table, using left joins to get names in other tables from IDs.
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{				
						var htm = "<table class='table' id='tblCallHistory' border='1'>";
						htm+="<tr id='t0'><th onclick='SortTable(0)' scope='col'>#</th>";
						htm+="<th scope='col'>Operator</th>";
						htm+="<th scope='col'>Caller</th>";
						htm+="<th scope='col'>Time/Date</th>";
						htm+="<th 'scope='col'>Notes</th></tr>"; //Appending column headers.
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							htm += "<tr id='callRow' class='rowDeselected'>"; //Set ID and class (deselected) of row.
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
							htm +="<td>"+json[i].notes+"</td>";
							htm += "</tr>";							
						}
					}
					else
					{
						var htm = "Sorry, no results found..."; //If no results, display error.
					}
					document.getElementById("callHistoryDiv").innerHTML = htm; //Appends HTML to tableDiv.
				},'json');
			}
			
			function AllProblemsClicked() //Runs when the 'show all problems' checkbox is pressed when the user is a specialist.
			{
				box = document.getElementById("chkAllProblems");
				if (box.checked)
				{
					ShowRows(); //If checked, show all rows.
				}
				else
				{
					HideRows(); //If unchecked, hide rows not associated with current specialist.
				}
			}
			
			function ShowRows() //Makes all rows of the table visible.
			{
				for (i = 0; i < 3; i++) //Iterates through each of the three tables in the tabs.
				{
					rows = document.getElementById(GetCurrentTableID(i)).rows;
					if (rows[0].style.display == "none") //If header was hidden from there being no relevant rows earlier.
					{
						rows[0].style.display = "";
						rows[0].innerHTML.replace("There are no problems to show for this type...", "");
					}
					for (j = 1; j < rows.length; j++) //Iterates through each row in the table.
					{
						if (rows[j].style.display = "none")
						{
							rows[j].style.display = ""; //Makes every row visible.
						}
					}
				}
			}
			
			function HideRows() //Hides all rows not associated with the specialist that is currently logged in.
			{
				for (i = 0; i < 3; i++) //Iterates through each of the three tables in the tabs.
				{
					rows = document.getElementById(GetCurrentTableID(i)).rows;
					for (j = 1; j < rows.length; j++) //Iterates through each row in the table.
					{
						hiddenNum = 0;
						if (rows[j].style.display == "" && rows[j].cells[i+3].innerHTML != userData.split(",")[1])
						{
							if (rows[j].classList.contains("rowSelected")) //If selected.
							{
								rows[j].classList.replace("rowSelected", "rowDeselected") //Deselect row.
								selected-=1;
								if (selected == 0)
								{
									document.getElementById("callHistoryDiv").innerHTML = ""; //Hides call history if no problem is now selected.
								}
							}
							rows[j].style.display = "none"; //Makes rows assigned to other specialist invisible.
							hiddenNum+=1;
						}
					}
					if (hiddenNum == rows.length-1) //If all rows are hidden, hide header row and show error.
					{
						rows[0].style.display = "none";
						document.getElementById(GetCurrentTableID(i)).innerHTML+="There are no problems to show for this type...";
					}
				}
			}
			
			function GetArrays() //Function to get array of all the serial numbers, specialists and problem types.
			{
				sql = "SELECT * FROM tblEquipment;";
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							allSerialNumbers[i] = json[i].serialNumber + " (" + json[i].equipmentMake + " " + json[i].equipmentType + ")";
						}
					}
				},'json');
				sql = "SELECT * FROM tblProblemType;";
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							allProblemTypes[i] = json[i];
						}
					}
				},'json');
				sql = "SELECT tblPersonnel.userID, tblPersonnel.name, tblSpecialisation.typeName FROM tblPersonnel INNER JOIN tblSpecialisation ON tblPersonnel.userID = tblSpecialisation.userID WHERE tblPersonnel.specialist = 'Yes';";
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							allSpecialisations[i] = json[i];
						}
					}
					ChangeTab("Hardware", true);
					CheckIfUpdate();
				},'json');
			}
			
			function GetProblemTypeArray() //Function to get array of all the valid problem types for the current tab.
			{
				problemTypes = [];
				switch (extraCells)
				{
					case 0: FindAllProblemTypeChildren("Network problem"); break;
					case 1: FindAllProblemTypeChildren("Hardware problem"); break;
					case 2: FindAllProblemTypeChildren("Software problem"); break;
				}
			}
			
			function FindAllProblemTypeChildren(parent) //Give it a generalisation and it will find all problem types which stem from this generalisation.
			{
				problemTypes.push(parent);
				for (var i = 0; i < allProblemTypes.length; i++) //Iterates through array of all problem types to find types with the given generalisation.
				{
					if (allProblemTypes[i].generalisation == parent)
					{
						FindAllProblemTypeChildren(allProblemTypes[i].typeName); //Re-runs the function but with the newly discovered problem type as a generalisation.
					}
				}
			}
			
			function GetSpecialistArray() //Function to get array of all the valid specialists for the current tab.
			{
				specialists = [];
				if (txtProblemType.value != null)
				{
					FindAllSpecialisationsOfChildren(txtProblemType.value);
				}
			}
			
			function FindAllSpecialisationsOfChildren(child) //Give it a problem type generalisation and it will find all specialists for this generalisation.
			{
				for (i = 0; i < allSpecialisations.length; i++) //Iterates through the list of specialists to find which specialists are applicaable for this generalisation.
				{
					if (allSpecialisations[i].typeName == child && !DoesSpecialistExist(allSpecialisations[i].userID))
					{
						specialists[specialists.length] = allSpecialisations[i].userID + " - " + allSpecialisations[i].name + " (" + allSpecialisations[i].typeName + ")";
					}
				}
				for (var i = 0; i < allProblemTypes.length; i++) //Iterates through array of all problem types to find types with the given generalisation.
				{
					if (allProblemTypes[i].typeName == child)
					{
						parent = allProblemTypes[i].generalisation;
						if (parent!=null)
						{
							FindAllSpecialisationsOfChildren(parent); //Re-runs the function but with the newly discovered problem type as a generalisation.
						}
					}
				}
			}
			
			function GetIDFromSelBoxItem(item) //Takes an item from a selection box (ID + name or serial number + make + type) and returns just the ID or number. 
			{
				return (item.split(" "))[0]
			}
			
			function DoesSpecialistExist(id) //Function returns true if the selected specialists is already in the list of valid specialists.
			{
				for (i = 0; i<specialists.length; i++)
				{
					if (specialists[i].includes(id))
					{
						return true;
					}
				}
				return false;
			}
			
			function IsValidProblemType(item) //Returns true if problem type is in the list of valid problem types.
			{
				for (i = 0; i < problemTypes.length; i++) //Iterates through all problem types that are valid.
				{
					if (problemTypes[i] == item)
					{
						return true;
					}
				}
				return false;
			}
			
			function IsValidSpecialist(item) //Returns true if specialist is in the list of all valid specialists.
			{
				for (i = 0; i < specialists.length; i++) //Iterates through all valid specialist IDs that exist in the personnel table.
				{
					if (GetIDFromSelBoxItem(specialists[i]) == item)
					{
						return true;
					}
				}
				return false;
			}
			
			function IsValidSerialNumber(item) //Returns true if serial number is in the list of all serial numbers.
			{
				for (i = 0; i < allSerialNumbers.length; i++) //Iterates through all serial numbers.
				{
					if (GetIDFromSelBoxItem(allSerialNumbers[i]) == item)
					{
						return true;
					}
				}
				return false;
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
						htm+="<option>"+problemTypes[i]+"</option>"; //Problem type can be selected as a problem type for a a problem.
					}
				}
				selBox.innerHTML=htm; //Appends values to selection box.
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
				if (problemTypeBox.value.length > 0) //If the text box contains results, give the label the number of results.
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
				if (problemTypeBox.value != selBox.value) 
				{
					txtSpecialist.value = ""; //If problem type not valid, clear specialist text box.
				}
				GetSpecialistArray(); //Repopulates array of viable specialists based on new problem type.
				PopulateSpecialistSelect();
			}
			
			function PopulateSpecialistSelect() //Populates selection box with specialist IDs/names based on searched text.
			{
				specialistBox = document.getElementById("txtSpecialist");
				selBox = document.getElementById("selSpecialist");
				if (specialists.length == 0) //If there are no results, hide selection box.
				{
					selBox.style.display = "none";
					lbl.style.display = "none";
				}
				else
				{
					selBox.style.display = "inline";
					lbl.style.display = "inline";
				}
				htm = "<option></option>";
				size = 0; //Stores size of selection box.
				matchIndex = -1; //Will be assigned to a natural number if any of the IDs from the specialists list match exactly with the text box input.
				for (i = 0; i < specialists.length; i++) //Iterates through all specialist IDs that exist in the personnel table.
				{
					if (specialistBox.value != null)
					{
						if (specialists[i].toUpperCase().includes(specialistBox.value.toUpperCase()) || specialistBox.value == "")
						{
							size+=1;
							if (GetIDFromSelBoxItem(specialists[i]) == specialistBox.value)
							{
								matchIndex = size; //If the user has input an exact match, assign the variable defining what the default value for the box will be.
							}
							htm+="<option>"+specialists[i]+"</option>"; //Specialist can be selected as a specialist for a problem.
						}
					}
				}
				selBox.innerHTML=htm; //Appends values to selection box.
				if (matchIndex != -1)
				{
					selBox.selectedIndex = matchIndex;
				}
				lbl = document.getElementById("lblSpecialistNum");
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
			
			function PopulateSerialNumberSelect() //Populates selection box with equipment info based on searched text.
			{
				serialBox = document.getElementById("txtSerialNumber");
				selBox = document.getElementById("selSerialNumber");
				if (allSerialNumbers.length == 0) //If there are no results, hide selection box.
				{
					selBox.style.display = "none";
					lbl.style.display = "none";
				}
				else
				{
					selBox.style.display = "inline";
					lbl.style.display = "inline";
				}
				htm = "<option></option>";
				size = 0; //Stores size of selection box.
				matchIndex = -1; //Will be assigned to a natural number if any of the IDs from the serial number list match exactly with the text box input.
				for (i = 0; i < allSerialNumbers.length; i++) //Iterates through all serial numbers that exist in the serial number table.
				{
					if (serialBox.value != null)
					{
						if (allSerialNumbers[i].toUpperCase().includes(serialBox.value.toUpperCase()) || serialBox.value == "")
						{
							size+=1;
							if (GetIDFromSelBoxItem(allSerialNumbers[i]) == serialBox.value)
							{
								matchIndex = size; //If the user has input an exact match, assign the variable defining what the default value for the box will be.
							}
							htm+="<option>"+allSerialNumbers[i]+"</option>"; //Serial number can be selected as a numberr for a problem.
						}
					}
				}
				selBox.innerHTML=htm; //Appends values to selection box.
				if (matchIndex != -1)
				{
					selBox.selectedIndex = matchIndex;
				}
				lbl = document.getElementById("lblSerialNumberNum");
				if (serialBox.value.length > 0) //If the text box contains results, give the label the number of results.
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
				row = document.getElementById(GetCurrentTableID(extraCells)).rows[GetSelectedRow()]; //Gets the details of the row that is selected.
				row.cells[2].innerHTML = "";
				row.cells[3+extraCells].innerHTML = "";
				for (i = 0; i<extraCells; i++) //Clears tab-specific fields.
				{
					row.deleteCell(3);
				}
				for (i = 0; i<newExtraCells; i++) //Add new empty cells based on tab that the record is being moved to.
				{
					row.insertCell(3);
				}
				
				rowData = row.innerHTML; //Gets the details of the row that is selected.
				document.getElementById(GetCurrentTableID(extraCells)).deleteRow(GetSelectedRow()); //Delete the row from the current tab.
				
				switch (newExtraCells) //Changes tab to the tab that the record will be moved to.
				{
					case 0: ChangeTab("Network", false); break;
					case 1: ChangeTab("Hardware", false); break;
					case 2: ChangeTab("Software", false); break;
				}
				table = document.getElementById(GetCurrentTableID(extraCells));
				table.tBodies[0].innerHTML += "<tr>"+rowData+"</tr>"; //Adds row data to new tab after being removed from another tab.
				table.rows[table.rows.length-1].classList.replace("rowDeselected", "rowSelected"); //Reselects the row now that it has been moved.
				selected = 1;
				CheckIfUpdate();
				if (!ListContains(updList, row.cells[0].innerHTML)) //If moved row is not already marked to be updated when changes are saved to the database later.
				{
					updList.push(row.cells[0].innerHTML); //Add the ID of the row to the list of rows to be updated when changes are commited to the actual database.
					console.log(updList);
				}
			}

			function SpecialistOptionClicked() //Sets specialist text box value to selected option in selection box.
			{
				value = GetIDFromSelBoxItem(document.getElementById("selSpecialist").value);
				document.getElementById("txtSpecialist").value = value;
				if (value == "")
				{
					PopulateSpecialistSelect();
				}
			}	
			
			function ProblemTypeOptionClicked() //Sets problem type text box value to selected option in selection box.
			{
				document.getElementById("txtProblemType").value = document.getElementById("selProblemType").value;
				GetSpecialistArray(); //Repopulates array of viable specialists based on new problem type.
				PopulateSpecialistSelect();
			}
			
			function SerialNumberOptionClicked() //Sets serial number text box value to selected option in selection box.
			{
				value = GetIDFromSelBoxItem(document.getElementById("selSerialNumber").value);
				document.getElementById("txtSerialNumber").value = value;
				if (value == "")
				{
					PopulateSerialNumberSelect();
				}
			}				
				
			function CheckClicked() //Function that checks if the 'resolved' checkbox is selected, and thus if the 'date-time' and 'solution' input boxes should be visible.
			{
				box = document.getElementById("chkResolved");
				div = document.getElementById("solutionDiv");
				if (box.checked)
				{
					div.style.display = "inline";
					if (GetSelectedRow() != -1 && document.getElementById(GetCurrentTableID(extraCells)).rows[GetSelectedRow()].cells[5+extraCells].innerHTML == "") //If newly resolved problem is selected.
					{
						var resolvedDT = new Date();
						resolvedOptions = {day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: false} //Sets the time format.
						document.getElementById("txtDateTime").value = resolvedDT.toLocaleDateString("en-GB", resolvedOptions); //Assigns time to label.
					}
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
			
			function GetCurrentTableID(cells) //Returns the ID associated with the number of extra cells given (usually the current tab).
			{
				switch (cells)
				{
					case 0: return "tblNetwork"; break;
					case 1: return "tblHardware"; break;
					case 2: return "tblSoftware"; break;
				}
			}
			
			function GetCurrentTableDivID() //Returns the ID of current tab's table div.
			{
				switch (extraCells)
				{
					case 0: return "tableDivNetwork"; break;
					case 1: return "tableDivHardware"; break;
					case 2: return "tableDivSoftware"; break;
				}
			}
			
			function GetTableWithID(id) //Takes a row ID and returns which table it in is.
			{			
				for (j = 0; j < 3; j++)
				{
					returnTable = GetCurrentTableID(j);
					for (k = 0; k<document.getElementById(returnTable).rows.length; k++)
					{
						if (document.getElementById(returnTable).rows[k].cells[0].innerHTML == id)
						{
							return returnTable;
						}
					}
				}
			}
			
			function DeselectAllRows(tabID) //Deselects all rows before leaving a tab.
			{
				rows = GetRows();
				for (i = rows-1; i > 0; i--) //Iterate through the rows of the table.
				{
					document.getElementById(GetCurrentTableID(extraCells)).rows[i].classList.replace("rowSelected", "rowDeselected");
				}
				selected = 0;
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
				if (GetCurrentTableDivID() != null)
				{
					document.getElementById(GetCurrentTableDivID()).style.display = "none";
					DeselectAllRows(GetCurrentTableID(extraCells));
				}
				switch (tab)
				{
					case 'Hardware':
						extraCells = 1; //There is one extra cell appended to the table when on the hardware tab (serial number).
						document.getElementById("btnHardware").style="text-decoration: underline;"; //Underlines selected tab.
						htm+='Serial Number: <br/><input id="txtSerialNumber" type="text" onkeyup="PopulateSerialNumberSelect()"></input><br/>';
						htm+='<select id="selSerialNumber" onchange="SerialNumberOptionClicked()" class="greenBack">'
						htm+='</select><br/>'
						htm+='<label id="lblSerialNumberNum"></label>'			
						break;
					case 'Software':
						extraCells = 2; //There are two extra cells appended to the table when on the software tab (operating system, software concerned).
						document.getElementById("btnSoftware").style="text-decoration: underline;"; //Underlines selected tab.
						htm+='Operating System:<br/><input id="txtOperatingSystem" type="text"></input><br/>'
						htm+='Software Concerned:<br/><input id="txtSoftwareConcerned" type="text"></input><br/>';
						break;
					case 'Network':
						extraCells = 0; //There are no extra cells appended to the table when on the network tab.
						document.getElementById("btnNetwork").style="text-decoration: underline;"; //Underlines selected tab.
						break;
					default: break;
				}
				document.getElementById(GetCurrentTableDivID()).style.display = "inline";
				document.getElementById("typeSpecificDiv").innerHTML = htm; //Appends innerHTML for the input elements that change depending on the tab.
				GetProblemTypeArray();
				CheckIfUpdate() //Prevents user input if more or less than one row is selected.
				if (buttonPressed) //If entered via a button press, rather than my changing the tab of a record, set 'selected' to 0. Otherwise, it will remain at 1.
				{
					selected = 0;
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
					document.getElementById("txtProblem").value = document.getElementById(GetCurrentTableID(extraCells)).rows[rowNum].cells[1].innerHTML;
					document.getElementById("txtProblemType").disabled = false;
					document.getElementById("txtProblemType").value = document.getElementById(GetCurrentTableID(extraCells)).rows[rowNum].cells[2].innerHTML;
					document.getElementById("selProblemType").disabled = false;
					document.getElementById("txtSpecialist").disabled = false;
					GetSpecialistArray(); //Repopulates array of viable specialists based on new problem type.
					document.getElementById("txtSpecialist").value = document.getElementById(GetCurrentTableID(extraCells)).rows[rowNum].cells[3+extraCells].innerHTML;
					document.getElementById("selSpecialist").disabled = false;
					document.getElementById("chkResolved").disabled = false;
					document.getElementById("chkResolved").checked = GetResolvedAsBool(document.getElementById(GetCurrentTableID(extraCells)).rows[rowNum].cells[4+extraCells].innerHTML);
					document.getElementById("txtDateTime").value = document.getElementById(GetCurrentTableID(extraCells)).rows[rowNum].cells[5+extraCells].innerHTML;
					document.getElementById("txtSolution").disabled = false;
					document.getElementById("txtSolution").value = document.getElementById(GetCurrentTableID(extraCells)).rows[rowNum].cells[6+extraCells].innerHTML;
					if (extraCells == 1)
					{
						document.getElementById("txtSerialNumber").disabled = false;
						document.getElementById("txtSerialNumber").value = document.getElementById(GetCurrentTableID(extraCells)).rows[rowNum].cells[3].innerHTML;
						document.getElementById("selSerialNumber").disabled = false;
					}
					else if (extraCells == 2)
					{
						document.getElementById("txtOperatingSystem").disabled = false;
						document.getElementById("txtOperatingSystem").value = document.getElementById(GetCurrentTableID(extraCells)).rows[rowNum].cells[3].innerHTML;
						document.getElementById("txtSoftwareConcerned").disabled = false;
						document.getElementById("txtSoftwareConcerned").value = document.getElementById(GetCurrentTableID(extraCells)).rows[rowNum].cells[4].innerHTML;
					}
					ShowCallHistory(); //Calls function to show call history of selected problem.
				}
				else
				{
					document.getElementById("btnUpdate").disabled = true;
					document.getElementById("selMainType").disabled = true;
					document.getElementById("txtProblem").disabled = true;
					document.getElementById("txtProblem").value = "";
					document.getElementById("txtProblemType").disabled = true;
					document.getElementById("txtProblemType").value = "";
					document.getElementById("selProblemType").disabled = true;
					document.getElementById("txtSpecialist").disabled = true;
					document.getElementById("txtSpecialist").value = "";
					specialists=[]; //Clears list of valid specialists.
					document.getElementById("selSpecialist").disabled = true;
					document.getElementById("chkResolved").disabled = true;
					document.getElementById("chkResolved").checked = false;
					document.getElementById("txtDateTime").value = "";
					document.getElementById("txtSolution").disabled = true;
					document.getElementById("txtSolution").value = "";
					if (extraCells == 1)
					{
						document.getElementById("txtSerialNumber").disabled = true;
						document.getElementById("txtSerialNumber").value = "";
						document.getElementById("selSerialNumber").disabled = true;
					}
					else if (extraCells == 2)
					{
						document.getElementById("txtOperatingSystem").disabled = true;
						document.getElementById("txtOperatingSystem").value = "";
						document.getElementById("txtSoftwareConcerned").disabled = true;
						document.getElementById("txtSoftwareConcerned").value = "";
					}
					document.getElementById("callHistoryDiv").innerHTML = ""; //Hides call history if no problem is selected.
				}
				switch (extraCells) //After a selection, in any state of the page, the main type selection box will correlate with the tab.
				{
					case 0: document.getElementById("selMainType").value="Network Problem"; break;
					case 1: document.getElementById("selMainType").value="Hardware Problem"; break;
					case 2: document.getElementById("selMainType").value="Software Problem"; break;
				}
				CheckClicked();
				PopulateProblemTypeSelect();
				PopulateSpecialistSelect();
				if (extraCells == 1)
				{
					PopulateSerialNumberSelect();
				}
			}
			
			function ValidateInput() //Function returns true if the data input box is valid.
			{
				id = "txtProblem";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes('"'))
				{
					alert("Invalid problem name."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtProblemType";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes('"') || !IsValidProblemType(document.getElementById(id).value))
				{
					alert("Invalid problem type."); //Returns error if data input from text box is invalid.
					return false;
				}	
				id = "txtSpecialist";
				if (document.getElementById(id).value == false || isNaN(document.getElementById(id).value) || document.getElementById(id).value.includes('"') || !IsValidSpecialist(document.getElementById(id).value))
				{
					alert("Invalid specialist."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtSolution";
				if (document.getElementById(id).value.includes('"') || document.getElementById(id).value.length > 2047)
				{
					alert("Invalid solution."); //Returns error if data input from text box is invalid.
					return false;
				}
				if (extraCells == 1)
				{
					id = "txtSerialNumber";
					if (document.getElementById(id).value == false || document.getElementById(id).value.includes('"') || !IsValidSerialNumber(document.getElementById(id).value))
					{
						alert("Invalid serial number."); //Returns error if data input from text box is invalid.
						return false;
					}	
				}
				else if (extraCells == 2)
				{
					id = "txtOperatingSystem";
					if (document.getElementById(id).value == false || document.getElementById(id).value.includes('"'))
					{
						alert("Invalid operating system."); //Returns error if data input from text box is invalid.
						return false;
					}
					id = "txtSoftwareConcerned";
					if (document.getElementById(id).value == false || document.getElementById(id).value.includes('"'))
					{
						alert("Invalid software concerned."); //Returns error if data input from text box is invalid.
						return false;
					}
				}
				return true;
			}
			
			function UpdateRow() //Function that updates the selected row.
			{
				if (!ValidateInput())
				{
					return;
				}
				row = document.getElementById(GetCurrentTableID(extraCells)).rows[GetSelectedRow()]; //Gets the details of the row that is selected.
				row.cells[1].innerHTML = document.getElementById("txtProblem").value;
				row.cells[2].innerHTML = document.getElementById("txtProblemType").value;
				switch (extraCells)
				{
					case 1:
						row.cells[3].innerHTML = document.getElementById("txtSerialNumber").value;
						break;
					case 2:
						row.cells[3].innerHTML = document.getElementById("txtOperatingSystem").value;
						row.cells[4].innerHTML = document.getElementById("txtSoftwareConcerned").value;
						break;
				}
				row.cells[extraCells+3].innerHTML = document.getElementById("txtSpecialist").value;
				row.cells[extraCells+4].innerHTML = GetResolvedAsString(document.getElementById("chkResolved").checked);
				if (document.getElementById("chkResolved").checked)
				{
					row.cells[extraCells+5].innerHTML = document.getElementById("txtDateTime").value;
					row.cells[extraCells+6].innerHTML = document.getElementById("txtSolution").value;
				}
				else
				{
					row.cells[extraCells+5].innerHTML = "";
					row.cells[extraCells+6].innerHTML = "";
				}
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
				if (confirm("Delete selected rows?")) //Get user confirmation.
				{
					rows = GetRows();
					for (i = rows-1; i > 0; i--) //Iterate through the rows of the table.
					{
						deleteRow = false; //Variable holding if row will actually be deleted.
						if (document.getElementById(GetCurrentTableID(extraCells)).rows[i].classList.contains("rowSelected")) //If row is selected.
						{
							deleteRow = true;						
						}
						if (deleteRow == true) //If should be deleted after validation.
						{
							indexInUpdList = updList.indexOf(document.getElementById(GetCurrentTableID(extraCells)).rows[i].cells[0].innerHTML); //Get index of deleted item in update list.
							if (indexInUpdList > -1)
							{
								updList.splice(indexInUpdList, 1); //Delete row from the update list - if record is deleted, it will not need to be updated.
							}
							delList.push(document.getElementById(GetCurrentTableID(extraCells)).rows[i].cells[0].innerHTML); //Add record id to list of rows that will be deleted from the actual database later.
							document.getElementById(GetCurrentTableID(extraCells)).deleteRow(i); //Delete the row.
						}
					}
					selected = 0;
					console.log(delList);
					CheckIfUpdate();
				}
			}
			
			function GetRowWithIDFromCertainTable(id, table) //Finds row with a given unique ID, given a certain table.
			{		
				for (j = 1; j<document.getElementById(table).rows.length; j++)
				{
					if (document.getElementById(table).rows[j].cells[0].innerHTML == id)
					{
						return j;
					}
				}
				return -1;
			}
			
			function SaveChanges() //Function that saves table data back to database.
			{
				admin = (userData.split(","))[2]; //Retrieves statuses from userData that was earlier posted from previous form.
				analyst = (userData.split(","))[3];
				if (admin == 0 && analyst == 1) //If is not an operator or an admin, action is forbidden.
				{
					return;
				}
				sql = '';
				for (i = 0; i < delList.length; i++) //Iterate through delete list (deletion performed first as it reduces database size, making other operations quicker).
				{
					sql+="UPDATE tblCallHistory SET problemNumber = NULL WHERE problemNumber = " + delList[i] + "; "
					sql+='DELETE FROM tblProblem WHERE problemNumber = ' + delList[i] + '; ';
				}
				for (i = 0; i < updList.length; i++)//Iterate through the update list.
				{
					problemNumber = updList[i];
					table = GetTableWithID(problemNumber);
					rowNum = GetRowWithIDFromCertainTable(problemNumber, table); //Gets the row number the correct local table that corresponds to the problem number in the updList.
					if (rowNum != -1) //If row exists.
					{
						row = document.getElementById(table).rows[rowNum]; //Get row of local table that is being saved to database.
						sql+='UPDATE tblProblem SET ';
						sql+='problem = "'+ row.cells[1].innerHTML + '", ';
						switch (table)
						{
							case 'tblNetwork':
								tempCells = 0;
								sql+='problemType = "Network", ';
								break;
							case 'tblHardware':
								tempCells = 1;
								sql+='problemType = "Hardware", ';
								sql+='serialNumber = "'+ row.cells[3].innerHTML + '", ';
								break;
							case 'tblSoftware':
								tempCells = 2;
								sql+='problemType = "Software", ';
								sql+='operatingSystem = "'+ row.cells[3].innerHTML + '", ';
								sql+='softwareConcerned = "'+ row.cells[4].innerHTML + '", ';
								break;
						}
						sql+='problemSubType = "'+ row.cells[2].innerHTML + '", ';
						sql+='specialistID = '+ row.cells[tempCells+3].innerHTML + ', ';
						sql+='dateTimeResolved = "'+ row.cells[tempCells+5].innerHTML + '", ';
						sql+='solution = "'+ row.cells[tempCells+6].innerHTML + '", ';
						sql+='resolved = "'+ row.cells[tempCells+4].innerHTML + '" ';
						sql+='WHERE problemNumber = ' + problemNumber + '; ';
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
					<label id="dtLabel"  class="dtLabel"></label> <!-- Label to contain current data/time. -->
					<h2 id="headerId">Problem List</h2> <!-- Heading containing name of page. -->
				</div>
				<br/><br/>
				<div class="row" align="center">
				<div id="leftDiv" align="center" class="col-9">
						<div id="tabDiv" class="row" align="center" style="text-align:center; display: inline-block;"> <!-- Within this row are three buttons that change the tab of problems listed. -->
							<input type="button" id="btnHardware" class="btn tabButton" value="Hardware" onclick="ChangeTab('Hardware', true)"></input>
							<input type="button" id="btnSoftware" class="btn tabButton" value="Software" onclick="ChangeTab('Software', true)"></input>
							<input type="button" id="btnNetwork" class="btn tabButton" value="Network" onclick="ChangeTab('Network', true)"></input>
							<label id="lblAllProblems" style="position:absolute; top:1%; right:2%;">Show all problems?&nbsp&nbsp<input id="chkAllProblems" type="checkbox" onclick="AllProblemsClicked()"></input></label><br/> <!-- Checkbox that appears when the user is a specialist, allows them to show only their own problems. -->
						</div>
						<br/>
						<div id="tableOuterDiv" class="table-wrapper-scroll-y">
							<div id="tableDivHardware" style="display:none"> <!-- Div containing hardware data table. -->
								Loading data...
							</div>
							<div id="tableDivSoftware" style="display:none"> <!-- Div containing software data table. -->
								Loading data...
							</div>
							<div id="tableDivNetwork" style="display:none"> <!-- Div containing network data table. -->
								Loading data...
							</div>
							<div id="callHistoryDiv"> <!-- Div containing call history data table for the current problem. -->
							</div>
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
						Resolved?&nbsp&nbsp<input id="chkResolved" type="checkbox" onclick="CheckClicked()"></input><br/>
						<div id="solutionDiv">
							<input id="txtDateTime" type="text" disabled></input><br/>
							Solution:<br/><textArea class="form-control text" rows="10" id="txtSolution" maxlength="2048"></textArea>
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