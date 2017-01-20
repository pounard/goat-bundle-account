# Goat account bundle

Account management using [Goat database connector](https://github.com/pounard/goat) and [Goat bundle](https://github.com/pounard/goat-bundle).

Only working with PostgreSQL as of now (due to RETURNING SQL clause usage).


# Install

Install this bundle using composer:

```sh
composer require makinacorpus/goat-bundle-account
```

Inject the necessary SQL tables into your database:

```sh
psql -U username -d myDatabase -f Resources/docs/install.sql
```


Register it into your kernel class:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ... (your other bundles)
            new Goat\Bundle\GoatAccountBundle(),
        ];

        return $bundles;
    }
}
```


# Configure

Add into your ``app/config/routing.yml`` file:
```
goat_account:
    resource: "@GoatAccountBundle/Resources/config/routing.yml"
    prefix:   /u
```

Set-up your ``app/config/security.yml`` using the components defined in
[this sample.security.yml file](Resources/config/sample.security.yml).


# Creating a few users

First, create a new user so you can login:
```sh
bin/console account:create "John Smith" john.smith@example.com
```

Set or change his password:
```sh
bin/console account:password john.smith@example.com

Please enter the new password:
Please confirm the password:
password successfully set
```

Go to [http://localhost:8000/u/login](http://localhost:8000/u/login) and log-in.
**Username is not a unique key** use the mail address to log-in.


# Customisation

All views from this bundle require that you have a ``app/Resources/views/base.html.twig``
file containing the ``title`` and ``content`` block, but you can override them
as you wish in your ``app/Resources/views`` folder following [Symfony's documentation](https://symfony.com/doc/current/templating/overriding.html)

If you wish to use the provided user menu, you can set this into your main twig
template anywhere you wish to:

```twig
{% include 'GoatAccountBundle:Account:status.html.twig' %}
```


