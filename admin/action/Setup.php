<?php
include_once(APP_ROOT.'/beans/ApplicationSetup.php');
//include_once(APP_ROOT."/beans/tipo_presa_carico.php");
include_once(APP_ROOT."/beans/spese_spedizione.php");
include_once(APP_ROOT."/beans/users_type.php");
			
class Setup extends DBSmartyAction
{
	function Setup()
	{
		parent::DBSmartyAction();

		if(!empty($_REQUEST['reset']))	
			unset($_SESSION['Setup']);

		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			if(!empty($_REQUEST['spese_spedizione_peso']))
			{
				for ($i=0;$i<count($_REQUEST['spese_spedizione_peso']);$i++)
				{
					$BeanSpeseSpedizione = new spese_spedizione();
					if(!empty($_REQUEST['id_spese_spedizione_peso'][$i]))
						$BeanSpeseSpedizione->setId($_REQUEST['id_spese_spedizione_peso'][$i]);
					$BeanSpeseSpedizione->setSpese_spedizione_peso($_REQUEST['spese_spedizione_peso'][$i]);
					$BeanSpeseSpedizione->setPeso_spese_spedizione_da($_REQUEST['peso_spese_spedizione_da'][$i]);
					$BeanSpeseSpedizione->setPeso_spese_spedizione_a($_REQUEST['peso_spese_spedizione_a'][$i]);
					if(!empty($_REQUEST['spese_spedizione_peso'][$i]))
					$BeanSpeseSpedizione->dbStore($this->conn);
				}
			}
			$this->storeSetupByKey('commissione_imballi_dendekker', 'id_commissione_imballi_dendekker', 'commissione_imballi_dendekker');
			$this->storeSetupByKey('commissione_fissa_dendekker', 'id_commissione_fissa_dendekker', 'commissione_fissa_dendekker');
			$this->storeSetupByKey('commissione_scatola', 'id_commissione_scatola', 'commissione_scatola');
			
			$this->storeSetupByKey('commissione_imballi_gasa', 'id_commissione_imballi_gasa', 'commissione_imballi_gasa');
			$this->storeSetupByKey('commissione_fissa_gasa', 'id_commissione_fissa_gasa', 'commissione_fissa_gasa');
			
			$this->storeSetupByKey('iva', 'id_iva', 'iva');
			
			$this->storeSetupByKey('payment_type', 'id_payment_type', 'payment_type');
			
			$this->storeSetupByKey('tipo_consegna', 'id_tipo_consegna', 'tipo_consegna');
			
			$this->storeSetupByKey('percent_sale', 'id_percent_sale', 'percentuale_sconto');

			$this->storeSetupByKey('perc_ricarico', 'id_perc_ricarico', 'perc_ricarico');
			
			//$this->storeSetupByKey('spese_spedizione', 'id_spese_spedizione', 'spese_spedizione');

			$this->storeSetupByKey('altezza_carrello', 'id_altezza_carrello', 'altezza_carrello');
			$this->storeSetupByKey('larghezza_carrello', 'id_larghezza_carrello', 'larghezza_carrello');
			$this->storeSetupByKey('profondita_carrello', 'id_profondita_carrello', 'profondita_carrello');
				
			$this->storeUserTypeByKey('user_type', 'id_user_type');
			
			//$this->storeSetupByKey('iva', 'iva');

			unset($_SESSION['Setup']);
			$this->_redirect('?act=Setup');
		}
		else 
		{
			if(!empty($_REQUEST['delete_ut']))
			{
				$BeanUserType = new users_type();
				$BeanUserType->dbDelete($this->conn, array($_REQUEST['id']),false);
			}

			$BeanSpeseSpedizione = new spese_spedizione();
			$_SESSION['Setup']['spese_spedizione_peso'] = $BeanSpeseSpedizione->dbGetAll($this->conn);
			$this->tEngine->assign('spese_spedizione_peso', $_SESSION['Setup']['spese_spedizione_peso']);
				
			if(!empty($_REQUEST['add_spese']))
			{
				$_SESSION['Setup']['spese_spedizione_peso'][count($_SESSION['Setup']['spese_spedizione_peso'])+1] = array('id_spese_spedizione_peso'=>'','spese_spedizione_peso'=>'','peso_spese_spedizione_da'=>'','peso_spese_spedizione_a'=>'');
				$this->tEngine->assign('spese_spedizione_peso', $_SESSION['Setup']['spese_spedizione_peso']);
			}
			
			if(!empty($_REQUEST['rem_spese']))
			{
				$elToRemove = $_SESSION['Setup']['spese_spedizione_peso'][ count($_SESSION['Setup']['spese_spedizione_peso']) - 1 ];
				
				if(key_exists('id', $elToRemove))
				{
					$BeanSpeseSpedizione = new spese_spedizione();
					$BeanSpeseSpedizione->dbDelete($this->conn, $elToRemove['id'], false);
				}
				unset($_SESSION['Setup']['spese_spedizione_peso'][ count($_SESSION['Setup']['spese_spedizione_peso']) - 1 ]);
				
				$this->tEngine->assign('spese_spedizione_peso', $_SESSION['Setup']['spese_spedizione_peso']);
			}
			
			
			$BeanUserType = new users_type();
			$userType = $BeanUserType->dbGetAll($this->conn);
			$this->getElemendByKey($userType, 'user_type', 'ut', $BeanUserType);

			$BeanApplicationSetup 	= new ApplicationSetup();
			$iva 			= $BeanApplicationSetup->dbGetAllByField($this->conn, 'iva');
			$this->getElemendByKey($iva, 'iva', 'iva', $BeanApplicationSetup);

			
			$BeanApplicationSetup 	= new ApplicationSetup();
			$commissione 			= $BeanApplicationSetup->dbGetAllByField($this->conn, 'commissione_imballi_dendekker');
			$this->getElemendByKey($commissione, 'commissione_imballi_dendekker', 'cid', $BeanApplicationSetup);

			$BeanApplicationSetup 	= new ApplicationSetup();
			$commissione_sc 			= $BeanApplicationSetup->dbGetAllByField($this->conn, 'commissione_scatola');
			$this->getElemendByKey($commissione_sc, 'commissione_scatola', 'scid', $BeanApplicationSetup);
				
			$BeanApplicationSetup 	= new ApplicationSetup();
			$commissione 			= $BeanApplicationSetup->dbGetAllByField($this->conn, 'commissione_fissa_dendekker');
			$this->getElemendByKey($commissione, 'commissione_fissa_dendekker', 'cfd', $BeanApplicationSetup);
				

			$BeanApplicationSetup 	= new ApplicationSetup();
			$commissione 			= $BeanApplicationSetup->dbGetAllByField($this->conn, 'commissione_imballi_gasa');
			$this->getElemendByKey($commissione, 'commissione_imballi_gasa', 'cig', $BeanApplicationSetup);
			
			$BeanApplicationSetup 	= new ApplicationSetup();
			$commissione 			= $BeanApplicationSetup->dbGetAllByField($this->conn, 'commissione_fissa_gasa');
			$this->getElemendByKey($commissione, 'commissione_fissa_gasa', 'cfg', $BeanApplicationSetup);
				
			
			$BeanApplicationSetup 	= new ApplicationSetup();
			$percentSale 			= $BeanApplicationSetup->dbGetAllByField($this->conn, 'percentuale_sconto');
			$this->getElemendByKey($percentSale, 'percent_sale', 'ps', $BeanApplicationSetup);
			
// 			$BeanApplicationSetup 	= new ApplicationSetup();
// 			$speseSpedizione 			= $BeanApplicationSetup->dbGetAllByField($this->conn, 'spese_spedizione');
// 			$this->getElemendByKey($speseSpedizione, 'spese_spedizione', 'ss', $BeanApplicationSetup);
			
			$BeanApplicationSetup 	= new ApplicationSetup();
			$altezzaCarrello 		= $BeanApplicationSetup->dbGetAllByField($this->conn, 'altezza_carrello');
			$this->getElemendByKey($altezzaCarrello, 'altezza_carrello', 'ss', $BeanApplicationSetup);
			$BeanApplicationSetup 	= new ApplicationSetup();
			$larghezzaCarrello 		= $BeanApplicationSetup->dbGetAllByField($this->conn, 'larghezza_carrello');
			$this->getElemendByKey($larghezzaCarrello, 'larghezza_carrello', 'ss', $BeanApplicationSetup);
			$BeanApplicationSetup 	= new ApplicationSetup();
			$profonditaCarrello 	= $BeanApplicationSetup->dbGetAllByField($this->conn, 'profondita_carrello');
			$this->getElemendByKey($profonditaCarrello, 'profondita_carrello', 'ss', $BeanApplicationSetup);
				
			
			$paymentType 			= $BeanApplicationSetup->dbGetAllByField($this->conn, 'payment_type');
			$this->getElemendByKey($paymentType, 'payment_type', 'pt', $BeanApplicationSetup);
			
			$tipoConsegna 			= $BeanApplicationSetup->dbGetAllByField($this->conn, 'tipo_consegna');
			$this->getElemendByKey($tipoConsegna, 'tipo_consegna', 'tc', $BeanApplicationSetup);
			
			$BeanApplicationSetup 	= new ApplicationSetup();
			$perc_ricarico 			= $BeanApplicationSetup->dbGetAllByField($this->conn, 'perc_ricarico');
			$this->tEngine->assign('perc_ricarico', $perc_ricarico[0]);
		}
		
		$this->tEngine->assign('tpl_action', 'Setup');
		$this->tEngine->display('Index');
	}
	
	function storeUserTypeByKey($requestNameKey, $requestIdKey)
	{
		foreach ($_REQUEST[$requestNameKey] as $key => $val)
		{
			if(key_exists($key, $_REQUEST[$requestIdKey]))
			{
				$BeanUserType = new users_type($this->conn, $_REQUEST[$requestIdKey][$key]);
				$BeanUserType->setName($val);
				$BeanUserType->setDescription($val);
				$BeanUserType->dbStore($this->conn);
			}
			else 
			{
				$BeanUserType = new users_type();
				$BeanUserType->setName($val);
				$BeanUserType->setDescription($val);
				$BeanUserType->dbStore($this->conn);
			}
		}
	}	

	function storeSetupByKey($requestNameKey, $requestIdKey, $dbFieldName)
	{
 		if(!empty($_REQUEST['perc_ricarico']))
 		{
 			if(!empty($_REQUEST['id_perc_ricarico']))
 				$BeanApplicationSetup = new ApplicationSetup($this->conn, $_REQUEST['id_perc_ricarico']);
 			else
 				$BeanApplicationSetup = new ApplicationSetup();
 			$BeanApplicationSetup->setName($_REQUEST['perc_ricarico']);
 			$BeanApplicationSetup->setField_name('perc_ricarico');
 			$BeanApplicationSetup->setIs_active(1);
 			$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
 			$BeanApplicationSetup->dbStore($this->conn);
 		}
		
// 		if(!empty($_REQUEST['id_iva']))
// 		{
// 				$BeanApplicationSetup = new ApplicationSetup($this->conn, $_REQUEST['id_iva']);
// 				$BeanApplicationSetup->setName($_REQUEST['iva']);
// 				$BeanApplicationSetup->setField_name('iva');
// 				$BeanApplicationSetup->setIs_active(1);
// 				$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
// 				$BeanApplicationSetup->dbStore($this->conn);
// 		}

		if(!empty($_REQUEST['altezza_carrello']))
		{
				$BeanApplicationSetup = new ApplicationSetup($this->conn, $_REQUEST['id_altezza_carrello']);
				$BeanApplicationSetup->setName($_REQUEST['altezza_carrello']);
				$BeanApplicationSetup->setField_name('altezza_carrello');
				$BeanApplicationSetup->setIs_active(1);
				$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
				$BeanApplicationSetup->dbStore($this->conn);
		}
		if(!empty($_REQUEST['larghezza_carrello']))
		{
			$BeanApplicationSetup = new ApplicationSetup($this->conn, $_REQUEST['id_larghezza_carrello']);
			$BeanApplicationSetup->setName($_REQUEST['larghezza_carrello']);
			$BeanApplicationSetup->setField_name('larghezza_carrello');
			$BeanApplicationSetup->setIs_active(1);
			$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
			$BeanApplicationSetup->dbStore($this->conn);
		}
		if(!empty($_REQUEST['profondita_carrello']))
		{
			$BeanApplicationSetup = new ApplicationSetup($this->conn, $_REQUEST['id_profondita_carrello']);
			$BeanApplicationSetup->setName($_REQUEST['profondita_carrello']);
			$BeanApplicationSetup->setField_name('profondita_carrello');
			$BeanApplicationSetup->setIs_active(1);
			$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
			$BeanApplicationSetup->dbStore($this->conn);
		}
		
// 		if(!empty($_REQUEST['spese_spedizione']))
// 		{
// 				$BeanApplicationSetup = new ApplicationSetup($this->conn, $_REQUEST['id_spese_spedizione']);
// 				$BeanApplicationSetup->setName($_REQUEST['spese_spedizione']);
// 				$BeanApplicationSetup->setField_name('spese_spedizione');
// 				$BeanApplicationSetup->setIs_active(1);
// 				$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
// 				$BeanApplicationSetup->dbStore($this->conn);
// 		}
		
		if(!empty($_REQUEST['commissione_imballi_dendekker']))
		{
			$BeanApplicationSetup = new ApplicationSetup($this->conn, $_REQUEST['id_commissione_imballi_dendekker']);
			$BeanApplicationSetup->setName($_REQUEST['commissione_imballi_dendekker']);
			$BeanApplicationSetup->setDescription($_REQUEST['commissione_imballi_dendekker']);
			$BeanApplicationSetup->setField_name('commissione_imballi_dendekker');
			$BeanApplicationSetup->setIs_active(1);
			$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
			$BeanApplicationSetup->dbStore($this->conn);
		}
		if(!empty($_REQUEST['commissione_fissa_dendekker']))
		{
			$BeanApplicationSetup = new ApplicationSetup($this->conn, $_REQUEST['id_commissione_fissa_dendekker']);
			$BeanApplicationSetup->setName($_REQUEST['commissione_fissa_dendekker']);
			$BeanApplicationSetup->setDescription($_REQUEST['commissione_fissa_dendekker']);
			$BeanApplicationSetup->setField_name('commissione_fissa_dendekker');
			$BeanApplicationSetup->setIs_active(1);
			$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
			$BeanApplicationSetup->dbStore($this->conn);
		}
		if(!empty($_REQUEST['commissione_scatola']))
		{
			$BeanApplicationSetup = new ApplicationSetup($this->conn, $_REQUEST['id_commissione_scatola']);
			$BeanApplicationSetup->setName($_REQUEST['commissione_scatola']);
			$BeanApplicationSetup->setDescription($_REQUEST['commissione_scatola']);
			$BeanApplicationSetup->setField_name('commissione_scatola');
			$BeanApplicationSetup->setIs_active(1);
			$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
			$BeanApplicationSetup->dbStore($this->conn);
		}
		
		unset($_REQUEST['commissione_imballi_dendekker']);
		unset($_REQUEST['commissione_fissa_dendekker']);
		unset($_REQUEST['commissione_scatola']);
		
		if(!empty($_REQUEST['commissione_imballi_gasa']))
		{
			$BeanApplicationSetup = new ApplicationSetup($this->conn, $_REQUEST['id_commissione_imballi_gasa']);
			$BeanApplicationSetup->setName($_REQUEST['commissione_imballi_gasa']);
			$BeanApplicationSetup->setDescription($_REQUEST['commissione_imballi_gasa']);
			$BeanApplicationSetup->setField_name('commissione_imballi_gasa');
			$BeanApplicationSetup->setIs_active(1);
			$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
			$BeanApplicationSetup->dbStore($this->conn);
		}
		if(!empty($_REQUEST['commissione_fissa_gasa']))
		{
			$BeanApplicationSetup = new ApplicationSetup($this->conn, $_REQUEST['id_commissione_fissa_gasa']);
			$BeanApplicationSetup->setName($_REQUEST['commissione_fissa_gasa']);
			$BeanApplicationSetup->setDescription($_REQUEST['commissione_fissa_gasa']);
			$BeanApplicationSetup->setField_name('commissione_fissa_gasa');
			$BeanApplicationSetup->setIs_active(1);
			$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
			$BeanApplicationSetup->dbStore($this->conn);
		}
		
		unset($_REQUEST['commissione_imballi_gasa']);
		unset($_REQUEST['commissione_fissa_gasa']);
// 		unset($_REQUEST['iva']);
		
		foreach ($_REQUEST[$requestNameKey] as $key => $val)
		{

			if(key_exists($key, $_REQUEST[$requestIdKey]))
			{
				$BeanApplicationSetup = new ApplicationSetup($this->conn, $_REQUEST[$requestIdKey][$key]);

				$BeanApplicationSetup->setName($val);
				if(!empty($_REQUEST[$requestNameKey.'_description'][$key]))
					$BeanApplicationSetup->setDescription($_REQUEST[$requestNameKey.'_description'][$key]);
				$BeanApplicationSetup->setField_name($dbFieldName);
				$BeanApplicationSetup->setIs_active(1);
				$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
				$BeanApplicationSetup->dbStore($this->conn);
			}
			else 
			{
				$BeanApplicationSetup = new ApplicationSetup();

				$BeanApplicationSetup->setName($val);
				if(!empty($_REQUEST[$requestNameKey.'_description'][$key]))
					$BeanApplicationSetup->setDescription($_REQUEST[$requestNameKey.'_description'][$key]);
				$BeanApplicationSetup->setField_name($dbFieldName);
				$BeanApplicationSetup->setIs_active(1);
				$BeanApplicationSetup->setOperatore($_SESSION['LoggedUser']['username']);
				$BeanApplicationSetup->dbStore($this->conn);	
			}
		}
	}
	
	function getElemendByKey($value, $element, $suffixRequestKey, $BeanApplicationSetup)
	{
		if(empty($_SESSION['Setup'][$element]))
			$_SESSION['Setup'][$element] = $value;
		if(!empty($_REQUEST['add_'.$suffixRequestKey]))
			$_SESSION['Setup'][$element][ count($_SESSION['Setup'][$element]) ]['name'] = '';
		if(!empty($_REQUEST['rem_'.$suffixRequestKey]))
		{
			$elToRemove = $_SESSION['Setup'][$element][ count($_SESSION['Setup'][$element]) - 1 ];
			if(key_exists('id', $elToRemove))
			{
				$BeanApplicationSetup->dbDelete($this->conn, array($elToRemove['id']), true);
			}
			unset($_SESSION['Setup'][$element][ count($_SESSION['Setup'][$element]) - 1 ]);
		}
		if(!empty($_REQUEST['delete_'.$suffixRequestKey]))
		{
			if($suffixRequestKey != 'ut')
				$BeanApplicationSetup->dbDelete($this->conn, array($_REQUEST['id']), true);
			unset($_SESSION['Setup']);
			$this->_redirect('?act=Setup');
		}
		
		$this->tEngine->assign($element, $_SESSION['Setup'][$element]);		
	}
}
?>