// class to deal with content creation

(function($)
{
	var currCallback = undefined;
	var ajax = undefined;
	var busyLoading = false;
	var loadingQueue = new Array();
	var REST_WRITE = 1, REST_DELETE = 2, REST_EDIT = 3;

	var currContent = null;
	var contentCache = null;
	var currSubMenuUrls = null;

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

	/////////////////////////////////////////////////////////////////////////
	// view
	/////////////////////////////////////////////////////////////////////////
	
	var utils = 
	{
		RenderTable : function(tableName, types, requirements, fields, params)
		{
			var tableHolder = document.createElement("span");
			var table = document.createElement("table");
			table.setAttribute("class", "adminTable");
			var colTypes = types[0]; 
			var headRow = document.createElement("tr");
			for(headIndex in colTypes)
			{
				var headCol = document.createElement("th");
				headCol.textContent = headIndex;
				headRow.appendChild(headCol);
			}
			table.appendChild(headRow);
			if(params)
				params.json = true;
			else
				params = {json:true};
				
			//
			var downloadButton = document.createElement("input");
			downloadButton.setAttribute("type", "button");
			downloadButton.setAttribute("value", "download as xml");
			tableHolder.appendChild(downloadButton);

			var uniqueName =  "uploadTarget_"+Math.random()*1000;
			var uploadTarget = document.createElement("iframe");
			uploadTarget.setAttribute("id", uniqueName);
			uploadTarget.setAttribute("class", "uploadTarget");
			uploadTarget.setAttribute("name", uniqueName);
			uploadTarget.setAttribute("align", "right");
			tableHolder.appendChild(uploadTarget);
			var uploadForm = document.createElement("form");
			uploadForm.setAttribute("action", "fileupload.php");
			uploadForm.setAttribute("method", "POST");
			uploadForm.setAttribute("id", "uploadForm");
			uploadForm.setAttribute("target", uniqueName);
			uploadForm.setAttribute("enctype", "multipart/form-data");
			var uploadLink = document.createElement("input");
			uploadLink.setAttribute("type", "file");
			uploadLink.setAttribute("value", "XML hochladen");
			uploadLink.setAttribute("id", "uploadedfile");
			uploadLink.setAttribute("name", "uploadedfile");
			uploadLink.setAttribute("align", "right");
			uploadForm.appendChild(uploadLink);
			tableHolder.appendChild(uploadForm);

			tableHolder.appendChild(table);
			
			$(downloadButton).click(function(result){
				console.log("download!");
				$.fn.loadContent(tableName,function(result){
//					console.log(result);
					document.location = "openFile.php/?url="+result;
				}, requirements, "xml", {xmloutput:true});
			});
			$(uploadLink).change(function(){
				uploadForm.submit();
			});
			$(uploadTarget).load(function() {
				function manipIframe() {
				  	console.log("polling...");
				    el = $(uploadTarget).contents();
				    if (el.length != 1) {
				    	setTimeout(manipIframe, 100);
				     	return;
				    }
					var resultString = $(uploadTarget).contents().text();
					if(""==resultString)
						return;
					console.log("resultString:\""+resultString+"\"");
					var lastSlash = resultString.lastIndexOf("/");
					resultString = resultString.substring(lastSlash+1, resultString.length);
					console.log("uploaded file:"+resultString);
					$.fn.loadContent(tableName,function(result){
						console.log(result);
//						document.location = "openFile.php/?url="+result;
					}, null, "xml", {xmlinput:resultString});
				}
				manipIframe();
			});
				
			$.fn.loadContent(tableName, function(result)
			{
				var json = eval(result);
				for(rowHash in json)
				{
					var rowDiv = document.createElement("tr");
					rowDiv.setAttribute("id", "tr_"+rowHash);
					var row = json[rowHash];
					for(colHash in row)
					{
						var col = row[colHash];
						var dataTd = document.createElement("td");
						dataTd.setAttribute("class", "adminTableTd")
						rowDiv.appendChild(dataTd);
						
						if("pic"==colHash)
						{
							var picHolder = new PicBrowse("picUrl", "Verwendetes Bild.");
							var dataDiv = picHolder.createControl(document, {"picUrl":col});
							dataTd.appendChild(dataDiv);
							$(picHolder.control).attr("value", col);
							$(picHolder.control).attr("rowId", row["id"]);
							$(picHolder.control).attr("field", colHash);
							picHolder.init(null, function(){
								var theId = this.getAttribute("rowId");
								var field = this.getAttribute("field");
								var value = this.src;
								console.log("result of dummy search:"+value.search(/noImageDummy/));
								if(-1!=value.search(/noImageDummy/))
									value = "";
							    var dbData = eval("({"+field+": \""+value+"\"})"); 
							    $.fn.loadContent(tableName, null, dbData, "data", {edit:true,req:("id="+theId)});
							});
						}
						else
						{
							switch(colTypes[colHash])
							{
								case "string":
								case "blob":
								{
									var dataDiv = document.createElement("div");
									$(dataDiv).html(col);
									dataTd.appendChild(dataDiv);
								
									$(dataDiv).editable(function(value, settings) { 
									    var dbData = eval("({"+settings.submitdata.field+": \""+value+"\"})"); 
									    $.fn.loadContent(tableName, null, dbData, "data", {edit:true,req:("id="+settings.submitdata.id)});
									    return(value);
									}, { 
									    // type    : 'textarea',
										// submit  : 'OK',
										submitdata: {id: row["id"], field:colHash},
									});
								}
								break;
								case "date":
								{
									var dataDiv = document.createElement("input");
									dataTd.appendChild(dataDiv);
									$(dataDiv).attr("value", col);
									$(dataDiv).attr("rowId", row["id"]);
									$(dataDiv).attr("field", colHash);
									
									$(dataDiv).datepicker({
										dateFormat:'yy-mm-dd',
										onClose: function(value, inst) 
										{
											var theId = this.getAttribute("rowId");
											var field = this.getAttribute("field");
										    var dbData = eval("({"+field+": \""+value+"\"})"); 
										    $.fn.loadContent(tableName, null, dbData, "data", {edit:true,req:("id="+theId)});
										}
									});
								}
								break;
							}
						}
					}
					table.appendChild(rowDiv);
					var eventId = $(this).find("id").text();
				}
			}, requirements, "xml", params);
			
			return tableHolder;
		},
		
		renderPargraphHTML : function(paraDiv, paragraphData, paraIndex)
		{
			output(paragraphData.find("meta").text());
			metaData = interpreteMetaData(paragraphData.find("meta").text());

			paraDiv.setAttribute("class", "adminParagraph");
			var paraID = paragraphData.find("id").text();
			//paraDiv.style.height = metaData['height'] + "px";
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
			deleteParaButton.setAttribute("value", "delete "+paraID);
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
					var theCategory = metaData['category'];
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
					$.fn.loadContent(theTable, function(result)
					{
						var typeJson = eval(result);
						var table = utils.RenderTable(theTable,typeJson,{category:theCategory});
						tableDiv.appendChild(table);
					}, null, "data", {def:true,json:"types"});

					paraDiv.appendChild(tableDiv);
				}
				break;
			default:
				break;
			}
			
			var myHeight = metaData['height'];
			var whichTable = metaData['table'];
			var myCategory = metaData['category'];
			
			// clck handlers
			$(editParaButton).click(function()
			{
				var defaultData = {
					title:titleDiv.textContent,
					type:type,
					height:myHeight,
					picUrl:imageUrl,
					picTitle:imageTitle,
					content:paraContent,
					table:whichTable,
					category:myCategory,
				};
				createParagraphHandler(defaultData, paraID, picID);
			});
			$(deleteParaButton).click(function()
			{
				var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			    var paragraphString = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
			    var pageIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
			    var paraArray = paragraphString.split(",");
			    paraArray.splice(paraIndex, 1);
			    for(p in paraArray)
			    	if(paraArray[p]=="")
			    		paraArray.splice(p, 1);
				var pageData = {
					paragraphs:paraArray.join(",")
				};
//				alert("paraindex:"+paraIndex+", new para string:"+paraArray.join(","));
				
				var keep = confirm("Den Absatz in der DB behalten?");
				if(!keep)
				{
					$.fn.loadContent("paragraphs", function(result)
					{
						alert("Absatz erfolgreich geloescht.");
					}, {id:paraID}, "data", {del:true});
				}
				$.fn.loadContent("pages", function(result)
				{
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
			    var paragraphString = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
			    var pageIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
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
			    var paragraphString = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
			    var pageIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
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
	}
	
	/////////////////////////////////////////////////////////////////////////
	// view
	/////////////////////////////////////////////////////////////////////////
	
	$.view = function()
	{
		
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

		selectContentHandler = function()
		{
			$("#pageTitle").attr("value", "");
			var selection = $("#pagesDropDown").attr("value");
			output("Selected Content page: "+selection);
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			output("Selected index:"+selectedIndex);
			
			//Title
			var myIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
			$.fn.loadContent("navigation", function(result)
			{
				$("#pageTitle").attr("value", $(result).find( 'title' ).first().text());
				$("#menuPriority").attr("value", $(result).find( 'priority' ).first().text());
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
		editContentHandler = function()
		{
			var theTitle = $("#pageTitle").attr("value");
			var theID = $("#pagesDropDown").attr("value");
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			var pageId = $(contentCache.find( 'id' )[selectedIndex]).text();
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
							$.fn.loadContent("navigation", contentEditHandler, naviDelData, "data", {del:true});
						}
					}, pageData, "data", {edit:true, req:reqString});
				}, {identifier:theID, menuTitle:$("#pageTitle").attr("value"), priority:menuPriority});
			}, {pageRef:pageId}, "data");
		}
		deleteContentHandler = function()
		{
			var confirmation = confirm("Sind Sie sicher dass Sie den Inhalt loeschen wollen?");
			if(!confirmation)
				return;
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
	    	var pageId = $(contentCache.find( 'id' )[selectedIndex]).text();
			var data = {id:pageId};
			var navidata = {pageRef:pageId};
			$.fn.loadContent("pages", function(result)
			{
				$.fn.loadContent("navigation", contentEditHandler, navidata, "data", {del:true});
			}, data, "data", {del:true});
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
				var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			    var menuRef = $(contentCache.find( 'menuRef' )[selectedIndex]).text();

			    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
			}, null, presetOptions);
		}
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
			    var menuRef = $(contentCache.find( 'menuRef' )[selectedIndex]).text();

			    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
			}, presetValues, presetOptions);
		}
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
			var entryIndex = $("#submenuEntries").attr("selectedIndex");
			urls[entryIndex] = $("#paragraphDropDown").attr("value");
			var data = {entries:entryData.join("|"), links:urls.join("|")};

			// Get menu id to update
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
		    var menuRef = $(contentCache.find( 'menuRef' )[selectedIndex]).text();
			output(entryIndex+", "+urls[entryIndex]+", "+menuRef);

		    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
		}
	
		deleteMenuEntryHandler = function()
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
		    var menuRef = $(contentCache.find( 'menuRef' )[selectedIndex]).text();

		    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
		}
		
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
		
		triggerParagraphCreation = function()
		{
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
			refreshPages(function(result)
			{
			    populateParagraphs();
			});
		};
		
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
			var selectedvalue = $("#pagesDropDown").attr("value");
			
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
							var tableDiv = document.createElement("div");
							tableDiv.setAttribute("class", "adminTableDiv");
		
							var addEntryButton = document.createElement("input");
							addEntryButton.setAttribute("type", "button");
							addEntryButton.setAttribute("value", "Neuer Eintrag");
							tableDiv.appendChild(addEntryButton);
		
							$(addEntryButton).click(function()
							{
								editTableTrigger({table:"links"});
							});
							contentDiv.appendChild(tableDiv);
							
							for(sectionIndex in sections)
							{
								section = sections[sectionIndex];
								var paraDiv = document.createElement("div");
								paraDiv.setAttribute("id", "paragraph_"+section.rubrik);
								paraDiv.setAttribute("class", "adminParagraph");
								paraDiv.appendChild(utils.RenderTable("links", typeJson, {rubrik:section.rubrik}, null));
								contentDiv.appendChild(paraDiv);
							}
						}, null, "data", {selector:"rubrik",distinct:true,json:"sections"});
					}, null, "data", {def:true,json:"types"});
				}
				break;
			case "order":
				{
					$.fn.loadContent("bestellung,kunden", function(result)
					{
						var typeJson = eval(result);
						var paraDiv = document.createElement("div");
						paraDiv.setAttribute("id", "paragraph_order");
						paraDiv.setAttribute("class", "adminParagraph");
						paraDiv.appendChild(utils.RenderTable("bestellung,kunden", typeJson, null, null, {joinFields:"nachNameBesteller=nachname"}));
						contentDiv.appendChild(paraDiv);
					}, null, "data", {def:true,json:"types",joinFields:"nachNameBesteller=nachname"});
				}
				break;
			default:
				{
				    var paragraphs = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
				    var paraArray = paragraphs.split(",");
		
					console.log("paraArray.count"+paraArray.length);
				    for(paraIndex in paraArray)
				    {
				    	console.log("paraArray["+paraIndex+"]: "+paraArray[paraIndex]);
				    	if(paraArray[paraIndex]=="" || paraArray[paraIndex]==null)
				    		continue;
				    	console.log("is valid paraArray[paraIndex]");
						var paraDiv = document.createElement("div");
						paraDiv.setAttribute("id", "paragraph_"+paraArray[paraIndex]);
				    	$.fn.loadContent("paragraphs", function(result)
				    	{
				    		result = getXmlDocFromResponse(result);
								$(result).find("row").each(function()
						    {
							    // paragraph itself
							    var myParagraphId = $(this).find("id").first().text();
							    var myParagraph = element("paragraph_"+myParagraphId);
							    if(null==myParagraph)
							    	return;
							    console.log("myParagraphId: "+myParagraphId+", "+myParagraph);

		//						alert("localParaIndex:"+$(this).find('id').text());
								var localParaIndex = paraArray.indexOf($(this).find('id').text());
		//				    	output("id:"+$(this).find('id').text());
						    	$(this).find('title').each(function(index, value)
							    {
									optn = document.createElement("OPTION");
									optn.textContent = $(this).text();
								    $("#paragraphDropDown").append(optn);
							    })
							    
							    utils.renderPargraphHTML(myParagraph, $(this), localParaIndex);
							});
				    	}, {id:paraArray[paraIndex]}, "data");
							contentDiv.appendChild(paraDiv);
				    }

				    $.fn.loadContent("paragraphs", populateAllParagraphSelect, null, "xml");
				}
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
									    var paragraphString = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
									    var pageIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
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
								    var paragraphString = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
								    var pageIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
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
						    var paragraphString = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
						    var pageIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
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
					    var paragraphString = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
					    var pageIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
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
				
		insertParagraphHandler = function(defaultData, paraID, picID)
		{
			if(null==$("#paragraphDropDown").find( 'OPTION' ))
			{
				alert("Bitte waehlen Sie einen Artikel zum editieren!");
				return;
			}
			if(null==defaultData)
				defaultData = {};
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
		    var paragraphString = $(contentCache.find( 'paragraphs' )[selectedIndex]).text();
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
		    var pageIndex = $(contentCache.find( 'id' )[selectedIndex]).text();
			$.fn.loadContent("pages", function(result)
			{
				alert("Absatz eingefuegt.");
				triggerParagraphCreation();
			}, pageData, "data", {edit:true,req:("id="+pageIndex)});
		}
		
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
