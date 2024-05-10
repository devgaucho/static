<?php
/*
v0.1.0 (04mai2024)
- render($str,$data): renderiza uma string no  formato chaplin
*/
namespace src;
class Chaplin{
	function render($str,$data){
		// {{#nome_da_variavel}} ... {{/nome_da_variavel}}
		$blockPattern='/{{#([a-zA-Z0-9_]+)}}(.*?){{\/\1}}/s';
		// {{&nome_da_variavel}} ou {{nome_da_variavel}}
		$variablePattern='/{{(?:&)?([a-zA-Z0-9_]+)}}/';
		// retorna os blocos pro append
		$fnBlockAppend=function(
			$blockName,$blockContent
		) use ($data){
			// processa para cada variável
			$blockResult='';
			foreach(
				$data[$blockName] as $key=>$item
			){
				if(
				is_array($data[$blockName][$key])
				){
					$blockResult.=$this
						->render(
							$blockContent,
							$item
						);
				}else{
					//fix string
					//TODO: chaplin.js
					$blockResult.=$this
						->render(
							$blockContent,
							$data[
							$blockName
							]
						);
				}
			}
			return $blockResult;
		};
		// função de renderização dos loops
		$fnLoop=function($matches) use (
			$data,
			$fnBlockAppend
		){
			$blockName=$matches[1];
			$blockContent=$matches[2];
			if(
				isset($data[$blockName]) and
				is_array($data[$blockName])
			){
				return $fnBlockAppend(
					$blockName,$blockContent
				);
			}else{
				// / remove o bloco se ele não exista
				return '';
			}
		};
		$str=preg_replace_callback(
			$blockPattern,$fnLoop,$str
		);
		// função de renderização as variáveis simples
		$fnVar=function($matches) use ($data){
			$variableName=$matches[1];
			if(isset($data[$variableName])){
				$value=$data[$variableName];
			}else{
				$value=$matches[0];
			}
			if(strpos($matches[0],'{{&')===0){
				return $value;
			}else{
				return htmlspecialchars($value);
			}
		};
		$str=preg_replace_callback(
			$variablePattern,$fnVar,$str
		);
		return $str;
	}
}