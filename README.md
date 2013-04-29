# Pupulate and configure Laravel's IoC container with xml

[![Build Status](https://travis-ci.org/iwyg/iocconf.png?branch=master)](https://travis-ci.org/iwyg/iocconf)

## Synopsis

IoCConf provides a convenient way to handle dependecy injection using xml. 
E.g. it is possible to setup your controllers with either contructor or setter injection. 


## Installation

IocConf requires [XmlConf](https://github.com/iwyg/xmlconf) to work. 

Add thapp/iocconf ans thapp/xmlconf as a requirement to composer.json:

```json
{
    "require": {
        "thapp/iocconf": "1.0.*",
        "thapp/xmlconf": "1.0.*"
    }
}
```

Then run `composer update` or `composer install`

Next step is to tell laravel to load the serviceprovider. In `app/config/app.php` add

```php
  // ...
  'Thapp\IocConf\IocConfServiceProvider' 
  // ...
```
to the `providers` array.

Make sure XmlConf is installed properly, then tell XmlConf where to find the ioc configuration (`app/config/packages/thapp/xmlconf/config.php`)

```php
return array(

    /*
    |--------------------------------------------------------------------------
    | Basedir relative to the install directory
    |--------------------------------------------------------------------------
     */
    'basedir' => array(
        'ioc'      => 'vendor/thapp/iocconf/src/Thapp/IocConf'
    ),

    /*
    |--------------------------------------------------------------------------
    | Reader dictionary
    |--------------------------------------------------------------------------
     */
    'namespaces' => array(
        'ioc'      => 'Thapp\\IocConf'
    ),
);


```

## Example

Given you want to inject Laravel's view Object into a controller, the xml configuration would look something like this:

```php

<?php

namespace Acme;

use \BaseController;

class FrontController extends BaseController
{

    // ... setter method on your controller
    public function setView(Illuminate\View\Environment $view)
    {
        $this->view = $view;
    }

}   

```

The conig xml would look like this

```xml
<container xmlns="http://getsymphony.com/schema/ioc">

  <entities>
    <entity class="Acme\FrontController" scope="prototype"/>
        <!-- the controller has a setter method for setting the view object -->
        <call method="setView">
    		<argument id="view"/>
    	</call>
    </entity>
  </entities>

</container>    
```

## Usage

#### xml

Note: An entity node can have an id attribute but must have a class and a scope attribute,
An agument node must either have an id or class attribute.

Possible entity scopes: 

```
- prototype // see Container::bind();
- singleton // see Container::singleton();
- shared    // see Container::share();

```


```xml
<?xml version="1.0" encoding="UTF-8"?>

<container xmlns="http://getsymphony.com/schema/ioc">

  <entities>

    <entity id="acme.frontcontroller" class="FrontController" scope="prototype"/>
    
    <entity id="acme.admincontroller" class="AdminController" scope="prototype">
    	<call method="setView">
    		<argument id="view"/>
    	</call>
    </entity>

    <entity class="ControllerRepository" scope="singleton">
      <argument id="acme.frontcontroller"/>
      <argument id="acme.admincontroller"/>
    </entity>

  </entities>

</container>    

```
#### php

```php
$repo = App::make('ControllerRepository');

$repo2 = App::make('ControllerRepository');

$repo === $repo2 // true

```

