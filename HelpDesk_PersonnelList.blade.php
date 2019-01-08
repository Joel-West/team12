<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_PersonnelList</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script>
		<script type="text/javascript">	
			function Load()
			{
				RunQuery();
				setTimeout(function(){rows = GetRows();}, 10);
				for (i = 0; i < rows; i++)
				{
					//document.getElementById("tbl").rows[i].style.backgroundColor = '#9FFF30';
					document.getElementById("tbl").rows[i].id = "t" + i;
				}
				WriteTime();
			}
			
			function RunQuery()
			{
				sql = "SELECT * FROM tblPersonnel;";
				$.get("Query.php", {'sql':sql},function(json) //Calls query.php, which handles the SQL query and sorting of result data.
				{
					if(json && json[0]) //If result of php was a json array		
					{				
						var htm = "<table id='tbl' border='1'><tr><th>userID</th><th>Name</th><th>Job Title</th><th>Department</th><th>Telephone Number</th></tr>"; //Appending column headers.
						for (i = 0; i<json.length; i++) //Iterates through the json array.
						{
							//col = GetRandomCol(); //Gets a random colour from RGB values.
							//htm += '<tr style="background-color:rgb('+col[0]+', '+col[1]+', '+col[2]+');">'; //Assigns colour to a row.
							htm += "<tr>";
							htm +="<td>"+json[i].userID+"</td>";
							htm +="<td>"+json[i].name+"</td>";
							htm +="<td>"+json[i].jobTitle+"</td>";		
							htm +="<td>"+json[i].department+"</td>";
							htm +="<td>"+json[i].telephoneNumber+"</td>";
							htm += "</tr>";							
						}
					}
					else
					{
						var htm = "Sorry, no results found..."; //If no results, display error.
					}
					$("#tableDiv").html(htm) //Appends HTML to the results div.
				},'json');
			}
			
			var selected = 0;
			
			function AddNewRow()
			{
				if (document.getElementById("txtName").value == false || document.getElementById("txtJobTitle").value == false || document.getElementById("txtDepartment").value == false || document.getElementById("txtTelephoneNumber").value == false)
				{
					alert("Invalid input");
					return;
				}
				rows = GetRows();
				table = document.getElementById("tbl");
				row = table.insertRow(rows);
				cell0 = row.insertCell(0);
				cell0.innerHTML = "-";
				cell1 = row.insertCell(1);
				cell1.innerHTML = document.getElementById("txtName").value;
				cell2 = row.insertCell(2);
				cell2.innerHTML = document.getElementById("txtJobTitle").value;
				cell3 = row.insertCell(3);
				cell3.innerHTML = document.getElementById("txtDepartment").value;
				cell4 = row.insertCell(4);
				cell4.innerHTML = document.getElementById("txtTelephoneNumber").value;
				document.getElementById("tbl").rows[rows].id = "t" + document.getElementById("tbl").rows[rows-1].id;
				document.getElementById("tbl").rows[rows].style.backgroundColor = '#9FFF30';
				alert("New equipment added.");
			}
			
			function SaveChanges(page)
			{
				alert("Changes saved.");
				GoToNewPage(page);
			}
		</script>
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css">
	</head>
	<body onload="Load()">
	<form id="mainform" name="mainform" method="post" action="">
		<input type='hidden' name="Username" value="<?php echo $_POST['Username']; ?>" />
		@csrf
		<div class="titleDiv">
			<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" />
			<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label>
			<h2 id="headerId" style="style=display:inline-block; font-size:30px;">Personnel</h2>	
		</div>
		<div id="tableDiv"></div>	
		<div align="center">
			<p>
				Search:<input type="text"></input>		
				<input type="button" value="Submit"></input>
			</p>
			<input type="button" value="Delete Selected Items" id="del" style="font-size:16px;" onclick="Delete()"/><br/><br/>
			Name:<input id="txtName" type="text"></input><br/>
			Job Title:<input id="txtJobTitle" type="text"></input><br/>
			Department:<input id="txtDepartment" type="text"></input><br/>
			Telephone Number:<input id="txtTelephoneNumber" type="text"></input><br/>
			<input type="button" value="Add New Item" style="font-size:16px;" onclick="AddNewRow()"></input>	
		</div>
		<p align="center">
			<input type="button" value="Save Changes" style="font-size:26px; padding: 6px 12px;" onClick="SaveChanges('Home');" />
		</p>
		<input type="button" value="Test" onclick="GetRows()"
	</form>
	</body>
</html>