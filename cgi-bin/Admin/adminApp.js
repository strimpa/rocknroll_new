﻿// class to deal with content creation

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
			$("#pageTitle").attr("value", "");
			var selection = $("#pagesDropDown").attr("value");
			output("Selected Content page: "+selection);
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			output("Selected index:"+selectedIndex);
			
			//Title
			var myIndex = $($contentCache.find( 'id' )[selectedIndex]).text();
			$.fn.loadContent("navigation", function(result)
			{
				$("#pageTitle").attr("value", $(result).find( 'title' ).first().text());
				$("#menuPriority").attr("value", $(result).find( 'priority' ).first().text());
			}, {"pageRef":myIndex}, "xml");

			// trigger submenu creation			
		    var menuRef = $($contentCache.find( 'menuRef' )[selectedIndex]).text();
			output("Sub menu reference: "+menuRef);
		    $.fn.loadContent("submenus", populateSubMenuItems, {"id":menuRef}, "xml");
		    
			// trigger paragraph creation
		    triggerParagraphCreation();
		};
		
		function refreshPages(callback)
		{
			$.fn.loadContent("pages", function(result)
			{
				$contentCache = $(result);
				callback();
			}, null, "xml");
		}
		function contentEditHandler(result)
		{
			refreshPages(populateContentDropDown);
		}
		this.createContentHandler = function()
		{
			PageCreationDialog.createDialog(document, function()
			{
				var data = {};
				PageCreationDialog.getData(data);
				var menuTitle = data['menuTitle'];
				var menuPriority = data['priority'];
				var pattern = /[^a-z^A-Z^_]/ig;
				var pageData = {title:menuTitle};
				pageData['identifier'] = data['identifier'].replace(pattern, "_"); 
				$.fn.loadContent("pages", function(result)
				{
					if(pageData['identifier'])
					{
						var thePageRef = $(result).find("max_id_").text();
						var naviData = {title:menuTitle, pageRef:thePageRef, priority:menuPriority};
						$.fn.loadContent("navigation", contentEditHandler, naviData, "data", {write:true});
					}
					else
						contentEditHandler(result);
				}, pageData, "data", {write:true});
			});
		}
		this.editContentHandler = function()
		{
			var theTitle = $("#pageTitle").attr("value");
			var theID = $("#pagesDropDown").attr("value");
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			var pageId = $($contentCache.find( 'id' )[selectedIndex]).text();
			$.fn.loadContent("navigation", function(naviResult)
			{
				var naviId = $(naviResult).find("id").text();
				var menuPriority = $(naviResult).find("priority").text();
				PageCreationDialog.createDialog(document, function()
				{
					var data = {};
					PageCreationDialog.getData(data);
					var reqString = "id="+pageId;
					var menuTitle = data['menuTitle'];
					var newMenuPriority = data['priority'];
					var pattern = /[^a-z^A-Z^_]/ig;
	//				data['title'] = data['title'].replace(pattern, "_"); 
					var pageData = {title:data['title']};
					$.fn.loadContent("pages", function(result)
					{
						// if existent - create, otherwise - delete
//						alert("menuTitle:"+data['menuTitle']);
						if(data['menuTitle']!="")
						{
							// is navigation entry already existent?
							var idTestData = {pageRef:pageId};
							$.fn.loadContent("navigation", function(naviTestResult)
							{
								if($(naviTestResult).find("id").size()>0)
								{
									var naviData = {title:menuTitle, priority:newMenuPriority};
									var reqString = "pageRef="+pageId;
									$.fn.loadContent("navigation", contentEditHandler, naviData, "data", {edit:true, req:reqString});
								}
								else
								{
									var naviData = {title:menuTitle, pageRef:pageId, priority:newMenuPriority};
									$.fn.loadContent("navigation", contentEditHandler, naviData, "data", {write:true});
								}
							}, idTestData, "data");
						}
						else
						{
							var naviDelData = {pageRef:pageId};
							$.fn.loadContent("navigation", contentEditHandler, naviDelData, "data", {delete:true});
						}
					}, pageData, "data", {edit:true, req:reqString});
				}, {identifier:theID, menuTitle:$("#pageTitle").attr("value"), priority:menuPriority});
			}, {pageRef:pageId}, "data");
		}
		this.deleteContentHandler = function()
		{
			var confirmation = confirm("Sind Sie sicher dass Sie den Inhalt loeschen wollen?");
			if(!confirmation)
				return;
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
	    var pageId = $($contentCache.find( 'id' )[selectedIndex]).text();
			var data = {id:pageId};
			var navidata = {pageRef:pageId};
			$.fn.loadContent("pages", function(result)
			{
				$.fn.loadContent("navigation", contentEditHandler, navidata, "data", {delete:true});
			}, data, "data", {delete:true});
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
		    var entryArray = entries.split("|");
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
		    currSubMenuUrls = links.split("|");
		}
		function createMenuCallback(result)
		{
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
		    var menuRef = $($contentCache.find( 'menuRef' )[selectedIndex]).text();
			output("Sub menu reference: "+menuRef);
		    $.fn.loadContent("submenus", populateSubMenuItems, {"id":menuRef}, "xml");
		}
		this.createMenuEntryHandler = function()
		{
			if(null==$contentCache)
			{
				alert("Bitte waehlen Sie einen Artikel zum editieren!");
				return;
			}
			var allTitles = [];
			$("#paragraphDropDown").find( 'OPTION' ).each(function(index, value)
		    {
				allTitles.push($(this).text());
		    });
		    
			var presetOptions = {url:allTitles.join("|")};
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
				var data = {entries:entryData.join("|"), links:urls.join("|")};

				// Get menu id to update
				var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			    var menuRef = $($contentCache.find( 'menuRef' )[selectedIndex]).text();

			    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
			}, null, presetOptions);
		}
		this.editMenuEntryHandler = function()
		{
			if(null==$contentCache)
			{
				alert("Bitte waehlen Sie einen Artikel zum editieren!");
				return;
			}
			var allTitles = [];
			$("#paragraphDropDown").find( 'OPTION' ).each(function(index, value)
		    {
				allTitles.push($(this).text());
		    });
			var presetOptions = {url:allTitles.join("|")};
			var index = $("#submenuEntries").attr("selectedIndex");
			var selection = element("submenuEntries").children[index].innerHTML;
			var presetValues = {title:selection, url:currSubMenuUrls[index]};
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
				currSubMenuUrls[index] = newdata['url'];
				var data = {entries:entryData.join("|"), links:currSubMenuUrls.join("|")};

				// Get menu id to update
				var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			    var menuRef = $($contentCache.find( 'menuRef' )[selectedIndex]).text();

			    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
			}, presetValues, presetOptions);
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
			var data = {entries:entryData.join("|"), links:urls.join("|")};

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
			var data = {entries:entryData.join("|"), links:urls.join("|")};

			// Get menu id to update
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
		    var menuRef = $($contentCache.find( 'menuRef' )[selectedIndex]).text();

		    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
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
		
		function populateAllParagraphSelect(response)
		{
			$("#insertParagraphSelect").empty();

			optn = document.createElement("OPTION");
		    $("#insertParagraphSelect").append(optn);
		    //output(response);
			$(response).find("row").each(function()
		    {
		    	var id = $(this).find( 'id').text();
		    	var title = $(this).find( 'title').text();
				optn = document.createElement("OPTION");
				optn.textContent = title + " - " + id;
			    $("#insertParagraphSelect").append(optn);
			});		
		}
		
		function triggerParagraphCreation()
		{
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			refreshPages(function(result)
			{
			    populateParagraphs();

			    $.fn.loadContent("paragraphs", populateAllParagraphSelect, null, "xml");
			});
		};
		
		function interpreteMetaData(metaString)
		{
			var items = metaString.split(";");
			var metaObject = {};
			for(i in items)
			{
				var metaTuple = items[i].split("=");
				metaObject[metaTuple[0]] = metaTuple[1];
			}
			return metaObject;
		}
		
		function editTableTrigger(defaultData)
		{
			TableEntryDialog.createDialog(document, function()
			{
				var data = {}; 
				TableEntryDialog.getData(data);
				var theId = defaultData['id'];
				if(data['newCategory']!="")
					data['category'] = data['newCategory'];
				data.newCategory = null;	
				if(null!=theId)
					$.fn.loadContent(defaultData['table'], triggerParagraphCreation, data, "data", {edit:true,req:("id="+theId)});
				else
					$.fn.loadContent(defaultData['table'], triggerParagraphCreation, data, "data", {write:true});
			}, defaultData);
		}
		
		function renderPargraphHTML(paraDiv, paragraphData, paraIndex)
		{
			output(paragraphData.find("meta").text());
			metaData = interpreteMetaData(paragraphData.find("meta").text());

			paraDiv.setAttribute("class", "adminParagraph");
			paraDiv.style.height = metaData['height'] + "px";
//			heightobj.offset += parseInt(metaData['height']);
			// title
			var editDiv = document.createElement("div");
			editDiv.setAttribute("class", "editDiv");
			editDiv.style.textAlign = "right";
			var editParaButton = document.createElement("input");
			editParaButton.setAttribute("type", "button");
			editParaButton.setAttribute("value", "edit");
			editParaButton.setAttribute("class", "editButton");
			editParaButton.setAttribute("disabled", "true");
			editDiv.appendChild(editParaButton);
			var deleteParaButton = document.createElement("input");
			deleteParaButton.setAttribute("type", "button");
			deleteParaButton.setAttribute("value", "delete "+paraIndex);
			deleteParaButton.setAttribute("class", "deleteButton");
			editDiv.appendChild(deleteParaButton);
			var upParaButton = document.createElement("input");
			upParaButton.setAttribute("type", "button");
			upParaButton.setAttribute("value", "nach oben");
			editDiv.appendChild(upParaButton);
			var downParaButton = document.createElement("input");
			downParaButton.setAttribute("type", "button");
			downParaButton.setAttribute("value", "nach unten");
			editDiv.appendChild(downParaButton);
			paraDiv.appendChild(editDiv);
			// title
			var paraID = paragraphData.find("id").text();
			var titleDiv = document.createElement("div");
			titleDiv.setAttribute("class", "paragraphTitle");
			titleDiv.textContent = paragraphData.find("title").text();
			paraDiv.appendChild(titleDiv);
			
			var type = paragraphData.find("type").text()
			var imageUrl = "";
			var imageTitle = "";
			var paraContent = "";
			var picID = -1;
			// image
			if(null!=metaData['image'])
			{
				var imgDiv = document.createElement("div");
//				var classString = "";
				imgDiv.setAttribute("class", "picFrame picFrameLeft");
				$.fn.loadContent("pictures", function(result)
				{
					picID = $(result).find("id").text();
					var img = document.createElement("img");
					imageUrl = $(result).find("url").text();
					img.setAttribute("src", imageUrl);
					imgDiv.appendChild(img);
					editParaButton.removeAttribute("disabled");
					var txt = document.createElement("div");
					imageTitle = $(result).find("title").text();
					txt.textContent = imageTitle;
					imgDiv.appendChild(txt);
				}, {"id":metaData['image']}, "xml");
				paraDiv.appendChild(imgDiv);
			}
			else
			{
				editParaButton.removeAttribute("disabled");
			}
			switch(type)
			{
			case "0":
			case "1":
				// title
				var textDiv = document.createElement("div");
				textDiv.setAttribute("class", "paragraphContent");
				var contentHtml = $(paragraphData.find("content").children().first());
//				alert($(contentHtml).html());
				$(textDiv).html(contentHtml);
				paraContent = $(textDiv).html();
				paraDiv.appendChild(textDiv);
				break;
			case "2":
				if(metaData['table'])
				{
					var theTable = metaData['table'];
					var tableDiv = document.createElement("div");
					tableDiv.setAttribute("class", "adminTableDiv");

					var addEntryButton = document.createElement("input");
					addEntryButton.setAttribute("type", "button");
					addEntryButton.setAttribute("value", "Neu");
					addEntryButton.setAttribute("class", "editButton");
					tableDiv.appendChild(addEntryButton);

					$(addEntryButton).click(function()
					{
						editTableTrigger({table:theTable});
					});

					var table = document.createElement("table");
					table.setAttribute("class", "adminTable");
					$.fn.loadContent(metaData['table'], function(result)
					{
						$(result).find("row").each(function()
						{
							var rowDiv = document.createElement("tr");
//							rowDiv.setAttribute("class", "paragraphContent");
							var editDiv = document.createElement("td");
							editDiv.setAttribute("class", "editDiv");
							editDiv.style.textAlign = "right";
							var editEntryButton = document.createElement("input");
							editEntryButton.setAttribute("type", "button");
							editEntryButton.setAttribute("value", "edit");
							editEntryButton.setAttribute("class", "editButton");
							editDiv.appendChild(editEntryButton);
							var deleteEntryButton = document.createElement("input");
							deleteEntryButton.setAttribute("type", "button");
							deleteEntryButton.setAttribute("value", "delete");
							deleteEntryButton.setAttribute("class", "deleteButton");
							editDiv.appendChild(deleteEntryButton);
							rowDiv.appendChild(editDiv);
							$(this).children().each(function()
							{
								var dataDiv = document.createElement("td");
								dataDiv.textContent = $(this).text();
//								rowDiv.setAttribute("class", "paragraphContent");
								rowDiv.appendChild(dataDiv);
							});
							table.appendChild(rowDiv);
							var eventId = $(this).find("id").text();
							// clck handlers
							var defaultEditData = {
								table:theTable,
								id:eventId,
								category:$(this).find("category").text(),
								title:$(this).find("title").text(),
								date:$(this).find("date").text(),
								description:$(this).find("description").text(),
								venue:$(this).find("venue").text(),
								time:$(this).find("time").text(),
								misc:$(this).find("misc").text()
							};
							$(editEntryButton).click(function()
							{
								editTableTrigger(defaultEditData);
							});
							$(deleteEntryButton).click(function()
							{
								var confirmation = confirm("Sind Sie sicher dass Sie den Eintrag loeschen wollen?");
								if(confirmation)
								{
									$.fn.loadContent(theTable, function(result)
									{
										alert("Eintrag erfolgreich geloescht.");
										triggerParagraphCreation();
									}, {id:eventId}, "data", {delete:true});
								}
							});
						});
					}, {"category":metaData['category']}, "xml");
					tableDiv.appendChild(table);
					paraDiv.appendChild(tableDiv);
				}
				break;
			default:
				break;
			}
			
			// clck handlers
			$(editParaButton).click(function()
			{
				var defaultData = {
					title:titleDiv.textContent,
					type:metaData['type'],
					height:metaData['height'],
					picUrl:imageUrl,
					picTitle:imageTitle,
					content:paraContent,
					table:metaData['table'],
					category:metaData['category'],
				};
				$.createParagraphHandler(defaultData, paraID, picID);
			});
			$(deleteParaButton).click(function()
			{
				var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			    var paragraphString = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
			    var pageIndex = $($contentCache.find( 'id' )[selectedIndex]).text();
			    var paraArray = paragraphString.split(",");
			    paraArray.splice(paraIndex, 1);
			    for(p in paraArray)
			    	if(paraArray[p]=="")
			    		paraArray.splice(p, 1);
				var pageData = {
					paragraphs:paraArray.join(",")
				};
//				alert("paraindex:"+paraIndex+", new para string:"+paraArray.join(","));
				$.fn.loadContent("pages", function(result)
				{
					alert("Absatz erfolgreich geloescht.");
					triggerParagraphCreation();
				}, pageData, "data", {edit:true,req:("id="+pageIndex)});
			});
			$(upParaButton).click(function()
			{
				if(paraIndex<=0)
				{
					alert("Dies ist bereits der erste Paragraph.");
					return;
				}
				var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			    var paragraphString = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
			    var pageIndex = $($contentCache.find( 'id' )[selectedIndex]).text();
			    var paraArray = paragraphString.split(",");
				var formerVal = paraArray[paraIndex-1];
				paraArray[paraIndex-1] = paraArray[paraIndex];
				paraArray[paraIndex] = formerVal;
			    for(p in paraArray)
			    	if(paraArray[p]=="")
			    		paraArray.splice(p, 1);
				var pageData = {
					paragraphs:paraArray.join(",")
				};
				$.fn.loadContent("pages", function(result)
				{
					triggerParagraphCreation();
				}, pageData, "data", {edit:true,req:("id="+pageIndex)});
			});
			$(downParaButton).click(function()
			{
				var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			    var paragraphString = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
			    var pageIndex = $($contentCache.find( 'id' )[selectedIndex]).text();
			    var paraArray = paragraphString.split(",");
				if(paraIndex>=paraArray.length-1)
				{
					alert("Dies ist bereits der letzte Paragraph.");
					return;
				}
				var followingVal = paraArray[paraIndex+1];
				paraArray[paraIndex+1] = paraArray[paraIndex];
				paraArray[paraIndex] = followingVal;
			    for(p in paraArray)
			    	if(paraArray[p]=="")
			    		paraArray.splice(p, 1);
				var pageData = {
					paragraphs:paraArray.join(",")
				};
				$.fn.loadContent("pages", function(result)
				{
					triggerParagraphCreation();
				}, pageData, "data", {edit:true,req:("id="+pageIndex)});
			});
		}


		function populateParagraphs()
		{
			$("#admincontent").empty();
			$("#paragraphDropDown").empty();

			optn = document.createElement("OPTION");
		    $("#paragraphDropDown").append(optn);
			var contentDiv = document.createElement("div");
			contentDiv.setAttribute("class", "contentDiv");
			
//			var heightobj = new Object();
//			heightobj.offset = 0;
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
		    var paragraphs = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
		    var paraArray = paragraphs.split(",");

		    for(paraIndex in paraArray)
		    {
		    	if(paraArray[paraIndex]=="")
		    		continue;
				var paraDiv = document.createElement("div");
				paraDiv.setAttribute("id", "paragraph_"+paraArray[paraIndex]);
		    	$.fn.loadContent("paragraphs", function(result)
		    	{
		    		result = getXmlDocFromResponse(result);
						$(result).find("row").each(function()
				    {
//						alert("localParaIndex:"+$(this).find('id').text());
						var localParaIndex = paraArray.indexOf($(this).find('id').text());
//				    	output("id:"+$(this).find('id').text());
				    	$(this).find('title').each(function(index, value)
					    {
							optn = document.createElement("OPTION");
							optn.textContent = $(this).text();
						    $("#paragraphDropDown").append(optn);
					    })
					    
					    // paragraph itself
					    var reverseIndex = paraArray.length-localParaIndex;
					    var myParagraphId = $(this).find("id").first().text();
					    var myParagraph = element("paragraph_"+myParagraphId);
					    renderPargraphHTML(myParagraph, $(this), localParaIndex);
					});
		    	}, {id:paraArray[paraIndex]}, "data");
					contentDiv.appendChild(paraDiv);
		    }
			$("#admincontent").append(contentDiv);
		}
//		function importParagraphHTML(identifier)
//		{
//			$("#admincontent").empty();
//			var html = $.fn.loadContent("paragraphs", renderPargraphHTML, {"identifier":identifier}, "html");
//		}
//		function createParagraphCallback(result)
//		{
//			output(result);
//			triggerParagraphCreation();
//		}
		
		this.createParagraphHandler = function(defaultData, paraID, picID)
		{
			if(null==$contentCache)
			{
				alert("Bitte waehlen Sie einen Artikel zum editieren!");
				return;
			}
			var pictureID = picID;
			if(null==defaultData)
				defaultData = {};
			ParagraphCreationDialog.createDialog(document, function()
			{
				var data = {}; 
				ParagraphCreationDialog.getData(data);
//				for(d in data)
//					output(d+":"+data[d]);
				if(data['type']!="Tabelle")
				{
					var metaString = "height="+data['height']+";table="+data['table']+";category="+data['category'];
					var paraData = {
						title:data['title'],
						type:TableTypeStrings.indexOf(data['type']),
						content:data['content'],
						meta:metaString
					};
					var paraParams = {write:true};
					if(paraID!=null)
						paraParams = {edit:true,req:"id="+paraID};
					// Text and picture. Insert pic.
					if(data['picUrl']!="")
					{
						// try to find pic in database
						var picReadData = {"url":data['picUrl']};
						$.fn.loadContent("pictures", function(result)
						{
							// no picture found: insert!
							var foundID = $(result).find("id").text();
							if(""==foundID)
							{
								var picWriteData = {"url":data['picUrl'],"title":data['picTitle']};
								var picWriteParams = {write:true};
								$.fn.loadContent("pictures", function(result)
								{
									var lastPicIndex = $(result).find("max_id_").text();
									metaString += ";image="+lastPicIndex;
									$.fn.loadContent("paragraphs", function(result)
									{
										var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
									    var paragraphString = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
									    var pageIndex = $($contentCache.find( 'id' )[selectedIndex]).text();
										var lastParaIndex = $(result).find("max_id_").text();
										paragraphString += ","+lastParaIndex;
										var pageData = {
											paragraphs:paragraphString
										};
										$.fn.loadContent("pages", function(result)
										{
											output("Content successfully created.");
											triggerParagraphCreation();
										}, pageData, "data", {edit:true,req:("id="+pageIndex)});
									}, paraData, "data", paraParams);
								}, picWriteData, "data", picWriteParams);
							}
							else // picture found, just insert into paragraphs
							{
								var metaString = "height="+data['height']+";table="+data['table']+";category="+data['category']+";image="+foundID;
								var paraData = {
									title:data['title'],
									type:TableTypeStrings.indexOf(data['type']),
									content:data['content'],
									meta:metaString
								};
								var paraParams = {write:true};
								if(paraID!=null)
									paraParams = {edit:true,req:"id="+paraID};
								$.fn.loadContent("paragraphs", function(result)
								{
									var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
								    var paragraphString = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
								    var pageIndex = $($contentCache.find( 'id' )[selectedIndex]).text();
									var lastParaIndex = $(result).find("max_id_").text();
									paragraphString += ","+lastParaIndex;
									var pageData = {
										paragraphs:paragraphString
									};
									$.fn.loadContent("pages", function(result)
									{
										output("Content successfully created.");
										triggerParagraphCreation();
									}, pageData, "data", {edit:true,req:("id="+pageIndex)});
								}, paraData, "data", paraParams);							
							}
						}, picReadData, "xml");
					}
					else // NO picture entered
					{
						$.fn.loadContent("paragraphs", function(result)
						{
							var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
						    var paragraphString = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
						    var pageIndex = $($contentCache.find( 'id' )[selectedIndex]).text();
							var lastParaIndex = $(result).find("max_id_").text();
							paragraphString += ","+lastParaIndex;
							var pageData = {
								paragraphs:paragraphString
							};
							$.fn.loadContent("pages", function(result)
							{
								output("Content successfully created.");
								triggerParagraphCreation();
							}, pageData, "data", {edit:true,req:("id="+pageIndex)});
						}, paraData, "data", paraParams);

					}
				}
				else
				{
					// Tabelle creation
					var metaString = "height="+data['height']+";table="+data['table']+";category="+data['category'];
					var paraData = {
						title:data['title'],
						type:TableTypeStrings.indexOf(data['type']),
						content:data['content'],
						meta:metaString
					};
					var paraParams = {write:true};
					if(paraID!=null)
						paraParams = {edit:true,req:"id="+paraID};
					$.fn.loadContent("paragraphs", function(result)
					{
						var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
					    var paragraphString = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
					    var pageIndex = $($contentCache.find( 'id' )[selectedIndex]).text();
						var lastParaIndex = $(result).find("max_id_").text();
						paragraphString += ","+lastParaIndex;
						var pageData = {
							paragraphs:paragraphString
						};
						$.fn.loadContent("pages", function(result)
						{
							output("Content successfully created.");
							triggerParagraphCreation();
						}, pageData, "data", {edit:true,req:("id="+pageIndex)});
					}, paraData, "data", paraParams);
				}
			}, defaultData);
		}
				
		this.insertParagraphHandler = function(defaultData, paraID, picID)
		{
			if(null==$("#paragraphDropDown").find( 'OPTION' ))
			{
				alert("Bitte waehlen Sie einen Artikel zum editieren!");
				return;
			}
			if(null==defaultData)
				defaultData = {};
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
		    var paragraphString = $($contentCache.find( 'paragraphs' )[selectedIndex]).text();
		    var selectedNewValue = $("#insertParagraphSelect").attr("value");
		    var valueIdTuple = selectedNewValue.split(" - ");
		    selectedNewValue = valueIdTuple[1];
		    var paraArray = paragraphString.split(",");
		    for(p in paraArray)
		    {
		    	if(paraArray[p]=="")
		    	{
		    		paraArray.splice(p, 1);
		    	}
			}		    		
		    paraArray.unshift(selectedNewValue);
		    paragraphString = paraArray.join(",");
			var pageData = {
				paragraphs:paragraphString
			};
		    var pageIndex = $($contentCache.find( 'id' )[selectedIndex]).text();
			$.fn.loadContent("pages", function(result)
			{
				alert("Absatz eingefuegt.");
				triggerParagraphCreation();
			}, pageData, "data", {edit:true,req:("id="+pageIndex)});
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
		$("#insertParagraphSelect").change(this.insertParagraphHandler);
		$("#paragraphDropDown").change(this.selectParaForSubMenu);
		refreshPages(populateContentDropDown);
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
		
//		alert("url:"+url);
		
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