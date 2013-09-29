////////////////////////////////////////////////////////////////////////////
/// module to help with rendering thw admin plugin page
////////////////////////////////////////////////////////////////////////////

define(['utils'], function(utils) 
{
	////////////////////////////////////////////////////////////////////////////
	/// module definition 
	////////////////////////////////////////////////////////////////////////////
	var adminPage = {};
	
	adminPage.Render = function(contentDiv)
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
	};
	
	return adminPage;
	
});
