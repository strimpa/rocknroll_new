////////////////////////////////////////////////////////////////////////////////////////
var Spinner = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
};
Spinner.prototype = new DataEntity;
Spinner.prototype.createControl = function(doc, presetValues)
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
Spinner.prototype.init = function(presetValues)
{
	$(this.control).spinner({ min: 0, max: 500, step:1 });
	if(null!=presetValues && null!=presetValues[this.name])
	{
		var trimmedVal = parseInt(presetValues[this.name], 10);
		$(this.control).spinner("value", trimmedVal);
	}
	else if(null!=this.staticPresetData)
	{
		$(this.control).spinner("value", this.staticPresetData);
	}
	else if(this.control.getAttribute("value")=="")
		$(this.control).spinner("value", 300);
};

Spinner.prototype.getData = function(data)
{
	data[this.name] = this.control.value;
};