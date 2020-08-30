# Router @HnrAzevedo

[![Maintainer](https://img.shields.io/badge/maintainer-@hnrazevedo-blue?style=flat-square)](https://github.com/hnrazevedo)
[![Latest Version](https://img.shields.io/github/v/tag/hnrazevedo/Router?label=version&style=flat-square)](Release)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/quality/g/hnrazevedo/Router?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Router/?branch=master)
[![Build Status](https://img.shields.io/scrutinizer/build/g/hnrazevedo/Router?style=flat-square)](https://scrutinizer-ci.com/g/hnrazevedo/Router/build-status/master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/hnrazevedo/Router?style=flat-square)](https://packagist.org/packages/hnrazevedo/Router)
[![Total Downloads](https://img.shields.io/packagist/dt/hnrazevedo/Router?style=flat-square)](https://packagist.org/packages/hnrazevedo/Router)

###### Router is a simple friendly URL abstractor. Its author is not a professional in the development area, just someone in the Technology area who is improving his knowledge.

O Router é um simples abstrator de URL amigável. Seu autor não é profissional da área de desenvolvimento, apenas alguem da área de Tecnologia que está aperfeiçoando seus conhecimentos.

### Highlights

- Easy to set up (Fácil de configurar)
- Simple controller interface (Interface de controlador simples)
- Composer ready (Pronto para o composer)

## Installation

Router is available via Composer:

```bash 
"hnrazevedo/router": "^1.6"
```

or run

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

###### For details on how to use the Router, see the sample folder with details in the component directory

Para mais detalhes sobre como usar o Router, veja a pasta de exemplos com detalhes no diretório do componente

### Configure

#### It is necessary to configure the storage directory of the routes
É necessário configurar o diretório de armazenamento das rotas

```php
define("ROUTER_CONFIG", [
    "path" => "/Routes/", //Directory where PHP files with routes are stored
    "filters.namespace" => "Example\\Filters" // Namespace of your project's filter
    "controller.namespace" => "Example\\Controllers" // Namespace of your project's controller
]);
```

### Errors

#### In cases of configuration errors or nonexistent pages, the Router will throw an Exception.
Em casos de erros de configuração ou páginas inexistentes, o Router disparara uma Exception.

### Router methods

#### Available protocols

- get: URL access or get method
- post: post method
- ajax: called fetch or XMLHttpRequest
- form: called fetch or XMLHttpRequest (with Requested-Method defined in the header as form)

#### REST request
- post: REST request
- get: REST request
- put: REST requests
- delete: REST requests
- patch: REST requests

### The routes must be set in a flat file without classes, as they will be imported when creating the object
As rotas devem ser setadas num arquivo simples sem classes, pois seram importadas na criação do objeto

## Methods

### get
```php
Router::get('/','Application:index');
```

### post
```php
Router::post('/controller/method','Controller:method');
```

### ajax
```php
Router::ajax('/userList/','User:listme');
```

### filter
#### Defines a filter, or several, for the route
```php
Router::get('/logout','User:logout')->filter('user_in');

Router::get('/logout','User:logout')->filter(['user_in',...]);
```

### name
#### Defines a name for the route, if you want to call dynamically by name
```php
Router::get('/','Application:index')->name('index');
```

### before
#### Runs before starting the work of the accessed route
```php
Router::get('/user/{?id}','Controller:method')
      ->before('Controller:method');

Router::get('/user/{?id}','Controller:method')
      ->before(function(){
          //
      });
```

### after 
#### Executes after completing the work of the accessed route
```php
Router::get('/user/{?id}','Controller:method')
      ->after('Controller:method');

Router::get('/user/{?id}','Controller:method')
      ->after(function(){
          //
      });
```

### beforeAll 
#### Runs before work on any route
#### NOTE: execute the beforeAll method before the before method
```php
/* Callback, optional except routes (name) */
Router::beforeAll('Controller:method');
Router::beforeAll('Controller:method','Except_route');
Router::beforeAll('Controller:method',['Except_route','Outer_route']);
Router::beforeAll(function(){
          //
      });
```

### after All
#### Runs after completing work on any route
#### NOTE: execute the afterAll method before the after method
```php
/* Callback, optional except routes (name) */
Router::afterAll('Controller:method');
Router::afterAll('Controller:method','Except_route');
Router::afterAll('Controller:method',['Except_route','Outer_route']);

Router::afterAll(function(){
          //
      });
```

### group
#### Set the group to use a common filter or before/after methods
```php
Router::group('/administrator/', function(){
    Router::post('/controller/','Administrator:execute');
    Router::get('/pages/index','Administrator:view');
})->filter('admin')
  ->before(function(){
      //
  })
  ->after(function(){
      //
  });
```

### REST
```php
Router::delete('pattern','Controller:method');
Router::get('pattern','Controller:method');
Router::post('pattern','Controller:method');
Router::put('pattern','Controller:method');
Router::patch('pattern','Controller:method');
```

## Parameters
```php
Router::get('/{parameter}', function($parameter){
    //
});

Router::get('/{parameter}/{outerparameter}', function($parameter, $outerParameter){
    //
});

Router::post('/{controller}/{method}', '{controller}:{method}');
```

## Optional parameters
```php
Router::get('/user/{?id}','Controller:method');
/* Access on HTTP GET '/user' or '/user/1' */

Router::get('/user/{?any}/{?id}','Controller:method')->where([
    'id' => '[0-9]{1,11}'
]);
/* HTTP GET REQUESTS
 * /user                            -> ACCESS
 * /user/anything                   -> ACCESS
 * /user/anything/1                 -> ACCESS
 * /user/anything/123456789011111   -> NOT ACCESS
 * /user/anything/abc               -> NOT ACCESS
 */

Router::get('/user/{?id}/{text}','Controller:method')->where([
    'id' => '[0-9]{1,11}'
]);
/* HTTP GET REQUESTS
 * IMPORTANT: Access only if all parameters are passed
 */
```

## Route definition

### Protocols
```php
/* Unique protocol */
Router::get('/my-account','User:my_account')->filter('user_in');

/* Multiple protocols */
Router::match('post|get|ajax','/my-account','User:my_account')->filter('user_in');

/* All protocols */

Router::any('/my-account','User:my_account')->filter('user_in');
```

### Order

#### Correct way of defining routes
```php
/* Access via anything except /1 and /3 */
Router::get('/{test}',function($test){
    //
});
/* Acess via /1 */
Router::get('/1',function(){
    //
});
/* Acess via /3 */
Router::get('/3',function(){
    //
});
```

#### Incorrect way of defining routes
```php
/* It will never be accessed */
Router::get('/1',function(){
    //
});

/* It will never be accessed */
Router::get('/3',function(){
    //
});

/* Access via anything */
Router::get('/{test}',function($test){
    //
});
```

### Regular Expression Constraints
```php
Router::get('/test/{id}/{id2}',function(){
    //
})->where([
    'id'=>'[0-9]*',
    'id2' => '[0-9]*'
]);

Router::get('/test/{id}/{id2}',function(){
    //
})->where('id','[0-9]*');
```

### Current route
```php
$route = Router::current();
$name = Router::currentRouteName();
$action = Router::currentRouteAction();
```

### Run route
```php
/* NOTE: in case of error an exception is thrown */
/* Fires from the URL accessed */
Router::dispatch();

/* Shoot by name */
Router::dispatch('index');
```

## Controller
```php
namespace Example\Controllers;

class User{

    public function my_account(/* form inputs */): void
    {
        //
    }

}
```

### Router Controller

#### If you want to validate your Ajax form or request data automatically or the need to do it on your controller, extend your HnrAzevedo\Router\Controller controller
Caso você queira validar seus dados de formulários ou de requisições Ajax automáticamente nem a necessidade de faze-lo em seu controlador, extenda seu controlador de HnrAzevedo\Router\Controller

```php
namespace Example\Controllers;

use HnrAzevedo\Router\Controller;

class User extends Controller{

    public function my_account($another): void
    {
        //
    }

}
```
#### For more information on how to validate your data automatically see https://github.com/hnrazevedo/Validator
Para mais informações de como validar seus dados automáticamente consulte https://github.com/hnrazevedo/Validator

### NOTE: To validate your data automatically, it is necessary that the route is defined as follows {controller}: method (static "method" syntax), and pass the desired function via dataForm with the name "role" in your data
Para validar seus dados automaticamente, é necessário que a rota seja definida da seguinte forma {controller}:method (sintax "method" estático), e passar a função deseja via dataForm com o nome "role" em seus dados

## Route Filter

#### To create filters for your routes, see https://github.com/hnrazevedo/Filter.
Para criar filtros para suas rotas, consulte https://github.com/hnrazevedo/Filter.


## Support

###### Security: If you discover any security related issues, please email hnr.azevedo@gmail.com instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para hnr.azevedo@gmail.com em vez de usar o rastreador de problemas.

## Credits

- [Henri Azevedo](https://github.com/hnrazevedo) (Developer)
- [Robson V. Leite](https://github.com/robsonvleite) (Readme based on your datalayer design)

## License

The MIT License (MIT). Please see [License File](https://github.com/hnrazevedo/Router/blob/master/LICENSE.md) for more information.
