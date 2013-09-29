<?php 

function gibLinksAusFuerRubrik($rubrik)
{
	return DBCntrl::GetInst()->Conn()->GetTableContent(
		array(
			'table'=>"links", 
			'requirements'=>array("category"=>$rubrik, "approved"=>1),
			'fields'=>array("category", "description", "url", "anlegeDatum")
			));

	// $sql = "SELECT DISTINCT category, description, url, anlegeDatum FROM links WHERE category LIKE \"$rubrik\" AND approved != 0"; 
	// $result = mysqli_query($this->db, $sql);
	// if(!$result)
		// print "Aufgetretene Fehler: ".mysqli_error($this->db);
	// return $result;
}

function getLinkSections(){
	return DBCntrl::GetInst()->Conn()->GetTableContent(
		array(
			'table'=>"links", 
			'fields'=>array("category"),
			'distinct'=>true
			));

	// $this->Connect();
	// $sql = "SELECT DISTINCT category FROM links"; 
	// $result = mysqli_query($this->db, $sql);
	// if(!$result)
		// print "Aufgetretene Fehler: ".mysqli_error($this->db);
	// return $result;
}

function gibLinksAusFuerSuche($eingabe){
	return DBCntrl::GetInst()->Conn()->GetTableContent(
		array(
			'table'=>"links", 
			'requirements'=>new DBReq(array("description"=>$eingabe, "url"=>$eingabe),DBReq::DBReqOpOr, DBReq::DBReqCompContains),
			'fields'=>array("category", "description", "url", "anlegeDatum"),
			'distinct'=>true
			));

	// $this->Connect();
	// print "Sucheingabe \"$eingabe\"";
	// $sql = "SELECT DISTINCT `category`,`description`,`url`,`anlegeDatum` FROM links WHERE LOCATE(\"".$eingabe."\", description) != 0 OR LOCATE(\"".$eingabe."\", url) != 0"; 
	// $result = mysqli_query($sql);
	// if(mysqli_num_rows($result)<1){
		// $sql = "SELECT DISTINCT `category`,`description`,`url`,`anlegeDatum` FROM links WHERE LOCATE(\"".$eingabe."\", LCASE(description)) != 0 OR LOCATE(\"".$eingabe."\", LCASE(url)) != 0"; 
		// $result = mysqli_query($this->db, $sql);
	// }
	// if(!$result)
		// print "Ausgabe: ".mysqli_error($this->db);
	// return $result;
}

?>