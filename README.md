![version][opencart-badge] [![version][opencartbrasil-badge]][CHANGELOG] [![license][licenca-badge]][LICENSE]

### Apresentação

A plataforma OpenCart Brasil é um fork da plataforma OpenCart com recursos para lojistas brasileiros. https://www.opencartbrasil.com.br/

### Requisitos para instalação

**Certifique-se que o PHP está configurado de acordo com os requisitos listados abaixo:**

- Versão do PHP: 5.6 ou superior
- Register Globals: Off
- Magic Quotes GPC: Off
- File Uploads: On
- Session Auto Start: Off

**Certifique-se que as extensões do PHP listadas abaixo estão instaladas:**

- MySQLi
- GD
- cURL
- OpenSSL
- ZLIB
- ZIP

**Importante:**

- Não utilize com sistema operacional Windows e servidor web IIS.
- No caso de ambiente para homologação, você pode utilizar Windows e servidor web Apache 2.
- O ambiente de homologação ideal é com uma distro Linux e servidor web Apache 2 ou Nginx.
- Em produção utilize apenas com distro Linux e servidor Apache 2 ou Nginx.
- Testado com banco de dados MySQL até a versão 5.7  
- Testado com banco de dados MariaDB até versão 10
- Testado com PHP até a versão 7.2.x

[opencart-badge]: https://img.shields.io/badge/opencart-3.0.2.0-blue.svg
[opencartbrasil-badge]: https://img.shields.io/badge/opencartbrasil-1.0.1-blue.svg
[CHANGELOG]: ./CHANGELOG.md
[licenca-badge]: https://img.shields.io/badge/licença-GPLv3-blue.svg
[LICENSE]: ./LICENSE
