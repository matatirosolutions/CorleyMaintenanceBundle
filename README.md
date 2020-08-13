# Corley Maintenance Bundle

A unified way of putting a web application under maintenance using web server strategies. The maintenance
mode will cut off all requests and it will replies with a static html file and a 503 header (Service Unavailable).

Those conditions will ensure that a load balancer cut an instance off during a maintenance

[![Build Status](https://travis-ci.org/matatirosolutions/CorleyMaintenanceBundle.svg?branch=master)](https://travis-ci.org/matatirosolutions/CorleyMaintenanceBundle)

## Install

In `composer.json` add the requirement. The current version requires at least PHP 7.2 and a supported version of Symfony (3.4, 4.4 and 5.1). 

```json
"require": {
    "corley/maintenance-bundle": "^0.4"
}
```

To support earlier versions e.g. SF 2.x, 3.3, 4.1 etc or PHP less than 7.2 you will need to use:

```json
"require": {
    "corley/maintenance-bundle": "^0.2"
}
```
This version can also be used for more recent Symfony versions, e.g. with 3.4 or 4.4 but is not compatible with Symfony 5 - only `0.3` or greater can be used there because of changes to the event structure in Symfony 5.0.


For pre-Flex applications register the bundle in `AppKernel.php`

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

For projects built with recent versions of Flex, a default recipe will be generated which will add the bundle to your `bundles.php`. In older versions of Flex you may need to do this yourself
```php
Corley\MaintenanceBundle\CorleyMaintenanceBundle::class => ['all' => true],
```

## Maintenance mode

When you want to put your web application under maintenance

```shell
bin/console corley:maintenance:lock on
```

Restore the application status

```shell
bin/console corley:maintenance:lock off
```

## Configure your web server

If you use Apache2 you have to add a few lines to your `.htaccess`, for nginx just add dedicated
lines to web app configuration.
Make sure that those lines precede any other rewrite rule.
The `mod_rewrite` module in Apache2 has to be installed and enabled.

In order to obtain your configuration options use the console

### Apache2

```shell
bin/console corley:maintenance:dump-apache
```
### Nginx

```shell
bin/console corley:maintenance:dump-nginx
```

## Configuration

You can configure the bundle in order to change the default behaviour (all options have a default value)

For projects not using Flex
```yml
# config.yml
corley_maintenance:
    page: %kernel.root_dir%/../web/maintenance.dist.html
    hard_lock: lock.html
    symlink: false
    web: %kernel.root_dir%/../web
```

For Flex projects
```yml
# config/packages/corley.yml
corley_maintenance:
    page: %kernel.project_dir%/templates/maintenance.dist.html
    hard_lock: lock.html
    symlink: false
```

Options:

* `page` is the original maintenance page (default: `vendor/corley/maintenance-bundle/Corley/MaintenanceBundle/Resources/views/maintenance.html`)
* `symlink` If you want to use symlinks instead hardcopy strategy (default: hardcopy)
* `hard_lock` Is the name used in order to lock the website (default: `hard.lock`)
* `web` public folder. Prior to 0.4 this defaulted to `%kernel.root_dir%/../web`, since 0.4.0 the new default is `%kernel.project_dir%/public` as the `%kernel.root_dir%` parameter has been deprecated since Symfony 4.2, and was removed in 5.1. If your project's public folder is still `web` (or some other folder) set this in the config file.
* `soft_lock` Is the name used in order to lock the website (using app layer)
* `whitelist` Authorized connections [soft-lock only]
  * `paths` A list of paths that skip the maintenance lock
  * `ips` A list of ips that skip the maintenance lock


## Soft locking
The soft locking strategy uses the php layer in order to lock down the website. This means that the
application must work in order to lock down the web site.

The soft lock runs at `kernel.request` and stops other event propagation.

When you want to put your web application under maintenance using a soft-locking strategy:

```shell
bin/console corley:maintenance:soft-lock on
```

Restore the application status

```shell
bin/console corley:maintenance:soft-lock off
```
