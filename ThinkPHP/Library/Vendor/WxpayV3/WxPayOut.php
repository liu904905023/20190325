<?php
class Random
{
	private static $randomCount  = 0;

	public static function GenerateOutTradeNo($SystemUserSysNo){
		$RandomText = self::CreateRandomText();
		mt_srand($RandomText);
		$randval = mt_rand(100000000, 999999999);
		return "13".date('YmdHis').$randval.$SystemUserSysNo;
	
	}
	
	private static function CreateRandomText(){
		self::$randomCount++;
		$guid = self::guid();
		$key1 = self::hashCode($guid);
		$key2 = microtime(true)*10000;
		mt_srand($key1*$key2*self::$randomCount);
		return $randval = mt_rand(100000, 999999);

	}
	
	private static function guid(){

		if(function_exists('com_create_guid')){
		return com_create_guid();//window下
		}else{//非windows下
		mt_srand((double)microtime()*10000);//optional for php 4.2.0 andup.
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45);//字符 "-"
		$uuid = substr($charid, 0, 8).$hyphen
			   .substr($charid, 8, 4).$hyphen
			   .substr($charid,12, 4).$hyphen
			   .substr($charid,16, 4).$hyphen
			   .substr($charid,20,12);//字符 "}"
		return $uuid;
		}
	}



//字符转换为ascii码
private  static function asc_encode($c)
{
    $len = strlen($c);
    $a = 0;
	$scill='';
     while ($a < $len)
     {
        $ud = 0;
         if (ord($c{$a}) >=0 && ord($c{$a})<=127)
         {
            $ud = ord($c{$a});
            $a += 1;
         }
         else if (ord($c{$a}) >=192 && ord($c{$a})<=223)
         {
            $ud = (ord($c{$a})-192)*64 + (ord($c{$a+1})-128);
            $a += 2;
         }
         else if (ord($c{$a}) >=224 && ord($c{$a})<=239)
         {
            $ud = (ord($c{$a})-224)*4096 + (ord($c{$a+1})-128)*64 + (ord($c{$a+2})-128);
            $a += 3;
         }
         else if (ord($c{$a}) >=240 && ord($c{$a})<=247)
         {
            $ud = (ord($c{$a})-240)*262144 + (ord($c{$a+1})-128)*4096 + (ord($c{$a+2})-128)*64 + (ord($c{$a+3})-128);
            $a += 4;
         }
         else if (ord($c{$a}) >=248 && ord($c{$a})<=251)
         {
            $ud = (ord($c{$a})-248)*16777216 + (ord($c{$a+1})-128)*262144 + (ord($c{$a+2})-128)*4096 + (ord($c{$a+3})-128)*64 + (ord($c{$a+4})-128);
            $a += 5;
         }
         else if (ord($c{$a}) >=252 && ord($c{$a})<=253)
         {
            $ud = (ord($c{$a})-252)*1073741824 + (ord($c{$a+1})-128)*16777216 + (ord($c{$a+2})-128)*262144 + (ord($c{$a+3})-128)*4096 + (ord($c{$a+4})-128)*64 + (ord($c{$a+5})-128);
            $a += 6;
         }
         else if (ord($c{$a}) >=254 && ord($c{$a})<=255)
         { //error
            $ud = 0;
            $a++;
         }else{
             $ud = 0;
             $a++;
         }
         $scill .= "$ud";
     }
     return $scill;
}
//生成hashcode
private static function hashCode($s){
    $arr_str = str_split($s);
    $len = count($arr_str);
    $hash = 0;
    for($i=0; $i<$len; $i++){
        if(ord($arr_str[$i])>127){
            $ac_str = $arr_str[$i].$arr_str[$i+1].$arr_str[$i+2];
            $i+=2;
        }else{
            $ac_str = $arr_str[$i];
        }
       
        $hash = (int)($hash*31 + self::asc_encode($ac_str));
        //64bit下判断符号位
        if(($hash & 0x80000000) == 0) {
             //正数取前31位即可
             $hash &= 0x7fffffff;
        }
        else{
             //负数取前31位后要根据最小负数值转换下
             $hash = ($hash & 0x7fffffff) - 2147483648;
        }
    }
    return $hash;
}
}

