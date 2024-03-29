﻿# Router @HnrAzevedo

[![Maintainer](https://img.shields.io/badge/maintainer-@hnrazevedo-blue?style=flat-square)](https://github.com/hnrazevedo)
[![Latest Version](https://img.shields.io/github/v/tag/hnrazevedo/Router?label=version&style=flat-square)](https://github.com/hnrazevedo/Router/releases)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/hnrazevedo/Router?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Router/?branch=master)
[![Build Status](https://img.shields.io/scrutinizer/build/g/hnrazevedo/Router?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Router/build-status/master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/hnrazevedo/Router?style=flat-square)](https://packagist.org/packages/hnrazevedo/Router)
[![Total Downloads](https://img.shields.io/packagist/dt/hnrazevedo/Router?style=flat-square)](https://packagist.org/packages/hnrazevedo/Router)

##### Router is a simple friendly URL abstractor. It can be used in an easy and practical way, either individually in a static way, or together as a middleware and now as an attribute with PHP 8. Its author is not a professional in the development area, just someone in the Technology area who is improving their knowledge.

O Router é um simples abstrator de URL amigável. Ele pode ser utilizada de maneira fácil e prática, tanto individualmente de forma estática, quanto em conjunto como middleware e agora como atributo com o PHP 8. Seu autor não é um profissional da área de desenvolvimento, apenas alguem da área de Tecnologia que está aperfeiçoando seus conhecimentos.

## Highlights

- Easy to set up (Fácil de configurar)
- Easy information caching (Fácil cacheamento de informações)
- Follows standard PSR-15 (Segue padrão o PSR-15)
- Composer ready (Pronto para o composer)

## Installation

Router is available via composer.json:

```bash 
"hnrazevedo/router": "^2.4" # PHP <= 7.4
"hnrazevedo/router": "^3.0" # PHP >= 8.0
```

or in at terminal

```bash
composer require hnrazevedo/router
```

## Configure server

### Nginx

#### nginx.conf
```
location / {
    index index.php;
    try_files $uri  /index.php$is_args$args;
}
```
### Apache

#### .htaccess
```
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews
        Options -Indexes
    </IfModule>

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]

    <FilesMatch "index.php">
        allow from all
    </FilesMatch>

</IfModule>
```

## Documentation

#### For more details on the use and configuration of the Router, see the example folder with details on component targeting

Para mais detalhes sobre a utilização e configuração do Router, veja a pasta de exemplos com detalhes no diretório do componente

### Errors

#### In the static use of the Router, if an inexistent page error is returned, an Exception will be thrown
#### When used as middleware, a 404 response is returned

Na utilização estática do Router, caso retorne erro de página inexistente, será lançada uma Exception
Na utilização como middleware, é retornado uma resposta 404

### Access methods

#### Available protocols

- get: URL access or get method
- post: post method
- ajax: called fetch or XMLHttpRequest

#### Ajax

#### To use the Ajax call, it is necessary to define REQUEST_METHOD as AJAX:

Para utilizar a chamada Ajax, é necessário a definição do REQUEST_METHOD como AJAX:
```html
<form>
    <input type="hidden" name="REQUEST_METHOD" value="AJAX" />
    ...
</form>
```

#### REST request
- post: REST request
- get: REST request
- put: REST requests
- delete: REST requests
- patch: REST requests


### Router Attributes

#### Attribute routing works the same as role routing, with just a few caveats:
- Groups are not supported;
- Pre and post functions do not support anonymous functions;
- You must declare the classes with routes in a pipeline and load it with the router.

#### Both means of declaring routes can be used together.

O roteamento por atributo funciona da mesma forma que o roteamento por função, apenas com algumas resalvas:

- Não há suporte para grupos; 
- As funções anteriores e posteriores não tem suporte à funções anônimas;
- Deve-se declarar as classes com rotas em uma pipeline e carrega-la com o roteador.

Ambos os meios de declaração de rotas podem ser usados em conjunto.

```php
use HnrAzevedo\Router\Route;

/**
 * @param string $uri
 * @param ?array $methods
 * @param ?string $name
 * @param ?string $before
 * @param ?string $after
 * @param ?array $middleware
 * @param ?array $attributes
 * @param ?array $where
 */
#[Route('/path', name:'routeName')]
```

Example:

```php
# Controller File
use HnrAzevedo\Router\Route;

class ControllerAttribute{

    #[Route(
        '/user/{id}',
        methods:['GET'],
        name:'routeName',
        before:'Namespace\Controller@methodBefore',
        middleware:[],
        attributes:[
            'attributeName'=>'attributeValue',
            'attributeName0'=>'attributeValue0'
            ],
        where:['id'=>'[0-9]{1,11}'],
        after:'Namespace\Controller@methodAfter',
    )]
    public function method($param)
    {
        echo 'Controller@method executed!'.PHP_EOL."Param:{$param}";
    }

    public function methodBefore(): void
    {
        echo 'methodBefore'.PHP_EOL;
    }

    public function methodAfter(): void
    {
        echo PHP_EOL.'methodAfter';
    }
}
```

#### It is necessary to load the classes with routes in the same way as the route declaration files, it is interesting for both methods that the loading is done directly by composer.

#### Loading can be done directly with a class or with the directory.

É necessário fazer o carregamento das classes com rotas da mesma forma que os arquivos de declarações de rota, é interessante para ambos os métodos, que o carregamento seja feito diretamente pelo composer.

O carregamento pode ser feito diretamente com uma class ou com o diretório

```php
# Pipeline declaration
use HnrAzevedo\Router\Router;

Router::pipeline([
    HnrAzevedo\Router\Example\Controllers\ControllerAttribute::class,
    'examples\Controllers'
]);
```


### Router methods

### get
```php
Router::get('/','App\Controller\Application@method');
```

### post
```php
Router::post('/controller/method','App\Controller\Application@method');
```

### ajax
```php
Router::ajax('/userList','foo\bar\User@listme');
```

### middleware
```php

Router::globalMiddlewares([
    'Authorization'=> \App\Middlewares\Authorization::class
])

Router::get('/foo','foo\bar\User@method')->middleware([
    \App\Middlewares\Authentication::class,
    'Authorization'
]);
```

### name
#### Defines a name for the route, if you want to call dynamically by name
```php
Router::get('/','foo@bar')->name('index');
```

### attribute
#### Defines attributes to route, to be used dynamically
```php
/**
 * @param string $name
 * @param $value
 */
Router::get('/','foo@bar')->attribute('permission','permissionName');
```
#### Returning an attribute
```php
/**
 * @param string $name
 */
$permission = Router::getAttribute('attributeName');
```
#### Returning all attributes
```php
$permissions = Router::getAttributes();
```

### before
#### Runs before starting the work of the accessed route
```php
Router::get('/foo/bar','foo@bar')
      ->before('foo@beforeMethod');

Router::get('/foo/bar','foo@bar')
      ->before(function(){
          //
      });
```

### after 
#### Executes after completing the work of the accessed route
```php
Router::get('/bar/foo','bar@foo')
      ->after('bar@afterMethod');

Router::get('/bar/foo','bar@foo')
      ->after(function(){
          //
      });
```

### beforeAll 
#### Runs before work on any route
#### NOTE: execute the beforeAll method before the before method
```php
/**
 * @param \Closure|string $action
 * @param ?array $excepts
 */
Router::beforeAll('foo@bar');
Router::beforeAll('foo@bar',['Except_route','Outer_route']);
Router::beforeAll(function(){
          //
      });
```

### after All
#### Runs after completing work on any route
#### NOTE: execute the afterAll method before the after method
```php
/**
 * @param \Closure|string $action
 * @param ?array $excepts
 */
Router::afterAll('bar@foo');
Router::afterAll('bar@foo',['Except_route','Outer_route']);
Router::afterAll(function(){
          //
      });
```

### group
#### Set the group to use a common filter or before/after methods
```php
/**
 * @param string $prefix
 * @param \Closure $definitions
 */
Router::group('/foo', function(){
    Router::post('/bar','foo@bar');
});
```
### groupAttribute
#### Defines attributes on all routes in the group
#### NOTE:If the attribute has already been defined directly on the route, it will be disregarded here
```php
/**
 * @param string $name
 * @param $value
 */
Router::group('/foo', function(){
    Router::post('/bar','foo@bar');
})->groupAttribute('permission','permissionName');
```

### groupMiddlewares
#### Defines middleware for all group members
```php
/**
 * @param array $middlewares
 * @param ?array $excepts
 */
Router::group('/foo', function(){
    //
})->groupMiddlewares([
    'Authorization'
]);
```


### beforeGroup | afterGroup
#### Defines actions to be taken before and after any group member is triggered
```php
/**
 * @param \Closure|string $action
 * @param ?array $excepts
 */
Router::group('/foo', function(){
    Router::post('/bar','foo@bar');
})->beforeGroup(function(){
    //
});

/**
 * @param \Closure|string $action
 * @param ?array $excepts
 */
Router::group('/foo', function(){
    Router::post('/bar','foo@bar');
})->afterGroup(function(){
    //
});

```

### REST
```php
Router::delete('pattern','Namespaces\\Controller:method');
Router::get('pattern','Namespaces\\Controller:method');
Router::post('pattern','Namespaces\\Controller:method');
Router::put('pattern','Namespaces\\Controller:method');
Router::patch('pattern','Namespaces\\Controller:method');
```

## Parameters
```php
Router::get('/{param}', function($param){
    //
});

Router::get('/{param}/{param2}', function($param, $param2){
    //
});
```

## Optional parameters
```php
Router::get('/foo/{?id}','foo@bar');

Router::get('/foo/{?any}/{?id}','foo@baz');

Router::get('/user/{?id}/{text}','foo@bat');
```

### Regular Expression Constraints
```php
Router::get('/test/{id}/{id2}',function(){
    //
})->where([
    'id'=>'[0-9]{1,11}',
    'id2' => '[0-9]*'
]);

Router::group('/bar', function(){
    //
})->groupWhere([
    'id'=>'[0-9]{1,11}',
    'id2' => '[0-9]*'
]);
```

## Route definition

### Protocols
```php
/* Unique protocol */
Router::get('/get','foo@bar');

/* Multiple protocols */
Router::match('POST|get|AjAx','/my-account','baz@bar');

/* All protocols */
Router::any('/any','all@met');
```


### Current route
```php
$route = Router::current();

$name = Router::currentRouteName();

$action = Router::currentRouteAction();
```

### load
```php
/* NOTE: in case of error an exception is thrown */

/* Load the route via the URL accessed on the Router object */
Router::load();
/* Load the route via the name passed to the Router object */
Router::load('bar');

/* After loading the route it is necessary to fire it */
/* NOTE: After loading the route, if any dispatch function name is passed, it will be ignored. */
Router::load('foo')->run();

Router::load();
$currentRouter = Router::current();
Router::run();
```

### run
```php
/* NOTE: in case of error an exception is thrown */

/* Trigger route via URL accessed */
Router::run();
/* Trigger route by the given name */
Router::run('baz');
```

### Cache
```php
/* Returns the routes already defined for caching */
$routes = Router::routes();
/* Pass cached routes to the router */
Router::routes($routes);
```

#### Example of caching in SESSION
```php
if(!isset($_SESSION['cache']['routes'])){
    //Import routes
    $path = BASEPATH.'/../routes';

    foreach (scandir($path) as $routeFile) {
        if(pathinfo($path.DIRECTORY_SEPARATOR.$routeFile, PATHINFO_EXTENSION) === 'php'){
            require_once($path. DIRECTORY_SEPARATOR .$routeFile);
        }
    }

    $_SESSION['cache']['router']['middlewares'] = Router::globalMiddlewares();
    $_SESSION['cache']['router']['routes'] = Router::routes();
}
    
Router::routes($_SESSION['cache']['router']['routes']);
Router::globalMiddlewares($_SESSION['cache']['router']['middlewares']);
```

### Definition order

#### Routing loading is a priority with static routes (without parameters)

O carregamento das rotas é prioritário com as rotas estáticas (sem paramêtros)

## Support

##### Security: If you discover any security related issues, please email hnr.azevedo@gmail.com instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para hnr.azevedo@gmail.com em vez de usar o rastreador de problemas.

## Credits

- [Henri Azevedo](https://github.com/hnrazevedo) (Developer)

## License

The MIT License (MIT). Please see [License File](https://github.com/hnrazevedo/Router/blob/master/LICENSE.md) for more information.
