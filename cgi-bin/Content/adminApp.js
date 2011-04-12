// class to deal with content creation

(function($)
{
	var currCallback = undefined;
	var ajax = undefined;
	var busyLoading = false;
	var loadingQueue = new Array();
	var REST_WRITE = 1, REST_DELETE = 2, REST_EDIT = 3;

	/////////////////////////////////////////////////////////////////////////
	// 'Global functions'
	/////////////////////////////////////////////////////////////////////////
	
	function element(name)
	{
		return document.getElementById(name);
	}
	
	function output(text)
	{
		element("outputDiv").innerHTML = text+"<br />"+element("outputDiv").innerHTML;
	}
	
	/////////////////////////////////////////////////////////////////////////
	// view
	/////////////////////////////////////////////////////////////////////////
	
	$.view = function()
	{
		var currContent = null;
		var $contentCache = null;
		var currSubMenuUrls = null;
		var $paragraphXml = null;
		
		////////////////////////////////////////////////////////////////////////////////////////
		// General utils
		////////////////////////////////////////////////////////////////////////////////////////
		function getXmlDocFromResponse(response)
		{
			var xml = response;
		    xmlDoc = $.parseXML( xml );
			return $( xmlDoc );
		}
	
		function populateContentDropDown(response)
		{
			$("#pagesDropDown").empty();
			var optn = document.createElement("OPTION");
		    $("#pagesDropDown").append(optn);
		    $contentCache = getXmlDocFromResponse(response);
		    $contentCache.find( 'identifier' ).each(function(index, value)
		    {
				optn = document.createElement("OPTION");
				optn.textContent = $(this).text();
			    $("#pagesDropDown").append(optn);
		    });
//		    $("#pagesDropDown").effect("bounce", { times:10 }, 300);
		}
		
		////////////////////////////////////////////////////////////////////////////////////////
		// Global event handlers
		////////////////////////////////////////////////////////////////////////////////////////

		function listboxEntryMouseOut()
		{
			var matches = $(this).attr("class").match(/listboxRowSelected/);
			if(null==matches)
				$(this).attr("class", "listboxRow");
		}
		function listboxEntryMouseOver()
		{
			var matches = $(this).attr("class").match(/listboxRowSelected/);
			if(null==matches)
				$(this).attr("class", "listboxRow listboxRowHover");
		}
		function listboxEntryClick()
		{
			var index = $(this).attr("listBoxIndex");
			var matches = $(this).attr("class").match(/listboxRowSelected/);
			if(null!=matches)
			{
				$("#submenuEntries").attr("selectedIndex", -1);
				$(this).attr("class", "listboxRow listboxRowHover");
			}
			else
			{
				$("#submenuEntries").children().each(function(){
					$(this).attr("class", "listboxRow");
				});
				$(this).attr("class", "listboxRow listboxRowSelected");
//				output("selected index:"+index+", "+currSubMenuUrls[index]);
				$("#submenuEntries").attr("selectedIndex", index);
			}

			element("paragraphDropDown").selectedIndex = 0;
			$("#paragraphDropDown").find("option").each(function()
			{
				var index = $("#submenuEntries").attr("selectedIndex");
				var url = currSubMenuUrls[index];
				if($(this).text()==url)	
					element("paragraphDropDown").value = url;
			});
		};

		////////////////////////////////////////////////////////////////////////////////////////
		// ContentPage population
		////////////////////////////////////////////////////////////////////////////////////////

		this.selectContentHandler = function()
		{
			var selection = $("#pagesDropDown").attr("value");
			output("Selected Content page: "+selection);
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			output("Selected index:"+selectedIndex);
			
			//Title
			$("#pageTitle").attr("value", $($contentCache.find( 'title' )[selectedIndex]).text());

			// trigger submenu creation			
		    var menuRef = $($contentCache.find( 'menuRef' )[selectedIndex]).text();
			output("Sub menu reference: "+menuRef);
		    $.fn.loadContent("submenus", populateSubMenuItems, {"id":menuRef}, "xml");
		    
			// trigger paragraph creation
		    triggerParagraphCreation();
		};
		
		function createContentCallback(result)
		{
			output(result);
			$("#pageTitle").attr("value", "");
			$.fn.loadContent("pages", populateContentDropDown, null, "xml");
		}
		this.createContentHandler = function()
		{
			PageCreationDialog.createDialog(document, function()
			{
				var data = {};
				PageCreationDialog.getData(data); 
				$.fn.loadContent("pages", createContentCallback, data, "data", {write:true});
			});
		}
		this.editContentHandler = function()
		{
			var oldIdentifier = $("#pagesDropDown").attr("value");
			PageCreationDialog.createDialog(document, function()
			{
				var data = {};
				PageCreationDialog.getData(data); 
				var reqString = "identifier="+oldIdentifier;
				$.fn.loadContent("pages", createContentCallback, data, "data", {edit:true, req:reqString});
			}, {identifier:oldIdentifier, title:$("#pageTitle").attr("value")});
		}
		this.deleteContentHandler = function()
		{
			var confirmation = confirm("Sind Sie sicher dass Sie den Inhalt loeschen wollen?");
			if(!confirmation)
				return;
			var data = {identifier:$("#pagesDropDown").attr("value")};
			$.fn.loadContent("pages", createContentCallback, data, "data", {delete:true});
		}

		////////////////////////////////////////////////////////////////////////////////////////
		// Submenu population
		////////////////////////////////////////////////////////////////////////////////////////
		function populateSubMenuItems(response)
		{
			$("#submenuEntries").empty();
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
				$(subMenuEntry).mouseover(listboxEntryMouseOver);
				$(subMenuEntry).mouseout(listboxEntryMouseOut);
				$(subMenuEntry).click(listboxEntryClick);
				$("#submenuEntries").append(subMenuEntry);
			};
			// anchor urls
		    var links = $menuXml.find( 'links' ).first().text();
		    currSubMenuUrls = links.split(",");
		}
		function createMenuCallback(result)
		{
			output(result);
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
		    var menuRef = $($contentCache.find( 'menuRef' )[selectedIndex]).text();
			output("Sub menu reference: "+menuRef);
		    $.fn.loadContent("submenus", populateSubMenuItems, {"id":menuRef}, "xml");
		}
		this.createMenuEntryHandler = function()
		{
			if(null==$paragraphXml)
			{
				alert("Bitte waehlen Sie einen Artikel zum editieren!");
				return;
			}
			var allTitles = [];
			$paragraphXml.find( 'title' ).each(function(index, value)
		    {
				allTitles.push($(this).text());
		    });
			var presetValues = {url:allTitles.join(",")};
			SubMenuCreationDialog.createDialog(document, function(){
				// prepare data
				/////////////////////////
				var entryData = [];
				var urls = [];
				for(var c=0;c<element("submenuEntries").children.length;c++)
				{
					entryData.push(element("submenuEntries").children[c].innerHTML);
					urls.push(currSubMenuUrls[c]);
				}
				var newdata = {}; 
				SubMenuCreationDialog.getData(newdata);
				entryData.push(newdata['title']);
				urls.push(newdata['url']);
				var data = {entries:entryData.join(","), links:urls.join(",")};

				// Get menu id to update
				var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			    var menuRef = $($contentCache.find( 'menuRef' )[selectedIndex]).text();

			    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
			}, presetValues);
		}
		this.editMenuEntryHandler = function()
		{
			if(null==$paragraphXml)
			{
				alert("Bitte waehlen Sie einen Artikel zum editieren!");
				return;
			}
			var allTitles = [];
			$paragraphXml.find( 'title' ).each(function(index, value)
		    {
				allTitles.push($(this).text());
		    });
			var presetValues = {url:allTitles.join(",")};
			SubMenuCreationDialog.createDialog(document, function(){
				// prepare data
				/////////////////////////
				var entryData = [];
				var urls = [];
				for(var c=0;c<element("submenuEntries").children.length;c++)
				{
					entryData.push(element("submenuEntries").children[c].innerHTML);
					urls.push(currSubMenuUrls[c]);
				}
				var newdata = {}; 
				SubMenuCreationDialog.getData(newdata);
				entryData.push(newdata['title']);
				urls.push(newdata['url']);
				var data = {entries:entryData.join(","), links:urls.join(",")};

				// Get menu id to update
				var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			    var menuRef = $($contentCache.find( 'menuRef' )[selectedIndex]).text();

			    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
			}, presetValues);
		}
		this.selectParaForSubMenu = function()
		{
			if(null == $contentCache)
				return;
			alert("set paragraph...");
			// prepare data
			/////////////////////////
			var entryData = [];
			var urls = [];
			for(var c=0;c<element("submenuEntries").children.length;c++)
			{
				entryData.push(element("submenuEntries").children[c].innerHTML);
				urls.push(currSubMenuUrls[c]);
			}
			var entryIndex = $("#submenuEntries").attr("selectedIndex");
			urls[entryIndex] = $("#paragraphDropDown").attr("value");
			var data = {entries:entryData.join(","), links:urls.join(",")};

			// Get menu id to update
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
		    var menuRef = $($contentCache.find( 'menuRef' )[selectedIndex]).text();
			output(entryIndex+", "+urls[entryIndex]+", "+menuRef);

		    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
		}
	
		this.deleteMenuEntryHandler = function()
		{
			var confirmation = confirm("Sind Sie sicher dass Sie den Menueintrag loeschen wollen?");
			if(!confirmation)
				return;
			
			// prepare data
			/////////////////////////
			var entryData = [];
			var urls = [];
			for(var c=0;c<element("submenuEntries").children.length;c++)
			{
				entryData.push(element("submenuEntries").children[c].innerHTML);
				urls.push(currSubMenuUrls[c]);
			}
			var selMenuIndex = $("#submenuEntries").attr("selectedIndex");
			entryData.splice(selMenuIndex, 1);
			urls.splice(selMenuIndex, 1);
			var data = {entries:entryData.join(","), links:urls.join(",")};

			// Get menu id to update
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
		    var menuRef = $($contentCache.find( 'menuRef' )[selectedIndex]).text();

		    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true}, ("id="+menuRef));
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
	
		////////////////////////////////////////////////////////////////////////////////////////
		// Paragraph population
		////////////////////////////////////////////////////////////////////////////////////////
		
		function triggerParagraphCreation()
		{
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
		    var paragraphs = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
		    output("ContentPage paragraphs:"+paragraphs);
		    paragraphs = paragraphs.replace(/,/g, "$|^");
		    paragraphs = "^"+paragraphs+"$";
		    $.fn.loadContent("paragraphs", populateParagraphs, {"id":paragraphs}, "xml");
		};

		var TableTypeStrings = ["Text mit Bild rechts","Text mit Bild links","Tabelle"];
		function populateParagraphs(response)
		{
			$("#paragraphDropDown").empty();
			optn = document.createElement("OPTION");
		    $("#paragraphDropDown").append(optn);
		    $paragraphXml =  getXmlDocFromResponse(response);
//		    output(response);
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
			// fixup images
//			result = result.replace(/src=\"images/g, "src=\"../../images");
			$("#admincontent").append(result);
		}
		function importParagraphHTML(identifier)
		{
			$("#admincontent").empty();
			var html = $.fn.loadContent("paragraphs", renderPargraphHTML, {"identifier":identifier}, "html");
		}
		function createParagraphCallback(result)
		{
			output(result);
			triggerParagraphCreation();
		}
		
		this.createParagraphHandler = function()
		{
			if(null==$paragraphXml)
			{
				alert("Bitte waehlen Sie einen Artikel zum editieren!");
				return;
			}
			ParagraphCreationDialog.createDialog(document, function()
			{
				var data = {}; 
				ParagraphCreationDialog.getData(data);
//				for(d in data)
//					output(d+":"+data[d]);
				if(data['type']!="Tabelle")
				{
					// Text and picture. Insert pic.
					var picdata = {url:data['picUrl'], title:data['picTitle']};
					$.fn.loadContent("pictures", function(result)
					{
						output(result);
						var lastPicIndex = $(result).find("max_id_").text();
						var metaString = "height="+data['height']+";table="+data['table']+";category="+data['category']+";image="+lastPicIndex;
						var paraData = {
							title:data['title'],
							type:TableTypeStrings.indexOf(data['type']),
							meta:metaString
						};
						$.fn.loadContent("paragraphs", function(result)
						{
							output(result);
							var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
						    var paragraphString = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
						    var pageIndex = $($contentCache.find( 'id' )[selectedIndex]).text();
							var lastParaIndex = $(result).find("max_id_").text();
							paragraphString += ","+lastParaIndex;
							var pageData = {
								paragraphs:paragraphString
							};
//							alert(pageIndex);
//							for(var k in pageData)
//								alert(k+", "+pageData[k]);
							$.fn.loadContent("pages", function(result)
							{
								output(result);
								alert("Content successfully created.");
								triggerParagraphCreation();
							}, pageData, "data", {edit:true,req:("id="+pageIndex)});
						}, paraData, "data", {write:true});
					}, picdata, "data", {write:true});
				}
			}, {type:TableTypeStrings.join(",")});
		}
				
		////////////////////////////////////////////////////////////////////////////////////////
		// Entry point
		////////////////////////////////////////////////////////////////////////////////////////
		$("#pagesDropDown").change(this.selectContentHandler);
		$("#createPageButton").click(this.createContentHandler);
		$("#editPageButton").click(this.editContentHandler);
		$("#deletePageButton").click(this.deleteContentHandler);
		$("#createMenuEntryButton").click(this.createMenuEntryHandler);
		$("#editMenuEntryButton").click(this.editMenuEntryHandler);
		$("#deleteMenuEntryButton").click(this.deleteMenuEntryHandler);
		$("#createParagraphButton").click(this.createParagraphHandler);
		$("#paragraphDropDown").change(this.selectParaForSubMenu);
		$.fn.loadContent("pages", populateContentDropDown, null, "xml");
		
	}

	/////////////////////////////////////////////////////////////////////////
	// Per instance functions
	/////////////////////////////////////////////////////////////////////////
	$.fn.loadCallback = function(e)
	{
/*		output("success:"+e.success());
		var s = e.success();
		if(null!=s)
			for(d in s){output("e:"+s[d])};
*/	}
	
	$.fn.loadContent = function(content, callback, data, type, params)
	{
		var url = null;
		switch(type)
		{
			case "html":url = 'ContentAccess/'+content; break;
			default:url = 'DBAccess/'+content; break;
		}
		for(p in params)
		{
			url += "/"+p+":"+params[p];
		}
		output("loading url:"+url);
//		if(null!=data)
//			for(d in data){output("data:"+data[d])};
		
		$.post(url, data, callback)
//		.success($.fn.loadCallback)
		.error($.fn.loadCallback)
		.complete($.fn.loadCallback);
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
