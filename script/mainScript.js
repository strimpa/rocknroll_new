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
			var extendableDiv = $(this).parent().parent().next().find("div.extendable").first();
			$(this).click(function()
			{
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
			extendableDiv.css("height", "0px");
			extendableDiv.attr("extended", "false")
		});
	}
	
	function createPaymentEvents()
	{
		$("div#bankDetails").css("visibility", "hidden");
		$("div#transferDetails").css("visibility", "hidden");
		
		$("input#lastschrift").click(function(){
			$("div#bankDetails").css("visibility", "visible");
			$("div#transferDetails").css("visibility", "hidden");
		});
		$("input#ueberweisung").click(function(){
			$("div#bankDetails").css("visibility", "hidden");
			$("div#transferDetails").css("visibility", "visible");
		});
	}
	
	function createEvents()
	{
		linkInfoDivs();
		
		$("div.radiolabel").click(function()
		{
			var theInput = $(this).find("input");
			if(theInput.attr("checked"))
				theInput.removeAttr("checked");
			else
				theInput.attr("checked", "true");
		});
		
		createPaymentEvents();
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
