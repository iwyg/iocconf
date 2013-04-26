# Configure Dependency injection with xml


## Installation

Add thapp\xsltbridge as a requirement to composer.json:

```
{
    "require": {
        "thapp/xmlconf": "dev-master"
    },
    "repositories": [
        {
        "type":"vcs",
        "url":"https://github.com/iwyg/xmlconf"
        }
    ]
}
```

Then run `composer update` or `composer install`

Next step is to tell laravel to load the serviceprovider. In `app/config/app.php` add

```
  // ...
  'Thapp\IocConf\IocConfServiceProvider' 
  // ...
```
to the `providers` array.

Make sure XmlConf is installed properly, then tell XmlConf where to find the ioc configuration (`app/config/packages/thapp/xmlconf/config.php`)

```
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

```
<?xml version="1.0" encoding="UTF-8"?>

<container xmlns="http://getsymphony.com/schema/ioc">

  <entities>

    <entity id="acme.frontcontroller" class="FrontController" scope="prototype"/>
    <entity id="acme.admincontroller" class="AdminController" scope="prototype"/>

    <entity class="ControllerRepository" scope="singleton">
      <argument id="acme.frontcontroller"/>
      <argument id="acme.admincontroller"/>
    </entity>

  </entities>

</container>    

```



