fun = false;

window.setInterval(function()
{
	if (fun == true)
	{
		col = GetRandomCol();
		document.body.style.backgroundColor = "rgb("+col[0]+", "+col[1]+", "+col[2]+")";
	}
}, 100);

function Fun()
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

function GetRandomCol() //Function chooses 3 random values between 60 and 255, which will later be used to generate an RGB colour. Rewritten from PHP to JS.
{
	var x = [];
	x[0] = Math.floor(Math.random()*255);
	x[1] = Math.floor(Math.random()*255);
	x[2] = Math.floor(Math.random()*255);
	return x;
}
function WriteTime()
{
	var dt = new Date();
	options = {day: 'numeric', month: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric'}
	document.getElementById("dtLabel").innerHTML = dt.toLocaleDateString("en-UK", options);
	var wait = setTimeout(WriteTime, 1000); //Checks the time every second.
}
function GoToNewPage(page)
{
	location.replace(page);
}