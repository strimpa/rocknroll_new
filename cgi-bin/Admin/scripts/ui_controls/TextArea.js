////////////////////////////////////////////////////////////////////////////////////////
var TextArea = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
};
TextArea.prototype = new DataEntity;
TextArea.prototype.createControl = function(doc, presetValues)
{
	this.control =  doc.createElement("textarea");
	this.control.setAttribute("id", this.name+"_edit");
	this.control.setAttribute("class", "creationControl");
	if(null!=presetValues && null!=presetValues[this.name])
	{
		this.control.value = presetValues[this.name];
	}
	return this.control;
};

TextArea.prototype.getData = function(data)
{
	data[this.name] = this.control.value;
};