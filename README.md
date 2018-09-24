<p align="center"><a href="https://www.opencartbrasil.com.br/"><img src="https://forum.opencartbrasil.com.br/ext/sitesplat/flatbootsminicms/images/logo/logo-colorida.png" alt="OpenCart Brasil"></a>
</p>

<p align="center">
<a href="https://github.com/opencartbrasil/opencartbrasil/releases"><img src="https://img.shields.io/github/downloads/opencartbrasil/opencartbrasil/total.svg"></a>
<a href="./CHANGELOG.md"><img src="https://img.shields.io/github/release/opencartbrasil/opencartbrasil.svg" alt="Última versão"></a>
<img src="https://img.shields.io/badge/opencart-3.0.2.0-blue.svg" alt="Compatibilidade">
<a href="./LICENSE"><img src="https://img.shields.io/github/license/opencartbrasil/opencartbrasil.svg" alt="Licença"></a>
</p>

## Apresentação

O projeto OpenCart Brasil é um fork do OpenCart, que tem como objetivo atender lojistas brasileiros sempre mantendo a compatibilidade com a última versão estável do OpenCart.

Aqui você poderá analisar o código, relatar erros e enviar correções ou melhorias para o projeto.

Recomendamos que todos os desenvolvedores sigam este repositório para ficarem atualizados sobre o projeto.

## Imagens

### Início da instalação:

<img src="https://www.opencartbrasil.com.br/image/catalog/teste-drive/install.png">

### Página principal da loja:

<img src="https://www.opencartbrasil.com.br/image/catalog/teste-drive/front-3.0.2.0.png">

### Painel de controle administrativo:

<img src="https://www.opencartbrasil.com.br/image/catalog/teste-drive/back-3.0.2.0.png">

## Diferenciais

- Melhorias no processo de download e instalação.
- Correção de bugs, melhorias e otimizações no código.
- O processo de instalação ocorre em português do Brasil.
- Ferramenta de instalação por linha de comando em português do Brasil.
- Após a instalação, a loja já estará em português do Brasil com a moeda Real.
- Todos os dados de demonstração e dados auxiliares já estão em português do Brasil.

## Roteiro

- [ ] Opções relacionadas nos produtos.
- [ ] Campo SKU nas opções dos produtos.
- [ ] Cadastro de clientes padrão Brasil.
- [ ] Tema versão Brasil 100% customizado.
- [ ] Extensões integradas com serviços brasileiros.
- [ ] API para integração com sistemas externos como ERP, CRM, etc.

## Requisitos

### ⚠ Aviso:

Se o seu serviço de hospedagem não lhe oferece versões atualizadas dos softwares citados abaixo, troque de serviço de hospedagem, pois sua loja não pode ficar exposta por causa das falhas de segurança existentes em softwares antigos.

Se você é o profissional que administra os servidores que armazenam os arquivos e dados da loja, não utilize os softwares descritos abaixo em versões legadas. No mínimo utilize as versões mínimas recomendadas.

### Servidores web compatíveis:

- Apache 2.4 ou superior.
- Nginx 1.14 ou superior.

### OpenSLL:

- 1.0.1c ou superior.

### cURL:

- 7.34.0 ou superior.

### Bancos de dados compatíveis:

- MySQL 5.5 ou superior.
- MariaDB 5.5 ou superior.

### Versões do PHP compatíveis:

- 5.6 ou superior.

### Configurações necessárias no PHP:

| Diretiva | Valor |
| -------- | ----- |
| `register_globals` | Off |
| `magic_quotes_gpc` | Off |
| `file_uploads` | On |
| `allow_url_fopen` | On |
| `open_basedir` | none |
| `default_charset` | UTF-8 |
| `session.auto_start` | Off |
| `session.use_only_cookies` | On |
| `session.use_trans_sid` | Off |
| `session.cookie_httponly` | On |
| `session.gc_maxlifetime` | 3600 |

### Extensões necessárias no PHP:

- cURL
- Fileinfo
- GD
- MySQLi
- OpenSSL
- ZLIB
- ZIP

### ⚠ Notas:

Não é compatível com sistema operacional Windows utilizando servidor web IIS.

Em breve a versão mínima do PHP será 7.1, pois o suporte para PHP até 7.0 encerrará em dezembro de 2018.

## Download

### Manual através do site:

Faça o download da última versão estável através de nosso site [clicando aqui](https://www.opencartbrasil.com.br/download).

### Manual através do repositório:

Faça o download da última versão estável marcada como **latest release** [clicando aqui](https://github.com/opencartbrasil/opencartbrasil/releases/).

### Automático utilizando o composer:

``
composer create-project opencartbrasil/opencartbrasil nome_da_pasta
``

### Automático utilizando o Git Bash:

``
git clone https://github.com/opencartbrasil/opencartbrasil.git
``

## Instalação

### ⚠ Preparativos:

1. Crie um banco de dados no MySQL para uso da loja.
2. Crie um usuário no MySQL para uso da loja. **Atenção:** Em produção utilize um usuário exclusivo para a loja.
3. Adicione no usuário as permissões de acesso ao banco de dados da loja.

### Semi-automática através do navegador:

1. Extraia o conteúdo do arquivo que você baixou deste repositório para o servidor em que você irá instalar o projeto OpenCart Brasil.
2. Renomeie os arquivos **config_dist.php** e admin/**config_dist.php** para **config.php**.
3. Em ambiente Linux, a permissão incial de todos os arquivos deve ser **644** e de todas as pastas **755**.
3. Através do seu navegador, acesse o domínio onde estão os arquivos do projeto OpenCart Brasil para iniciar a instalação.

### Automática por linha de comando:

Você pode instalar o projeto OpenCart Brasil via linha de comando.

Os seguintes parâmetros são necessários para utilizar o instalador via linha de comando:

| Parâmetro | Descrição | Padrão | Obrigatório |
| --------- | --------- | ------ | ----------- |
| `db_driver` | Driver para conexão com o banco de dados. | mysqli | Não |
| `db_hostname` | Nome do servidor de banco de dados. | localhost | Não |
| `db_username` | Usuário com permissão para o banco de dados. | | Sim |
| `db_password` | Senha do usuário com permissão para o banco de dados. | | Sim |
| `db_database` | Nome do banco de dados para instalar as tabelas da loja. | | Sim |
| `db_port` | Porta para acesso ao banco de dados MySQL. | 3306 | Não |
| `db_prefix` | Prefixo adicionado nas tabelas criadas no banco de dados. | oc_ | Não |
| `username` | Usuário administrador da loja que será cadastrado durante a instalação. | admin | Não |
| `password` | Senha do usuário administrador da loja. | | Sim |
| `email` | E-mail do usuário administrador da loja. | | Sim |
| `http_server` | Domínio da loja com uma / (barra) no final. | | Sim |

**Exemplo de instalação no servidor local:**

``
php install/cli_install.php install --db_hostname localhost --db_username root --db_password 123456 --db_database opencartbrasil --username admin --password 123456 --email usuario@dominio.com.br --http_server http://localhost/opencartbrasil/
``

## Configurações adicionais

### Caso utilize o servidor web Nginx:

Adicione no arquivo **nginx.conf** dentro do bloco "**location / { }**":

```
  location ~ (?i)((\.twig|\.tpl|\.ini|\.log|(?<!robots)\.txt)) { deny all; }

  rewrite ^/sitemap.xml$ /index.php?route=extension/feed/google_sitemap last;
  rewrite ^/googlebase.xml$ /index.php?route=extension/feed/google_base last;
  rewrite ^/system/storage/(.*) /index.php?route=error/not_found last;

  if (!-f $request_filename) { set $rule_0 1$rule_0; }
  if (!-d $request_filename){ set $rule_0 2$rule_0; }
  if ($uri !~ ".*.(ico|gif|jpg|jpeg|png|js|css)"){ set $rule_0 3$rule_0; }
  if ($rule_0 = "321"){ rewrite ^/([^?]*) /index.php?_route_=$1 last; }
```

## Versionamento

Para o controle de versões utilizamos as especificações de [Versionamento Semântico](https://semver.org/lang/pt-BR/spec/v2.0.0.html)

## Suporte

Este repositório não é adequado para fornecer suporte sobre a utilização do projeto OpenCart Brasil.

Só registre uma Issue para relatar erros no núcleo do projeto OpenCart Brasil.

Para suporte relacionado sobre a utilização do projeto OpenCart Brasil, utilize o nosso fórum:

https://forum.opencartbrasil.com.br/

## Contribuindo

Se você encontrou um erro no núcleo do projeto OpenCart Brasil e deseja nos relatar, você deve registrar uma Issue.

Se você tem uma correção ou melhoria e deseja nos enviar, faça um fork e nos envie um Pull request para avaliarmos.

## Vulnerabilidades

Se você descobrir uma vulnerabilidade de segurança no projeto OpenCart Brasil, envie um e-mail para [dev@opencartbrasil.com.br](mailto:dev@opencartbrasil.com.br). Todas as vulnerabilidades informadas serão imediatamente tratadas caso confirmadas.

## Licença

O projeto OpenCart Brasil é um software de código aberto licenciado sob a [GPL v3](./LICENSE).
