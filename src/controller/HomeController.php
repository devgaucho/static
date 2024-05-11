<?php
/**
 * Site: static
 * Date: 09/05/24
 * Time: 19:41
 */

namespace src\controller;

use src\model\MensagensModel;
use src\Utils;

class HomeController extends Utils
{
	function get(){
		parent::view('inc/header',['title'=>'Static']);
		$MensagensModel=new MensagensModel();
		$data['mensagens']=$MensagensModel->read();
		parent::view('home',$data);
	}
}