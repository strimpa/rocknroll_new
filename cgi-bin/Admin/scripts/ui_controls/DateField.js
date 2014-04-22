////////////////////////////////////////////////////////////////////////////////////////
var DateField = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
};
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
};
DateField.prototype.init = function()
{
	$(this.control).datepicker({dateFormat:'yy-mm-dd'});
	$(this.control).datepicker( "setDate", "0" );
//	if(this.control.getAttribute("value")=="")
//		$(this.control).datepicker("value", 300);
};

DateField.prototype.getData = function(data)
{
	data[this.name] = this.control.value;
};