////////////////////////////////////////////////////////////////////////////////////////
var TextField = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
};
TextField.prototype = new DataEntity;
TextField.prototype.createControl = function(doc, presetValues)
{
	this.control =  doc.createElement("input");
	this.control.setAttribute("type", "text");
	this.control.setAttribute("id", this.name+"_edit");
	this.control.setAttribute("class", "creationControl");
	if(null!=presetValues && null!=presetValues[this.name])
	{
		this.control.value = presetValues[this.name];
	}
	return this.control;
};

TextField.prototype.getData = function(data)
{
	data[this.name] = this.control.value;
};