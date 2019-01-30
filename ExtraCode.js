fun = false;
delList = []; //List of rows to be deleted when changes are saved to actual database.
updList = []; //List of rows to be updated when changes are saved to actual database.

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
	console.log("Rows = " + rows); //Logs to console for debugging purposes.
	return rows;
}

function Delete() //Function for deleting selected rows from a table.
{
	if (selected == 0) //if no rows are selected, leave function.
	{
		return;
	}
	if (confirm("Delete selected rows?")) //Get user confirmation.
	{
		rows = GetRows();
		for (i = rows-1; i > 0; i--) //Iterate through the rows of the table.
		{
			if (document.getElementById("tbl").rows[i].style.backgroundColor != 'rgb(159, 255, 48)') //If row is selected.
			{
				console.log("deleting t" + i);
				document.getElementById("tbl").deleteRow(i); //Delete the row.
				if (document.getElementById("tbl").rows[i].cells[0].innerHTML != "-") //If not a new item.
				{
					delList.push(document.getElementById("tbl").rows[i].cells[0].innerHTML); //Add record id to list of rows that will be deleted from the actual database later.
				}
			}
		}
		selected = 0;
		console.log(delList);
		CheckIfUpdateOrAdd();
	}
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
		document.getElementById("txtName").value = document.getElementById("tbl").rows[rowNum].cells[1].innerHTML;
		document.getElementById("txtJobTitle").value = document.getElementById("tbl").rows[rowNum].cells[2].innerHTML;
		document.getElementById("txtDepartment").value = document.getElementById("tbl").rows[rowNum].cells[3].innerHTML;
		document.getElementById("txtTelephoneNumber").value = document.getElementById("tbl").rows[rowNum].cells[4].innerHTML;
	}
	else
	{
		document.getElementById("btnAdd").value = "Add New Item";
		document.getElementById("txtName").value = "";
		document.getElementById("txtJobTitle").value = "";
		document.getElementById("txtDepartment").value = "";
		document.getElementById("txtTelephoneNumber").value = "";
	}
}