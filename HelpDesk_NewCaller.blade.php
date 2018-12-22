<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_NewCaller</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="ExtraCode.js"></script>
		<script type="text/javascript">
			var counterSoft = 0;
			var counterHard = 0;
			function LabelChange(){
				if (document.getElementById("Problems").value != ""){
					document.getElementById("Button").value = "Edit Problem";
				}
				else{
					document.getElementById("Button").value = "New Problem";
				}
			}
			function GoToEditProblem(){
				document.getElementById("SaveChange").style.display = "block";
				document.getElementById("Radio").style.visibility = "visible";
				document.getElementById("OuterDiv").style.visibility = "visible";
				document.getElementById("NewProblemDiv").style.visibility = "hidden";
				document.getElementById("NewProblemDiv2").style.visibility = "hidden";
				document.getElementById("FinalSubmit").style.visibility = "hidden";
				if (document.getElementById("Button").value == "Edit Problem"){
					if (document.getElementById("Problems").value == "Broken Capslock"){
						document.getElementById("CheckBox2").style.visibility = "hidden";
						document.getElementById("CheckBox").style.visibility = "visible";
						document.getElementById("CheckBox").checked = false;
						document.getElementById("Software").checked = false;
						document.getElementById("Hardware").checked = true;
						document.getElementById("SoftwareDiv").style.visibility = "hidden";
						document.getElementById("HardwareDiv").style.visibility = "visible";
						document.getElementById("HardwareNo.").value = "K45nQkCo";
						document.getElementById("Problem Types2").value = "Keyboard Problems" ;
					}
					if (document.getElementById("Problems").value == "Overheated Computer"){
						document.getElementById("SaveChange").style.display = "block";
						document.getElementById("CheckBox2").style.visibility = "hidden";
						document.getElementById("CheckBox").style.visibility = "visible";
						document.getElementById("CheckBox").checked = false;
						document.getElementById("Software").checked = false;
						document.getElementById("Hardware").checked = true;
						document.getElementById("SoftwareDiv").style.visibility = "hidden";
						document.getElementById("HardwareDiv").style.visibility = "visible";
						document.getElementById("HardwareNo.").value = "z9e3BMDT";
						document.getElementById("Problem Types2").value = "Cooling Problems" ;
					}
					if (document.getElementById("Problems").value == "MS Paint won't close"){
						document.getElementById("SaveChange").style.display = "block";
						document.getElementById("CheckBox").style.visibility = "hidden";
						document.getElementById("Software").checked = true;
						document.getElementById("Hardware").checked = false;
						document.getElementById("CheckBox2").style.visibility = "visible";
						document.getElementById("CheckBox2").checked = false;
						document.getElementById("SoftwareDiv").style.visibility = "visible";
						document.getElementById("HardwareDiv").style.visibility = "hidden";
						document.getElementById("OS").value = "Windows";
						document.getElementById("SoftwareConc").value = "MS Paint won't close";
						document.getElementById("Problem Types").value = "Microsoft Software Problems";
						
					}
				}
				else{
					document.getElementById("SoftwareDiv").style.visibility = "hidden";
					document.getElementById("HardwareDiv").style.visibility = "hidden";
					document.getElementById("CheckBox").style.visibility = "hidden";
					document.getElementById("CheckBox2").style.visibility = "hidden";
					document.getElementById("CheckBox").checked = false;
					document.getElementById("CheckBox2").checked = false;
					document.getElementById("SolutionDiv").style.visibility = "hidden";
					document.getElementById("SaveChange").style.display = "none";
					document.getElementById("HardwareNo.").value = "";
					document.getElementById("OS").value = "";
					document.getElementById("SoftwareConc").value = "";
					document.getElementById("NewProblem").value = "";
					document.getElementById("Software").checked = false;
					document.getElementById("Hardware").checked = false;
				}
			}
			function SoftwareFunc(){
				document.getElementById("FinalSubmit").style.visibility = "hidden";
				document.getElementById("NewProblemDiv2").style.visibility = "hidden";
				document.getElementById("Hardware").checked = false;
				document.getElementById("HardwareDiv").style.visibility = "hidden";
				document.getElementById("SoftwareDiv").style.visibility = "visible";
				document.getElementById("SolutionDiv").style.visibility = "hidden";
				document.getElementById("CheckBox").style.visibility = "hidden";
				document.getElementById("CheckBox2").style.visibility = "visible";
				document.getElementById("HardwareNo.").value = "";
				document.getElementById("OS").value = "";
				document.getElementById("SoftwareConc").value = "";
				document.getElementById("NewProblem").value = "";
				if (document.getElementById("Problems").value == ""){
					document.getElementById("FinalSubmit").style.visibility = "visible";
					document.getElementById("NewProblemDiv").style.visibility = "visible";
					document.getElementById("CheckBox2").style.visibility = "hidden";
				}
				if (document.getElementById("Software").checked == false){
					document.getElementById("SoftwareDiv").style.visibility = "hidden";
					document.getElementById("CheckBox2").style.visibility = "hidden";
					document.getElementById("FinalSubmit").style.visibility = "hidden";
					document.getElementById("NewProblemDiv").style.visibility = "hidden";
				}
			}
			function HardwareFunc(){
				document.getElementById("FinalSubmit").style.visibility = "hidden";
				document.getElementById("NewProblemDiv").style.visibility = "hidden";
				document.getElementById("Software").checked = false;
				document.getElementById("HardwareNo.").value = "";
				document.getElementById("SoftwareDiv").style.visibility = "hidden";
				document.getElementById("HardwareDiv").style.visibility = "visible";
				document.getElementById("SolutionDiv").style.visibility = "hidden";
				document.getElementById("CheckBox2").style.visibility = "hidden";
				document.getElementById("CheckBox").style.visibility = "visible";
				document.getElementById("HardwareNo.").value = "";
				document.getElementById("OS").value = "";
				document.getElementById("SoftwareConc").value = "";
				document.getElementById("NewProblem").value = "";
				if (document.getElementById("Problems").value == ""){
					document.getElementById("FinalSubmit").style.visibility = "visible";
					document.getElementById("NewProblemDiv2").style.visibility = "visible";
					document.getElementById("CheckBox").style.visibility = "hidden";
				}
				if (document.getElementById("Hardware").checked == false){
					document.getElementById("HardwareDiv").style.visibility = "hidden";
					document.getElementById("CheckBox").style.visibility = "hidden";
					document.getElementById("FinalSubmit").style.visibility = "hidden";
					document.getElementById("NewProblemDiv2").style.visibility = "hidden";
				}
			}
			function SolvedSoft(){
				if((counterSoft % 2) == 0){
					document.getElementById("SolutionDiv").style.visibility = "visible";
					counterSoft++;
				}
				else{
					document.getElementById("SolutionDiv").style.visibility = "hidden";
					counterSoft++;
				}
			}
			function SolvedHard(){
				if((counterHard % 2) == 0){
					document.getElementById("SolutionDiv").style.visibility = "visible";
					counterHard++;
				}
				else{
					document.getElementById("SolutionDiv").style.visibility = "hidden";
					counterHard++;
				}
			}
		</script>
		<link href="Styles.css" rel="stylesheet" type="text/css">
	</head>
	
	<body onload="WriteTime()">	
		<div class="titleDiv">
			<label id="dtLabel" style="font-size:26px; position:absolute; margin-left:85%"></label>
			<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('HelpDesk_Home.html');" />
			<h2 id="headerId" style="display:inline-block; font-size:30px; position:absolute; margin-left:1%">Call Details</h2>
		</div>
		
		<div class="center" style="position:absolute; margin-left: 36%; top:100px;">
			The Operator logging the new call is Alice. <br><br>
			Caller Name:<input type="text" name="CallerName" id="CallerName"><br><br>
			Time and Date will be recorded on Submit. <br><br>
			Reason for Call/Notes: <br>
			<textarea rows="4" cols="50" style="resize:none">
			</textarea>
		</div>
		
		<br>
		<br>
		
		<div class="center" style="position:absolute; margin-left: 38%; top:325px;">
			Select Problem:
			<select onchange="LabelChange()" id="Problems">
				<option value=""></option>
				<option value="Broken Capslock">Broken Capslock</option>
				<option value="Overheated Computer">Overheated Computer</option>
				<option value="MS Paint won't close">MS Paint won't close</option>
			</select>
			<input type="button" value="New Problem" id="Button" onclick="GoToEditProblem()"/>
		</div>
		
		<br>
		
		<div id="Radio" class="center" style="visibility:hidden; position:absolute; margin-left: 44%; top:350px;">
			<form>
				<input type="checkbox" name="Software" id="Software" onclick="SoftwareFunc();" value="software"> Software
				<input type="checkbox" name="Hardware" id="Hardware" onclick="HardwareFunc();" value="hardware"> Hardware<br>
			</form>
		</div>
		
		<br/>
		
		<div id = "OuterDiv" class="centre" style="visibility:hidden; position: absolute; margin-left: 36%; top:375px; width:1000px;">
			
			<div id="HardwareDiv" class="center" style="visibility:hidden; position: absolute;">
				<label style="margin-left: 50%;">Serial No. :</label> <input type="text" name="HardwareNo." id="HardwareNo." style="margin-left: 45%;"><br><br>
				<label style="margin-left: 50%;">Select Problem Type:</label>
				<select onchange="" id="Problem Types2" style="margin-left: 45%;">
					<option value="Keyboard Problems">Keyboard Problems</option>
					<option value="Audio Device Problems">Mouse Problems</option>
					<option value="Keyboard Problems">Monitor Problems</option>
					<option value="Audio Device Problems">Audio Device Problems</option>
					<option value="Cooling Problems">Cooling Problems</option>
					<option value="Misc Problems">Misc Problems</option>
				</select><br><br>
				
				<div id="NewProblemDiv2" style="visibility:hidden">
					<label style="margin-left: 50%;">New Problem:</label> <input type="text" name="NewProblem" id="NewProblem" style="margin-left: 45%;"><br><br>
				</div>
				
				<div id="CheckBox" style="visiility:hidden" >
					<input type="checkbox" name="Checkbox" value="Solved" onclick="SolvedHard();" style="margin-left: 45%;"> Solved <br><br>
				</div>
			</div>
			
			<div id="SoftwareDiv" class="center" style="visibility:hidden; position: absolute; ">
				<label style="margin-left: 30%;">Operating System:</label> <input type="text" name="OS" id="OS" style="margin-left: 30%;"><br><br>
				<label style="margin-left: 30%;">Software Concerned:</label> <input type="text" name="SoftwareConc" id="SoftwareConc" style="margin-left: 30%;"><br><br>
				<label style="margin-left: 30%;">Select Problem Type:</label>
				<select onchange="" id="Problem Types" style="margin-left: 30%;">
					<option value="Browser Problems">Browser Problems</option>
					<option value="Driver Problems">Driver Problems</option>
					<option value="System Software Problems">System Software Problems</option>
					<option value="Operating System Problems">Operating System Problems</option>
					<option value="Apple Software Problems">Apple Software Problems</option>
					<option value="Microsoft Software Problems">Microsoft Software Problems</option>
					<option value="Misc Problems">Misc Problems</option>
				</select><br><br>
				
				<div id="NewProblemDiv" style="visibility:hidden">
					<label style="margin-left: 31%">New Problem:</label><br><input type="text" name="NewProblem" id="NewProblem" style="margin-left: 29%"><br><br>
				</div>
				
				<div id="CheckBox2" style="visibility:hidden">
					<input type="checkbox" name="SolvedCheck" id="SolvedCheck" onclick="SolvedSoft();" style="margin-left: 30%;"> Solved <br><br>
				</div>
			</div>
			
			<div id="SolutionDiv" style="visibility:hidden; position: absolute; top: 250px;">
				Solution Text:<br>
				<textarea rows="4" cols="50" style="resize:none;">
				</textarea>
				<br>
			</div>
			
			<div id="FinalSubmit" style="visibility:hidden; position:absolute; margin-left: 10%; top: 250px;">
				<input type="button" value="Save New Problem" style="font-size:26px; padding: 6px 12px;" onClick="GoToNewPage('HelpDesk_Home.html');" />
			</div>
			
			<div id="SaveChange" style="display:none; position:absolute; margin-left: 12%; top: 400px;">
				<input type="button" id="SaveChangeButton"; value="Save Problem" style="font-size:26px; padding: 6px 12px;" onClick="GoToNewPage('HelpDesk_Home.html');" />
			</div>
			
		</div>
	</body>
</html>