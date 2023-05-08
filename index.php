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

/* Tipos de respostas
cabeçalho, texto, Json, XML
*/

// -- Como retornar um cabeçalho -- \\ 
//withHeader permite definir informações de cabeçalho, neste caso a informação que vamos definir, que é permitida neste caso, é o 'allow' que significa 'permitir, que será definida como 'PUT'.
$app->get('/header', function(Request $request, Response $response){
    //Também é permitido definir outros cabeçalhos, usando o withAddedHeader(), nesse caso vamos definir o tamanho do retorno 'Content-Length' para 10, que mostrara somente os 10 primerios caracteres.
    $response->write('Esse é um retorno header');   
    return $response->withHeader('allow', 'PUT')
    ->withAddedHeader('Content-Length', 10);                

});

//-- Como retornar um Jason --\\
//withJson retorna um array para um formato Json
$app->get('/json', function(Request $request, Response $response){
    
    return $response->withJson([
        "nome" => "Elson Nunes",
        "endereco" => "Antônio Virg..."
    ]);   
             
});

//-- Como retornar um xml --\\
//Para este exemplo eu criei um arquivo com nome arquivo, sem extenção definida na raiz do diretório, e dentro dele foi definido a seguite estrutura, vale resaltar que você pode salvar como extenção xml, não é obrigatorio, mas é uma opção. 
$app->get('/xml', function(Request $request, Response $response){
    //file-get-contents é uma função para acessar tipos de arquivos, e o parâmetro que passei é o arquivo que acabei de criar na raiz do projeto slim
    $xml = file_get_contents('arquivo');
    //return $response->write($xml);      //Neste caso ele vai retornar o conteudo, porem seu Contant-Type vai estar com html, e o retorno é um html
    //Para retornar de fato um xml devemos fazer tratamento no cabeçalho definido o tipo do conteúdo 'Content-Type' para aplicação xml 'application/xml'
    $response->write($xml);
    return $response->withHeader('Content-Type', 'application/xml');

});

//Na documentação slim você consegue ver mais informações a respeito de tipos header e exemplos, tanto para requiest quanto para response



/* middleware 
//middlewere basicamente adiciona camadas de códigos detro da aplicação, que executa antes da aplicação, exemplo, e possivel adicionar uma camada de código que faz a validação de uma autenticação antes de executar a aplicação, veja o exemplo abaixo.

//Criando um middleware, a função add() permite criar um middleware, neste caso o middlewere sera uma função anônima, e é necessario passar três parâmetros, sendo $requuest, $response e $next, os dois primeiros parâmetros já temos conhecimento, o o next, é para o proximo middleware.   

$app->add(function($request, $response, $next){
    //return $response->write('Inicio camada 1 + ');              Perceba que ao executar à rota /postagens este código é executado, ps: sem o return retorna um erro. Para que ele execute a rota /postagens é necessario usar o $next.  

    $response->write(' Inicio camada 1 + ');
    return $next($request, $response);                             //Agora com o $next o middleware vai direcionar para a proxima rota, que pode ser uma das duas logo à baixo /usuarios ou /postagens  
});  

/*É possivel usar mais de 1 meddleware 
//Quando executarmos, a primeira camada a ser executada é esta, e depois ele executa o de cima, pois é, a logiaca é assim mesmo.
//Resultado: "Inicio camada 2 + Inicio camada 1 + Ação principal usuarios" é usuarios poique eu usei a rota /usuarios 
$app->add(function($request, $response, $next){
    
    $response->write(' Inicio camada 2 + ');                       
    return $next($request, $response);                             

});     


//Também é possivel definir uma saida em middleware, para isso, basta remover o return, e fazer uma execução recursiva. 
$app->add(function($request, $response, $next){
    
    $response->write(' Inicio camada 1 + ');                                                    
    $response = $next($request, $response);     //Aqui é executada uma execução de uma das rotas a baixo, e salvo na variável $respose.  

    $response->write(' + Fim camada 1 ');       //Aque é reescrito no response 
    return $response;                              

}); 

//Também é possivel usar mais de um middleware com saida definida
$app->add(function($request, $response, $next){
    
    $response->write(' Inicio camada 2 + ');                                                    
    $response = $next($request, $response);     //Aqui é executada uma execução de uma das rotas a baixo, e salvo na variável $respose.  

    $response->write(' + Fim camada 2 ');       //Aque é reescrito no response 
    return $response;                              

}); 


$app->get('/usuarios', function(Request $request, Response $response){
    
    $response->write(' Ação principal usuarios ');
    

});

$app->get('/postagens', function(Request $request, Response $response){
    
    $response->write(' Ação principal postagens ');
    

});
*/

//Banco de dados 
//illuminate é quit de ferramentas para banco de dados, para instalar use o seguinte comando, caso o composer não esteja instalado de forma global "php composer.phar require illuminate/database", caso contrario é possivel instalar da seguite forma "composer require illuminate/database". 
//Qundo indtalado, temos que definir um name space para o banco de dados 
use Illuminate\Database\Capsule\Manager as Capsule; 

//Aqui estamos configurando um container 
$container  = $app->getContainer();
//E aqui estamos definindo um db, que é banco de dados, pode ser qualquer nome 
$container['db'] = function(){

    //E dentro desta função anônima é definida a configuração do banco de dados, onde instanciamos e configurando, para mais informações acesse a documentação em <<https://github.com/illuminate/database>>;
    $capsule = new Capsule;
    //Para esta configuração de conexão com banco de dados eu criei no phpmyadimim um banco dedados slim, então ele esta configurado conforme o meu db.
    $capsule->addConnection([
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'slim',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ]);

    $capsule->setAsGlobal();        //Faz com que a instância db, que é capsule seja global
    $capsule->bootEloquent();       //Ferramenta para fazer a comunicação com o banco de dados. 

    return $capsule;
};

//Agora vamos criar uma rota para nosso db
$app->get('/usuarios', function(Request $request, Response $response){

    $db = $this->get('db');                 //Existe 2 formas de acessar o banco de dados, uma delas é esta usando o get, ou esta '$db = $this->db;' 
    /*
    //schema é um metodo utilizado para fazer configurações do banco de dados ou aterações, dropIfExists() é um metodo usado para remover tabela caso ela exista
    $db->schema()->dropIfExists('usuarios');
    //cria uma tabela 
    $db->schema()->create('usuarios', function($table){
        //na tabela estamo adicionando uma coluna ou atributo com auto inclement, alem de outras colunas 
        $table->increments('id');
        $table->string('nome');
        $table->string('email');
        $table->timestamps();   //O timestamps cria dois campos, sendo o primeiro dado a data de cração e o segundo a data de alteração. 

    }); 
    */

    /*
    //Agora vamos fazer insert de dados, neste caso usaremos um array associativo 
    $db->table('usuarios')->insert([
        'nome' => 'Paulo Rosa',
        'email' => 'paulo@gmail.com'
    ]);      
    */

    /*
    //Agora vamos atualizar o bd
    $db->table('usuarios')
        ->where('id', 3)
        ->update([
            'nome' => 'Paulo Nunes'
        ]);
    */

    /*    
    //Deletar 
    $db->table('usuarios')
        ->where('id', 1)
        ->delete();
    */

    //Listar
    $usuarios = $db->table('usuarios')->get();
    foreach($usuarios as $usuario){
        echo $usuario->nome . '<br>';
    }

});



$app->run();

