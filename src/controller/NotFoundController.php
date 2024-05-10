<?php
/**
 * Site: static
 * Date: 09/05/24
 * Time: 19:43
 */

namespace src\controller;

class NotFoundController
{
	function get(){
		http_response_code(404);
		die("erro 404");
	}
}