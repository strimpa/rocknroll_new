////////////////////////////////////////////////////////////////////////////////////////
var CheckGroup = function(n, h, d)
{
	// contructor
	DataEntity.call(this, n, h, d);
};
CheckGroup.prototype = new DataEntity;
CheckGroup.prototype.createControl = function(doc, presetValues, presetOptions)
{
	this.control =  doc.createElement("groupbox");
	this.control.setAttribute("id", this.name+"_edit");
	checkFlags = 0;
	if(null!=presetValues && null!=presetValues[this.name])
	{
		checkFlags = presetValues[this.name];
		console.log("checkFlags:"+checkFlags);
	}
	if(null!=presetOptions && null!=presetOptions[this.name])
		this.staticPresetData = presetOptions[this.name];

	if(null!=this.staticPresetData)
	{
		for(optionIndex=0;optionIndex<this.staticPresetData.length;optionIndex++)
		{
			option = this.staticPresetData[optionIndex];
			check =  doc.createElement("input");
			check.setAttribute("type", "checkbox");
			check.setAttribute("id", this.name+"_edit_option"+optionIndex);
			check.setAttribute("class", "creationControl");
			if(checkFlags&(1<<(optionIndex)))
				check.setAttribute("checked", "true");
			this.control.appendChild(check);

			lbl =  doc.createElement("label");
			lbl.textContent = option;
			this.control.appendChild(lbl);

			br =  doc.createElement("br");
			this.control.appendChild(br);
		}
	}
	return this.control;
};

CheckGroup.prototype.getData = function(data)
{
	checkFlags = 0;
	checkboxes = $(this.control).children(".creationControl");
	for(optionIndex=0;optionIndex<checkboxes.length;optionIndex++)
		if(checkboxes[optionIndex].checked)
			checkFlags |= (1<<(optionIndex));
	data[this.name] = checkFlags;
};