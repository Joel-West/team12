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
	<script type="text/javascript">
	  function autofillId(){
	    var Username=document.getElementById("CallerName").value;
	    if (Username.includes("'")){
		} else{
		  sql = "SELECT userID FROM tblPersonnel WHERE name = '" + Username +"'"; 
		  $.get("Query.php", {'sql':sql},function(json){
			if (json && json[0]){
			  if (json[1]){
				alert("There are multiple " + Username + ". Please type their user ID as well");
			  } else{
				document.getElementById("CallerID").value = json[0].userID;
			  }
			} else{
			  document.getElementById("CallerID").value = " ";
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
			  document.getElementById("CallerName").value = " ";
			}
		  },'json');
	    }
      }
	  
	  function problem(){
		  console.log("Problem");
	  }
	  
	  function Test(){
		  console.log("Clicked");
	  }
	</script>
  </head>
  
  <body>
	<div class="container">
	  <div class="row" align="center">
		<div class="col-12">
		  <h1>Call Details</h1>
		  <h5>The operator logging in is Alice #999999</h5>
		  <h6>Time and data will be recorded on submit</h6>
		  <br>
		</div>
		<div class="col-6">
		  Caller Name: <input type="text" name="CallerName" id="CallerName" onchange="autofillId()">
		</div>
		<div class="col-6">
		  Caller ID: <input type="text" name="CallerID" id="CallerID" onchange="autofillName()">
		</div>
		<div class="col-4">
		</div>
		<div class="col-4">
		  Notes on call<br>
		  <textarea rows="4" cols="50" style="resize:none">
		  </textarea>
		  <br>
		  <br>
		  Select Problem:
		  <select onchange="problem()" id="Problems" class="custom-select">
			<option selected>Choose Problem</option>
			<option value="New Problem">New Problem</option>
			<option value="Broken Capslock">Broken Capslock</option>
			<option value="Overheated Computer">Overheated Computer</option>
			<option value="MS Paint won't close">MS Paint won't close</option>
		  </select>
		</div>
	  </div>
	</div>
	
  </body>
</html>