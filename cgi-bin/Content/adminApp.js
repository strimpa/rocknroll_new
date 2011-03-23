// class to deal with content creation

(function($)
{
	var currCallback = undefined;
	var ajax = undefined;
	var busyLoading = false;
	var loadingQueue = new Array();

	/////////////////////////////////////////////////////////////////////////
	// 'Global functions'
	/////////////////////////////////////////////////////////////////////////
	function output(text)
	{
		outputDiv.innerHTML += text+"<br />";
	}
	
	function getXMLHttp()
	{
		if(window.XMLHttpRequest )
		{
			return new XMLHttpRequest();
		}
		else
			return new ActiveXObject( "Microsoft.XMLHTTP" );
			
	}
	function stateHandler()
	{
		output("readyState:"+ajax.readyState);
		switch(ajax.readyState)
		{
			case 4:
				currCallback(ajax.responseText);
				break;
		}
	}

	function xmlInterpreter()
	{
		
	}

	/////////////////////////////////////////////////////////////////////////
	// view
	/////////////////////////////////////////////////////////////////////////
	
	$.view = function()
	{
		var currContent = null;
		var $contentCache = null;
		var currSubMenuUrls = null;
		
		function getXmlDocFromResponse(response)
		{
			var xml = response;
		    xmlDoc = $.parseXML( xml );
			return $( xmlDoc );
		}
	
		function populateContentDropDown(response)
		{
		    $contentCache = getXmlDocFromResponse(response);
		    $contentCache.find( 'identifier' ).each(function(index, value)
		    {
				optn = document.createElement("OPTION");
				optn.textContent = $(this).text();
			    $("#pagesDropDown").append(optn);
		    });
		}

		function populateSubMenuItems(response)
		{
		    $menuXml =  getXmlDocFromResponse(response);
		    // titles
		    var entries = $menuXml.find( 'entries' ).first().text();
		    entries = entries.replace(/,/g, "\n");
			$("#submenuEntries").append(entries);
			// anchor urls
		    var entries = $menuXml.find( 'links' ).first().text();
		    currSubMenuUrls = entries.split(",");
		}
	
		function populateParagraphs(response)
		{
		    $menuXml =  getXmlDocFromResponse(response);
		}

		this.selectContentHandler = function()
		{
			var selection = $("#pagesDropDown").attr("value");
			output("Selected Content page: "+selection);
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			output("Selected index:"+selectedIndex);

			// trigger submenu creation			
		    var menuRef = $($contentCache.find( 'menuRef' )[selectedIndex]).text();
			output("Sub menu reference: "+menuRef);
		    $.fn.loadContent("submenus", populateSubMenuItems, {"id":menuRef});
		    
			// trigger paragraph creation
		    var paragraphs = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
		    output("ContentPage paragraphs:"+paragraphs);
		    paragraphs.replace(/,/g, " OR ");
		    $.fn.loadContent("paragraphs", populateParagraphs, {"id":paragraphs});
		}
		
		this.createContentHandler = function()
		{
		}
		
		this.selectSubMenuItemHandler = function(e)
		{
			var area = $(this);
			var rowNumber = (e.clientY-area.offset().top);// / 
			var charHeight = parseInt(area.css("font-size"), 10);
			rowNumber /= charHeight;
			rowNumber = Math.floor(rowNumber);
			output("Selection row number:"+rowNumber);
			var texts = area.text().split("\n");
			if(rowNumber>=texts.length)
				return;
			var startIndex = area.text().indexOf(texts[rowNumber-1]);
			var endIndex = area.text().length;
			if(rowNumber<texts.length)
				endIndex  = area.text().indexOf(texts[rowNumber]);
			submenuEntries.setSelectionRange(startIndex, endIndex);
		}
				
		$("#pagesDropDown").change(this.selectContentHandler);
		$("#createButton").click(this.createContentHandler);
		$("#submenuEntries").click(this.selectSubMenuItemHandler);
		$.fn.loadContent("pages", populateContentDropDown);
		
	}

	/////////////////////////////////////////////////////////////////////////
	// Per instance functions
	/////////////////////////////////////////////////////////////////////////
	$.fn.loadContent = function(content, callback, requirements)
	{
		currCallback = callback;
		
		ajax = getXMLHttp();
		var url = 'DBAccess.php/'+content;
		if(null!=requirements)
		{
			url += "?"
			var appendSeparator = false;
			for(var key in requirements)
			{
				if(appendSeparator)
				{
					url += "&amp;";
				}
				value = requirements[key];
				url += key+"="+value;
				appendSeparator = true;
			}
		}
		output("loading url:"+url);
		ajax.open('GET', url);
//		this.ajax.setRequestHeader('X-Test', 'one');
//		this.ajax.setRequestHeader('X-Test', 'two');
		ajax.onreadystatechange = stateHandler;
		ajax.send();
	}
	$.fn.Init = function() 
	{
		$.view();
	}
	
})(jQuery);

// entry point
$(document).ready(function()
{
    $.fn.Init();
});
