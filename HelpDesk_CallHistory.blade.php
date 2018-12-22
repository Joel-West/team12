<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_CallHistory</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>	
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script>
		<script type="text/javascript">
		</script>
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css">
	</head>
	<body onload="WriteTime()">
	<form id="mainform" name="mainform" method="post" action="">
		@csrf
		<div class="titleDiv">
			<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" />
			<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label>
			<h2 id="headerId" style="style=display:inline-block; font-size:30px;">Call History</h2>	
		</div>
		<table id="tbl" border="1">
			<tr>
				<th>Operator Name</th>
				<th>Caller Name</th>
				<th>Time/Date</th>
				<th>Problem</th>
				<th>Reason/Notes</th>
			</tr>
			<tr>
				<td>Alice</td>
				<td>Jim</td>
				<td>12/10/2018, 11:43:20</td>
				<td>MS Paint won't close</td>
				<td>Still a problem, left PC for 3 days and no change. Worried new computer will be required.</td>
			</tr>
			<tr>
				<td>Sam</td>
				<td>Sarah</td>
				<td>11/10/2018, 15:59:21</td>
				<td>Broken Capslock</td>
				<td>Caplock is not functioning.</td>
			</tr>
			<tr>
				<td>Sam</td>
				<td>Ryan</td>
				<td>11/10/2018, 10:31:03</td>
				<td>Chrome not running HTML</td>
				<td>Running Chrome on Mac, HTML code does not display, creating incorrectly blank webpages.</td>
			</tr>
			<tr>
				<td>Sam</td>
				<td>Jenny</td>
				<td>11/10/2018, 09:12:12</td>
				<td>Overheated Computer</td>
				<td>Computer won't turn on.</td>
			</tr>
			<tr>
				<td>Alice</td>
				<td>Ryan</td>
				<td>10/10/2018, 13:15:54</td>
				<td>Speakers Broken</td>
				<td>Speakers crackle but no actual sound output.</td>
			</tr>
			<tr>
				<td>Alice</td>
				<td>Jenny</td>
				<td>10/10/2018, 12:46:49</td>
				<td>Overheated Computer</td>
				<td>Computer crashes when overheating.</td>
			</tr>
			<tr>
				<td>Alice</td>
				<td>Jim</td>
				<td>9/10/2018, 15:20:07</td>
				<td>MS Paint won't close</td>
				<td>MS Paint preventing computer from turning off.</td>
			</tr>
		</table>
	</form>
	</body>
</html>
