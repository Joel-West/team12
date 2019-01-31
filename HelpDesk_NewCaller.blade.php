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
	  function Load(){
		WriteTime();
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
		if (document.getElementById("Problems").value == ""){
		  $('#newProblemCollapse').collapse('hide');
		  $('#existingProblemCollapse').collapse('hide');
		}
		else if(document.getElementById("Problems").value == "New Problem"){
		  $('#newProblemCollapse').collapse('show');
		  $('#existingProblemCollapse').collapse('hide');
		}
		else{
		  $('#newProblemCollapse').collapse('hide');
		  $('#existingProblemCollapse').collapse('show');
		}
	  }
	  
	  function Test(){
		  console.log("Clicked");
	  }
	</script>
  </head>
  
  <body onload="Load()">
	<div class="container-fluid">
	  <form id="mainform" name="mainform" method="post" action="">
	    @csrf
		<input type='hidden' name="User" value="<?php echo $_POST['User']; ?>" />
        <div class="titleDiv col-12 d-flex"> <!-- Div containing elements at the top of the page. -->
		  <nav class="navbar navbar-dark navbar-expand-lg bg-dark">
	        <a class="navbar-brand" href="#">
		      <img src="https://pbs.twimg.com/profile_images/378800000734794736/4f71f1537b67cb5d74c5aa5913604d68.jpeg" width="30" height="30" class="d-inline-block align-top" alt="">
		      Navbar
		    </a>
		    <div class="navbar-nav">
		      <a class="nav-item nav-link active" href="#">Home <span class="sr-only">(current)</span></a>
		      <a class="nav-item nav-link" href="#">Features</a>
		      <a class="nav-item nav-link" href="#">Pricing</a>
		      <a class="nav-item nav-link disabled" href="#">Disabled</a>
	        </div>
	      </nav>
		  <input type="button" class="btn mr-auto" value="&#x2190" onClick="GoToNewPage('Home');" /> <!-- Back button. -->
		  <label id="dtLabel" class="ml-auto" >
	    </div>
	  </form>
	  <div class="row" align="center">
		<div class="col-12">
		  <h1>Call Details</h1>
		  <h5>The operator logging in is Alice #999999</h5>
		  <h6>Time and data will be recorded on submit</h6>
		  <br>
		</div>
		<div class="col-2"></div>
		<div class="col-8 ">
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
		<div class="col-2"></div>
		<div class="col-4"></div>
		<div class="col-4">
		  Notes on call:<br>
		  <textarea class="form-control" rows="5" id="notes" ></textarea>
		  <br>
		  <br>
		  Select Problem:
		  <select onchange="problem()" id="Problems" class="custom-select" >
			<option selected value = "">Choose Problem</option>
			<option value="New Problem">New Problem</option>
			<option value="Broken Capslock">Broken Capslock</option>
			<option value="Overheated Computer">Overheated Computer</option>
			<option value="MS Paint won't close">MS Paint won't close</option>
		  </select>
		</div>
		<div class="col-4"></div>
		<div class="collapse col-12" id="newProblemCollapse">
		Hey look,it worked.
		</div>
		<div class="collapse col-12" id="existingProblemCollapse">
		BOO
		</div>
	  </div>
	</div>
  </body>
</html>