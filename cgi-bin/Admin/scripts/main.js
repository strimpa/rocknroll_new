﻿// class to deal with content creation
require(['creationTemplates']);

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
		console.log(text);
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
	var excludeFields = ["id", "type", "angelegtVon", "anlegeDatum"];
	var maxCols = 7;
	
	var utils = 
	{
		RenderTableRow : function(row, name, table, types, tableName, ids, renderControls)
		{

			var headRow = document.createElement("tr");
			headRow.setAttribute("id", "title_tr_"+name);
			headRow.setAttribute("class", "title_row");
			table.appendChild(headRow);
			var rowDiv = document.createElement("tr");
			rowDiv.setAttribute("id", "tr_"+name);
			table.appendChild(rowDiv);
			var headCol = document.createElement("td");
			headCol.textContent = "Eintrag bearbeiten";
			headRow.appendChild(headCol);
			var delTd = document.createElement("td");
			delTd.setAttribute("class", "adminTableTd");
			var delRowButton = document.createElement("input");
			delRowButton.setAttribute("type", "button");
			delRowButton.setAttribute("class", "deleteButton");
			delRowButton.setAttribute("value", "X");
			delRowButton.setAttribute("id", row["id"]);
			delTd.appendChild(delRowButton);
			var textRowButton = document.createElement("input");
			textRowButton.setAttribute("type", "button");
			textRowButton.setAttribute("class", "editButton");
			textRowButton.setAttribute("value", "T");
			textRowButton.setAttribute("id", row["id"]);
			delTd.appendChild(textRowButton);
			var markRowCheck = document.createElement("input");
			markRowCheck.setAttribute("type", "checkbox");
			markRowCheck.setAttribute("class", "markRowCheck");
			markRowCheck.setAttribute("id", row["id"]);
			delTd.appendChild(markRowCheck);
			rowDiv.appendChild(delTd);

			$(delRowButton).click(function(evnt)
			{
				if(!confirm("Sicher dass du den Eintrag loeschen moechtest?"))
					return;
				var tabellenliste  = tableName.split(",");
				var idliste  = ids.split(",");
				for(var ti=0; ti<tabellenliste.length; ti++)
				{
					var table = tabellenliste[ti];
					var theID = idliste[ti];
					var delData = {};
					delData[theID] = this.id;
					$.fn.loadContent(table, triggerParagraphCreation, delData, "data", {del:true});
				}
			});
			
			var colCOunt = 0;
			var endColCount = 0;
			var rowCOunt = 0;
			for(colHash in row)
			{
				if($.inArray(colHash, excludeFields)!=-1)
					continue;
					
				if(colCOunt >= maxCols)
				{
					headRow = document.createElement("tr");
					headRow.setAttribute("id", "title_tr_"+name);
					headRow.setAttribute("class", "title_row");
					table.appendChild(headRow);
					rowDiv = document.createElement("tr");
					rowDiv.setAttribute("id", "tr_"+name);
					table.appendChild(rowDiv);
					rowCOunt+=2;
					colCOunt = 0;
				}
			
				var col = row[colHash];
				var headCol = document.createElement("td");
				headCol.textContent = colHash;
				headRow.appendChild(headCol);

				var dataTd = document.createElement("td");
				dataTd.setAttribute("class", "adminTableTd")
				dataTd.setAttribute("id", name);
				rowDiv.appendChild(dataTd);
				
				if(renderControls)
				{
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
						switch(types[colHash])
						{
							case "varchar":
							case "blob":
							case "text":
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
									dateFormat:"yy-mm-dd",
									onClose: function(value, inst) 
									{
										var theId = this.getAttribute("rowId");
										var field = this.getAttribute("field");
										var dateParts = value.split(".");
										var sqlDate = value;
										if(dateParts.length>1)
										{
											sqlDate = dateParts[2]+"-"+dateParts[1]+"-"+dateParts[0];
										}
									    var dbData = eval("({"+field+": \""+sqlDate+"\"})"); 
									    $.fn.loadContent(tableName, null, dbData, "data", {edit:true,req:("id="+theId)});
									}
								});
								$(dataDiv).datepicker( "setDate" , col );
								$(dataDiv).datepicker( "option", "dateFormat", "dd.mm.yy" );
							}
							break;
							case "int":
							case "float":
							{
								var dataDiv = document.createElement("input");
								dataTd.appendChild(dataDiv);
								$(dataDiv).attr("value", col);
								$(dataDiv).attr("rowId", row["id"]);
								$(dataDiv).attr("field", colHash);
								
								$(dataDiv).spinner({
									change: function(ev, ui) 
									{
										var theId = this.getAttribute("rowId");
										var field = this.getAttribute("field");
										var val = $(this).spinner("value");
									    var dbData = eval("({"+field+": \""+val+"\"})"); 
									    $.fn.loadContent(tableName, null, dbData, "data", {edit:true,req:("id="+theId)});
									}
								});
							}
							break;
							case "tinyint":
							{
								var dataDiv = document.createElement("input");
								dataDiv.setAttribute("type", "checkbox");
								dataTd.appendChild(dataDiv);
								if(col=="1")
									$(dataDiv).attr("checked", true);
								$(dataDiv).attr("rowId", row["id"]);
								$(dataDiv).attr("id", colHash + row["id"]);
								$(dataDiv).attr("field", colHash);
								
								$(dataDiv).change(function(ev, ui) 
									{
										var theId = this.getAttribute("rowId");
										var field = this.getAttribute("field");
										var val = this.checked ? "1" : "0";
										console.log("checkbox "+field+" is checked:"+val);
									    var dbData = eval("({"+field+": \""+val+"\"})"); 
									    $.fn.loadContent(tableName, null, dbData, "data", {edit:true,req:("id="+theId)});
									});
							}
							break;
							case "time":
							{
								var dataDiv = document.createElement("input");
								dataTd.appendChild(dataDiv);
								$(dataDiv).attr("value", col);
								$(dataDiv).attr("rowId", row["id"]);
								$(dataDiv).attr("field", colHash);
								
								$(dataDiv).timeEntry({
									show24Hours: true, 
									showSeconds: false,
									spinnerImage: '../../images/jquery_ui/spinnerDefault.png'
								});
									
								$(dataDiv).change(function(e) 
								{
									var theId = this.getAttribute("rowId");
									var field = this.getAttribute("field");
									var value = $(this).attr("value");
								    var dbData = eval("({"+field+": \""+value+"\"})"); 
								    $.fn.loadContent(tableName, null, dbData, "data", {edit:true,req:("id="+theId)});
								});
							}
							break;
						}
					}
				}
				else
				{
					var dataDiv = document.createElement("div");
					dataTd.appendChild(dataDiv);
					dataDiv.innerHTML = col;
				}
				if(endColCount<maxCols)
					endColCount++;
				colCOunt++;
			}
			while(colCOunt<endColCount)
			{
				var dataTd = document.createElement("td");
				dataTd.setAttribute("class", "adminTableTd")
				rowDiv.appendChild(dataTd);
				colCOunt++;
			}
			if(rowCOunt>0)
			{
				delTd.setAttribute("rowspan", rowCOunt+1);
			}
			
			// create plain textgrab 
			$(textRowButton).click(function(){
				console.log("click:"+$("#showPlainTextWindow"));
				
				if(null!=document.getElementById("showPlainTextWindow"))
				{
					console.log("destroying");
					$("#showPlainTextWindow").remove();
				}
				else
				{
					console.log("create");
					var textWin = $("<div id='showPlainTextWindow' ></div>");
					var textArea = $("<textarea id='showPlainTextField' />");
					textWin.append(textArea);
					$(this).parents("table").find("#"+name).each(function(){
						var tdText = $(this).text();
						if(tdText != "Click to edit")
							textArea.append(tdText+"\n")
					});
					$("body").append(textWin);
					textWin.css("top", $(this).offset().top);
					textWin.css("left", $(this).offset().left+30);
				}
			});
//			var eventId = $(this).find("id").text();
			return endColCount+1;
		},
		
		RenderTable : function(tableName, types, requirements, fields, params, ids, doControls)
		{
			if(null==ids)
				ids = "id";
			if(null==doControls)
				doControls = true;
			
			var tableHolder = document.createElement("span");
			var table = document.createElement("table");
			table.setAttribute("class", "adminTable");
			if(params)
				params.json = "results";
			else
				params = {json:"results"};
				
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
						triggerParagraphCreation();
//						document.location = "openFile.php/?url="+result;
					}, null, "xml", {xmlinput:resultString});
				}
				manipIframe();
			});
			
			var suposedTodoControls = doControls;
			$("#progresstitle").text("Loading: "+tableName);
			$.fn.loadContent(tableName, function(result)
			{
				var json = eval(result);
				var colCOunt = 0;
				var rowCounter = 0;
				var objkeys = [];
				for(key in json)
				{
					console.log("key:"+key);
					objkeys.push(key);
				}
				var keyIterator = 0;
				function iterateJson(json, rowHash, table, types, tableName, ids, suposedTodoControls)
				{
					colCOunt = utils.RenderTableRow(json[rowHash], rowHash, table, types, tableName, ids, suposedTodoControls);
					
					rowCounter ++;
					var progressVal = (100.0 * rowCounter); 
					var lengthVal = 1.0 * json.length;
					progressVal = Math.floor(progressVal/lengthVal);
					console.log("progressVal:"+progressVal);
					$("#progressbar").progressbar({value : progressVal});
					$("#progresstitle").text("Loading: "+tableName+": "+progressVal+"%");
//					alert(progressVal);
					setTimeout(iterateJson, 50, json, objkeys[++keyIterator], table, types, tableName, ids, suposedTodoControls);
				}
				if(objkeys.length>0)
					iterateJson(json, objkeys[0], table, types, tableName, ids, suposedTodoControls);
				
				var rowTr = document.createElement("tr");
				var delTd = document.createElement("td");
				delTd.setAttribute("colspan", colCOunt);
				delTd.setAttribute("class", "adminTableTd");
				var delRowsButton = document.createElement("input");
				delRowsButton.setAttribute("type", "button");
				delRowsButton.setAttribute("class", "deleteButton");
				delRowsButton.setAttribute("value", "Loesche alle markierten Zeilen");
				delTd.appendChild(delRowsButton);
				rowTr.appendChild(delTd);
				table.appendChild(rowTr);

				$(delRowsButton).click(function(evnt)
				{
					if(!confirm("Sicher dass du die Eintraege loeschen moechtest?"))
						return;

					var tabellenliste  = tableName.split(",");
					var idliste  = ids.split(",");
					
					$(this).parents(".adminTable").find(".markRowCheck").each(function()
					{
						if($(this).attr("checked"))
						{
							for(var ti=0; ti<tabellenliste.length; ti++)
							{
								var table = tabellenliste[ti];
								var theID = idliste[ti];
								var delData = {};
								delData[theID] = $(this).attr("id");
								$.fn.loadContent(table, null, delData, "data", {del:true});
							}
						}
					});
					triggerParagraphCreation();
						
				});

//				progressbar.remove();

			}, requirements, "xml", params);

			$("#progresstitle").text("Loading: "+tableName+"... DONE!");
			
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
			
			var paratype = paragraphData.find("type").text();
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
			switch(paratype)
			{
			case "0":
			case "1":
				// title
				var textDiv = document.createElement("div");
				textDiv.setAttribute("class", "paragraphContent");
				var contentHtml = $(paragraphData.find("content").children().first());
				$(textDiv).append(contentHtml);
				paraContent = $(textDiv).html();
				paraDiv.appendChild(textDiv);
				break;
			case "2":
				if(metaData['table'])
				{
					var theTable = metaData['table'];
					var theCategory = metaData['category'];
					if(theCategory=="" || null==theCategory)
						$("#errorOutput").append("Absatz "+paraID+" hat keine Kategorie definiert!");
					var tableDiv = document.createElement("div");
					$(tableDiv).css("height", "0px");
					var tableCollapseButton = document.createElement("input");
					tableCollapseButton.setAttribute("type", "button");
					tableCollapseButton.setAttribute("value", "Daten anzeigen");
					$(tableCollapseButton).click(function(evnt)
					{
						if($(this).attr("value")=="Daten anzeigen")
						{
							$(tableDiv).css("height","");
							$(tableDiv).css("overflow","visible");
							$(this).attr("value","Daten verbergen");
						}
						else
						{
							$(tableDiv).css("height", "0px");
							$(tableDiv).css("overflow","hidden");
							$(this).attr("value","Daten anzeigen");
						}
					});
					tableDiv.setAttribute("class", "adminTableDiv");

					var addEntryButton = document.createElement("input");
					addEntryButton.setAttribute("type", "button");
					addEntryButton.setAttribute("value", "Neuer Eintrag");
					tableDiv.appendChild(addEntryButton);
					var delEntriesButton = document.createElement("input");
					delEntriesButton.setAttribute("type", "button");
					delEntriesButton.setAttribute("class", "deleteButton");
					delEntriesButton.setAttribute("value", "Loesche \""+theCategory+"\" Eintraege");
					tableDiv.appendChild(delEntriesButton);

					$(addEntryButton).click(function()
					{
						var data = {category:theCategory,table:theTable};
						editTableTrigger(data);
//						$.fn.loadContent(theTable, triggerParagraphCreation, data, "data", {write:true});
					});
					$(delEntriesButton).click(function()
					{
						if(!confirm("Wollen Sie wirklich ALLE Eintraege aus diesem Absatz aus der Datenbank loeschen?"))
							return;
						var data = {category:theCategory};
						$.fn.loadContent(theTable, triggerParagraphCreation, data, "data", {del:true});
					});
					var abreak = document.createElement("br");
					paraDiv.appendChild(abreak);
					$.fn.loadContent(theTable, function(result)
					{
						var typeJson = eval(result);
						var reqs = null;
						if(theCategory!="")
							reqs = {category:theCategory}
						var table = utils.RenderTable(theTable,typeJson[0],reqs);
						tableDiv.appendChild(table);
					}, null, "data", {def:true,json:"types"});

					paraDiv.appendChild(tableCollapseButton);
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
					type:paratype,
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
				if(!confirm("Sind sie sicher dass Sie den Absatz loeschen moechten?"))
					return;
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
					$.fn.loadContent("paragraphs", null, {id:paraID}, "data", {del:true});
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
		}
		editContentHandler = function()
		{
			var theTitle = $("#pageTitle").attr("value");
			var theID = $("#pagesDropDown").attr("value");
			var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
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
				}, {identifier:theID, menuTitle:$("#pageTitle").attr("value"), articleTitle:articleTitle, priority:menuPriority});
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
				subMenuEntry.innerHTML = entryArray[entryIndex];
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
			for(var c=0;c<element("submenuEntries").children.length;c++)
			{
				entryData.push(element("submenuEntries").children[c].innerHTML);
			}
			var selMenuIndex = $("#submenuEntries").attr("selectedIndex");
			entryData.splice(selMenuIndex, 1);
			currSubMenuUrls.splice(selMenuIndex, 1);
			var data = {entries:entryData.join("|"), links:currSubMenuUrls.join("|")};

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
		
		moveMenuItemHandler = function(e)
		{
			var selMenuIndex = parseInt($("#submenuEntries").attr("selectedIndex"));
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
			var selectedPageIndex = $("#pagesDropDown").attr("selectedIndex")-1;
		    var menuRef = $(contentCache.find( 'menuRef' )[selectedPageIndex]).text();

		    $.fn.loadContent("submenus", createMenuCallback, data, "data", {edit:true,req:("id="+menuRef)});
		}
	
		////////////////////////////////////////////////////////////////////////////////////////
		// Paragraph population
		////////////////////////////////////////////////////////////////////////////////////////
		
		function populateAllParagraphSelect(response)
		{
			$("#insertParagraphSelect").empty();
			$("#errorOutput").find("error").each($(this).remove());

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
		}
		


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
								var data = {category:$("#categorySelect").attr("value")};
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
							pic.attr("class","folderBrowsePic"); 
							pic.attr("src",picUrl);
							pic.css("float", "left");
							picFrame.append(pic);
							picFrame.attr("title", picUrl);

							var picText = $('<textarea />');
							picText.css("height", "100px");
							picText.css("width", "300px");
							$(picFrame).append(picText);

							var picButton = $('<input />');
							picButton.attr("type", "button");
							picButton.css("width", "300px");
							picButton.attr("value", "Update");
							$(picFrame).append(picButton);

							$(picFrame).css("clear", "both");
							$(photoDiv).append(picFrame);
							
							picButton.click(function(){
								console.log($(this).prev().attr("value"));
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
						var titleIDDiv = document.createElement("div");
						titleIDDiv.setAttribute("class", "adminParaTitle");
						titleIDDiv.textContent = "Absatz ID:"+paraArray[paraIndex];
						contentDiv.appendChild(titleIDDiv);
				    	$.fn.loadContent("paragraphs", function(result)
				    	{
				    		try{
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
								    });
								    utils.renderPargraphHTML(myParagraph, $(this), localParaIndex);
								});
							}catch(err)
							{
								$(paraDiv).append("Der Ansatz konnte nicht gerendert werden. Ueberpruefen sie bitte das eingegebene html.");
							}
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
						var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
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
					}
						
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
						var selectedIndex = $("#pagesDropDown").attr("selectedIndex")-1;
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
		$("#upMenuItemButton").click(moveMenuItemHandler);
		$("#downMenuItemButton").click(moveMenuItemHandler);
		$("#errorOutputDelete").click(function(evt){
			$("#errorOutput").empty();
		});
		$("#refreshButton").click(triggerParagraphCreation);
		$("#spinNumTableCols").spinner({
			value: maxCols,
			change: function(ev, ui) 
			{
				var val = $(this).spinner("value");
				maxCols = val;
				console.log("utils.maxCols:"+maxCols);
			}
		});
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

		var cb = function(response)
		{
			try{
				xmlDoc = $.parseXML( response );
				if(null!=xmlDoc)
				{
					console.log("yeah response is xml.");
					$(xmlDoc).find("error").each(function()
				    {
						var anError = document.createElement("div");
						$(anError).append(this);
				    	console.log("an error:"+$(anError).text());
					    $("#errorOutput").append(anError);
					});		
				}
			}
			catch(e)
			{
				console.log("Couldn't parse response to jQuery object.");
			}
			if(null!=callback)
				callback(response);
		};

		$.post(url, data, cb);
//		.success($.fn.loadCallback)
//		.error($.fn.loadCallback)
//		.complete($.fn.loadCallback);
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