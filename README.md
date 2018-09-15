<p align="center"><a href="https://www.opencartbrasil.com.br/"><img src="https://forum.opencartbrasil.com.br/ext/sitesplat/flatbootsminicms/images/logo/logo-colorida.png" alt="OpenCart Brasil"></a>
</p>

<p align="center">
<a href="./LICENSE"><img src="https://poser.pugx.org/opencartbrasil/opencartbrasil/license" alt="Licença"></a> 
<a href="./CHANGELOG.md"><img src="https://poser.pugx.org/opencartbrasil/opencartbrasil/v/stable" alt="Última versão estável"></a>
<img src="https://img.shields.io/badge/opencart-3.0.2.0-blue.svg" alt="Compatibilidade">
</p>

### Apresentação

Bem-vindo ao repositório do projeto OpenCart Brasil no GitHub.

Aqui você pode analisar o código, relatar erros e enviar correções ou melhorias para o projeto.

O projeto OpenCart Brasil é um fork do OpenCart, que tem como objetivo atender lojistas brasileiros.

Recomendamos que todos os desenvolvedores sigam este repositório para ficarem atualizados sobre o projeto.

### Requisitos para instalação

**O PHP deve está configurado com os requisitos listados abaixo:**

- Versão do PHP: 5.6 ou superior
- Register Globals: Off
- Magic Quotes GPC: Off
- File Uploads: On
- Session Auto Start: Off

**As extensões do PHP listadas abaixo devem está instaladas:**

- MySQLi
- GD
- cURL
- OpenSSL
- ZLIB
- ZIP

**Importante:**

- Não utilize com sistema operacional Windows e servidor web IIS.
- Para montar um ambiente de homologação, você pode utilizar Windows e servidor web Apache 2 ou Nginx.
- O ambiente de homologação ideal é com uma distro Linux e servidor web Apache 2 ou Nginx.
- Em produção utilize apenas com distro Linux e servidor Apache 2 ou Nginx.
- Testado com banco de dados MySQL até a versão 5.7  
- Testado com banco de dados MariaDB até versão 10
- Testado com PHP até a versão 7.2

### Download do projeto via composer

Você pode baixar os arquivos do projeto utilizando o composer:

``composer create-project opencartbrasil/opencartbrasil nome_da_pasta``

Para instalar a versão em desenvolvimento utilizando o composer:

``composer create-project opencartbrasil/opencartbrasil nome_da_pasta dev-master``

### Instalação por linha de comando

Você pode instalar o projeto OpenCart Brasil via linha de comando.

Os seguintes parâmetros são necessários para utilizar o instalador via linha de comando:

- **db_driver**: driver para conexão com o banco de dados. O padrão é mysqli (não obrigatório)
- **db_hostname**: nome do servidor de banco de dados. O padrão é localhost (não obrigatório)
- **db_username**: usuário com permissão no banco de dados.
- **db_password**: senha do usuário com permissão no banco de dados.
- **db_database**: nome do banco de dados para instalar as tabelas da loja.
- **db_port**: porta de acesso ao banco de dados mysql. O padrão é 3306 (não obrigatório)
- **db_prefix**: prefixo das tabelas do banco de dados.  O padrão é oc_ (não obrigatório)
- **username**: usuário administrador da loja que será cadastrado. O padrão é admin (não obrigatório)
- **password**: senha do usuário administrador.
- **email**: e-mail do usuário administrador.
- **http_server**: domínio da loja com uma / (barra) no final.

Exemplo de instalação via linha de comando em um computador local:

``php install/cli_install.php install --db_hostname localhost --db_username root --db_password 123456 --db_database opencartbrasil --username admin --password 123456 --email usuario@dominio.com.br --http_server http://localhost/opencartbrasil/``

### Versionamento

Para o controle de versões, utilizamos as especificações de [Versionamento Semântico](https://semver.org/lang/pt-BR/spec/v2.0.0.html)

### Suporte

Este repositório não é adequado para fornecer suporte sobre a utilização do projeto OpenCart Brasil.

Por favor, só registre uma issue para relatar erros no núcleo do projeto OpenCart Brasil.

Para suporte relacionado a utilização do projeto OpenCart Brasil, utilize o nosso fórum:

https://forum.opencartbrasil.com.br/

### Contribuindo

Se você encontrou um erro no núcelo do projeto OpenCart Brasil e deseja nos relatar, você deve registrar uma Issue.

Se você tem uma correção ou melhoria e deseja nos enviar, faça um fork e nos envie um Pull request para avaliarmos.
