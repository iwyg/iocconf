# Configure Dependency injection with xml


## Installation

Add thapp\xsltbridge as a requirement to composer.json:

```json
{
    "require": {
        "thapp/xmlconf": "dev-master"
    },
    "repositories": [
        {
        "type":"vcs",
        "url":"https://github.com/iwyg/iocconf"
        }
    ]
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

## Usage

#### xml

Note: An entity node can have an id attribute but must have a class attribute,
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

