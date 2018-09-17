# Changelog
Todas as mudanças neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e utiliza [Semantic Versioning](https://semver.org/lang/pt-BR/spec/v2.0.0.html).

## [Não lançada]

## [1.0.4] (Data de lançamento: 17.09.2018)
### Corrigido
- Correção no layout do e-mail de novo pedido
- Correções no envio de e-mail por SMTP
- Correções no gerenciamento de sessões
- Correção na biblioteca de sessão
- Correções no gerenciamento de cache
- Correção no startup sass do admin
- Correção no startup sass do catalog
- Pequenos ajustes nos arquivos config
- Implementação no install
- Correções no formulário de produtos
- Correção pp_standard
- Correção na biblioteca do carrinho
- Correção no formulário de contato
- Melhoria no carrinho
- Correção na API

### Modificado
- .htaccess
- README.md
- vendor
- autoload

### Adicionado
- Exibindo quantidade de produtos na lista de desejos
- Captura de erros ao enviar e-mail

## [1.0.3] (Data de lançamento: 16.09.2018)
### Corrigido
- Correção no instalador via navegador
- Correção no instalador via linha de comando

### Modificado
- README.md

### Adicionado
- .gitattributes

## [1.0.2] (Data de lançamento: 15.09.2018)
### Corrigido
- Correção no instalador via linha de comando
- Correções na conta do cliente

### Modificado
- README.md
- composer.json

### Adicionado
- .htaccess

## [1.0.1] (Data de lançamento: 14.09.2018)
### Corrigido
- Remoção de atributos, grupos de atributos, países e estados https://github.com/opencartbrasil/opencartbrasil/issues/1
- PayPal Express Checkout.
- API do catálogo.

### Modificado
- README.md

### Adicionado
- composer.json
- CHANGELOG.md
- CONTRIBUTING.md
- CODE_OF_CONDUCT.md

### Removido
- Suporte para sql_mode MYSQL40 que está obsoleto.

## 1.0.0 (Data de lançamento: 08.09.2018)
### Corrigido
- Instalador do OpenCart.
- Biblioteca mysqli.php.
- 10 bugs relatados em: https://forum.opencartbrasil.com.br/viewtopic.php?f=105&t=17361

### Modificado
- Instalador do OpenCart em português do Brasil.

### Adicionado
- Tradução para português do Brasil cadastrada e selecionada como padrão.
- O CEP está marcado como obrigatório no cadastro do país Brasil.
- Dados auxiliares traduzidos para o português do Brasil.
- Moeda Real cadastrada e selecionada como padrão.
- Configurações da loja em português do Brasil.
- O Brasil está selecionado como país padrão.
- Bibliotecas pgsql.php e pdo.php.

### Removido
- Moeda Euro e Libra esterlina.
- Cupons de descontos para demonstração.
- Bibliotecas mpdo.php, mssql.php, mysql.php, postgre.php

[Não lançada]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.4...HEAD
[1.0.4]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.3...v1.0.4
[1.0.3]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.2...v1.0.3
[1.0.2]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.0...v1.0.1
