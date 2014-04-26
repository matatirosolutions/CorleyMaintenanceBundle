# Corley Maintenance Bundle

Just an unified way to put a web application under maintenance mode using web server strategies. The maintenance
mode will cut off all requests and it will replies with a static html file and a 503 header (Service Unavailable).

Those conditions will ensure that a load balancer cut an instance off during a maintenance

  * Master (stable)
    * [![Build Status](https://travis-ci.org/wdalmut/CorleyMaintenanceBundle.svg?branch=master)](https://travis-ci.org/wdalmut/CorleyMaintenanceBundle)
  * Develop
    * [![Build Status](https://travis-ci.org/wdalmut/CorleyMaintenanceBundle.svg?branch=develop)](https://travis-ci.org/wdalmut/CorleyMaintenanceBundle)

## Install

In your `composer.json` add the requirement

```json
require: {
    "corley/maintenance-bundle": "0.1.*"
}
```

Add also the repository to your composer

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/wdalmut/CorleyMaintenanceBundle"
    }
],
```

Register the bundle in your `AppKernel`

```php
public function registerBundles()
{
    ...
    $bundles = array(
        ...
        new Corley\MaintenanceBundle\CorleyMaintenanceBundle(),
    );
    ...
    return $bundles;
}
```

## Maintenance mode

When you want to put your web application under maintenance

```shell
app/console corley:maintenance:lock on
```

Restore the application status

```shell
app/console corley:maintenance:lock off
```

## Configure your web server

If you use Apache2 you have to add few lines to your `.htaccess`, in case of nginx just add dedicated
lines to web app configuration.

In order to obtain your configuration options just use the console

### Apache2

```shell
app/console corley:maintenance:apache-dump
```
### Nginx

```shell
app/console corley:maintenance:nginx-dump
```

## Configuration

You can configure the bundle in order to change the default behaviour (all options has a default value)

```yml
# config.yml
corley_maintenance:
    page: %kernel.root_dir%/../web/maintenance.dist.html
    active_link_name: lock.html
    symlink: false
```

Options:

* `page` is the original maintenance page
* `active_link_name` Is the name used in order to lock the website
* `symlink` If you want to use symlinks instead hardcopy strategy

You can also rewrite the `public` folder using the `web` parameter.


