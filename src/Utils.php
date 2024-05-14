<?php
/**
 * Site: static
 * Date: 08/05/24
 * Time: 19:40
 *
 * v0.1.0 (08mai2024)
 * - controller($name): retorna uma instância do controller
 * - db(): retorna uma instância do banco de dados
 * - env($filename=null): carrega arquivos .env de configuração
 * - erroFatal($msg): encerra o script e exibe uma mensagem de erro
 * - getExtension($filaname): Retorna a extensão do arquivo
 * - getMethod($methodRaw=false): retorna o método da requisição
 * - isCli(): verifica se tá modo cli ou web
 * - mig(): executa as migrations
 * - model($name): retorna uma instância do model
 * - notFound(): erro 404
 * - redirect($url): redireciona para a url
 * - root(): retorna o diretório root do site
 * - router(): faz o roteamento automático do site
 * - segment($segmentId=null): retorna segmentos da url
 * - showErrors($bool=true): exibe os erros
 * - view($name,$data=[],$print=true): imprime a view via Chaplin
 * v0.1.1 (14mai2024)
 * - verifica a permissão do db sqlite
 */

namespace src;

use Exception;
use Medoo\Medoo;
use src\Chaplin;
use src\Env;
use src\Mig;

class Utils{
	var $db;
	function controller($name){
		$className='src\\controller\\'.$name;
		$className.='Controller';
		if(class_exists($className)){
			return new $className();
		}else{
			return false;
		}
	}
	function db(){
		if(isset($this->db)){
			return $this->db;
		}
		$type=@$_ENV['DB_TYPE'];
		if(!$type){
			$this->erroFatal("DB_TYPE não definido");
		}
		$opts=null;
		if($type=='mysql'){
			$opts=[
				'type'=>'mysql',
				'host'=>'localhost',
				'database'=>$_ENV['MYSQL_DB'],
				'username'=>$_ENV['MYSQL_USER'],
				'password'=>$_ENV['MYSQL_PASSWORD'],
				'charset'=>'utf8mb4',
				'collation'=>'utf8mb4_unicode_ci',
				'port'=>3306
			];
		}
		if($type=='sqlite'){
			$filename=$this->root();
			$filename.='/'.$_ENV['SQLITE_DB'];
			$perms=@fileperms($filename);
			$perms=@decoct($perms);
			$chmod=@substr($perms,-3);
			if(!file_exists($filename) OR $chmod<>'777'){
				$msg='touch "'.$filename;
				$msg.='" && sudo chmod 777 "';
				$msg.=$filename.'"';
				$msg.=' && php "';
				$msg.=$this->root().'/bin/mig.php"';
				die($msg);
			}
			$opts=[
				'type'=>'sqlite',
				'database'=>$filename
			];
		}
		if($opts){
			try{
				$this->db=new Medoo($opts);
			}catch (Exception $e){
				$this->erroFatal($e->getMessage());
			}
		}
		return $this->db;
	}
	function env($filename){
		$Env=new Env();
		$filename=$this->root().'/'.$filename;
		return $Env->loadFromFile($filename);
	}
	function erroFatal($msg){
		die($msg);
	}
	function getExtension($filename){
		return pathinfo($filename,PATHINFO_EXTENSION);
	}
	function getMethod($methodRaw=false){
		$method=$_SERVER['REQUEST_METHOD'];
		if($methodRaw){
			return $method;
		}else{
			if($method=='POST') {
				return 'POST';
			}else{
				return 'GET';
			}
		}
	}
	function isCli(){
		if(php_sapi_name()=="cli"){
			return true;
		}else{
			return false;
		}
	}
	function mig(){
		$conn=$this->db()->pdo;
		$tableDirectory=$this->root().'/table';
		$dbType=$_ENV['DB_TYPE'];
		$debug=$_ENV['SHOW_ERRORS'];
		$Mig=new Mig($conn,$tableDirectory,$dbType,$debug);
		$Mig->mig();
	}
	function model($name){
		$className='src\\model\\'.$name;
		$className.='Model';
		if(class_exists($className)){
			return new $className();
		}else{
			return false;
		}
	}
	function notFound(){
		$this->controller('NotFound')->get();
	}
	function redirect($url){
		header('Location: '.$url);
		die();
	}
	function root(){
		return realpath(__DIR__.'/../');
	}
	function router(){
		$method=strtolower($this->getMethod());
		$primeiroSeg=$this->segment(1);
		if($primeiroSeg=='/'){
			$className='Home';
		}else{
			$className=ucfirst($primeiroSeg);
		}
		//verifica se o controller existe
		$controller=$this->controller($className);
		if(!$controller){
			$controller=$this->controller('NotFound');
			if($controller){
				$method='get';
			}else{
				$msg='controller/NotFound not found';
				$this->erroFatal($msg);
			}
		}
		//verifica se o método existe
		if(method_exists($controller,$method)){
			return $controller->$method();
		}else{
			$msg='method NotFound->get() not found';
			$this->erroFatal($msg);
		}
	}
	function segment($segmentId=null){
		$str=$_SERVER["REQUEST_URI"];
		if (isset($_ENV['SITE_URL'])) {
			$path=parse_url(
				$_ENV['SITE_URL'],
				PHP_URL_PATH
			);
			if (!is_null($path) and $path<>'/') {
				$str=substr($str, strlen($path));
			}
		}
		$str=@explode('?', $str)[0];
		$arr=explode('/', $str);
		$arr=array_filter($arr);
		$arr=array_values($arr);
		if (count($arr)<1) {
			$segment[1]='/';
		} else {
			$i=1;
			foreach ($arr as $key => $value) {
				$segment[$i++]=$value;
			}
		}
		if (is_null($segmentId)) {
			return $segment;
		} else {
			if (isset($segment[$segmentId])) {
				return $segment[$segmentId];
			} else {
				return false;
			}
		}
	}
	function showErrors($bool=false){
		if($bool){
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		}else{
			ini_set('display_errors', 0);
			ini_set('display_startup_errors', 0);
			error_reporting(0);
		}
	}
	function view($name,$data=[],$print=true){
		$data['SITE_NAME']=$_ENV['SITE_NAME'];
		$data['SITE_URL']=$_ENV['SITE_URL'];
		$filename=$this->root().'/view/'.$name.'.html';
		if(!file_exists($filename)){
			die("view ".$filename.' not found');
		}
		$str=file_get_contents($filename);
		$Chaplin=new Chaplin();
		$out=$Chaplin->render($str,$data);
		if($print){
			print $out;
		}else{
			return $out;
		}
	}
}
