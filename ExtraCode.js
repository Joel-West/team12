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
}, 100);

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
	options = {day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric'} //Sets the time format.
	document.getElementById("dtLabel").innerHTML = dt.toLocaleDateString("en-UK", options); //Assigns time to label.
	var wait = setTimeout(WriteTime, 1000); //Checks the time every second.
}

function GoToNewPage(page) //Function that submits the main form of the current page, changing the page to that specified in the 'page' variable.
{
	document.getElementById("mainform").action = "http://35.204.60.31/" + page; //Defines page that data will be posted to.
	document.getElementById("mainform").submit(); //Submits form.
}

function GetRows() //Function for returning the number of rows in a data table.
{
	var rows = document.getElementById('tbl').getElementsByTagName("tr").length;
	//console.log("Rows = " + rows); //Logs to console for debugging purposes.
	return rows;
}

function GetSelectedRow() //Returns selected row (if only one is selected).
{
	rows = GetRows();
	for (i = rows-1; i > 0; i--) //Iterate through the rows of the table.
	{
		if (document.getElementById("tbl").rows[i].style.backgroundColor != 'rgb(159, 255, 48)') //If row is selected.
		{
			return i;
		}
	}
}

function AddPressed() //Function to add new row to the local data table.
{
	if (selected == 1) //If only 1 row is selected, thus if updating row.
	{
		UpdateRow();
	}
	else //Else, if adding row
	{
		AddRow()
	}
}

$(document).on('click','tr',function(event) //Function for selecting/deselecting rows.
{
	console.log($(this).attr('id')); //Logs ID (for debugging).
	if ($(this).attr('id') != 't0') //If not the header.
	{
		if ($(this).css('background-color') == 'rgb(159, 255, 48)') //If deselected (if green).
		{	
			$(this).css('background-color', '#00FFFF'); //Select.
			selected += 1;
		}
		else if ($(this).css('background-color') == 'rgb(0, 255, 255)') //If selected (if blue).
		{
			$(this).css('background-color', '#9FFF30'); //Deselect.
			selected -= 1;
		}
		console.log(selected);
	}
	CheckIfUpdateOrAdd();
});

function CheckIfUpdateOrAdd() //The 'add' button into an 'update' button and populate the text boxes, if exactly one row is selected.
{
	if (selected == 1)
	{
		document.getElementById("btnAdd").value = "Update Item";
		rowNum = GetSelectedRow(); //Gets the row that is selected.
		document.getElementById("txtID").value = document.getElementById("tbl").rows[rowNum].cells[0].innerHTML;
		document.getElementById("txtID").disabled = true;
		document.getElementById("txtName").value = document.getElementById("tbl").rows[rowNum].cells[1].innerHTML;
		document.getElementById("txtJobTitle").value = document.getElementById("tbl").rows[rowNum].cells[2].innerHTML;
		document.getElementById("txtDepartment").value = document.getElementById("tbl").rows[rowNum].cells[3].innerHTML;
		document.getElementById("txtTelephoneNumber").value = document.getElementById("tbl").rows[rowNum].cells[4].innerHTML;
	}
	else
	{
		document.getElementById("btnAdd").value = "Add New Item";
		document.getElementById("txtID").value = "";
		document.getElementById("txtID").disabled = false;
		document.getElementById("txtName").value = "";
		document.getElementById("txtJobTitle").value = "";
		document.getElementById("txtDepartment").value = "";
		document.getElementById("txtTelephoneNumber").value = "";
	}
}

function GetRowWithID(id) //Returns row of a column with a given ID (first column, which is presumed to be the primary key).
{
	for (i = 1; i<GetRows(); i++)
	{
		if (document.getElementById("tbl").rows[i].cells[0].innerHTML == id)
		{
			return i;
		}
	}
	return -1;
}

function SortTable(column) //Function sorts table by the selected column.
{
	table = document.getElementById("tbl");
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

userData = document.getElementsByName('User');
userData.addEventListener('DOMSubtreeModified', UserDataChanged);

function UserDataChanged(e)
{
	console.log(userData.innerHTML);
}