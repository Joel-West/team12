<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_ProblemList</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="ExtraCode.js"></script>
		<script type="text/javascript">
			function SaveChanges(page)
			{
				alert("Changes saved.");
				GoToNewPage(page);
			}
			$(document).ready(function () {
    resize_to_fit();
});

function resize_to_fit(){
    var fontsize = $('div#outer div').css('font-size');
    $('div#outer div').css('fontSize', parseFloat(fontsize) - 1);

    if($('div#outer div').height() >= $('div#outer').height()){
        resize_to_fit();
    }
}
		</script>
		<link href="Styles.css" rel="stylesheet" type="text/css">
	</head>
	<body onload="WriteTime()">
		<div class="titleDiv">
			<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('HelpDesk_Home.html');" />
			<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label>
			<h2 id="headerId" style="style=display:inline-block; font-size:30px;">Problem Details</h2>	
		</div>
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
		    <!--<div id="outer" style="width:200px; height:20px; border:1px solid red;">
<div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer mollis dui felis, vel vehicula tortor cursus nec</div>
</div>-->
		<p align="center">
			<input type="button" value="Save Changes" style="font-size:26px; padding: 6px 12px;" onClick="SaveChanges('HelpDesk_Home.html');" />
		</p>	
	</body>
</html>