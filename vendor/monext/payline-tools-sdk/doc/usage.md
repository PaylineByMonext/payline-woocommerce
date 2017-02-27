Using PaylineTools
=============

Installation
------------

To install PaylineToolsClient, simply get the code (from github or through Composer) and
configure an autoloader for the PaylineTools namespace.


Create a PaylineToolsClient instance
----------------------------

Here is a basic setup to create a PaylineToolsClient instance

```php
 use PaylineTools\PaylineToolsClient;

    // create an instance
    $plnTools = new PaylineToolsClient($proxy_host, $proxy_port, $proxy_login, $proxy_password [, $pathLog= null[, $logLevel = Logger::INFO]]);
    /*
    If $pathLog is null, log files will be written under default logs directory. Fill with your custom log files path
    */
```

Call a PaylineTools web service
--------------------------

All PaylineTools web services are available through a PaylineToolsClient instance. Here is an examples :

### getPaymentMeans

This web service returns a list of all payment means available through Payline

```php
<?php
$getPaymentMeansResponse = $plnTools->getPaymentMeans();

// $getPaymentMeansResponse['result'] contains a string describing the call result
// in case of success :
// - $doWebPaymentResponse['listPaymentMean'] contains an array of paymentMean objects

```