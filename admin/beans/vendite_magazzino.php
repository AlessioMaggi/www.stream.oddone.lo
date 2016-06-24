<?php

class vendite_magazzino extends BeanBase
{
	var $id_vendita;
	var $id_magazzino;
	var $personal_price;
	var $quantita;
	var $total;
	var $percentuale_sconto;
	var $ddt;

	function vendite_magazzino($conn=null, $id=null)
	{
		parent::BeanBase();
		
		$this->table_name = "vendite_magazzino";
		
		if(isset($id))
		{
			if(is_array($id))
				$this->fill($id);
			elseif(is_numeric($id) && $id>0)
				$this->dbGetOne($conn, $id);
		}
	}

	function dbGetOne($db=null, $id=null)
	{
		if (!$this->_is_connection($db))
			return false;

		$query="SELECT * FROM ".$this->table_name." WHERE id=". $id ;
		$result=$db->query($query);
		if(get_class($result) == "DB_Error")
			return $this->_showErrorNoQuery("File: ".__FILE__."<BR> Class: ".get_class($this)."<BR>Line: ".__LINE__."<BR>Query: <BR>".$query);

		//		Loggo la query sql
		$this->BeanLog("query", $query);
		//		Loggo la query sql

		if($row=$result->fetchRow(DB_FETCHMODE_ASSOC))
			$this->fill($row);

		$result->free();
	}

	function dbGetAll($db=null)
	{
		if (!$this->_is_connection($db))
			return false;

		$values=array();
		$query="SELECT * FROM ".$this->table_name;
		$result=$db->query($query);
		if(get_class($result) == "DB_Error")
			return $this->_showErrorNoQuery("File: ".__FILE__."<BR> Class: ".get_class($this)."<BR>Line: ".__LINE__."<BR>Query: <BR>".$query);

		//		Loggo la query sql
		$this->BeanLog("query", $query);
		//		Loggo la query sql

		while($row=$result->fetchRow(DB_FETCHMODE_ASSOC))
				$values[]=$row;

		$result->free();
		return $values;
	}

	function dbGetAllByIdVendita($db=null, $idVendita, $objMagazzino = null)
	{
		if (!$this->_is_connection($db))
			return false;

		$values=array();
		$query="SELECT * FROM ".$this->table_name.' WHERE id_vendita='.$idVendita;
		$result=$db->query($query);
		if(get_class($result) == "DB_Error")
			return $this->_showErrorNoQuery("File: ".__FILE__."<BR> Class: ".get_class($this)."<BR>Line: ".__LINE__."<BR>Query: <BR>".$query);

		//		Loggo la query sql
		$this->BeanLog("query", $query);
		//		Loggo la query sql

		while($row=$result->fetchRow(DB_FETCHMODE_ASSOC))
		{
			if(!empty($objMagazzino))
			{
				$dataMagazzino = $objMagazzino->dbSearch($db, ' AND magazzino.id = '.$row['id_magazzino']);
				if(!empty($dataMagazzino))
					$row['magazzino'] = $dataMagazzino[0];
			}			
			
			$values[]=$row;
		}
		
		$result->free();
		return $values;
	}
	
	function _dbAdd($db=null)
	{
		if (!$this->_is_connection($db))
			return false;
		$values=$this->vars();
		
		$table_fields=array_keys($values);
		$table_values=array_values($values);

		$sth = $db->autoPrepare($this->table_name, $table_fields, DB_AUTOQUERY_INSERT);		
		$db->execute($sth, $table_values);

		//		Loggo la query sql
		$this->BeanLog("PEAR_DB", $db);
		//		Loggo la query sql

		return $id;
	}

	function _dbUpdate($db=null)
	{
		if(!$this->_is_connection($db))
			return false;

		$values=$this->vars();
		
		unset($values['id']);

		$table_fields = array_keys($values);
		$table_values = array_values($values);

		$sth = $db->autoPrepare($this->table_name, $table_fields, DB_AUTOQUERY_UPDATE, "id = ".$id);
		$db->execute($sth, $table_values);
		//		Loggo la query sql
		$this->BeanLog("PEAR_DB", $db);
		//		Loggo la query sql

		return $this->vars();
	}

	function dbStore($db=null)
	{
		if(!$this->_is_connection($db))
			return false;

		if(isset($this->id) && is_numeric($this->id) && $this->id>0)
			return $this->_dbUpdate($db);
		else
			return $this->_dbAdd($db);
	}

	function fast_edit($db, $id=null, $key="", $value="")
	{
		if(!$this->_is_connection($db))
			return false;
		
		$query="UPDATE ".$this->table_name." SET ".$key."='".$value."' WHERE id =".$id."";

		$db->query($query);

		//		Loggo la query sql
		$this->BeanLog("PEAR_DB", $db);
		//		Loggo la query sql
	}

	function _is_connection($db)
	{
		$ret = false;
		if(is_object($db) && is_subclass_of($db, 'db_common') && method_exists($db, 'simpleQuery') )
			$ret = true;
		return $ret;
	}

	function dbDelete($db=null, $IDS=null, $is_logical = true)
	{
																																
		if(is_array($IDS) && count($IDS) > 1)
		{
			if($is_logical)
				$query = "UPDATE ".$this->table_name." SET  = 0 WHERE id IN (".implode(", ", $IDS).")";
			else
				$query = "DELETE FROM ".$this->table_name." WHERE id IN (".implode(", ", $IDS).")";
		}
		else
		{
			if($is_logical)
				$query = "UPDATE ".$this->table_name." SET  = 0 WHERE id = ".$IDS[0];
			else
				$query = "DELETE FROM ".$this->table_name." WHERE id = ".$IDS[0];
		}

		$db->query($query);
		//		Loggo la query sql
		$this->BeanLog("PEAR_DB", $db);
		//		Loggo la query sql		
	}

	function fill($value=null) 
	{ 
		if(!is_array($value)) 
			$value=array(); 	
		
		$props = $this->vars(); 
		foreach($props as $k=>$v) 
		{ 
			$func = "set".ucfirst($k); 
			if(isset($value[$k]))
				$this->$func($value[$k]);
		}
	}

	function dbSearch($db=null, $search=null)
	{
		if (!$this->_is_connection($db))
			return false;

		$values=array();

		$query = "";
		
		$result=$db->query($query);

		if(get_class($result) == "DB_Error")
			return $this->_showErrorNoQuery("File: ".__FILE__."<BR> Class: ".get_class($this)."<BR>Line: ".__LINE__."<BR>Query: <BR>".$query);

		//		Loggo la query sql
		$this->BeanLog("query", $query);
		//		Loggo la query sql

		while($row=$result->fetchRow(DB_FETCHMODE_ASSOC))
			$values[]=$row;

		$result->free();
		return $values;
	}
	
	function vars() 
	{  
		$vars = get_object_vars($this);
		unset($vars['table_name']);
		return $vars;  
	}
	
	/*			GET e SET		*/	
	function getId_vendita(){return $this->id_vendita;}

	function setId_vendita($value= null)
	{
				if(strlen($value) > 11)
			$value = substr($value, 0, 11);
				
								
		$this->id_vendita = (int)$value;
	}

	function getId_magazzino(){return $this->id_magazzino;}

	function setId_magazzino($value= null)
	{
				if(strlen($value) > 11)
			$value = substr($value, 0, 11);
				
								
		$this->id_magazzino = (int)$value;
	}

	function getPersonal_price(){return $this->personal_price;}

	function setPersonal_price($value= null)
	{
				if(strlen($value) > 255)
			$value = substr($value, 0, 255);
				
								
		$this->personal_price = (string)$value;
	}

	function getQuantita(){return $this->quantita;}

	function setQuantita($value= null)
	{
				if(strlen($value) > 255)
			$value = substr($value, 0, 255);
				
								
		$this->quantita = (string)$value;
	}

	function getTotal(){return $this->total;}

	function setTotal($value= null)
	{
				if(strlen($value) > 255)
			$value = substr($value, 0, 255);
				
								
		$this->total = (string)$value;
	}

	function getPercentuale_sconto(){return $this->percentuale_sconto;}

	function setPercentuale_sconto($value= null)
	{
				if(strlen($value) > 255)
			$value = substr($value, 0, 255);
				
								
		$this->percentuale_sconto = (string)$value;
	}

	function getDdt(){return $this->ddt;}

	function setDdt($value= null)
	{
				if(strlen($value) > 255)
			$value = substr($value, 0, 255);
				
								
		$this->ddt = (string)$value;
	}

			
}
?>