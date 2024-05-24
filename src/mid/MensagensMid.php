<?php
/**
 * Site: static
 * Date: 24/05/24
 * Time: 19:20
 */

namespace src\mid;


class MensagensMid{
	function create($data){
		//validar tamanho da mensagem
		$mensagem=$data['mensagem'];
		$mensagem=trim($mensagem);
		$mensagem=mb_strtolower($mensagem);
		$len=mb_strlen($mensagem);
		$maxLength=5;
		$minLength=2;
		if($len<$minLength){
			$msg='digite uma mensagem com no mínimo ';
			$msg.=$minLength.' caracteres';
			die($msg);
		}
		if($len>$maxLength){
			$msg='digite uma mensagem com no máximo ';
			$msg.=$maxLength.' caracteres';
			die($msg);
		}
		$data['mensagem']=$mensagem;
		return $data;
	}
}