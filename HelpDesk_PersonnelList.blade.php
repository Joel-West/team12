<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_PersonnelList</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script>
		<script type="text/javascript">
		</script>
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css">
	</head>
	<body onload="WriteTime()">
		<div class="titleDiv">
			<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('HelpDesk_Home.html');" />
			<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label>
			<h2 id="headerId" style="style=display:inline-block; font-size:30px;">Personnel</h2>	
		</div>
		<table id="tbl" border="1">
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Job Title</th>
				<th>Department</th>
				<th>Telephone Number</th>
			</tr>
			<tr>
				<td>42512</td>
				<td>Alice Newman</td>
				<td>Helpdesk operator</td>
				<td>Support</td>
				<td>01509-405414</td>
			</tr>
			<tr>
				<td>13413</td>
				<td>Sam Sheppard</td>
				<td>Helpdesk operator</td>
				<td>Support</td>
				<td>01509-998487</td>
			</tr>
			<tr>
				<td>56724</td>
				<td>Sarah Knight</td>
				<td>Junior software developer</td>
				<td>Development</td>
				<td>01509-672014</td>
			</tr>
			<tr>
				<td>19836</td>
				<td>Ryan Watts</td>
				<td>Software analyst</td>
				<td>Development</td>
				<td>01509-371574</td>
			</tr>
			<tr>
				<td>76521</td>
				<td>Jim Flynn</td>
				<td>Product design intern</td>
				<td>Product design</td>
				<td>01509-405137</td>
			</tr>
			<tr>
				<td>65427</td>
				<td>Jenny Holland</td>
				<td>Senior recruitment officer</td>
				<td>Human resources</td>
				<td>01509-796566</td>
			</tr>
			<tr>
				<td>99341</td>
				<td>Ellie Morgan</td>
				<td>Senior hardware designer</td>
				<td>Product design</td>
				<td>01509-826960</td>
			</tr>
			<tr>
				<td>23753</td>
				<td>Greg Howells</td>
				<td>Senior software developer</td>
				<td>Development</td>
				<td>01509-478799</td>
			</tr>
			<tr>
				<td>76251</td>
				<td>Bert Linux</td>
				<td>Hardware support specialist</td>
				<td>Support</td>
				<td>01509-545608</td>
			</tr>
			<tr>
				<td>65672</td>
				<td>Clara Mac</td>
				<td>Software support specialist</td>
				<td>Support</td>
				<td>01509-046241</td>
			</tr>
			<tr>
				<td>99345</td>
				<td>Nick Windows</td>
				<td>Networks support specialist</td>
				<td>Support</td>
				<td>01509-777294</td>
			</tr>
		</table>
		<div align="center">
			<p>
				Search:<input type="text"></input>
				<input type="button" value="Submit"></input>
			</p>	
		</div>
	</body>
</html>