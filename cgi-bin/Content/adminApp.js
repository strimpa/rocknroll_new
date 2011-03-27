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
	
	function element(name)
	{
		return document.getElementById(name);
	}
	
	function output(text)
	{
		element("outputDiv").innerHTML += text+"<br />";
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
		
		function listboxEntryClick()
		{
			var index = $(this).attr("listBoxIndex");
			output("Selected idnex: "+index);
			$("#submenuEntries").attr("selectedIndex", index);
			$("#paragraphDropDown").find("option").each(function()
			{
				var index = $("#submenuEntries").attr("selectedIndex");
				var url = currSubMenuUrls[index];
				output("url: "+url);
				if($(this).text()==url)	
					element("paragraphDropDown").value = url;
			});
		}

		function populateSubMenuItems(response)
		{
		    $menuXml =  getXmlDocFromResponse(response);
		    // titles
		    var entries = $menuXml.find( 'entries' ).first().text();
		    var entryArray = entries.split(",");
			for(var entryIndex in entryArray)
			{
				var subMenuEntry = document.createElement("div");
				subMenuEntry.setAttribute("class", "listboxRow");
				subMenuEntry.textContent = entryArray[entryIndex];
				subMenuEntry.setAttribute("listboxIndex", entryIndex);
				$(subMenuEntry).mouseover(function(){$(this).attr("class", "listboxRow listboxRowHover");});
				$(subMenuEntry).mouseout(function(){$(this).attr("class", "listboxRow");});
				$(subMenuEntry).click(listboxEntryClick);
				$("#submenuEntries").append(subMenuEntry);
			};
			// anchor urls
		    var links = $menuXml.find( 'links' ).first().text();
		    currSubMenuUrls = links.split(",");
		}
	
		function populateParagraphs(response)
		{
		    $paragraphXml =  getXmlDocFromResponse(response);
//		    output($paragraphXml.text());
		    $paragraphXml.children().each(function()
		    {
		    	$(this).find( 'title').each(function(index, value)
			    {
					optn = document.createElement("OPTION");
					optn.textContent = $(this).text();
				    $("#paragraphDropDown").append(optn);
			    })
			});
			
		}
		
		function renderPargraphHTML(result)
		{
			$("#admincontent").append(result);
		}
		
		function importParagraphHTML(identifier)
		{
			var html = $.fn.loadContent("paragraphs", renderPargraphHTML, {"identifier":identifier}, "html");
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
		    $.fn.loadContent("submenus", populateSubMenuItems, {"id":menuRef}, "xml");
		    
			// trigger paragraph creation
		    var paragraphs = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
		    output("ContentPage paragraphs:"+paragraphs);
		    paragraphs = paragraphs.replace(/,/g, "' OR '");
		    $.fn.loadContent("paragraphs", populateParagraphs, {"id":paragraphs}, "xml");
		    
			importParagraphHTML(selection);
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
			
			// simulate listbox
			var texts = area.text().split("\n");
			if(rowNumber>=texts.length)
				return;
			var startIndex = area.text().indexOf(texts[rowNumber-1]);
			var endIndex = area.text().length;
			if(rowNumber<texts.length)
				endIndex  = area.text().indexOf(texts[rowNumber]);
			element("submenuEntries").setSelectionRange(startIndex, endIndex);
			
			// tyry to select dropdown item
			output("surrent link:"+currSubMenuUrls[rowNumber]);
		}
				
		$("#pagesDropDown").change(this.selectContentHandler);
		$("#createButton").click(this.createContentHandler);
		$.fn.loadContent("pages", populateContentDropDown, null, "xml");
		
	}

	/////////////////////////////////////////////////////////////////////////
	// Per instance functions
	/////////////////////////////////////////////////////////////////////////
	$.fn.loadContent = function(content, callback, requirements, type)
	{
		currCallback = callback;
		
		var url = null;
		switch(type)
		{
			case "xml":url = 'DBAccess.php/'+content; break;
			case "html":url = 'ContentAccess.php/'+content; break;
		}
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
		$.get(url, callback)
		.success(function() { output("second success"); })
		.error(function() { output("error"); })
		.complete(function() { output("complete"); });
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
