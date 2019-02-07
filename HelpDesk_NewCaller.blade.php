<!DOCTYPE html>
<html lang="en">
  <head>
	<title>New Caller</title>
    <meta charset="utf-8"> 
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script>
	<script type="text/javascript">
	  var userData; //Variable containing data about user
	  var currentPage = "NewCaller";
	  function Load(){
		problemCreation();
		WriteTime();
		userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
		NavBar();
	  }
	  
	  function NavBar(){
		  var html = "<ul class='navbar-nav mr-auto'>"
		  var admin = (userData.split(","))[2]; 
		  var analyst = (userData.split(","))[3];
		  
		  html+= "<a class='nav-item nav-link' href='#' onClick='GoToNewPage(document.getElementById(&quot;Previous&quot;).value)'>&#x2190 </a>";
		  html+= "<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;Home&quot;);'>Home</a>";
		  html+= "<a class='nav-item nav-link active' href='#'>New Call <span class='sr-only'>(current)</span></a>";
		  html+= "<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;CallHistory&quot;);'>Call History</a>";
		  html+= "<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;ProblemList&quot;);'>Problems List</a>";
		  html+= "<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;PersonnelList&quot;);'>Personnel</a>";
		 
		 if (admin==1){
			html+="<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;UserList&quot;);'>Users</a>";
			html+="<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;SpecialisationList&quot;);'>Specialisations</a>";
		  }
		  else{
			html+= "<a class='nav-item nav-link disabled' href='#'>Users</a>";
			html+= "<a class='nav-item nav-link disabled' href='#'>Specialisations</a>"
		  }
		  
		  html+= "<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;EquipmentList&quot;);'>Equipment</a>";
		  
		  if (admin == 0 && analyst == 0){
			html+= "<a class='nav-item nav-link disabled' href='#'>Analytics</a></ul>";
		  }
          else{
		    html+= "<a class='nav-item nav-link' href='#' onClick='GoToNewPage(&quot;Analytics&quot;);'>Analytics</a></ul>";
		  }
		document.getElementById("navbarNavDropdown").innerHTML = html;
	    }
	  
	  function autofillId(){
	    var Username=document.getElementById("CallerName").value;
	    if (Username.includes("'")){
		} else{
		  sql = "SELECT userID FROM tblPersonnel WHERE name = '" + Username +"'"; 
		  $.get("Query.php", {'sql':sql, 'returnData':true},function(json){
			if (json && json[0]){
			  if (json[1]){
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
	  
	  function autofillName(){
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
	  
	  function problemCreation(){
		var html = "<button class='btn greenBack dropdown-toggle' type='button' id='dropdownButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>";
	    html += "Choose Problem<span class='caret'></span></button>";
		html += "<div class='dropdown-menu' id='dropdown-menu' aria-labelledby='dropdownMenu1'>";
	    html += "<a class='dropdown-item'>New Problem</a><div class='dropdown-divider'></div>";
		html += "<h6 class='dropdown-header'>Existing Problems</h6>";
		var sql = "SELECT problem FROM tblProblem WHERE resolved = 'no'";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			for (i = 0; i < json.length; i++){
			  html+="<a class='dropdown-item' href='#'>" + json[i].problem + "</a>";
			}
			html+="</div>";
		    document.getElementById("dropdownNewOrExisiting").innerHTML = html;
		  }
		},'json');
	  }
	  
      $(document).on('click', '#dropdown-menu a', function(){
        $("#dropdownButton:first-child").text($(this).text());
        $("#dropdownButton:first-child").val($(this).text());
	    problem();
      });
	  
	  function problem(){
		if(document.getElementById("dropdownButton").value == "New Problem"){
		  newProblemCreation();
		  $('#newProblemCollapse').collapse('show');
		  $('#existingProblemCollapse').collapse('hide');
		}
		else{
		  $('#newProblemCollapse').collapse('hide');
		  $('#existingProblemCollapse').collapse('show');
		}
	  }
	  
	  function newProblemCreation(){
		var html = "<button class='btn greenBack dropdown-toggle' type='button' id='dropdownButton2' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>";
	    html += "Choose Problem<span class='caret'></span></button>";
		html += "<div class='dropdown-menu' id='dropdown-menu2' aria-labelledby='dropdownMenu2'>";
		html += "<form class ='px-4 py-3'><div class='form-group'><label for='dropdownSearch'>Search</label>"
		html += "<input type='text' class='form-control' id='dropdownSearch2' placeholder='Search' onkeyup='filter(2)'></div></form>"
	    html += "<div class='dropdown-divider'></div><a class='dropdown-item'>New Problem</a><form class='px-4 py-3'>";
		html += "<input type='text' class='form-control' id='newProblemInput' placeholder='Enter New Problem'></form><div class='dropdown-divider'></div>";
		html += "<h6 class='dropdown-header'>Previously Problems</h6>";
		var sql = "SELECT problem FROM tblProblem";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			for (i = 0; i < json.length; i++){
			  html+="<a class='dropdown-item' href='#'>" + json[i].problem + "</a>";
			}
			html+="</div>";
		    document.getElementById("chooseNewProblemCombo").innerHTML = html;
		  }
		},'json');
	  }
	  
	  function filter(num){
	    var input = document.getElementById("dropdownSearch" + num);
		var x = document.getElementById("dropdown-menu" + num).getElementsByTagName("a");
		for (i = 0; i < x.length; i++){
		  txtValue = x[i].textContent || x[i].innerText;
		  if (txtValue.toUpperCase().indexOf(input.value.toUpperCase()) > -1) {
		    x[i].style.display = "";
		  }
		  else{
		    x[i].style.display = "none";
		  }
		}
	  }
	  
	  $(document).on('click', '#dropdown-menu2 a', function(){
		if ($(this).text() == "New Problem"){
		  $("#dropdownButton2:first-child").text(document.getElementById("newProblemInput").value);
		  $("#dropdownButton2:first-child").val(document.getElementById("newProblemInput").value);
		  $('#newNewProblemCollapse').collapse('show');
		}
		else{
          $("#dropdownButton2:first-child").text($(this).text());
          $("#dropdownButton2:first-child").val($(this).text());
		  $('#newNewProblemCollapse').collapse('hide');
		}
      });
	  
	  function radios(num){
		var html = "<button class='btn greenBack dropdown-toggle' type='button' id='dropdownButton3' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>";
	    html += "Choose Problem Type<span class='caret'></span></button>";
		html += "<div class='dropdown-menu' id='dropdown-menu3' aria-labelledby='dropdownMenu3'>";
		html += "<form class ='px-4 py-3'><div class='form-group'><label for='dropdownSearch'>Search</label>"
		html += "<input type='text' class='form-control' id='dropdownSearch3' placeholder='Search' onkeyup='filter(3)'></div></form>"
	    html += "<div class='dropdown-divider'></div><h6 class='dropdown-header'>Problem Types</h6>";
		document.getElementById("problemTypeComboBox").innerHTML = html;
		if (num==1){
		  findAllChildren("Hardware problem", html);
		}
		else if (num==2){
		  findAllChildren("Software problem", html);
		}
		else{
		  findAllChildren("Network problem", html);		  
		}
		html="</div>";
		document.getElementById("problemTypeComboBox").innerHTML += html;
		$('#resultCollapse').collapse('show');
	  }
	  
	  function findAllChildren(parent,html){
		var sql = "SELECT typeName FROM tblProblemType WHERE generalisation = '" + parent + "';";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			for (i = 0; i < json.length; i++){
			  html="<a class='dropdown-item' href='#'>" + json[i].typeName + "</a>";
			  document.getElementById("dropdown-menu3").innerHTML += html;
			  findAllChildren(json[i].typeName,html);
			}
		  }
		},'json');
		return(html); 
	  }
	  
	  $(document).on('click', '#dropdown-menu3 a', function(){
        $("#dropdownButton3:first-child").text($(this).text());
        $("#dropdownButton3:first-child").val($(this).text());
		populateSpecialist($(this).text());
      });
	  
	  var problemTypeList = [];
	  var specialistIDList = [];
	  var count = [];
	  var specialistList = [];
	  var problemTypeVar;
	  function populateSpecialist(problemType){
		problemTypeList = [];
		problemTypeVar = "";
		problemTypeVar = problemType;
		populateProblemTypeList(problemType);
	  }
	  
	  function populateProblemTypeList(problemType){
		var sql = "SELECT generalisation FROM tblProblemType WHERE typeName = '" + problemType + "';";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			if (json[0].generalisation == null){
			  problemTypeList.push(problemType);
			  populateIDList();
			}
			else{
			  problemTypeList.push(problemType);
			  populateProblemTypeList(json[0].generalisation);
			  return;
			}
		  }
	    },'json');
	  }
	  
	  function populateIDList(){
		
		specialistList = [];
		count = [];
		specialistIDList = []; 
		console.log(specialistIDList);
		for (i = 0; i < problemTypeList.length; i++){
		  sql = "SELECT userID FROM tblSpecialisation WHERE typeName = '" + problemTypeList[i] + "';";
		  $.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		    if (json && json[0]){
			  for (i = 0; i < json.length; i++){
				if (specialistIDList.indexOf(json[i].userID) == -1){
				  specialistIDList.push(json[i].userID)
				}
			  }
			}
		  },'json');
		}
		populateCount();
	  }
	  
	  function populateCount(){		  
	    for (i = 0; i < specialistIDList.length; i++){
		  console.log("LOOP");
		  sql = "SELECT COUNT(problem) FROM tblProblem WHERE specialistID = " + specialistIDList[i] + " AND resolved = 'No';";
		  $.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		    if (json && json[0]){
			  count.push(json[0]);
			}
		  },'json');
		}
		console.log(count);
		populateSpecialistList();
	  }
	  
	  function populateSpecialistList(){
		for (i = 0; i < specialistIDList.length; i++){
		  sql = "SELECT name FROM tblPersonnel WHERE userID = " + specialistIDList[i] + ";";
		  $.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		    if (json && json[0]){
			  specialistList.push(json[0]);
			}
		  },'json');
		}
		fillSpecialistComboBox();
	  }
	  
	  function fillSpecialistComboBox(){
		var sql = "SELECT generalisation FROM tblProblemType WHERE typeName = '" + problemTypeVar + "';";
		var html;
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			html += "<h6 class='dropdown-header'>Specialists to exact problem type</h6>"
			for (i = 0; i < json.length; i++){
			  html += "<a class='dropdown-item' href='#'>" + specialistList[i] + " (" + count[i] + " current jobs)</a>"
			}
			specialistList.splice(i, 1);
			html += "<div class='dropdown-divider'></div>"
		  }
		},'json');
		html+= "<h6 class='dropdown-header'>Specialists to a generalisation of the problem type</h6>";
		for (i = 0; i < specialistList.length; i++){
		  html+= "<a class='dropdown-item' href='#'>" + specialistList[i] + " (" + count[i] + " current jobs)</a>"
		}
		document.getElementById("dropdown-menu4").innerHTML += html;
	  }
	    
	</script>
	<style>
	  .dropdown-menu{
		  max-height:350px;
		  overflow-y:auto;
	  }
	</style>
  </head>
  
  <body onload="Load()">
    <header class="navbar flex-column flex-md-row bd-navbar navbar-dark navbar-expand-lg bg-dark">
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="navbar-collapse collapse" id="navbarNavDropdown">
	  </div>
	  <a class='nav-item nav-link' href='#' onClick='GoToNewPage("");'>Logout</a>
	  <a class="navbar-brand ml-md-auto" href="#">
		<img src="https://www.goodfreephotos.com/albums/vector-images/screwdriver-and-wrench-vector-clipart.png" width="30" height="30" class="d-inline-block align-top" alt="">
		  Make-It-All
	  </a>
	</header>
	<div class="container-fluid">
	  <form id="mainform" name="mainform" method="post" action="">
	    @csrf
		<input type='text' hidden id="user" name="User"  /> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
		<input type='hidden' name='Previous' id='Previous' value="<?php echo $_GET['previous']; ?>" />
        <div class="titleDiv col-12 d-flex"> <!-- Div containing elements at the top of the page. -->
		  <label id="dtLabel" class="ml-auto" >
	    </div>
	  </form>
	  <div class="row" align="center">
		<div class="col-12">
		  <h1>Call Details</h1>
		  <h5>The operator logging is <?php echo (explode(",", $_POST['User']))[0]; ?> #<?php echo (explode(",", $_POST['User']))[1]; ?></h5>
		  <h6>Time and data will be recorded on submit</h6>
		  <br>
		</div>
		<div class="col-3"></div>
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
		  <textarea class="form-control" rows="5" id="notes" ></textarea>
		  <br>
		  <br>
		  Select New/Existing Problem:
		  <div class="dropdownNewOrExisting" id="dropdownNewOrExisiting"> </div>
		</div>
		<div class="col-4"></div>
		
		<div class="col-3"></div>
		<div class="collapse col-6" id="newProblemCollapse">
		  Select New Problem:
		  <div id="chooseNewProblemCombo"></div>
		</div>
		<div class="col-3"></div>
		
		<div class="col-3"></div>
		<div class="collapse col-6" id="newNewProblemCollapse">
		  <div class="form-check">
		    <input class="form-check-input" type="radio" name="Radios" id="Radios1" value="Hardware" onClick = "radios(1);">
			<label class="form-check-label" for="Radios1">
			  Hardware
			</label>
		  </div>
		  <div class="form-check">
		    <input class="form-check-input" type="radio" name="Radios" id="Radios1" value="Software" onClick = "radios(2);">
			<label class="form-check-label" for="Radios1">
			  Software
			</label>
		  </div>
		  <div class="form-check">
		    <input class="form-check-input" type="radio" name="Radios" id="Radios1" value="Network" onClick = "radios(3);">
			<label class="form-check-label" for="Radios1">
			  Network
			</label>
		  </div>
		</div>
		<div class="col-3"></div>
		
		<div class="col-3"></div>
		<div class="collapse col-6" id="resultCollapse">
		  <div id="changeableThings">
		  </div>
		  Problem Type:
		  <div id="problemTypeComboBox">
		  </div>
		  Specialist:
		  <div id="specialistComboBox">
		    <button class='btn greenBack dropdown-toggle' type='button' id='dropdownButton4' data-toggle='dropdown' aria-haspopup='true' aria-expanded='true'>
			  Choose Specialist<span class='caret'></span>
			</button>
			<div class='dropdown-menu' id='dropdown-menu4' aria-labelledby='dropdownMenu4'>
		  </div>
		  Resolved:
		  <div id="resolvedRadioButtons">
		  </div>
		</div>
		<div class="col-3"></div>
		
		<div class="collapse col-12" id="existingProblemCollapse">
		BOO
		</div>
	  </div>
	</div>
  </body>
</html>