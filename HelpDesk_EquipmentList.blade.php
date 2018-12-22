<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_EquipmentList</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script>
		<script type="text/javascript">
			function Load()
			{
				rows = GetRows();
				for (i = 0; i < rows; i++)
				{
					document.getElementById("tbl").rows[i].style.backgroundColor = '#9FFF30';
					document.getElementById("tbl").rows[i].id = "t" + i;
				}
				WriteTime();
			}
			
			function GetRows()
			{
				var rows = $('#tbl tr').length;
				return rows;
			}
			
			function AddNewRow()
			{
				if (document.getElementById("txtSerial").value == false || document.getElementById("txtType").value == false || document.getElementById("txtMake").value == false)
				{
					alert("Invalid input");
					return;
				}
				rows = GetRows();
				table = document.getElementById("tbl");
				row = table.insertRow(rows);
				cell0 = row.insertCell(0);
				cell0.innerHTML = document.getElementById("txtSerial").value;
				cell1 = row.insertCell(0);
				cell1.innerHTML = document.getElementById("txtType").value;
				cell2 = row.insertCell(0);
				cell2.innerHTML = document.getElementById("txtMake").value;
				document.getElementById("tbl").rows[rows].id = "t" + document.getElementById("tbl").rows[rows-1].id;
				document.getElementById("tbl").rows[rows].style.backgroundColor = '#9FFF30';
				alert("New equipment added.");
			}
			
			function SaveChanges(page)
			{
				alert("Changes saved.");
				GoToNewPage(page);
			}
			
			var selected = 0;
			
			function Delete()
			{
				if (selected == 0)
				{
					return;
				}
				if (confirm("Delete selected rows?"))
				{
					rows = GetRows();
					for (i = rows-1; i > 0; i--)
					{
						if (document.getElementById("tbl").rows[i].style.backgroundColor != 'rgb(159, 255, 48)')
						{
							console.log("deleting t" + i);
							document.getElementById("tbl").deleteRow(i);
						}
					}
					selected = 0;
				}
			}
			
			$(document).ready(function()
			{
				$("#tbl").on('click','tr',function(event)
				{
					if ($(this).attr('id') != 't0')
					{
						if ($(this).css('background-color') == 'rgb(159, 255, 48)')
						{	
							$(this).css('background-color', '#00FFFF');
							selected += 1;
						}
						else if ($(this).css('background-color') == 'rgb(0, 255, 255)')
						{
							$(this).css('background-color', '#9FFF30');
							selected -= 1;
						}
						console.log(selected);
					}
				});
			});	
		</script>
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css">
	</head>
	<body onload="Load()" id="body">
	<form id="mainform" name="mainform" method="post" action="">
		@csrf
		<div class="titleDiv">
			<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" />
			<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label>
			<h2 id="headerId" style="style=display:inline-block; font-size:30px;">Equipment</h2>	
		</div>
		<div id="tableDiv">
			<table id="tbl" border="1">
				<tr id="first">
					<th>Serial Number</th>
					<th>Equipment Type</th>
					<th>Equipment Make</th>
				</tr>
				<tr>
					<td>K45nQkCo</td>
					<td>Keyboard</td>
					<td>Dell</td>
				</tr>
				<tr>
					<td>aMAnEozQ</td>
					<td>Headset with microphone</td>
					<td>Plantronics</td>
				</tr>
				<tr>
					<td>FLoDZzQF</td>
					<td>Speakers (pair)</td>
					<td>Logitech</td>
				</tr>
				<tr>
					<td>z9e3BMDT</td>
					<td>Desktop computer</td>
					<td>Lenovo</td>
				</tr>
				<tr>
					<td>mf33tLqF</td>
					<td>Ergonomic mouse</td>
					<td>Logitech</td>
				</tr>
			</table>
		</div>
		<br/>
		<div align="center">
			<input type="button" value="Delete Selected Items" id="del" style="font-size:16px;" onclick="Delete()"/><br/><br/>
			Serial Number:<input id="txtSerial" type="text"></input><br/>
			Equipment Type:<input id="txtType" type="text"></input><br/>
			Equipment Make:<input id="txtMake" type="text"></input><br/>
			<input type="button" value="Add New Item" style="font-size:16px;" onclick="AddNewRow()"></input>	
		</div>
		<p align="center">
			<input type="button" value="Save Changes" style="font-size:26px; padding: 6px 12px;" onClick="SaveChanges('HelpDesk_Home.html');" />
		</p>
	</form>
	</body>
</html>