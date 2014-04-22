////////////////////////////////////////////////////////////////////////////////////////
var ComboBox = function(n, h, d, returnIndex)
{
	this.returnIndex = returnIndex;
	// contructor
	DataEntity.call(this, n, h, d);
};
ComboBox.prototype = new DataEntity;
ComboBox.prototype.createControl = function(doc, presetValues, presetOptions)
{
	this.control =  doc.createElement("select");
	this.control.setAttribute("id", this.name+"_edit");
	this.control.setAttribute("class", "creationControl");
	if(null!=presetOptions && null!=presetOptions[this.name])
		this.staticPresetData = presetOptions[this.name];
	if(null!=this.staticPresetData)
	{
		var entries = this.staticPresetData.split("|");
		for(e in entries)
		{
			optn = document.createElement("OPTION");
			optn.textContent = entries[e];
			this.control.appendChild(optn);
		}
	}
	if(null!=presetValues && null!=presetValues[this.name])
	{
		this.control.selectedIndex = presetValues[this.name];
	}
	return this.control;
};

ComboBox.prototype.getData = function(data)
{
	if(this.returnIndex)
		data[this.name] = this.control.selectedIndex;
	else
		data[this.name] = this.control.value;
};