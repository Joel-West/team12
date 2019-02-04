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
	  
	  function problem(){
		console.log("Problem");
		if(document.getElementById("Problems").value == "New Problem"){
		  newProblemCreation();
		  $('#newProblemCollapse').collapse('show');
		  $('#existingProblemCollapse').collapse('hide');
		}
		else{
		  $('#newProblemCollapse').collapse('hide');
		  $('#existingProblemCollapse').collapse('show');
		}
	  }
	  
	  $(function(){
        $(".dropdown-menu").on('click', 'li a', function(){
          $(".btn:first-child").text($(this).text());
		  $(".btn:first-child").val($(this).text());
		  problem($(this).text());
        });
      });
	  
	  function newProblemCreation(){
		var html = "<select id='chooseProblem' class='custom-select' >";
	    var sql = "SELECT problem FROM tblProblem";
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json){
		  if (json && json[0]){
			html+= "<option selected value = 'Choose'>Please Choose</option>";
			html+= "<option value = 'NewProblem'>New Problem</option>";
			for (i = 0; i < json.length; i++){
			  html+="<option value = '" + json[i].problem + "'>" + json[i].problem + "</option>";
			}
			html+="</select>";
		    document.getElementById("chooseNewProblemCombo").innerHTML = html;
		  }
		},'json');
	  }
	  
	</script>
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
		  <div class="dropdownNewOrExisting">
	        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
	          Choose Problem
		      <span class="caret"></span>
	        </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
	          <li><a class="dropdown-item">New Problem</a></li>
		      <li><div class="dropdown-divider"></div></li>
              <li><h6 class="dropdown-header">Existing Problems</h6></li>
              <li><a class="dropdown-item" href="#">Broken Capslocks</a></li>
              <li><a class="dropdown-item" href="#">Overheated Computer</a></li>
		      <li><a class="dropdown-item" href="#">MS Paint won't close</a></li>
            </ul>
	      </div>
		</div>
		<div class="col-4"></div>
		
		<div class="col-3"></div>
		<div class="collapse col-6" id="newProblemCollapse">
		  Select New Problem:
		  <div id="chooseNewProblemCombo"></div>
		</div>
		<div class="col-3"></div>
		
		<div class="collapse col-12" id="existingProblemCollapse">
		BOO
		</div>
	  </div>
	</div>
  </body>
</html>