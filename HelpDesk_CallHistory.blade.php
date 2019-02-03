<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_CallHistory</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>	<!-- Get JQuery library from google. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple other files -->
		<script type="text/javascript">
			var userData; //Variable containing data about user.
			var currentPage = "CallHistory"; //Variable storing the name of the current page, so it can be passed in the URL to the next page as a 'previous page' variable.
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				WriteTime(); //Function that writes the current time at the top of the page.
			}
		</script>
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
	</head>
	<body onload="Load()">
	<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
		<input type='text' hidden id="user" name="User"/> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
		@csrf <!--Token to validates requests to server. -->
		<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
			<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" /> <!-- Back button. -->
			<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label> <!-- Label to contain current data/time. -->
			<h2 id="headerId" style="style=display:inline-block; font-size:30px;">Call History</h2>	 <!-- Heading containing name of page. -->
		</div>
		<div id="tableDiv"> <!-- Div containing data table. -->
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
		</div>
	</form>
	</body>
</html>
