<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_UserList</title>
		<link rel="icon" href="https://www.goodfreephotos.com/albums/vector-images/screwdriver-and-wrench-vector-clipart.png">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>	<!-- Importing Bootstrap files. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple pages. -->
		<script type="text/javascript">	
			var userData; //Variable containing data about user.
			var currentPage = "ProblemTypeList"; //Variable storing the name of the current page, so it can be passed in the URL to the next page as a 'previous page' variable.
			var selected = 0; //Global variable corresponding to number of highlighted table rows.
			var generalisations = [];
			
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				SetPrivileges(userData) //Enter function that defines what functions are available to user based on status.
				ResetTable();
				WriteTime(); //Function that writes the current time at the top of the page.
				GetValidGeneralisationsArray();
			}
			
			function SetPrivileges(userData) //Function that checks if user is an admin, analyst or specialist and adjusts available buttons accordingly.
			{
				admin = (userData.split(","))[2]; //Retrieves admin status from userData that was earlier posted from previous form.
				if (admin == 0)
				{
					$("#inputDiv :input").prop("disabled", true); //If not admin, disable all input fields in the input div.
				}	
			}
			
			function ResetTable()
			{
				if (document.getElementById("txtSearch").value == "") //If not searching anything.
				{
					sql = "SELECT * FROM tblProblemType;"; //Simple query to get all data from table.
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
					sql = "SELECT * FROM tblProblemType WHERE 1 = 0;"; //Get no results.
				}
				else
				{
					
					str = str.replace(", ", ",").split(","); //Split search text by commas.
					sql = "SELECT * FROM tblProblemType WHERE ";
					for (i = 0; i < str.length; i++) //Iterates through list of search terms, adding to the SQL query.
					{
						if (i != 0)
						{
							sql+=" OR ";
						}
						sql += "upper(typeName) LIKE '%"+str[i]+"%' OR upper(generalisation) LIKE '%"+str[i]+"%'"; //Query that returns all database records with a cell containing search string.
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
						htm+="<tr id='t0'><th onclick='SortTable(0)' scope='col'>Problem Type</th>";
						htm+="<th onclick='SortTable(1)'scope='col'>Generalisation</th></tr>"; //Appending column headers.
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							htm += "<tr class='rowDeselected'>"; //Sets class (deselected) of row.
							htm +="<td>"+json[i].typeName+"</td>";
							htm +="<td>"+json[i].generalisation+"</td>";
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
			
			function GetValidGeneralisationsArray() //Function to get array of all problem types that the user could assign a new user to.
			{
				generalisations = [];
				sql = "SELECT typeName FROM tblProblemType;";
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							generalisations[i] = json[i].typeName;
						}
						PopulateGeneralisationSelect();
					}
				},'json');
			}
			
			function IsValidGeneralisation(item, child) //Returns true if ID is in the list of valid IDs.
			{
				if (child == item)
				{
					return false;
				}
				for (i = 0; i < generalisations.length; i++) //Iterates through all types that exist in the problem type table.
				{
					if (generalisations[i] == item)
					{
						return true;
					}
				}
				return false;
			}
			
			function PopulateGeneralisationSelect() //Populates selection box with problem types based on searched text.
			{
				generalisationBox = document.getElementById("txtGeneralisation");
				selBox = document.getElementById("selGeneralisation");
				htm = "<option></option>";
				size = 0; //Stores size of selection box.
				matchIndex = -1; //Will be assigned to a natural number if any of the type names from the generalisations list match exactly with the text box input.
				for (i = 0; i < generalisations.length; i++) //Iterates through all problem types that exist in the problem type table.
				{
					if (generalisations[i].toUpperCase().includes(generalisationBox.value.toUpperCase()) || generalisationBox.value == "")
					{
						size+=1;
						if (GetIDFromSelBoxItem(generalisations[i]) == generalisationBox.value)
						{
							matchIndex = size; //If the user has input an exact match, assign the variable defining what the default value for the box will be.
						}
						htm+="<option>"+generalisations[i]+"</option>"; //problem type can be selected as an generalisation for a new problem type.
					}
				}
				selBox.innerHTML=htm; //Appends values to selection box.
				if (matchIndex != -1)
				{
					selBox.selectedIndex = matchIndex;
				}
				lbl = document.getElementById("lblGeneralisationNum");
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
				if (generalisationBox.value.length > 0) //If the text box contains results, give the label the number of results.
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
			
			function GeneralisationOptionClicked() //Sets generalisation text box value to selected option in selection box.
			{
				value = GetIDFromSelBoxItem(document.getElementById("selGeneralisation").value);
				document.getElementById("txtGeneralisation").value = value;
				if (value == "")
				{
					PopulateGeneralisationSelect();
				}
			}	
			
			function CheckIfUpdateOrAdd() //Turns the 'add' button into an 'update' button and populates the input fields, if exactly one row is selected.
			{
				if (selected == 1)
				{
					document.getElementById("btnAdd").value = "Update Item";
					rowNum = GetSelectedRow(); //Gets the row that is selected.
					document.getElementById("txtTypeName").value = document.getElementById("tbl").rows[rowNum].cells[0].innerHTML;
					document.getElementById("txtTypeName").disable = true;
					document.getElementById("txtGeneralisation").value = document.getElementById("tbl").rows[rowNum].cells[1].innerHTML;
					document.getElementById("selGeneralisation").style.display = "none";
					document.getElementById("lblGeneralisationNum").style.display = "none";
				}
				else
				{
					document.getElementById("btnAdd").value = "Add New Item";
					document.getElementById("txtTypeName").value = "";
					document.getElementById("txtTypeName").disable = false;
					document.getElementById("txtGeneralisation").value = "";
					document.getElementById("selGeneralisation").style.display = "inline";
					document.getElementById("lblGeneralisationNum").style.display = "inline";
					PopulateGeneralisationSelect();
				}
			}		
			
			function ValidateInput() //Function returns true if the data input boxes are all valid.
			{
				id = "txtTypeName";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'") ||
				((GetRowWithID(document.getElementById(id).value) != -1 || GetRowWithID(document.getElementById(id).value + "(new)") != -1) && document.getElementById(id).disabled == false))
				{
					alert("Invalid problem type name."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtGeneralisation";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'") || !IsValidGeneralisation(document.getElementById(id).value, document.getElementById("txtTypeName").value))
				{
					alert("Invalid generalisation."); //Returns error if data input from text box is invalid.
					return false;
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
				htm +="<td>"+document.getElementById("txtID").value + "(new)</td>"; //Until it has been added to the database, the first field is given a '(new)' tag.
				htm +="<td>"+document.getElementById("txtGeneralisation").value+"</td>";
				htm += "</tr>";	
				document.getElementById("tbl").tBodies[0].innerHTML += htm; //Appends HTML to tableDiv.				
				newRowCount+=1;
				alert("New problem type added."); //Success message.
				document.getElementById("txtTypeName").value = "";
				document.getElementById("txtGeneralisation").value = "";
			}
			
			function UpdateRow() //Function that updates the selected row.
			{
				if (!ValidateInput())
				{
					return;
				}
				row = document.getElementById("tbl").rows[GetSelectedRow()]; //Gets the details of the row that is selected.
				row.cells[1].innerHTML = document.getElementById("txtGeneralisation").value;
				row.classList.replace("rowSelected", "rowDeselected"); //Deselect updated row.
				selected = 0;
				CheckIfUpdateOrAdd();
				if (!ListContains(updList, row.cells[0].innerHTML) && !row.cells[0].innerHTML.includes("(new)")) //If selected row is not already marked to be updated when changes are saved to the database later and is not a new row.
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
						name = document.getElementById("tbl").rows[i].cells[0].innerHTML;
						if (name == "Hardware problem" || name == "Software problem" || name == "Network problem")
						{
							alert("You cannot delete one of the base problem types.");
							return;
						}
						deleteRow = false; //Variable holding if row will actually be deleted.
						if (document.getElementById("tbl").rows[i].classList.contains("rowSelected")) //If row is selected.
						{
							deleteRow = true;
						}
						if (deleteRow == true) //If should be deleted after validation.
						{
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
					sql+="UPDATE tblProblem SET problemSubType = NULL WHERE problemSubType = " + delList[i] + "; ";
					sql+="UPDATE tblProblemType SET generalisation = NULL WHERE generalisation = " + delList[i] + "; ";
					sql+="DELETE FROM tblProblemType WHERE typeName = " + delList[i] + "; ";
				}
				for (i = 0; i < updList.length; i++) //Iterate through the update list.
				{
					id = updList[i];
					rowNum = GetRowWithID(id); //Gets the row number in the local table that corresponds to the type name in the updList.
					if (rowNum != -1) //If row exists.
					{
						row = document.getElementById("tbl").rows[rowNum]; //Get row of local table that is being saved to database.
						sql+="UPDATE tblUser SET ";
						sql+="generalisation = '"+ row.cells[1].innerHTML + "', ";
						sql+="WHERE typeName = " + id + "; ";
					}
				}
				for (i = 0; i < GetRows(); i++) //Iterate through all rows to find new rows.
				{
					row = document.getElementById("tbl").rows[i];
					if (row.cells[0].innerHTML.includes("(new)")) //If record is new.
					{
						row.cells[0].innerHTML = row.cells[0].innerHTML.replace("(new)", '') //Remove the 'new' tag from the record.
						sql+="INSERT INTO tbl VALUES (";
						sql+="'" + row.cells[0].innerHTML + "', ";
						sql+="'" + row.cells[1].innerHTML + "'); ";
					}
				}
				alert(sql);
				sql = "";
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
					GetValidGeneralisationsArray(); //Regenerate array of valid generalisations, now that new types may have been added.
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
			max-height:88vh;
			overflow-y: auto;
			-ms-overflow-style: -ms-autohiding-scrollbar;
		}
		</style>
	</head>
	<body onload="Load()" style="height:100%;">
		<div class="container-fluid"> <!-- Container holds elements together using Bootstrap. -->
			<form autocomplete="off" id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
				<input type='text' hidden id="user" name="User"/> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
				
				@csrf <!--Token to validates requests to server. -->
				<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
					<input type="button" id="btnBack" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" /> <!-- Back button. -->
					<label id="dtLabel" class="dtLabel"></label> <!-- Label to contain current data/time. -->
					<h2 id="headerId">Problem Types</h2> <!-- Heading containing name of page. -->
				</div>
				<br/><br/>
				<div class="row" align="center">
					<div class="col-1"></div> <!--Empty div to create indent. -->
					<div id="tableDiv" class="col-7 table-wrapper-scroll-y"> <!-- Div containing data table. -->
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
							Type Name:<br/><input id="txtTypeName" type="text"></input><br/>		
							Generalisation:<br/><input id="txtGeneralisation" type="text" onkeyup="PopulateGeneralisationSelect()"></input><br/> <!-- Input fields for adding a new row.-->						
							<select id="selGeneralisation" onchange="GeneralisationOptionClicked()" class="greenBack"></select>
							<br/>
							<label id="lblGeneralisationNum"></label>
							<br/><				
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