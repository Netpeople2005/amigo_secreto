<?php

namespace Index\Service;

use Index\Model\Noticias as Model;
use KumbiaPHP\Di\Container\ContainerInterface;

/**
* 
*/
class Noticias
{

	protected $container;
	
	function __construct(ContainerInterface $con)
	{
		$this->container = $con;
	}

	public function add($message)
	{
		$noticia = new Model();
		$noticia->noticia = $message;
		$noticia->hora = date('Y-m-d H:i:s');
		$noticia->save();
	}
}