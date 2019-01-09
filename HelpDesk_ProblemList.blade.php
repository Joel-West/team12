<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_ProblemList</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple other files -->
		<script type="text/javascript">
			function SaveChanges(page) //Function fo saving data back to database.
			{
				alert("Changes saved.");
				document.getElementById("mainform").action = "http://35.204.60.31/" + page; //Assigns form to go to home page on submit.
				document.getElementById("mainform").submit();
			}

		</script>
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
	</head>
	<body onload="WriteTime()">
	<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
		<input type='hidden' name="User" value="<?php echo $_POST['User']; ?>" /> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
		@csrf <!--Token to validates requests to server. -->
		<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
			<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" /> <!-- Back button. -->
			<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label> <!-- Label to contain current data/time. -->
			<h2 id="headerId" style="style=display:inline-block; font-size:30px;">Problem Details</h2>	<!-- Heading containing name of page. -->
		</div>
		<div id="tableDiv">  <!-- Div containing data table. -->
			<table id="tbl" border="1">
				<tr>
					<th>Problem Number</th>
					<th>Problem</th>
					<th>Serial Number</th>
					<th>Problem Type</th>
					<th>Specialist</th>
					<th>Resolved?</th>
					<th>Time/date resolved</th>
					<th>Solution</th>
				</tr>
				<tr>
					<td>5</td>
					<td><div contenteditable>Broken Capslock</div></td>
					<td><div contenteditable>K45nQkCo</div></td>
					<td><div contenteditable>Keyboard problems</div></td>
					<td>Bert Linux</td>
					<td><div contenteditable>No</div></td>
					<td></td>
					<td><div contenteditable><br/></div></td>
				</tr>
				<tr>
					<td>3</td>
					<td><div contenteditable>Speakers Broken</div></td>
					<td><div contenteditable>FLoDZzQF</div></td>
					<td><div contenteditable>Audio device problems</div></td>
					<td>Bert Linux</td>
					<td><div contenteditable>Yes</div></td>
					<td>10/10/2018, 13:18:07</td>
					<td><div contenteditable>Speakers were not turned on.</div></td>
				</tr>
				<tr>
					<td>2</td>
					<td><div contenteditable>Overheated Computer</div></td>
					<td><div contenteditable>z9e3BMDT</div></td>
					<td><div contenteditable>Cooling problems</div></td>
					<td>Bert Linux</td>
					<td><div contenteditable>No</div></td>
					<td></td>
					<td><div contenteditable><br/></div></td>
				</tr>
			</table>	
			<br/>
					<table id="tbl" border="1">
				<tr>
					<th>Problem Number</th>
					<th>Problem</th>
					<th>Operating System</th>
					<th>Software Concerned</th>
					<th>Problem Type</th>
					<th>Specialist</th>
					<th>Resolved?</th>
					<th>Time/date resolved</th>
					<th>Solution</th>
				</tr>
				<tr>
					<td>4</td>
					<td><div contenteditable>Chrome not running HTML</div></td>
					<td><div contenteditable>Mac</div></td>
					<td><div contenteditable>Google Chrome</div></td>
					<td><div contenteditable>Browser problems</div></td>
					<td>Clara Mac</td>
					<td><div contenteditable>Yes</div></td>
					<td>11/10/2018, 10:37:54</td>
					<td><div contenteditable>Firewall was restricting Chrome.</div></td>
				</tr>
				<tr>
					<td>1</td>
					<td><div contenteditable>MS Paint won't close</div></td>
					<td><div contenteditable>Windows</div></td>
					<td><div contenteditable>Microsoft Paint</div></td>
					<td><div contenteditable>Microsoft software problems</div></td>
					<td>Clara Mac</td>
					<td><div contenteditable>No</div></td>
					<td></td>
					<td><div contenteditable><br/></div></td>
				</tr>
			</table>
		</div>	
		<p align="center">
			<input type="button" value="Save Changes" style="font-size:26px; padding: 6px 12px;" onClick="SaveChanges('Home');" /> <!-- Button for submitting changes to table. -->
		</p>
	</form>
	</body>
</html>