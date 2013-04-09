CONTRIBUTING
============

Contributions are **welcome** and be fully **credited** <3

**Geotools** will use the [Symfony2 Coding Standard](http://symfony.com/doc/current/contributing/code/standards.html).
The easiest way to apply these conventions is to install [PHP_CodeSniffer](http://pear.php.net/package/PHP_CodeSniffer)
and the [Opensky Symfony2 Coding Standard](https://github.com/opensky/Symfony2-coding-standard).

You may be interested in [PHP Coding Standards Fixer](https://github.com/fabpot/PHP-CS-Fixer).

Installation
------------

``` bash
$ pear install PHP_CodeSniffer
$ cd `pear config-get php_dir`/PHP/CodeSniffer/Standards
$ git clone git://github.com/opensky/Symfony2-coding-standard.git Symfony2
$ phpcs --config-set default_standard Symfony2
```

Usage
-----

``` bash
$ phpcs src/
```

**Happy coding** !
