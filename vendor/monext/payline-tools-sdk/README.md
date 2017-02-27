[![Latest Stable Version](https://poser.pugx.org/monext/payline-tools-client/v/stable)](https://packagist.org/packages/monext/payline-tools-client)
[![Total Downloads](https://poser.pugx.org/monext/payline-tools-client/downloads)](https://packagist.org/packages/monext/payline-tools-client)
[![License](https://poser.pugx.org/monext/payline-tools-client/license)](https://packagist.org/packages/monext/payline-tools-client)

PaylineToolsClient - Payline Tools library for PHP
====================================

Usage
-----
```php
    use PaylineTools\PaylineToolsClient;

    // create an instance
    $plnTools = new PaylineToolsClient($proxy_host, $proxy_port, $proxy_login, $proxy_password [, $pathLog= null[, $logLevel = Logger::INFO]]);
    /*
    If $pathLog is null, log files will be written under default logs directory. Fill with your custom log files path
    */

    // call a web service, for example getPaymentMeans
    $getPaymentMeansResponse = $plnTools->getPaymentMeans();
```    

Docs
====

See the doc/ directory for more detailed documentation. More information available on http://support.payline.com.


About
=====

Requirements
------------

Compliant with PHP 5.3 and over
Requires monolog/monolog, just let Composer do the job


Author
------

Fabien SUAREZ - <fabien.suarez@payline.com>

License
-------

Payline is licensed under the LGPL-3.0+ License - see the LICENSE file for details
