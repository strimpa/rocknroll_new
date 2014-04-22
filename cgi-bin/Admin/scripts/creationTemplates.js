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
			pic.prop("class","folderBrowsePic"); 
			pic.prop("src",$(this).text()); 
			picFrame.append(pic);
			picFrame.append($("<i>"+$(this).text()+"</i>"));
			picHolder.append(picFrame);
			pic.click(function(){
				$(targetPicElem).prop("src", $(this).prop("src"));
				holder.remove();
			});
		});
//			$("#menuPriority").val( $(result).find( 'priority' ).first().text());
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
};
DataEntity.prototype.init = function(presetValues)
{
};
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
};
DataEntity.prototype.destroy = function()
{
};


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


require.config({
/*    baseUrl : '/cgi-bin/Admin/scripts', */
    paths : {
		pluginsPath : '../../Plugins'
    },
	waitSeconds:5,
	catchError:true
});
requirejs.onError = function (err) {
    alert(err.requireType);
    if (err.requireType === 'timeout') {
        alert('modules: ' + err.requireModules);
    }

    throw err;
};

require([
	//	'ui_controls/DataEntity',
	'ui_controls/TextField',
	'ui_controls/TextArea',
	'ui_controls/Spinner',
	'ui_controls/DateField',
	'ui_controls/TimeField',
	'ui_controls/ComboBox',
	'ui_controls/WYSIWYGField',
	'ui_controls/PicBrowse',
	'ui_controls/CheckGroup',
	],
	function()
	{
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
			
			$('html, body').animate({ scrollTop: 0 }, 'fast');
		};
		function removeDialog()
		{
			$("#creationBg").remove();
			$(".creationBox").remove();
		};
		
		/********************************************************************************************
		 *  Uses
		 ********************************************************************************************/
		
		jQuery.PageCreationDialog = new EditDialog("PageCreationDialog", "Neuen Inhalt einfuegen");
		jQuery.PageCreationDialog.controls = new Array(
				new TextField("identifier", 	"Der eindeutige identifier fuer die URL."),
				new TextField("menuTitle", 		"Der Titel im Menu falls gewuenscht."),
				new TextField("articleTitle", 	"Der Titel des Artikels."),
				new Spinner("priority", 		"Die Reihenfolge im Menu.", 0)
		);
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		jQuery.SubMenuCreationDialog = new EditDialog("SubMenuCreationDialog", "Neuen Menueintrag einfuegen");
		jQuery.SubMenuCreationDialog.controls = new Array(
				new TextField("title", "Der Titel der im Menu angezeigt wird."),
				new ComboBox("url",  "Der Textanker-link auf das der Eintrag verweist.")
		);
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		jQuery.ParagraphCreationDialog = new EditDialog("ParagraphCreationDialog", "Neuen Absatz erstellen");
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
				new TextField("newCategory", "Neue Kategorie."),
				new ComboBox("sortBy",  "Sortiere den Inhalt der Tabelle bezuegllich dieser Spalte."),
				new CheckGroup("flags", "Binaere Eigenschaften des Absatzes.", ["Zeige nur zukuenftige Eintraege an."])
		);
		jQuery.ParaTypeStrings = ["Text mit Bild rechts","Text mit Bild links","Tabelle"];
		jQuery.ParagraphCreationDialog.controls = new Array(
				new TextField("title", "Der Titel des Absatzes."),
				new ComboBox("type",  "Typ des verwendeten Layouts.", jQuery.ParaTypeStrings.join("|")),
				new Spinner("height", "Hoehe des Absatz."),
				PicTextGroup,
				TableGroup
		);
		
		/////////////////////////////////////////////////////////////////////////////////
		// helper fucntions
		/////////////////////////////////////////////////////////////////////////////////
		
		
		function toggleGroup(event, index)
		{
			if(null==index)
				index = $("#type_edit").prop("selectedIndex"); 
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
		
		function getSortingHeaderCallback(result)
		{
			$(TableGroup.controls[3].control).empty();
		
			optn = document.createElement("OPTION");
			optn.textContent = "";
			$(TableGroup.controls[3].control).append(optn);
			
			$(result).find("A_0").children().each(function(){
				optn = document.createElement("OPTION");
				optn.textContent = this.nodeName.toLowerCase();
				$(TableGroup.controls[3].control).append(optn);
			});
		}
		
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
			$(EventTableEntryDialog.controls[0].control).val( $(EventTableEntryDialog.controls[0].control).data("initVal"));
		}
		
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
			$(ArchivTableEntryDialog.controls[0].control).val( $(ArchivTableEntryDialog.controls[0].control).data("initVal"));
		}
		
		/////////////////////////////////////////////////////////////////////////////////
		// inits
		/////////////////////////////////////////////////////////////////////////////////
		
		jQuery.ParagraphCreationDialog.init = function(presetValues)
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
				var table = $(this).val();
				$.fn.loadContent(table, getCategoryCallback, null, "data", {selector:"category",distinct:true});
				// attaching change listener for table to set sorting alternatives.
				$.fn.loadContent(table, getSortingHeaderCallback, null, "data", {def:true});
			});
			Group.prototype.init.call(this, presetValues);
			var defaultVal = "events";
			if(null!=presetValues['table'])
				 defaultVal = presetValues['table'];
			var gGroupContext = this.controls[1].control;
			var gNewCatContext = this.controls[2].control;
			var gSortBy = this.controls[3].control;
			$.fn.loadContent(defaultVal, function(result){	
				getCategoryCallback(result);
		
				// If entries contain a categrory like this or not
				if(null!=presetValues['category'])
				{
					var cat = presetValues['category']; 
					if(null!=$(gGroupContext).text().match(cat))
					{
						$(gGroupContext).val( cat);
					}
					else
					{
						$(gNewCatContext).val( cat);
					}
				}
		
			}, null, "data", {selector:"category",distinct:true});
			
			$.fn.loadContent(defaultVal, function(result){
				getSortingHeaderCallback(result);
				
				if(null!=presetValues['sortBy'])
				{
					var sortBy = presetValues['sortBy']; 
					if(null!=$(gSortBy).text().match(sortBy))
					{
						$(gSortBy).val(sortBy);
					}
				}
			}, null, "data", {def:true});
		};
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		jQuery.EventTableEntryDialog = new EditDialog("EventTableEntryDialog", "In Tabelle einfuegen.");
		jQuery.EventTableEntryDialog.controls = new Array(
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
		
		
		
		jQuery.EventTableEntryDialog.init = function(initData)
		{
			$(EventTableEntryDialog.controls[0].control).data("initVal", initData['category']);
			$.fn.loadContent(initData['table'], getEventCategoryCallback, null, "data", {selector:"category",distinct:true});
			Group.prototype.init.call(this, initData);
		};
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		jQuery.ArchivTableEntryDialog = new EditDialog("EventTableEntryDialog", "In Tabelle einfuegen.");
		jQuery.ArchivTableEntryDialog.controls = new Array(
				new ComboBox("category",  "Die Kategorie des Eintrages."),
				new TextField("newCategory",  "Neue Kategorie erstellen (KLEINBUCHSTABEN!). Ueberschreibt obige Auswahl."),
				new Spinner("issue", "Die Ausgabenummer."),
				new PicBrowse("pic", "Cover"),
				new TextField("content", "Inhalt des Heftes.")
		);
		jQuery.ArchivTableEntryDialog.init = function(initData)
		{
			$(ArchivTableEntryDialog.controls[0].control).data("initVal", initData['category']);
			$.fn.loadContent(initData['table'], getArchivCategoryCallback, null, "data", {selector:"category",distinct:true});
			Group.prototype.init.call(this, initData);
		};
	}
);