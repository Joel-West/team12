fun = false;
delList = []; //List of rows to be deleted when changes are saved to actual database.
updList = []; //List of rows to be updated when changes are saved to actual database.
newRowCount = 0; //Varaible storing number of new rows.

window.setInterval(function() //Function used for fun mode.
{
	if (fun == true)
	{
		col = GetRandomCol();
		document.body.style.backgroundColor = "rgb("+col[0]+", "+col[1]+", "+col[2]+")";
	}
}, 50);

function ListContains(list, value) //Function returns true if an item is in a list.
{
	for (i = 0; i < list.length; i++)
	{
		if (list[i] == value)
		{
			return true;
		}
	}
	return false;
}

function Fun() //Fun mode function. Used for testing changing colour of elements dynamically.
{
	if (document.getElementById("checkFun").checked)
	{
		fun = true;
	}
	else
	{
		fun = false;
		document.body.style.backgroundColor = "#D6D6D6";
	}
}

function GetRandomCol() //Function chooses 3 random values between 60 and 255, which will later be used to generate an RGB colour.
{
	var x = [];
	x[0] = Math.floor(Math.random()*255);
	x[1] = Math.floor(Math.random()*255);
	x[2] = Math.floor(Math.random()*255);
	return x;
}

function WriteTime() //Writes current time (up to the minute) to a label at the top-right of the current page.
{
	var dt = new Date();
	options = {day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', hour12: false} //Sets the time format.
	document.getElementById("dtLabel").innerHTML = dt.toLocaleDateString("en-UK", options); //Assigns time to label.
	var wait = setTimeout(WriteTime, 1000); //Checks the time every second.
}

function GoToNewPage(page) //Function that submits the main form of the current page, changing the page to that specified in the 'page' variable.
{
	admin = (userData.split(","))[2]; //Retrieves admin/analyst status from userData that was earlier posted from previous form.
	analyst = (userData.split(","))[3];
	go = true; //Variable stores if form should be sent or not.
	switch(page) //An additional layer of validation to check that the user is authorised to go to the new page.
	{
		case "NewCaller":
			if ((admin == false) && (analyst == true))
			{
				go = false;
			}
		break;
		case "Analytics":
			if ((admin == false) && (analyst == false))
			{
				go = false;
			}
		break;
		case "UserList": case 'SpecialisationList':
			if (admin == false)
			{
				go = false;
			}
		break;
	}
	if (go) //if valid (e.g. if user has access to next page).
	{
		SetUserDataToPost();
		document.getElementById("mainform").action = "http://35.204.60.31/" + page + "?previous=" + currentPage; //Defines page that data will be posted to.
		document.getElementById("mainform").submit(); //Submits form.
	}
}

function SetUserDataToPost()
{
	document.getElementById("user").value = userData;
}

function GetRows() //Function for returning the number of rows in a data table.
{
	var rows = document.getElementById(GetTable()).getElementsByTagName("tr").length;
	return rows;
}

function GetSelectedRow() //Returns selected row (if only one is selected).
{
	rows = GetRows();
	for (i = rows-1; i > 0; i--) //Iterate through the rows of the table.
	{
		if (document.getElementById(GetTable()).rows[i].style.backgroundColor != 'rgb(159, 255, 48)') //If row is selected.
		{
			return i;
		}
	}
}

function AddPressed() //Function to add new row to the local data table.
{
	admin = (userData.split(","))[2];
	if (admin == 0) //If not admin, action is forbidden.
	{
		return;
	}
	if (selected == 1) //If only 1 row is selected, thus if updating row.
	{
		UpdateRow();
	}
	else //Else, if adding row
	{
		AddRow()
	}
}

function GetTable() //Returns the ID of the current table.
{
	if (currentPage == "ProblemList")
	{
		return GetCurrentTableID(extraCells);
	}
	else
	{
		return "tbl";
	}
}

$('document').ready(function() //When document is prepped, ensures enter key allows users to submit search box.
{
	$('#txtSearch').keydown(function(event)
	{
		if (event.keyCode === 13)
		{
			$("#btnSearch").click();
		}
	});
});

$(document).on('click','tr',function(event) //Function for selecting/deselecting rows.
{
	admin = (userData.split(","))[2];
	analyst = (userData.split(","))[3];
	if (currentPage == "CallHistory" || currentPage == "ProblemList")
	{
		if (admin == 0 && analyst == 1) //If not admin, action is forbidden.
		{
			return;
		}
		if (currentPage == "ProblemList")
		{
			if ($(this).attr('id') == 'callRow') //If trying to select row on the call history table that pops up when a row is selected in the problem table, leave function.
			{
				return;
			}
		}
	}
	else
	{
		if (admin == 0) //If not admin, action is forbidden.
		{
			return;
		}
	}
	if ($(this).attr('id') != 't0') //If not the header.
	{
		if ($(this).css('background-color') == 'rgb(255, 255, 255)') //If deselected (if while).
		{	
			$(this).css('background-color', '#4CAF50'); //Select.
			$(this).css('color', '#FFFFFF');
			selected += 1;
		}
		else if ($(this).css('background-color') == 'rgb(76, 175, 80)') //If selected (if green).
		{
			$(this).css('background-color', '#FFFFFF'); //Deselect.
			$(this).css('color', '#000000');
			selected -= 1;
		}
	}
	if (currentPage == "CallHistory" || currentPage == "ProblemList")
	{
		CheckIfUpdate();
	}
	else
	{
		CheckIfUpdateOrAdd();
	}
});

function GetRowWithID(id) //Returns row of a column with a given ID (first column, which is presumed to be the unique primary key).
{
	rows = GetRows();
	for (j = 1; j<rows; j++)
	{
		if (document.getElementById(GetTable()).rows[j].cells[0].innerHTML == id)
		{
			return j;
		}
	}
	return -1;
}

function SortTable(column) //Function sorts table by the selected column.
{
	table = document.getElementById(GetTable());
	swapping = true;
	shouldSwap = false;
	swapCount = 0;
	direction = "asc"; //Default direction is ascending.
	i = 0;
	while (swapping) //Loops until no swapping is performed.
	{
		swapping = false;
		for (i = 1; i < GetRows()-1; i++) //Iterate through all rows apart from top row.
		{
			shouldSwap = false;
			item1 = table.rows[i].cells[column]; //Gets 2 items to compare.
			item2 = table.rows[i+1].cells[column];
			if (isNaN(item1.innerHTML) || isNaN(item2.innerHTML)) //If either item is not a number, use string comparison.
			{
				if ((direction == "asc" && item1.innerHTML.toLowerCase() > item2.innerHTML.toLowerCase()) ||
				(direction == "desc" && item1.innerHTML.toLowerCase() < item2.innerHTML.toLowerCase())) //If conditions for swapping are true.
				{
					shouldSwap = true; //If swap to be made, break out.
					break;
				}
			}
			else //Else, use numeric comparison.
			{
				if ((direction == "asc" && parseFloat(item1.innerHTML) > parseFloat(item2.innerHTML)) ||
				(direction == "desc" && parseFloat(item1.innerHTML) < parseFloat(item2.innerHTML))) //If conditions for swapping are true.
				{
					shouldSwap = true; //If swap to be made, break out.
					break;
				}
			}
		}
		if (shouldSwap)
		{
			table.rows[i].parentNode.insertBefore(table.rows[i + 1], table.rows[i]); //Swap rows.
			swapping = true;
			swapCount++;
		}
		if (direction == "asc" && swapCount == 0) //If nothing has been swapped while trying to sort ascending, sort descending.
		{
			direction = "desc";
			swapping = true;
		}
	}
	for (i = 0; i < table.rows[0].cells.length; i++)
	{
		cell = table.rows[0].cells[i]; //Gets relevant header cell.
		if (cell.innerHTML.includes("↑") || cell.innerHTML.includes("↓")) //If arrow already exists in header cell.
		{
			cell.innerHTML = cell.innerHTML.slice(0, -1); //Remove current arrow from header cell.
		}
	}
	
	cell = table.rows[0].cells[column]; //Gets relevant header cell.
	if (direction == "asc") //If ascending, draw up arrow in header cell.
	{
		cell.innerHTML += "&#x2193";
	}
	else //If descending, draw down arrow in header cell.
	{
		cell.innerHTML += "&#x2191";
	}
}

function findAllChildren(parent) //Give it a generalisation and it will find all problem types which stem from this generalisation.
{
  var sql = "SELECT typeName FROM tblProblemType WHERE generalisation = '" + parent + "';"; //Finds all problem type with the given generalisation.
  $.get("Query.php", {'sql':sql, 'returnData':true},function(json){
    if (json && json[0]){
      for (i = 0; i < json.length; i++){
        result.push(json[i].typeName);
	findAllChildren(json[i].typeName); //Re-runs the function but with the newly discovered problem type as a generalisation.
      }
    }
  },'json');
  return(result); 
}