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
			var currentPage = "UserList"; //Variable storing the name of the current page, so it can be passed in the URL to the next page as a 'previous page' variable.
			var selected = 0; //Global variable corresponding to number of highlighted table rows.
			var validIDs = [];
			
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				SetPrivileges(userData) //Enter function that defines what functions are available to user based on status.
				ResetTable();
				WriteTime(); //Function that writes the current time at the top of the page.
				GetValidIDsArray();
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
				if (str.includes("'")) //If contains ' (if it is SQL injection-prone).
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
						if (str[i] == "YES" || str[i] == "YE" || str[i] == "Y") //As database contains booleans as strings for the 'admin' field, unlike the local table, this is an approximate algorithm for searching 'yes' and 'no'.
						{
							sql += "upper(userID) LIKE '%"+str[i]+"%' OR upper(username) LIKE '%"+str[i]+"%' OR upper(password) LIKE '%"+str[i]+"%' OR upper(admin) LIKE '1'"; //Query that returns all database records with a cell containing search string.
						}
						else if (str[i] == "NO"  || str[i] == "N")
						{
							sql += "upper(userID) LIKE '%"+str[i]+"%' OR upper(username) LIKE '%"+str[i]+"%' OR upper(password) LIKE '%"+str[i]+"%' OR upper(admin) LIKE '0'"; //Query that returns all database records with a cell containing search string.
						}
						else
						{
							sql += "upper(userID) LIKE '%"+str[i]+"%' OR upper(username) LIKE '%"+str[i]+"%' OR upper(password) LIKE '%"+str[i]+"%'"; //Query that returns all database records with a cell containing search string.
						}
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
						htm+="<th onclick='SortTable(1)' scope='col'>Username</th>";
						htm+="<th onclick='SortTable(2)'scope='col'>Password</th>";
						htm+="<th onclick='SortTable(3)'scope='col'>Admin</th></tr>"; //Appending column headers.
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							htm += "<tr class='rowDeselected'>"; //Sets class (deselected) of row.
							htm +="<td>"+json[i].userID+"</td>";
							htm +="<td>"+json[i].username+"</td>";
							htm +="<td class='hidetext'>"+json[i].password+"</td>";		
							htm +="<td>"+GetAdminAsString(json[i].admin)+"</td>";
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
				sql = "SELECT userID, name FROM tblPersonnel;";
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php file was a json array.	
					{
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							validIDs[i] = json[i].userID + " - " + json[i].name;
						}
						PopulateIDSelect();
					}
				},'json');
			}
			
			function GetIDFromSelBoxItem(item) //Takes an item from a selection box (ID + name) and returns just the ID.
			{
				return (item.split(" "))[0]
			}
			
			function IsValidID(item) //Returns true if ID is in the list of valid IDs.
			{
				for (i = 0; i < validIDs.length; i++) //Iterates through all IDs that exist in the personnel table.
				{
					if (GetIDFromSelBoxItem(validIDs[i]) == item)
					{
						return true;
					}
				}
				return false;
			}
			
			function PopulateIDSelect() //Populates selection box with IDs/names based on searched text.
			{
				IDBox = document.getElementById("txtID");
				selBox = document.getElementById("selID");
				htm = "<option></option>";
				size = 0; //Stores size of selection box.
				matchIndex = -1; //Will be assigned to a natural number if any of the IDs from the validIDs list match exactly with the text box input.
				for (i = 0; i < validIDs.length; i++) //Iterates through all IDs that exist in the personnel table.
				{
					if ((GetRowWithID(GetIDFromSelBoxItem(validIDs[i])) == -1) && ((GetRowWithID(GetIDFromSelBoxItem(validIDs[i])).value + "(new)") != -1) && (validIDs[i].toUpperCase().includes(IDBox.value.toUpperCase()) || IDBox.value == ""))
					{
						size+=1;
						if (GetIDFromSelBoxItem(validIDs[i]) == IDBox.value)
						{
							matchIndex = size; //If the user has input an exact match, assign the variable defining what the default value for the box will be.
						}
						htm+="<option>"+validIDs[i]+"</option>"; //ID can be selected as an ID for a new user.
					}
				}
				selBox.innerHTML=htm; //Appends values to selection box.
				if (matchIndex != -1)
				{
					selBox.selectedIndex = matchIndex;
				}
				lbl = document.getElementById("lblIDNum");
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
				if (IDBox.value.length > 0) //If the text box contains results, give the label the number of results.
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
			
			function IDOptionClicked() //Sets ID text box value to selected option in selection box.
			{
				value = GetIDFromSelBoxItem(document.getElementById("selID").value);
				document.getElementById("txtID").value = value;
				if (value == "")
				{
					PopulateIDSelect();
				}
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
			
			function CheckIfUpdateOrAdd() //Turns the 'add' button into an 'update' button and populates the input fields, if exactly one row is selected.
			{
				if (selected == 1)
				{
					document.getElementById("btnAdd").value = "Update Item";
					rowNum = GetSelectedRow(); //Gets the row that is selected.
					document.getElementById("txtID").value = document.getElementById("tbl").rows[rowNum].cells[0].innerHTML;
					document.getElementById("txtID").disabled = true;
					document.getElementById("selID").style.display = "none";
					document.getElementById("lblIDNum").style.display = "none";
					document.getElementById("txtUsername").value = document.getElementById("tbl").rows[rowNum].cells[1].innerHTML;
					document.getElementById("txtPassword").value = "";
					document.getElementById("chkAdmin").checked = GetAdminAsBool(document.getElementById("tbl").rows[rowNum].cells[3].innerHTML);
				}
				else
				{
					document.getElementById("btnAdd").value = "Add New Item";
					document.getElementById("txtID").value = "";
					document.getElementById("txtID").disabled = false;
					document.getElementById("selID").style.display = "inline";
					document.getElementById("lblIDNum").style.display = "inline";
					PopulateIDSelect();
					document.getElementById("txtUsername").value = "";
					document.getElementById("txtPassword").value = "";
					document.getElementById("chkAdmin").checked = false;
				}
			}		
			
			function ValidateInput() //Function returns true if the data input boxes are all valid.
			{
				id = "txtID";
				if (document.getElementById(id).value == false || isNaN(document.getElementById(id).value) || document.getElementById(id).value.includes("'") ||
				((GetRowWithID(document.getElementById(id).value) != -1 || GetRowWithID(document.getElementById(id).value + "(new)") != -1 || !IsValidID(document.getElementById(id).value)) && document.getElementById(id).disabled == false))
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
				htm = "<tr class='rowDeselected'>"; //Sets colour of row.
				htm +="<td>"+document.getElementById("txtID").value + "(new)</td>"; //Until it has been added to the database, the first field is given a '(new)' tag.
				htm +="<td>"+document.getElementById("txtUsername").value+"</td>";
				$.get("Hash.php", {'Password':document.getElementById("txtPassword").value},function(Hashed){
				  if(Hashed){
				    htm +="<td class='hidetext'>"+Hashed+"</td>";
					htm +="<td>"+GetAdminAsString(document.getElementById("chkAdmin").checked)+"</td>";
					htm += "</tr>";	
					document.getElementById("tbl").tBodies[0].innerHTML += htm; //Appends HTML to tableDiv.
				  }
				},'json');				
				newRowCount+=1;
				alert("New user info added."); //Success message.
				document.getElementById("txtID").value = "";
				document.getElementById("txtUsername").value = "";
				document.getElementById("txtPassword").value = "";
				document.getElementById("chkAdmin").checked = false;
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
				$.get("Hash.php", {'Password':document.getElementById("txtPassword").value},function(Hashed){
				  if(Hashed){
					row.cells[2].innerHTML = Hashed;
					row.cells[3].innerHTML = GetAdminAsString(document.getElementById("chkAdmin").checked);
					console.log(GetAdminAsString(document.getElementById("chkAdmin").checked);
					console.log(row.cells[3]);
				    row.classList.replace("rowSelected", "rowDeselected"); //Deselect updated row.
				  }
				},'json');
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
				if (confirm("Deleting a user will prevent them from being able to log in again. Delete selected rows?")) //Get user confirmation.
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
					sql+="DELETE FROM tblUser WHERE userID = " + delList[i] + "; ";
				}
				for (i = 0; i < updList.length; i++) //Iterate through the update list.
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
		.hidetext { -webkit-text-security: disc;}
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
					<h2 id="headerId">Users</h2> <!-- Heading containing name of page. -->
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
							Personnel ID:<br/><input id="txtID" type="text" onkeyup="PopulateIDSelect()"></input><br/> <!-- Input fields for adding a new row.-->						
							<select id="selID" onchange="IDOptionClicked()" class="greenBack"></select>
							<br/>
							<label id="lblIDNum"></label>
							<br/><br/>
							Username:<br/><input id="txtUsername" type="text"></input><br/>
							Password:<br/><input class="hidetext" id="txtPassword" type="text"></input><br/>							
							Admin?&nbsp&nbsp<input id="chkAdmin" type="checkbox"></input><br/>
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