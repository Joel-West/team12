<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_EquipmentList</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple other files -->
		<script type="text/javascript">
			var userData; //Variable containing data about user.
			var currentPage = "EquipmentList"; //Variable storing the name of the current page, so it can be passed in the URL to the next page as a 'previous page' variable.
			var selected = 0; //Global variable corresponding to number of highlighted table rows.
			
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				SetPrivileges(userData) //Enter function that defines what functions are available to user based on status.
				ResetTable();
				WriteTime(); //Function that writes the current time at the top of the page.
			}
			
			function SetPrivileges(userData) //Function that checks if user is an admin or analyst and adjusts available buttons accordingly.
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
					sql = "SELECT * FROM tblEquipment;"; //Simple query to get all data from table.
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
				if (str.includes("'")) //If contains ' (if it is SQL injection-prone)
				{
					sql = "SELECT * FROM tblEquipment WHERE 1 = 0;"; //Get no results.
				}
				else
				{
					
					str = str.replace(", ", ",").split(","); //Split search text by commas.
					sql = "SELECT * FROM tblEquipment WHERE ";
					for (i = 0; i < str.length; i++) //Iterates through list of search terms, adding to the SQL query.
					{
						if (i != 0)
						{
							sql+=" OR ";
						}
						sql += "upper(serialNumber) LIKE '%"+str[i]+"%' OR upper(equipmentType) LIKE '%"+str[i]+"%' OR upper(equipmentMake) LIKE '%"+str[i]+"%'"; //Query that returns all database records with a cell containing search string.
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
						htm+="<tr id='t0'><th onclick='SortTable(0)' scope='col'>Serial Number</th>";
						htm+="<th onclick='SortTable(1)' scope='col'>Equipment Type</th>";
						htm+="<th onclick='SortTable(2)'scope='col'>Equipment Make</th></tr>"; //Appending column headers.
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							htm += "<tr style='background-color:rgb(159, 255, 48);'>"; //Sets colour and ID of row.
							htm +="<td>"+json[i].serialNumber+"</td>";
							htm +="<td>"+json[i].equipmentType+"</td>";
							htm +="<td>"+json[i].equipmentMake+"</td>";
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
			
			function CheckIfUpdateOrAdd() //The 'add' button into an 'update' button and populate the text boxes, if exactly one row is selected.
			{
				if (selected == 1)
				{
					document.getElementById("btnAdd").value = "Update Item";
					rowNum = GetSelectedRow(); //Gets the row that is selected.
					document.getElementById("txtSerial").value = document.getElementById("tbl").rows[rowNum].cells[0].innerHTML;
					document.getElementById("txtSerial").disabled = true;
					document.getElementById("txtType").value = document.getElementById("tbl").rows[rowNum].cells[1].innerHTML;
					document.getElementById("txtMake").value = document.getElementById("tbl").rows[rowNum].cells[2].innerHTML;
				}
				else
				{
					document.getElementById("btnAdd").value = "Add New Item";
					document.getElementById("txtSerial").value = "";
					document.getElementById("txtSerial").disabled = false;
					document.getElementById("txtType").value = "";
					document.getElementById("txtMake").value = "";
				}
			}		
			
			function ValidateInput() //Function returns true if the data input boxes are all valid.
			{
				id = "txtSerial";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'") ||
				((GetRowWithID(document.getElementById(id).value) != -1 || GetRowWithID(document.getElementById(id).value + "(new)") != -1) && document.getElementById(id).disabled == false))
				{
					alert("Invalid serial number."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtType";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'"))
				{
					alert("Invalid equipment type."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtMake";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'"))
				{
					alert("Invalid equipment make."); //Returns error if data input from text box is invalid.
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
				htm = "<tr style='background-color:rgb(159, 255, 48);'>"; //Sets colour and ID of row.
				htm +="<td>"+document.getElementById("txtSerial").value + "(new)</td>"; //Until it has been added to the database, the first field is given a '(new)' tag.
				htm +="<td>"+document.getElementById("txtType").value+"</td>";
				htm +="<td>"+document.getElementById("txtMake").value+"</td>";		
				htm += "</tr>";	
				document.getElementById("tbl").innerHTML += htm; //Appends HTML to tableDiv.				
				newRowCount+=1;
				alert("New equipment added."); //Success message.
				document.getElementById("btnAdd").value = "Add New Item";
				document.getElementById("txtSerial").value = "";
				document.getElementById("txtType").value = "";
				document.getElementById("txtMake").value = "";
			}
			
			function UpdateRow() //Function that updates the selected row.
			{
				if (!ValidateInput())
				{
					return;
				}
				row = document.getElementById("tbl").rows[GetSelectedRow()]; //Gets the details of the row that is selected.
				row.cells[1].innerHTML = document.getElementById("txtType").value;
				row.cells[2].innerHTML = document.getElementById("txtMake").value;
				row.style.backgroundColor = '#9FFF30';
				selected = 0;
				CheckIfUpdateOrAdd();
				if (!ListContains(updList, row.cells[0].innerHTML) && !row.cells[0].innerHTML.includes("(new)")) //If selected row is not already marked to be updated when changes are saved to the database later and is not a new row.
				{
					updList.push(row.cells[0].innerHTML); //Add the ID of the row to the list of rows to by updated when changes are commited to the actual database.
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
						if (document.getElementById("tbl").rows[i].style.backgroundColor != 'rgb(159, 255, 48)') //If row is selected.
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
					sql+="DELETE FROM tblEquipment WHERE serialNumber = '" + delList[i] + "'; ";
				}
				for (i = 0; i < updList.length; i++) //Iterate through delete list (deletion performed first as it reduces database size, making other operations quicker).
				{
					serialNumber = updList[i];
					rowNum = GetRowWithID(id); //Gets the row number in the local table that corresponds to the serialNumber in the updList.
					if (rowNum != -1) //If row exists.
					{
						row = document.getElementById("tbl").rows[rowNum]; //Get row of local table that is being saved to database.
						sql+="UPDATE tblEquipment SET ";
						sql+="equipmentType = '"+ row.cells[1].innerHTML + "', ";
						sql+="equipmentMake = '"+ row.cells[2].innerHTML + "', ";
						sql+="WHERE serialNumber = " + serialNumber + "; ";
					}
				}
				for (i = 0; i < GetRows(); i++) //Iterate through all rows to find new rows.
				{
					row = document.getElementById("tbl").rows[i];
					if (row.cells[0].innerHTML.includes("(new)")) //If record is new.
					{
						row.cells[0].innerHTML = row.cells[0].innerHTML.replace("(new)", '') //Remove the 'new' tag from the record.
						sql+="INSERT INTO tblEquipment VALUES (";
						sql+=row.cells[0].innerHTML + ", ";
						sql+="'" + row.cells[1].innerHTML + "', ";
						sql+="'" + row.cells[2].innerHTML + "'); ";
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
			max-height: 850px;
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
					<h2 id="headerId" style="font-weight:bold; style=display:inline-block; font-size:30px;">Equipment</h2> <!-- Heading containing name of page. -->
				</div>
				<br/><br/>
				<div class="row" align="center">
					<div class="col-1"></div> <!--Empty div to create indent. -->
					<div id="tableDiv" class="col-7 table-wrapper-scroll-y"> <!-- Div containing data table. -->
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
							Serial Number:<br/><input id="txtSerial" type="text"></input><br/> <!-- Input fields for adding a new row. -->
							Equipment Type:<br/><input id="txtType" type="text"></input><br/>
							Equipment Make:<br/><input id="txtMake" type="text"></input><br/>
							<br/><input type="button" class="btn" id="btnAdd" value="Add New Item" style="font-size:16px;" onclick="AddPressed()"></input>	
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