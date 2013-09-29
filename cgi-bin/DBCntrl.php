<?php
/*
 * Managemmt amd helper class for database connections
 * 
 * PERSISTENCE: PAGE RUN / STATIC!
*/
interface DBConn
{
	function Connect();
	function GetTableContent($settings);
	function GetTableDef($settings);
	function DropTableContent($settings);
	function SetTableContent($settings);
	function InsertTableContent($settings);
}

require_once("DBConnMysqli.php");

/**
 *  class to encapsulate hierarchies of requirements
 */
class DBReq
{
	const DBReqOpAnd=0,DBReqOpOr=1;
	const DBReqCompExact=0,DBReqCompContains=1,DBReqCompRegExp=2;
	var $operator;
	var $operands;
	var $comparison;
	
	function DBReq($ops, $op=DBReq::DBReqOpAnd, $comp=DBReq::DBReqCompExact)
	{
		$this->operands = $ops;
		$this->operator = $op;
		$this->comparison = $comp;
	}
	
	function CreateReqString()
	{
		if(count($this->operands)<=0)
			return "";
		$reqString = "(";
		$index = 0;
		foreach ($this->operands as $key => $value) 
		{
			if($index++>0)
			{
				if($this->operator==DBReq::DBReqOpOr)
					$reqString .= " OR ";
				else
					$reqString .= " AND ";
			}
			if(is_a($value, 'DBReq'))
			{
				$reqString .= $op->CreateReqString();
			}
			else 
			{
				$value = preg_replace("/%20/", " ", $value);
				if($this->comparison==DBReq::DBReqCompRegExp)
					$reqString .= $key." REGEXP '".$value."'";
				else if($this->comparison==DBReq::DBReqCompContains)
					$reqString .= $key." LIKE '%".$value."%'";
				else
					$reqString .= $key." = '".$value."'";
//		        print("<!-- requirements:".$reqString." //-->\n");
			}
		}
		$reqString .= ")";
		
		return $reqString;
	}
}

class ConnSettings
{
	public $table = "";
	public $fields = array(); 
	public $requirements = NULL; 
	public $useRegExp = FALSE;
	public $orderBy = NULL;
	public $distinct=FALSE;
}

class DBCntrl
{
	private static $instance;
	private $dbConn;
	
	private function DBCntrl()
	{
		$this->dbConn = new DBConnMysqli();
	}
	
	public static function &GetInst()
	{
		global $build_errors;
		if(!isset(self::$instance))
		{
			$build_errors = array();
			self::$instance = new DBCntrl();
		} 
		
		return self::$instance;
	}
	
	public function &Conn()
	{
		if(null==$this->dbConn)
			$this->dbConn = new DBConnMysqli();
		
		return $this->dbConn;
	}
	
	public function GetContent($id)
	{
		return $this->Conn()->GetTableContent(
			array(
				'table'=>"pages", 
				'requirements'=>array("identifier"=>$id)
				));
	}
	public function GetNavi()
	{
		return $this->Conn()->GetTableContent(
			array(
				'table'=>"navigation", 
				'orderBy'=>"priority"
				));
	}
	public function GetPageIdentifier($id)
	{
		$result = $this->Conn()->GetTableContent(
			array(
				'table'=>"pages", 
				'fields'=>"identifier", 
				'requirements'=>array("id"=>$id)
				));
		assert(count($result)==1);
		$result= $result[0];
		return $result;
	}
	
	public function GetMenu($idval)
	{
		$result = $this->Conn()->GetTableContent(
			array(
				'table'=>"submenus", 
				'requirements'=>array("id"=>$idval)
				));
		if(count($result)!=1)
			return NULL;
		else
			return $result[0];
	}

	public function GetParagraph($idval)
	{
		$result = $this->Conn()->GetTableContent(
			array(
				'table'=>"paragraphs", 
				'requirements'=>array("id"=>$idval)
				));
		if(count($result)!=1)
			return NULL;
		else
			return $result[0];
	}
	
	public function GetPicData($picID)
	{
		$result = $this->Conn()->GetTableContent(
			array(
				'table'=>"pictures", 
				'requirements'=>array("id"=>$picID)
				));
		assert(count($result)==1);
		$result= $result[0];
		return $result;
	}
	
}

?>