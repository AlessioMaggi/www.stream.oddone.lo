<?php

class ecm_basket extends BeanBase
{
	var $id;
	var $id_user;
	var $is_active;
	var $data_inserimento_riga;
	var $data_modifica_riga;
	var $operatore;

	function ecm_basket($conn=null, $id=null)
	{
		parent::BeanBase();
		
		$this->table_name = "ecm_basket";
		
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

		$query="SELECT * FROM ".$this->table_name." WHERE id=". $id . " AND is_active = 1";
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
		$query="SELECT * FROM ".$this->table_name." WHERE is_active = 1";
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

	function dbGetOneByIdUser($db=null, $id=null)
	{
		if (!$this->_is_connection($db))
			return false;
	
		if(!empty($date))
			$date = " AND data_modifica_riga = '".$date."'";
	
		$query="SELECT * FROM ".$this->table_name." WHERE id_user=". $id ." AND is_active = 1";
	
		$result=$db->query($query);
		if(get_class($result) == "DB_Error")
			return $this->_showErrorNoQuery("File: ".__FILE__."<BR> Class: ".get_class($this)."<BR>Line: ".__LINE__."<BR>Query: <BR>".$query);
	
		//		Loggo la query sql
		$this->BeanLog("query", $query);
		//		Loggo la query sql
	
		if($row=$result->fetchRow(DB_FETCHMODE_ASSOC))
			$ret = $row;
	
		$result->free();
		return $ret;
	}
	
	function dbGetOneByCustomerAndDate($db=null, $id=null, $date = null)
	{
		if (!$this->_is_connection($db))
			return false;

		if(!empty($date))
			$date = " AND data_modifica_riga = '".$date."'";
		
		$query="SELECT * FROM ".$this->table_name." WHERE id_user=". $id . $date." AND is_active = 1 ORDER BY data_inserimento_riga DESC";

		$result=$db->query($query);
		if(get_class($result) == "DB_Error")
			return $this->_showErrorNoQuery("File: ".__FILE__."<BR> Class: ".get_class($this)."<BR>Line: ".__LINE__."<BR>Query: <BR>".$query);

		//		Loggo la query sql
		$this->BeanLog("query", $query);
		//		Loggo la query sql

		if($row=$result->fetchRow(DB_FETCHMODE_ASSOC))
			$ret = $row;

		$result->free();
		return $ret;
	}
	
	function _dbAdd($db=null)
	{
		if (!$this->_is_connection($db))
			return false;

		$id = $db->nextId($this->table_name);
		$this->setID($id);
		$this->setData_inserimento_riga(date('Y-m-d'));
		$this->setData_modifica_riga(date('Y-m-d'));
		$this->setIs_active(1);
				
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

		$id = $this->id;
		$this->setData_modifica_riga(date('Y-m-d'));
//		$this->setIs_active(1);
						
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
				$query = "UPDATE ".$this->table_name." SET is_active = 0 WHERE id IN (".implode(", ", $IDS).")";
			else
				$query = "DELETE FROM ".$this->table_name." WHERE id IN (".implode(", ", $IDS).")";
		}
		else
		{
			if($is_logical)
				$query = "UPDATE ".$this->table_name." SET is_active = 0 WHERE id = ".$IDS[0];
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
			{
				$value[$k] = str_replace('"', "''", stripslashes($value[$k]));
								$this->$func($value[$k]);
			}
		}
	}

	function dbSearch($db=null, $search=null, $BeanEcmBasket = null)
	{
		if (!$this->_is_connection($db))
			return false;

		$values=array();

		$query = "SELECT * FROM ".$this->table_name." WHERE is_active = 1 ".$search;
		
		$result=$db->query($query);

		if(get_class($result) == "DB_Error")
			return $this->_showErrorNoQuery("File: ".__FILE__."<BR> Class: ".get_class($this)."<BR>Line: ".__LINE__."<BR>Query: <BR>".$query);

		//		Loggo la query sql
		$this->BeanLog("query", $query);
		//		Loggo la query sql

		while($row=$result->fetchRow(DB_FETCHMODE_ASSOC))
		{
			if(!empty($BeanEcmBasket))
			{
				$data = $BeanEcmBasket->dbGetAllByIdBasket($db, $row['id']);
				$row['basket_magazzino'] = $data;
			}
			$values[]=$row;
		}
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
	function getId(){return $this->id;}

	function setId($value= null)
	{
				if(strlen($value) > 11)
			$value = substr($value, 0, 11);
				
								
		$this->id = (int)$value;
	}

	function getId_user(){return $this->id_user;}

	function setId_user($value= null)
	{
				if(strlen($value) > 11)
			$value = substr($value, 0, 11);
				
								
		$this->id_user = (int)$value;
	}

	function getIs_active(){return $this->is_active;}

	function setIs_active($value= null)
	{
		$this->is_active = $value;
	}

	function getData_inserimento_riga(){return $this->data_inserimento_riga;}

	function setData_inserimento_riga($value= null)
	{
				
				if(strrpos($value, "-") != 7)
		{
			echo "Errore nel settaggio della properies data_inserimento_riga!";
			exit;
		}
								
		$this->data_inserimento_riga = (string)$value;
	}

	function getData_modifica_riga(){return $this->data_modifica_riga;}

	function setData_modifica_riga($value= null)
	{
				
				if(strrpos($value, "-") != 7)
		{
			echo "Errore nel settaggio della properies data_modifica_riga!";
			exit;
		}
								
		$this->data_modifica_riga = (string)$value;
	}

	function getOperatore(){return $this->operatore;}

	function setOperatore($value= null)
	{
				if(strlen($value) > 255)
			$value = substr($value, 0, 255);
				
								
		$this->operatore = (string)$value;
	}

			
}
?>