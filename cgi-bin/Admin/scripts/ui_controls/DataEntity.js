

var DataEntity = function(n, h, d)
{
	this.name = n;
	this.helpString = h;
	this.staticPresetData = d;
	this.control = null;
};
DataEntity.prototype.createControl = function(doc, presetValues)
{
	return this.control;
};
DataEntity.prototype.init = function(presetValues)
{
};
DataEntity.prototype.create = function(doc, presetValues, presetOptions)
{
	var controlDiv = doc.createElement("div");
	controlDiv.setAttribute("class", "controlDiv");
	var label = doc.createElement("div");
	label.setAttribute("class", "creationLabel");
	label.innerHTML = this.helpString;
	controlDiv.appendChild(label);
	
	var theControl = this.createControl(doc, presetValues, presetOptions);
	if(null!=theControl)
		controlDiv.appendChild(theControl);
	
	var helpIcon = doc.createElement("img");
	helpIcon.setAttribute("class", "helpIcon");
	helpIcon.setAttribute("src", "../../images/layout/question_mark.png");
	controlDiv.setAttribute("title", this.helpString);
	controlDiv.appendChild(helpIcon);
	
	return controlDiv;
};
DataEntity.prototype.destroy = function()
{
};


////////////////////////////////////////////////////////////////////////////////////////
/********************************************************************************************
 *  Group
 ********************************************************************************************/

var Group = function(n, h) 
{
	// contructor
	DataEntity.call(this, n, h);
	this.controls = [];
};
Group.prototype = new DataEntity;
Group.prototype.init = function(presetValues)
{
	for(d in this.controls)
	{
		this.controls[d].init(presetValues);
	}
};
Group.prototype.create = function(doc, presetValues, presetOptions)
{
	this.control = doc.createElement("fieldset");
	this.control.setAttribute("id", this.name+"_edit");
	var boxLegend = doc.createElement("legend");
	boxLegend.innerHTML = this.helpString;
	this.control.appendChild(boxLegend);
	for(d in this.controls)
	{
		this.control.appendChild(this.controls[d].create(doc, presetValues, presetOptions));
	}
	
	return this.control;
};

Group.prototype.getData = function(data)
{
	for(d in this.controls)
	{
		this.controls[d].getData(data);
	}
};
Group.prototype.destroy = function()
{
	for(d in this.controls)
	{
		this.controls[d].destroy();
	}
};
