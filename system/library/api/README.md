## Apresentação

A biblioteca API foi criada para facilitar o desenvolvimento de aplicações que necessitam fazer chamadas em APIs de terceiros.

A biblioteca usa o Client HTTP Guzzle para fazer as requisições simples, assíncronas e simultâneas.

É possível utilizar essa biblioteca de duas maneiras. A primeira é criar um arquivo de configuração no formato json (exemplo incluso na biblioteca). A segunda é instanciando direto na aplicação.

## Requisições simples

**Modo 1:**

Como a maioria das APIs estão utilizando o formato json tanto para consumir as requisições, como para a documentação, basta criar um arquivo json na pasta endpoints com base no modelo abaixo:

{
  "base_uri": {
    "production": "https://api.provider-example.com", // ambiente de produção (padrão)
    "sandbox": "https://sandbox.provider-example.com" // ambiente de testes
  },
  "endpoints": { // matriz com os endpoints que serão consumidos durante as chamadas de cada método
    "getPaymentId": { // método nomeado para fazer a chamada
      "endpoint": "payment/:id", // endpoint para consumir a requisição
      "method": "get", // verbo da requisição
      "assoc": "" // parâmetro para retornar uma matriz associativa. Pode ser preenchido com true ou deixar como vazio na chave. Se não for definido será retornado um objeto.
    },
    "getToken": {
      "endpoint": "oauth/token",
      "method": "post"
    },
    "creatPayment": {
      "endpoint": "payment/new",
      "method": "post"
    },
    "putStatus": {
      "endpoint": "payment/:id",
      "method": "put"
    }
  }
}

Após criar o arquivo, na sua aplicação basta instanciar a classe Endpoints:

$api = new \Api\Endpoints('endpoint-file-name', 'sandbox', ['timeout' => 60.0]); // nome do arquivo, tipo do ambiente, opções

Faça a requisição conforme o método configurado no arquivo

$result = $api->getPaymentId(['id' => 8]); // mapeamento inteligente do parâmetro id (https://sandbox.provider-example.com/payment/8)

**Inserindo cabeçalhos:**

$api->getPaymentId(['id' => 8, 'headers' => ['key' => 'value']]);

**Usando urls do tipo Query String:**

$api->getPaymentId(['id' => 8, 'headers' => ['key' => 'value'], 'query' => ['key' => 'value']]);

**Enviando dados do tipo json:**

$api->creatPayment([json => ['key' => 'value']]);

**ou:**

$api->creatPayment(['body' => json_encode($data)]);

**Enviando dados do tipo form (application/x-www-form-urlencoded):**

$api->creatPayment(['form_params' => ['key' => 'value']]);

///////////////////////////////////////////////////////////////////////////////////////////////////////////

**Modo 2:**
Você pode instanciar a Api direto na sua aplicação. Nesse caso será necessário passar o verbo e o endpoint para cada chamada.

Aqui não há o uso do método mágico call, será necessário fazer a chamada no método send da class.

$api = new \Api\Api('https://api.provider-example.com', ['timeout' => 60.0]);

$api->send('get', 'payments', ['headers' => ['key' => 'value']]);

## Requisições Assíncronas/Simultâneas:

$api = new \Api\Api('https://api.provider-example.com', ['timeout' => 60.0]);

$endpoints = ['onde', 'two', 'three'];

foreach ($endpoints as $endpoint) {
  $api->attach('get', $endpoint); // anexa cada requisição
}

**Assíncronas:**
$api->async(); // defina true no parâmetro para retornar uma matriz associativa para os resultados.

**Simultâneas:**
$api->multi(); // defina true no parâmetro para retornar uma matriz associativa para os resultados.

## Créditos:

Uma parte da ideia dessa biblioteca foi extraída da biblioteca SDK-PHP da Gerencianet. Extraí e fiz algumas melhorias em um trecho de código e apliquei nesse projeto.

Mais detalhes sobre o Client Guzzle aqui: http://docs.guzzlephp.org/en/stable/index.html
