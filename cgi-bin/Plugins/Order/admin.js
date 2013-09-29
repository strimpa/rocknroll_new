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
		$.fn.loadContent("bestellung,kunden", function(result)
		{
			var typeJson = eval(result);
			var paraDiv = document.createElement("div");
			paraDiv.setAttribute("id", "paragraph_order");
			paraDiv.setAttribute("class", "adminParagraph");
			paraDiv.appendChild(utils.RenderTable("bestellung,kunden", typeJson[0], null, null, {joinFields:"kundenID=id"}, "kundenID,id", false));
			contentDiv.appendChild(paraDiv);
		}, null, "data", {def:true,json:"types",joinFields:"kundenID=id"});
	};
	return adminPage;
});