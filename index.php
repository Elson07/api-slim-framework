<?php
require 'vendor/autoload.php';

$app = new \Slim\App;

$app->get('/postagens2', function(){
    echo 'Lista de postagens';
});

//Com o colchete [] o id é opcional no request
$app->get('/usuarios[/{id}]', function($request, $response){
    $id = $request->getAttribute('id');

    echo 'Lista de usuarios ou ID: ' . $id;
});

//Com colchete {} é possivel passar um paramentro para o resquest
$app->get('/postagens[/{ano}[/{mes}]]', function($request, $response){
    //Recupera o ano e mes passado como parametro pelo request
    $ano = $request->getAttribute('ano');
    $mes = $request->getAttribute('mes');

    echo 'Lista de postagens: ' . $mes . '/' .$ano;
});

//.* significa que aceita quaquer tipo de parametro passado pelo request
$app->get('/lista/{itens:.*}', function($request, $response){

    $itens = $request->getAttribute('itens');
    //echo $itens;                          
    var_dump(explode("/", $itens));
});

//Nomear rotas 
$app->get('/blog/postagens/{id}', function($request, $response){
    echo "Listar postagem para um ID";
//O setName define um nome para a rota '/blog/postagens/{id}' 
})->setName('blog');

$app->get('/meusite', function($request, $response){
    //router é um metodo que recupera rota, e pathFor é o caminho que sera recuperado
    $retorno = $this->get("router")->pathFor("blog", ["id"=> "10"]);

    echo $retorno;
});

//Agrupar rotas
//O metodo group é utulizado para agrupar rotas
$app->group('/v1', function(){

    $this->get('/usuarios', function(){
        echo 'Lista de usuarios';
    });

    $this->get('/postagens', function(){
        echo 'Lista de postagens';
    });

});



$app->run();