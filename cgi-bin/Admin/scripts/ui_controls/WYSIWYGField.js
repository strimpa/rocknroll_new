////////////////////////////////////////////////////////////////////////////////////////
var WYSIWYGField = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
};
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
};
function createMce(on)
{
	on.tinymce({
		// Location of TinyMCE script
		script_url : 'scripts/tiny_mce/tiny_mce.js',

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
};

WYSIWYGField.prototype.getData = function(data)
{
//	alert($(this.control).tinymce().getContent());
	var wrapper = $("<div><div /></div>");
	wrapper.children().first().append($(this.control).tinymce().getContent());
	data[this.name] = wrapper.html();
//	alert(data[this.name]);
};
WYSIWYGField.prototype.destroy = function(presetValues)
{
//	$(this.control).tinymce().removeControl();
//	$("#"+this.name+"_edit_parent").remove();
//	alert("destroy editor");
};