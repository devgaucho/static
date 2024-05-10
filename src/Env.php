<?php
/* v0.1.0
 * - loadFromFile($filename): carrega o .env a partir do arquivo
 * - loadFromString($str): converte a string para $_ENV
 */
namespace src;
class Env{
	private function arr2env($contents_arr){
		foreach ($contents_arr as $value_str){
			$value_str=trim($value_str);
			$value_arr=null;
			$first_char=substr($value_str,0,1);
			if(
				!empty($value_str) and
				$first_char != '#'
			){
				$value_arr=explode('=',$value_str);
			}
			if(
				!empty($value_arr) and
				is_array($value_arr)
			){
				$key_str=$value_arr[0];
				unset($value_arr[0]);
				$_ENV[$key_str]=implode(
					'=',$value_arr
				);
			}
		}
		return true;
	}
	function loadFromFile($filename){
		if(!file_exists($filename)){
			die('.env '.$filename.' not found'.PHP_EOL);
		}
		$str=file_get_contents($filename);
		return $this->loadFromString($str);
	}
	function loadFromString($str){
		$arr=explode(PHP_EOL,$str);
		return $this->arr2env($arr);
	}
}