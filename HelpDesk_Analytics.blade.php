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
			function Load() //Function that runs when file loads.
			{
				WriteTime(); //Function that writes the current time at the top of the page.
				GetWorstHardware();
				ProblemChartHardware();
				SpecialistChart();
				ResolvedChart();
			}
	function GetWorstHardware()	
	{
		sql= "SELECT tblEquipment.serialNumber, tblEquipment.equipmentType, tblEquipment.equipmentMake, COUNT(tblProblem.serialNumber) AS occurence FROM tblProblem INNER JOIN tblEquipment ON tblProblem.serialNumber = tblEquipment.serialNumber GROUP BY tblEquipment.serialNumber ORDER BY occurence DESC LIMIT 0, 1;"; //SQL statement gets most common serial number in problem list.
		
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json)
		{
			if(json && json[0])
			{
			document.getElementById("label1").innerHTML = "Hardware with most problems logged: " + json[0].serialNumber + " (" + json[0].equipmentMake + " " + json[0].equipmentType + ") - " + json[0].occurence + " times.";
			}
			else
			{
			document.getElementById("label1").innerHTML="Can't find appropriate data";
			}
		},"json");
	}	
	
	function ProblemChartHardware()
	{
		sql= "SELECT tblEquipment.serialNumber, tblEquipment.equipmentType, tblEquipment.equipmentMake, COUNT(tblProblem.serialNumber) AS occurence FROM tblProblem INNER JOIN tblEquipment ON tblProblem.serialNumber = tblEquipment.serialNumber GROUP BY tblEquipment.serialNumber ORDER BY occurence DESC LIMIT 0, 5;"; //SQL statement gets most common serial number in problem list.
		
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json)
		{
			if(json)
			{
			var labels = [];
			var data = [];
			for(var i = 0; i < json.length; i++) {
				var equipment = json[i];
				
				labels.push(equipment.equipmentType);
				data.push(equipment.occurence);
			}
			
			var ctx = document.getElementById("hardwareChart").getContext('2d');
		var myBarChart = new Chart(ctx, {
    type: 'line',
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
			document.getElementById("hardwareChart").innerHTML="Can't find appropriate data";
			}
		},"json");
		
	}	
	
	function SpecialistChart()
	{
		sql= "SELECT COUNT(tblProblem.problemNumber) AS problem_count FROM tblProblem WHERE resolved = 'Yes';"; //SQL statement gets most common serial number in problem list.
		
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json)
		{
			if(json && json[0])
			{
			var total = json[0].problem_count;
			sql= "SELECT COUNT(tblProblem.problemNumber) AS problem_count FROM tblProblem WHERE resolved = 'Yes' AND specialistID IN (SELECT userID FROM tblPersonnel WHERE specialist = 'Yes');"; //SQL statement gets most common serial number in problem list.
			$.get("Query.php", {'sql':sql, 'returnData':true},function(json2)
			{
				if(json2 && json2[0]) {
				var specialists = json2[0].problem_count;
				var specialistsPercent = total > 0 ? Math.round((specialists/total) * 100) : 0;
				var otherPercent = total > 0 ? 100 - specialistsPercent : 0;
			
				var ctx = document.getElementById("specialistChart").getContext('2d');
		var myBarChart = new Chart(ctx, {
    type: 'bar',
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
		sql= "SELECT COUNT(tblProblem.problemNumber) AS problem_count FROM tblProblem WHERE resolved = 'Yes';"; //SQL statement gets most common serial number in problem list.
		
		$.get("Query.php", {'sql':sql, 'returnData':true},function(json)
		{
			if(json && json[0])
			{
			var resolved = json[0].problem_count;
			sql= "SELECT COUNT(tblProblem.problemNumber) AS problem_count FROM tblProblem WHERE resolved = 'No';"; //SQL statement gets most common serial number in problem list.
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
		<div class="container"> <!-- Container holds elements together using Bootstrap. -->
			<form id="mainform" name="mainform" method="post" action=""> <!-- This form will post data to an initially unspecified page when submitted. -->
				<input type='hidden' name="User" value="<?php echo $_POST['User']; ?>" /> <!-- Hidden tag used to store posted user data so that it can later be posted back to the home page. -->
				@csrf <!--Token to validates requests to server. -->
				<div class="titleDiv"> <!-- Div containing elements at the top of the page. -->
					<input type="button" style="font-size:40px; position:absolute; left:0;" value="&#x2190" style="display:inline-block;" onClick="GoToNewPage('Home');" /> <!-- Back button. -->
					<label id="dtLabel" style="font-size:26px; position:absolute; right:0;"></label> <!-- Label to contain current data/time. -->
					<h2 id="headerId" style="style=display:inline-block; font-size:30px;">Analytics</h2> <!-- Heading containing name of page. -->
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