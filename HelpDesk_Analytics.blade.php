<!DOCTYPE html>
<html>
	<head>
		<meta content="text/html" charset="UTF-8" />
		<title>HelpDesk_Analytics</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> <!-- Get JQuery library from google. -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>	<!-- Importing Bootstrap files. -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js"></script>
		<script type="text/javascript" src="{{ URL::asset('js/ExtraCode.js') }}"></script> <!-- Import JS file containing functions that are used in multiple other files -->
		<script type="text/javascript">
		var userData; //Variable containing data about user.
		var currentPage = "Analytics"; //Variable storing the name of the current page, so it can be passed in the URL to the next page as a 'previous page' variable.
			function Load() //Function that runs when file loads.
			{
				userData = "<?php echo $_POST['User']; ?>"; //Gets data from previous form.
				WriteTime(); //Function that writes the current time at the top of the page.
				SetNavSettings();
				ProblemChartHardware(); // Line chart showing the number of times problems for a particular hardware were solved.
				SpecialistChart(); // Bar graph showing the percentage how problems solved by specialists and non-specialists. 
				ResolvedChart(); // Bar chart showing the percentage of problems resolved and not resolved.
			}

	
	function ProblemChartHardware()
	{
		sql= "SELECT tblEquipment.serialNumber, tblEquipment.equipmentType, tblEquipment.equipmentMake, COUNT(tblProblem.serialNumber) AS occurence FROM tblProblem INNER JOIN tblEquipment ON tblProblem.serialNumber = tblEquipment.serialNumber GROUP BY tblEquipment.serialNumber ORDER BY occurence DESC LIMIT 0, 5;"; //SQL statement gets most common serial number in problem list.
		
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json)
		{
			if(json)
			{
			var labels = []; // New Array 
			var data = []; 
			for(var i = 0; i < json.length; i++) {
				var equipment = json[i];
				
				labels.push(equipment.equipmentType); // New label to the end of array.
				data.push(equipment.occurence);
			}
			
			var ctx = document.getElementById("hardwareChart").getContext('2d');
		var myBarChart = new Chart(ctx, {
    type: 'line', // Graph type Line
data: {
        labels: labels,
        datasets: [{
            label: '# of Problems',
            data: data,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)', 
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'         
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
    	responsive: true,
    	maintainAspectRatio: false,
		scales: {
			yAxes: [{
				ticks: {
					beginAtZero: true
				}
			}]
		}    
    }
});
			}
			else
			{
			document.getElementById("hardwareChart").innerHTML="Can't find appropriate data"; // If Problems are not found.
			}
		},"json");
		
	}	
	
	function SpecialistChart()
	{
		sql= "SELECT COUNT(tblProblem.problemNumber) AS problem_count FROM tblProblem WHERE resolved = 'Yes';"; //SQL statement gets Problem solved but not by specialists.
		
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json)
		{
			if(json && json[0])
			{
			var total = json[0].problem_count;
			sql= "SELECT COUNT(tblProblem.problemNumber) AS problem_count FROM tblProblem WHERE resolved = 'Yes' AND specialistID IN (SELECT userID FROM tblPersonnel WHERE specialist = 'Yes');"; //SQL statement gets Problems solved by Specialists
			$.get("Query.php", {'sql':sql, 'returnData':true},function(json2)
			{
				if(json2 && json2[0]) {
				var specialists = json2[0].problem_count;
				var specialistsPercent = total > 0 ? Math.round((specialists/total) * 100) : 0;   // Representing in Percentages
				var otherPercent = total > 0 ? 100 - specialistsPercent : 0;
			
				var ctx = document.getElementById("specialistChart").getContext('2d');
		var myBarChart = new Chart(ctx, {
    type: 'bar',								// Graph type Bar.
data: {
        labels: ["Specialists", "Non-Specialists"],
        datasets: [{
            label: '% of Problems Solved',
            data: [specialistsPercent, otherPercent],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
    	responsive: true,
    	maintainAspectRatio: false,
		scales: {
			yAxes: [{
				ticks: {
					beginAtZero: true
				}
			}]
		}    
    }
});
} else {
			document.getElementById("specialistChart").innerHTML="Can't find appropriate data";
}
			}, 'json');
			}
			else
			{
			document.getElementById("specialistChart").innerHTML="Can't find appropriate data";
			}
		},"json");
}


	
	function ResolvedChart()
	{
		sql= "SELECT COUNT(tblProblem.problemNumber) AS problem_count FROM tblProblem WHERE resolved = 'Yes';"; //SQL statement gets number of problems not resolved.
		
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json)
		{
			if(json && json[0])
			{
			var resolved = json[0].problem_count;
			sql= "SELECT COUNT(tblProblem.problemNumber) AS problem_count FROM tblProblem WHERE resolved = 'No';"; //SQL statement gets the number of problems not resolved
			$.get("Query.php", {'sql':sql, 'returnData':true},function(json2)
			{
				if(json2 && json2[0]) {
				var notResolved = json2[0].problem_count;
			
				var ctx = document.getElementById("resolvedChart").getContext('2d');
		var myBarChart = new Chart(ctx, {
    type: 'bar',
data: {
        labels: ["Resolved", "Not Resolved"],
        datasets: [{
            label: '# of Problems',
            data: [resolved, notResolved],
            backgroundColor: [    // Defining the backgroundColor
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [		// Defining the borderColor
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
    	responsive: true,
    	maintainAspectRatio: false,
		scales: {
			yAxes: [{
				ticks: {
					beginAtZero: true
				}
			}]
		}    
    }
});
} else {
			document.getElementById("resolvedChart").innerHTML="Can't find appropriate data";
}
			}, 'json');
			}
			else
			{
			document.getElementById("resolvedChart").innerHTML="Can't find appropriate data";
			}
		},"json");
	}	

		</script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"> <!-- Bootstrap CSS stylesheet. -->
		<link rel="stylesheet" href="{{ asset('css/Styles.css') }}" type="text/css"> <!-- Import external CSS stylesheet that contains presentation info that applies to all the pages. -->
		<style>
		.labelClass
		{
			font-size:24px;
		}
		.chart-container
		{
			width: 600px !important;
			height: 600px !important;
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
		<div class="container"> <!-- Container holds elements together using Bootstrap. -->
			<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
				<input type='text' hidden id="user" name="User"/> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
				<input type='hidden' id='previous' name='Previous' value="<?php echo $_GET['previous']; ?>" /> <!-- Hidden tag holding name of previous page. -->
				@csrf <!--Token to validates requests to server. -->
				<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
					<label id="dtLabel" class="dtLabel"></label> <!-- Label to contain current date/time. -->
					<h2 id="headerId">Analytics</h2> <!-- Heading containing name of page. -->
				</div>
				<br/>
				<br/>
				
				<div class="chart-container">
				<canvas id="hardwareChart" width="400" height="400"></canvas>
				</div>
				<div class="chart-container">
				<canvas id="specialistChart" width="400" height="400"></canvas>
				</div>
				<div class="chart-container">
				<canvas id="resolvedChart" width="400" height="400"></canvas>
				</div>
				
				<div class="row" align="center">
					<div id="analyticsDiv">  <!-- Div containing analytics info. -->
						<!-- Put stuff in here. -->
						<label id="label1" class="labelClass"></label>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>