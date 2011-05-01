(function($)
{
	var currCallback = undefined;
	var ajax = undefined;
	var busyLoading = false;
	var loadingQueue = new Array();
	var REST_WRITE = 1, REST_DELETE = 2, REST_EDIT = 3;


	function linkInfoDivs()
	{
		$(".infoDiv").each(function()
		{
			$(this).click(function()
			{
				var extendableDiv = $(this).parent().parent().next().find("div.extendable").first();
//				alert(extendableDiv.text());
				if(extendableDiv.attr("extended")=="true")
				{
					extendableDiv.css("height", "0px");
					extendableDiv.attr("extended", "false")
				}
				else
				{
					extendableDiv.css("height", "50px");
					extendableDiv.attr("extended", "true")
				}
			});
		});
	}
	
	function createEvents()
	{
		linkInfoDivs();
	}
	
	////////////////////////////////////////////////////////////////////////////////////////
	// Entry point
	////////////////////////////////////////////////////////////////////////////////////////
	$.Init = function() 
	{
		createEvents();
	}
})(jQuery);

// entry point
$(document).ready(function()
{
    $.Init();
});
