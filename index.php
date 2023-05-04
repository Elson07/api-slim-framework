<?php
//Interface que define o tipo da $request e $response, não é obrigatório, pois o slim ja faz isso, mas é uma boa pratica
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$app = new \Slim\App([
    'settings'=>[
        'displayErrorDetails' => true
    ]

]);

//Container dependecy injection
class Servico{

}

//Sem injeção de dependencia

    $servico = new Servico;

    //Para usar uma veriavel de instancia é necessario usar o 'use' e passar como parametro a variavel de instancia de objeto, mas normalmente não usamos dessa forma, pois usamos injeção de dependencias. 
    $app->get('/servico1', function(Request $request, Response $response) use ($servico){

        var_dump($servico);                             

    });
//Fim

//Com injeção de dependencia
    //para fazer injeção de dependencias usamos comtainer "pimple" 
    // Container Pimple 
    $container = $app->getContainer();
    $container['servico'] = function(){                 //aqui estamos adicionando a um container uma variavel de instancia da classe Servico
        return new Servico;
    }; 

    $app->get('/servico', function(Request $request, Response $response){

        $servico =  $this->get('servico');                          //aqui pegamos a variavel de referencia de instacia passada pelo container
        var_dump($servico);                                         //Então em resumo, isto é ima injeção de dependencia.

    });
//Fim


//Controllers como serviço 
    //Neste caso estamos usando uma classe, com um metodo dentro de uma rota, mas para fazer isso é necessario abrir o conposer.jason e configuralo. 
    //Em composer.joson foi adicionado o seginte codigo
    /*
        "autoload": {
        "psr-4": {
            "MyApp\\": "src/MyApp"
        }
    }
    */ 
    //Feito isso use o propt ou terminal de comando e vá até a pasta do pojeto, que é slim, execute o seginte comando "composer dumpauload", caso o coposer não esteva intalado de foma global, use o seginte comando "php composer.phar dumpautoload", na qual vai retornar, 'Generating autoload files' e 'Generated autoload files'

    //$app->get('/usuario', 'Classe:metodo');

    //Agora podemos usar a classe, para isso precissamos emplementar em src/MyApp/controllers, acessa para criar.

    //Agora temos uma rota usuario que possui uma classe, que aponta para um metodo
    //$app->get('/usuario', '\MyApp\controllers\Home:index');

    //injetando dpendencia em controller 
    $container = $app->getContainer();
    $container['Home'] = function(){                 //aqui estamos adicionando a um container uma variavel de instancia da classe Servico
        return new MyApp\controllers\Home(new MyApp\View);
    }; 

    $app->get('/usuario', 'Home:index');

$app->run();





