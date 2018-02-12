<?php

class Functions
{
	public function encrypt($pass)
	{
	  	$splited = str_split($pass);
		$newData = null;
		foreach($splited as $split)
		{
			$sign = ord($split) + 1;
			$sign = chr($sign);
			$newData .= $sign;
		}

		$pwd = "tt9)hr!a3a2*_ss3d58ps2wpq4p9hya8fuy#48gawi3eusm_ob9d3h#r9f1s_seya94sru9(edf3do";
		$newData = $pwd.$pass.$pwd;
		$newData = hash('sha512', $pwd.$newData.$pwd);
		$newData = hash('whirlpool', $pwd.$newData.$pwd);
		$newData = base64_encode($pwd.$newData.$pwd);
		$newData = md5($pwd.$newData.$pwd);

	  	return $newData;
	}

	public function timeToAgo($date)
	{
		if(empty($date))
			return "Невалидна дата..";
	  
		$lengths         = array("60","60","24","7","4.35","12","10");
	 
		$now             = time();
		$unix_date        = strtotime($date);
	 
		if(empty($unix_date))
			return "Невалидна дата..";
	 
		if($now > $unix_date) 
		{    
			$difference     = $now - $unix_date;
			$tense         = "преди ";
	 
		} 
		elseif($now < $unix_date)
		{
			$difference    = $unix_date - $now;
			$tense         = "след ";
		}
		else 
		{
			$difference     = $unix_date - $now;
			$tense         = "точно сега";
		}
	 
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++)
			$difference /= $lengths[$j];
	 
		$difference = round($difference);
		if($difference >= 2)
			$periods = array("секунди", "минути", "часа", "дена", "седмици", "месеца", "години", "века");
		else
			$periods = array("секунда", "минута", "час", "ден", "седмица", "месец", "година", "век");
	
		return $difference != 0 ? "{$tense} $difference $periods[$j]" : $tense;
	}
}