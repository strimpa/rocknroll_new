// class to deal with content creation

// globals
var currContent = null;
var contentCache = null;
var currSubMenuUrls = null;

require.config({
	waitSeconds:15
});

jQuery = require([
		'lib/jquery-1.9.1',
		'lib/jquery-ui-1.10.3.custom.min'
	],
	function(jQuery)
	{
		require([
		'utils',
		'creationTemplates',
		'lib/jquery.ui.timepicker',
		'lib/jquery.jeditable.mini',
		'tiny_mce/jquery.tinymce'
		], 
		function(utils)
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
				console.log(text);
			}
		
			/////////////////////////////////////////////////////////////////////////
			// view
			/////////////////////////////////////////////////////////////////////////
			
			$.view = (function(){
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
				    contentCache.find( 'identifier' ).each(function(index, value)
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
					var matches = $(this).prop("class").match(/listboxRowSelected/);
					if(null==matches)
						$(this).prop("class", "listboxRow");
				}
				function listboxEntryMouseOver()
				{
					var matches = $(this).prop("class").match(/listboxRowSelected/);
					if(null==matches)
						$(this).prop("class", "listboxRow listboxRowHover");
				}
				function listboxEntryClick()
				{
					var index = $(this).data("listboxIndex");
					var matches = $(this).prop("class").match(/listboxRowSelected/);
					if(null!=matches)
					{
						$("#submenuEntries").prop("selectedIndex", -1);
						$(this).prop("class", "listboxRow listboxRowHover");
					}
					else
					{
						$("#submenuEntries").children().each(function(){
							$(this).prop("class", "listboxRow");
						});
						$(this).prop("class", "listboxRow listboxRowSelected");
		//				output("selected index:"+index+", "+currSubMenuUrls[index]);
						$("#submenuEntries").prop("selectedIndex", index);
					}
		
					element("paragraphDropDown").selectedIndex = 0;
					$("#paragraphDropDown").find("option").each(function()
					{
						var index = $("#submenuEntries").prop("selectedIndex");
						var url = currSubMenuUrls[index];
						if($(this).text()==url)	
							element("paragraphDropDown").value = url;
					});
				};
		
				////////////////////////////////////////////////////////////////////////////////////////
				// ContentPage population
				////////////////////////////////////////////////////////////////////////////////////////
		
				selectContentHandler = function()
				{
					$("#pageTitle").val( "");
					var selection = $("#pagesDropDown").val();
					output("Selected Content page: "+selection);
					var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
					output("Selected index:"+selectedIndex);
					
					//Title
					var myIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
					$.fn.loadContent("navigation", function(result)
					{
						$("#pageTitle").val( $(result).find( 'title' ).first().text());
						$("#menuPriority").val( $(result).find( 'priority' ).first().text());
					}, {"pageRef":myIndex}, "xml");
		
					// trigger submenu creation			
				    var menuRef = $(contentCache.find( 'menuRef' )[selectedIndex]).text();
					output("Sub menu reference: "+menuRef);
				    $.fn.loadContent("submenus", populateSubMenuItems, {"id":menuRef}, "xml");
				    
					// trigger paragraph creation
				    triggerParagraphCreation();
				};
				
				function refreshPages(callback)
				{
					$.fn.loadContent("pages", function(result)
					{
						contentCache = $(result);
						callback();
					}, null, "xml");
				}
				function contentEditHandler(result)
				{
					refreshPages(populateContentDropDown);
				}
				
				createContentHandler = function()
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
						pageData['title'] = data['articleTitle'];
						$.fn.loadContent("pages", function(result)
						{
							if(pageData['identifier'])
							{
								var thePageRef = result;
								var naviData = {title:menuTitle, pageRef:thePageRef, priority:menuPriority};
								$.fn.loadContent("navigation", contentEditHandler, naviData, "data", {write:true});
							}
							else
								contentEditHandler(result);
						}, pageData, "data", {write:true});
					});
				};
				editContentHandler = function()
				{
					var theTitle = $("#pageTitle").val();
					var theID = $("#pagesDropDown").val();
					var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
					var pageId = $(contentCache.find( 'id' )[selectedIndex]).text();
					var articleTitle = $(contentCache.find( 'title' )[selectedIndex]).text();
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
							var pageData = {identifier:data['identifier']};
							pageData['identifier'] = data['identifier'].replace(pattern, "_"); 
							pageData['title'] = data['articleTitle'];
							$.fn.loadContent("pages", function(result)
							{
								// if existent - create, otherwise - delete
		//						alert("menuTitle:"+data['menuTitle']);
								if(menuTitle!="")
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
									$.fn.loadContent("navigation", contentEditHandler, naviDelData, "data", {del:true});
								}
							}, pageData, "data", {edit:true, req:reqString});
						}, {identifier:theID, menuTitle:$("#pageTitle").val(), articleTitle:articleTitle, priority:menuPriority});
					}, {pageRef:pageId}, "data");
				};
				deleteContentHandler = function()
				{
					var confirmation = confirm("Sind Sie sicher dass Sie den Inhalt loeschen wollen?");
					if(!confirmation)
						return;
					var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
			    	var pageId = $(contentCache.find( 'id' )[selectedIndex]).text();
					var data = {id:pageId};
					var navidata = {pageRef:pageId};
					$.fn.loadContent("pages", function(result)
					{
						$.fn.loadContent("navigation", contentEditHandler, navidata, "data", {del:true});
					}, data, "data", {del:true});
				};
		
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
						subMenuEntry.innerHTML = entryArray[entryIndex];
						$(subMenuEntry).data("listboxIndex", entryIndex);
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
					var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
				    var menuRef = $(contentCache.find( 'menuRef' )[selectedIndex]).text();
					output("Sub menu reference: "+menuRef);
				    $.fn.loadContent("submenus", populateSubMenuItems, {"id":menuRef}, "xml");
				}
				createMenuEntryHandler = function()
				{
					if(null==contentCache)
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
						var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
					    var menuRef = $(contentCache.find( 'menuRef' )[selectedIndex]).text();
		
					    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
					}, null, presetOptions);
				};
				editMenuEntryHandler = function()
				{
					if(null==contentCache)
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
					var index = $("#submenuEntries").prop("selectedIndex");
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
						var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
					    var menuRef = $(contentCache.find( 'menuRef' )[selectedIndex]).text();
		
					    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
					}, presetValues, presetOptions);
				};
				selectParaForSubMenu = function()
				{
					if(null == contentCache)
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
					var entryIndex = $("#submenuEntries").prop("selectedIndex");
					urls[entryIndex] = $("#paragraphDropDown").val();
					var data = {entries:entryData.join("|"), links:urls.join("|")};
		
					// Get menu id to update
					var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
				    var menuRef = $(contentCache.find( 'menuRef' )[selectedIndex]).text();
					output(entryIndex+", "+urls[entryIndex]+", "+menuRef);
		
				    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
				};
			
				deleteMenuEntryHandler = function()
				{
					var confirmation = confirm("Sind Sie sicher dass Sie den Menueintrag loeschen wollen?");
					if(!confirmation)
						return;
					
					// prepare data
					/////////////////////////
					var entryData = [];
					for(var c=0;c<element("submenuEntries").children.length;c++)
					{
						entryData.push(element("submenuEntries").children[c].innerHTML);
					}
					var selMenuIndex = $("#submenuEntries").prop("selectedIndex");
					entryData.splice(selMenuIndex, 1);
					currSubMenuUrls.splice(selMenuIndex, 1);
					var data = {entries:entryData.join("|"), links:currSubMenuUrls.join("|")};
		
					// Get menu id to update
					var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
				    var menuRef = $(contentCache.find( 'menuRef' )[selectedIndex]).text();
		
				    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
				};
				
				selectSubMenuItemHandler = function(e)
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
				};
				
				moveMenuItemHandler = function(e)
				{
					var selMenuIndex = parseInt($("#submenuEntries").prop("selectedIndex"));
					if(selMenuIndex<=0)
					{
						return;
					}
		
					// prepare data
					/////////////////////////
					var entryData = [];
					var urls = [];
					var selectedEntry = null;
					var selectedUrl = currSubMenuUrls[selMenuIndex];
					currSubMenuUrls.splice(selMenuIndex, 1);
					for(var c=0;c<element("submenuEntries").children.length;c++)
					{
						if(c==selMenuIndex)
							selectedEntry = element("submenuEntries").children[c].innerHTML;
						else
							entryData.push(element("submenuEntries").children[c].innerHTML);
					}
					if(this.id=="upMenuItemButton")
					{
						entryData.splice(selMenuIndex-1, 0, selectedEntry);
						currSubMenuUrls.splice(selMenuIndex-1, 0, selectedUrl);
					}
					else
					{
						entryData.splice(selMenuIndex+1, 0, selectedEntry);
						currSubMenuUrls.splice(selMenuIndex+1, 0, selectedUrl);
					}
		
					var data = {entries:entryData.join("|"), links:currSubMenuUrls.join("|")};
		
					// Get menu id to update
					var selectedPageIndex = $("#pagesDropDown").prop("selectedIndex")-1;
				    var menuRef = $(contentCache.find( 'menuRef' )[selectedPageIndex]).text();
		
				    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
				};
			
				////////////////////////////////////////////////////////////////////////////////////////
				// Paragraph population
				////////////////////////////////////////////////////////////////////////////////////////
				
				function populateAllParagraphSelect(response)
				{
					$("#insertParagraphSelect").empty();
					$("#errorOutput").find("error").each($(this).remove);
		
					optn = document.createElement("OPTION");
				    $("#insertParagraphSelect").append(optn);
				    //output(response);
					$(response).find("row").children().each(function()
				    {
				    	var id = $(this).find( 'id').text();
				    	var title = $(this).find( 'title').text();
						optn = document.createElement("OPTION");
						optn.textContent = title + " - " + id;
					    $("#insertParagraphSelect").append(optn);
					});		
				}
				
				triggerParagraphCreation = function()
				{
					refreshPages(populateParagraphs);
				};
				
				editTableTrigger = function (defaultData)
				{
					var theTable = defaultData['table'];
					var enterData = function(data)
					{
						var theId = defaultData['id'];
						if(data['newCategory']!="")
							data['category'] = data['newCategory'];
						data.newCategory = null;	
						if(null!=theId)
							$.fn.loadContent(theTable, triggerParagraphCreation, data, "data", {edit:true,req:("id="+theId)});
						else
							$.fn.loadContent(theTable, triggerParagraphCreation, data, "data", {write:true});
					};
					
					var theDialog = EventTableEntryDialog;
					switch(theTable)
					{
						case "archive":
							theDialog = ArchivTableEntryDialog;
							break;
					}
					theDialog.createDialog(document, function()
					{
						var data = {}; 
						theDialog.getData(data);
						enterData(data);
					}, defaultData);
				};
				
		
		
				function populateParagraphs()
				{
					$("#admincontent").empty();
					$("#paragraphDropDown").empty();
		
					optn = document.createElement("OPTION");
				    $("#paragraphDropDown").append(optn);
					var contentDiv = document.createElement("p");
					contentDiv.setAttribute("class", "contentDiv");
					var progressbar = $("<div id='progressbar'></div>");
					$(contentDiv).prepend("<div id='progresstitle'></div>"); 
					$(contentDiv).prepend(progressbar);
					progressbar.progressbar({value:0,color: "green"});
		
					$("#admincontent").append(contentDiv);
					
		//			var heightobj = new Object();
		//			heightobj.offset = 0;
					var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
					var selectedvalue = $("#pagesDropDown").val();
					
					switch(selectedvalue)
					{
					case "links":
						{
							$.fn.loadContent("links", function(result)
							{
								var typeJson = eval(result);
								$.fn.loadContent("links", function(result)
								{
									var sections = eval(result);
				
									var addEntryButton = document.createElement("input");
									addEntryButton.setAttribute("type", "button");
									addEntryButton.setAttribute("value", "Neuer Eintrag");
									contentDiv.appendChild(addEntryButton);
				
									var abreak = document.createElement("br");
									contentDiv.appendChild(abreak);
									var label = document.createTextNode("Zeige Kategorie:");
									contentDiv.appendChild(label);
									
									var categorySelect = document.createElement("select");
									categorySelect.setAttribute("id", "categorySelect");
									contentDiv.appendChild(categorySelect);
									for(sectionIndex in sections)
									{
										section = sections[sectionIndex];
										var sectionOption = document.createElement("option");
										sectionOption.textContent = section.category;
										categorySelect.appendChild(sectionOption);
									}
		
									$(addEntryButton).click(function()
									{
										var data = {category:$("#categorySelect").val()};
										$.fn.loadContent("links", triggerParagraphCreation, data, "data", {write:true});
									});
		
									var tableDiv = document.createElement("div");
									tableDiv.setAttribute("class", "adminTableDiv");
									contentDiv.appendChild(tableDiv);
									
									var categoryFill = function(section)
									{
										$(tableDiv).empty();
										var paraDiv = document.createElement("div");
										paraDiv.setAttribute("id", "paragraph_"+section);
										paraDiv.setAttribute("class", "adminParagraph");
										paraDiv.appendChild(utils.RenderTable("links", typeJson[0], {category:section}, null));
										tableDiv.appendChild(paraDiv);
									};
									$(categorySelect).change(function(){
										categoryFill(this.value);
									});
									categoryFill(sections[0].category);
									
								}, null, "data", {selector:"category",distinct:true,json:"sections"});
							}, null, "data", {def:true,json:"types"});
						}
						break;
					case "approve":
						{
							$.fn.loadContent("links", function(result)
							{
								var typeJson = eval(result);
								var paraDiv = document.createElement("div");
								paraDiv.setAttribute("id", "paragraph_order");
								paraDiv.setAttribute("class", "adminParagraph");
								paraDiv.appendChild(utils.RenderTable("links", typeJson[0], {approved:0}));
								contentDiv.appendChild(paraDiv);
							}, null, "data", {def:true,json:"types"});
							$.fn.loadContent("events", function(result)
							{
								var typeJson = eval(result);
								var paraDiv = document.createElement("div");
								paraDiv.setAttribute("id", "paragraph_order");
								paraDiv.setAttribute("class", "adminParagraph");
								paraDiv.appendChild(utils.RenderTable("events", typeJson[0], {approved:0}));
								contentDiv.appendChild(paraDiv);
							}, null, "data", {def:true,json:"types"});
						}
						break;
					case "bestellen":
						{
							$.fn.loadContent("bestellung,kunden", function(result)
							{
								var typeJson = eval(result);
								var paraDiv = document.createElement("div");
								paraDiv.setAttribute("id", "paragraph_order");
								paraDiv.setAttribute("class", "adminParagraph");
								paraDiv.appendChild(utils.RenderTable("bestellung,kunden", typeJson[0], null, null, {joinFields:"kundenID=id"}, "kundenID,id", false));
								contentDiv.appendChild(paraDiv);
							}, null, "data", {def:true,json:"types",joinFields:"kundenID=id"});
						}
						break;
					case "galerie":
						{
							var paraDiv = document.createElement("div");
							paraDiv.setAttribute("id", "paragraph_order");
							paraDiv.setAttribute("class", "adminParagraph");
							var folderSelect = document.createElement("select");
							$(folderSelect).append("<option>Suche Ordner aus Liste aus...</option>");
							var photoDiv = document.createElement("div");
							var photoArray = new Array();
							$.fn.loadContent("folder", function(result)
							{
								$(result).find("row").children().children().each(function(){
									var folderName = this.nodeName;
									console.log("folderName:"+folderName);
									var pics = [];
									$(this).children().each(function(){
										var picture = $(this).text();
										console.log("\tpic:"+picture);
										pics.push(picture);
									});
									photoArray[folderName] = pics;
									$(folderSelect).append($("<option>"+folderName+"</option>"));
								});
							}, {"assetFolder":"MiniGal/photos"}, "xml", {recursive:true});
							$(folderSelect).change(function(){
								$(photoDiv).empty();
								var folder = this.value;
								console.log("selectied folder:"+folder);
								for(d in photoArray[folder])
								{
									var picFrame = $('<div class="folderBrowserPicFrame">'); 
									var pic = $('<img />');
									var picUrl = photoArray[folder][d];
									pic.prop("class","folderBrowsePic"); 
									pic.prop("src",picUrl);
									pic.css("float", "left");
									picFrame.append(pic);
									picFrame.prop("title", picUrl);
		
									var picText = $('<textarea />');
									picText.css("height", "100px");
									picText.css("width", "300px");
									$(picFrame).append(picText);
		
									var picButton = $('<input />');
									picButton.prop("type", "button");
									picButton.css("width", "300px");
									picButton.val( "Update");
									$(picFrame).append(picButton);
		
									$(picFrame).css("clear", "both");
									$(photoDiv).append(picFrame);
									
									picButton.click(function(){
										console.log($(this).prev().val());
									});
								}
							});
							paraDiv.appendChild(folderSelect);
							paraDiv.appendChild(photoDiv);
							contentDiv.appendChild(paraDiv);
						}
						break;
					default:
						{
							console.log("selectedIndex:"+selectedIndex);
						    var paragraphs = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
						    var paraArray = paragraphs.split(",");
				
						    for(paraIndex in paraArray)
						    {
						    	if(paraArray[paraIndex]=="" || paraArray[paraIndex]==null)
						    		continue;
								var paraDiv = document.createElement("div");
								paraDiv.setAttribute("id", "paragraph_"+paraArray[paraIndex]);
								// var titleIDDiv = document.createElement("div");
								// titleIDDiv.setAttribute("class", "adminParaTitle");
								// titleIDDiv.textContent = "Absatz ID:"+paraArray[paraIndex];
								// contentDiv.appendChild(titleIDDiv);
						    	$.fn.loadContent("paragraphs", function(result)
						    	{
						    		//try{
							    		result = getXmlDocFromResponse(result);
										$(result).find("row").each(function()
									    {
										    // paragraph itself
										    var myParagraphId = $(this).find("id").first().text();
										    var myParagraph = element("paragraph_"+myParagraphId);
										    if(null==myParagraph)
										    {
										    	throw("Can't resolve holding paragraph HTML node.");
										    	return;
										    }
			
					//						alert("localParaIndex:"+$(this).find('id').text());
											var localParaIndex = paraArray.indexOf($(this).find('id').text());
					//				    	output("id:"+$(this).find('id').text());
									    	$(this).find('title').each(function(index, value)
										    {
												optn = document.createElement("OPTION");
												optn.textContent = $(this).text();
											    $("#paragraphDropDown").append(optn);
										    });
										    utils.renderPargraphHTML(myParagraph, $(this), localParaIndex);
										});
									// }catch(err)
									// {
										// $(paraDiv).append("Der Ansatz konnte nicht gerendert werden. Ueberpruefen sie bitte das eingegebene html. Error:"+err+"<br />");
									// }
						    	}, {id:paraArray[paraIndex]}, "data");
								
								// append
								contentDiv.appendChild(paraDiv);
						    }
		
						    $.fn.loadContent("paragraphs", populateAllParagraphSelect, null, "xml");
						}
					}
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
				
				createParagraphHandler = function(defaultData, paraID, picID)
				{
					if(null==contentCache)
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
								type:ParaTypeStrings.indexOf(data['type']),
								content:data['content'],
								meta:metaString
							};
							var paraParams = {write:true};
							if(paraID!=null)
								paraParams = {edit:true,req:"id="+paraID};
								
							var enterParagraph = function(result)
							{
								var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
								var paragraphString = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
								var paraArray = paragraphString.split(",");
								var pageIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
								var lastParaIndex = parseInt(result);
								console.log("result:"+lastParaIndex+" currentpargraphs:");
								for(d in paraArray)
									console.log("#"+paraArray[d]);
								if(!isNaN(lastParaIndex) && paraArray.indexOf(lastParaIndex)==-1)
								{
									console.log("adding");
									paraArray.push(lastParaIndex);
								}
								paragraphString = paraArray.join(",");
								var pageData = {
									paragraphs:paragraphString
								};
								$.fn.loadContent("pages", function(result)
								{
									output("Content successfully created.");
									triggerParagraphCreation();
								}, pageData, "data", {edit:true,req:("id="+pageIndex)});
							};
								
							// Text and picture. Insert pic.
							if(data['picUrl']!="")
							{
								// try to find pic in database
								var picReadData = {"url":data['picUrl']};
								$.fn.loadContent("pictures", function(result)
								{
									// no picture found: insert!
									var foundID = $(result).find("id");
									if(foundID.length==0)	
									{
										console.log("Did NOT find picture ID");
										var picWriteData = {"url":data['picUrl'],"title":data['picTitle']};
										var picWriteParams = {write:true};
										$.fn.loadContent("pictures", function(result)
										{
											console.log("NEW picture ID :"+result);
											var lastPicIndex = result;
											metaString += ";image="+lastPicIndex;
											paraData.meta = metaString;
											$.fn.loadContent("paragraphs", enterParagraph, paraData, "data", paraParams);
										}, picWriteData, "data", picWriteParams);
									}
									else // picture found, just insert into paragraphs
									{
										var idString = foundID.first().text();
										console.log("found picture ID :"+idString);
										var metaString = "height="+data['height']+";image="+idString;
										paraData.meta = metaString;
										$.fn.loadContent("paragraphs", enterParagraph, paraData, "data", paraParams);							
									}
								}, picReadData, "xml");
							}
							else // NO picture entered
							{
								$.fn.loadContent("paragraphs", enterParagraph, paraData, "data", paraParams);
		
							}
						}
						else
						{
							// Tabelle creation
							var category = ""!=data['newCategory'] ? data['newCategory'] : data['category'];
							var metaString = "height="+data['height']+";table="+data['table']+";category="+category;
							if("sortBy" in data)
							{
								console.log("sor by:"+data['sortBy']);
								metaString += ";sortBy="+data['sortBy'];
							}
							var paraData = {
								title:data['title'],
								type:ParaTypeStrings.indexOf(data['type']),
								content:data['content'],
								meta:metaString
							};
							var paraParams = {write:true};
							if(paraID!=null)
								paraParams = {edit:true,req:"id="+paraID};
							$.fn.loadContent("paragraphs", function(result)
							{
								var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
							    var paragraphString = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
							    var pageIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
								var lastParaIndex = result;
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
				};
						
				insertParagraphHandler = function(defaultData, paraID, picID)
				{
					if(null==$("#paragraphDropDown").find( 'OPTION' ))
					{
						alert("Bitte waehlen Sie einen Artikel zum editieren!");
						return;
					}
					if(null==defaultData)
						defaultData = {};
					var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
				    var paragraphString = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
				    var selectedNewValue = $("#insertParagraphSelect").val();
				    var valueIdTuple = selectedNewValue.split("- ");
				    selectedNewValue = valueIdTuple[1];
				    var paraArray = paragraphString.split(",");
				    for(p in paraArray)
				    {
				    	if(paraArray[p]=="")
				    	{
				    		paraArray.splice(p, 1);
				    	}
					}		    		
				    paraArray.push(selectedNewValue);
				    paragraphString = paraArray.join(",");
					var pageData = {
						paragraphs:paragraphString
					};
				    var pageIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
					$.fn.loadContent("pages", function(result)
					{
						alert("Absatz eingefuegt.");
						triggerParagraphCreation();
					}, pageData, "data", {edit:true,req:("id="+pageIndex)});
				};
				
				////////////////////////////////////////////////////////////////////////////////////////
				// Entry point
				////////////////////////////////////////////////////////////////////////////////////////
				$("#pagesDropDown").change(selectContentHandler);
				$("#createPageButton").click(createContentHandler);
				$("#editPageButton").click(editContentHandler);
				$("#deletePageButton").click(deleteContentHandler);
				$("#createMenuEntryButton").click(createMenuEntryHandler);
				$("#editMenuEntryButton").click(editMenuEntryHandler);
				$("#deleteMenuEntryButton").click(deleteMenuEntryHandler);
				$("#createParagraphButton").click(createParagraphHandler);
				$("#insertParagraphSelect").change(insertParagraphHandler);
				$("#paragraphDropDown").change(selectParaForSubMenu);
				$("#upMenuItemButton").click(moveMenuItemHandler);
				$("#downMenuItemButton").click(moveMenuItemHandler);
				$("#errorOutputDelete").click(function(evt){
					$("#errorOutput").empty();
				});
				$("#refreshButton").click(triggerParagraphCreation);
				$("#spinNumTableCols").spinner({
					value: utils.maxCols,
					change: function(ev, ui) 
					{
						var val = $(this).spinner("value");
						utils.maxCols = val;
						console.log("utils.maxCols:"+utils.maxCols);
					}
				});
				refreshPages(populateContentDropDown);
			});
		
			/////////////////////////////////////////////////////////////////////////
			// Per instance functions
			/////////////////////////////////////////////////////////////////////////
			$.fn.loadCallback = function(e)
			{
		/*		output("success:"+e.success());
				var s = e.success();
				if(null!=s)
					for(d in s){output("e:"+s[d])};
		*/	};
			
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
		
				var cb = function(response)
				{
					if(undefined!=params && !("json" in params))
					{
						try{
							xmlDoc = $.parseXML( response );
							if(null!=xmlDoc)
							{
								$(xmlDoc).find("error").each(function()
							    {
									var anError = document.createElement("div");
									$(anError).append(this);
							    	console.log("an error:"+$(anError).text());
								    $("#errorOutput").append(anError);
								});		
							}
							else
							{
								console.log("No xml document given back.");
							}
						}
						catch(e)
						{
							console.log("Error parsing response to jQuery object:"+e);
						}
					}
					if(null!=callback)
						callback(response);
				};
		
				$.post(url, data, cb);
		//		.success($.fn.loadCallback)
		//		.error($.fn.loadCallback)
		//		.complete($.fn.loadCallback);
			};
			$.fn.Init = function() 
			{
				$.view();
			};
	
		// entry point
		$(document).ready(function()
		{
		    $.fn.Init();
		});
		
		return jQuery;
	});
});