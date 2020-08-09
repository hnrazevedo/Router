# Router @HnrAzevedo

[![Maintainer](https://img.shields.io/badge/maintainer-@hnrazevedo-blue?style=flat-square)](https://github.com/hnrazevedo)
[![Latest Version](https://img.shields.io/github/v/tag/hnrazevedo/Router?label=version&style=flat-square)](Release)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/hnrazevedo/Router?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Router/?branch=master)
[![Build Status](https://img.shields.io/scrutinizer/build/g/hnrazevedo/Router?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Router/build-status/master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/hnrazevedo/Router?style=flat-square)](https://packagist.org/packages/hnrazevedo/Router)
[![Total Downloads](https://img.shields.io/packagist/dt/hnrazevedo/Router?style=flat-square)](https://packagist.org/packages/hnrazevedo/Router)

## Falta implementar a interface do controller. - Ainda não está utilizável

###### Router is a simple friendly URL abstractor. Its author is not a professional in the development area, just someone in the Technology area who is improving his knowledge.

O Vadalitor é um simples abstrator de URL amigável. Seu autor não é profissional da área de desenvolvimento, apenas alguem da área de Tecnologia que está aperfeiçoando seus conhecimentos.

### Highlights

- Easy to set up (Fácil de configurar)
- Simple controller interface (Interface de controlador simples)
- Composer ready (Pronto para o composer)

## Installation

Router is available via Composer:

```bash 
"hnrazevedo/router": "^1.0"
```

or run

```bash
composer require hnrazevedo/router
```

## Documentation

###### For details on how to use the Router, see the sample folder with details in the component directory

Para mais detalhes sobre como usar o Router, veja a pasta de exemplos com detalhes no diretório do componente

#### Configure

#### It is necessary to configure the storage directory of the routes
É necessário configurar o diretório de armazenamento das rotas

```php
define("ROUTER_CONFIG", [
    "path" => "/Routes/"
]);
```

#### errors

#### In cases of configuration errors or nonexistent pages, the Router will throw an Exception.
Em casos de erros de configuração ou páginas inexistentes, o Router disparara uma Exception.

#### Router methods

#### Available methods

- get: URL access or get method
- post: post method
- ajax: called fetch or XMLHttpRequest
- form*: to be implemented globally

#### The routes must be set in a flat file without classes, as they will be imported when creating the object
As rotas devem ser setadas num arquivo simples sem classes, pois seram importadas na criação do objeto

```php
use HnrAzevedo\Router\Router;

Router::get('/','Application:index');

Router::get('/{controller}/{method}','{controller}:{method}');
```

#### run route

```php
use HnrAzevedo\Router\Router;

/* NOTE: in case of error an exception is thrown */
Router::create()->dispatch();
```

## Support

###### Security: If you discover any security related issues, please email hnrazevedo@gmail.com instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para hnrazevedo@gmail.com em vez de usar o rastreador de problemas.

## Credits

- [Henri Azevedo](https://github.com/hnrazevedo) (Developer)
- [Robson V. Leite](https://github.com/robsonvleite) (Readme based on your datalayer design)

## License

The MIT License (MIT). Please see [License File](https://github.com/hnrazevedo/Validator/blob/master/LICENSE.md) for more information.