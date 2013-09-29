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
					var data = {category:$("#categorySelect").val()};
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
	};
	return adminPage;
});