////////////////////////////////////////////////////////////////////////////////////////
var PicBrowse = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
};
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
	if(null!=presetValues && null!=presetValues[this.name] && ""!=presetValues[this.name])
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
};
PicBrowse.prototype.init = function(presetValues, changeHandler)
{
	var controlScopePass = this.control; 
	$(this.control).load(changeHandler);
	
	$(this.picDelete).click(function(){
		$(controlScopePass).prop("src", "images/noImageDummy.png");
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
					$(controlScopePass1).prop("src", resultString);
				}
				else
				{
					alert(resultString);
				}
			}
		}
		manipIframe();
	});
};

PicBrowse.prototype.getData = function(data)
{
	if($(this.control).prop("src").indexOf("noImageDummy")!=-1)
	{
		data[this.name] = ""; 
		return;
	}
	
	data[this.name] = $(this.control).prop("src");
};
