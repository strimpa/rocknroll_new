////////////////////////////////////////////////////////////////////////////////////////
var TimeField = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
};
TimeField.prototype = new DataEntity;
TimeField.prototype.createControl = function(doc, presetValues)
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
	else
		this.control.setAttribute("value", "19:00:00");

	return this.control;
};
TimeField.prototype.init = function()
{
	$(this.control).timepicker();
};

TimeField.prototype.getData = function(data)
{
	data[this.name] = this.control.value;
};
