////////////////////////////////////////////////////////////////////////////
/// module to help with rendering thw admin plugin page
////////////////////////////////////////////////////////////////////////////

define(function() 
{
	////////////////////////////////////////////////////////////////////////////
	/// module definition 
	////////////////////////////////////////////////////////////////////////////
	var adminPage = {};
	
	adminPage.Render = function(contentDiv)
	{
		var paraDiv = document.createElement("div");
		paraDiv.setAttribute("id", "paragraph_order");
		paraDiv.setAttribute("class", "adminParagraph");
		var folderSelect = document.createElement("select");
		$(folderSelect).append("<option>Suche Ordner aus Liste aus...</option>");
		var photoDiv = document.createElement("div");
		var photoArray = new Array();
		var assetFolder = "/photos";
		if(null!=adminPage.path)
		{
			assetFolder = adminPage.path + assetFolder; 
		}
		console.log("assetFolder:"+assetFolder);
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
		}, {"assetFolder":assetFolder}, "xml", {recursive:true});
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
		contentDiv.appendChild(paraDiv);	};
	return adminPage;
});