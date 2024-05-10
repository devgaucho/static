<?php
/**
 * Site: static
 * Date: 10/05/24
 * Time: 19:27
 */

namespace src\controller;

use src\Utils;

class StaticController extends Utils
{
	function get(){
		$hashRecebido=parent::segment(2);
		$file=parent::segment(3);
		$filename=parent::root();
		$filename.='/static/'.$file;
		if(
			file_exists($filename)
			AND is_file($filename)
		){
			$hashReal=md5_file($filename);
			if($hashRecebido==$hashReal){
				$this->imprimirArquivo($filename);
			}else{
				$url=$_ENV['SITE_URL'].'/';
				$url.='static/'.$hashReal.'/'.$file;
				parent::redirect($url);
			}
		}else{
			parent::notFound();
		}
	}
	function imprimirArquivo($filename){
		$ext=parent::getExtension($filename);
		$mime=null;
		switch($ext){
			case 'css':
				$mime='text/css';
				break;
			case 'js':
			case 'json':
				$mime='application/javascript';
				break;
		}
		if(!is_null($mime)){
			header('Content-Type: '.$mime);
			print file_get_contents($filename);
		}
	}
}