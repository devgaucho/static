<?php
/**
 * Site: static
 * Date: 11/05/24
 * Time: 18:56
 */

namespace src\model;

use src\Utils;

class MensagensModel extends Utils{
	function create($data){
		$db=parent::db();
		$data['created_at']=time();
		$db->insert('mensagens',$data);
		return $db->id();
	}
	function read(){
		$db=parent::db();
		$where=[
			'ORDER'=>['id'=>'DESC']
		];
		$mensagens=$db->select('mensagens','*',$where);
		foreach($mensagens as $key=>$mensagem){
			$date=date('r',$mensagem['created_at']);
			$mensagens[$key]['created_at_h']=$date;
		}
		return $mensagens;
	}
}