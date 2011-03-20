// class to deal with content creation

function output(text)
{
	outputDiv.innerHTML += text+"<br />";
}

function AdminApp()
{
	var currCallback = undefined;
	var ajax = undefined;

	this.getXMLHttp = function()
	{
		if(window.XMLHttpRequest )
		{
			return new XMLHttpRequest();
		}
		else
			return new ActiveXObject( "Microsoft.XMLHTTP" );
			
	}

	this.Init = function() 
	{
		this.loadContent("pages", function(response){alert(response);})
	}
	
	this.xmlInterpreter = function()
	{
		
	}
	
	this.loadContent = function(content, callback)
	{
		currCallback = callback;
		
		ajax = this.getXMLHttp();
		var url = 'DBAccess.php/'+content;
		output("loading url:"+url);
		ajax.open('GET', url);
//		this.ajax.setRequestHeader('X-Test', 'one');
//		this.ajax.setRequestHeader('X-Test', 'two');
		ajax.onreadystatechange = this.stateHandler;
		ajax.send();
	}
	
	this.stateHandler = function()
	{
		output("readyState:"+ajax.readyState);
		switch(ajax.readyState)
		{
			case 4:
				currCallback(ajax.responseText);
				break;
		}
	}
};

if(undefined==adminApp)
	var adminApp = new AdminApp();

// entry point
//$(document).ready(function()
//{
//	adminApp.Init();
//}
