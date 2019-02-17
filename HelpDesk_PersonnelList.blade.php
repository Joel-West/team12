<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_PersonnelList</title>
		<link rel="icon" href="https://www.goodfreephotos.com/albums/vector-images/screwdriver-and-wrench-vector-clipart.png">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>	<!-- Importing Bootstrap files. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple pages. -->
		<script type="text/javascript">	
			var userData; //Variable containing data about user.
			var currentPage = "PersonnelList"; //Variable storing the name of the current page, so it can be passed in the URL to the next page as a 'previous page' variable.
			var selected = 0; //Global variable corresponding to number of highlighted table rows.
			
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				SetPrivileges(userData) //Enter function that defines what functions are available to user based on status.
				ResetTable();
				WriteTime(); //Function that writes the current time at the top of the page.
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
					sql = "SELECT * FROM tblPersonnel;"; //Simple query to get all data from table.
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
					sql = "SELECT * FROM tblPersonnel WHERE 1 = 0;"; //Get no results.
				}
				else
				{
					
					str = str.replace(", ", ",").split(","); //Split search text by commas.
					sql = "SELECT * FROM tblPersonnel WHERE ";
					for (i = 0; i < str.length; i++) //Iterates through list of search terms, adding to the SQL query.
					{
						if (i != 0)
						{
							sql+=" OR ";
						}
						sql += "upper(userID) LIKE '%"+str[i]+"%' OR upper(name) LIKE '%"+str[i]+"%' OR upper(jobTitle) LIKE '%"+str[i]+"%' OR upper(department) LIKE '%"+str[i]+"%' OR upper(telephoneNumber) LIKE '%"+str[i]+"%' OR upper(specialist) LIKE '%"+str[i]+"%'"; //Query that returns all database records with a cell containing search string.
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
						htm+="<th onclick='SortTable(1)' scope='col'>Name</th>";
						htm+="<th onclick='SortTable(2)'scope='col'>Job Title</th>";
						htm+="<th onclick='SortTable(3)'scope='col'>Department</th>";
						htm+="<th onclick='SortTable(4)'scope='col'>Telephone Number</th>";
						htm+="<th onclick='SortTable(5)'scope='col'>Specialist</th></tr>"; //Appending column headers.
						for (i = 0; i<json.length; i++) //Iterates through the json array of results.
						{
							htm += "<tr class='rowDeselected'>"; //Sets class (deselected) of row.
							htm +="<td>"+json[i].userID+"</td>";
							htm +="<td>"+json[i].name+"</td>";
							htm +="<td>"+json[i].jobTitle+"</td>";		
							htm +="<td>"+json[i].department+"</td>";
							htm +="<td>"+json[i].telephoneNumber+"</td>";
							htm +="<td>"+json[i].specialist+"</td>";
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
			
			function CheckIfUpdateOrAdd() //Turns the 'add' button into an 'update' button and populates the input fields, if exactly one row is selected.
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
			
			function ValidateInput() //Function returns true if the data input boxes are all valid.
			{
				id = "txtID";
				if (document.getElementById(id).value == false || isNaN(document.getElementById(id).value) || document.getElementById(id).value.includes("'") ||
				((GetRowWithID(document.getElementById(id).value) != -1 || GetRowWithID(document.getElementById(id).value + "(new)") != -1) && document.getElementById(id).disabled == false))
				{
					alert("Invalid ID."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtName";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'"))
				{
					alert("Invalid name."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtJobTitle";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'"))
				{
					alert("Invalid job title."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtDepartment";
				if (document.getElementById(id).value == false || document.getElementById(id).value.includes("'"))
				{
					alert("Invalid department."); //Returns error if data input from text box is invalid.
					return false;
				}
				id = "txtTelephoneNumber";
				if (document.getElementById(id).value == false || isNaN(document.getElementById(id).value) || document.getElementById(id).value.includes("'"))
				{
					alert("Invalid telephone number."); //Returns error if data input from text box is invalid.
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
				htm +="<td>"+document.getElementById("txtName").value+"</td>";
				htm +="<td>"+document.getElementById("txtJobTitle").value+"</td>";		
				htm +="<td>"+document.getElementById("txtDepartment").value+"</td>";
				htm +="<td>"+document.getElementById("txtTelephoneNumber").value+"</td>";
				htm +="<td>"+GetSpecialistAsString(document.getElementById("chkSpecialist").checked)+"</td>";
				htm += "</tr>";	
				document.getElementById("tbl").tBodies[0].innerHTML += htm; //Appends HTML to tableDiv.				
				newRowCount+=1;
				alert("New personnel added."); //Success message.
				document.getElementById("btnAdd").value = "Add New Item";
				document.getElementById("txtID").value = "";
				document.getElementById("txtName").value = "";
				document.getElementById("txtJobTitle").value = "";
				document.getElementById("txtDepartment").value = "";
				document.getElementById("txtTelephoneNumber").value = "";
				document.getElementById("chkSpecialist").checked = false;
			}
			
			function UpdateRow() //Function that updates the selected row.
			{
				if (!ValidateInput())
				{
					return;
				}
				row = document.getElementById("tbl").rows[GetSelectedRow()]; //Gets the details of the row that is selected.
				row.cells[1].innerHTML = document.getElementById("txtName").value;
				row.cells[2].innerHTML = document.getElementById("txtJobTitle").value;
				row.cells[3].innerHTML = document.getElementById("txtDepartment").value;
				row.cells[4].innerHTML = document.getElementById("txtTelephoneNumber").value;
				row.cells[5].innerHTML = GetSpecialistAsString(document.getElementById("chkSpecialist").checked);
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
				if (confirm("If any of these personnel are found in the table of users or specialisations, they will also be deleted. Delete selected rows?")) //Get user confirmation.
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
					sql+="DELETE FROM tblSpecialisation WHERE userID = " + delList[i] + "; ";
					sql+="UPDATE tblCallHistory SET operatorID = NULL WHERE operatorID = " + delList[i] + "; ";
					sql+="UPDATE tblCallHistory SET callerID = NULL WHERE callerID = " + delList[i] + "; ";
					sql+="DELETE FROM tblPersonnel WHERE userID = " + delList[i] + "; ";
				}
				for (i = 0; i < updList.length; i++) //Iterate through the update list.
				{
					id = updList[i];
					rowNum = GetRowWithID(id); //Gets the row number in the local table that corresponds to the ID in the updList.
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
						if (row.cells[5].innerHTML == "No")
						{
							sql+="DELETE FROM tblSpecialisation WHERE userID = " + id + "; ";
						}
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
		<header class="navbar flex-column flex-md-row bd-navbar navbar-dark navbar-expand-lg bg-dark">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="navbar-collapse collapse" id="navbarNavDropdown" onload="SetNavSettings()">
			<ul class='navbar-nav mr-auto'>
			<a class='nav-item nav-link' href='#' onClick='GoToNewPage(document.getElementById(&quot;previous&quot;).value)'>&#x2190 </a>
			<a  class='nav-item nav-link' href='#' onClick='GoToNewPage&quot;Home&quot;);'>Call History</a>
			<a  class='nav-item nav-link' href='#' onClick='GoToNewPage&quot;NewCaller&quot;);'>Call History</a>
			<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;CallHistory&quot;);'>Call History</a>
			<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;ProblemList&quot;);'>Problems List</a>
			<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;PersonnelList&quot;);'>Personnel</a>
			<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;UserList&quot;);'>Users</a>
			<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;SpecialisationList&quot;);'>Specialisations</a>
			<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;EquipmentList&quot;);'>Equipment</a>
			<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;ProblemTypeList&quot;);'>Problem Type List</a>
			<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;Analytics&quot;);'>Analytics</a></ul>
		</div>
		<a class='nav-item nav-link' href='#' onClick='GoToNewPage("");'>Logout</a>
		<a class="navbar-brand ml-md-auto" href="#">
		<img src="https://www.goodfreephotos.com/albums/vector-images/screwdriver-and-wrench-vector-clipart.png" width="30" height="30" class="d-inline-block align-top" alt="">
		  Make-It-All
		</a>
		</header>
		<div class="container-fluid"> <!-- Container holds elements together using Bootstrap. -->
			<form autocomplete="off" id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
				<input type='text' hidden id="user" name="User"/> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
				<input type='hidden' id='previous' name='Previous' value="<?php echo $_GET['previous']; ?>" /> <!-- Hidden tag holding nam of previous page. -->
				@csrf <!--Token to validates requests to server. -->
				<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
					<label id="dtLabel" class="dtLabel"></label> <!-- Label to contain current data/time. -->
					<h2 id="headerId">Personnel</h2> <!-- Heading containing name of page. -->
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
							ID:<br/><input id="txtID" type="text"></input><br/> <!-- Input fields for adding a new row. -->
							Name:<br/><input id="txtName" type="text"></input><br/>
							Job Title:<br/><input id="txtJobTitle" type="text"></input><br/>
							Department:<br/><input id="txtDepartment" type="text"></input><br/>
							Telephone Number:<br/><input id="txtTelephoneNumber" type="text"></input><br/>
							Specialist?&nbsp&nbsp<input id="chkSpecialist" type="checkbox"></input><br/>
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