<?php 
namespace MyApp\controllers;

class Home{

	//Usando injeção de dependencias, neste casso precissado usar um controtor para poder fazer o uso deste recurso 
	//protected $container;
	protected $view;
	public function __construct($view){
		$this->view = $view;
	}

	public function index($request, $response){
		//$view = $this->container->get('View');
		var_dump($this->$view);
		return $response->write('Teste index');
	}
}

?>