<?php
//Interface que define o tipo da $request e $response, não é obrigatório, pois o slim ja faz isso, mas é uma boa pratica
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$app = new \Slim\App;

// Padrão PSR7, é uma pratica de retorno ($response), melhor que usar echo!
$app->get('/postagens', function(Request $request, Response $response){

    //PSR7, getBody() é o corpo da mensagem, e write() escreve no corpo da mensagem.
    $response->getBody()->write('Lista de postagens');

});

//post cria dados no servidor (Insert)
$app->post('/postagens/adiciona', function(Request $request, Response $response){

    $post = $request->getParsedBody();
    $nome = $post['nome'];
    $email = $post['email'];

    //Usando este mestodo, podemos savar os dados em um banco de dados usando o insert into, e no return poderiamos escrever uma menssagem de sucesso, ao inves dos dados, como fisemos agora.
    //return $response->getBody()->write($nome . ' - ' . $email);

    return $response->getBody()->write('Sucesso');

});

//put atualiza dados no servidor (Update), neste caso podemos passar um id como parâmetro, para saber qual dado sera atualizado. 
$app->put('/postagens/atualizar', function(Request $request, Response $response){

    $post  = $request->getParsedBody();
    $id    = $post['id'];
    $nome  = $post['nome'];
    $email = $post['email'];

    //Usando este mestodo, podemos atualizar os dados em um banco de dados usando o update, e no return poderiamos escrever uma menssagem de sucesso ao atualizar.

    return $response->getBody()->write('Sucesso ao atualizar: ' . $id);

});

//delet deleta os dados no servidor (Delete), neste caso podemos passar um id como parâmetro, para saber qual dado sera atualizado. 
$app->delete('/postagens/remove/{id}', function(Request $request, Response $response){

    $id = $request->getAttribute('id');
    
    //Usando este mestodo, podemos deletar os dados em um banco de dados usando o delete, e no return poderiamos escrever uma menssagem de sucesso ao deletar.

    return $response->getBody()->write('Sucesso ao deletar: ' . $id);

});

$app->run();




