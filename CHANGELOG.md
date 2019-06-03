# Changelog
Todas as mudanças neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e utiliza [Semantic Versioning](https://semver.org/lang/pt-BR/spec/v2.0.0.html).

## [Não lançada]

## [1.3.4] (Data de lançamento: 03.06.2019)
### Corrigido
- Melhorias no processo de atualização
- Melhorias na tradução da administração
- Melhorias no layout da edição do pedido
- Correção na data de cadastro dos afiliados
- Correção no botão fechar do alerta no catálogo
- Correção no filtro por data de cadastro dos afiliados
- Indentação dos templates do catálogo na administração

### Adicionado
- Filtro por SKU do produto na administração
- Filtro de departamentos, atributos, opções, marcas, downloads e páginas de informações na administração

## [1.3.3] (Data de lançamento: 08.05.2019)
### Corrigido
- Melhoria na sintaxe do index.php
- Melhorias na identificação de eventos estatísticos
- Correção na API para hospedagem com proxy estranho

## [1.3.2] (Data de lançamento: 01.05.2019)
### Corrigido
- Várias correções ortográficas
- Correção no upgrade do banco de dados
- Correções e melhorias na estatística de comentários e devoluções

### Adicionado
- Validação da URL amigável no Editor de URL amigável

### Modificado
- Melhorias no gerenciamento da sessão
- Melhorias na instrução de atualização da versão

### Removido
- Remoção de chamadas de cache não utilizadas

## [1.3.1] (Data de lançamento: 16.04.2019)
### Corrigido
- Correção nos links para https
- Correção nas extensões Wordpay e Authorize.Net

### Modificado
- Extensão Google Shopping v111
- Melhorias na gestão da sessão
- Melhorias na library document
- Melhorias ao salvar o endereço na finalização
- Melhorias na tradução da extensão Google Shopping
- Melhorias no formulário de produtos na administração

## [1.3.0] (Data de lançamento: 03.04.2019)
### Corrigido
- Correção na edição do pedido
- Correções nas extensões klarna
- Correções em extensões estrangeiras
- Correção no layout da lista de países
- Correção no layout do gerenciador de cache
- Correção na utilização do cache de idiomas
- Correção para o estouro em tabelas responsivas
- Correção da extensão de frete fedex por @caiobernal
- Correção no botão para redefinir produtos visualizados
- Remoção de registro desnecessário da extensão OpenBay

### Adicionado
- Tarefas agendadas
- Suporte para limpeza de sessão por tarefa
- Adicionado no framework o header para limpeza de cache do navegador

### Modificado
- Melhorias na API catalog
- Melhorias no engine loader
- Melhorias no helper general
- Melhorias na footer.php catalog
- Melhorias no startup do catálogo
- Melhorias no tratamento da paginação
- Tradução das mensagens de erro no core
- Melhorias na atualização do diretório storage
- Melhorias na biblioteca de sessão por arquivo
- Melhoria na tradução do processo de instalação
- Melhorias na tradução da extensão Google Shopping
- Mudança no limite de itens exibidos na administração
- Adicionada a hora do pedido nas informações do pedido
- Melhoria na tradução do erro de conexão com o banco de dados

## [1.2.4] (Data de lançamento: 18.02.2019)
### Corrigido
- Correção na edição do pedido com afiliados

### Modificado
- Extensão Google Shopping v106

## [1.2.3] (Data de lançamento: 11.02.2019)
### Corrigido
- Correções na solicitação de nova senha do cliente na loja
- Correção no manuseio dos arquivos de sessão
- Correções na extensão de pagamento Wordpay

## [1.2.2] (Data de lançamento: 29.01.2019)
### Corrigido
- Correção na atualização da senha do cliente

## [1.2.1] (Data de lançamento: 26.01.2019)
### Corrigido
- Tradução do prefixo da fatura após a instalação
- Desabilitando a atualização automática da moeda no SQL de instalação
- Adição das permissões para acesso as extensões do tipo Propaganda no SQL de instalação

## [1.2.0] (Data de lançamento: 07.01.2019)
### Corrigido
- Correção no pagamento eWAY
- Correção na atualização da senha
- Melhorias no menu da administração
- Pequenas melhorias na segurança da API
- Correção no fechamento da conexão com o banco de dados

### Adicionado
- Compatível com OpenCart 3.0.3.1
- Extensão de propaganda Google Shopping v105
- Novo tipo de extensão para extensão de Propaganda

### Modificado
- Extensão OpenBay Pro v3250

## [1.1.5] (Data de lançamento: 22.12.2018)
### Corrigido
- Melhorias no SQL de instalação
- Melhorias na captura de IP pela API
- Melhoria no envio de arquivos anexados ao produto

### Adicionado
- Inclusão da coluna SKU nas opções do produto

## [1.1.4] (Data de lançamento: 24.11.2018)
### Corrigido
- Melhorias na tradução
- Correção no sistema de login do cliente

## [1.1.3] (Data de lançamento: 24.11.2018)
### Corrigido
- Remoção de espaços em branco
- Correção do Editor de Temas
- Correção no Core da API REST
- Correção no SQL de instalação
- Correção no checkout sem conta
- Correção na extensão para Sitemap
- Correção na extensão Google reCAPTCHA
- Correção no envio de e-mails administrativos
- Correção na extensão para Google Merchant Center
- Correção no e-mail de novo pedido para o lojista
- Melhorias nas mensagens de erro dos templates
- Melhorias nas traduções do formulário de contato
- Melhorias nas mensagens de erro do banco de dados
- Melhorias nas mensagens de erro do envio de e-mail
- Melhorias no envio do informativo pela administração
- Melhorias na recuperação da senha do cliente através da loja
- Melhorias na tradução das mensagens de erros nativas do motor
- Melhorias na tradução das mensagens de erros nativas do núcleo

### Modificado
- .htaccess
- php.ini

## [1.1.2] (Data de lançamento: 12.11.2018)
### Corrigido
- Remoção de espaços em branco
- Melhorias na tradução do pedido
- Melhorias na instalação do OpenCart
- Melhorias nas instruções para atualização
- Melhorias na captura de IP dos clientes online
- Correção na ordenação dos departamentos no admin
- Correção no contador de estatísticas ao deletar o pedido
- Correção no model de redimensionamento de imagens no admin
- Correção no model de redimensionamento de imagens no catalog

## [1.1.1] (Data de lançamento: 29.10.2018)
### Corrigido
- Melhorias no formulário de comentário
- Remoção de informações desnecessárias
- Correção na edição de opções do pedido
- Correção na edição de dados do cliente na loja
- Escondendo campos personalizados desabilitados no admin
- Correção nos campos personalizados do cliente e pedido no admin

### Adicionado
- Integração com Tiny ERP
- Adicionado no rodapé da administração a versão Brasil

## [1.1.0] (Data de lançamento: 02.10.2018)
### Corrigido
- Correção no sitemap
- Correção na biblioteca SMTP
- Correção na captura de erros
- Correção no checkout da loja
- Correção na situação do pedido
- Correção no nível de compressão
- Correção no sistema de pontos
- Correção no sistema de marketing
- Correção no sistema de afiliados
- Correção no sistema de donwloads
- Correção no sistema de transações
- Correção no sistema de estatística
- Correção na instalação do OpenCart
- Correção na extensão de frete grátis
- Correção nos cadastros da administração

### Modificado
- README.md
- LICENSE

### Adicionado
- Tema Básico
- Extensão para Frenet
- Sistema de atualização da versão

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

[Não lançada]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.3.4...HEAD
[1.3.4]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.3.3...v1.3.4
[1.3.3]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.3.2...v1.3.3
[1.3.2]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.3.1...v1.3.2
[1.3.1]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.3.0...v1.3.1
[1.3.0]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.2.4...v1.3.0
[1.2.4]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.2.3...v1.2.4
[1.2.3]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.2.2...v1.2.3
[1.2.2]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.2.1...v1.2.2
[1.2.1]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.1.5...v1.2.0
[1.1.5]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.1.4...v1.1.5
[1.1.4]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.1.3...v1.1.4
[1.1.3]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.1.2...v1.1.3
[1.1.2]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.6...v1.1.0
[1.0.6]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.5...v1.0.6
[1.0.5]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.4...v1.0.5
[1.0.4]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.3...v1.0.4
[1.0.3]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.2...v1.0.3
[1.0.2]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.0.0...v1.0.1
