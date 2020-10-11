# Changelog
Todas as mudanças neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e utiliza [Semantic Versioning](https://semver.org/lang/pt-BR/spec/v2.0.0.html).

## [Não lançada]

## [1.5.0] (Data de lançamento: 10.10.2020)
### Corrigido
- Correção ao excluir idioma no admin
- Correção no backup das tabelas no admin
- Correção na tradução do voucher no admin
- Correção na paginação de clientes no admin
- Correção ao carregar cache de idioma no admin
- Correção no breadcrumbs da tela de login no admin
- Correção na exibição do código da fatura no admin
- Correção na ordem de exibição dos impostos no admin
- Correção no pedido com pagamento recorrente no admin
- Correção no help do campo cliente confiável no admin
- Correção no layout da tabela de localizações no admin
- Correção no carregamento de model de voucher no admin
- Correção na tradução do envio de informativo no admin
- Correção de segurança no gerenciador de arquivos do admin
- Correção na exibição de extensões do tipo dashboard no admin
- Correção no tamanho do código do cupom para 20 caracteres no admin
- Correção no cadastro e edição de produtos com assinaturas no admin
- Correção no arquivo .htaccess
- Correção na edição do cliente no catalog
- Correção no cadastro do cliente no catalog
- Correção no cadastro de afiliado no catalog
- Correção na extensão Fraudlabs Pro no catalog
- Correção no formulário de devolução no catalog
- Correção no módulo que exibe produtos novos no catalog
- Correção no cadastro de endereço do cliente no catalog
- Correção no layout do e-mail com resumo do pedido no catalog
- Correção na validação do endereço na cotação de frete no catalog
- Reornganização do código em arquivos
- Remoção de espaços em branco em arquivos
- Remoção de model desnecessário nas marcas
- Remoção de histórico nulo após a atualização da situação do pedido
- Verificação de tipo inteiro em parâmetros recebidos por GET

### Modificado
- Melhoria no formulário de contato
- Melhoria no processo de instalação
- Melhoria na biblioteca de cache Redis
- Melhoria na extensão de frete por peso
- Melhoria no garbage collector da sessão
- Melhoria na leitura dos arquivos de cache
- Melhoria na validação do endereço no startup
- Melhoria na biblioteca SMTP para envio de e-mail
- Melhoria no botão configurações de desenvolvimento
- Melhoria no registro da conexão com o banco de dados
- Melhoria no envio de notificação para e-mail adicional
- Melhoria na validação durante a compra de vale presente
- Exibe imagem padrão no produto dentro do carrinho quando o produto não possuir imagem

### Removido
- Remoção da extensão Frenet
- Remoção da integração Tiny ERP

## [1.4.15] (Data de lançamento: 22.06.2020)
### Corrigido
- Melhoria no cadastro de produtos
- Melhoria no gerenciamento de cache por arquivo
- Melhoria na validação do cadastro de opções no admin
- Melhoria na validação do cadastro de marcas no admin
- Melhoria na validação do cadastro de filtros no admin
- Melhoria na validação do cadastro de downloads no admin
- Melhoria na validação do cadastro de atributos no admin
- Melhoria na validação do cadastro de comentários no admin
- Melhoria na validação do cadastro de grupos de atributos no admin
- Correções na aprovação de afiliados
- Correções no e-mail de aprovação de clientes
- Correção na validação por regex dos campos personalizados
- Correção no envio do e-mail de notificação de novo pedido
- Correção no envio do e-mail de notificação de novo cliente
- Correção no envio do e-mail de notificação de novo afiliado

### Adicionado
- Filtro na listagem de filtros no admin

## [1.4.14] (Data de lançamento: 13.06.2020)
### Corrigido
- Melhoria na importação do backup
- Melhoria no gerenciamento de cache por arquivo
- Correção na exibição do número da fatura na administração
- Correção na listagem de extensões por desenvolvedor no Marketplace

## [1.4.13] (Data de lançamento: 01.06.2020)
### Corrigido
- Correção para extensão Openbay
- Correção no módulo dashboard chart
- Correção no e-mail de afiliado aprovado
- Correção de bug no editor Summernote @redbraz
- Correção no nome da função para deletar sessão da API
- Correção nas configurações da extensão depósito bancário
- Correção no link para login no e-mail de afiliado aprovado
- Correção na permissão de acesso a tela do procedimento de segurança @condor2
- Correção na seleção de endereço quando o cliente realiza o login no checkout @condor2
- Melhoria na segurança da paginação @condor2
- Melhorias e correções na instalação do OpenCart

### Modificado
- Atualização da extensão para Frenet
- Atualização no stylesheet.css do admin
- Atualização no stylesheet.css do install
- Atualização da integração com a Tiny ERP
- Novo endpoint para verificação do IP do cliente
- Remoção do AddThis por problemas com cookie e lentidão no carregamento

### Adicionado
- Compatível com OpenCart 3.0.3.3

## [1.4.12] (Data de lançamento: 04.05.2020)
### Corrigido
- Correção no .htaccess
- Correção na exibição das avaliações
- Correção no módulo Opções de filtro
- Correção na extensão de antifraude por IP
- Correção no erro de favicon na administração
- Correção no carregamento da biblioteca swipper
- Melhoria na exclusão de extensões
- Melhoria no gerenciador de arquivos
- Remoção da formatação de mensagens de erro do PHP
- Remoção de chamada model duplicada no módulo Carrossel de imagens

### Adicionado
- Suporte para CDN no tema da administração
- Atualização da biblioteca jQuery para versão 2.2.4 no tema default, basico e administração

## [1.4.11] (Data de lançamento: 05.04.2020)
### Corrigido
- Correção na renovação da sessão ao logar e editar dados do cliente na loja

## [1.4.10] (Data de lançamento: 25.02.2020)
### Corrigido
- Biblioteca de sessão corrigida, otimizada e segura para tráfegos pequenos a grandes

### Adicionado
- Novo session_id com 32 caracteres
- Gerenciamento de cookies de sessão
- Cookies HTTPOnly por padrão
- Cookies Secure quando o HTTPS é detectado
- Cookie SameSite quando PHP 7.3 ou superior
- Suporte para coletor de lixo com atraso na execução
- Suporte para verificar se o novo session_id já está em uso, se estiver gera um novo e exclusivo
- Suporte para regenerar o session_id quando: login, alteração de senha e edição de dados
- Suporte para limpar a sessão e invalidar o cookie ao fazer logoff

## [1.4.9] (Data de lançamento: 12.02.2020)
### Corrigido
- Melhorias no login
- Melhorias ao cadastrar filtros
- Melhorias ao cadastrar atributos
- Correção no campo sort_order do subtotal
- Informação sobre a moeda padrão na listagem de moedas
- Suporte para exibir a imagem do vale presentes do carrinho

## [1.4.8] (Data de lançamento: 31.12.2019)
### Corrigido
- Correção nas configurações da loja
- Correção ao vincular assinaturas no produto
- Correção no layout de cadastro dos produtos
- Correção na tradução das configurações da loja
- Correção na exibição das tags do produto na loja
- Correção no fechamento de tags de comentário HTML
- Correção no cadastro de afiliados na administração
- Correção na biblioteca de sessão por banco de dados
- Correção na integração com o marketplace do OpenCart
- Correção no layout e tradução do procedimento de segurança
- Indentação do tema da instalação

### Adicionado
- Atualizações para compatibilização com PHP 7.4

## [1.4.7] (Data de lançamento: 19.12.2019)
### Corrigido
- Correção no filtro de produtos na administração
- Correção no formulário de cadastro do cliente na administração
- Melhorias no cadastro de idiomas

### Adicionado
- Suporte para exibir/ocultar a password

## [1.4.6] (Data de lançamento: 06.12.2019)
### Corrigido
- Correção no menu da administração
- Correção na remoção de filtros na loja
- Correção no Editor de traduções da administração
- Correção na tradução dos comentários no marketplace
- Correção no slideshow para quando só tiver um banner
- Correção na busca de produtos com carga duplicada de model
- Correção no filtro de colunas das assinaturas na administração
- Correção no login do cliente pela administração com multilojas
- Correção na validação de país no cadastro do cliente na administração
- Melhorias no instalador de extensões
- Melhoria na aplicação do cupom de desconto
- Melhorias na validação do Google reCAPTCHA V2
- Melhoria no filtro de produtos na administração

## [1.4.5] (Data de lançamento: 05.11.2019)
### Corrigido
- Correção na extensão LiqPay
- Correção na gestão de devoluções
- Correção na listagem de localizações
- Correção no placeholder do option_form.twig
- Correção na listagem das campanhas de marketing
- Correção de bug na instalação através do console
- Melhorias na edição de campos personalizados no admin
- Atualização de referências no carousel.twig e slideshow.twig
- Remoção de parâmetro desnecessário na função que carrega as lojas

## [1.4.4] (Data de lançamento: 26.10.2019)
### Corrigido
- Correção na extensão UPS
- Correção na extensão Square
- Correção na extensão Amazon Pay
- Correção na tradução do captcha
- Correção no CSS do alert no admin
- Correção na listagem de localizações
- Correção no formulário de localização
- Correção nas extensões PayPoint e Worldpay
- Correção nos termos de afiliado e devolução
- Correção na paginação de históricos do cupom
- Correção no e-mail de cadastro do cliente e afiliado
- Correção na visualização de pedidos na administração
- Correção na visualização de afiliados na administração
- Correções nas extensões LIQPAY, First Data e Cardinity
- Correção para o estouro em tabelas responsivas mobile
- Correção na mensagem que exibe a contagem de informativos enviados
- Melhoria na busca por nome do produto no admin @rafaelmarrichi
- Melhoria na biblioteca de sessão em banco de dados
- Melhorias nos templates do menu marketing no admin
- Melhorias no gerenciamento de downloads
- Melhorias no gerenciamento de idiomas
- Melhorias nas consultas SQL
- Melhorias no tiny.api.php
- Melhoria no php.ini
- Atualização do extension.php

### Adicionado
- Inclusão dos campos SKU e NCM controller e model no catalog
- Novo filtro de pedidos com ou sem comentários
- Novo campo CEST no cadastro do produto
- Novo campo NCM no cadastro do produto

## [1.4.3] (Data de lançamento: 16.07.2019)
### Corrigido
- Melhorias e correções na posição dos campos personalizados
- Correção no carregamento do bootstrap.min.js

## [1.4.2] (Data de lançamento: 12.07.2019)
### Corrigido
- Correção nos relatórios de produtos visualizados, palavras pesquisadas e atividades por clientes
- Correção na ordem de exibição dos campos personalizados
- Melhoria no breadcrumb para dispositivos móveis
- Otimização do tema default para SEO
- Correção na descrição dos layouts

### Adicionado
- Suporte para adicionar meta tags robots por página

## [1.4.1] (Data de lançamento: 27.06.2019)
### Corrigido
- Correção para o bug ao salvar cadastros na administração
*Limpe o cache do navegador para apagar os arquivos javascript em cache

## [1.4.0] (Data de lançamento: 24.06.2019)
### Corrigido
- Melhorias no layout do cadastro de cliente e produto na administração
- Melhorias no layout do pedido para mobile na administração
- Melhorias no layout do pedido na administração da loja
- Melhorias no layout do gerenciador de extensões
- Melhorias no layout dos filtros para mobile

### Adicionado
- Suporte para extensões do tipo conversor de moedas

## [1.3.4] (Data de lançamento: 03.06.2019)
### Corrigido
- Correção na data de cadastro dos afiliados
- Correção no botão fechar do alerta no catálogo
- Correção no filtro por data de cadastro dos afiliados
- Indentação dos templates do catálogo na administração

### Adicionado
- Filtro por SKU do produto na administração
- Filtro de departamentos, atributos, opções, marcas, downloads e páginas de informações na administração

### Modificado
- Bootstrap v3.3.7
- Melhorias no processo de atualização
- Melhorias na tradução da administração
- Melhorias no layout da edição do pedido

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

[Não lançada]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.5.0...HEAD
[1.5.0]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.15...v1.5.0
[1.4.15]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.14...v1.4.15
[1.4.14]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.13...v1.4.14
[1.4.13]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.12...v1.4.13
[1.4.12]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.11...v1.4.12
[1.4.11]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.10...v1.4.11
[1.4.10]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.9...v1.4.10
[1.4.9]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.8...v1.4.9
[1.4.8]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.7...v1.4.8
[1.4.7]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.6...v1.4.7
[1.4.6]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.5...v1.4.6
[1.4.5]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.4...v1.4.5
[1.4.4]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.3...v1.4.4
[1.4.3]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.2...v1.4.3
[1.4.2]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.1...v1.4.2
[1.4.1]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.4.0...v1.4.1
[1.4.0]: https://github.com/opencartbrasil/opencartbrasil/compare/v1.3.4...v1.4.0
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
