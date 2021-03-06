<?php
class Format {
	/**
	 * 时间转换
	 * 0-5分钟--->刚刚
	 * 5-60分钟--->X分钟前
	 * 1小时-6小时--->X小时前
	 * @param string | int $intime		//可传 字符串：2000-01-01~2000-01-01 00:00:00，时间戳：13....
	 * @return string $outTime
	 */
	static public function convertTimeByRules($intime){
		$outTime = '';
		if (empty($intime)) return $outTime;
	
		$tmpTime = (!is_numeric($intime)) ? strtotime($intime) : $intime;
	
		$curtime = time();
		$sec = $curtime - $tmpTime;
		if ($sec < 5 * 60) {
			$outTime = '刚刚';
		}elseif ($sec > 5 * 60 && $sec < 60 * 60){
			$outTime = floor($sec / 60) .'分钟前';
		}else if ($sec >= 60 * 60 && $sec < 7 * 60 * 60) {
			$outTime = floor($sec / 3600) .'小时前';
		}else {
			if (!is_numeric($intime)) $outTime = substr($intime, 0, 16);
			else $outTime = date('Y-m-d H:i', $intime);
		}
		return $outTime;
	}
	/**
	* 计算剩余时间
	* @param  int  $intime       
	* @return $string $outTime 
	* @author XJW Create At 2017-8-31
	*/
	static public function residueTime($intime){
	    $outTime = '';
	    if (empty($intime)) return $outTime;
	    
	    $tmpTime = (!is_numeric($intime)) ? strtotime($intime) : $intime;
	    
	    $curtime = time();
	    $sec     = $tmpTime - $curtime;
	    if ($sec > (24*3600)){
	        $outTime = floor($sec/3600/24).'天'.floor($sec/3600%24).'時';
	    }elseif ($sec > 3600){
	        $outTime = floor($sec/3600%24).'時'.floor($sec/60%60).'分';
	    }elseif ($sec > 60){
	        $outTime = floor($sec/60%60).'分';
	    }elseif ($sec > 0){
	        $outTime = '1分';
	    }else{
	        $outTime = '';
	    }
	    return $outTime;
	}
	
	/**
	 * 格式化数字类型
	 * 
	 * @param	string $number
	 * @param	int	   $digit		//要保留是的小数位数，默认保留两位小数
	 * @param	bool   $round		//是否四舍五入，默认是
	 * @param	bool   $trimZero	//是否去除多余的0，默认是
	 * @param   string $split       //每三位分隔符
	 * @return
	 */
	static public function formatNumber($number, $digit = 1, $round = false, $trimZero = true, $split = ''){
		$pow = bcpow(10, $digit);
		
		$des_number = bcadd($number, 0, $digit);//直接舍去$digit后的小数
		
		$dotPosition = strpos($number, '.');
		//浮点数进入四舍五入
		if ($round && $dotPosition !== false){
			//字符串模式处理四舍五入
			$end = $dotPosition + $digit + 1;
			
			$value = substr($number, $end, 1);// $number位数不足，则直接舍去
			//是否进位
			if ($value !== false && $value >= 5) {
				$des_number = bcdiv(bcadd(bcmul($number, $pow, 0), 1, 0), $pow, $digit);
			}
		}
		
		if ($trimZero){
		    //去除右边多余的0
		    if (preg_match('/^-?\d+?\.0+$/', $des_number)) {
		        $des_number = preg_replace('/^(-?\d+?)\.0+$/', "$1", $des_number);
		    }elseif (preg_match('/^-?\d+?\.[0-9]+?0+$/', $des_number)) {
		        $des_number = preg_replace('/^(-?\d+\.[0-9]+?)0+$/', "$1", $des_number);
		    }
		    
		}
		
		//是否需要金钱格式化
		if(!!$split){
		    $digits = explode('.', $des_number);
		    $newInteger = '';
		    
		    $count = 0;
		    for ($index = strlen($digits[0]) - 1; $index >= 0; $index--){
		        $newInteger = $digits[0][$index] . $newInteger;
		        $count++;
		        if($count % 3 == 0 && ($index > 1 || ($index == 1 && $digits[0][0] != '-'))){
		            $newInteger = $split . $newInteger;
		            $count = 0;
		        }
		    }
		    
		    if (count($digits) == 2){
		        $des_number = $newInteger . '.' . $digits[1];
		    }else{
		        $des_number = $newInteger;
		    }
		}
		
		return $des_number;
	}
	
	/**
	 * 字符串剪切
	 * @param $string			//原始字符串
	 * @param $cutlength		//截取长度
	 * @param $hasDot			//超过截取长度是否加省略号
	 * @return string
	 */
	static public function cutstr($string, $cutlength = 112, $hasDot = true) {
		if (empty($string)) return '';
		$returnstr='';
		$i=0;
		$n=0;
		$str_length = strlen($string);//字符串的字节数
		while (($n < $cutlength) && ($i <= $str_length))
		{
			$temp_str = substr($string, $i, 1);
			$ascnum = Ord($temp_str);//得到字符串中第$i位字符的ascii码
			if ($ascnum >= 224) {
				$returnstr .= substr($string, $i, 3);//根据UTF-8编码规范，将3个连续的字符计为单个字符
				$i = $i + 3;     //实际Byte计为3
				$n++;            //字串长度计1
			}elseif ($ascnum >= 192) {
				$returnstr .= substr($string, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
				$i = $i + 2;    //实际Byte计为2
				$n++;           //字串长度计1
			}elseif ($ascnum >= 65 && $ascnum <= 90) {//大写字母
				$returnstr .= substr($string, $i, 1);
				$i = $i + 1;    //实际的Byte数仍计1个
				$n++;           //但考虑整体美观，大写字母计成一个高位字符
			}else {//其他情况，包括小写字母和半角标点符号
				$returnstr .= substr($string, $i, 1);
				$i = $i + 1;    //实际的Byte数计1个
				$n = $n + 0.5;  //小写字母和半角标点等与半个高位字符宽...
			}
		}
		if ($str_length > $i && $hasDot){
			$returnstr .= "...";//超过长度时在尾处加上省略号
		}
		return htmlspecialchars($returnstr);
	}
	
	/**
	 * 处理邮件地址（不全部显示）
	 * @param string $email		//邮件地址
	 * @return string
	 */
	public static function convertEmail($email) {
		if (!Validator::isEmail($email)) return '';
		
		return substr($email, 0, 2) .'****'. substr($email, strrpos($email, '@'));
	}
	
	
	/**
	 * 隐藏手机号码部分数字
	 * @param 
	 * @return 
	 */
	public static function convertPhone($phone) {
		if (!Validator::isPhoneNumber($phone)) return '';
		
		return substr($phone, 0, 3) .'*****'. substr($phone, -3);
	}
	/**
	 * 隐藏身份证号码部分号码
	 * @param
	 * @return
	 */
	public static function convertIdentitycard($identitycard) {
	    return substr($identitycard, 0, 5) .'*****'. substr($identitycard, -4);
	}
}