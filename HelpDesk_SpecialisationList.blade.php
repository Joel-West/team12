<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_SpecialisationList</title>
		<link rel="icon" href="https://www.goodfreephotos.com/albums/vector-images/screwdriver-and-wrench-vector-clipart.png">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>	<!-- Importing Bootstrap files. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple pages. -->
		<script type="text/javascript">	
			var userData; //Variable containing data about user.
			var currentPage = "SpecialisationList"; //Variable storing the name of the current page, so it can be passed in the URL to the next page as a 'previous page' variable.
			var selected = 0; //Global variable corresponding to number of highlighted table rows.
			var specialists = [];
			var problemTypes = [];
			
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				SetPrivileges(userData) //Enter function that defines what functions are available to user based on status.
				ResetTable();
				WriteTime(); //Function that writes the current time at the top of the page.
				GetArrays();
			}
			
			function SetPrivileges(userData) //Function that checks if user is an admin, analyst or specialist and adjusts available buttons accordingly.
			{
				admin = (userData.split(","))[2]; //Retrieves admin status from userData that was earlier posted from previous form.
				if (admin == 0)
				{
					$("#inputDiv :input").prop("disabled", true); //If not admin, disable all input fields in the input div.
				}
				SetNavSettings();
			}
			
			function ResetTable()
			{
				if (document.getElementById("txtSearch").value == "") //If not searching anything.
				{
					sql = "SELECT * FROM tblSpecialisation;"; //Simple query to get all data from table.
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
					sql = "SELECT * FROM tblSpecialisation WHERE 1 = 0;"; //Get no results.
				}
				else
				{
					
					str = str.replace(", ", ",").split(","); //Split search text by commas.
					sql = "SELECT * FROM tblSpecialisation WHERE ";
					for (i = 0; i < str.length; i++) //Iterates through list of search terms, adding to the SQL query.
					{
						if (i != 0)
						{
							sql+=" OR ";
						}
						sql += "upper(userID) LIKE '%"+str[i]+"%' OR upper(typeName) LIKE '%"+str[i]+"%'"; //Query that returns all database records with a cell containing search string.
					}
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
						htm+="<tr id='t0'><th onclick='SortTable(0)' scope='col'>ID</th>";
						htm+="<th onclick='SortTable(1)' scope='col'>Specialist</th>";
						htm+="<th onclick='SortTable(2)'scope='col'>Problem Type</th>";
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							htm += "<tr class='rowDeselected'>"; //Sets class (deselected) of row.
							htm +="<td>"+json[i].specialisationID+"</td>";
							htm +="<td>"+json[i].userID+"</td>";
							htm +="<td>"+json[i].typeName+"</td>";		
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
			
			function GetArrays() //Function to get array of all the specialists and problem types which the user could assign a new specialisation to.
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
				for (i = 0; i < specialists.length; i++) //Iterates through all specialist IDs that exist in the personnel table.
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
						htm+="<option>"+specialists[i]+"</option>"; //Specialist can be selected to be assigned a problem type.
					}
				}
				selBox.innerHTML=htm; //Appends values to selection box.
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
						htm+="<option>"+problemTypes[i]+"</option>"; //Problem type can be selected as a problem type for a specialist.
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
				value = document.getElementById("selProblemType").value;
				document.getElementById("txtProblemType").value = value;
				if (value == "")
				{
					PopulateProblemTypeSelect();
				}
			}
			
			function CheckIfUpdateOrAdd() //Turns the 'add' button into an 'update' button and populates the input fields, if exactly one row is selected.
			{
				if (selected == 1)
				{
					document.getElementById("btnAdd").value = "Update Item";
					rowNum = GetSelectedRow(); //Gets the row that is selected.
					document.getElementById("txtSpecialist").value = document.getElementById("tbl").rows[rowNum].cells[1].innerHTML;
					document.getElementById("txtProblemType").value = document.getElementById("tbl").rows[rowNum].cells[2].innerHTML;
				}
				else
				{
					document.getElementById("btnAdd").value = "Add New Item";
					document.getElementById("txtSpecialist").value = "";
					document.getElementById("txtProblemType").value = "";
				}
				PopulateSpecialistSelect();
				PopulateProblemTypeSelect();
			}		
			
			function ValidateInput() //Function returns true if the data input boxes are all valid.
			{
				id = "txtSpecialist";
				if (document.getElementById(id).value == false || isNaN(document.getElementById(id).value) || document.getElementById(id).value.includes("'") || !IsValidSpecialist(document.getElementById(id).value))
				{
					alert("Invalid specialist."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtProblemType";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'") || !IsValidProblemType(document.getElementById(id).value))
				{
					alert("Invalid problem type."); //Returns error if data input from text box is invalid.
					return false;
				}				
				
				if (selected != 1) //If adding, rather than updating.
				{
					for (i = 0; i < document.getElementById("tbl").rows.length; i++)
					{
						row = document.getElementById("tbl").rows[i];
						if (row.cells[1].innerHTML == txtSpecialist.value && row.cells[2].innerHTML == txtProblemType.value)
						{
							alert("This record already exists."); //If the specialist and problem types that have been input are both already in the local table at the same location, return error.
							return false;
						}
					}
				}
				return true;
			}
			
			function AddRow() //Adds a new row to the table, from data in the text boxes.
			{
				if (!ValidateInput())
				{
					return;
				}
				htm = "<tr class='rowDeselected'>"; //Sets colour of row.
				htm +="<td>-</td>"; //Until it has been added to the database, the first field is given a '(new)' tag.
				htm +="<td>"+document.getElementById("txtSpecialist").value+"</td>";
				htm +="<td>"+document.getElementById("txtProblemType").value+"</td>";
				htm += "</tr>";	
				document.getElementById("tbl").tBodies[0].innerHTML += htm; //Appends HTML to tableDiv.				
				newRowCount+=1;
				alert("New specialisation info added."); //Success message.
				document.getElementById("txtSpecialist").value = "";
				document.getElementById("txtProblemType").value = "";
				CheckIfUpdateOrAdd();
			}
			
			function UpdateRow() //Function that updates the selected row.
			{
				if (!ValidateInput())
				{
					return;
				}
				row = document.getElementById("tbl").rows[GetSelectedRow()]; //Gets the details of the row that is selected.
				row.cells[1].innerHTML = document.getElementById("txtSpecialist").value;
				row.cells[2].innerHTML = document.getElementById("txtProblemType").value;
				row.classList.replace("rowSelected", "rowDeselected"); //Deselect updated row.
				selected = 0;
				CheckIfUpdateOrAdd();
				if (!ListContains(updList, row.cells[0].innerHTML) && !row.cells[0].innerHTML.includes("-")) //If selected row is not already marked to be updated when changes are saved to the database later and is not a new row.
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
							if (document.getElementById("tbl").rows[i].cells[0].innerHTML.includes("-")) //If row is a new row, decrement number of new rows.
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
			
			function SaveChanges() //Function that saves table data back to database.
			{
				admin = (userData.split(","))[2];
				if (admin == 0) //If not admin, action is forbidden.
				{
					return;
				}
				sql = "";
				for (i = 0; i < delList.length; i++) //Iterate through delete list (deletion performed first as it reduces database size, making other operations quicker).
				{
					sql+="DELETE FROM tblSpecialisation WHERE specialisationID = " + delList[i] + "; ";
				}
				for (i = 0; i < updList.length; i++) //Iterate through the update list.
				{
					id = updList[i];
					rowNum = GetRowWithID(id); //Gets the row number in the local table that corresponds to the ID in the updList.
					if (rowNum != -1) //If row exists.
					{
						row = document.getElementById("tbl").rows[rowNum]; //Get row of local table that is being saved to database.
						sql+="UPDATE tblSpecialisation SET ";
						sql+="userID = "+ row.cells[1].innerHTML + ", ";
						sql+="typeName = '"+ row.cells[2].innerHTML + "' ";
						sql+="WHERE specialisationID = " + id + "; ";
					}
				}
				for (i = 0; i < GetRows(); i++) //Iterate through all rows to find new rows.
				{
					row = document.getElementById("tbl").rows[i];
					if (row.cells[0].innerHTML.includes("-")) //If record is new.
					{
						sql+="INSERT INTO tblSpecialisation VALUES (NULL, ";
						sql+="'" + row.cells[1].innerHTML + "', ";
						sql+="'" + row.cells[2].innerHTML + "'); ";
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
		<div class="container-fluid"> <!-- Container holds elements together using Bootstrap. -->
			<form autocomplete="off" id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
				<input type='text' hidden id="user" name="User"/> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
				<input type='hidden' id='previous' name='Previous' value="<?php echo $_GET['previous']; ?>" /> <!-- Hidden tag holding name of previous page. -->
				@csrf <!--Token to validates requests to server. -->
				<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
					<label id="dtLabel" class="dtLabel"></label> <!-- Label to contain current date/time. -->
					<h2 id="headerId">Specialisations</h2> <!-- Heading containing name of page. -->
				</div>
				<br/><br/>
				<div class="row" align="center">
					<div class="col-1"></div> <!--Empty div to create indent. -->
					<div id="tableDiv" class="col-6 table-wrapper-scroll-y"> <!-- Div containing data table. -->
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
							Specialist:<br/><input id="txtSpecialist" type="text" onkeyup="PopulateSpecialistSelect()"></input><br/> <!-- Input fields for adding a new row.-->						
							<select id="selSpecialist" onchange="SpecialistOptionClicked()" class="greenBack"></select>
							<br/>
							<label id="lblSpecialistNum"></label>
							<br/><br/>
							Problem Type:<br/><input id="txtProblemType" type="text" onkeyup="PopulateProblemTypeSelect()"></input><br/>					
							<select id="selProblemType" onchange="ProblemTypeOptionClicked()" class="greenBack"></select>
							<br/>
							<label id="lblProblemTypeNum"></label>
							<br/>
							<br/><input type="button" class="btn" id="btnAdd" value="Add New Item" style="font-size:16px;" onclick="AddPressed()"></input>	
							<br/><br/>
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