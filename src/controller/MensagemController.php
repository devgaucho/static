<?php
/**
 * Site: static
 * Date: 11/05/24
 * Time: 18:58
 */

namespace src\controller;

use src\mid\MensagensMid;
use src\model\MensagensModel;
use src\Utils;

class MensagemController extends Utils{
	function post(){
		$data=[
			'mensagem'=>$_POST['mensagem']
		];
		return $this->create($data);
	}
	function create($data){
		$MensagensMid=new MensagensMid();
		$data=$MensagensMid->create($data);
		$MensagensModel=new MensagensModel();
		if($MensagensModel->create($data)){
			$url=$_ENV['SITE_URL'];
			parent::redirect($url);
		}else{
			die("erro ao criar a mensagem");
		}
	}
}