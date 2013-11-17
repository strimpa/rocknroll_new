////////////////////////////////////////////////////////////////////////////
/// module to help with rendering 
////////////////////////////////////////////////////////////////////////////

define(['config'], function(config) 
{
	////////////////////////////////////////////////////////////////////////////
	/// Private Helpers 
	////////////////////////////////////////////////////////////////////////////
	
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
	};

	////////////////////////////////////////////////////////////////////////////
	/// module definition 
	////////////////////////////////////////////////////////////////////////////
	var utils = {};
	utils.maxCols = 7;
	
	utils.RenderTableRow = function(row, name, table, types, tableName, ids, renderControls)
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
			if($.inArray(colHash, config.excludeFields)!=-1)
				continue;
				
			if(colCOunt >= utils.maxCols)
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
			dataTd.setAttribute("class", "adminTableTd id_"+name);
//			dataTd.setAttribute("id", name);
			rowDiv.appendChild(dataTd);
			
			if(renderControls)
			{
				if("pic"==colHash)
				{
					var picHolder = new PicBrowse("picUrl", "Verwendetes Bild.");
					var dataDiv = picHolder.createControl(document, {"picUrl":col});
					dataTd.appendChild(dataDiv);
					$(picHolder.control).val( col);
					$(picHolder.control).data("rowId", row["id"]);
					$(picHolder.control).data("field", colHash);
					picHolder.init(null, function(){
						var theId = $(this).data("rowId");
						var field = $(this).data("field");
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
							$(dataDiv).val( col);
							$(dataDiv).data("rowId", row["id"]);
							$(dataDiv).data("field", colHash);
							
							
							$(dataDiv).datepicker({
								dateFormat:"yy-mm-dd",
								onClose: function(value, inst) 
								{
									var theId = $(this).data("rowId");
									var field = $(this).data("field");
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
							$(dataDiv).val( col);
							$(dataDiv).data("rowId", row["id"]);
							$(dataDiv).data("field", colHash);
							
							$(dataDiv).spinner({
								min: 0, max: 500, step:1,
								change: function(ev, ui) 
								{
									var theId = $(this).data("rowId");
									var field = $(this).data("field");
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
								$(dataDiv).prop("checked", true);
							$(dataDiv).data("rowId", row["id"]);
							$(dataDiv).data("id", colHash + row["id"]);
							$(dataDiv).data("field", colHash);
							
							$(dataDiv).change(function(ev, ui) 
								{
									var theId = $(this).data("rowId");
									var field = $(this).data("field");
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
							$(dataDiv).val( col);
							$(dataDiv).data("rowId", row["id"]);
							$(dataDiv).data("field", colHash);
							
							$(dataDiv).timepicker();
								
							$(dataDiv).change(function(e) 
							{
								var theId = $(this).data("rowId");
								var field = $(this).data("field");
								var value = $(this).val();
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
			if(endColCount<utils.maxCols)
				endColCount++;
			colCOunt++;
		}
		while(colCOunt<endColCount)
		{
			var dataTd = document.createElement("td");
			dataTd.setAttribute("class", "adminTableTd");
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
				console.log("create:\""+name+"\"");
				var textWin = $("<div id='showPlainTextWindow' ></div>");
				var textArea = $("<textarea id='showPlainTextField' />");
				textWin.append(textArea);
				$(this).parents("table").find(".id_"+name).each(function(){
					var tdText = $(this).text();
					if(tdText != "Click to edit")
						textArea.append(tdText+"\n");
				});
				$("body").append(textWin);
				textWin.css("top", $(this).offset().top);
				textWin.css("left", $(this).offset().left+30);
			}
		});
//			var eventId = $(this).find("id").text();
		return endColCount+1;
	};
	
	utils.RenderTable = function(tableName, types, requirements, fields, params, ids, doControls)
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

		var adminParaTableHolder = document.createElement("div");
		$(adminParaTableHolder).prop("class", "adminParaTableHolder");
		adminParaTableHolder.appendChild(table);
		tableHolder.appendChild(adminParaTableHolder);
		
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
				objkeys.push(key);
			}
			
			if(objkeys.length==0)
			{
				var td = $("<tr><td></td></tr>");
				td.append("Es wurden keine Datenbank eintraege gefunden mit den Bedingungen:");
				var list = $("<ul></ul>");
				for (var i in requirements) {
				  list.append($("<li>"+i+":"+requirements[i]+"</li>"));
				};
				td.append(list);
				$(table).append(td);
			}
			
			var keyIterator = 0;
			
			function addDelButton()
			{
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
						if($(this).prop("checked"))
						{
							for(var ti=0; ti<tabellenliste.length; ti++)
							{
								var table = tabellenliste[ti];
								var theID = idliste[ti];
								var delData = {};
								delData[theID] = $(this).prop("id");
								$.fn.loadContent(table, null, delData, "data", {del:true});
							}
						}
					});
					triggerParagraphCreation();
						
				});
			}
			function iterateJson(json, rowHash, table, types, tableName, ids, suposedTodoControls)
			{
				colCOunt = utils.RenderTableRow(json[rowHash], rowHash, table, types, tableName, ids, suposedTodoControls);
				
				rowCounter ++;
				var progressVal = (100.0 * rowCounter); 
				var lengthVal = 1.0 * json.length;
				progressVal = Math.floor(progressVal/lengthVal);
				$("#progressbar").progressbar({value : progressVal});
				$("#progresstitle").text("Loading: "+tableName+": "+progressVal+"%");
				var nextKey = objkeys[++keyIterator];
				if(undefined!=nextKey)
					setTimeout(iterateJson, 50, json, nextKey, table, types, tableName, ids, suposedTodoControls);
				else
					addDelButton();
			}
			if(objkeys.length>0)
				iterateJson(json, objkeys[0], table, types, tableName, ids, suposedTodoControls);

//				progressbar.remove();

		}, requirements, "xml", params);

		$("#progresstitle").text("Loading: "+tableName+"... DONE!");
		
		return tableHolder;
	};
	
	utils.renderPargraphHTML = function(paraDiv, paragraphData, paraIndex)
	{
		metaData = interpreteMetaData(paragraphData.find("meta").text());

		paraDiv.setAttribute("class", "adminParagraph");
		var paraID = paragraphData.find("id").text();

		//paraDiv.style.height = metaData['height'] + "px";
//			heightobj.offset += parseInt(metaData['height']);
		// title
		var editDiv = document.createElement("fieldset");
		editDiv.setAttribute("class", "ParaEditGroupBox");
		editDiv.style.textAlign = "right";
		var editLabel = document.createElement("legend");
		editLabel.innerText = "Bearbeiten";
		editDiv.appendChild(editLabel);
		var editParaButton = document.createElement("input");
		editParaButton.setAttribute("type", "image");
		editParaButton.setAttribute("title", "Bearbeiten");
		editParaButton.setAttribute("src", "images/081 Pen.png");
		editParaButton.setAttribute("class", "editButton");
		editParaButton.setAttribute("disabled", "true");
		editDiv.appendChild(editParaButton);
		var deleteParaButton = document.createElement("input");
		deleteParaButton.setAttribute("type", "image");
		deleteParaButton.setAttribute("src", "images/023 Document Delete.png");
		deleteParaButton.setAttribute("title", "Loeschen "+paraID);
		deleteParaButton.setAttribute("class", "deleteButton");
		editDiv.appendChild(deleteParaButton);
		var upParaButton = document.createElement("input");
		upParaButton.setAttribute("type", "image");
		upParaButton.setAttribute("src", "images/037 ArrowUp.png");
		upParaButton.setAttribute("title", "Absatz nach oben bewegen");
		upParaButton.setAttribute("class", "editButton");
		editDiv.appendChild(upParaButton);
		var downParaButton = document.createElement("input");
		downParaButton.setAttribute("type", "image");
		downParaButton.setAttribute("src", "images/038 ArrowDown.png");
		downParaButton.setAttribute("title", "Absatz nach unten bewegen");
		downParaButton.setAttribute("class", "editButton");
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
		if(null==metaData['image'])
		{
			editParaButton.removeAttribute("disabled");
		}
		switch(paratype)
		{
		case "0":
		case "1":
			// title
			var contentHolder = document.createElement("div");
			$(contentHolder).css("height", "0px");
			$(contentHolder).css("overflow", "hidden");

			// image
			if(null!=metaData['image'])
			{
				var imgDiv = document.createElement("div");
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
				contentHolder.appendChild(imgDiv);
			}

			var textDiv = document.createElement("div");
			textDiv.setAttribute("class", "paragraphContent");
			var contentHtml = $(paragraphData.find("content").first());	
			xmlString = $('<div>').append(contentHtml).html();
			$(textDiv).html(xmlString);
			paraContent = xmlString;
			contentHolder.appendChild(textDiv);

			var tableCollapseButton = document.createElement("input");
			tableCollapseButton.setAttribute("type", "button");
			tableCollapseButton.setAttribute("value", "Daten anzeigen");
			$(tableCollapseButton).click(function(evnt)
			{
				if($(this).val()=="Daten anzeigen")
				{
					$(contentHolder).css("height","");
					$(contentHolder).css("overflow","visible");
					$(this).val("Daten verbergen");
				}
				else
				{
					$(contentHolder).css("height", "0px");
					$(contentHolder).css("overflow","hidden");
					$(this).val("Daten anzeigen");
				}
			});

			paraDiv.appendChild(tableCollapseButton);
			paraDiv.appendChild(contentHolder);

			break;
		case "2":
			if(metaData['table'])
			{
				var theTable = metaData['table'];
				var theCategory = metaData['category'];
				var toSortBy = metaData['sortBy'] || "";
				if(theCategory=="" || null==theCategory)
					$("#errorOutput").append("Absatz "+paraID+" hat keine Kategorie definiert!");
				var tableDiv = document.createElement("div");
				$(tableDiv).css("height", "0px");
				
				var tableCollapseButton = document.createElement("input");
				tableCollapseButton.setAttribute("type", "button");
				tableCollapseButton.setAttribute("value", "Daten anzeigen");
				$(tableCollapseButton).click(function(evnt)
				{
					if($(this).val()=="Daten anzeigen")
					{
						$(tableDiv).css("height","");
						$(tableDiv).css("overflow","visible");
						$(this).val("Daten verbergen");

						if($(tableDiv).children("[name='content']").length == 0)
						{
							$.fn.loadContent(theTable, function(result)
							{
								var typeJson = eval(result);
								var reqs = null;
								var params = null;
								if(theCategory!="")
									reqs = {category:theCategory};
								if(toSortBy!=undefined)
									params = {orderBy:toSortBy};
								
								var table = utils.RenderTable(theTable,typeJson[0],reqs, null, params);
								table.setAttribute("name", "content");
								tableDiv.appendChild(table);
							}, null, "data", {def:true,json:"types"});
						}
					}
					else
					{
						$(tableDiv).css("height", "0px");
						$(tableDiv).css("overflow","hidden");
						$(this).val("Daten anzeigen");
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
		var toSortBy = metaData['sortBy'];
		
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
				sortBy:toSortBy
			};
			createParagraphHandler(defaultData, paraID, picID);
		});
		$(deleteParaButton).click(function()
		{
			if(!confirm("Sind sie sicher dass Sie den Absatz loeschen moechten?"))
				return;
			var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
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
			var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
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
			var selectedIndex = $("#pagesDropDown").prop("selectedIndex")-1;
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
	};
	
	return utils;
});