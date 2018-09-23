# Changelog
Todas as mudanças neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e utiliza [Semantic Versioning](https://semver.org/lang/pt-BR/spec/v2.0.0.html).

## [Não lançada]

## [1.0.6] (Data de lançamento: 23.09.2018)
### Corrigido
- Correção na exibição das estrelas de avaliação do módulo de Produtos em destaque. @leandrorppo
- Correção na URL de login enviada por e-mail para clientes aprovados.
- Correção na extensão de pagamento PayPal Express Checkout.
- Correção em várias extensões de pagamento estrangeiras.
- Correção no módulo Botão PayPal Express Checkout.
- Correção na limpeza do cache do tema.
- Correção no arquivo: .htaccess
- Correção no arquivo: php.ini
- Correção no install.
- Correção no upgrade.

### Modificado
- Desabilitando o cache nas requisições Ajax GET do checkout.
- Adicionado a moeda Real no Google Merchant Center.

## [1.0.5] (Data de lançamento: 18.09.2018)
### Corrigido
- Correção na edição dos pedidos na administração.
- Correção no cadastro da região Brasil.

## [1.0.4] (Data de lançamento: 17.09.2018)
### Corrigido
- Correção no layout do e-mail de novo pedido.
- Correção no envio de e-mail por SMTP.
- Correção no gerenciamento de sessões.
- Correção no startup sass do catalog.
- Correção no formulário de produtos.
- Correção no gerenciamento de cache.
- Correção na biblioteca do carrinho.
- Correção no formulário de contato.
- Correção no startup sass do admin.
- Correção na biblioteca de sessão.
- Correção no arquivos config.
- Correção pp_standard.
- Correção no carrinho.
- Correção no install.
- Correção na API.

### Modificado
- .htaccess
- README.md
- autoload
- vendor

### Adicionado
- Visualização da quantidade de produtos na lista de desejos dentro da conta. @leandrorppo
- Captura de erros ao enviar e-mail.

## [1.0.3] (Data de lançamento: 16.09.2018)
### Corrigido
- Correção no instalador via linha de comando.
- Correção no instalador via navegador.

### Modificado
- README.md

### Adicionado
- .gitattributes

## [1.0.2] (Data de lançamento: 15.09.2018)
### Corrigido
- Correção no instalador via linha de comando.
- Correção na conta do cliente.

### Modificado
- composer.json
- README.md

### Adicionado
- .htaccess

## [1.0.1] (Data de lançamento: 14.09.2018)
### Corrigido
- Remoção de atributos, grupos de atributos, países e estados https://github.com/opencartbrasil/opencartbrasil/issues/1
- Correção na extensão de pagamento PayPal Express Checkout.
- Correção na API do catálogo.

### Modificado
- README.md

### Adicionado
- CODE_OF_CONDUCT.md
- CONTRIBUTING.md
- composer.json
- CHANGELOG.md

### Removido
- Suporte para sql_mode MYSQL40 que está obsoleto.

## 1.0.0 (Data de lançamento: 08.09.2018)
### Corrigido
- Correção de 10 bugs relatados em: https://forum.opencartbrasil.com.br/viewtopic.php?f=105&t=17361
- Correção na biblioteca: mysqli.php
- Correção no install.

### Modificado
- Install do OpenCart em português do Brasil.

### Adicionado
- Tradução para português do Brasil cadastrada e selecionada como padrão.
- O CEP está marcado como obrigatório no cadastro do país Brasil.
- Dados auxiliares traduzidos para o português do Brasil.
- Moeda Real cadastrada e selecionada como padrão.
- Configurações da loja em português do Brasil.
- O Brasil está selecionado como país padrão.
- Bibliotecas: pgsql.php e pdo.php

### Removido
- Bibliotecas: mpdo.php, mssql.php, mysql.php, postgre.php
- Cupons de descontos para demonstração.
- Moedas: Euro e Libra esterlina.

[Não lançada]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.6...HEAD
[1.0.6]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.5...v1.0.6
[1.0.5]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.4...v1.0.5
[1.0.4]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.3...v1.0.4
[1.0.3]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.2...v1.0.3
[1.0.2]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.0...v1.0.1
