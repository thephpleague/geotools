CONTRIBUTING
============

Contributions are **welcome** and be fully **credited** :)

**Geotools** will use the [Symfony2 Coding Standard](http://symfony.com/doc/current/contributing/code/standards.html).
The easiest way to apply these conventions is to install [PHP_CodeSniffer](http://pear.php.net/package/PHP_CodeSniffer)
and the [Opensky Symfony2 Coding Standard](https://github.com/opensky/Symfony2-coding-standard).

Installation
------------

``` bash
% pear install PHP_CodeSniffer
% cd `pear config-get php_dir`/PHP/CodeSniffer/Standards
% git clone git://github.com/opensky/Symfony2-coding-standard.git Symfony2
% phpcs --config-set default_standard Symfony2
```

Usage
-----

``` bash
% phpcs src/
% phpcs tests/
```

**Happy coding** !
