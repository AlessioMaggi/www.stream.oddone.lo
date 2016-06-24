<?php
class Currency
{
	static public function getFormatDiscount($price, $percentSale)
	{		
		$return['discount']	= ( $price * $percentSale / 100);
		$return['total_discounted'] = $price - $return['discount'];
		
		return $return;
	}
	
	static public function FormatDateFromMysql($date)
	{
		$exp = explode('-', $date);
		return $exp[2].'/'.$exp[1].'/'.$exp[0];
	}	
	
	static public function FormatEuro($str)
	{
		$str = round(str_replace(',', '.', $str), 2);

		if(strstr($str, "."))
		{
			$exp_price = explode(".", $str);

			if(strlen($exp_price[1]) == 1)
				$return = $str."0";
			elseif(strlen($exp_price[1]) == 0)
				$return = $exp_price[0].",00";
			else 
				$return = $str;
		}
		else
			$return = $str.",00";

		$return = str_replace(".", ",", $return);
		if(empty($_SESSION['label_currency']))
			$_SESSION['label_currency'] = '&euro;';
		return $_SESSION['label_currency'].'<span style="color:#fff">&nbsp;</span>'.$return;
	}
}
?>