<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_UserList</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>	<!-- Importing Bootstrap files. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple other files -->
		<script type="text/javascript">	
			var userData; //Variable containing data about user.
			var currentPage = "PersonnelList"; //Variable storing the name of the current page, so it can be passed in the URL to the next page as a 'previous page' variable.
			var validIDs = [];
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				SetPrivileges(userData) //Enter function that defines what functions are available to user based on status.
				ResetTable();
				WriteTime(); //Function that writes the current time at the top of the page.
				GetValidIDsArray();
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
					sql = "SELECT * FROM tblUser;"; //Simple query to get all data from table.
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
					sql = "SELECT * FROM tblUser WHERE 1 = 0;"; //Get no results.
				}
				else
				{
					
					str = str.replace(", ", ",").split(","); //Split search text by commas.
					sql = "SELECT * FROM tblUser WHERE ";
					for (i = 0; i < str.length; i++) //Iterates through list of search terms, adding to the SQL query.
					{
						if (i != 0)
						{
							sql+=" OR ";
						}
						sql += "upper(userID) LIKE '%"+str[i]+"%' OR upper(username) LIKE '%"+str[i]+"%' OR upper(password) LIKE '%"+str[i]+"%' OR upper(admin) LIKE '%"+str[i]+"%'"; //Query that returns all database records with a cell containing search string.
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
						htm+="<tr id='t0'><th onclick='SortTable(0)' scope='col'>userID</th>";
						htm+="<th onclick='SortTable(1)' scope='col'>Username</th>";
						htm+="<th onclick='SortTable(2)'scope='col'>Password</th>";
						htm+="<th onclick='SortTable(3)'scope='col'>Admin</th></tr>"; //Appending column headers.
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							htm += "<tr id='t" + (i+1) + "' style='background-color:rgb(159, 255, 48);'>"; //Sets colour and ID of row.
							htm +="<td id='id'>"+json[i].userID+"</td>";
							htm +="<td id='username'>"+json[i].username+"</td>";
							htm +="<td class='hidetext' id='password'>"+json[i].password+"</td>";		
							htm +="<td id='admin'>"+GetAdminAsString(json[i].admin)+"</td>";
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
			
			function GetValidIDsArray() //Function to get array of all IDs which the user could assign a new user to.
			{
				sql = "SELECT userID, name FROM tblPersonnel";
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							validIDs[i] = json[i].userID + " - " + json[i].name;
						}
					}
				},'json');
				console.log(validIDs);
			}
			
			function GetAdminAsBool(Admin) //Gets the admin value from a table as a string and returns a boolean.
			{
				if (Admin == "Yes")
				{
					return 1;
				}
				else
				{
					return 0;
				}
			}
			
			function GetAdminAsString(Admin) //Gets the admin value from the checkbox as a boolean and returns a string.
			{
				if (Admin == 1)
				{
					return "Yes";
				}
				else
				{
					return "No";
				}
			}		
			
			function CheckIfUpdateOrAdd() //The 'add' button into an 'update' button and populate the text boxes, if exactly one row is selected.
			{
				if (selected == 1)
				{
					document.getElementById("btnAdd").value = "Update Item";
					rowNum = GetSelectedRow(); //Gets the row that is selected.
					document.getElementById("txtID").value = document.getElementById("tbl").rows[rowNum].cells[0].innerHTML;
					document.getElementById("txtID").disabled = true;
					document.getElementById("txtUsername").value = document.getElementById("tbl").rows[rowNum].cells[1].innerHTML;
					document.getElementById("txtPassword").value = document.getElementById("tbl").rows[rowNum].cells[2].innerHTML;
					document.getElementById("chkAdmin").checked = GetAdminAsBool(document.getElementById("tbl").rows[rowNum].cells[3].innerHTML);
				}
				else
				{
					document.getElementById("btnAdd").value = "Add New Item";
					document.getElementById("txtID").value = "";
					document.getElementById("txtID").disabled = false;
					document.getElementById("txtUsername").value = "";
					document.getElementById("txtPassword").value = "";
					document.getElementById("chkAdmin").checked = false;
				}
			}		
			
			function ValidateInput() //Function returns true if the data input boxes are all valid.
			{
				id = "txtID";
				if (document.getElementById(id).value == false || isNaN(document.getElementById(id).value) || document.getElementById(id).value.includes("'") ||
				((GetRowWithID(document.getElementById(id).value) != -1 || GetRowWithID(document.getElementById(id).value + "(new)") != -1) && document.getElementById(id).disabled == false))
				{
					alert("Invalid ID."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtUsername";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'"))
				{
					alert("Invalid username."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtPassword";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'"))
				{
					alert("Invalid password."); //Returns error if data input from text box is invalid.
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
				rows = GetRows(); //Gets number of rows.
				table = document.getElementById("tbl");
				row = table.insertRow(rows); //Adds new empty row.
				cell0 = row.insertCell(0); //Inserts and modifies each cell of the new row in turn.
				cell0.innerHTML = document.getElementById("txtID").value + "(new)"; //Until it has been added to the database, the first field is given a '(new)' tag.
				cell1 = row.insertCell(1);
				cell1.innerHTML = document.getElementById("txtUsername").value;
				cell2 = row.insertCell(2);
				cell2.innerHTML = document.getElementById("txtPassword").value;
				cell3 = row.insertCell(3);
				cell3.innerHTML = GetAdminAsString(document.getElementById("chkAdmin").checked);
				document.getElementById("tbl").rows[rows].id = "t" + document.getElementById("tbl").rows[rows-1].id; //Sets ID of new row.
				document.getElementById("tbl").rows[rows].style.backgroundColor = '#9FFF30'; //Sets background colour of new row.
				newRowCount+=1;
				alert("New user info added."); //Success message.
			}
			
			function UpdateRow() //Function that updates the selected row.
			{
				if (!ValidateInput())
				{
					return;
				}
				if (document.getElementById("txtID").value == (userData.split(","))[1] && document.getElementById("chkAdmin").checked == 0)//Prevents user from changing their admin status.
				{
					alert("You cannot change your own admin status.");
					return;
				}
				row = document.getElementById("tbl").rows[GetSelectedRow()]; //Gets the details of the row that is selected.
				row.cells[1].innerHTML = document.getElementById("txtUsername").value;
				row.cells[2].innerHTML = document.getElementById("txtPassword").value;
				row.cells[3].innerHTML = GetAdminAsString(document.getElementById("chkAdmin").checked);
				row.style.backgroundColor = '#9FFF30';
				selected = 0;
				CheckIfUpdateOrAdd();
				if (!ListContains(updList, row.cells[0].innerHTML) && !row.cells[0].innerHTML.includes("(new)")) //If selected row is not already marked to be updated when changes are saved to the database later and is not a new row.
				{
					updList.push(row.cells[0].innerHTML); //Add the ID of the row to the list of rows to by updated when changes are commited to the actual database.
					console.log(updList);
				}
				alert("User info updated successfully.");
			}
			
			function Delete() //Function for deleting selected rows from a table.
			{
				admin = (userData.split(","))[2];
				if (selected == 0 || admin == 0) //if no rows are selected, or if not admin, leave function.
				{
					return;
				}
				if (confirm("Deleting a user will prevent them from being able to log in again. Delete selected rows?")) //Get user confirmation.
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
					sql+="DELETE FROM tblUser WHERE userID = " + delList[i] + "; ";
				}
				for (i = 0; i < updList.length; i++) //Iterate through delete list (deletion performed first as it reduces database size, making other operations quicker).
				{
					id = updList[i];
					rowNum = GetRowWithID(id); //Gets the row number in the local table that corresponds to the ID in the updList.
					if (rowNum != -1) //If row exists.
					{
						row = document.getElementById("tbl").rows[rowNum]; //Get row of local table that is being saved to database.
						sql+="UPDATE tblUser SET ";
						sql+="username = '"+ row.cells[1].innerHTML + "', ";
						sql+="password = '"+ row.cells[2].innerHTML + "', ";
						sql+="admin = '"+ GetAdminAsBool(row.cells[3].innerHTML) + "' ";
						sql+="WHERE userID = " + id + "; ";
					}
				}
				for (i = 0; i < GetRows(); i++) //Iterate through all rows to find new rows.
				{
					row = document.getElementById("tbl").rows[i];
					if (row.cells[0].innerHTML.includes("(new)")) //If record is new.
					{
						row.cells[0].innerHTML = row.cells[0].innerHTML.replace("(new)", '') //Remove the 'new' tag from the record.
						sql+="INSERT INTO tblUser VALUES (";
						sql+=row.cells[0].innerHTML + ", ";
						sql+="'" + row.cells[1].innerHTML + "', ";
						sql+="'" + row.cells[2].innerHTML + "', "
						sql+="" + GetAdminAsBool(row.cells[3].innerHTML) + "); ";
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
		.table-wrapper-scroll-y
		{
			display: block;
			max-height: 800px;
			overflow-y: auto;
			-ms-overflow-style: -ms-autohiding-scrollbar;
		}
		.hidetext { -webkit-text-security: disc;}
		</style>
	</head>
	<body onload="Load()" style="height:100%;">
		<div class="container-fluid-webkit-filter: blur(0px);"> <!-- Container holds elements together using Bootstrap. -->
			<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
				<input type='text' hidden id="user" name="User"/> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
				
				@csrf <!--Token to validates requests to server. -->
				<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
					<input type="button" id="btnBack" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" /> <!-- Back button. -->
					<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label> <!-- Label to contain current data/time. -->
					<h2 id="headerId" style="font-weight: bold; style=display:inline-block; font-size:30px;">Users</h2> <!-- Heading containing name of page. -->
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
								Search:<input id="txtSearch" type="text" oninput="ResetTable()"></input> <!-- Box for searching the table for specific strings. -->
								<input type="button" class="btn" id="btnSearch" value="Submit" onclick="Search()"></input> <!-- Submits search on press -->
							</p>
						</div>
						<div id="inputDiv">
							<input type="button" class="btn" id="btnDelete" value="Delete Selected Items" id="del" style="font-size:16px;" onclick="Delete()"/><br/><br/> <!-- Delete button that calls function within ExtraCode.js when pressed. -->
							ID:<br/><input id="txtID" type="text"></input><br/> <!-- Input fields for adding a new row.-->						
							Username:<br/><input id="txtUsername" type="text"></input><br/>
							Password:<br/><input class="hidetext" id="txtPassword" type="text"></input><br/>							
							Admin? <input id="chkAdmin" type="checkbox"></input><br/>
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