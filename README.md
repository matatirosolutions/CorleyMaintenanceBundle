# Corley Maintenance Bundle

Just an unified way to put a web application under maintenance mode using web server strategies. The maintenance
mode will cut off all requests and it will replies with a static html file and a 503 header (Service Unavailable).

Those conditions will ensure that a load balancer cut an instance off during a maintenance

[![Build Status](https://travis-ci.org/wdalmut/CorleyMaintenanceBundle.svg?branch=master)](https://travis-ci.org/wdalmut/CorleyMaintenanceBundle)

## Install

In your `composer.json` add the requirement

```json
"require": {
    "corley/maintenance-bundle": "0.1.*"
}
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
app/console corley:maintenance:dump-apache
```
### Nginx

```shell
app/console corley:maintenance:dump-nginx
```

## Configuration

You can configure the bundle in order to change the default behaviour (all options has a default value)

```yml
# config.yml
corley_maintenance:
    page: %kernel.root_dir%/../web/maintenance.dist.html
    hard_lock: lock.html
    symlink: false
```

Options:

* `page` is the original maintenance page
* `symlink` If you want to use symlinks instead hardcopy strategy
* `hard_lock` Is the name used in order to lock the website
* `web` public folder (by default `web` folder)
* `soft_lock` Is the name used in order to lock the website (using app layer)
* `whilelist` Authorized connections [soft-lock only]
  * `paths` A list of paths that skip the maintenance lock
  * `ips` A list of ips that skip the maintenance lock


## Soft locking
The soft locking strategy use the php layer in order to lock down the website. This means that the
application must works in order to lock down the web site.

The soft lock runs at `kernel.request` and stop other event propagations.

