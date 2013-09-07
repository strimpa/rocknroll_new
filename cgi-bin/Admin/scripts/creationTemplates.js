//var DataDescription =
 
//{
//	controlType:null,
//	dataType:null
//}

	function showImageBrowser(position, rootFolder, targetPicElem)
	{
		var holder = $('<div id="folderBrowser">');
		holder.css("left", position.left);
		holder.css("top", position.top);
		var closeBar = $('<a><div id="closeBar">schliessen<img src="../../images/layout/closeButton.png" align="right" /></div></a>');
		holder.append(closeBar);
		$("body").append(holder);
		var picHolder = $('<div id="folderBrowserPicHolder">'); 
		holder.append(picHolder);
		$.fn.loadContent("folder", function(result)
		{
			$(result).find("row").children().children().each(function(){
				var picFrame = $('<div class="folderBrowserPicFrame">'); 
				var pic = $('<img />');
				pic.attr("class","folderBrowsePic"); 
				pic.attr("src",$(this).text()); 
				picFrame.append(pic);
				picFrame.append($("<i>"+$(this).text()+"</i>"));
				picHolder.append(picFrame);
				pic.click(function(){
					console.log("targetPicElem:"+targetPicElem);
					$(targetPicElem).attr("src", $(this).attr("src"));
					holder.remove();
				});
			});
//			$("#menuPriority").attr("value", $(result).find( 'priority' ).first().text());
		}, {"assetFolder":rootFolder}, "xml");
		$(closeBar).click(function(){
			$("#folderBrowser").remove();
		});
	}
	
	function destroyImageBrwoser()
	{
		var holder = $("#folderBrowser");
		if(null!=holder)
			holder.remove();
	}

/********************************************************************************************
 *  Controls
 ********************************************************************************************/
var DataEntity = function(n, h, d)
{
	this.name = n;
	this.helpString = h;
	this.staticPresetData = d;
	this.control = null;
};
DataEntity.prototype.createControl = function(doc, presetValues)
{
	return this.control;
}
DataEntity.prototype.init = function(presetValues)
{
}
DataEntity.prototype.create = function(doc, presetValues, presetOptions)
{
	var controlDiv = doc.createElement("div");
	controlDiv.setAttribute("class", "controlDiv");
	var label = doc.createElement("div");
	label.setAttribute("class", "creationLabel");
	label.innerHTML = this.helpString;
	controlDiv.appendChild(label);
	
	var theControl = this.createControl(doc, presetValues, presetOptions);
	if(null!=theControl)
		controlDiv.appendChild(theControl);
	
	var helpIcon = doc.createElement("img");
	helpIcon.setAttribute("class", "helpIcon");
	helpIcon.setAttribute("src", "../../images/layout/question_mark.png");
	controlDiv.setAttribute("title", this.helpString);
	controlDiv.appendChild(helpIcon);
	
	return controlDiv;
}
DataEntity.prototype.destroy = function()
{
};

////////////////////////////////////////////////////////////////////////////////////////
var TextField = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
}
TextField.prototype = new DataEntity;
TextField.prototype.createControl = function(doc, presetValues)
{
	this.control =  doc.createElement("input");
	this.control.setAttribute("type", "text");
	this.control.setAttribute("id", this.name+"_edit");
	this.control.setAttribute("class", "creationControl");
	if(null!=presetValues && null!=presetValues[this.name])
	{
		this.control.value = presetValues[this.name];
	}
	return this.control;
}

TextField.prototype.getData = function(data)
{
	data[this.name] = this.control.value;
}
////////////////////////////////////////////////////////////////////////////////////////
var TextArea = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
}
TextArea.prototype = new DataEntity;
TextArea.prototype.createControl = function(doc, presetValues)
{
	this.control =  doc.createElement("textarea");
	this.control.setAttribute("id", this.name+"_edit");
	this.control.setAttribute("class", "creationControl");
	if(null!=presetValues && null!=presetValues[this.name])
	{
		this.control.value = presetValues[this.name];
	}
	return this.control;
}

TextArea.prototype.getData = function(data)
{
	data[this.name] = this.control.value;
}
////////////////////////////////////////////////////////////////////////////////////////
var ComboBox = function(n, h, d, returnIndex)
{
	this.returnIndex = returnIndex;
	// contructor
	DataEntity.call(this, n, h, d);
}
ComboBox.prototype = new DataEntity;
ComboBox.prototype.createControl = function(doc, presetValues, presetOptions)
{
	this.control =  doc.createElement("select");
	this.control.setAttribute("id", this.name+"_edit");
	this.control.setAttribute("class", "creationControl");
	if(null!=presetOptions && null!=presetOptions[this.name])
		this.staticPresetData = presetOptions[this.name];
	if(null!=this.staticPresetData)
	{
		var entries = this.staticPresetData.split("|");
		for(e in entries)
		{
			optn = document.createElement("OPTION");
			optn.textContent = entries[e];
			this.control.appendChild(optn);
		}
	}
	if(null!=presetValues && null!=presetValues[this.name])
	{
		this.control.selectedIndex = presetValues[this.name];
	}
	return this.control;
}

ComboBox.prototype.getData = function(data)
{
	if(this.returnIndex)
		data[this.name] = this.control.selectedIndex;
	else
		data[this.name] = this.control.value;
}

////////////////////////////////////////////////////////////////////////////////////////
var Spinner = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
}
Spinner.prototype = new DataEntity;
Spinner.prototype.createControl = function(doc, presetValues)
{
	this.control =  doc.createElement("input");
	this.control.setAttribute("type", "text");
	var myId = this.name+"_edit";
	this.control.setAttribute("id", myId);
	this.control.setAttribute("class", "creationControl");
	this.control.setAttribute("value","");
	if(null!=presetValues && null!=presetValues[this.name])
	{
		this.control.setAttribute("value", presetValues[this.name]);
	}
	return this.control;
}
Spinner.prototype.init = function(presetValues)
{
	$(this.control).spinner();
	if(null!=presetValues && null!=presetValues[this.name])
	{
		$(this.control).spinner("value", presetValues[this.name]);
	}
	else if(null!=this.staticPresetData)
	{
		$(this.control).spinner("value", this.staticPresetData);
	}
	else if(this.control.getAttribute("value")=="")
		$(this.control).spinner("value", 300);
}

Spinner.prototype.getData = function(data)
{
	data[this.name] = this.control.value;
}
////////////////////////////////////////////////////////////////////////////////////////
var DateField = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
}
DateField.prototype = new DataEntity;
DateField.prototype.createControl = function(doc, presetValues)
{
	this.control =  doc.createElement("input");
	this.control.setAttribute("type", "text");
	var myId = this.name+"_edit";
	this.control.setAttribute("id", myId);
	this.control.setAttribute("class", "creationControl");
	this.control.setAttribute("value","");
	if(null!=presetValues && null!=presetValues[this.name])
	{
		this.control.setAttribute("value", presetValues[this.name]);
	}
	return this.control;
}
DateField.prototype.init = function()
{
	$(this.control).datepicker({dateFormat:'yy-mm-dd'});
	$(this.control).datepicker( "setDate", "0" );
//	if(this.control.getAttribute("value")=="")
//		$(this.control).datepicker("value", 300);
}

DateField.prototype.getData = function(data)
{
	data[this.name] = this.control.value;
}
////////////////////////////////////////////////////////////////////////////////////////
var TimeField = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
}
TimeField.prototype = new DataEntity;
TimeField.prototype.createControl = function(doc, presetValues)
{
	this.control =  doc.createElement("input");
	this.control.setAttribute("type", "text");
	var myId = this.name+"_edit";
	this.control.setAttribute("id", myId);
	this.control.setAttribute("class", "creationControl");
	this.control.setAttribute("value","");
	if(null!=presetValues && null!=presetValues[this.name])
	{
		this.control.setAttribute("value", presetValues[this.name]);
	}
	else
		this.control.setAttribute("value", "19:00:00");

	return this.control;
}
TimeField.prototype.init = function()
{
	$(this.control).timeEntry({show24Hours: true, showSeconds: true});
}

TimeField.prototype.getData = function(data)
{
	data[this.name] = this.control.value;
}

////////////////////////////////////////////////////////////////////////////////////////
var WYSIWYGField = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
}
WYSIWYGField.prototype = new DataEntity;
WYSIWYGField.prototype.createControl = function(doc, presetValues)
{
//	if(null!=$('textarea:tinymce'))
//		$('textarea:tinymce').execCommand('mceRemoveControl',false, (this.name+"_edit") );
//	if(this.control == null)
	{
		this.control = doc.createElement("textarea");
	}
	var myId = this.name+"_edit";
	this.control.setAttribute("id", myId);
	return this.control;
}
function createMce(on)
{
	on.tinymce({
		// Location of TinyMCE script
		script_url : '../../script/tiny_mce/tiny_mce.js',

		// General options
		theme : "advanced",

		// Theme options
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

/*
		// Example content CSS (should be your site CSS)
		content_css : "../../css/mainStyles.css",

		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",

		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		},
*/
	});
}
WYSIWYGField.prototype.init = function(presetValues)
{
	var thevalue = "";
	if(null!=presetValues && null!=presetValues[this.name])
	{
		thevalue = presetValues[this.name];
	}
	else if(null!=this.staticPresetData)
	{
		thevalue = this.staticPresetData;
	}
	$(this.control).css("visibility", "visible");
	createMce($(this.control));

	//this.control.setAttribute("value", thevalue);
	//$(selector).tinymce().setContent(thevalue);
	$(this.control).html(thevalue);
}

WYSIWYGField.prototype.getData = function(data)
{
//	alert($(this.control).tinymce().getContent());
	var wrapper = $("<div><div /></div>");
	wrapper.children().first().append($(this.control).tinymce().getContent());
	data[this.name] = wrapper.html();
//	alert(data[this.name]);
}
WYSIWYGField.prototype.destroy = function(presetValues)
{
//	$(this.control).tinymce().removeControl();
//	$("#"+this.name+"_edit_parent").remove();
//	alert("destroy editor");
}

////////////////////////////////////////////////////////////////////////////////////////
var PicBrowse = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
}
PicBrowse.prototype = new DataEntity;
PicBrowse.prototype.createControl = function(doc, presetValues)
{
	var holder =  doc.createElement("div");
	holder.setAttribute("class", "picBrowseHolder");

	var numberOfTargets = $('[id*="uploadTarget"]').size();
	var uniqueName =  "uploadTarget_"+numberOfTargets;
	this.uploadTarget = doc.createElement("iframe");
	this.uploadTarget.setAttribute("id", uniqueName);
	this.uploadTarget.setAttribute("class", "uploadTarget");
	this.uploadTarget.setAttribute("name", uniqueName);
	this.uploadTarget.setAttribute("align", "right");
	
	this.uploadForm = doc.createElement("form");
	this.uploadForm.setAttribute("action", "fileupload.php");
	this.uploadForm.setAttribute("method", "POST");
	this.uploadForm.setAttribute("id", "uploadForm");
	this.uploadForm.setAttribute("target", uniqueName);
	this.uploadForm.setAttribute("enctype", "multipart/form-data");
	holder.appendChild(this.uploadForm);
	
	this.uploadLink = doc.createElement("input");
	this.uploadLink.setAttribute("type", "file");
	this.uploadLink.setAttribute("id", "uploadedfile");
	this.uploadLink.setAttribute("name", "uploadedfile");
	this.uploadLink.setAttribute("align", "right");
	this.uploadForm.appendChild(this.uploadLink);

	// var uploadSubmit = doc.createElement("input");
	// uploadSubmit.setAttribute("type", "submit");
	// uploadSubmit.setAttribute("id", "uploadSubmit");
	// uploadSubmit.setAttribute("value", "Neue Datei hochladen");
	// uploadForm.appendChild(uploadSubmit);

	holder.appendChild(this.uploadTarget);
	// holder.innerHTML += "<br />";
	
	this.control =  doc.createElement("img");
	var myId = this.name+"_edit";
	this.control.setAttribute("id", myId);
	this.control.setAttribute("class", "creationControl");
	this.control.setAttribute("src","images/noImageDummy.png");
	if(null!=presetValues && null!=presetValues[this.name])
	{
		this.control.setAttribute("src", presetValues[this.name]);
	}
	holder.appendChild(this.control);

	this.picDelete = doc.createElement("input");
	this.picDelete.setAttribute("type", "button");
	this.picDelete.setAttribute("id", "picDelete");
	this.picDelete.setAttribute("value", "Bild entfernen");
	holder.appendChild(this.picDelete);
	
	this.browseLink = doc.createElement("input");
	this.browseLink.setAttribute("type", "button");
	this.browseLink.setAttribute("id", "browseLink");
	this.browseLink.setAttribute("value", "Vorhandene Bilder durchsuchen");
	holder.appendChild(this.browseLink);

	return holder;
}
PicBrowse.prototype.init = function(presetValues, changeHandler)
{
	var controlScopePass = this.control; 
	$(this.control).load(changeHandler);
	
	$(this.picDelete).click(function(){
		$(controlScopePass).attr("src", "images/noImageDummy.png");
	});

	var picElement = this.control;
	$(this.browseLink).click(function(){
		showImageBrowser($(this).offset(), "images", picElement);
	});
	
	var picForm = this.uploadForm;
	$(this.uploadLink).change(function(){
		picForm.submit();
	});
	
//	var oDoc = (this.uploadTarget.contentWindow || this.uploadTarget.contentDocument);
	var target = this.uploadTarget;
	$(this.uploadTarget).load(function() {
		var controlScopePass1 = controlScopePass;
		//var target = this;
		function manipIframe() {
		  	console.log("polling...");
		    el = $(target).contents();
		    if (el.length != 1) {
		    	setTimeout(manipIframe, 100);
		     	return;
		    }
			var resultString = $(target).contents().text();
			if(resultString.length>0)
			{
				if(resultString.search(/Invalid file/) == -1)
				{
					console.log("New src:"+resultString);
					$(controlScopePass1).attr("src", resultString);
				}
				else
				{
					alert(resultString);
				}
			}
		}
		manipIframe();
	});
}

PicBrowse.prototype.getData = function(data)
{
	if($(this.control).attr("src").indexOf("noImageDummy")!=-1)
	{
		data[this.name] = ""; 
		return;
	}
	
	data[this.name] = $(this.control).attr("src");
}

////////////////////////////////////////////////////////////////////////////////////////
/********************************************************************************************
 *  Group
 ********************************************************************************************/

var Group = function(n, h) 
{
	// contructor
	DataEntity.call(this, n, h);
	this.controls = [];
};
Group.prototype = new DataEntity;
Group.prototype.init = function(presetValues)
{
	for(d in this.controls)
	{
		this.controls[d].init(presetValues);
	}
};
Group.prototype.create = function(doc, presetValues, presetOptions)
{
	this.control = doc.createElement("fieldset");
	this.control.setAttribute("id", this.name+"_edit");
	var boxLegend = doc.createElement("legend");
	boxLegend.innerHTML = this.helpString;
	this.control.appendChild(boxLegend);
	for(d in this.controls)
	{
		this.control.appendChild(this.controls[d].create(doc, presetValues, presetOptions));
	}
	
	return this.control;
};

Group.prototype.getData = function(data)
{
	for(d in this.controls)
	{
		this.controls[d].getData(data);
	}
};
Group.prototype.destroy = function()
{
	for(d in this.controls)
	{
		this.controls[d].destroy();
	}
};

/********************************************************************************************
 *  EditDialog
 ********************************************************************************************/
var EditDialog = function(n, h) 
{
	// contructor
	Group.call(this, n, h);
};
EditDialog.prototype = new Group;
EditDialog.prototype.createDialog = function(doc, callback, presetValues, presetOptions)
{
	this.closeMe = function()
	{
		destroyImageBrwoser();
		dialogInst.destroy();
		removeDialog();
	};
	this.confirm = function()
	{
		callback();
		dialogInst.closeMe();
	};

	var bg = doc.createElement("div");
	bg.setAttribute("id","creationBg");
	var holder = doc.createElement("div");
	holder.setAttribute("class","creationBox");

	var childControls = this.create(doc, presetValues, presetOptions);
	holder.appendChild(childControls);

	var createButton = doc.createElement("input");
	createButton.setAttribute("type", "button");
	createButton.setAttribute("style", "float:right;");
	createButton.setAttribute("value", "Create");
	holder.appendChild(createButton);
	$(createButton).click(this.confirm);
	
	var cancelButton = doc.createElement("input");
	cancelButton.setAttribute("type", "button");
	cancelButton.setAttribute("style", "float:left;");
	cancelButton.setAttribute("value", "Cancel");
	holder.appendChild(cancelButton);
	var dialogInst = this;
	$(cancelButton).click(this.closeMe);

	$("body").append(bg);
	$("body").append(holder);
	var newLeft = $(window).width()/2 - parseInt($(".creationBox").css("width"))/2;
	var newTop = 20;//$(window).height()/2 - parseInt($(".creationBox").css("height"))/2;
	$(".creationBox").css("left", newLeft);
	$(".creationBox").css("top", newTop);
	
	//Group.prototype.init.call(this, presetValues);
	this.init(presetValues);
};
function removeDialog()
{
	$("#creationBg").remove();
	$(".creationBox").remove();
};

/********************************************************************************************
 *  Uses
 ********************************************************************************************/

var PageCreationDialog = new EditDialog("PageCreationDialog", "Neuen Inhalt einfuegen");
PageCreationDialog.controls = new Array(
		new TextField("identifier", 	"Der eindeutige identifier fuer die URL."),
		new TextField("menuTitle", 		"Der Titel im Menu falls gewuenscht."),
		new TextField("articleTitle", 	"Der Titel des Artikels."),
		new Spinner("priority", 		"Die Reihenfolge im Menu.", 0)
);
////////////////////////////////////////////////////////////////////////////////////////////////////////
var SubMenuCreationDialog = new EditDialog("SubMenuCreationDialog", "Neuen Menueintrag einfuegen");
SubMenuCreationDialog.controls = new Array(
		new TextField("title", "Der Titel der im Menu angezeigt wird."),
		new ComboBox("url",  "Der Textanker-link auf das der Eintrag verweist.")
);
////////////////////////////////////////////////////////////////////////////////////////////////////////
var ParagraphCreationDialog = new EditDialog("ParagraphCreationDialog", "Neuen Absatz erstellen");
var PicTextGroup = new Group("picTextGroup", "Absatz spezifische Inhalte");
PicTextGroup.controls = new Array(
		new PicBrowse("picUrl", "Verwendetes Bild."),
		new TextField("picTitle", "Untertitel des verwendeten Bildes."),
		new WYSIWYGField("content",  "Der Inhaltstext.")
);
var TableGroup = new Group("tableGroup", "Absatz spezifische Inhalte");
TableGroup.controls = new Array(
		new ComboBox("table", "Die verwendete Tabelle.", "events|links|archive"),
		new ComboBox("category",  "Kategorie oder Monat."),
		new TextField("newCategory", "Neue Kategorie.")
);
var ParaTypeStrings = ["Text mit Bild rechts","Text mit Bild links","Tabelle"];
ParagraphCreationDialog.controls = new Array(
		new TextField("title", "Der Titel des Absatzes."),
		new ComboBox("type",  "Typ des verwendeten Layouts.", ParaTypeStrings.join("|")),
		new Spinner("height", "Hoehe des Absatz."),
		PicTextGroup,
		TableGroup
);
function toggleGroup(event, index)
{
	if(null==index)
		index = $("#type_edit").attr("selectedIndex"); 
	console.log("para type index:"+index);
	if(index==2)
	{
		$("#picTextGroup_edit").css("visibility", "hidden");
		$("#tableGroup_edit").css("visibility", "visible");
	}
	else
	{
		$("#picTextGroup_edit").css("visibility", "visible");
		$("#tableGroup_edit").css("visibility", "hidden");
	}
}
function getCategoryCallback(result)
{
	$(TableGroup.controls[1].control).empty();

	optn = document.createElement("OPTION");
	optn.textContent = "";
	$(TableGroup.controls[1].control).append(optn);
	
	$(result).find("category").each(function(){
		optn = document.createElement("OPTION");
		optn.textContent = $(this).text();
		$(TableGroup.controls[1].control).append(optn);
	});
}
ParagraphCreationDialog.init = function(presetValues)
{
	$(this.controls[1].control).change(toggleGroup);
	Group.prototype.init.call(this, presetValues);
	var index = presetValues['type'];
	toggleGroup(null, index);
};
TableGroup.init = function(presetValues)
{
	this.control.setAttribute("style", "visibility:hidden;");
	$(this.controls[0].control).change(function(){
		var table = $(this).attr("value");
		$.fn.loadContent(table, getCategoryCallback, null, "data", {selector:"category",distinct:true});
	});
	Group.prototype.init.call(this, presetValues);
	var defaultVal = "events";
	if(null!=presetValues['table'])
		 defaultVal = presetValues['table'];
	var gGroupContext = this.controls[1].control;
	var gNewCatContext = this.controls[2].control;
	$.fn.loadContent(defaultVal, function(result){
		getCategoryCallback(result);
		// If entries contain a categrory like this or not
		if(null!=presetValues['category'])
		{
			var cat = presetValues['category']; 
			console.log("category:"+cat);
			if(null!=$(gGroupContext).text().match(cat))
			{
				$(gGroupContext).attr("value", cat);
			}
			else
			{
				$(gNewCatContext).attr("value", cat);
			}
		}
		
	}, null, "data", {selector:"category",distinct:true});
}
////////////////////////////////////////////////////////////////////////////////////////////////////////
var EventTableEntryDialog = new EditDialog("EventTableEntryDialog", "In Tabelle einfuegen.");
EventTableEntryDialog.controls = new Array(
		new ComboBox("category",  "Die Kategorie des Eintrages."),
		new TextField("newCategory",  "Neue Kategorie erstellen. Ueberschreibt obige Auswahl."),
		new TextField("title", "Der Titel des Eintrages."),
		new TextField("artists", "Die Kuenstler die an dem Event teilnehmen."),
		new PicBrowse("pic", "Event Bild"),
		new DateField("date", "Das Datum des Events."),
		new TextField("description", "Beschreibung des Events."),
		new TextField("location", "Ort des Events."),
		new TextField("venue", "Veranstaltungsraum."),
		new TimeField("time", "Anfangszeit des Events."),
		new TextField("url", "Website mit info bezueglich des Events."),
		new TextField("mail", "E-mail fuer mehr informationen.")
);
function getEventCategoryCallback(result)
{
	$(EventTableEntryDialog.controls[0].control).empty();

	optn = document.createElement("OPTION");
	optn.textContent = "";
	$(EventTableEntryDialog.controls[0].control).append(optn);
	
	$(result).find("category").each(function(){
		optn = document.createElement("OPTION");
		optn.textContent = $(this).text();
		$(EventTableEntryDialog.controls[0].control).append(optn);
	});
	$(EventTableEntryDialog.controls[0].control).attr("value", $(EventTableEntryDialog.controls[0].control).attr("initVal"));
}
EventTableEntryDialog.init = function(initData)
{
	$(EventTableEntryDialog.controls[0].control).attr("initVal", initData['category']);
	$.fn.loadContent(initData['table'], getEventCategoryCallback, null, "data", {selector:"category",distinct:true});
	Group.prototype.init.call(this, initData);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////
var ArchivTableEntryDialog = new EditDialog("EventTableEntryDialog", "In Tabelle einfuegen.");
ArchivTableEntryDialog.controls = new Array(
		new ComboBox("category",  "Die Kategorie des Eintrages."),
		new TextField("newCategory",  "Neue Kategorie erstellen (KLEINBUCHSTABEN!). Ueberschreibt obige Auswahl."),
		new Spinner("issue", "Die Ausgabenummer."),
		new PicBrowse("pic", "Cover"),
		new TextField("content", "Inhalt des Heftes.")
);
function getArchivCategoryCallback(result)
{
	$(ArchivTableEntryDialog.controls[0].control).empty();

	optn = document.createElement("OPTION");
	optn.textContent = "";
	$(ArchivTableEntryDialog.controls[0].control).append(optn);
	
	$(result).find("category").each(function(){
		optn = document.createElement("OPTION");
		optn.textContent = $(this).text();
		$(ArchivTableEntryDialog.controls[0].control).append(optn);
	});
	$(ArchivTableEntryDialog.controls[0].control).attr("value", $(ArchivTableEntryDialog.controls[0].control).attr("initVal"));
}
ArchivTableEntryDialog.init = function(initData)
{
	$(ArchivTableEntryDialog.controls[0].control).attr("initVal", initData['category']);
	$.fn.loadContent(initData['table'], getArchivCategoryCallback, null, "data", {selector:"category",distinct:true});
	Group.prototype.init.call(this, initData);
}