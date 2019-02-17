<!DOCTYPE html>
<html lang="en">
  <head>
	<title>New Caller</title>
    <meta charset="utf-8"> 
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="https://www.goodfreephotos.com/albums/vector-images/screwdriver-and-wrench-vector-clipart.png">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script>
	<script type="text/javascript">
	  var userData; //Variable containing data about user
	  var currentPage = "NewCaller"; //Variable containing current page user is on
	  var flag = 0; //Flag, 0 if problem the caller is calling about is new, 1 if it is about a existing problem
	  var problemNumber; //Global Variable to hold the problem number of the chosen unsolved problem
	  var problemTypeList = []; //Holds all the problemType
	  var specialistIDList = []; //Holds specialistID 
	  var count = []; //Holds job count for specialists
	  var specialistList = []; //Holds specialist names
	  var problemTypeVar; //Holds the problem type of the problem given by the operator
	  resolvedOptions = {day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: false} //Sets the time format.
	  var startDT = new Date();
	  var resolvedDTCurrent = "";
	  
	  function Load(){ //Runs on load of the page
		problemCreation();
		WriteTime();
		userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
		SetNavSettings();
		startDT = startDT.toLocaleDateString("en-GB", resolvedOptions);
	  }
	  
	  function autofillId(){ //Autofills ID field depending on Caller Name field
	    var Username=document.getElementById("CallerName").value;
	    if (Username.includes("'")){
		} else{
		  sql = "SELECT userID FROM tblPersonnel WHERE name = '" + Username +"'"; 
		  $.get("Query.php", {'sql':sql, 'returnData':true},function(json){ //Query to get userID from the given name
			if (json && json[0]){
			  if (json[1]){ //If multiple names were found, alert the user
				alert("There are multiple " + Username + ". Please type their user ID as well");
			  } else{
				document.getElementById("CallerID").value = json[0].userID;
			  }
			} else{
			  document.getElementById("CallerID").value = "";
			}
		  },'json');
	    }
      }
	  
	  function autofillName(){ //Autofills Caller Name field depending on ID field
	    var UserID=document.getElementById("CallerID").value;
	    if (UserID.includes("'")){
		} else{
		  sql = "SELECT name FROM tblPersonnel WHERE userID = '" + UserID +"'"; 
		  $.get("Query.php", {'sql':sql},function(json){
			if (json && json[0]){
			  document.getElementById("CallerName").value = json[0].name;
			} else{
			  document.getElementById("CallerName").value = "";
			}
		  },'json');
	    }
      }
	  
	  function problemCreation(){ //Dynamically fills the dropdown box for deciding if the call is about a new or existing problem
	    var html = "<a class='dropdown-item'>New Problem</a><div class='dropdown-divider'></div>";
		html += "<h6 class='dropdown-header'>Existing Problems</h6>";
		var sql = "SELECT problem, problemNumber FROM tblProblem WHERE resolved = 'no'"; //Query to get all unresolved problems
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			for (i = 0; i < json.length; i++){
			  html+="<a class='dropdown-item' >" + json[i].problem + ". Problem Number: " + json[i].problemNumber + "</a>";
			}
		    document.getElementById("dropdown-menu").innerHTML = html;
		  }
		},'json');
	  }
	  
	  //JQuery to check when dropdown dynamically filled in the above function was clicked
      $(document).on('click', '#dropdown-menu a', function(){
        $("#dropdownButton:first-child").text($(this).text()); // When clicked set the button text to the option clicked
        $("#dropdownButton:first-child").val($(this).text()); // When clicked set the button value to the option clicked
	    problem();
      });
	  
	  function problem(){ //Shows/hides divs depending on if the call is about a new problem or a existing ,unsolved problem.
		$("#dropdownButton2:first-child").text('Choose Problem');
        $("#dropdownButton2:first-child").val('');
		$('#Checkbox').prop('checked', false); //Sets the solved checkbox to false
		checkbox();
		if(document.getElementById("dropdownButton").value == "New Problem"){ //If the call is about a new problem
		  flag = 0;
		  newProblemCreation(); //Create the dropdown box which allows the operator to choose more exact details about the new problem
		  //Hiding and showing divs to reformat the page to the current situation
		  $('#problemTypeCollapse').collapse('hide');
		  $('#updateDiv1').collapse('show');
		  $('#updateDiv2').collapse('show');
		  $('#serialNumberCollapse').collapse('hide');
		  $('#OSCollapse').collapse('hide');
		  $('#concernCollapse').collapse('hide');
		  $('#concernCollapseDiv').collapse('hide');
		  $('#concernCollapseDiv2').collapse('hide');
		  $('#result2Collapse').collapse('hide');
		  $('#newNewProblemCollapse').collapse('hide');
		  $('#newProblemCollapse').collapse('show');
		  $('#existingProblemCollapse').collapse('hide');
		}
		else{
		  flag = 1;
		  $('#newProblemCollapse').collapse('hide');
		  $('#result2Collapse').collapse('hide');
		  $('#updateDiv1').collapse('hide');
		  $('#updateDiv2').collapse('hide');
		  problemNumber = document.getElementById("dropdownButton").value;
		  problemNumber = problemNumber.split(" ");
		  problemNumber = problemNumber[problemNumber.length - 1];
		  getGenericProblemType(problemNumber); //Takes the problem number of the chosen existing problem and passes it
		}
	  }
	  
	  function newProblemCreation(){ //Dynamically fills the dropdown menu which allows the operator to choose options for the new problem the caller is calling about
		var html = "<form class ='px-4 py-3'><div class='form-group'><label for='dropdownSearch'>Search</label>"
		html += "<input type='text' class='form-control' id='dropdownSearch2' placeholder='Search' onkeyup='filter(2)'></div></form>" //Adds the search bar
	    html += "<div class='dropdown-divider'></div><a class='dropdown-item'>New Problem</a><form class='px-4 py-3'>";
		html += "<input type='text' class='form-control' id='newProblemInput' placeholder='Enter New Problem'></form><div class='dropdown-divider'></div>"; //Adds the new problem box
		html += "<h6 class='dropdown-header'>Previously Problems</h6>";
		var sql = "SELECT problem,problemNumber FROM tblProblem";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			for (i = 0; i < json.length; i++){
			  html+="<a class='dropdown-item' >" + json[i].problem + ". Problem Number: " + json[i].problemNumber + "</a>";
			}
		    document.getElementById("dropdown-menu2").innerHTML = html;
		  }
		},'json');
	  }
	  
	  function filter(end){ //Filters the contents of the dropdown depending on the contents of its search bar
	    var input = document.getElementById("dropdownSearch" + end); //Each dropdownSearch is numbered, the end number is passed to this function so filter can filter the correct dropdown box
		var x = document.getElementById("dropdown-menu" + end).getElementsByTagName("a"); //Gets all a tags, all options in dropdownbox
		for (i = 0; i < x.length; i++){
		  txtValue = x[i].textContent || x[i].innerText;
		  if (txtValue.toUpperCase().indexOf(input.value.toUpperCase()) > -1) { //If the search is matching up then keep its css style, if it isn't then remove its style making it disappear
		    x[i].style.display = "";
		  }
		  else{
		    x[i].style.display = "none";
		  }
		}
	  }
	  
	  $(document).on('click', '#dropdown-menu2 a', function(){ //Checks if the dropdown containing the options of the new problem has been clicked
		if ($(this).text() == "New Problem"){ //If the new problem is entirely new, no instance of this problem has occured in the past
		  $("#dropdownButton2:first-child").text(document.getElementById("newProblemInput").value);
		  $("#dropdownButton2:first-child").val(document.getElementById("newProblemInput").value);
		  $(':radio').prop('checked',false);
		  $('#newNewProblemCollapse').collapse('show');
		}
		else{ //A similar previous problem was picked
          $("#dropdownButton2:first-child").text($(this).text());
          $("#dropdownButton2:first-child").val($(this).text());
		  $('#result2Collapse').collapse('hide');
		  problemNumber = document.getElementById("dropdownButton2").value;
		  problemNumber = problemNumber.split(" ");
		  problemNumber = problemNumber[problemNumber.length - 1];
		  getGenericProblemType(problemNumber);
		}
      });
	  
	  function getGenericProblemType(parent){ //Collapses the correct divs based on the problem type 
		var sql;
		$("#dropdownButton4:first-child").text('Choose Specialist:');
        $("#dropdownButton4:first-child").val('');
		sql = 'SELECT problemType,problemSubType FROM tblProblem WHERE problemNumber = "' + parent + '";';//Gets the problemType and problemSubType of the chosen problem
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			if(json[0].problemType == "Hardware"){ //If a hardware problem
			  $('#OSCollapse').collapse('hide');
			  $('#concernCollapse').collapse('hide');
			  $('#concernCollapseDiv').collapse('hide');
			  $('#concernCollapseDiv2').collapse('hide');
			  document.getElementById("RadiosH").checked = true;
			  $("#dropdownButtonSerial:first-child").text('Choose Serial Number');
			  $("#dropdownButtonSerial:first-child").val('');
			  if (flag == 1){ //If a existing problem was picked
				var sqlH = "SELECT serialNumber FROM tblProblem WHERE problemNumber = '" + problemNumber + "';"; //Get the serial number of the affected hardware
				$.get("Query.php", {'sql':sqlH, 'returnData':true},function(json){
				  if(json && json[0]){
					$("#dropdownButtonSerial:first-child").text(json[0].serialNumber); //Update the button with the affected hardwares serial number
					$("#dropdownButtonSerial:first-child").val(json[0].serialNumber);
				  }
				},'json');
			  }
			  radios(1); //Runs radios, the function which occurs when the radio button is clicked
			}else if(json[0].problemType == "Software"){
			  $('#serialNumberCollapse').collapse('hide');
			  document.getElementById("RadiosS").checked = true;
			  $("#dropdownButtonOS:first-child").text('Choose Operating System');
			  $("#dropdownButtonOS:first-child").val('Choose Operating System');
			  $("#dropdownButtonConcern:first-child").text('Choose Concerning Software');
			  $("#dropdownButtonConcern:first-child").val('Choose Concerning Software');
			  if (flag == 1){
			    var sqlS = "SELECT operatingSystem, softwareConcerned FROM tblProblem WHERE problemNumber = '" + problemNumber + "';";
				$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
				  if(json && json[0]){
					$("#dropdownButtonOS:first-child").text(json[0].operatingSystem);
					$("#dropdownButtonOS:first-child").val(json[0].operatingSystem);
					$("#dropdownButtonConcern:first-child").text(json[0].softwareConcerned);
					$("#dropdownButtonConcern:first-child").val(json[0].softwareConcerned);
				  }
				},'json');
			  }
			  radios(2);
			}else{
			  $('#serialNumberCollapse').collapse('hide');
			  $('#OSCollapse').collapse('hide');
			  $('#concernCollapse').collapse('hide');
			  $('#concernCollapseDiv2').collapse('hide');
			  document.getElementById("RadiosN").checked = true;
			  radios(3);
			}
			$('#newNewProblemCollapse').collapse('show');
			$("#dropdownButton3:first-child").text(json[0].problemSubType);
            $("#dropdownButton3:first-child").val(json[0].problemSubType);
			populateSpecialist(json[0].problemSubType); //Populates the specialist dropdown box
			
		  }
		},'json');
	  }
	  
	  function radios(num){ //Occurs when the radio buttons are clicked to choose the general problem type, collpases the correct divs and calls specific functions depending on the decision
		var html = "<form class ='px-4 py-3'><div class='form-group'><label for='dropdownSearch'>Search</label>"
		html += "<input type='text' class='form-control' id='dropdownSearch3' placeholder='Search' onkeyup='filter(3)'></div></form>"
	    html += "<div class='dropdown-divider'></div><h6 class='dropdown-header'>Problem Types</h6>";
		document.getElementById("dropdown-menu3").innerHTML = html;
		$("#dropdownButton3:first-child").text('Choose Problem Type');
		$("#dropdownButton3:first-child").val('');
		$('#result2Collapse').collapse('hide');
		if (num==1){ //If Hardware
		  $('#OSCollapse').collapse('hide');
		  $('#concernCollapse').collapse('hide');
		  $('#concernCollapseDiv').collapse('hide');
		  $('#concernCollapseDiv2').collapse('hide');
		  document.getElementById("dropdown-menu3").innerHTML += "<a class='dropdown-item' >Hardware problem</a>";
		  findAllChildren("Hardware problem", html); //Gets all branching problem types from Hardware
		  setTimeout(createSerialNumber,200); //Timeouts needed to allow the collapse animation to occur, not allowing the animation to occur causes the page to mess up for a brief second, looking unprofessional
		}
		else if (num==2){ //If Software
		  $('#serialNumberCollapse').collapse('hide');
		  document.getElementById("dropdown-menu3").innerHTML += "<a class='dropdown-item' >Software problem</a>";
		  findAllChildren("Software problem", html);
		  setTimeout(createSoftwareDropdown,300);
		}
		else{ //If Network
		  $('#serialNumberCollapse').collapse('hide');
		  $('#OSCollapse').collapse('hide');
		  $('#concernCollapse').collapse('hide');
		  $('#concernCollapseDiv').collapse('show');
		  $('#concernCollapseDiv2').collapse('hide');
		  document.getElementById("dropdown-menu3").innerHTML += "<a class='dropdown-item' >Network problem</a>";
		  findAllChildren("Network problem", html);		  
		}
		html="</div>";
		document.getElementById("dropdown-menu3").innerHTML += html;
		$('#problemTypeCollapse').collapse('show');
	  }
	  
	  function findAllChildren(parent,html){ //Finds all branching typenames from a given typename e.g. if given Hardware it will find all problem types related to Hardware, like Keyboard problem.
		var sql = "SELECT typeName FROM tblProblemType WHERE generalisation = '" + parent + "';";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			for (i = 0; i < json.length; i++){
			  html="<a class='dropdown-item' >" + json[i].typeName + "</a>";
			  document.getElementById("dropdown-menu3").innerHTML += html;
			  findAllChildren(json[i].typeName,html);
			}
		  }
		},'json');
		return(html); 
	  }
	  
	  function createSerialNumber(){ //Fills in the dropdown box for the serial number
		var html = "<form class ='px-4 py-3'><div class='form-group'><label for='dropdownSearch'>Search</label>"
		html += "<input type='text' class='form-control' id='dropdownSearch5' placeholder='Search' onkeyup='filter(5)'></div></form>"
	    html += "<div class='dropdown-divider'></div>";
		html += "<h6 class='dropdown-header'>Serial Numbers</h6>";
		var sql = "SELECT * FROM tblEquipment";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			for (i = 0; i < json.length; i++){
			  html+="<a class='dropdown-item' >" + json[i].serialNumber + "(" + json[i].equipmentMake + " " + json[i].equipmentType + ")</a>";
			}
		    document.getElementById("dropdown-menu5").innerHTML = html;
		  }
		  $('#serialNumberCollapse').collapse('show');
		},'json');
	  }
	  
	  function createSoftwareDropdown(){ //Fills in the dropdown box for the concerned software and operating system
		var html = "<form class ='px-4 py-3'><div class='form-group'><label for='dropdownSearch'>Search</label>"
		html += "<input type='text' class='form-control' id='dropdownSearch6' placeholder='Search' onkeyup='filter(6)'></div></form>"
	    html += "<div class='dropdown-divider'></div><a class='dropdown-item'>New OS</a><form class='px-4 py-3'>";
		html += "<input type='text' class='form-control' id='newOSInput' placeholder='Enter New OS'></form><div class='dropdown-divider'></div>";
		html += "<h6 class='dropdown-header'>Operating Systems</h6>";
		var sql = "SELECT operatingSystem FROM tblProblem WHERE operatingSystem != ' ' GROUP BY operatingSystem;";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			for (i = 0; i < json.length; i++){
			  html+="<a class='dropdown-item' >" + json[i].operatingSystem + "</a>";
			}
		    document.getElementById("dropdown-menu6").innerHTML = html;
		  }
		  var concernHtml = "<form class ='px-4 py-3'><div class='form-group'><label for='dropdownSearch'>Search</label>"
		  concernHtml += "<input type='text' class='form-control' id='dropdownSearch7' placeholder='Search' onkeyup='filter(7)'></div></form>"
	      concernHtml += "<div class='dropdown-divider'></div><a class='dropdown-item'>New Concerned Software</a><form class='px-4 py-3'>";
		  concernHtml += "<input type='text' class='form-control' id='newConcernInput' placeholder='Enter New Concerned Software'></form><div class='dropdown-divider'></div>";
		  concernHtml += "<h6 class='dropdown-header'>Previous Software</h6>";
		  var concernSql = "SELECT softwareConcerned FROM tblProblem WHERE softwareConcerned != ' ' GROUP BY softwareConcerned;"
		  $.get("Query.php", {'sql':concernSql, 'returnData':true},function(json){
			if (json && json[0]){
			  for (i = 0; i < json.length; i++){
			    concernHtml+="<a class='dropdown-item' >" + json[i].softwareConcerned + "</a>";
			  }
		      document.getElementById("dropdown-menu7").innerHTML = concernHtml;
		    }
			$('#OSCollapse').collapse('show');
			$('#concernCollapse').collapse('show');
			$('#concernCollapseDiv').collapse('show');
			$('#concernCollapseDiv2').collapse('show');
		  },'json');
		},'json');
	  }
	  
	  $(document).on('click', '#dropdown-menu5 a', function(){ //Occurs on click of the serial number dropdown
        $("#dropdownButtonSerial:first-child").text($(this).text());
        $("#dropdownButtonSerial:first-child").val($(this).text());
      });
	  
	  $(document).on('click', '#dropdown-menu6 a', function(){ //Occurs on click of the OS dropdown
		if ($(this).text() == "New OS"){
		  $("#dropdownButtonOS:first-child").text(document.getElementById("newOSInput").value);
		  $("#dropdownButtonOS:first-child").val(document.getElementById("newOSInput").value);
		}
		else{
          $("#dropdownButtonOS:first-child").text($(this).text());
          $("#dropdownButtonOS:first-child").val($(this).text());
		}
      });
	  
	  $(document).on('click', '#dropdown-menu7 a', function(){ //Occurs on the click of the concerned software dropdown
		if ($(this).text() == "New Concerned Software"){
		  $("#dropdownButtonConcern:first-child").text(document.getElementById("newConcernInput").value);
		  $("#dropdownButtonConcern:first-child").val(document.getElementById("newConcernInput").value);
		}
		else{
          $("#dropdownButtonConcern:first-child").text($(this).text());
          $("#dropdownButtonConcern:first-child").val($(this).text());
		}
      });
	  
	  $(document).on('click', '#dropdown-menu3 a', function(){ //Occurs on the click of the problem Type dropdown
        $("#dropdownButton3:first-child").text($(this).text());
        $("#dropdownButton3:first-child").val($(this).text());
		populateSpecialist($(this).text());
		checkbox();
      });
	  
	  function populateSpecialist(problemType){ //Resets global variables
		problemTypeList = [];
		problemTypeVar = "";
		problemTypeVar = problemType;
		populateProblemTypeList(problemType);
	  }
	  
	  function populateProblemTypeList(problemType){ //Fills the problemTypeList with problemtypes with a specific generalisation
		var sql = "SELECT generalisation FROM tblProblemType WHERE typeName = '" + problemType + "';";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			if (json[0].generalisation == null){
			  problemTypeList.push(problemType);
			  specialistList = [];
			  count = [];
			  specialistIDList = []; 
			  populateIDList(0);
			}
			else{
			  problemTypeList.push(problemType);
			  populateProblemTypeList(json[0].generalisation);
			}
		  }
	    },'json');
	  }
	  
	  //This function simulates a for loop through recursion, this is done as using a for loop to loop the query causes asynchronus issues but recursion doesn't
	  function populateIDList(a){ //Populates the id list with specialists to each problem type in the problem type list
		sql = "SELECT userID FROM tblSpecialisation WHERE typeName = '" + problemTypeList[a] + "';";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			for (t = 0; t < json.length; t++){
			  if (specialistIDList.indexOf(json[t].userID) == -1){
				specialistIDList.push(json[t].userID);
			  }
			}
		  }
		  if (a == problemTypeList.length - 1){
			populateCount(0);
		  }
		  a++;
		  populateIDList(a);
		},'json');
	  }
	  
	  //This function simulates a for loop through recursion, this is done as using a for loop to loop the query causes asynchronus issues but recursion doesn't
	  function populateCount(b){ //Populates count with the amount of jobs each specialist in IDList
		sql = "SELECT COUNT(problem) AS occurence FROM tblProblem WHERE specialistID = " + specialistIDList[b] + " AND resolved = 'No';";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			count.push(json[0].occurence);
		  }
		  if (b == specialistIDList.length - 1){
			populateSpecialistList(0);
		  }
		  b++;
		  populateCount(b);
		},'json');
	  }
	  
	  //This function simulates a for loop through recursion, this is done as using a for loop to loop the query causes asynchronus issues but recursion doesn't
	  function populateSpecialistList(c){ //Populates this list with the names of the specialists in the ID list
		sql = "SELECT name FROM tblPersonnel WHERE userID = " + specialistIDList[c] + ";";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			specialistList.push(json[0].name);
		  }
		  if (c == specialistIDList.length - 1){
			fillSpecialistComboBox();
		  }
		  c++;
		  populateSpecialistList(c);
		},'json');
	  }
	  
	  function fillSpecialistComboBox(){ //Fills the specialist combo box with the information gathered in previous functions
		var sql = "SELECT userID FROM tblSpecialisation WHERE typeName = '" + problemTypeVar + "';";
		var html = "";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			html += "<h6 class='dropdown-header'>Specialists to exact problem type</h6>"
			for (i = 0; i < json.length; i++){
			  html += "<a class='dropdown-item' >" + specialistList[i] + " (" + count[i] + " current jobs) (" + specialistIDList[i] + ")</a>"
			}
			specialistList.splice(0,i); //Splices the added users from the specialistList, so they aren't repeated
			specialistIDList.splice(0,i);
			count.splice(0,i);
			html += "<div class='dropdown-divider'></div>"
		  }
		  if (specialistList.length > 0){
			html+= "<h6 class='dropdown-header'>Specialists to a generalisation of the problem type</h6>";
			for (j = 0; j < specialistList.length; j++){
		      html+= "<a class='dropdown-item' >" + specialistList[j] + " (" + count[j] + " current jobs) (" + specialistIDList[j] + ")</a>"
			}
		  }
		  document.getElementById("dropdown-menu4").innerHTML = html;
	      $('#result2Collapse').collapse('show');
		},'json');
	  }
	  
	  $(document).on('click', '#dropdown-menu4 a', function(){
        $("#dropdownButton4:first-child").text($(this).text());
        $("#dropdownButton4:first-child").val($(this).text());
      });
	  
	  function checkbox(){ //Runs when the solution checkbox is clicked
		if(document.getElementById("Checkbox").checked == true){
		  var resolvedDT = new Date();
		  resolvedDTCurrent = resolvedDT.toLocaleDateString("en-GB", resolvedOptions);
		  solutionCreation(); 
		}
		else{
		  $('#solutionCollapse').collapse('hide');
		}
	  }
		
	  function solutionCreation(){ //Fills and shows the solution dropdown box underneath the solution text area, only appears if the selected problem has a previous problem with the same type which has been solved.
		$("#solution").val('');
		var sql = "SELECT solution FROM tblProblem WHERE problemSubType = '" + problemTypeVar + "';";
		var html = "";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			if (json[0].solution != ''){
		      for (i = 0; i<json.length; i++){
			    html+= "<a class='dropdown-item' data-toggle='tooltip' data-placement='right' data-title='" + json[i].solution + "'>" + problemTypeVar + "</a>";
		      }
		      document.getElementById("dropdown-menuSolution").innerHTML = html;
			  $('#specialistSolutionComboBox').collapse('show');
			  $('#dropdown-menuSolution a').tooltip();
		      $('#solutionCollapse').collapse('show');
		    }
			else{
			  $('#specialistSolutionComboBox').collapse('hide');
			  $('#solutionCollapse').collapse('show');
			}
		  }
		},'json');
	  }
	  
	  $(document).on('click', '#dropdown-menuSolution a', function(){ //Occurs on click of the solution dropdown
        $("#dropdownButtonSolution:first-child").text($(this).text());
        $("#dropdownButtonSolution:first-child").val($(this).text());
		$("#solution").val($(this).attr('data-title'));
      });
	  
	  function Validation(){ //Validates the data before being added to the database
		var validation = 0;
		var radioValue = $('input[name=Radios]:checked').val();
		if (radioValue == "Hardware"){
		  var serialNumber = document.getElementById('dropdownButtonSerial').value;
		  if (serialNumber == "Choose Serial Number" || serialNumber == ""){
			validation = 1;
		  }
		} else if (radioValue == "Software"){
		  var OS = document.getElementById('dropdownButtonOS').value;
		  var concernSoftware = document.getElementById('dropdownButtonConcern').value;
		  if ((OS == "Choose Operating System" || OS == "") || (concernSoftware == "Choose Concerning Software" || concernSoftware == "")){
			validation = 1;
		  }
		}
		var specialistID = specialist.split(" ");
		specialistID = specialistID[specialistID.length - 1];
		try{
		  specialistID = specialistID.replace("(", "");
		  specialistID = specialistID.replace(")", "");
		}catch(err){
		  validation = 1;
		}
		
		var dropdownButton2Var = document.getElementById('dropdownButton2').value;
		if(dropdownButton2Var == "" || dropdownButton2Var.includes('"')){
		  validation = 1;
		}
		
		var notesVar = document.getElementById(notes).value; //Basic sql injection precautions
		if(notesVar.includes('"')){
		  validation = 1;
		}
		
		var solutionVar = document.getElementById(solution).value;
		if(solutionVar.includes('"')){
		  validation = 1;
		}
		
		var callerID = document.getElementById("CallerID").value;
		sql = "SELECT userID FROM tblPersonnel WHERE userID = '" + callerID + "';";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){

		  if(json&&json[0]){
		  }
		  else{
			validation = 1;
		  }
		  
		  if(validation == 1){
		    alert("There is a invalid field");
		  }
		  else{
			console.log("ENTER");
		    SaveChanges();
		  }
		},'json');
	  }
	  
	  function SaveChanges(){ //Handles saving the data to the problem table, handles Inserts and Updates
		sql = "";
		var radioValue = $('input[name=Radios]:checked').val();
		var problem = document.getElementById('dropdownButton2').value;
		var specialist = document.getElementById('dropdownButton4').value;
		var specialistID = specialist.split(" ");
		specialistID = specialistID[specialistID.length - 1];
		specialistID = specialistID.replace("(", "");
		specialistID = specialistID.replace(")", "");
		var subProblemType = document.getElementById('dropdownButton3').value;
		var resolved = "";
		if ($('#Checkbox').is(":checked")){
		  resolved = "Yes";
		  var dateTime = resolvedDT;
		}
		else{
		  resolved = "No";
		  var dateTime = "";
		}
		var solution = document.getElementById("solution").value;
		if (document.getElementById('dropdownButton').value = "New Problem"){ //New Problem requires a insert
		  if (radioValue == "Hardware"){
			var problemType = "Hardware";
			var serialNumber = document.getElementById('dropdownButtonSerial').value;
			serialNumber = serialNumber.split("(");
			serialNumber = serialNumber[0];
		    sql += "INSERT INTO tblProblem VALUES ";
		    sql += "(NULL, '" + problem + "', '" + problemType + "', '" + subProblemType + "', '" + serialNumber + "', '', '', '" + specialistID + "', '" + resolved + "', '" + dateTime + "', '" + solution + "');";
		    alert(sql);
			
			$.get("Query.php", {'sql':sql, 'returnData':false},function(json){
			  if(json && json[0]){ //If result of php file was a json array.					
			    alert(json);
			    alert(json[0]);
			  }
			},'json');
			
			setTimeout(insertCall, 100);
		  }
		  
		  else if (radioValue == "Software"){
			var problemType = "Software";
			var OS = document.getElementById('dropdownButtonOS').value;
			var concernSoftware = document.getElementById('dropdownButtonConcern').value;
			sql += "INSERT INTO tblProblem VALUES ";
			sql += "(NULL, '" + problem + "', '" + problemType + "', '" + subProblemType + "', '', '" + OS + "', '" + concernSoftware + "', '" + specialistID + "', '" + resolved + "', '" + dateTime + "', '" + solution + "');";
			alert(sql);
			
			$.get("Query.php", {'sql':sql, 'returnData':false},function(json){
			  if(json && json[0]){ //If result of php file was a json array.					
			    alert(json);
			    alert(json[0]);
			  }
			},'json');
			
			setTimeout(insertCall, 100);
		  }
		  
		  else if (radioValue == "Network"){
			sql += "INSERT INTO tblProblem VALUES ";
			sql += "(NULL , '" + problem + "', '" + problemType + "', '" + subProblemType + "', '', '', '', '" + specialistID + "', '" + resolved + "', '" + dateTime + "', '" + solution + "');";
		    alert(sql);
			
			$.get("Query.php", {'sql':sql, 'returnData':false},function(json){
			  if(json && json[0]){ //If result of php file was a json array.					
			    alert(json);
			    alert(json[0]);
			  }
			},'json');
			
			setTimeout(insertCall, 100);
		  }
		}
		else{ //Existing problem, therefore needs a update
		  if (radioValue == "Hardware"){
			var problemType = "Hardware";
			var serialNumber = document.getElementById('dropdownButtonSerial').value;
			serialNumber = serialNumber.split("(");
			serialNumber = serialNumber[0];
		    sql += "UPDATE tblProblem SET problemType = '" + problemType + "', problemSubType = '" + subProblemType + "', serialNumber = '" + serialNumber + "', specialistID = '" + specialistID + "', resolved = '" + resolved + "', solution = '" + solution + "' WHERE problemNumber = '" + problemNumber + "';";
		    alert(sql);
			
			$.get("Query.php", {'sql':sql, 'returnData':false},function(json){
			  if(json && json[0]){ //If result of php file was a json array.					
			    alert(json);
			    alert(json[0]);
			  }
			},'json');
			
			setTimeout(insertCall, 100);
		  }
		  else if (radioValue == "Software"){
			var problemType = "Software";
			var OS = document.getElementById('dropdownButtonOS').value;
			var concernSoftware = document.getElementById('dropdownButtonConcern').value;
			sql += "UPDATE tblProblem SET problemType = '" + problemType + "', problemSubType = '" + subProblemType + "', operatingSystem = '" + OS + "', softwareConcerned = '" + concernSoftware + "', specialistID = '" + specialistID + "', resolved = '" + resolved + "', solution = '" + solution + "' WHERE problemNumber = '" + problemNumber + "';";
		    alert(sql);
			
			$.get("Query.php", {'sql':sql, 'returnData':false},function(json){
			  if(json && json[0]){ //If result of php file was a json array.					
			    alert(json);
			    alert(json[0]);
			  }
			},'json');
			
			setTimeout(insertCall, 100);
		  }
		  else if (radioValue == "Network"){
			var problemType = "Network";
			sql += "UPDATE tblProblem SET problemType = '" + problemType + "', problemSubType = '" + subProblemType + "', specialistID = '" + specialistID + "', resolved = '" + resolved + "', solution = '" + solution + "' WHERE problemNumber = '" + problemNumber + "';"; 
		    
			$.get("Query.php", {'sql':sql, 'returnData':false},function(json){
			  if(json && json[0]){ //If result of php file was a json array.					
			    alert(json);
			    alert(json[0]);
			  }
			},'json');
			
			setTimeout(insertCall, 100);
		  }
		}
	  }
	  
	  function insertCall(){ //Handles the Insert of the call history
		var sqlCall = "";
		var operatorID = "<?php echo (explode(",", $_POST['User']))[1]; ?>";
		var callerID = document.getElementById("CallerID").value;
		var dateTime = startDT;
		var sqlProblemNumber = "SELECT MAX(problemNumber) AS problemNumber FROM tblProblem;";
		$.get("Query.php", {'sql':sqlProblemNumber, 'returnData':true},function(json){
		  if (json&&json[0]){
		    var problemNumber = json[0].problemNumber;
		    var notes = document.getElementById("notes").value;
		    sqlCall += "INSERT INTO tblCallHistory VALUES ";
		    sqlCall += "(NULL, '" + operatorID + "', '" + callerID + "', '" + dateTime + "', '" + problemNumber + "', '" + notes + "');";
		    alert(sqlCall);
		    $.get("Query.php", {'sql':sqlCall, 'returnData':false},function(json){
			  if(json && json[0]){ //If result of php file was a json array.					
			    alert(json);
			    alert(json[0]);
			  }
		    },'json');
			GoToNewPage('Home');
		  }
		   
	    },'json');
	  }
	  
	</script>
	<style>
	  .dropdown-menu{
		  max-height:350px;
		  overflow-y:auto;
	  }
	  .tooltip {
		pointer-events: none;
	  }
	</style>
  </head>
  
	<body onload="Load()">
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
	<div autocomplete="off" class="container-fluid"> <!-- Container holds elements together using Bootstrap. -->
		<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
			<input type='text' hidden id="user" name="User"  /> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
			<input type='hidden' id='previous' name='Previous' value="<?php echo $_GET['previous']; ?>" /> <!-- Hidden tag holding name of previous page. -->
			@csrf <!--Token to validates requests to server. -->
			<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
				<label id="dtLabel" class="dtLabel"></label> <!-- Label to contain current date/time. -->
			</div>
		</form>
	  <div class="row" align="center">
		<div class="col-12">
		  <h2>Call Details</h2>
		  <h5>The operator logging is <?php echo (explode(",", $_POST['User']))[0]; ?> #<?php echo (explode(",", $_POST['User']))[1]; ?></h5> <!-- The user explode adds the currently logged in user to the page, including ID -->
		  <h6>Time and data will be recorded on submit</h6>
		  <br>
		</div>
		<div class="col-3"></div> <!-- Empty divs to format the page -->
		<div class="col-6 ">
		  <form>
		    <div class="form-row">
			  <div class="col">
		        <label for="CallerName" class="mr-sm-2">Caller Name:</label>
		        <input type="text" name="CallerName" id="CallerName" onchange="autofillId()" class="form-control mb-2 mr-sm-2">
			  </div>
			  <div class="col">
				<label for="CallerID" class="mr-sm-2">Caller ID:</label>
				<input type="text" name="CallerID" id="CallerID" onchange="autofillName()" class="form-control mb-2 mr-sm-2">
			  </div>
			</div>
		  </form>
		</div>
		<div class="col-3"></div>
		
		<div class="col-4"></div>
		<div class="col-4">
		  Notes on call:<br>
		  <textarea class="form-control text" rows="5" id="notes" ></textarea>
		  <br>
		  <br>
		  Select New/Existing Problem:
		  <div class="dropdownNewOrExisting" id="dropdownNewOrExisiting">
		    <button class='btn greenBack dropdown-toggle' type='button' id='dropdownButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
			  Choose Problem<span class='caret'></span>
			</button>
			<div class='dropdown-menu' id='dropdown-menu' aria-labelledby='dropdownMenu1'>
			
			</div>
		  </div>
		</div>
		<div class="col-4"></div>
		
		<div class="col-3"></div>
		<div class="collapse col-6" id="newProblemCollapse">
		  Select New Problem:
		  <div id="chooseNewProblemCombo">
		    <button class='btn greenBack dropdown-toggle' type='button' id='dropdownButton2' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
			  Choose Problem<span class='caret'></span>
			</button>
			<div class='dropdown-menu' id='dropdown-menu2' aria-labelledby='dropdownMenu2'>
				
			</div>
		  </div>
		</div>
		<div class="collapse col-3" id="updateDiv1"></div> <!-- Collapse empty divs to format the page depending on options the operator can choose which reshape the page layout -->
		
		<div class="collapse col-3" id="updateDiv2"></div>
		<div class="collapse col-6" id="newNewProblemCollapse">
		  <div class="form-check-inline">
		    <input class="form-check-input" type="radio" name="Radios" id="RadiosH" value="Hardware" onClick = "radios(1);">
			<label class="form-check-label" for="RadiosH">
			  Hardware
			</label>
		  </div>
		  <div class="form-check-inline">
		    <input class="form-check-input" type="radio" name="Radios" id="RadiosS" value="Software" onClick = "radios(2);">
			<label class="form-check-label" for="RadiosS">
			  Software
			</label>
		  </div>
		  <div class="form-check-inline">
		    <input class="form-check-input" type="radio" name="Radios" id="RadiosN" value="Network" onClick = "radios(3);">
			<label class="form-check-label" for="RadiosN">
			  Network
			</label>
		  </div>
		</div>
		<div class="col-3"></div>
		
		<div class="col-3"></div>
		<div class="collapse col-3" id="problemTypeCollapse">
		  <div class="mr-5 text-right">
		    Problem Type:
		  </div>
		  <div id="problemTypeComboBox" class="text-right">
		    <button class='btn greenBack dropdown-toggle' type='button' id='dropdownButton3' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
			  Choose Problem Type<span class='caret'></span>
			</button>
		  <div class='dropdown-menu' id='dropdown-menu3' aria-labelledby='dropdownMenu3'>
			
		  </div>	
		  </div>
		</div>
		
		<div class="collapse col-3 " id="serialNumberCollapse">
		  <div class="ml-5 text-left">
		    Serial Number:
		  </div>
		  <div id="serialNumberComboBox" class="text-left">
		    <button class='btn greenBack dropdown-toggle' type='button' id='dropdownButtonSerial' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
			  Choose Serial Number<span class='caret'></span>
	        </button>
		    <div class='dropdown-menu' id='dropdown-menu5' aria-labelledby='dropdownMenuSerial'>
			  
		    </div>
		  </div>
		</div>
		
		<div class="collapse col-3 " id="OSCollapse">
		  <div class="ml-5 text-left">
		    Operating System:
		  </div>
		  <div id="OSComboBox" class="text-left">
		    <button class='btn greenBack dropdown-toggle' type='button' id='dropdownButtonOS' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
			  Choose Operating System<span class='caret'></span>
	        </button>
		    <div class='dropdown-menu' id='dropdown-menu6' aria-labelledby='dropdownMenuOS'>
			  
		    </div>
		  </div>
		  <div class="col-3"></div>
		</div>
		
		<div class="col-4"></div>
		<div class="collapse col-4 " id="concernCollapse">
		  <div>
		    Software Concerned:
		  </div>
		  <div id="concernComboBox">
		    <button class='btn greenBack dropdown-toggle' type='button' id='dropdownButtonConcern' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
			  Choose Concerning Software<span class='caret'></span>
	        </button>
		    <div class='dropdown-menu' id='dropdown-menu7' aria-labelledby='dropdownMenuConcern'>
			  
		    </div>
		  </div>
		</div>
		<div class="collapse col-4" id="concernCollapseDiv"></div>

		<div class="collapse col-4" id="concernCollapseDiv2"></div>
		
		<div class="collapse col-4" id="result2Collapse">
		  Specialist:
		  <div id="specialistComboBox">
		    <button class='btn greenBack dropdown-toggle' type='button' id='dropdownButton4' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
			  Choose Specialist:<span class='caret'></span>
			</button>
			<div class='dropdown-menu' id='dropdown-menu4' aria-labelledby='dropdownMenu4'>
			</div>
		  </div>
		  <div id="resolvedCheckbox">
			<div class="form-check">
			  <input class="form-check-input" type="checkbox" id="Checkbox" value="Resolved" onClick = "checkbox();">
			  <label class="form-check-label" for="Checkbox">
			    Resolved
			  </label>
			</div>
		  </div>
		  <div class="collapse" id="solutionCollapse">
		    Solution:
		    <textarea class="form-control text" rows="5" id="solution" ></textarea>
			<div class="collapse" id="specialistSolutionComboBox">
			  <div>
		        <button class='btn greenBack dropdown-toggle' type='button' id='dropdownButtonSolution' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
			      Choose Solution:<span class='caret'></span>
			    </button>
			    <div class='dropdown-menu' id='dropdown-menuSolution' aria-labelledby='dropdownMenuSolution'>
			    </div>
			  </div>
		    </div>
		  </div>
		  <br>
	      <input type="button" id="btnSave" class="btn" value="Save Changes" onClick="Validation();" />
		</div>
		<div class="col-4"></div>
	  </div>
	</div>
  </body>
</html>